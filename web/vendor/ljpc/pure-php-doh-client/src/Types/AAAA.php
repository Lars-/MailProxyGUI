<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;

final class AAAA extends Type {
	protected int $typeId = 28;
	protected string $type = 'AAAA';

	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		$this->value = implode( ":", unpack( "H4a/H4b/H4c/H4d/H4e/H4f/H4g/H4h", $byteOperations->getNextBytes( 16 ) ) );
	}
}
