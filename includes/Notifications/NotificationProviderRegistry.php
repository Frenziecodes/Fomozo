<?php
/**
 * Notification provider registry.
 *
 * @package Fomozo
 */

declare( strict_types=1 );

namespace Fomozo\Notifications;

/**
 * Collects notifications from registered providers.
 */
final class NotificationProviderRegistry {
	/** @var array<string, NotificationProviderInterface> */
	private array $providers = array();

	/** Adds a notification provider to the registry. */
	public function register(NotificationProviderInterface $provider): void {
		$this->providers[$provider->source()] = $provider;
	}

	/**
	 * Merges, sorts, sanitizes, and limits notifications from enabled sources.
	 *
	 * @param array<int, string> $enabled_sources Enabled notification source keys.
	 * @return array<int, array<string, mixed>>
	 */
	public function collect(array $enabled_sources, int $limit): array {
		$notifications = array();

		foreach ($enabled_sources as $source) {
			if (! isset($this->providers[$source])) {
				continue;
			}

			$notifications = array_merge($notifications, $this->providers[$source]->notifications($limit));
		}

		usort(
			$notifications,
			static fn (array $a, array $b): int => (int) ($b['timestamp'] ?? 0) <=> (int) ($a['timestamp'] ?? 0)
		);

		return array_slice(array_map(array(NotificationSanitizer::class, 'sanitize'), $notifications), 0, $limit);
	}
}
