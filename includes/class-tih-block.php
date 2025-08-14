<?php
if (!defined('ABSPATH')) exit;

class TIH_Block {
  public static function register() {
    wp_register_script(
      'tih-block',
      TIH_URL . 'assets/block.js',
      ['wp-blocks','wp-element','wp-editor','wp-components','wp-data'],
      '1.1.0',
      true
    );

    register_block_type('tih/today', [
      'editor_script'   => 'tih-block',
      'render_callback' => [__CLASS__, 'render_block'],
      'attributes'      => [
        'type'  => ['type'=>'string', 'default'=>'events'],
        'month' => ['type'=>'number', 'default'=>0],
        'day'   => ['type'=>'number', 'default'=>0],
        'limit' => ['type'=>'number', 'default'=>10],
      ],
      'title'       => __('Today In History', 'today-x-history'),
      'description' => __('Display historical events, births, or deaths for a given date.', 'today-x-history'),
      'category'    => 'widgets',
      'icon'        => 'calendar-alt',
    ]);
  }

  public static function render_block($attrs) {
    $type  = isset($attrs['type'])  ? sanitize_key($attrs['type']) : 'events';
    $month = !empty($attrs['month']) ? (int) $attrs['month'] : null;
    $day   = !empty($attrs['day'])   ? (int) $attrs['day']   : null;
    $limit = isset($attrs['limit']) ? (int) $attrs['limit'] : 10;
    return TIH_Shortcode::render(compact('type','month','day','limit'));
  }
}
