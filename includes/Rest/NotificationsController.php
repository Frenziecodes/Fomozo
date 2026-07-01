<?php
/**
 * Notifications REST endpoint.
 *
 * @package Noravo
 */

declare(strict_types=1);

namespace Noravo\Rest;

use Noravo\Automation\AutomationRuleRepository;
use Noravo\Notifications\NotificationProviderRegistry;
use Noravo\Settings\SettingsRepository;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Exposes notification data via the REST API.
 */
final class NotificationsController {
	private SettingsRepository $settings;

	private NotificationProviderRegistry $providers;

	private AutomationRuleRepository $automation_rules;

	/**
	 * @param SettingsRepository           $settings  Plugin settings store.
	 * @param NotificationProviderRegistry $providers Notification source registry.
	 */
	public function __construct(SettingsRepository $settings, NotificationProviderRegistry $providers, AutomationRuleRepository $automation_rules) {
		$this->settings         = $settings;
		$this->providers        = $providers;
		$this->automation_rules = $automation_rules;
	}

	/** Registers REST route hooks. */
	public function register(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/** Registers the notifications collection route. */
	public function register_routes(): void {
		register_rest_route(
			'noravo/v1',
			'/notifications',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'index' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'limit' => array(
						'type'              => 'integer',
						'default'           => $this->settings->all()['max_per_page'],
						'sanitize_callback' => 'absint',
					),
				),
			)
		);
	}

	/**
	 * Returns notifications from enabled sources.
	 *
	 * @param WP_REST_Request $request Incoming REST request.
	 * @return WP_REST_Response
	 */
	public function index(WP_REST_Request $request): WP_REST_Response {
		if ( ! $this->settings->is_enabled() ) {
			return new WP_REST_Response( array( 'notifications' => array() ), 200 );
		}

		$settings = $this->settings->all();
		$sources  = $settings['enabled_sources'];
		$sources  = array_values(array_unique(array_merge($sources, $this->automation_rules->active_sources())));

		if ( ! $settings['demo_mode']) {
			$sources = array_values(array_diff( $sources, array( 'demo' ) ) );
		}

		$limit = max( 1, min( (int) $request->get_param( 'limit' ), (int) $settings['max_per_page'] ) );

		return new WP_REST_Response(
			array(
				'notifications' => $this->providers->collect( $sources, $limit ),
			),
			200
		);
	}
}
