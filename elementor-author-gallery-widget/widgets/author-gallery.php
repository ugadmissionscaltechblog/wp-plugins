<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Typography;
use \Elementor\Utils;
use \ElementorPro\Base\Base_Widget;
use \Elementor\Controls_Manager;

class AuthorGallery extends Base_Widget {


	protected $_has_template_content = false;

	public function get_name() {
		return 'author_gallery';
	}

	public function get_title() {
		return esc_html__( 'Author Gallery', 'elementor-pro' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_keywords() {
		return [ 'posts', 'cpt', 'item', 'loop', 'portfolio', 'custom post type' ];
	}

	public function get_script_depends() {
		return [ 'imagesloaded' ];
	}

	public function on_import( $element ) {
		if ( isset( $element['settings']['posts_post_type'] ) && ! get_post_type_object( $element['settings']['posts_post_type'] ) ) {
			$element['settings']['posts_post_type'] = 'post';
		}

		return $element;
	}

	protected function register_controls() {
		$this->register_query_section_controls();
	}

	private function register_query_section_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'prefix_class' => 'elementor-grid%s-',
				'frontend_available' => true,
				'selectors' => [
					'.elementor-msie {{WRAPPER}} .elementor-portfolio-item' => 'width: calc( 100% / {{SIZE}} )',
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Posts Per Page', 'elementor-pro' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail_size',
				'exclude' => [ 'custom' ],
				'default' => 'medium',
				'prefix_class' => 'elementor-portfolio--thumbnail-size-',
			]
		);

		$this->add_control(
			'masonry',
			[
				'label' => esc_html__( 'Masonry', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'elementor-pro' ),
				'label_on' => esc_html__( 'On', 'elementor-pro' ),
				'condition' => [
					'columns!' => '1',
				],
				'render_type' => 'ui',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'item_ratio',
			[
				'label' => esc_html__( 'Item Ratio', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.66,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__thumbnail__link' => 'padding-bottom: calc( {{SIZE}} * 100% )',
					'{{WRAPPER}}:after' => 'content: "{{SIZE}}"; position: absolute; color: transparent;',
				],
				'condition' => [
					'masonry' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => esc_html__( 'Show Title', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => esc_html__( 'Off', 'elementor-pro' ),
				'label_on' => esc_html__( 'On', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_layout',
			[
				'label' => esc_html__( 'Items', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		/*
		 * The `item_gap` control is replaced by `column_gap` and `row_gap` controls since v 2.1.6
		 * It is left (hidden) in the widget, to provide compatibility with older installs
		 */

		$this->add_control(
			'item_gap',
			[
				'label' => esc_html__( 'Item Gap', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}; --grid-column-gap: {{SIZE}}{{UNIT}};',
				],
				'frontend_available' => true,
				'classes' => 'elementor-hidden',
			]
		);

		$this->add_control(
			'column_gap',
			[
				'label' => esc_html__( 'Columns Gap', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => ' --grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'row_gap',
			[
				'label' => esc_html__( 'Rows Gap', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-portfolio-item__img, {{WRAPPER}} .elementor-portfolio-item__overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_overlay',
			[
				'label' => esc_html__( 'Item Overlay', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color_background',
			[
				'label' => esc_html__( 'Background Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} a .elementor-portfolio-item__overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'color_title',
			[
				'label' => esc_html__( 'Color', 'elementor-pro' ),
				'separator' => 'before',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a .elementor-portfolio-item__title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-portfolio-item__title',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	public function get_authors() {
		$current_year = date( 'Y' );
		$last_year = $current_year - 2;

		$args = array(
			'post_type'      => 'post',
            'post_status'    => 'publish',
			'posts_per_page' => -1,
			'date_query'     => array(
				'after' => array(
					'year' => $last_year,
				),
			),
			'meta_query'     => array(
				array(
					'key'     => '_molongui_author',
					'compare' => 'EXISTS',
                ),
			),
		);

		$posts_query = new WP_Query( $args );

		$user_ids = array(); // To store the unique author IDs.
		$guest_ids = array(); // To store the unique author IDs.

		if ( $posts_query->have_posts() ) {
			while ( $posts_query->have_posts() ) {
				$posts_query->the_post();
				$author_meta = get_post_meta( get_the_ID(), '_molongui_author', true );
				if ( strpos( $author_meta, 'guest-' ) !== false ) {
					$guest_id = str_replace( 'guest-', '', $author_meta );
					if ( !in_array( $guest_id, $guest_ids ) ) {
						$guest_ids[] = $guest_id;
					}
				} elseif ( strpos( $author_meta, 'user-' ) !== false ) {
					$user_id = str_replace( 'user-', '', $author_meta );
					if ( !in_array( $user_id, $user_ids ) ) {
						$user_ids[] = $user_id;
					}
				}
			}
			wp_reset_postdata();
		}
		
		$authors = molongui_get_authors( 
			$type = 'users', 
			$include_users = array(), //$user_ids, 
			$exclude_users = array(236695106), 
			$include_guests = array(), //$guest_ids, 
			$exclude_guests = array(), 
			$order = 'ASC', 
			$orderby = 'rand', 
			$get_data = true, 
			$min_post_count = 0, 
			$post_types = array( 'post' ) 
		);

		return $authors;
	}

    public function get_profile_pic_id( $author_id ) {
        $profile_pic_id = get_post_thumbnail_id( $author_id );
        if ( ! $profile_pic_id ) {
            $profile_pic_id = get_user_meta( $author_id, 'molongui_author_image_id', true );
        }
        return $profile_pic_id;
    }

	public function render() {

		?>
		<div class="elementor-portfolio elementor-grid elementor-posts-container">
		<?php
		$classes = [
			'elementor-portfolio-item',
			'elementor-post',
		];

			// LOOP
        $authors = $this->get_authors();
        
        $settings = $this->get_settings();
        $n = $settings['posts_per_page'];
        $count = 0;
        foreach ( $authors as $author ) {
            if ( $count >= $n ) {
                break; // break out of the loop if $count becomes greater than $n.
            }

            $profile_pic_id = $this->get_profile_pic_id( $author['id'] );

            if ( $profile_pic_id ) {
                $settings['thumbnail_size'] = [
                    'id' => $profile_pic_id,
                ];
            } else {
                continue; // skip this author if they don't have a profile pic.
            }
            $count++;
            ?>

            
            <article <?php post_class( $classes ); ?>>
            <a class="elementor-post__thumbnail__link" href="<?php echo $author['archive']; ?>">
    
			<div class="elementor-portfolio-item__img elementor-post__thumbnail">
				<?php Group_Control_Image_Size::print_attachment_image_html( $settings, 'thumbnail_size' ); ?>
			</div>


			<div class="elementor-portfolio-item__overlay">
			<?php


			// TITLE
			$tag = $this->get_settings( 'title_tag' );
			?>
			<<?php Utils::print_validated_html_tag( $tag ); ?> class="elementor-portfolio-item__title">
			<?php echo $author['name']; ?>
			</<?php Utils::print_validated_html_tag( $tag ); ?>>


			</div>
			</a>
			</article>
			<?php
		}	// END LOOP

		?>
		</div>
		<?php

	} // render()

} // class