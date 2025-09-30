<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Sharing\Model;

use OCA\Sharing\Registry;
use OCA\Sharing\ResponseDefinitions;
use OCP\Server;
use RuntimeException;

/**
 * @psalm-import-type SharingShareRecipient from ResponseDefinitions
 */
readonly class ShareRecipient {
	public function __construct(
		/** @var class-string<IShareRecipientType> $type */
		public string $type,
		/** @var non-empty-string $value */
		public string $value,
		/** @var ?non-empty-string $displayName */
		public ?string $displayName,
	) {
		if (!isset(Server::get(Registry::class)->getRecipientTypes()[$type])) {
			throw new RuntimeException('The recipient type is not registered: ' . $type);
		}

		/** @psalm-suppress DocblockTypeContradiction */
		if ($value === '') {
			throw new RuntimeException('The value is empty.');
		}

		/** @psalm-suppress DocblockTypeContradiction */
		if ($displayName === '') {
			throw new RuntimeException('The displayName is empty.');
		}
	}

	/**
	 * @param SharingShareRecipient $recipient
	 */
	public static function fromArray(array $recipient): self {
		return new self(
			$recipient['type'],
			$recipient['value'],
			$recipient['display_name'] ?? null,
		);
	}

	/**
	 * @return SharingShareRecipient
	 */
	public function toArray(): array {
		$out = [
			'type' => $this->type,
			'value' => $this->value,
		];

		if ($this->displayName !== null) {
			$out['display_name'] = $this->displayName;
		}

		return $out;
	}
}
