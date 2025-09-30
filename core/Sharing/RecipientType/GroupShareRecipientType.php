<?php

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OC\Core\Sharing\RecipientType;

use OCA\Sharing\Model\IShareRecipientType;
use OCA\Sharing\Model\IShareRecipientTypeSearch;
use OCA\Sharing\Model\ShareRecipientSearchResult;
use OCP\Collaboration\Collaborators\ISearch;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\IUser;
use OCP\Server;
use OCP\Share\IShare;
use RuntimeException;

class GroupShareRecipientType implements IShareRecipientType, IShareRecipientTypeSearch {
	public function getDisplayName(): string {
		return Server::get(IL10N::class)->t('Group');
	}

	public function validateRecipient(string $recipient): bool {
		return Server::get(IGroupManager::class)->groupExists($recipient);
	}

	public function getRecipientValues(?IUser $currentUser, mixed $arguments): array {
		if (!$currentUser instanceof IUser) {
			return [];
		}

		return Server::get(IGroupManager::class)->getUserGroupIds($currentUser);
	}

	public function getRecipientDisplayName(string $recipient): ?string {
		$displayName = Server::get(IGroupManager::class)->getDisplayName($recipient);
		if ($displayName === '') {
			return null;
		}

		return $displayName;
	}

	/**
	 * @return list<ShareRecipientSearchResult>
	 */
	public function searchRecipients(string $query, int $limit, int $offset): array {
		// TODO: Maybe enable lookup?
		/** @var array{array{groups: list<array>}, bool} $results */
		$results = Server::get(ISearch::class)->search($query, [IShare::TYPE_GROUP], false, $limit, $offset);
		$results = $results[0]['groups'];

		return array_map(static function (array $result): ShareRecipientSearchResult {
			if (!isset($result['value'])) {
				throw new RuntimeException('The value is missing.');
			}

			if (!is_array($result['value'])) {
				throw new RuntimeException('The value is not an array.');
			}

			if (!isset($result['value']['shareWith'])) {
				throw new RuntimeException('The shareWith is missing.');
			}

			if (!is_string($result['value']['shareWith'])) {
				throw new RuntimeException('The shareWith is not a string.');
			}

			if ($result['value']['shareWith'] === '') {
				throw new RuntimeException('The shareWith is empty.');
			}

			if (!isset($result['label'])) {
				throw new RuntimeException('The label is missing.');
			}

			if (!is_string($result['label'])) {
				throw new RuntimeException('The label is not a string.');
			}

			if ($result['label'] === '') {
				throw new RuntimeException('The label is empty.');
			}

			return new ShareRecipientSearchResult(
				$result['value']['shareWith'],
				$result['label'],
				null,
				null,
				null,
			);
		}, $results);
	}
}
