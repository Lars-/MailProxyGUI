<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;
use LJPc\DoH\DomainLabel;

final class SOA extends Type {
	use DomainLabel;

	protected int $typeId = 6;
	protected string $type = 'SOA';

	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		$this->value = $this->domainLabel( $byteOperations );
		$responsible = $this->domainLabel( $byteOperations );

		$this->extras                = unpack( "Nserial/Nrefresh/Nretry/Nexpiry/Nminttl", $byteOperations->getNextBytes( 20 ) );
		$this->extras['responsible'] = $responsible;
	}
}
