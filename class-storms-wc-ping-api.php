<?php
/**
 * Storms Framework (http://storms.com.br/)
 *
 * @author    Vinicius Garcia | vinicius.garcia@storms.com.br
 * @copyright (c) Copyright 2012-2020, Storms Websolutions
 * @license   GPLv2 - GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package   Storms
 * @version   3.0.0
 *
 * Storms_WC_Ping_API class
 * Ping endpoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_REST_Controller' ) ) {

	class Storms_WC_Ping_API extends WC_REST_Controller
	{

		/**
		 * Endpoint namespace.
		 *
		 * @var string
		 */
		protected $namespace = 'wc-storms/v1';

		/**
		 * Route base.
		 *
		 * @var string
		 */
		protected $rest_base = 'ping';

		/**
		 * Register the routes for customers.
		 */
		public function register_routes() {

			register_rest_route( $this->namespace, '/' . $this->rest_base, array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_ping' ),
					'permission_callback' => array( $this, 'get_ping_permissions_check' ),
					'args' => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			) );
		}

		/**
		 * Ping the server
		 *
		 * @param WP_REST_Request $request
		 * @return mixed|WP_REST_Response
		 * @throws Exception
		 */
		public function get_ping( $request ) {

			$date = new DateTime();
			$date->setTimezone( new DateTimeZone( 'America/Sao_Paulo' ) );

			$ping = array(
				'server_status' => true,
				'description' => __( 'The server is up and running', 'storms' ),
				'status_date' => $date->format( 'Y-m-d H:i:s' ),
			);

			$ping = $this->prepare_item_for_response( $ping, $request );
			$response = rest_ensure_response( $ping );

			return $response;
		}

		/**
		 * Check whether a given request has permission to ping the server
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 * @return WP_Error|boolean
		 */
		public function get_ping_permissions_check( $request ) {

			if ( ! current_user_can( 'read' ) ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'No permission to read this endpoint.', 'storms' ), array( 'status' => rest_authorization_required_code() ) );
			}
			return true;
		}

		/**
		 * Prepare a server status request output for response.
		 *
		 * @param array $ping Ping object.
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response $response Response data.
		 */
		public function prepare_item_for_response( $ping, $request ) {

			$data = array(
				'server_status' => $ping['server_status'],
				'description' => $ping['description'],
				'status_date' => wc_rest_prepare_date_response( $ping['status_date'] ),
			);

			$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
			$data = $this->add_additional_fields_to_object( $data, $request );
			$data = $this->filter_response_by_context( $data, $context );

			// Wrap the data in a response object.
			$response = rest_ensure_response( $data );

			/**
			 * Filter customer data returned from the REST API.
			 *
			 * @param WP_REST_Response $response The response object.
			 * @param string $ping Ping object.
			 * @param WP_REST_Request $request Request object.
			 */
			return apply_filters( 'storms_wc_ping_api_prepare_ping_response', $response, $ping, $request );
		}

		/**
		 * Get the Ping's schema, conforming to JSON Schema.
		 * @return array
		 */
		public function get_item_schema() {
			$schema = [
				'server_status' => array(
					'description' => __( 'Server satatus.', 'storms' ),
					'type' => 'boolean',
					'context' => array('view', 'edit'),
					'readonly' => true,
				),
				'description' => array(
					'description' => __( 'Status description.', 'storms' ),
					'type' => 'string',
					'context' => array('view', 'edit'),
					'readonly' => true,
				),
				'status_date' => array(
					'description' => __( 'Request date.', 'storms' ),
					'type' => 'date-time',
					'context' => array( 'view', 'edit' ),
					'readonly' => true,
				),
			];

			return $this->add_additional_fields_schema( $schema );
		}

		/**
		 * Get the query params for collections.
		 * @return array
		 */
		public function get_collection_params() {
			return array(
				'context' => array(
					'default' => 'view'
				)
			);
		}
	}

}
