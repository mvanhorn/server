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
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserManager;
use OCP\Server;
use OCP\Share\IShare;
use RuntimeException;

class UserShareRecipientType implements IShareRecipientType, IShareRecipientTypeSearch {
	public function getDisplayName(): string {
		return Server::get(IL10N::class)->t('User');
	}

	public function validateRecipient(string $recipient): bool {
		return Server::get(IUserManager::class)->userExists($recipient);
	}

	public function getRecipientValues(?IUser $currentUser, mixed $arguments): array {
		if (!$currentUser instanceof IUser) {
			return [];
		}

		return [$currentUser->getUID()];
	}

	public function getRecipientDisplayName(string $recipient): ?string {
		return Server::get(IUserManager::class)->getDisplayName($recipient);
	}

	/**
	 * @return list<ShareRecipientSearchResult>
	 */
	public function searchRecipients(string $query, int $limit, int $offset): array {
		// TODO: Maybe enable lookup?
		/** @var array{array{users: list<array>}, bool} $results */
		$results = Server::get(ISearch::class)->search($query, [IShare::TYPE_USER], false, $limit, $offset);
		$results = $results[0]['users'];

		$urlGenerator = Server::get(IURLGenerator::class);
		return array_map(static function (array $result) use ($urlGenerator): ShareRecipientSearchResult {
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

			if (!isset($result['shareWithDisplayNameUnique'])) {
				throw new RuntimeException('The shareWithDisplayNameUnique is missing.');
			}

			if (!is_string($result['shareWithDisplayNameUnique'])) {
				throw new RuntimeException('The shareWithDisplayNameUnique is not a string.');
			}

			if ($result['shareWithDisplayNameUnique'] === '') {
				throw new RuntimeException('The shareWithDisplayNameUnique is empty.');
			}

			$uid = $result['value']['shareWith'];

			$iconUrlLight = $urlGenerator->linkToRouteAbsolute('core.avatar.getAvatar', ['userId' => $uid, 'size' => 64]);
			if ($iconUrlLight === '') {
				throw new RuntimeException('The iconUrlLight is empty.');
			}

			$iconUrlDark = $urlGenerator->linkToRouteAbsolute('core.avatar.getAvatarDark', ['userId' => $uid, 'size' => 64]);
			if ($iconUrlDark === '') {
				throw new RuntimeException('The iconUrlDark is empty.');
			}

			return new ShareRecipientSearchResult(
				$uid,
				$result['label'],
				$result['shareWithDisplayNameUnique'],
				$iconUrlLight,
				$iconUrlDark,
			);
		}, $results);
	}
}
