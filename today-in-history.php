<?php
/**
 * Plugin Name: Today X History
 * Description: Display historical events, births, and deaths for a given date (defaults to today) using the "On This Day" API (byabbe.se). Shortcode + block, with server-side caching.
 * Version: 1.1.0
 * Author: SaiyanWeb
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: today-x-history
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('TIH_DIR', plugin_dir_path(__FILE__));
define('TIH_URL', plugin_dir_url(__FILE__));

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
