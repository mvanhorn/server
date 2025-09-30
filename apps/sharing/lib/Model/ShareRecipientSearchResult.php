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
 * @psalm-import-type SharingRecipientSearchResult from ResponseDefinitions
 */
class ShareRecipientSearchResult {
	/** @var ?class-string<IShareRecipientType> $type */
	private ?string $type = null;

	public function __construct(
		/** @var non-empty-string $value */
		readonly public string $value,
		/** @var non-empty-string $displayName */
		readonly public string $displayName,
		/** @var ?non-empty-string $displayNameUnique */
		readonly public ?string $displayNameUnique,
		/** @var ?non-empty-string $iconUrlLight */
		readonly public ?string $iconUrlLight,
		/** @var ?non-empty-string $iconUrlDark */
		readonly public ?string $iconUrlDark,
	) {
		/** @psalm-suppress DocblockTypeContradiction */
		if ($value === '') {
			throw new RuntimeException('The value is empty.');
		}

		/** @psalm-suppress DocblockTypeContradiction */
		if ($displayName === '') {
			throw new RuntimeException('The displayName is empty.');
		}

		/** @psalm-suppress DocblockTypeContradiction */
		if ($displayNameUnique === '') {
			throw new RuntimeException('The displayNameUnique is empty.');
		}

		if ($iconUrlLight !== null && !preg_match('/^https?:\/\//', $iconUrlLight)) {
			throw new RuntimeException('The iconUrlLight is not a valid absolute URL: ' . $iconUrlLight);
		}

		if ($iconUrlDark !== null && !preg_match('/^https?:\/\//', $iconUrlDark)) {
			throw new RuntimeException('The iconUrlDark is not a valid absolute URL: ' . $iconUrlDark);
		}
	}

	/**
	 * @param class-string<IShareRecipientType> $type
	 */
	public function setType(string $type): self {
		if ($this->type !== null) {
			throw new RuntimeException('The type is already set.');
		}

		if (!isset(Server::get(Registry::class)->getRecipientTypes()[$type])) {
			throw new RuntimeException('The recipient type is not registered: ' . $type);
		}

		$this->type = $type;

		return $this;
	}

	/**
	 * @return SharingRecipientSearchResult
	 */
	public function toArray(): array {
		if ($this->type === null) {
			throw new RuntimeException('The type has not been set.');
		}

		$out = [
			'type' => $this->type,
			'value' => $this->value,
			'display_name' => $this->displayName,
		];

		if ($this->displayNameUnique !== null) {
			$out['display_name_unique'] = $this->displayNameUnique;
		}

		if ($this->iconUrlLight !== null) {
			$out['icon_url_light'] = $this->iconUrlLight;
		}

		if ($this->iconUrlDark !== null) {
			$out['icon_url_dark'] = $this->iconUrlDark;
		}

		return $out;
	}
}
