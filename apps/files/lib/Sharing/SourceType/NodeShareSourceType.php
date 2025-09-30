<?php

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Files\Sharing\SourceType;

use OCA\Sharing\Model\IShareSourceType;
use OCP\Files\IRootFolder;
use OCP\IL10N;
use OCP\IUser;
use OCP\Server;

class NodeShareSourceType implements IShareSourceType {
	public function getDisplayName(): string {
		return Server::get(IL10N::class)->t('File or folder');
	}

	public function validateSource(IUser $owner, string $source): bool {
		return Server::get(IRootFolder::class)->getUserFolder($owner->getUID())->getFirstNodeById((int)$source) !== null;
	}

	public function getSourceDisplayName(string $source): ?string {
		$displayName = Server::get(IRootFolder::class)->getFirstNodeById((int)$source)?->getName();
		if ($displayName === '') {
			return null;
		}

		return $displayName;
	}
}
