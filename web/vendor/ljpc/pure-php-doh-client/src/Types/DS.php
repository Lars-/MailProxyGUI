<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;

final class DS extends Type {
	protected int $typeId = 43;
	protected string $type = 'DS';

	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		$this->extras = unpack( "ntag/calgo/ctype", $byteOperations->getNextBytes( 4 ) );
		$this->value  = strtoupper( bin2hex( $byteOperations->getNextBytes( $ansHeader['length'] - 4 ) ) );
	}
}
