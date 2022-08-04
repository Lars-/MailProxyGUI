<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;
use LJPc\DoH\DomainLabel;

class TextAnswerType extends Type {
	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		for ( $string_count = 0; strlen( $this->value ) + ( 1 + $string_count ) < $ansHeader['length']; $string_count ++ ) {
			$string_length = ord( $byteOperations->getNextBytes( 1 ) );
			$this->value   .= $byteOperations->getNextBytes( $string_length );
		}
	}
}
