<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_include_files( $path )
{
    if ( is_file( $path ) )
    {
        require_once $path;
    }

    elseif ( is_dir( $path ) )
    {
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ) ) as $file )
        {

            if ( $file->isFile() and 'php' === $file->getExtension() and 'index.php' !== $file->getFilename() )
            {
                require_once $file->getPathname();
            }
        }
    }
}
$paths = array
(
    MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/helpers/',
    MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/hooks/',
    MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/sitemaps/',
    MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/shortcodes/',
    MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/compat.php',
    MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/extend-wp-rest-api.php',
);
foreach ( $paths as $path ) authorship_pro_include_files( $path );