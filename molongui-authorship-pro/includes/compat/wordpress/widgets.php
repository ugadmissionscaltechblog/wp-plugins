<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
remove_filter( 'widget_text_content', 'wpautop' );