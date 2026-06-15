<?php
/**
 * Demo notification provider.
 *
 * @package Fomozo
 */

declare(strict_types=1);

namespace Fomozo\Notifications;

/**
 * Supplies sample notifications for previews and onboarding.
 */
final class DemoNotificationProvider implements NotificationProviderInterface {
	public function source(): string {
		return 'demo';
	}

	/** @return array<int, array<string, mixed>> Demo notifications with fresh timestamps. */
	public function notifications(int $limit): array {
		$path = FOMOZO_PATH . 'data/demo-notifications.json';

		if ( ! is_readable( $path ) ) {
			return array();
		}

		$decoded = json_decode( (string) file_get_contents( $path ), true );

		if ( ! is_array( $decoded ) ) {
			return array();
		}

		$now = time();

		foreach ( $decoded as $index => &$notification ) {
			$notification['timestamp'] = $now - ( ( $index + 1 ) * 420 );
			$notification['source']    = 'demo';
		}

		return array_slice( $decoded, 0, $limit );
	}
}
