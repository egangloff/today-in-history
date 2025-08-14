<?php
if (!defined('ABSPATH')) exit;

class TIH_Settings {
  public static function register_settings() {
    register_setting('tih_settings_group', 'tih_cache_ttl', [
      'type' => 'integer',
      'sanitize_callback' => function($v){ $v = (int)$v; return $v < 300 ? 300 : $v; }, // min 5min
      'default' => 43200, // 12h
    ]);
    register_setting('tih_settings_group', 'tih_theme', [
      'type' => 'string',
      'sanitize_callback' => function($v){ return in_array($v, ['auto','light','dark'], true) ? $v : 'auto'; },
      'default' => 'auto',
    ]);

    add_settings_section('tih_main', __('General Settings', 'today-x-history'), function(){
      echo '<p>'. esc_html__('Configure cache lifetime and display theme for the TodayInHistory plugin.', 'today-x-history') .'</p>';
    }, 'tih_settings');

    add_settings_field('tih_cache_ttl', __('Cache TTL (seconds)', 'today-x-history'), [__CLASS__, 'field_ttl'], 'tih_settings', 'tih_main');
    add_settings_field('tih_theme', __('Theme', 'today-x-history'), [__CLASS__, 'field_theme'], 'tih_settings', 'tih_main');
  }

  public static function add_settings_page() {
    add_options_page(
      __('TodayInHistory', 'today-x-history'),
      __('TodayInHistory', 'today-x-history'),
      'manage_options',
      'tih_settings',
      [__CLASS__, 'render_settings_page']
    );
  }

  public static function field_ttl() {
    $val = (int) get_option('tih_cache_ttl', 43200);
    echo '<input type="number" min="300" step="60" name="tih_cache_ttl" value="'. esc_attr($val) .'" class="regular-text" />';
    echo '<p class="description">'. esc_html__('Minimum 300 seconds (5 minutes). Higher values reduce API calls.', 'today-x-history') .'</p>';
  }

  public static function field_theme() {
    $val = get_option('tih_theme', 'auto');
    echo '<select name="tih_theme">';
    printf('<option value="auto"%s>%s</option>', selected($val,'auto',false), esc_html__('Auto (follow system)', 'today-x-history'));
    printf('<option value="light"%s>%s</option>', selected($val,'light',false), esc_html__('Light', 'today-x-history'));
    printf('<option value="dark"%s>%s</option>', selected($val,'dark',false), esc_html__('Dark', 'today-x-history'));
    echo '</select>';
  }

  public static function render_settings_page() { ?>
    <div class="wrap">
      <h1><?php echo esc_html__('TodayInHistory Settings', 'today-x-history'); ?></h1>
      <form method="post" action="options.php">
        <?php
          settings_fields('tih_settings_group');
          do_settings_sections('tih_settings');
          submit_button();
        ?>
      </form>
      <hr/>
      <p>
        <?php echo esc_html__('Use the shortcode', 'today-x-history'); ?>:
        <code>[tih]</code>,
        <code>[tih type="births" limit="5"]</code>,
        <code>[tih type="deaths" month="8" day="14"]</code>
      </p>
    </div>
  <?php }
}
