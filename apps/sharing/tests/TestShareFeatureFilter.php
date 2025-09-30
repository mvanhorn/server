<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Sharing\Tests;

use OCA\Sharing\Model\IShareFeatureFilter;
use OCA\Sharing\ResponseDefinitions;
use OCP\IUser;

/**
 * @psalm-import-type SharingCompatible from ResponseDefinitions
 */
class TestShareFeatureFilter implements IShareFeatureFilter {

	public function __construct(
		/** @var non-empty-list<SharingCompatible> $compatibles */
		private readonly array $compatibles,
	) {
	}

	/**
	 * @return non-empty-list<SharingCompatible>
	 */
	public function getCompatibles(): array {
		return $this->compatibles;
	}

	public function validateProperties(array $properties): bool {
		return true;
	}

	public function isFiltered(?IUser $currentUser, mixed $arguments, array $properties): bool {
		return $arguments === 'filtered' || (($properties['filtered'] ?? ['false'])[0] === 'true');
	}
}
