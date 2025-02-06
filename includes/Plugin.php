<?php
namespace BYOB;

use BYOB\Admin\Settings\BatteryOptions;

class Plugin {
    private $post_type;
    private $shortcode;
    private $meta_boxes;
    private $settings;

    public function init(): void {
        $this->init_post_type();
        $this->init_meta_boxes();
        $this->init_shortcode();
        $this->init_settings();
        
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'frontend_scripts']);
        add_action('wp_head', [$this, 'output_custom_css']);
    }

    private function init_post_type(): void {
        $this->post_type = new PostTypes\Battery();
        $this->post_type->register();
    }

    private function init_meta_boxes(): void {
        $this->meta_boxes = new Admin\BatteryMetaBoxes();
        $this->meta_boxes->init();
    }

    private function init_shortcode(): void {
        $this->shortcode = new Shortcodes\BatteryBuilder();
        $this->shortcode->register();
    }

    private function init_settings(): void {
        $this->settings = new Admin\Settings\BatteryOptions();
        $this->settings->init();
    }

    public function admin_scripts(): void {
        wp_enqueue_style(
            'byob-admin',
            BYOB_PLUGIN_URL . 'assets/css/admin.css',
            [],
            BYOB_VERSION
        );

        wp_enqueue_script(
            'byob-admin',
            BYOB_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            BYOB_VERSION,
            true
        );
    }

    public function frontend_scripts(): void {
        // Enqueue jQuery if not already loaded
        wp_enqueue_script('jquery');

        // Enqueue our frontend script
        wp_enqueue_script(
            'byob-frontend',
            BYOB_PLUGIN_URL . 'assets/js/frontend.js',
            ['jquery'],
            BYOB_VERSION,
            true
        );

        // Localize script with ajax url and nonce
        wp_localize_script(
            'byob-frontend',
            'byobData',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('filter_batteries')
            ]
        );
    }

    public function output_custom_css(): void {
        // Only output CSS if we're on a page with our shortcode
        global $post;
        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'batterybuilder')) {
            return;
        }

        $options = $this->settings->get_options();
        $custom_css = $options['custom_css'] ?? '';

        if (!empty($custom_css)) {
            echo "\n<!-- Battery Builder Custom CSS -->\n";
            echo "<style type='text/css'>\n";
            echo wp_strip_all_tags($custom_css) . "\n";
            echo "</style>\n";
        }
    }
} 