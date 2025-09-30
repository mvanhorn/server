<?php

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Sharing\Model;

use OCA\Sharing\ResponseDefinitions;

/**
 * @psalm-import-type SharingCompatible from ResponseDefinitions
 * @psalm-import-type SharingFeature from ResponseDefinitions
 */
interface IShareFeature {
	/**
	 * Get compatible source type and recipient type combinations.
	 *
	 * @return non-empty-list<SharingCompatible>
	 */
	public function getCompatibles(): array;

	/**
	 * Validate properties of new shares.
	 *
	 * TODO: Maybe tighten to non-empty-list
	 * @param array<string, list<string>> $properties
	 */
	public function validateProperties(array $properties): bool;
}
