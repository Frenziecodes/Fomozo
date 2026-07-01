<?php
/**
 * Plugin Name: Noravo
 * Plugin URI: https://github.com/Frenziecodes/Noravo
 * Description: Boost conversions with Noravo: Social Proof & FOMO Notifications for WordPress.
 * Version: 1.0.3
 * Requires at least: 6.2
 * Requires PHP: 8.0
 * Author: lewisushindi
 * Author URI: https://github.com/Frenziecodes/
 * Text Domain: noravo
 * Domain Path: /languages
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Noravo
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NORAVO_VERSION', '1.0.3' );
define( 'NORAVO_FILE', __FILE__ );
define( 'NORAVO_PATH', plugin_dir_path( __FILE__ ) );
define( 'NORAVO_URL', plugin_dir_url( __FILE__ ) );
define( 'NORAVO_BASENAME', plugin_basename( __FILE__ ) );

$noravo_autoload = NORAVO_PATH . 'vendor/autoload.php';

if ( is_readable( $noravo_autoload ) ) {
	require_once $noravo_autoload;
} else {
	require_once NORAVO_PATH . 'includes/Autoloader.php';

	\Noravo\Autoloader::register();
}

if ( ! function_exists( 'nor_fs' ) ) {
    // Create a helper function for easy SDK access.
    function nor_fs() {
        global $nor_fs;

        if ( ! isset( $nor_fs ) ) {
            // Include Freemius SDK.
            // SDK is auto-loaded through Composer

            $nor_fs = fs_dynamic_init( array(
                'id'                  => '33277',
                'slug'                => 'noravo',
                'type'                => 'plugin',
                'public_key'          => 'pk_3f0980dd20f78c24eebd269bd7924',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'is_org_compliant'    => true,
                'menu'                => array(
                    'slug'           => 'noravo',
                    'contact'        => false,
                ),
            ) );
        }

        return $nor_fs;
    }

    // Init Freemius.
    nor_fs();
    // Signal that SDK was initiated.
    do_action( 'nor_fs_loaded' );
}

register_activation_hook(
	__FILE__,
	static function (): void {
		\Noravo\Plugin::activate();
	}
);

add_action(
	'plugins_loaded',
	static function (): void {
		\Noravo\Plugin::instance()->boot();
	}
);
