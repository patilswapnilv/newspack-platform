<?php
/**
 * Plugin Name: Pantheon
 * Plugin URI: https://pantheon.io/
 * Description: Building on Pantheon's and WordPress's strengths, together.
 * Version: 1.5.3
 * Author: Pantheon
 * Author URI: https://pantheon.io/
 *
 * @package pantheon
 */

define( 'PANTHEON_MU_PLUGIN_VERSION', '1.5.3' );

if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
	require_once 'inc/functions.php';
	require_once 'inc/pantheon-page-cache.php';
	require_once 'inc/site-health.php';
	if ( ! defined( 'DISABLE_PANTHEON_UPDATE_NOTICES' ) || ! DISABLE_PANTHEON_UPDATE_NOTICES ) {
		require_once 'inc/pantheon-updates.php';
	}
	if ( ! defined( 'RETURN_TO_PANTHEON_BUTTON' ) || RETURN_TO_PANTHEON_BUTTON ) {
		require_once 'inc/pantheon-login-form-mods.php';
	}
	if ( 'dev' === $_ENV['PANTHEON_ENVIRONMENT'] && function_exists( 'wp_is_writable' ) ) {
		require_once 'inc/pantheon-plugin-install-notice.php';
	}
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		require_once 'inc/cli.php';
	}
	if ( ! defined( 'FS_METHOD' ) ) {
		/**
		 * When this constant is not set, WordPress writes and then deletes a
		 * temporary file to determine if it has direct access to the filesystem,
		 * which we already know to be the case.  This multiplies filesystem
		 * operations and can degrade performance of the filesystem as a whole in
		 * the case of large sites that do a lot of filesystem operations.
		 * Setting this constant to 'direct' tells WordPress to assume it has
		 * direct access and skip creating the extra temporary file.
		 */
		define( 'FS_METHOD', 'direct' );
	}
	// When developing a WordPress Multisite locally, ensure that this constant is set.
	// This will set the Multisite variable in all Pantheon environments.
	if ( getenv( 'FRAMEWORK' ) === 'wordpress_network' && ! defined( 'WP_ALLOW_MULTISITE' ) ) {
		define( 'WP_ALLOW_MULTISITE', true );
	}
	if ( defined( 'WP_ALLOW_MULTISITE' ) && WP_ALLOW_MULTISITE ) {
		require_once 'inc/pantheon-network-setup.php';
		if ( ! defined( 'MULTISITE' ) && MULTISITE ) {
			require_once 'inc/pantheon-multisite-finalize.php';
		}
	}

	if ( ! defined( 'PANTHEON_COMPATIBILITY' ) || PANTHEON_COMPATIBILITY ) {
		require_once 'inc/compatibility/class-compatibilityfactory.php';
		Pantheon\Compatibility\CompatibilityFactory::get_instance();
	}
} // Ensuring that this is on Pantheon.
