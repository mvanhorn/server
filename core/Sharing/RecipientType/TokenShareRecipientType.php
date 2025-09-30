<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OC\Core\Sharing\RecipientType;

use OC\Share\Constants;
use OCA\Sharing\Model\IShareRecipientType;
use OCP\IL10N;
use OCP\IUser;
use OCP\Server;

class TokenShareRecipientType implements IShareRecipientType {
	public function getDisplayName(): string {
		return Server::get(IL10N::class)->t('Public link');
	}

	public function validateRecipient(string $recipient): bool {
		return preg_match('/^[a-z0-9-]{' . Constants::MIN_TOKEN_LENGTH . ',' . Constants::MAX_TOKEN_LENGTH . '}$/i', $recipient) === 1;
	}

	public function getRecipientValues(?IUser $currentUser, mixed $arguments): array {
		if (is_string($arguments)) {
			return [$arguments];
		}

		return [];
	}

	public function getRecipientDisplayName(string $recipient): ?string {
		return null;
	}
}
