<?php
/**
 * Displayed notification history storage.
 *
 * @package Noravo
 */

declare(strict_types=1);

namespace Noravo\Notifications;

/**
 * Stores notifications that were actually displayed on the frontend.
 */
final class NotificationHistoryRepository {
	public const OPTION = 'noravo_displayed_notifications';

	/** Seeds history storage on activation. */
	public static function install_defaults(): void {
		if (false === get_option(self::OPTION, false)) {
			add_option(self::OPTION, array(), '', false);
		}
	}

	/** Records a displayed notification. */
	public function record(array $notification): void {
		$item = NotificationSanitizer::sanitize($notification);

		if ('' === $item['message']) {
			return;
		}

		$item['displayed_at'] = time();

		$history = $this->latest(50);
		array_unshift($history, $item);

		update_option(self::OPTION, array_slice($history, 0, 50), false);
	}

	/** @return array<int, array<string, mixed>> */
	public function latest(int $limit = 5): array {
		$history = get_option(self::OPTION, array());

		if (! is_array($history)) {
			return array();
		}

		$history = array_map(
			static function (array $item): array {
				$sanitized = NotificationSanitizer::sanitize($item);
				$sanitized['displayed_at'] = absint($item['displayed_at'] ?? $sanitized['timestamp']);

				return $sanitized;
			},
			$history
		);

		usort(
			$history,
			static fn (array $a, array $b): int => (int) ($b['displayed_at'] ?? 0) <=> (int) ($a['displayed_at'] ?? 0)
		);

		return array_slice($history, 0, max(1, $limit));
	}
}
