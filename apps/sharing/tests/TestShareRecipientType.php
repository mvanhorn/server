<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Sharing\Tests;

use OCA\Sharing\Model\IShareRecipientType;
use OCA\Sharing\Model\IShareRecipientTypeSearch;
use OCA\Sharing\Model\ShareRecipientSearchResult;
use OCP\IUser;

class TestShareRecipientType implements IShareRecipientType, IShareRecipientTypeSearch {
	public function __construct(
		/** @var array<string, non-empty-string> $validRecipients */
		private readonly array $validRecipients,
		/** @var list<non-empty-string> $recipients */
		private readonly array $recipients,
		/** @var list<ShareRecipientSearchResult> $searchRecipients */
		private readonly array $searchRecipients,
	) {
	}

	public function getDisplayName(): string {
		/** @var non-empty-list<non-empty-string> $parts */
		$parts = explode('\\', static::class);
		return end($parts);
	}

	public function validateRecipient(string $recipient): bool {
		return array_key_exists($recipient, $this->validRecipients);
	}

	/**
	 * @return list<string>
	 */
	public function getRecipientValues(?IUser $currentUser, mixed $arguments): array {
		return $this->recipients;
	}

	public function getRecipientDisplayName(string $recipient): ?string {
		return $this->validRecipients[$recipient];
	}

	public function searchRecipients(string $query, int $limit, int $offset): array {
		return array_slice(array_map(static fn (ShareRecipientSearchResult $result): ShareRecipientSearchResult => clone $result, $this->searchRecipients), $offset, $limit);
	}
}
