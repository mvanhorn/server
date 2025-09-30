<?php

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);


use OCA\Sharing\Registry;
use OCA\Sharing\Tests\TestShareFeature;
use OCA\Sharing\Tests\TestShareRecipientType;
use OCA\Sharing\Tests\TestShareSourceType;
use OCP\Server;
use Test\TestCase;

class RegistryTest extends TestCase {
	private Registry $registry;

	public function setUp(): void {
		parent::setUp();

		$this->registry = Server::get(Registry::class);
	}

	protected function tearDown(): void {
		$this->registry->clear();

		parent::tearDown();
	}

	public function testRegisterSourceType(): void {
		$sourceType = new TestShareSourceType([]);
		$this->registry->registerSourceType($sourceType);

		$this->assertEquals([TestShareSourceType::class => $sourceType], $this->registry->getSourceTypes());

		$this->expectException(RuntimeException::class);
		$this->registry->registerSourceType($sourceType);
	}

	public function testRegisterRecipientType(): void {
		$recipientType = new TestShareRecipientType([], [], []);
		$this->registry->registerRecipientType($recipientType);

		$this->assertEquals([TestShareRecipientType::class => $recipientType], $this->registry->getRecipientTypes());

		$this->expectException(RuntimeException::class);
		$this->registry->registerRecipientType($recipientType);
	}

	public function testRegisterFeature(): void {
		$feature = new TestShareFeature([['source_type' => TestShareSourceType::class, 'recipient_type' => TestShareRecipientType::class]], []);
		$this->registry->registerFeature($feature);

		$this->assertEquals([TestShareFeature::class => $feature], $this->registry->getFeatures());

		$this->expectException(RuntimeException::class);
		$this->registry->registerFeature($feature);
	}

	public function testClear(): void {
		$this->registry->registerSourceType(new TestShareSourceType([]));
		$this->registry->registerRecipientType(new TestShareRecipientType([], [], []));
		$this->registry->registerFeature(new TestShareFeature([['source_type' => TestShareSourceType::class, 'recipient_type' => TestShareRecipientType::class]], []));

		$this->registry->clear();

		$this->assertEquals([], $this->registry->getFeatures());
	}
}
