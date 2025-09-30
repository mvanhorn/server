<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Sharing\Model;

interface IShareRecipientTypeSearch extends IShareRecipientType {
	/**
	 * Search for recipients.
	 *
	 * @param non-empty-string $query
	 * @param positive-int $limit
	 * @param non-negative-int $offset
	 * @return list<ShareRecipientSearchResult>
	 */
	public function searchRecipients(string $query, int $limit, int $offset): array;
}
