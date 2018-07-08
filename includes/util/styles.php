<?php

function magic_register_style($slug, $base_path ) {
  $content_url = content_url();
  $plugins_url = plugins_url();

  $plugin_path = str_replace( $content_url, '', $plugins_url );

  $local_path = $plugin_path . '/' . $base_path;

  wp_register_style( $slug, $local_path . '/' . $slug . '.less' );
  wp_enqueue_style( $slug );
}
