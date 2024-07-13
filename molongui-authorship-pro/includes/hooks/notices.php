<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

add_filter( 'authorship/admin/show_upgrade_notice', '__return_false' );
add_filter( 'authorship/action_links/go_pro'      , '__return_false' );
add_filter( 'authorship/options/display_banners'  , '__return_false' );