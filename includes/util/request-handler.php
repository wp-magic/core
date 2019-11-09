<?php
/**
 * Request handling helpers.
 *
 * @package Magic
 * @since 0.0.1
 */

if ( ! function_exists( 'magic_get_referer' ) ) {
	/**
	 * Redirects requests if errors happened during execution
	 *
	 * @since 0.0.1
	 */
	function magic_get_referer() {
		$original_redirect = esc_url_raw( filter_input( INPUT_SERVER, 'REDIRECT_URL' ) );
		$redirect          = ! empty( $_SERVER['REDIRECT_URL'] ) && esc_url_raw( wp_unslash( $_SERVER['REDIRECT_URL'] ) );

		if ( ! empty( $original_redirect ) ) {
			return explode( '?', $original_redirect )[0];
		} elseif ( ! empty( $redirect ) ) {
			return explode( '?', $redirect )[0];
		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'magic_request' ) ) {
	/**
	 * Sets the referer if possible
	 *
	 * @since 0.0.1
	 *
	 * @param string $referer sets this referer in $_SERVER['MAGIC_REFERER'].
	 */
	function magic_request( string $referer = null ) {
		// Get referer if it has not been set by the request.
		if ( empty( $referer ) ) {
			$referer = magic_get_referer();
		}

		$_SERVER['MAGIC_REFERER'] = esc_url_raw( $referer );
	}
}

if ( ! function_exists( 'magic_redirect' ) ) {
	/**
	 * Redirect a request
	 *
	 * @since 0.0.1
	 *
	 * @param array $refs query args to add to the request.
	 */
	function magic_redirect( array $refs = [] ) {
		if ( ! empty( $refs ) ) {
			magic_add_query_arg( $refs );
		}

		$referer = magic_get_referer();

		wp_safe_redirect( $referer );
		exit;
	}
}

if ( ! function_exists( 'magic_redirect_if_error' ) ) {
	/**
	 * Redirects requests if errors happened during execution
	 *
	 * @since 0.0.1
	 */
	function magic_redirect_if_error() {
		$referer = magic_get_referer();

		if ( false !== strpos( $referer, 'error' ) ) {
			wp_safe_redirect( $referer );
			exit;
		}
	}
}

if ( ! function_exists( 'magic_add_query_arg' ) ) {
	/**
	 * Add Query string to requests
	 *
	 * @since 0.0.1
	 *
	 * @param array $refs list of query parameters.
	 */
	function magic_add_query_arg( array $refs = [] ) {
		$referer = magic_get_referer();

		if ( ! empty( $refs ) ) {
			foreach ( $refs as $k => $r ) {
				$k = rawurlencode( $k );
				$r = rawurlencode( $r );

				if ( ! empty( $r ) && ! empty( $k ) ) {
					$params = [];
					$query  = wp_parse_url( $referer, PHP_URL_QUERY );
					parse_str( $query, $params );
					if ( ! empty( $params[ $k ] ) ) {
						$referer = add_query_arg( $k, $r . ',' . $params[ $k ] );
					} else {
						$referer = add_query_arg( $k, $r, $referer );
					}
				}
			}
		}

		$_SERVER['MAGIC_REFERER'] = $referer;
		return $referer;
	}
}

if ( ! function_exists( 'magic_add_query_error' ) ) {
	/**
	 * Add an error to the query
	 *
	 * @since 0.0.1
	 *
	 * @param string $err error to set, can be used in client after response.
	 */
	function magic_add_query_error( string $err = 'unknown' ) {
		magic_add_query_arg( [ 'error' => $err ] );
	}
}

if ( ! function_exists( 'magic_verify_nonce' ) ) {
	/**
	 * Verify a nonce and add an error to the query if it failed
	 *
	 * @since 0.0.1
	 *
	 * @param string $slug nonce action slug.
	 * @param bool   $redirect will redirect on error if true.
	 *
	 * @return bool|array false if nonce failed, $_POST if not.
	 */
	function magic_verify_nonce( string $slug, bool $redirect = true ) {
		if ( ! empty( $_POST['nonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
		}

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, $slug ) ) {
			magic_add_query_error( 'nonce' );

			// Return to stop redirection.
			if ( ! $redirect ) {
				return false;
			}
		} else {
			return $_POST;
		}

		if ( $redirect ) {
			magic_redirect_if_error();
		}
	}
}

if ( ! function_exists( 'magic_parse_arguments' ) ) {
	/**
	 * Parse request arguments
	 *
	 * @since 0.0.1
	 *
	 * @param array $args request keys to look for in $_POST.
	 * @param array $ctx context to fill with the results.
	 *
	 * @return array the passed in $ctx array, or a new one, filled with parse results.
	 */
	function magic_parse_arguments( array $args = [], array $ctx = [] ) {
		$defaults = [
			'query'  => [],
			'errors' => [],
		];

		$ctx = array_merge( $defaults, $ctx );

		// $args['nonce'] is the slug, not the nonce value
		$post = magic_verify_nonce( $args['nonce'] );
		if ( empty( $post ) ) {
			$ctx['errors'][] = 'nonce';
			return $ctx;
		}

		foreach ( $args as $key => $error ) {
			if ( ! empty( $post[ $key ] ) ) {
				if ( 'nonce' !== $key ) {
					$ctx['query'][ $key ] = sanitize_textarea_field( wp_unslash( $post[ $key ] ) );
				}
			} elseif ( ! empty( $error ) ) {
				if ( 'nonce' === $key ) {
					$ctx['errors'][] = $key;
				} else {
					$ctx['errors'][] = $error;
				}
			} else {
				$ctx['query'][ $key ] = '';
			}
		}

		if ( defined( 'MAGIC_GDPR_SLUG' ) ) {
			if ( ! empty( $post['allow_cookies'] ) && 'on' === $post['allow_cookies'] ) {
				magic_gdpr_set_cookies( array( 'settings', 'auth' ) );
			} elseif ( ! magic_gdpr_check_cookies() ) {
				$ctx['errors'][] = 'cookie';
			}
		}

		return $ctx;
	}
}

if ( ! function_exists( 'magic_is_same_user' ) ) {
	/**
	 * Return true if current user equals param id
	 *
	 * @since 0.0.1
	 *
	 * @param int $user_id id to check the logged in user against.
	 *
	 * @return bool true if user equals logged in user.
	 */
	function magic_is_same_user( int $user_id = -1 ) {
		return -1 === $user_id || get_current_user_id() === $user_id;
	}
}

if ( ! function_exists( 'magic_is_logged_in' ) ) {
	/**
	 * Return true if a user is logged in
	 *
	 * @since 0.0.1
	 *
	 * @return bool true if user is logged in.
	 */
	function magic_is_logged_in() {
		return get_current_user_id() > 0;
	}
}

if ( ! function_exists( 'magic_require_login' ) ) {
	/**
	 * Redirects to $redirect if user is not logged in.
	 *
	 * @since 0.0.1
	 *
	 * @param bool $redirect should this request be redirected if not logged in.
	 * @param int  $user_id checked with get_current_user_id if provided.
	 *
	 * @return bool true if user is logged in, false if user is not logged in but did not get redirected.
	 */
	function magic_require_login( $redirect = 0, int $user_id = -1 ) {
		$is_logged_in = magic_is_logged_in();
		$is_same_user = magic_is_same_user( $user_id );

		if ( ! $is_logged_in || ! $is_same_user ) {
			if ( false !== $redirect ) {
				if ( ! $redirect ) {
					$redirect = magic_get_option( 'magic_user_admin_login_page', '/login' );
				}

				wp_safe_redirect( $redirect );
				exit;
			}

			return false;
		}

		return true;
	}
}
