<?php

namespace LJPc\DoH;

use JsonSerializable;

class DNSQueryResult implements JsonSerializable {
	private string $server;
	private array $answers = [];
	private array $authorityRecords = [];
	private array $additionalRecords = [];

	/**
	 * @return DNSRecord[]
	 */
	public function getAnswers(): array {
		return $this->answers;
	}

	public function addAnswer( DNSRecord $record ) {
		$this->answers[] = $record;
	}

	/**
	 * @return DNSRecord[]
	 */
	public function getAuthorityRecords(): array {
		return $this->authorityRecords;
	}

	public function addAuthorityRecord( DNSRecord $record ): void {
		$this->authorityRecords[] = $record;
	}

	/**
	 * @return DNSRecord[]
	 */
	public function getAdditionalRecords(): array {
		return $this->additionalRecords;
	}

	public function addAdditionalRecord( DNSRecord $record ): void {
		$this->additionalRecords[] = $record;
	}

	public function jsonSerialize(): array {
		return [
			'server'            => $this->server,
			'answers'           => $this->answers,
			'authorityRecords'  => $this->authorityRecords,
			'additionalRecords' => $this->additionalRecords,
		];
	}

	public function getServer(): string {
		return $this->server;
	}

	public function setServer( string $server ): void {
		$this->server = $server;
	}
}
