<?php
namespace BYOB\Admin\Settings;

class BatteryOptions {
    private const OPTION_KEY = 'byob_battery_options';
    private const DEFAULT_CSS = '/**
 * Battery Builder Frontend Styles
 * 
 * This CSS controls all the styling for the battery builder form and results.
 * Sections:
 * 1. Container Layout
 * 2. Selection Paths (Tab Buttons)
 * 3. Form Styles
 * 4. Radio & Checkbox Inputs
 * 5. Submit Button
 * 6. Results Grid
 * 7. Battery Cards
 * 8. Status Messages
 */

/* 1. Container Layout */
.battery-builder-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* 2. Selection Paths */
.selection-paths {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 30px;
}

.path-selector {
    padding: 15px 30px;
    font-size: 1.1em;
    font-weight: bold;
    border: 2px solid #007bff;
    border-radius: 8px;
    background: #fff;
    color: #007bff;
    cursor: pointer;
    transition: all 0.3s ease;
}

.path-selector:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
}

.path-selector.active {
    background: #007bff;
    color: #fff;
}

/* 3. Form Styles */
.battery-filter-form {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    transition: all 0.3s ease;
    display: none;
}

.battery-filter-form.active {
    display: block;
}

.battery-filter-form.hidden {
    display: none;
}

.filter-group {
    margin-bottom: 20px;
}

.filter-group > label {
    display: block;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

/* 4. Radio & Checkbox Inputs */
.radio-group,
.checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.radio-group label,
.checkbox-group label {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 12px 20px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    transition: all 0.3s ease;
    font-size: 1.1em;
}

.radio-group label:hover,
.checkbox-group label:hover {
    background: #f0f0f0;
}

.radio-group input[type="radio"],
.checkbox-group input[type="checkbox"] {
    margin-right: 8px;
}

.radio-group input[type="radio"]:checked + label {
    background: #007bff;
    color: #fff;
    border-color: #0056b3;
}

.checkbox-group input[type="checkbox"]:checked + label {
    background: #28a745;
    color: #fff;
    border-color: #218838;
}

/* 5. Submit Button */
.submit-group {
    margin-top: 30px;
    text-align: center;
}

.build-battery-btn {
    background: #28a745;
    color: #fff;
    border: none;
    padding: 15px 40px;
    font-size: 1.2em;
    font-weight: bold;
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.build-battery-btn:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 6px 8px rgba(0,0,0,0.15);
}

.build-battery-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* 6. Results Grid */
.battery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

/* 7. Battery Cards */
.battery-item {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.battery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.battery-image {
    width: 100%;
    aspect-ratio: 1;
    overflow: hidden;
}

.battery-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.battery-title {
    padding: 15px;
    margin: 0;
    font-size: 1.2em;
    color: #333;
    text-align: center;
}

.battery-specs {
    padding: 0 15px 15px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
}

.battery-specs span,
.battery-specs .use-cases {
    background: #f5f5f5;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.9em;
    color: #666;
}

.battery-specs .use-cases {
    width: 100%;
    text-align: center;
    margin-top: 5px;
}

.battery-link {
    display: block;
    text-align: center;
    padding: 12px 20px;
    background: #007bff;
    color: #fff;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.battery-link:hover {
    background: #0056b3;
    color: #fff;
}

/* 8. Status Messages */
.loading,
.no-results,
.error {
    text-align: center;
    padding: 30px;
    background: #f5f5f5;
    border-radius: 8px;
    color: #666;
    font-style: italic;
}

.error {
    background: #f8d7da;
    color: #dc3545;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .selection-paths {
        flex-direction: column;
        gap: 10px;
    }

    .path-selector {
        width: 100%;
    }

    .radio-group label,
    .checkbox-group label {
        width: 100%;
        justify-content: center;
    }

    .battery-grid {
        grid-template-columns: 1fr;
    }
}';
    
    public function init(): void {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_byob_save_options', [$this, 'save_options']);
    }

    public function add_settings_page(): void {
        add_submenu_page(
            'edit.php?post_type=battery',
            __('Battery Options', 'build-your-own-battery'),
            __('Options', 'build-your-own-battery'),
            'manage_options',
            'battery-options',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings(): void {
        register_setting('byob_options', self::OPTION_KEY);
    }

    public function get_options(): array {
        $defaults = [
            'voltage_options' => ['12v', '48v'],
            'capacity_options' => ['50Ah', '100Ah', '200Ah', '300Ah'],
            'use_cases' => ['Home Backup', 'Solar Storage', 'RV/Marine'],
            'custom_css' => self::DEFAULT_CSS
        ];

        return get_option(self::OPTION_KEY, $defaults);
    }

    public function render_settings_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $options = $this->get_options();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="battery-options-container">
                <div class="options-group">
                    <h2><?php _e('Voltage Options', 'build-your-own-battery'); ?></h2>
                    <div class="option-list" id="voltage-options">
                        <?php foreach ($options['voltage_options'] as $voltage): ?>
                            <div class="option-item">
                                <input type="text" value="<?php echo esc_attr($voltage); ?>" 
                                       name="voltage_options[]" class="regular-text">
                                <button type="button" class="button remove-option">
                                    <?php _e('Remove', 'build-your-own-battery'); ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="button add-option" data-target="voltage-options">
                        <?php _e('Add Voltage Option', 'build-your-own-battery'); ?>
                    </button>
                </div>

                <div class="options-group">
                    <h2><?php _e('Capacity Options', 'build-your-own-battery'); ?></h2>
                    <div class="option-list" id="capacity-options">
                        <?php foreach ($options['capacity_options'] as $capacity): ?>
                            <div class="option-item">
                                <input type="text" value="<?php echo esc_attr($capacity); ?>" 
                                       name="capacity_options[]" class="regular-text">
                                <button type="button" class="button remove-option">
                                    <?php _e('Remove', 'build-your-own-battery'); ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="button add-option" data-target="capacity-options">
                        <?php _e('Add Capacity Option', 'build-your-own-battery'); ?>
                    </button>
                </div>

                <div class="options-group">
                    <h2><?php _e('Use Cases', 'build-your-own-battery'); ?></h2>
                    <p class="description">
                        <?php _e('Add use cases that users can select when filtering batteries.', 'build-your-own-battery'); ?>
                    </p>
                    <div class="option-list" id="use-cases">
                        <?php if (!empty($options['use_cases'])): ?>
                            <?php foreach ($options['use_cases'] as $use_case): ?>
                                <div class="option-item">
                                    <input type="text" value="<?php echo esc_attr($use_case); ?>" 
                                           name="use_cases[]" class="regular-text">
                                    <button type="button" class="button remove-option">
                                        <?php _e('Remove', 'build-your-own-battery'); ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="button add-option" data-target="use-cases">
                        <?php _e('Add Use Case', 'build-your-own-battery'); ?>
                    </button>
                </div>

                <div class="options-group">
                    <h2><?php _e('Custom CSS', 'build-your-own-battery'); ?></h2>
                    <p class="description">
                        <?php _e('Customize the appearance of the battery builder form and results.', 'build-your-own-battery'); ?>
                    </p>
                    <textarea id="custom-css" name="custom_css" rows="20" class="large-text code" style="margin-top: 10px;"><?php 
                        echo esc_textarea($options['custom_css'] ?? self::DEFAULT_CSS); 
                    ?></textarea>
                    <p class="description">
                        <?php _e('Changes will be applied after saving.', 'build-your-own-battery'); ?>
                    </p>
                </div>

                <div class="submit-container">
                    <button type="button" class="button button-primary" id="save-battery-options">
                        <?php _e('Save Options', 'build-your-own-battery'); ?>
                    </button>
                    <span class="spinner"></span>
                    <div class="notice notice-success inline" style="display: none;">
                        <p><?php _e('Options saved successfully!', 'build-your-own-battery'); ?></p>
                    </div>
                    <div class="notice notice-error inline" style="display: none;">
                        <p><?php _e('Error saving options. Please try again.', 'build-your-own-battery'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function enqueue_scripts($hook): void {
        if ($hook !== 'battery_page_battery-options') {
            return;
        }

        // Enqueue admin settings CSS
        wp_enqueue_style(
            'byob-admin-settings',
            BYOB_PLUGIN_URL . 'assets/css/admin-settings.css',
            [],
            BYOB_VERSION
        );

        // Enqueue jQuery
        wp_enqueue_script('jquery');

        // Enqueue admin settings JS
        wp_enqueue_script(
            'byob-admin-settings',
            BYOB_PLUGIN_URL . 'assets/js/admin-settings.js',
            ['jquery'],
            BYOB_VERSION . '.' . time(), // Add timestamp to prevent caching during development
            true
        );

        // Localize script with necessary data
        wp_localize_script(
            'byob-admin-settings',
            'byobSettings',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('byob_save_options'),
                'debug' => WP_DEBUG
            ]
        );
    }

    public function save_options(): void {
        check_ajax_referer('byob_save_options', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
            return;
        }

        // Get and sanitize all options
        $voltage_options = isset($_POST['voltage_options']) ? array_map('sanitize_text_field', (array) $_POST['voltage_options']) : [];
        $capacity_options = isset($_POST['capacity_options']) ? array_map('sanitize_text_field', (array) $_POST['capacity_options']) : [];
        $use_cases = isset($_POST['use_cases']) ? array_map('sanitize_text_field', (array) $_POST['use_cases']) : [];
        $custom_css = isset($_POST['custom_css']) ? wp_strip_all_tags($_POST['custom_css']) : self::DEFAULT_CSS;

        // Filter out empty values and prepare options array
        $options = [
            'voltage_options' => array_values(array_filter($voltage_options)),
            'capacity_options' => array_values(array_filter($capacity_options)),
            'use_cases' => array_values(array_filter($use_cases)),
            'custom_css' => $custom_css
        ];

        // Update options and send response
        if (update_option(self::OPTION_KEY, $options)) {
            wp_send_json_success('Options saved successfully');
        } else {
            wp_send_json_error('Error saving options');
        }
    }
} 