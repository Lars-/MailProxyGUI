<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;
use LJPc\DoH\DomainLabel;

final class CAA extends Type {
	use DomainLabel;

	protected int $typeId = 257;
	protected string $type = 'CAA';

	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		$this->extras['flags'] = hexdec( bin2hex( $byteOperations->getNextBytes( 1 ) ) );
		$tagLength             = hexdec( bin2hex( $byteOperations->getNextBytes( 1 ) ) );
		$this->extras['tag']   = $byteOperations->getNextBytes( $tagLength );
		$this->value           = $byteOperations->getNextBytes( $ansHeader['length'] - $tagLength - 2 );
	}
}
