<?php
namespace ElementorUTMManager;

if (!defined('ABSPATH')) exit;

class Plugin {
    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action('elementor/element/button/section_button/after_section_end', 
            [$this, 'add_utm_controls'], 10, 2);
        add_action('elementor/frontend/widget/before_render', 
            [$this, 'before_render'], 10, 2);
        add_action('elementor/frontend/after_register_scripts', 
            [$this, 'register_frontend_assets']);
    }

    public function register_frontend_assets() {
        wp_register_script(
            'elementor-utm-manager',
            ELEMENTOR_UTM_MANAGER_URL . 'assets/js/utm-manager.js',
            ['jquery'],
            ELEMENTOR_UTM_MANAGER_VERSION,
            true
        );

        wp_register_style(
            'elementor-utm-manager',
            ELEMENTOR_UTM_MANAGER_URL . 'assets/css/utm-manager.css',
            [],
            ELEMENTOR_UTM_MANAGER_VERSION
        );
    }

    public function add_utm_controls($element, $args) {
        $element->start_controls_section(
            'section_utm',
            [
                'label' => __('UTM Settings', 'elementor-utm-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $element->add_control(
            'copy_queries',
            [
                'label' => __('Copy URL Parameters', 'elementor-utm-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', 'elementor-utm-manager'),
                    'utm_only' => __('UTM Parameters Only', 'elementor-utm-manager'),
                    'all' => __('All Parameters', 'elementor-utm-manager'),
                ],
            ]
        );

        $element->end_controls_section();
    }

    public function before_render($widget) {
        if ($widget->get_name() !== 'button') {
            return;
        }

        $settings = $widget->get_settings_for_display();
        
        if (empty($settings['link']['url']) || $settings['copy_queries'] === 'none') {
            return;
        }

        add_filter('elementor/widget/render_content', function($content) use ($settings) {
            $current_url = add_query_arg(null, null);
            $parsed_current_url = parse_url($current_url);
            
            if (empty($parsed_current_url['query'])) {
                return $content;
            }

            $queries = [];
            parse_str($parsed_current_url['query'], $queries);
            
            $target_url = $settings['link']['url'];
            
            // Filtrar queries baseado na configuração
            $filtered_queries = [];
            foreach ($queries as $key => $value) {
                if ($settings['copy_queries'] === 'utm_only') {
                    if (strpos($key, 'utm_') === 0) {
                        $filtered_queries[$key] = $value;
                    }
                } else if ($settings['copy_queries'] === 'all') {
                    $filtered_queries[$key] = $value;
                }
            }

            // Adicionar queries ao URL de destino
            if (!empty($filtered_queries)) {
                $target_url = add_query_arg($filtered_queries, $target_url);
                $content = str_replace($settings['link']['url'], $target_url, $content);
            }

            return $content;
        }, 10, 1);
    }
}