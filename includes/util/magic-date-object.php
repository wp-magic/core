<?php
/**
 * Returns a date array with nicely named fields
 *
 * @package Magic
 * @since 0.0.1
 */

/**
 * Convert the $timestamp into a key => value array
 *
 * @since 0.0.1
 *
 * @param int $timestamp to convert to date.
 *
 * @return array with various fields related to the date.
 */
function magic_date_object( int $timestamp ) {
	$month_index = date( 'n', $timestamp );
	$day_index   = date( 'w', $timestamp );

	return [
		'day'         => date( 'd', $timestamp ),
		'month'       => date( 'm', $timestamp ),
		'year'        => date( 'Y', $timestamp ),
		'day_index'   => $day_index,
		'month_index' => $month_index,
		'month_name'  => MONTH_NAMES[ $month_index ],
		'day_name'    => DAY_NAMES[ $day_index ],
		'hour'        => date( 'H', $timestamp ),
		'minute'      => date( 'i', $timestamp ),
		'second'      => date( 's', $timestamp ),
	];
}
