<?php
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'authorship_format_sitemap_date' ) )
{
    function authorship_format_sitemap_date( $date, $format = DATE_W3C )
    {
        $immutable_date = date_create_immutable_from_format( 'Y-m-d H:i:s', $date, wp_timezone() );

        if ( !$immutable_date ) return $date;

        return $immutable_date->format( $format );
    }
}