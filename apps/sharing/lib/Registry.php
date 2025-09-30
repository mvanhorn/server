<?php

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Sharing;

use OCA\Sharing\Model\IShareFeature;
use OCA\Sharing\Model\IShareRecipientType;
use OCA\Sharing\Model\IShareSourceType;
use RuntimeException;

class Registry {
	/** @var array<class-string<IShareSourceType>, IShareSourceType> */
	private array $sourceTypes = [];

	/** @var array<class-string<IShareRecipientType>, IShareRecipientType> */
	private array $recipientTypes = [];

	/** @var array<class-string<IShareFeature>, IShareFeature> */
	private array $features = [];

	public function clear(): void {
		$this->sourceTypes = [];
		$this->recipientTypes = [];
		$this->features = [];
	}

	public function registerSourceType(IShareSourceType $sourceType): void {
		$class = $sourceType::class;

		if (isset($this->sourceTypes[$class])) {
			throw new RuntimeException('Share source type ' . $class . ' is already registered');
		}

		$this->sourceTypes[$class] = $sourceType;
	}

	public function registerRecipientType(IShareRecipientType $recipientType): void {
		$class = $recipientType::class;

		if (isset($this->recipientTypes[$class])) {
			throw new RuntimeException('Share recipient type ' . $class . ' is already registered');
		}

		$this->recipientTypes[$class] = $recipientType;
	}

	public function registerFeature(IShareFeature $feature): void {
		$class = $feature::class;

		if (isset($this->features[$class])) {
			throw new RuntimeException('Share feature ' . $class . ' is already registered');
		}

		$this->features[$class] = $feature;
	}

	/**
	 * @return array<class-string<IShareSourceType>, IShareSourceType>
	 */
	public function getSourceTypes(): array {
		return $this->sourceTypes;
	}

	/**
	 * @return array<class-string<IShareRecipientType>, IShareRecipientType>
	 */
	public function getRecipientTypes(): array {
		return $this->recipientTypes;
	}

	/**
	 * @return array<class-string<IShareFeature>, IShareFeature>
	 */
	public function getFeatures(): array {
		return $this->features;
	}
}
