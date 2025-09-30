<?php

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Sharing;

use OCA\Sharing\AppInfo\Application;
use OCA\Sharing\Model\IShareFeature;
use OCA\Sharing\Model\IShareRecipientType;
use OCA\Sharing\Model\IShareSourceType;
use OCP\Capabilities\ICapability;

/**
 * @psalm-import-type SharingFeature from ResponseDefinitions
 */
class Capabilities implements ICapability {
	public function __construct(
		private readonly Registry $registry,
	) {
	}

	/**
	 * @return array{
	 *     sharing: array{
	 *         api_versions: list<'v1'>,
	 *         source_types: array<class-string<IShareSourceType>, non-empty-string>,
	 *         recipient_types: array<class-string<IShareRecipientType>, non-empty-string>,
	 *         features: array<class-string<IShareFeature>, SharingFeature>,
	 *     },
	 * }
	 */
	public function getCapabilities(): array {
		return [
			Application::APP_ID => [
				'api_versions' => ['v1'],
				'source_types' => array_map(static fn (IShareSourceType $sourceType): string => $sourceType->getDisplayName(), $this->registry->getSourceTypes()),
				'recipient_types' => array_map(static fn (IShareRecipientType $recipientType): string => $recipientType->getDisplayName(), $this->registry->getRecipientTypes()),
				'features' => array_map(static fn (IShareFeature $feature): array => [
					'compatibles' => $feature->getCompatibles(),
				], $this->registry->getFeatures()),
			],
		];
	}
}
