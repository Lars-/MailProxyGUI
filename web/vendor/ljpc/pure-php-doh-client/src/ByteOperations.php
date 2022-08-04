<?php

namespace LJPc\DoH;

class ByteOperations {
	private int $byteCounter;
	private string $rawData;

	public function __construct( string $rawData, int $byteCounter = 0 ) {
		$this->byteCounter = $byteCounter;
		$this->rawData     = $rawData;
	}

	public function getSpecificBytes( int $start, int $bytes ): string {
		return substr( $this->rawData, $start, $bytes );
	}

	public function dismissBytes( int $bytes ): void {
		$this->byteCounter += $bytes;
	}

	public function getNextBytes( int $bytes ): string {
		$retVal            = substr( $this->rawData, $this->byteCounter, $bytes );
		$this->byteCounter += $bytes;

		return $retVal;
	}

	public function getRemainingData(): string {
		return substr( $this->rawData, $this->byteCounter );
	}

	public function getByteCounter(): int {
		return $this->byteCounter;
	}
}
