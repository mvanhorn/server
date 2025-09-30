<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Sharing\Model;

use OCA\Sharing\Exception\ShareInvalidException;
use OCP\IUser;
use OCP\IUserManager;
use OCP\Server;

readonly class ShareOwner {
	private IUser $user;

	public function __construct(
		/** @var non-empty-string $owner */
		public string $userId,
		/** @var ?non-empty-string $ownerDisplayName */
		public ?string $displayName,
	) {
		/** @psalm-suppress DocblockTypeContradiction */
		if ($this->userId === '') {
			throw new ShareInvalidException('The userId is empty.');
		}

		// TODO: Will not work for remote owner
		$ownerUser = Server::get(IUserManager::class)->get($this->userId);
		if ($ownerUser === null) {
			throw new ShareInvalidException('The userId does not exist: ' . $this->userId);
		}

		$this->user = $ownerUser;

		/** @psalm-suppress DocblockTypeContradiction */
		if ($displayName === '') {
			throw new ShareInvalidException('The displayName is empty.');
		}
	}

	public function getUser(): IUser {
		return $this->user;
	}
}
