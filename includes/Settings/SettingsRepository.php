<?php
/**
 * Options-backed settings repository.
 *
 * @package Fomozo
 */

declare( strict_types=1 );

namespace Fomozo\Settings;

/**
 * Reads and writes plugin settings stored in the options table.
 */
final class SettingsRepository {
	public const OPTION = 'fomozo_settings';

	/** @return array<string, mixed> Default settings values. */
	public static function defaults(): array {
		return array(
			'enabled'         => true,
			'demo_mode'       => true,
			'position'        => 'bottom-left',
			'animation'       => 'slide',
			'initial_delay'   => 2500,
			'interval'        => 9000,
			'max_per_page'    => 5,
			'enabled_sources' => array( 'demo' ),
		);
	}

	/** Seeds default settings on first install. */
	public static function install_defaults(): void {
		if ( false === get_option( self::OPTION, false ) ) {
			add_option( self::OPTION, self::defaults(), '', false );
		}
	}

	/** @return array<string, mixed> Merged and sanitized settings. */
	public function all(): array {
		$settings = get_option( self::OPTION, array() );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		return $this->sanitize( array_merge( self::defaults(), $settings ) );
	}

	/**
	 * Persists partial setting updates.
	 *
	 * @param array<string, mixed> $settings Settings to merge and save.
	 */
	public function update( array $settings ): void {
		update_option( self::OPTION, $this->sanitize( array_merge( $this->all(), $settings ) ), false );
	}

	/** Whether frontend notifications are enabled. */
	public function is_enabled(): bool {
		return (bool) $this->all()['enabled'];
	}

	/** Whether a notification source is enabled in settings. */
	public function is_source_enabled(string $source): bool {
		$settings = $this->all();

		return in_array( $source, $settings['enabled_sources'], true );
	}

	/**
	 * Normalizes and validates raw settings input.
	 *
	 * @param array<string, mixed> $settings Raw settings values.
	 * @return array<string, mixed>
	 */
	public function sanitize(array $settings): array {
		$defaults  = self::defaults();
		$positions = array( 'bottom-left', 'bottom-right', 'top-left', 'top-right' );
		$animations = array( 'slide', 'fade' );

		$sources = isset( $settings['enabled_sources'] ) && is_array( $settings['enabled_sources'] )
			? array_map( 'sanitize_key', $settings['enabled_sources'])
			: $defaults['enabled_sources'];

		return array(
			'enabled'         => ! empty( $settings['enabled']),
			'demo_mode'       => ! empty( $settings['demo_mode']),
			'position'        => in_array( $settings['position'] ?? '', $positions, true) ? $settings['position'] : $defaults['position'],
			'animation'       => in_array( $settings['animation'] ?? '', $animations, true) ? $settings['animation'] : $defaults['animation'],
			'initial_delay'   => max(0, min(60000, absint( $settings['initial_delay'] ?? $defaults['initial_delay']))),
			'interval'        => max(3000, min(120000, absint( $settings['interval'] ?? $defaults['interval']))),
			'max_per_page'    => max(1, min(20, absint( $settings['max_per_page'] ?? $defaults['max_per_page']))),
			'enabled_sources' => array_values(array_unique( $sources)),
		);
	}
}
