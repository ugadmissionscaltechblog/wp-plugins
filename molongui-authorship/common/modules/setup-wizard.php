<?php

namespace Molongui\Authorship\Common\Modules;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Setup_Wizard
{
    private $slug;
    private $css;
    private $markup;
    public function __construct()
    {
        $this->slug   = apply_filters( 'authorship/wizard_slug', MOLONGUI_AUTHORSHIP_NAME . '-setup-wizard' );
        $this->markup = apply_filters( 'authorship/wizard_markup', MOLONGUI_AUTHORSHIP_DIR . 'views/admin/html-setup-wizard.php' );

        $this->css    = is_rtl() ? 'common/modules/wizard/assets/css/styles-rtl.adb4.min.css' : 'common/modules/wizard/assets/css/styles.eff3.min.css';

        add_action( 'admin_init', array( $this, 'maybe_load_wizard' ) );
        add_action( 'admin_init', array( $this, 'maybe_redirect_after_activation' ), PHP_INT_MAX );
        add_action( 'admin_menu', array( $this, 'add_dashboard_page' ), 20 );

        add_action( 'wp_ajax_save_wizard_settings', array( $this, 'save_wizard_settings' ) );
    }
    public function maybe_load_wizard()
    {
        if ( wp_doing_ajax() )
        {
            return;
        }
        if ( !current_user_can( 'manage_options' ) )
        {
            return;
        }
        if ( !isset( $_GET['page'] ) or $this->slug !== sanitize_key( $_GET['page'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            return;
        }
        if ( !$this->should_setup_wizard_load() )
        {
            return;
        }
        if ( !file_exists( MOLONGUI_AUTHORSHIP_DIR . $this->css ) )
        {

            $fallback = apply_filters( 'authorship/wizard_fallback', '' );
            wp_safe_redirect( admin_url( $fallback ) );
            exit;
        }

        set_current_screen();

        $this->load_setup_wizard();
    }
    public function should_setup_wizard_load()
    {
        return (bool) apply_filters( 'authorship/load_setup_wizard', true );
    }
    private function load_setup_wizard()
    {
        do_action( 'authorship/before_wizard_load', $this );

        $this->setup_wizard_header();
        $this->setup_wizard_content();
        $this->setup_wizard_footer();
        do_action( 'authorship/after_wizard_load', $this );

        exit;
    }
    public function setup_wizard_header()
    {
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
            <head>
                <meta name="viewport" content="width=device-width"/>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <title>
                    <?php
                    /*! translators: %s: The plugin name. */
                    printf( esc_html__( "%s &rsaquo; Setup Wizard", 'molongui-authorship' ), esc_html( MOLONGUI_AUTHORSHIP_TITLE ) );
                    ?>
                </title>
                <link rel="stylesheet" id="<?php echo esc_attr( MOLONGUI_AUTHORSHIP_NAME ); ?>-setup-wizard-css" href="<?php echo esc_url( MOLONGUI_AUTHORSHIP_URL . $this->css . '?ver='.MOLONGUI_AUTHORSHIP_VERSION ); ?>" media="all">
            </head>
            <body class="<?php echo esc_attr( $this->slug ); ?> molongui-setup-wizard-welcome">
        <?php
    }
    public function setup_wizard_content()
    {
        if ( file_exists( $this->markup ) )
        {
            include $this->markup;
        }
        else
        {

            echo '<div class="warning">' . sprintf( "No content for this Wizard found. Please check the %s file exists.", esc_html( $this->markup ) ) . '</div>';
        }
    }
    public function setup_wizard_footer()
    {
        $ajaxurl  = admin_url( 'admin-ajax.php' );
        $nonce    = wp_create_nonce( MOLONGUI_AUTHORSHIP_ID.'_setup_wizard' );
        $redirect = admin_url( apply_filters( 'authorship/wizard_fallback', '' ) );
        $upgrade  = MOLONGUI_AUTHORSHIP_WEB;
        ?>
        <script type="text/javascript">var authorshipSetupWizard = {"ajaxurl":"<?php echo esc_url( $ajaxurl ); ?>","nonce":"<?php echo esc_html( $nonce ); ?>","redirect":"<?php echo esc_url( $redirect ); ?>","upgrade":"<?php echo esc_url( $upgrade ); ?>"};</script>
        </body>
        </html>
        <?php
    }
    public function maybe_redirect_after_activation() // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
    {
        /*!
         * FILTER HOOK
         *
         * Allows disabling redirection to the wizard after installation.
         *
         * @since 3.0.0
         */
        if ( apply_filters( 'authorship/prevent_wizard_redirect', false ) )
        {
            return;
        }
        if ( wp_doing_ajax() or wp_doing_cron() )
        {
            return;
        }
        if ( !get_transient( MOLONGUI_AUTHORSHIP_NAME.'-activation-redirect' ) )
        {
            return;
        }
        delete_transient( MOLONGUI_AUTHORSHIP_NAME.'-activation-redirect' );
        if ( isset( $_GET['activate-multi'] ) or is_network_admin() ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            return;
        }

        $install = get_option( MOLONGUI_AUTHORSHIP_INSTALL );

        if ( !$install )
        {
            return;
        }
        if ( empty( $install['timestamp'] ) or $install['timestamp'] > strtotime( "-2 days" ) )
        {
            wp_safe_redirect( $this->get_url() );
            exit;
        }
    }
    public function get_url()
    {
        return admin_url( 'index.php?page=' . $this->slug );
    }
    public function add_dashboard_page()
    {
        if ( !$this->should_setup_wizard_load() )
        {
            return;
        }

        add_submenu_page( '', '', '', 'manage_options', $this->slug, '' );
    }
    public function save_wizard_settings()
    {
        check_ajax_referer( MOLONGUI_AUTHORSHIP_ID.'_setup_wizard', 'nonce' );
        if ( !current_user_can( 'manage_options' ) )
        {
            wp_send_json_error('Insufficient permissions' );
            return;
        }
        if ( isset( $_POST ) )
        {
            $wizard_settings = array();
            $wizard_settings = apply_filters( 'authorship/wizard_settings', $wizard_settings );
            $options = Settings::get();
            update_option( MOLONGUI_AUTHORSHIP_PREFIX.'_options', array_merge( $options, $wizard_settings ) );

            wp_send_json_success( array( 'Settings saved', wp_json_encode( $wizard_settings ) ) );
        }
        else
        {
            wp_send_json_error( 'No data received' );
        }
    }
    public function render_timeline( $steps, $current = 0 )
    {
        --$current;
        ?>
        <div class="molongui-setup-wizard-timeline">
        <?php
        for ( $i=0; $i<$steps; $i++ )
        {
            $class = 'molongui-setup-wizard-timeline-step';
            if ( $i < $current )
            {
                $class .= ' molongui-setup-wizard-timeline-step-completed';
            }
            elseif ( $i === $current )
            {
                $class .= ' molongui-setup-wizard-timeline-step-active';
            }
            ?>
            <div class="<?php echo esc_attr( $class ); ?>"><svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" role="img" class="icon icon-success" data-icon="check" data-prefix="fas" focusable="false" aria-hidden="true" width="10" height="10"><path xmlns="http://www.w3.org/2000/svg" fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path></svg><svg viewBox="0 0 352 512" xmlns="http://www.w3.org/2000/svg" role="img" class="icon icon-failed" data-icon="times" data-prefix="fas" focusable="false" aria-hidden="true" width="8" height="11"><path xmlns="http://www.w3.org/2000/svg" fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg></div>
            <?php
            if ( $i < $steps-1 )
            {
                ?>
                <div class="molongui-setup-wizard-timeline-step-line"></div>
                <?php
            }
        }
        ?>
        </div>
        <?php
    }
    public function render_long_checkbox( $id, $label, $description, $checked = false, $disabled = false, $pro = false )
    {
        $label_class = "settings-input-long-checkbox";
        $input_class = "checkbox";
        if ( $checked )
        {
            $label_class .= " settings-input-long-checkbox-checked";
            $input_class .= " checkbox-checked";
        }
        if ( $disabled )
        {
            $label_class .= " settings-input-long-checkbox-disabled";
            $input_class .= " checkbox-disabled";
        }
        ?>

        <label for="molongui-settings-long-checkbox-<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $label_class ); ?>">
            <span class="settings-input-long-checkbox-container">
                <input id="molongui-settings-long-checkbox-<?php echo esc_attr( $id ); ?>" type="checkbox" name="<?php echo esc_attr( $id ); ?>" <?php checked( $checked ); disabled( $disabled ); ?>>
                <span class="<?php echo esc_attr( $input_class ); ?>">
                    <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" role="img" class="icon" data-icon="check" data-prefix="fas" focusable="false" aria-hidden="true" width="16" height="16">
                        <path xmlns="http://www.w3.org/2000/svg" fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path>
                    </svg>
                </span>
            </span>
            <div class="settings-input-long-checkbox-header">
                <span class="title-container">
                    <span class="label"><?php echo esc_html( $label ); ?></span>
                    <?php if ( $pro ) : ?>
                    <svg class="molongui-pro-badge" viewBox="0 0 46 26" height="24" width="46" xmlns="http://www.w3.org/2000/svg">
                        <defs xmlns="http://www.w3.org/2000/svg"><style>.a-prob{fill:#e6e6e6;}.b-prob{fill:#777;font-size:12px;font-weight:500;text-transform:uppercase;}</style></defs>
                        <rect xmlns="http://www.w3.org/2000/svg" class="a-prob" width="46" height="26" rx="3"></rect>
                        <text xmlns="http://www.w3.org/2000/svg" class="b-prob" transform="translate(9.999 17)"><tspan x="0" y="0"><?php esc_html_e( "Pro", 'molongui-authorship' ); ?></tspan></text>
                    </svg>
                    <?php endif; ?>
                </span>
                <p class="description"><?php echo wp_kses_post( $description ); ?></p>
            </div>
        </label>

        <?php
    }
    public function render_radio( $id, $label, $description, $value, $checked = false, $disabled = false, $pro = false )
    {
        $label_class = "settings-input-long-radio";
        $input_class = "molongui-styled-radio";
        if ( $checked )
        {
            $label_class .= " molongui-styled-radio-label-checked";
            $input_class .= " molongui-styled-radio-checked";
        }
        if ( $disabled )
        {
            $label_class .= " molongui-styled-radio-label-disabled";
            $input_class .= " molongui-styled-radio-disabled";
        }
        ?>

        <label for="molongui-settings-radio-<?php echo esc_attr( $id ); ?>[<?php echo esc_attr( $value ); ?>]" class="<?php echo esc_attr( $label_class ); ?>">
            <input id="molongui-settings-radio-<?php echo esc_attr( $id ); ?>[<?php echo esc_attr( $value ); ?>]" type="radio" name="<?php echo esc_attr( $id ); ?>" autocomplete="off" value="<?php echo esc_attr( $value ); ?>" <?php checked( $checked ); disabled( $disabled ); ?>>
            <span class="<?php echo esc_attr( $input_class ); ?>">
                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" role="img" class="icon" data-icon="check" data-prefix="fas" focusable="false" aria-hidden="true" width="16" height="16">
                    <path xmlns="http://www.w3.org/2000/svg" fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path>
                </svg>
            </span>
            <?php
            $img = 'assets/img/wizard/'.$value.'svg';
            if ( file_exists( MOLONGUI_AUTHORSHIP_DIR . $img ) ) : ?>
                <img src="<?php echo esc_url( MOLONGUI_AUTHORSHIP_URL . $img ); ?>" alt="<?php echo esc_attr( $value ); ?>" class="molongui-logo-icon">
            <?php endif; ?>
            <span class="molongui-styled-radio-text"><?php echo esc_html( $label ); ?></span>
            <?php if ( $pro ) : ?>
                <svg class="molongui-pro-badge" viewBox="0 0 46 26" height="24" width="46" xmlns="http://www.w3.org/2000/svg">
                    <defs xmlns="http://www.w3.org/2000/svg"><style>.a-prob{fill:#e6e6e6;}.b-prob{fill:#777;font-size:12px;font-weight:500;text-transform:uppercase;}</style></defs>
                    <rect xmlns="http://www.w3.org/2000/svg" class="a-prob" width="46" height="26" rx="3"></rect>
                    <text xmlns="http://www.w3.org/2000/svg" class="b-prob" transform="translate(9.999 17)"><tspan x="0" y="0"><?php esc_html_e( "Pro", 'molongui-authorship' ); ?></tspan></text>
                </svg>
            <?php endif; ?>
            <span class="molongui-styled-radio-description"><?php echo wp_kses_post( $description ); ?></span>
        </label>

        <?php
    }
}