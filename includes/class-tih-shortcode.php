<?php
if (!defined('ABSPATH')) exit;

class TIH_Shortcode {
  public static function register() {
    add_shortcode('tih', [__CLASS__, 'render']);
  }

  public static function render($atts) {
    $atts = shortcode_atts([
      'type'  => 'events',  // events|births|deaths
      'month' => '',        // 1..12 (optional)
      'day'   => '',        // 1..31 (optional)
      'limit' => '10'       // number of items
    ], $atts, 'tih');

    $type  = sanitize_key($atts['type']);
    $month = $atts['month'] ? (int) $atts['month'] : null;
    $day   = $atts['day']   ? (int) $atts['day']   : null;
    $limit = max(1, min(50, (int) $atts['limit']));

    $data = TIH_API::get_data($type, $month, $day);
    if (!$data) {
      return '<div class="tih">'. esc_html__('No data available for this date.', 'today-in-history') .'</div>';
    }

    $items = array_slice($data['items'], 0, $limit);

    $type_label = _x('Events', 'type label', 'today-in-history');
    if ($type === 'births') $type_label = _x('Births', 'type label', 'today-in-history');
    if ($type === 'deaths') $type_label = _x('Deaths', 'type label', 'today-in-history');

    // Theme option
    $theme = get_option('tih_theme', 'auto'); // auto|light|dark
    $theme_class = 'tih-theme-auto';
    if ($theme === 'light') $theme_class = 'tih-theme-light';
    if ($theme === 'dark')  $theme_class = 'tih-theme-dark';

    ob_start(); ?>
    <div class="tih <?php echo esc_attr($theme_class); ?>" data-type="<?php echo esc_attr($type); ?>">
      <h3 class="tih-title">
        <?php
          /* translators: %1$s: formatted date (e.g. August 14), %2$s: category (Events/Births/Deaths) */
          printf(esc_html__('Today in History — %1$s (%2$s)', 'today-in-history'),
            esc_html($data['date']),
            esc_html($type_label)
          );
        ?>
      </h3>
      <ul class="tih-list">
        <?php foreach ($items as $it): ?>
          <li class="tih-item">
            <strong class="tih-year"><?php echo esc_html($it['year']); ?></strong>
            <span class="tih-desc"><?php echo esc_html($it['description']); ?></span>
            <?php if (!empty($it['wiki'])): ?>
              <a class="tih-link" href="<?php echo esc_url($it['wiki']); ?>" target="_blank" rel="noopener">
                <?php echo esc_html_x('[Wiki]', 'external link label', 'today-in-history'); ?>
              </a>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php
    return ob_get_clean();
  }
}
