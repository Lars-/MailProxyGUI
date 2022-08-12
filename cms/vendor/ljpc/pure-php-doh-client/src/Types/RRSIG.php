<?php

namespace LJPc\DoH\Types;

use DateTime;
use LJPc\DoH\ByteOperations;
use LJPc\DoH\DNSType;
use LJPc\DoH\DomainLabel;
use RuntimeException;

final class RRSIG extends Type {
	use DomainLabel;

	protected int $typeId = 46;
	protected string $type = 'RRSIG';

	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		$length       = $byteOperations->getByteCounter();
		$this->extras = unpack( "ntype/calgo/clabels/Nttl/Nsignature_expiration/Nsignature_inception/ntag", $byteOperations->getNextBytes( 18 ) );
		try {
			$this->extras['type'] = DNSType::getById( $this->extras['type'] )->getType();
		} catch ( RuntimeException $e ) {
			$this->extras['type'] = 'TYPE' . $this->extras['type'];
		}
		$this->extras['signature_expiration'] = new DateTime( '@' . $this->extras['signature_expiration'] );
		$this->extras['signature_inception']  = new DateTime( '@' . $this->extras['signature_inception'] );
		$this->extras['signersName']          = $this->domainLabel( $byteOperations );
		$length                               = $byteOperations->getByteCounter() - $length;
		$this->value                          = base64_encode( $byteOperations->getNextBytes( $ansHeader['length'] - $length ) );
	}
}
