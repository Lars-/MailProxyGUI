<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;
use LJPc\DoH\DomainLabel;

final class URI extends Type {
	use DomainLabel;

	protected int $typeId = 256;
	protected string $type = 'URI';

	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		$this->extras = unpack( "npriority/nweight/a*target", $byteOperations->getNextBytes( $ansHeader['length'] ) );
		$this->value  = $this->extras['target'];
		unset( $this->extras['target'] );
	}
}
