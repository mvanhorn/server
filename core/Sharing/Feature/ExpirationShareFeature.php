<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OC\Core\Sharing\Feature;

use DateTimeImmutable;
use Exception;
use OC\Core\Sharing\RecipientType\GroupShareRecipientType;
use OC\Core\Sharing\RecipientType\UserShareRecipientType;
use OCA\Files\Sharing\SourceType\NodeShareSourceType;
use OCA\Sharing\Model\IShareFeature;
use OCA\Sharing\Model\IShareFeatureFilter;
use OCP\IUser;

class ExpirationShareFeature implements IShareFeature, IShareFeatureFilter {
	public function getCompatibles(): array {
		$compatibles = [];
		foreach ([NodeShareSourceType::class] as $sourceType) {
			foreach ([UserShareRecipientType::class, GroupShareRecipientType::class] as $recipientType) {
				$compatibles[] = [
					'source_type' => $sourceType,
					'recipient_type' => $recipientType,
				];
			}
		}

		return $compatibles;
	}

	public function validateProperties(array $properties): bool {
		if (array_keys($properties) !== ['date'] || count($properties['date']) !== 1 || $properties['date'][0] === '') {
			return false;
		}

		try {
			new DateTimeImmutable($properties['date'][0]);
			return true;
		} catch (Exception) {
			return false;
		}
	}

	/**
	 * @throws Exception
	 */
	public function isFiltered(?IUser $currentUser, mixed $arguments, array $properties): bool {
		return (new DateTimeImmutable($properties['date'][0]))->diff(new DateTimeImmutable())->invert === 0;
	}
}
