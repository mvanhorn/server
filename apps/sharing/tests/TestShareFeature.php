<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Sharing\Tests;

use OCA\Sharing\Model\IShareFeature;
use OCA\Sharing\ResponseDefinitions;

/**
 * @psalm-import-type SharingCompatible from ResponseDefinitions
 */
class TestShareFeature implements IShareFeature {
	public function __construct(
		/** @var non-empty-list<SharingCompatible> $compatibles */
		private readonly array $compatibles,
		/** @var list<string> $validProperties */
		private readonly array $validProperties,
	) {
	}

	/**
	 * @return non-empty-list<SharingCompatible>
	 */
	public function getCompatibles(): array {
		return $this->compatibles;
	}

	public function validateProperties(array $properties): bool {
		return array_intersect(array_keys($properties), $this->validProperties) !== [];
	}
}
