<?php
/*
Plugin Name: Elementor UTM Manager
Plugin URI: https://github.com/ricardochristovao
Description: UTM e Query Parameter Manager para botões do Elementor
Version: 1.0
Author: Ricardo Christovão
Author URI: https://github.com/ricardochristovao
Text Domain: elementor-utm-manager
*/

if (!defined('ABSPATH')) exit;

define('ELEMENTOR_UTM_MANAGER_VERSION', '1.0.0');
define('ELEMENTOR_UTM_MANAGER_FILE', __FILE__);
define('ELEMENTOR_UTM_MANAGER_PATH', plugin_dir_path(__FILE__));
define('ELEMENTOR_UTM_MANAGER_URL', plugin_dir_url(__FILE__));

require_once ELEMENTOR_UTM_MANAGER_PATH . 'includes/class-elementor-utm-manager.php';

// Inicializar o plugin
add_action('plugins_loaded', 'elementor_utm_manager_init');

function elementor_utm_manager_init() {
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', 'elementor_utm_manager_fail_load');
        return;
    }
    \ElementorUTMManager\Plugin::instance();
}

function elementor_utm_manager_fail_load() {
    $message = sprintf(
        esc_html__('Elementor UTM Manager requires Elementor to be installed and activated.', 'elementor-utm-manager')
    );
    echo '<div class="notice notice-error"><p>' . $message . '</p></div>';
}

add_action('init', function() {
    load_plugin_textdomain('elementor-utm-manager', false, dirname(plugin_basename(__FILE__)) . '/languages');
});