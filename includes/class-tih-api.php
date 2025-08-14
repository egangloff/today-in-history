<?php
if ( ! defined('ABSPATH') ) exit;

class TIH_API {
  private static function fetch_source( $type, $m, $d ) {
    $type = in_array( $type, array( 'events', 'births', 'deaths' ), true ) ? $type : 'events';
    $m    = max( 1, min( 12, (int) $m ) );
    $d    = max( 1, min( 31, (int) $d ) );

    $url = sprintf(
      'https://byabbe.se/on-this-day/%1$d/%2$d/%3$s.json',
      $m,
      $d,
      $type
    );

    $args = array(
      'timeout' => 12,
      'headers' => array(
        'Accept'     => 'application/json',
        'User-Agent' => 'TodayInHistory/1.1.0; ' . home_url( '/' ),
      ),
    );

    $res = wp_remote_get( $url, $args );
    if ( is_wp_error( $res ) ) {
      return null;
    }

    if ( (int) wp_remote_retrieve_response_code( $res ) !== 200 ) {
      return null;
    }

    $body = json_decode( wp_remote_retrieve_body( $res ), true );
    return is_array( $body ) ? $body : null;
  }


  public static function get_data( $type = 'events', $month = null, $day = null, $ttl = null ) {
    $type = in_array( $type, array( 'events', 'births', 'deaths' ), true ) ? $type : 'events';

    $now = current_time( 'timestamp' );

    $m = $month ? (int) $month : (int) wp_date( 'n', $now );
    $d = $day   ? (int) $day   : (int) wp_date( 'j', $now );

    $m = max( 1, min( 12, $m ) );
    $d = max( 1, min( 31, $d ) );

    $cache_key = sprintf( 'tih_%s_%02d_%02d', $type, $m, $d );

    if ( $cached = get_transient( $cache_key ) ) {
      return $cached;
    }

    $data = self::fetch_source( $type, $m, $d );
    if ( ! $data ) {
      return null;
    }

    $items = ( isset( $data[ $type ] ) && is_array( $data[ $type ] ) ) ? $data[ $type ] : array();

    $payload = array(
      'date'  => isset( $data['date'] ) ? (string) $data['date'] : sprintf( '%02d/%02d', $m, $d ),
      'type'  => $type,
      'items' => array_map(
        static function( $it ) {
          $wiki  = '';
          $title = '';

          if ( ! empty( $it['wikipedia'][0]['wikipedia'] ) && is_string( $it['wikipedia'][0]['wikipedia'] ) ) {
            $maybe = $it['wikipedia'][0]['wikipedia'];
            if ( 0 === strpos( $maybe, 'http://' ) || 0 === strpos( $maybe, 'https://' ) ) {
              $wiki = esc_url_raw( $maybe );
            }
          }
          if ( ! empty( $it['wikipedia'][0]['title'] ) && is_string( $it['wikipedia'][0]['title'] ) ) {
            $title = $it['wikipedia'][0]['title'];
          }

          return array(
            'year'        => isset( $it['year'] ) ? (string) $it['year'] : '',
            'description' => isset( $it['description'] ) ? (string) $it['description'] : '',
            'wiki'        => $wiki,
            'title'       => $title,
          );
        },
        $items
      ),
    );

    $opt_ttl   = (int) get_option( 'tih_cache_ttl', 43200 ); // 12h par défaut
    $ttl_final = is_null( $ttl ) ? max( 300, $opt_ttl ) : max( 300, (int) $ttl );

    set_transient( $cache_key, $payload, $ttl_final );
    return $payload;
  }

  /**
   * REST route /tih/v1/day
   */
  public static function register_routes() {
    register_rest_route(
      'tih/v1',
      '/day',
      array(
        'methods'             => 'GET',
        'callback'            => function( $req ) {
          $type  = sanitize_key( $req->get_param( 'type' ) ?: 'events' );
          $month = (int) ( $req->get_param( 'month' ) ?: 0 );
          $day   = (int) ( $req->get_param( 'day' ) ?: 0 );
          $limit = max( 1, min( 50, (int) ( $req->get_param( 'limit' ) ?: 10 ) ) );

          $data = self::get_data( $type, $month ?: null, $day ?: null );
          if ( ! $data ) {
            return new WP_REST_Response(
              array( 'message' => __( 'No data available for this date.', 'today-x-history' ) ),
              502
            );
          }

          $data['items'] = array_slice( $data['items'], 0, $limit );
          return new WP_REST_Response( $data, 200 );
        },
        'permission_callback' => '__return_true',
        'args'                => array(
          'type'  => array( 'type' => 'string' ),
          'month' => array( 'type' => 'integer' ),
          'day'   => array( 'type' => 'integer' ),
          'limit' => array( 'type' => 'integer' ),
        ),
      )
    );
  }
}