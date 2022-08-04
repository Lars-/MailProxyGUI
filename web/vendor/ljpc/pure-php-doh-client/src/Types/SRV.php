<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;
use LJPc\DoH\DomainLabel;

final class SRV extends Type {
	use DomainLabel;

	protected int $typeId = 33;
	protected string $type = 'SRV';

	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		$this->extras = unpack( "npriority/nweight/nport", $byteOperations->getNextBytes( 6 ) );
		$this->value  = $this->domainLabel( $byteOperations );
	}
}
