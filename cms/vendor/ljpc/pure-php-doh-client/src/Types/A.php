<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;

final class A extends Type {
	protected int $typeId = 1;
	protected string $type = 'A';

	public function decode( ByteOperations $byteOperations, array $ansHeader ):void {
		$this->value = implode( ".", unpack( "Ca/Cb/Cc/Cd", $byteOperations->getNextBytes( 4 ) ) );
	}
}
