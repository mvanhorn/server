<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

use OCA\Sharing\Command\Create;
use OCA\Sharing\Command\Delete;
use OCA\Sharing\Command\Get;
use OCA\Sharing\Command\Update;
use OCA\Sharing\ResponseDefinitions;
use OCA\Sharing\Tests\AbstractApiTests;
use OCP\Server;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;

/**
 * @psalm-import-type SharingShare from ResponseDefinitions
 */
#[Group(name: 'DB')]
class CommandTest extends AbstractApiTests {
	private Input&MockObject $input;

	private Output&MockObject $output;

	private string $stdout = '';

	public function setUp(): void {
		parent::setUp();

		$this->input = $this->createMock(Input::class);

		$this->output = $this->createMock(Output::class);
		$this->output
			->method('writeln')
			->willReturnCallback(function (string $message): void {
				$this->stdout .= $message . "\n";
			});
	}

	protected function createShare(array $data): array {
		$this->input
			->expects($this->once())
			->method('getArgument')
			->with('data')
			->willReturn(json_encode($data, JSON_THROW_ON_ERROR));

		$exitCode = Server::get(Create::class)->execute($this->input, $this->output);
		$this->assertEquals(0, $exitCode);

		/** @var SharingShare $out */
		$out = json_decode($this->stdout, true, 512, JSON_THROW_ON_ERROR);

		return $out;
	}

	protected function getShare(string $shareID): array {
		$this->input
			->expects($this->once())
			->method('getArgument')
			->with('id')
			->willReturn($shareID);

		$exitCode = Server::get(Get::class)->execute($this->input, $this->output);
		$this->assertEquals(0, $exitCode);

		/** @var SharingShare $out */
		$out = json_decode($this->stdout, true, 512, JSON_THROW_ON_ERROR);

		return $out;
	}

	protected function deleteShare(string $shareID): void {
		$this->input
			->expects($this->once())
			->method('getArgument')
			->with('id')
			->willReturn($shareID);

		$exitCode = Server::get(Delete::class)->execute($this->input, $this->output);
		$this->assertEquals(0, $exitCode);
	}

	protected function updateShare(array $data): void {
		$this->input
			->expects($this->once())
			->method('getArgument')
			->with('data')
			->willReturn(json_encode($data, JSON_THROW_ON_ERROR));

		$exitCode = Server::get(Update::class)->execute($this->input, $this->output);
		$this->assertEquals(0, $exitCode);
	}
}
