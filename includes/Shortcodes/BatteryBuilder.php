<?php
namespace BYOB\Shortcodes;

use BYOB\PostTypes\Battery;
use BYOB\Admin\Settings\BatteryOptions;

class BatteryBuilder {
    private $options;

    public function __construct() {
        $this->options = new BatteryOptions();
    }

    public function register(): void {
        add_shortcode('batterybuilder', [$this, 'render_shortcode']);
        add_action('wp_ajax_filter_batteries', [$this, 'filter_batteries']);
        add_action('wp_ajax_nopriv_filter_batteries', [$this, 'filter_batteries']);
    }

    public function render_shortcode($atts): string {
        $options = $this->options->get_options();
        $has_use_cases = !empty($options['use_cases']);
        
        ob_start();
        ?>
        <div class="battery-builder-container">
            <?php if ($has_use_cases): ?>
            <div class="selection-paths">
                <button type="button" class="path-selector active" data-path="specifications">
                    <?php _e('Find by Specifications', 'build-your-own-battery'); ?>
                </button>
                <button type="button" class="path-selector" data-path="use-case">
                    <?php _e('Find by Use Case', 'build-your-own-battery'); ?>
                </button>
            </div>
            <?php endif; ?>

            <form id="battery-filter-form" class="battery-filter-form active">
                <?php wp_nonce_field('filter_batteries', 'battery_filter_nonce'); ?>
                
                <div class="filter-group">
                    <label><?php _e('Voltage (Optional)', 'build-your-own-battery'); ?></label>
                    <div class="radio-group">
                        <?php foreach ($options['voltage_options'] as $voltage): ?>
                            <label>
                                <input type="radio" name="voltage" value="<?php echo esc_attr($voltage); ?>">
                                <?php echo esc_html($voltage); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-group">
                    <label><?php _e('Capacity (Optional)', 'build-your-own-battery'); ?></label>
                    <div class="radio-group">
                        <?php foreach ($options['capacity_options'] as $capacity): ?>
                            <label>
                                <input type="radio" name="capacity" value="<?php echo esc_attr($capacity); ?>">
                                <?php echo esc_html($capacity); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-group">
                    <label><?php _e('Self Heating (Optional)', 'build-your-own-battery'); ?></label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="self_heating" value="yes">
                            <?php _e('Yes', 'build-your-own-battery'); ?>
                        </label>
                        <label>
                            <input type="radio" name="self_heating" value="no">
                            <?php _e('No', 'build-your-own-battery'); ?>
                        </label>
                    </div>
                </div>

                <div class="filter-group submit-group">
                    <button type="button" id="build-battery-btn" class="build-battery-btn">
                        <?php _e('Find Batteries', 'build-your-own-battery'); ?>
                    </button>
                </div>
            </form>

            <?php if ($has_use_cases): ?>
            <form id="use-case-form" class="battery-filter-form hidden" style="display: none;">
                <?php wp_nonce_field('filter_batteries', 'battery_filter_nonce'); ?>
                
                <div class="filter-group">
                    <label><?php _e('Select Use Cases', 'build-your-own-battery'); ?></label>
                    <div class="checkbox-group use-case-group">
                        <?php foreach ($options['use_cases'] as $use_case): ?>
                            <label>
                                <input type="checkbox" name="use_cases[]" value="<?php echo esc_attr($use_case); ?>">
                                <?php echo esc_html($use_case); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-group submit-group">
                    <button type="button" id="find-by-use-case-btn" class="build-battery-btn">
                        <?php _e('Find Batteries', 'build-your-own-battery'); ?>
                    </button>
                </div>
            </form>
            <?php endif; ?>

            <div id="battery-results" class="battery-results" style="display: none;"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function filter_batteries(): void {
        check_ajax_referer('filter_batteries', 'nonce');

        $voltage = sanitize_text_field($_POST['voltage'] ?? '');
        $capacity = sanitize_text_field($_POST['capacity'] ?? '');
        $self_heating = sanitize_text_field($_POST['self_heating'] ?? '');
        $use_cases = isset($_POST['use_cases']) ? array_map('sanitize_text_field', (array) $_POST['use_cases']) : [];

        $html = $this->get_batteries_html($voltage, $capacity, $self_heating, $use_cases);
        
        if (empty($html)) {
            $html = '<p class="no-results">' . __('No batteries found matching your criteria.', 'build-your-own-battery') . '</p>';
        }

        wp_send_json_success([
            'html' => $html,
            'count' => $this->get_batteries_count($voltage, $capacity, $self_heating, $use_cases)
        ]);
    }

    private function get_batteries_count(string $voltage, string $capacity, string $self_heating, array $use_cases = []): int {
        $args = $this->get_query_args($voltage, $capacity, $self_heating, $use_cases);
        $query = new \WP_Query($args);
        return $query->found_posts;
    }

    private function get_query_args(string $voltage, string $capacity, string $self_heating, array $use_cases = []): array {
        $args = [
            'post_type' => Battery::POST_TYPE,
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND'
            ]
        ];

        if ($voltage) {
            $args['meta_query'][] = [
                'key' => 'byob_voltage',
                'value' => $voltage,
                'compare' => '='
            ];
        }

        if ($capacity) {
            $args['meta_query'][] = [
                'key' => 'byob_capacity',
                'value' => $capacity,
                'compare' => '='
            ];
        }

        if ($self_heating) {
            $args['meta_query'][] = [
                'key' => 'byob_self_heating',
                'value' => $self_heating,
                'compare' => '='
            ];
        }

        if (!empty($use_cases)) {
            $use_case_query = ['relation' => 'OR'];
            foreach ($use_cases as $use_case) {
                $use_case_query[] = [
                    'key' => 'byob_use_cases',
                    'value' => $use_case,
                    'compare' => 'LIKE'
                ];
            }
            $args['meta_query'][] = $use_case_query;
        }

        return $args;
    }

    private function get_batteries_html(string $voltage = '', string $capacity = '', string $self_heating = '', array $use_cases = []): string {
        $args = $this->get_query_args($voltage, $capacity, $self_heating, $use_cases);
        $query = new \WP_Query($args);
        
        ob_start();

        if ($query->have_posts()) {
            echo '<div class="battery-grid">';
            while ($query->have_posts()) {
                $query->the_post();
                $product_link = get_post_meta(get_the_ID(), 'byob_product_link', true);
                $battery_use_cases = get_post_meta(get_the_ID(), 'byob_use_cases', true) ?: [];
                ?>
                <div class="battery-item">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="battery-image">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <h3 class="battery-title"><?php the_title(); ?></h3>
                    
                    <div class="battery-specs">
                        <span class="voltage">
                            <?php echo esc_html(get_post_meta(get_the_ID(), 'byob_voltage', true)); ?>
                        </span>
                        <span class="capacity">
                            <?php echo esc_html(get_post_meta(get_the_ID(), 'byob_capacity', true)); ?>
                        </span>
                        <span class="self-heating">
                            <?php _e('Self Heating:', 'build-your-own-battery'); ?> 
                            <?php echo esc_html(get_post_meta(get_the_ID(), 'byob_self_heating', true)); ?>
                        </span>
                        <?php if (!empty($battery_use_cases)): ?>
                            <div class="use-cases">
                                <?php _e('Use Cases:', 'build-your-own-battery'); ?>
                                <?php echo esc_html(implode(', ', $battery_use_cases)); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($product_link): ?>
                        <a href="<?php echo esc_url($product_link); ?>" class="battery-link" target="_blank">
                            <?php _e('View Product', 'build-your-own-battery'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <?php
            }
            echo '</div>';
        }

        wp_reset_postdata();
        return ob_get_clean();
    }
} 