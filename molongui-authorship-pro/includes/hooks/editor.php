<?php
defined( 'ABSPATH' ) or exit;
add_filter( 'authorship/pro_tag', '__return_empty_string' );
add_filter( 'authorship/editor/show_premium_warnings', '__return_false' );
remove_filter( 'authorship/validate_editor_options', 'authorship_validate_editor_premium_options', 10 );