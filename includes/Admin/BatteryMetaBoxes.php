<?php
namespace BYOB\Admin;

use BYOB\PostTypes\Battery;
use BYOB\Admin\Settings\BatteryOptions;

class BatteryMetaBoxes {
    private const META_KEY_PREFIX = 'byob_';
    private $options;
    
    public function __construct() {
        $this->options = new BatteryOptions();
    }

    public function init(): void {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . Battery::POST_TYPE, [$this, 'save_meta_boxes'], 10, 2);
    }

    public function add_meta_boxes(): void {
        add_meta_box(
            'battery_specifications',
            __('Battery Specifications', 'build-your-own-battery'),
            [$this, 'render_specifications_meta_box'],
            Battery::POST_TYPE,
            'normal',
            'high'
        );
    }

    public function render_specifications_meta_box(\WP_Post $post): void {
        wp_nonce_field('battery_meta_box', 'battery_meta_box_nonce');

        $voltage = get_post_meta($post->ID, self::META_KEY_PREFIX . 'voltage', true);
        $capacity = get_post_meta($post->ID, self::META_KEY_PREFIX . 'capacity', true);
        $self_heating = get_post_meta($post->ID, self::META_KEY_PREFIX . 'self_heating', true);
        $product_link = get_post_meta($post->ID, self::META_KEY_PREFIX . 'product_link', true);
        $use_cases = get_post_meta($post->ID, self::META_KEY_PREFIX . 'use_cases', true) ?: [];

        $options = $this->options->get_options();
        ?>
        <div class="battery-meta-box">
            <p>
                <label for="voltage"><?php _e('Voltage', 'build-your-own-battery'); ?></label><br>
                <select name="<?php echo esc_attr(self::META_KEY_PREFIX . 'voltage'); ?>" id="voltage">
                    <option value=""><?php _e('Select Voltage', 'build-your-own-battery'); ?></option>
                    <?php foreach ($options['voltage_options'] as $option): ?>
                        <option value="<?php echo esc_attr($option); ?>" <?php selected($voltage, $option); ?>>
                            <?php echo esc_html($option); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="capacity"><?php _e('Capacity', 'build-your-own-battery'); ?></label><br>
                <select name="<?php echo esc_attr(self::META_KEY_PREFIX . 'capacity'); ?>" id="capacity">
                    <option value=""><?php _e('Select Capacity', 'build-your-own-battery'); ?></option>
                    <?php foreach ($options['capacity_options'] as $option): ?>
                        <option value="<?php echo esc_attr($option); ?>" <?php selected($capacity, $option); ?>>
                            <?php echo esc_html($option); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="self_heating"><?php _e('Self Heating', 'build-your-own-battery'); ?></label><br>
                <select name="<?php echo esc_attr(self::META_KEY_PREFIX . 'self_heating'); ?>" id="self_heating">
                    <option value=""><?php _e('Select Self Heating Option', 'build-your-own-battery'); ?></option>
                    <option value="yes" <?php selected($self_heating, 'yes'); ?>>Yes</option>
                    <option value="no" <?php selected($self_heating, 'no'); ?>>No</option>
                </select>
            </p>

            <?php if (!empty($options['use_cases'])): ?>
            <p>
                <label><?php _e('Use Cases', 'build-your-own-battery'); ?></label><br>
                <div class="use-cases-list">
                    <?php foreach ($options['use_cases'] as $use_case): ?>
                        <label class="use-case-item">
                            <input type="checkbox" 
                                   name="<?php echo esc_attr(self::META_KEY_PREFIX . 'use_cases[]'); ?>" 
                                   value="<?php echo esc_attr($use_case); ?>"
                                   <?php checked(in_array($use_case, $use_cases)); ?>>
                            <?php echo esc_html($use_case); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </p>
            <?php endif; ?>

            <p>
                <label for="product_link"><?php _e('Product Link', 'build-your-own-battery'); ?></label><br>
                <input type="url" id="product_link" name="<?php echo esc_attr(self::META_KEY_PREFIX . 'product_link'); ?>" 
                       value="<?php echo esc_url($product_link); ?>" class="widefat">
            </p>
        </div>
        <?php
    }

    public function save_meta_boxes(int $post_id, \WP_Post $post): void {
        if (!isset($_POST['battery_meta_box_nonce']) || 
            !wp_verify_nonce($_POST['battery_meta_box_nonce'], 'battery_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = [
            'voltage',
            'capacity',
            'self_heating',
            'product_link'
        ];

        foreach ($fields as $field) {
            $key = self::META_KEY_PREFIX . $field;
            if (isset($_POST[$key])) {
                $value = sanitize_text_field($_POST[$key]);
                update_post_meta($post_id, $key, $value);
            }
        }

        // Handle use cases separately as it's an array
        $use_cases_key = self::META_KEY_PREFIX . 'use_cases';
        $use_cases = isset($_POST[$use_cases_key]) ? (array) $_POST[$use_cases_key] : [];
        $use_cases = array_map('sanitize_text_field', $use_cases);
        update_post_meta($post_id, $use_cases_key, $use_cases);
    }
} 