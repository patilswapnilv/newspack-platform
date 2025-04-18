<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Handle Publicize connection information for each post.
 *
 * @package automattic/jetpack
 */

use Automattic\Jetpack\Publicize\REST_API\Connections_Controller;

/**
 * Add per-post Publicize Connection data.
 *
 * { # Post Object
 *   ...
 *   jetpack_publicize_connections: { # Defined below in this file. See schema for more detail.
 *     id:              (string)  Connection unique_id
 *     service_name:    (string)  Service slug
 *     display_name:    (string)  User name/display name of user/connection on Service
 *     profile_picture: (string)  Profile picture of user/connection on Service
 *     enabled:         (boolean) Is this connection slated to be shared to? context=edit only
 *     done:            (boolean) Is this post (or connection) done sharing? context=edit only
 *     toggleable:      (boolean) Can the current user change the `enabled` setting for this Connection+Post? context=edit only
 *   }
 *   ...
 *   meta: { # Not defined in this file. Handled in modules/publicize/publicize.php via `register_meta()`
 *     jetpack_publicize_feature_enabled: (boolean) Is this publicize feature enabled?
 *     jetpack_publicize_message: (string) The message to use instead of the post's title when sharing.
 *     jetpack_social_options: {
 *       attached_media: (array) List of media that will be attached to the social media post.
 *       image_generator_settings: (array) List of settings related to the generated image.
 *     }
 *   ...
 * }
 *
 * @since 6.8.0
 */
class WPCOM_REST_API_V2_Post_Publicize_Connections_Field extends WPCOM_REST_API_V2_Field_Controller {
	/**
	 * Array of post types that can handle Publicize.
	 *
	 * @var array
	 */
	protected $object_type = array( 'post' );

	/**
	 * Field name
	 *
	 * @var string
	 */
	protected $field_name = 'jetpack_publicize_connections';

	/**
	 * Array of post IDs that have been updated.
	 *
	 * @var array
	 */
	private $meta_saved = array();

	/**
	 * Used to memoize the updates for a given post.
	 *
	 * @var array
	 */
	public $memoized_updates = array();

	/**
	 * Registers the jetpack_publicize_connections field. Called
	 * automatically on `rest_api_init()`.
	 */
	public function register_fields() {
		$this->object_type = get_post_types_by_support( 'publicize' );
		foreach ( $this->object_type as $post_type ) {
			if ( $this->is_registered( $post_type ) ) {
				continue;
			}
			// Adds meta support for those post types that don't already have it.
			// Only runs during REST API requests, so it doesn't impact UI.
			if ( ! post_type_supports( $post_type, 'custom-fields' ) ) {
				add_post_type_support( $post_type, 'custom-fields' );
			}

			add_filter( 'rest_pre_insert_' . $post_type, array( $this, 'rest_pre_insert' ), 10, 2 );
			add_action( 'rest_insert_' . $post_type, array( $this, 'rest_insert' ), 10, 3 );
		}

		parent::register_fields();
	}

	/**
	 * Defines data structure and what elements are visible in which contexts
	 */
	public function get_schema() {
		return array(
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title'   => 'jetpack-publicize-post-connections',
			'type'    => 'array',
			'context' => array( 'view', 'edit' ),
			'items'   => $this->post_connection_schema(),
			'default' => array(),
		);
	}

	/**
	 * Schema for the endpoint.
	 */
	private function post_connection_schema() {
		$connection_fields = Connections_Controller::get_the_item_schema();
		$deprecated_fields = array(
			'id'       => array(
				'type'        => 'string',
				'description' => __( 'Unique identifier for the Jetpack Social connection.', 'jetpack' ) . ' ' . sprintf(
					/* translators: %s is the new field name */
					__( 'Deprecated in favor of %s.', 'jetpack' ),
					'connection_id'
				),
			),
			'username' => array(
				'type'        => 'string',
				'description' => __( 'Username of the connected account.', 'jetpack' ) . ' ' . sprintf(
					/* translators: %s is the new field name */
					__( 'Deprecated in favor of %s.', 'jetpack' ),
					'external_handle'
				),
			),
		);

		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'jetpack-publicize-post-connection',
			'type'       => 'object',
			'properties' => array_merge(
				$deprecated_fields,
				$connection_fields,
				array(
					'enabled' => array(
						'description' => __( 'Whether to share to this connection.', 'jetpack' ),
						'type'        => 'boolean',
						'context'     => array( 'edit' ),
					),
				)
			),
		);
	}

	/**
	 * Permission check, based on module availability and user capabilities.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return true|WP_Error
	 */
	public function permission_check( $post_id ) {
		global $publicize;

		if ( ! $publicize ) {
			return new WP_Error(
				'publicize_not_available',
				__( 'Sorry, Jetpack Social is not available on your site right now.', 'jetpack' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( $publicize->current_user_can_access_publicize_data( $post_id ) ) {
			return true;
		}

		return new WP_Error(
			'invalid_user_permission_publicize',
			__( 'Sorry, you are not allowed to access Jetpack Social data for this post.', 'jetpack' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Getter permission check
	 *
	 * @param mixed           $post_array Response from the post endpoint.
	 * @param WP_REST_Request $request    API request.
	 *
	 * @return true|WP_Error
	 */
	public function get_permission_check( $post_array, $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return $this->permission_check( isset( $post_array['id'] ) ? $post_array['id'] : 0 );
	}

	/**
	 * Setter permission check.
	 *
	 * @param mixed           $value   The new value for the field.
	 * @param WP_Post         $post    The post object.
	 * @param WP_REST_Request $request API request.
	 *
	 * @return true|WP_Error
	 */
	public function update_permission_check( $value, $post, $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return $this->permission_check( isset( $post->ID ) ? $post->ID : 0 );
	}

	/**
	 * Getter: Retrieve current list of connected social accounts for a given post.
	 *
	 * @see Publicize::get_filtered_connection_data()
	 *
	 * @param array           $post_array Response from Post Endpoint.
	 * @param WP_REST_Request $request    API request.
	 *
	 * @return array List of connections
	 */
	public function get( $post_array, $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		global $publicize;

		if ( ! $publicize ) {
			return array();
		}

		$schema     = $this->post_connection_schema();
		$properties = array_keys( $schema['properties'] );

		$connections = $publicize->get_filtered_connection_data( $post_array['id'] );

		$output_connections = array();
		foreach ( $connections as $connection ) {
			$output_connection = array();
			foreach ( $properties as $property ) {
				if ( isset( $connection[ $property ] ) ) {
					$output_connection[ $property ] = $connection[ $property ];
				}
			}

			$output_connections[] = $output_connection;
		}

		return $output_connections;
	}

	/**
	 * Prior to updating the post, first calculate which Services to
	 * Publicize to and which to skip.
	 *
	 * @param object          $post    Post data to insert/update.
	 * @param WP_REST_Request $request API request.
	 *
	 * @return object|WP_Error Filtered $post
	 */
	public function rest_pre_insert( $post, $request ) {
		if ( ! isset( $request['jetpack_publicize_connections'] ) ) {
			return $post;
		}

		$permission_check = $this->update_permission_check( $request['jetpack_publicize_connections'], $post, $request );
		if ( is_wp_error( $permission_check ) ) {
			return $permission_check;
		}
		// memoize.
		$this->get_meta_to_update( $request['jetpack_publicize_connections'], isset( $post->ID ) ? $post->ID : 0 );

		if ( isset( $post->ID ) ) {
			// Set the meta before we mark the post as published so that publicize works as expected.
			// If this is not the case post end up on social media when they are marked as skipped.
			$this->update( $request['jetpack_publicize_connections'], $post, $request );
		}

		return $post;
	}

	/**
	 * After creating a new post, update our cached data to reflect
	 * the new post ID.
	 *
	 * @param WP_Post         $post    Post data to update.
	 * @param WP_REST_Request $request API request.
	 * @param bool            $is_new  Is this a new post.
	 */
	public function rest_insert( $post, $request, $is_new ) {
		if ( ! $is_new ) {
			// An existing post was edited - no need to update
			// our cache - we started out knowing the correct
			// post ID.
			return;
		}

		if ( ! isset( $request['jetpack_publicize_connections'] ) ) {
			return;
		}

		if ( ! isset( $this->memoized_updates[0] ) ) {
			return;
		}

		$this->memoized_updates[ $post->ID ] = $this->memoized_updates[0];
		unset( $this->memoized_updates[0] );
	}

	/**
	 * Get list of meta data to update per post ID.
	 *
	 * @param array $requested_connections Publicize connections to update.
	 *              Items are either `{ id: (string) }` or `{ service_name: (string) }`.
	 * @param int   $post_id    Post ID.
	 */
	protected function get_meta_to_update( $requested_connections, $post_id = 0 ) {
		global $publicize;

		if ( ! $publicize || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return array();
		}

		$post = get_post( $post_id );
		if ( isset( $post->post_status ) && 'publish' === $post->post_status ) {
			return array();
		}

		if ( isset( $this->memoized_updates[ $post_id ] ) ) {
			return $this->memoized_updates[ $post_id ];
		}

		$available_connections = $publicize->get_filtered_connection_data( $post_id );

		$changed_connections = array();

		// Build lookup mappings.
		$available_connections_by_connection_id = array();
		$available_connections_by_service_name  = array();
		foreach ( $available_connections as $available_connection ) {
			$available_connections_by_connection_id[ $available_connection['connection_id'] ] = $available_connection;

			if ( ! isset( $available_connections_by_service_name[ $available_connection['service_name'] ] ) ) {
				$available_connections_by_service_name[ $available_connection['service_name'] ] = array();
			}
			$available_connections_by_service_name[ $available_connection['service_name'] ][] = $available_connection;
		}

		// Handle { service_name: $service_name, enabled: (bool) }.
		// If the service is not available, it will be skipped.
		foreach ( $requested_connections as $requested_connection ) {
			if ( ! isset( $requested_connection['service_name'] ) ) {
				continue;
			}

			if ( ! isset( $available_connections_by_service_name[ $requested_connection['service_name'] ] ) ) {
				continue;
			}

			foreach ( $available_connections_by_service_name[ $requested_connection['service_name'] ] as $available_connection ) {
				if ( $requested_connection['connection_id'] === $available_connection['connection_id'] ) {
					$changed_connections[ $available_connection['connection_id'] ] = $requested_connection['enabled'];
					break;
				}
			}
		}

		// Handle { id: $id, enabled: (bool) }
		// These override the service_name settings.
		foreach ( $requested_connections as $requested_connection ) {
			if ( ! isset( $requested_connection['connection_id'] ) ) {
				continue;
			}

			if ( ! isset( $available_connections_by_connection_id[ $requested_connection['connection_id'] ] ) ) {
				continue;
			}

			$changed_connections[ $requested_connection['connection_id'] ] = $requested_connection['enabled'];
		}

		// Set all changed connections to their new value.
		foreach ( $changed_connections as $id => $enabled ) {
			$connection = $available_connections_by_connection_id[ $id ];

			if ( $connection['done'] ) {
				continue;
			}

			$available_connections_by_connection_id[ $id ]['enabled'] = $enabled;
		}

		$meta_to_update = array();
		// For all connections, ensure correct post_meta.
		foreach ( $available_connections_by_connection_id as $connection_id => $available_connection ) {
			if ( $available_connection['enabled'] ) {
				$meta_to_update[ $publicize->POST_SKIP_PUBLICIZE . $connection_id ] = null; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			} else {
				$meta_to_update[ $publicize->POST_SKIP_PUBLICIZE . $connection_id ] = 1; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}
		}

		$this->memoized_updates[ $post_id ] = $meta_to_update;

		return $meta_to_update;
	}

	/**
	 * Update the connections slated to be shared to.
	 *
	 * @param array           $requested_connections Publicize conenctions to update.
	 *              Items are either `{ id: (string) }` or `{ service_name: (string) }`.
	 * @param WP_Post         $post    Post data.
	 * @param WP_REST_Request $request API request.
	 */
	public function update( $requested_connections, $post, $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if ( isset( $this->meta_saved[ $post->ID ] ) ) { // Make sure we only save it once - per request.
			return;
		}
		foreach ( $this->get_meta_to_update( $requested_connections, $post->ID ) as $meta_key => $meta_value ) {
			if ( $meta_value === null ) {
				delete_post_meta( $post->ID, $meta_key );
			} else {
				update_post_meta( $post->ID, $meta_key, $meta_value );
			}
		}
		$this->meta_saved[ $post->ID ] = true;
	}
}

if ( Jetpack::is_module_active( 'publicize' ) ) {
	wpcom_rest_api_v2_load_plugin( 'WPCOM_REST_API_V2_Post_Publicize_Connections_Field' );
}
