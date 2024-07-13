<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
$config = array
(
    'brand' => 'Molongui',
    'name'  => 'Authorship Pro',
    'title' => 'Authorship Pro',
    'id'    => 'authorship_pro',
    'db' => 6,
);
$plugin_id = strtolower( str_replace( ' ', '-', $config['brand'] . ' ' . $config['name'] ) );
$plugin_px = str_replace( '-', '_', $plugin_id );
defined( 'MOLONGUI_AUTHORSHIP_PRO_DIR'  ) or define( 'MOLONGUI_AUTHORSHIP_PRO_DIR' , dirname( __DIR__ ) . '/' );
defined( 'MOLONGUI_AUTHORSHIP_PRO_FILE' ) or define( 'MOLONGUI_AUTHORSHIP_PRO_FILE', dirname( __DIR__ ) . '/' . $plugin_id . '.php' );
if ( !defined( 'MOLONGUI_AUTHORSHIP_PREFIX' ) ) define( 'MOLONGUI_AUTHORSHIP_PREFIX', 'molongui_authorship' );
if ( !defined( 'MOLONGUI_AUTHORSHIP_NAME' ) ) define( 'MOLONGUI_AUTHORSHIP_NAME', 'molongui-authorship' );
if ( !defined( 'MOLONGUI_AUTHORSHIP_TITLE' ) ) define( 'MOLONGUI_AUTHORSHIP_TITLE', 'Molongui Authorship' );
$constants = array
(
    'NAME'   => $plugin_id,
    'PREFIX' => $plugin_px,
    'ID'     => $config['id'],
    'TITLE'  => $config['brand'] . ' ' . $config['title'],
    'FOLDER'    => basename( dirname( MOLONGUI_AUTHORSHIP_PRO_FILE ) ),
    'URL'       => plugin_dir_url( MOLONGUI_AUTHORSHIP_PRO_FILE ),
    'BASENAME'  => plugin_basename( MOLONGUI_AUTHORSHIP_PRO_FILE ),
    'NAMESPACE' => '\Molongui\\' . str_replace( ' ', '', ucwords( strtr( ucwords( strtolower( str_replace( ' Pro', '', $config['name'] ) ) ), array( '-' => ' ', '_' => ' ' ) ) ) ) . '\Pro',
    'DB'         => $config['db'],
    'DB_VERSION' => $plugin_px.'_db_version',
    'INSTALL'    => $plugin_px.'_install',
    'CONTRIB_ID' => $plugin_px.'_contributors_page_id',
);
if ( isset( $dont_load_constants ) and $dont_load_constants )
{
    unset( $dont_load_constants );
    return;
}
$constant_prefix = strtoupper( $plugin_px.'_' );
foreach ( $constants as $param => $value )
{
    $param = $constant_prefix . $param;
    if ( !defined( $param ) ) define( $param, $value );
}