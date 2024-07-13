<?php

namespace Molongui\Authorship\Pro;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Settings extends \Molongui\Authorship\Pro\Common\Modules\Settings
{
    public function __construct()
    {
        parent::__construct();
        add_filter( 'mpb/options/display/banners', '__return_false' );
        add_filter( 'mpb/default_options', array( $this, 'set_defaults' ), 11 );
    }
    public function set_defaults( $defaults )
    {
        return array_merge( $defaults, self::get_defaults() );
    }
    public static function get_defaults()
    {
        return array
        (
        );
    }

} // class
new Settings();