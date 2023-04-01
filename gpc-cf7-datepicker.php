<?php

/*
Plugin Name: GPC Contact form 7 Datepicker
Plugin URI: https://giaiphapclinic.com
Description: Add datepicker to contact form 7
Author: GPC Team
Version: 0.0.1
*/

define('GPC_CF7_DTP', __FILE__);
define('GPC_CF7_DTP_VERSION', '0.0.1');
define('GPC_CF7_DTP_BASE', plugin_basename(GPC_CF7_DTP));
define('GPC_CF7_DTP_DIR', plugin_dir_path(GPC_CF7_DTP));
define('GPC_CF7_DTP_URI', plugins_url('/', GPC_CF7_DTP));

class Gpc_Cf7_DateTimePicker
{
    public function __construct()
    {
        add_action('wpcf7_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wpcf7_enqueue_styles', [$this, 'enqueue_styles']);
        add_action('wpcf7_init', [$this, 'add_form_tags']);

        add_filter( 'wpcf7_validate_gpc_datepicker', [$this, 'wpcf7_text_validation_filter'], 10, 2 );
        add_filter( 'wpcf7_validate_gpc_datepicker*', [$this, 'wpcf7_text_validation_filter'], 10, 2 );
        add_filter( 'wpcf7_validate_gpc_datetimepicker', [$this, 'wpcf7_text_validation_filter'], 10, 2 );
        add_filter( 'wpcf7_validate_gpc_datetimepicker*', [$this, 'wpcf7_text_validation_filter'], 10, 2 );
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script(
            'gpc-cf7-datepicker-js',
            GPC_CF7_DTP_URI . 'assets/datetimepicker/jquery.datetimepicker.full.min.js',
            array('jquery'),
            GPC_CF7_DTP_VERSION,
            true
        );

        wp_enqueue_script(
            'gpc-cf7-datepicker',
            GPC_CF7_DTP_URI . 'assets/scripts.js',
            array('jquery'),
            GPC_CF7_DTP_VERSION,
            true
        );
    }

    public function enqueue_styles()
    {
        wp_enqueue_style(
            'gpc-cf7-datepicker-css',
            GPC_CF7_DTP_URI . 'assets/datetimepicker/jquery.datetimepicker.min.css',
            false,
            GPC_CF7_DTP_VERSION,
            'all'
        );
    }

    public function add_form_tags()
    {
        wpcf7_add_form_tag(
            array( 'gpc_datepicker', 'gpc_datepicker*', 'gpc_datetimepicker', 'gpc_datetimepicker*', ),
            [$this, 'datepicker_form_tag_handler'],
            array(
                'name-attr' => true,
            )
        );
    }

    public function datepicker_form_tag_handler($tag)
    {
        if ( empty( $tag->name ) ) {
            return '';
        }

        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = wpcf7_form_controls_class( $tag->type, 'wpcf7-text' );

        if ($tag->basetype == 'gpc_datepicker') {
            $class .=  ' gpc-cf7-datepicker';
        }

        if ($tag->basetype == 'gpc_datetimepicker') {
            $class .= ' gpc-cf7-datetimepicker';
        }


        $class .= ' wpcf7-validates-as-text';

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = array();

        $atts['size'] = $tag->get_size_option( '40' );
        $atts['maxlength'] = $tag->get_maxlength_option();
        $atts['minlength'] = $tag->get_minlength_option();

        if ( $atts['maxlength'] and $atts['minlength']
        and $atts['maxlength'] < $atts['minlength'] ) {
            unset( $atts['maxlength'], $atts['minlength'] );
        }

        $atts['class'] = $tag->get_class_option( $class );
        $atts['id'] = $tag->get_id_option();
        $atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

        $atts['autocomplete'] = $tag->get_option( 'autocomplete',
            '[-0-9a-zA-Z]+', true );

        if ( $tag->has_option( 'readonly' ) ) {
            $atts['readonly'] = 'readonly';
        }

        if ( $tag->is_required() ) {
            $atts['aria-required'] = 'true';
        }

        if ( $validation_error ) {
            $atts['aria-invalid'] = 'true';
            $atts['aria-describedby'] = wpcf7_get_validation_error_reference(
                $tag->name
            );
        } else {
            $atts['aria-invalid'] = 'false';
        }

        $value = (string) reset( $tag->values );

        if ( $tag->has_option( 'placeholder' )
        or $tag->has_option( 'watermark' ) ) {
            $atts['placeholder'] = $value;
            $value = '';
        }

        $value = $tag->get_default_option( $value );

        $value = wpcf7_get_hangover( $tag->name, $value );

        $atts['value'] = $value;
        $atts['type'] = 'text'; // input type
        $atts['name'] = $tag->name;

        $atts = wpcf7_format_atts( $atts );

        $html = sprintf(
            '<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
            sanitize_html_class( $tag->name ), $atts, $validation_error
        );

        return $html;
    }

    function wpcf7_text_validation_filter( $result, $tag ) {
        $name = $tag->name;

        $value = isset( $_POST[$name] )
            ? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
            : '';

        if ( $tag->is_required() and '' === $value ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
        }

        return $result;
    }
}

new Gpc_Cf7_DateTimePicker();
