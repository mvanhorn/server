<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OC\Core\Sharing\Feature;

use OC\Core\Sharing\RecipientType\GroupShareRecipientType;
use OC\Core\Sharing\RecipientType\UserShareRecipientType;
use OCA\Files\Sharing\SourceType\NodeShareSourceType;
use OCA\Sharing\Model\IShareFeature;

class LabelShareFeature implements IShareFeature {
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
		return array_keys($properties) === ['text'] && count($properties['text']) === 1 && $properties['text'][0] !== '';
	}
}
