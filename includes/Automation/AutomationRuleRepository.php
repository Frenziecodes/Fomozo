<?php
/**
 * Automation rule storage.
 *
 * @package Noravo
 */

declare(strict_types=1);

namespace Noravo\Automation;

/**
 * Stores automation rules in a single WordPress option.
 */
final class AutomationRuleRepository {
	public const OPTION = 'noravo_automation_rules';

	/** Seeds rule storage on activation. */
	public static function install_defaults(): void {
		if (false === get_option(self::OPTION, false)) {
			add_option(self::OPTION, array(), '', false);
		}
	}

	/** @return array<int, array<string, mixed>> */
	public function all(): array {
		$rules = get_option(self::OPTION, array());

		if (! is_array($rules)) {
			return array();
		}

		return array_values(array_map(array($this, 'sanitize_rule'), $rules));
	}

	/** Saves a new rule and returns it. */
	public function create(string $name, string $trigger, string $action, string $status, string $source): array {
		$rules = $this->all();
		$now   = current_time('mysql');
		$rule  = $this->sanitize_rule(
			array(
				'id'         => uniqid('rule_', true),
				'name'       => $name,
				'trigger'    => $trigger,
				'action'     => $action,
				'status'     => $status,
				'source'     => $source,
				'times_run'  => 0,
				'created_at' => $now,
				'updated_at' => $now,
			)
		);

		$rules[] = $rule;
		update_option(self::OPTION, $rules, false);

		return $rule;
	}

	/** @return array<int, string> */
	public function active_sources(): array {
		$sources = array();

		foreach ($this->all() as $rule) {
			if ('active' === $rule['status'] && '' !== $rule['source']) {
				$sources[] = $rule['source'];
			}
		}

		return array_values(array_unique($sources));
	}

	/** @return array<string, mixed> */
	private function sanitize_rule(array $rule): array {
		$status = sanitize_key((string) ($rule['status'] ?? 'draft'));

		return array(
			'id'         => sanitize_key((string) ($rule['id'] ?? '')),
			'name'       => sanitize_text_field((string) ($rule['name'] ?? 'Untitled rule')),
			'trigger'    => sanitize_key((string) ($rule['trigger'] ?? '')),
			'action'     => sanitize_key((string) ($rule['action'] ?? '')),
			'status'     => in_array($status, array('active', 'draft'), true) ? $status : 'draft',
			'source'     => sanitize_key((string) ($rule['source'] ?? '')),
			'times_run'  => absint($rule['times_run'] ?? 0),
			'created_at' => sanitize_text_field((string) ($rule['created_at'] ?? '')),
			'updated_at' => sanitize_text_field((string) ($rule['updated_at'] ?? '')),
		);
	}
}
