<?php
/**
 * Campaign Monitor ESP Service Controller.
 *
 * @package Newspack
 */

defined( 'ABSPATH' ) || exit;

/**
 * API Controller for Newspack Campaign Monitor ESP service.
 */
class Newspack_Newsletters_Campaign_Monitor_Controller extends Newspack_Newsletters_Service_Provider_Controller {
	/**
	 * Newspack_Newsletters_Campaign_Monitor_Controller constructor.
	 *
	 * @param \Newspack_Newsletters_Campaign_Monitor $campaign_monitor The service provider class.
	 */
	public function __construct( $campaign_monitor ) {
		$this->service_provider = $campaign_monitor;
		add_action( 'init', [ __CLASS__, 'register_meta' ] );
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		parent::__construct( $campaign_monitor );
	}

	/**
	 * Register custom fields.
	 */
	public static function register_meta() {
		\register_meta(
			'post',
			'cm_list_id',
			[
				'object_subtype' => Newspack_Newsletters::NEWSPACK_NEWSLETTERS_CPT,
				'show_in_rest'   => [
					'schema' => [
						'context' => [ 'edit' ],
					],
				],
				'type'           => 'string',
				'single'         => true,
				'auth_callback'  => '__return_true',
			]
		);
		\register_meta(
			'post',
			'cm_segment_id',
			[
				'object_subtype' => Newspack_Newsletters::NEWSPACK_NEWSLETTERS_CPT,
				'show_in_rest'   => [
					'schema' => [
						'context' => [ 'edit' ],
					],
				],
				'type'           => 'string',
				'single'         => true,
				'auth_callback'  => '__return_true',
			]
		);
		\register_meta(
			'post',
			'cm_send_mode',
			[
				'object_subtype' => Newspack_Newsletters::NEWSPACK_NEWSLETTERS_CPT,
				'show_in_rest'   => [
					'schema' => [
						'context' => [ 'edit' ],
					],
				],
				'type'           => 'string',
				'single'         => true,
				'auth_callback'  => '__return_true',
			]
		);
		\register_meta(
			'post',
			'cm_from_name',
			[
				'object_subtype' => Newspack_Newsletters::NEWSPACK_NEWSLETTERS_CPT,
				'show_in_rest'   => [
					'schema' => [
						'context' => [ 'edit' ],
					],
				],
				'type'           => 'string',
				'single'         => true,
				'auth_callback'  => '__return_true',
			]
		);
		\register_meta(
			'post',
			'cm_from_email',
			[
				'object_subtype' => Newspack_Newsletters::NEWSPACK_NEWSLETTERS_CPT,
				'show_in_rest'   => [
					'schema' => [
						'context' => [ 'edit' ],
					],
				],
				'type'           => 'string',
				'single'         => true,
				'auth_callback'  => '__return_true',
			]
		);
		\register_meta(
			'post',
			'cm_preview_text',
			[
				'object_subtype' => Newspack_Newsletters::NEWSPACK_NEWSLETTERS_CPT,
				'show_in_rest'   => [
					'schema' => [
						'context' => [ 'edit' ],
					],
				],
				'type'           => 'string',
				'single'         => true,
				'auth_callback'  => '__return_true',
			]
		);
	}

	/**
	 * Register API endpoints unique to Campaign Monitor.
	 */
	public function register_routes() {

		// Register common ESP routes from \Newspack_Newsletters_Service_Provider_Controller::register_routes.
		parent::register_routes();

		// Note that this service provider uses an additional /retrieve endpoint because we need additional GET routes.
		\register_rest_route(
			$this->service_provider::BASE_NAMESPACE . $this->service_provider->service,
			'(?P<id>[\a-z]+)/retrieve',
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'api_retrieve' ],
				'permission_callback' => [ $this->service_provider, 'api_authoring_permissions_check' ],
				'args'                => [
					'id' => [
						'sanitize_callback' => 'absint',
						'validate_callback' => [ 'Newspack_Newsletters', 'validate_newsletter_id' ],
					],
				],
			]
		);
		\register_rest_route(
			$this->service_provider::BASE_NAMESPACE . $this->service_provider->service,
			'(?P<public_id>[\a-z]+)/content',
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'api_content' ],
				'permission_callback' => '__return_true',
			]
		);
		\register_rest_route(
			$this->service_provider::BASE_NAMESPACE . $this->service_provider->service,
			'(?P<id>[\a-z]+)/test',
			[
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'api_test' ],
				'permission_callback' => [ $this->service_provider, 'api_authoring_permissions_check' ],
				'args'                => [
					'id'         => [
						'sanitize_callback' => 'absint',
						'validate_callback' => [ 'Newspack_Newsletters', 'validate_newsletter_id' ],
					],
					'test_email' => [
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);
	}

	/**
	 * Get campaign data.
	 *
	 * @param WP_REST_Request $request API request object.
	 * @return WP_REST_Response|mixed API response or error.
	 */
	public function api_retrieve( $request ) {
		$response = $this->service_provider->retrieve( $request['id'], true );
		return self::get_api_response( $response );
	}

	/**
	 * Test campaign.
	 *
	 * @param WP_REST_Request $request API request object.
	 * @return WP_REST_Response|mixed API response or error.
	 */
	public function api_test( $request ) {
		$emails = explode( ',', $request['test_email'] );
		foreach ( $emails as &$email ) {
			$email = sanitize_email( trim( $email ) );
		}
		$this->update_user_test_emails( $emails );
		$response = $this->service_provider->test(
			$request['id'],
			$emails
		);
		return self::get_api_response( $response );
	}

	/**
	 * Get raw HTML for a campaign. Required for the Campaign Monitor API.
	 *
	 * @param WP_REST_Request $request API request object.
	 * @return void|WP_Error
	 */
	public function api_content( $request ) {
		$response = $this->service_provider->content(
			$request['public_id']
		);

		if ( is_wp_error( $response ) ) {
			return self::get_api_response( $response );
		}

		header( 'Content-Type: text/html; charset=UTF-8' );

		echo $response; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit();
	}
}
