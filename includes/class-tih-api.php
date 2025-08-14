<?php
if (!defined('ABSPATH')) exit;

class TIH_API {
  private static function fetch_source($type, $m, $d) {
    $type = in_array($type, ['events','births','deaths'], true) ? $type : 'events';
    $m = max(1, min(12, (int) $m));
    $d = max(1, min(31, (int) $d));
    $url = "https://byabbe.se/on-this-day/{$m}/{$d}/{$type}.json";
    $res = wp_remote_get($url, ['timeout' => 12]);
    if (is_wp_error($res)) return null;
    if ((int) wp_remote_retrieve_response_code($res) !== 200) return null;
    $body = json_decode(wp_remote_retrieve_body($res), true);
    return is_array($body) ? $body : null;
  }

  public static function get_data($type='events', $month=null, $day=null, $ttl=null) {
    $type = in_array($type, ['events','births','deaths'], true) ? $type : 'events';
    $now = current_time('timestamp');
    $m = $month ? (int) $month : (int) date('n', $now);
    $d = $day   ? (int) $day   : (int) date('j', $now);
    $cache_key = "tih_{$type}_{$m}_{$d}";

    if ($cached = get_transient($cache_key)) return $cached;

    $data = self::fetch_source($type, $m, $d);
    if (!$data) return null;

    $items = isset($data[$type]) && is_array($data[$type]) ? $data[$type] : [];
    $payload = [
      'date'  => $data['date'] ?? sprintf('%02d/%02d', $m, $d),
      'type'  => $type,
      'items' => array_map(function($it){
        return [
          'year'        => $it['year'] ?? '',
          'description' => $it['description'] ?? '',
          'wiki'        => !empty($it['wikipedia'][0]['wikipedia']) ? $it['wikipedia'][0]['wikipedia'] : '',
          'title'       => !empty($it['wikipedia'][0]['title']) ? $it['wikipedia'][0]['title'] : '',
        ];
      }, $items),
    ];

    // TTL from settings (seconds)
    $opt_ttl = (int) get_option('tih_cache_ttl', 43200); // 12h default
    $ttl_final = is_null($ttl) ? max(300, $opt_ttl) : max(300, (int) $ttl);
    set_transient($cache_key, $payload, $ttl_final);
    return $payload;
  }

  public static function register_routes() {
    register_rest_route('tih/v1', '/day', [
      'methods'  => 'GET',
      'callback' => function($req){
        $type  = sanitize_key($req->get_param('type') ?: 'events');
        $month = (int) ($req->get_param('month') ?: 0);
        $day   = (int) ($req->get_param('day') ?: 0);
        $limit = max(1, min(50, (int) ($req->get_param('limit') ?: 10)));

        $data = self::get_data($type, $month ?: null, $day ?: null);
        if (!$data) {
          return new WP_REST_Response(
            ['message' => __('No data available for this date.', 'today-in-history')],
            502
          );
        }

        $data['items'] = array_slice($data['items'], 0, $limit);
        return new WP_REST_Response($data, 200);
      },
      'permission_callback' => '__return_true',
      'args' => [
        'type'  => ['type'=>'string'],
        'month' => ['type'=>'integer'],
        'day'   => ['type'=>'integer'],
        'limit' => ['type'=>'integer'],
      ]
    ]);
  }
}
