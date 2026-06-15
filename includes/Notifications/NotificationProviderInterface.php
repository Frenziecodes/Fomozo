<?php
/**
 * Notification provider contract.
 *
 * @package Fomozo
 */

declare( strict_types=1 );

namespace Fomozo\Notifications;

interface NotificationProviderInterface {
	/** Source key used in settings and API responses. */
	public function source(): string;

	/**
	 * Returns notification payloads for this source.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function notifications( int $limit ): array;
}
