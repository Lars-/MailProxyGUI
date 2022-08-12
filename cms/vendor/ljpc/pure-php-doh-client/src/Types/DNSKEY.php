<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;

final class DNSKEY extends Type {
	protected int $typeId = 48;
	protected string $type = 'DNSKEY';

	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		$this->extras = unpack( "nflags/cprotocol/calgo", $byteOperations->getNextBytes( 4 ) );
		$this->value  = base64_encode( $byteOperations->getNextBytes( $ansHeader['length'] - 4 ) );
	}
}
