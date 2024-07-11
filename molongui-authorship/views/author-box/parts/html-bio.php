<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( 'none' === $options['author_box_bio_source'] ) return;

?>

<div class="m-a-box-bio" <?php echo ( $add_microdata ? 'itemprop="description"' : '' ); ?>>
    <?php
    $bio = str_replace( array( "\n\r", "\r\n", "\n\n", "\r\r" ), "<br>", wpautop( apply_filters( 'authorship/box/bio', $author['bio'], $author ) ) );

    echo $bio;
    if ( !empty( $options['extra_content'] ) )
    {
        echo $options['extra_content'];
    }
    ?>
</div>
