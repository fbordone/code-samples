<?php
/**
 * LibCal API integration.
 *
 * @package CplPlugin
 */

namespace CplPlugin\LibCal;

use DateTime;

/**
 * LibCal API class
 *
 * @package CplPlugin
 */
final class LibCal {

	/**
	 * LibCal API url for version 1.1.
	 *
	 * @var string
	 */
	const LIBCAL_API_URL = 'https://cpl.libcal.com/api/1.1';

	/**
	 * Token endpoint for fetching access tokens.
	 *
	 * @var string
	 */
	const LIBCAL_TOKEN_ENDPOINT = '/oauth/token';

	/**
	 * Cache group for storing tokens.
	 *
	 * @var string
	 */
	const LIBCAL_ACCESS_TOKEN_CACHE_GROUP = 'cpl_libcal_access_token';

	/**
	 * Retrieve the client ID.
	 *
	 * @return string LibCal Client ID
	 */
	protected function get_client_id(): string {
		return defined( 'LIBCAL_CLIENT_ID' ) ? LIBCAL_CLIENT_ID : '';
	}

	/**
	 * Retrieve the client secret.
	 *
	 * @return string LibCal Client Secret
	 */
	protected function get_client_secret(): string {
		return defined( 'LIBCAL_CLIENT_SECRET' ) ? LIBCAL_CLIENT_SECRET : '';
	}

	/**
	 * Get access token from cache or fetch a new one.
	 *
	 * @return string|\WP_Error Access token or WP_Error on failure.
	 */
	public function get_access_token(): string|\WP_Error {
		$access_token = get_transient( self::LIBCAL_ACCESS_TOKEN_CACHE_GROUP );
		if ( ! empty( $access_token ) ) {
			return $access_token;
		}

		return $this->fetch_access_token();
	}

	/**
	 * Fetch a new access token from the API and cache it.
	 *
	 * @return string|WP_Error Access token or WP_Error on failure.
	 */
	protected function fetch_access_token(): string|\WP_Error {
		$body = http_build_query(
			[
				'grant_type'    => 'client_credentials',
				'client_id'     => $this->get_client_id(),
				'client_secret' => $this->get_client_secret(),
			]
		);

		$response = wp_remote_post(
			self::LIBCAL_API_URL . self::LIBCAL_TOKEN_ENDPOINT,
			[
				'body'    => $body,
				'headers' => [
					'Content-Type' => 'application/x-www-form-urlencoded',
				],
			]
		);

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'libcal_token_error', __( 'Failed to retrieve LibCal access token.', 'cpl-plugin' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data['access_token'] ) ) {
			return new \WP_Error( 'libcal_token_error', __( 'No access token found in response.', 'cpl-plugin' ) );
		}

		// Cache the token for 1 hour (3600 seconds).
		set_transient( self::LIBCAL_ACCESS_TOKEN_CACHE_GROUP, $data['access_token'], 3600 );
		return $data['access_token'];
	}

	/**
	 * Make an authenticated API request to LibCal.
	 *
	 * @param string $endpoint The API endpoint.
	 * @param array  $args Optional. Arguments for the request.
	 *
	 * @return array|WP_Error Response data or WP_Error on failure.
	 */
	public function api_request( $endpoint, $args = [] ): array|\WP_Error {
		$access_token = $this->get_access_token();
		if ( is_wp_error( $access_token ) ) {
			return $access_token;
		}

		$headers = [
			'Authorization' => 'Bearer ' . $access_token,
			'Content-Type'  => 'application/json',
		];

		$url = self::LIBCAL_API_URL . $endpoint;
		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		$response = \wp_remote_get(
			$url,
			[
				'headers' => $headers,
			]
		);

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'libcal_request_error', __( 'LibCal API request failed.', 'cpl-plugin' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return $data;
	}

	/**
	 * Get upcoming events from LibCal.
	 *
	 * For demo purposes, if the API request fails or returns empty data,
	 * we return a stubbed event.
	 *
	 * @param array $args Arguments for the request.
	 *
	 * @return array Event data.
	 */
	public function get_events( $args = [] ): array {
		$query_params = [
			'cal_id'   => 8758,  // Public calendar ID
			'days'     => intval( $args['eventDaysAhead'] ),
			'limit'    => intval( $args['numberOfEvents'] ),
			'campus'   => implode( ',', array_map( 'intval', $args['eventLocations'] ) ),
			'category' => implode( ',', array_map( 'intval', $args['eventCategories'] ) ),
			'tag'      => implode( ',', array_map( 'intval', $args['eventTags'] ) ),
		];

		$response = $this->api_request( '/events', $query_params );
		if ( is_wp_error( $response ) || empty( $response['events'] ) || ! is_array( $response['events'] ) ) {
			return [];
		}

		$events = [];
		foreach ( $response['events'] as $event ) {
			$title          = ! empty( $event['title'] ) ? $event['title'] : '';
			$start_time     = ! empty( $event['start'] ) ? $event['start'] : '';
			$end_time       = ! empty( $event['end'] ) ? $event['end'] : '';
			$description    = ! empty( $event['description'] ) ? wp_strip_all_tags( $event['description'] ) : '';
			$link           = ! empty( $event['url']['public'] ) ? esc_url( $event['url']['public'] ) : '';
			$location       = ( ! empty( $event['campus'] ) && is_array( $event['campus'] ) && ! empty( $event['campus']['name'] ) ) ? $event['campus']['name'] : '';
			$featured_image = ! empty( $event['featured_image'] ) ? esc_url( $event['featured_image'] ) : '';

			if ( ! empty( $start_time ) && ! empty( $end_time ) ) {
				$start_datetime       = new DateTime( $start_time );
				$end_datetime         = new DateTime( $end_time );
				$formatted_date       = $this->format_event_date( $start_datetime );
				$formatted_start_time = $this->format_event_time( $start_datetime );
				$formatted_end_time   = $this->format_event_time( $end_datetime );
			} else {
				$formatted_date       = '';
				$formatted_start_time = '';
				$formatted_end_time   = '';
			}

			$events[] = [
				'title'       => $title,
				'date'        => $formatted_date,
				'start_time'  => $formatted_start_time,
				'end_time'    => $formatted_end_time,
				'description' => $description,
				'link'        => $link,
				'location'    => $location,
				'image'       => $featured_image,
			];
		}

		return $events;
	}

	/**
	 * Format the event date.
	 *
	 * @param DateTime $date The event start date.
	 *
	 * @return string Formatted date (e.g., "Mar 7").
	 */
	protected function format_event_date( DateTime $date ): string {
		return $date->format( 'M j' );
	}

	/**
	 * Format the event time.
	 *
	 * @param DateTime $date The event time (start or end).
	 *
	 * @return string Formatted time (e.g., "4:30 PM").
	 */
	protected function format_event_time( DateTime $date ): string {
		return $date->format( 'g:i A' );
	}

	// Additional helper methods (e.g., mapping audience, background color) have been omitted for brevity.
}
