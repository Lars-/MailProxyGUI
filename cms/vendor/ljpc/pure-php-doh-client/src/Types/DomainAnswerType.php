<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;
use LJPc\DoH\DomainLabel;

class DomainAnswerType extends Type {
	use DomainLabel;

	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		$this->value = $this->domainLabel( $byteOperations );
	}
}
