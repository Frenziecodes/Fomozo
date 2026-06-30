<?php
/**
 * Asset registration.
 *
 * @package Noravo
 */

declare(strict_types=1);

namespace Noravo\Assets;

use Noravo\Settings\SettingsRepository;

/**
 * Registers and enqueues frontend and admin assets.
 */
final class AssetManager {
	private SettingsRepository $settings;

	/** @param SettingsRepository $settings Plugin settings store. */
	public function __construct( SettingsRepository $settings ) {
		$this->settings = $settings;
	}

	/** Registers frontend styles, scripts, and localized config. */
	public function register_frontend(): void {
		wp_register_style(
			'noravo-frontend',
			NORAVO_URL . 'assets/css/frontend.css',
			array(),
			NORAVO_VERSION
		);

		wp_register_script(
			'noravo-frontend',
			NORAVO_URL . 'assets/js/frontend.js',
			array(),
			NORAVO_VERSION,
			true
		);

		$settings = $this->settings->all();

		wp_localize_script(
			'noravo-frontend',
			'noravoConfig',
			array(
				'restUrl'      => esc_url_raw(rest_url( 'noravo/v1/notifications' ) ),
				'position'     => $settings['position'],
				'animation'    => $settings['animation'],
				'timeFormat'   => $settings['time_format'],
				'initialDelay' => $settings['initial_delay'],
				'interval'     => $settings['interval'],
				'maxPerPage'   => $settings['max_per_page'],
				'i18n'         => array(
					'justNow'    => __( 'Just now', 'noravo' ),
					'minuteAgo'  => __( '1 minute ago', 'noravo' ),
					/* translators: %d is the number of minutes since the notification event. */
					'minutesAgo' => __( '%d minutes ago', 'noravo' ),
					'hourAgo'    => __( '1 hour ago', 'noravo' ),
					/* translators: %d is the number of hours since the notification event. */
					'hoursAgo'   => __( '%d hours ago', 'noravo' ),
					'dayAgo'     => __( '1 day ago', 'noravo' ),
					/* translators: %d is the number of days since the notification event. */
					'daysAgo'    => __( '%d days ago', 'noravo' ),
					'minute'     => __( '1 minute', 'noravo' ),
					/* translators: %d is the number of minutes since the notification event. */
					'minutes'    => __( '%d minutes', 'noravo' ),
					'hour'       => __( '1 hour', 'noravo' ),
					/* translators: %d is the number of hours since the notification event. */
					'hours'      => __( '%d hours', 'noravo' ),
					'day'        => __( '1 day', 'noravo' ),
					/* translators: %d is the number of days since the notification event. */
					'days'       => __( '%d days', 'noravo' ),
					/* translators: %s is a human-readable duration, such as "1 day 5 hours". */
					'ago'        => __( '%s ago', 'noravo' ),
				),
			)
		);
	}

	/** Enqueues frontend assets on public pages. */
	public function enqueue_frontend(): void {
		$this->register_frontend();
		wp_enqueue_style( 'noravo-frontend' );
		wp_enqueue_script( 'noravo-frontend' );
	}

	/**
	 * Enqueues admin assets on the Noravo settings screen.
	 *
	 * @param string $hook Current admin page hook suffix.
	 */
	public function enqueue_admin(string $hook): void {
		if ( 'toplevel_page_noravo' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'noravo-admin',
			NORAVO_URL . 'assets/css/admin.css',
			array(),
			NORAVO_VERSION
		);
	}
}
