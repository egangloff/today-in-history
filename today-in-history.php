<?php
/**
 * Plugin Name: TodayInHistory
 * Description: Display historical events, births, and deaths for a given date (defaults to today) using the "On This Day" API from byabbe.se. Includes shortcode + Gutenberg block, with server-side caching and settings.
 * Version: 1.1.0
 * Author: SaiyanWeb
 * Update URI: false
 * Text Domain: today-in-history
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('TIH_DIR', plugin_dir_path(__FILE__));
define('TIH_URL', plugin_dir_url(__FILE__));

function tih_load_textdomain() {
  load_plugin_textdomain('today-in-history', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'tih_load_textdomain');

require_once TIH_DIR . 'includes/class-tih-api.php';
require_once TIH_DIR . 'includes/class-tih-shortcode.php';
require_once TIH_DIR . 'includes/class-tih-block.php';
require_once TIH_DIR . 'includes/class-tih-settings.php';

// Register
add_action('init', ['TIH_Shortcode', 'register']);
add_action('rest_api_init', ['TIH_API', 'register_routes']);
add_action('init', ['TIH_Block', 'register']);
add_action('admin_init', ['TIH_Settings', 'register_settings']);
add_action('admin_menu', ['TIH_Settings', 'add_settings_page']);

// Enqueue styles (front + editor)
function tih_enqueue_styles() {
  wp_register_style('tih-styles', TIH_URL . 'assets/tih.css', [], '1.1.0');
  wp_enqueue_style('tih-styles');
}
add_action('wp_enqueue_scripts', 'tih_enqueue_styles');
add_action('enqueue_block_assets', 'tih_enqueue_styles');
