<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;
use LJPc\DoH\DNSType;
use LJPc\DoH\DomainLabel;
use RuntimeException;

final class NSEC extends Type {
	use DomainLabel;

	protected int $typeId = 47;
	protected string $type = 'NSEC';

	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		$length                           = $byteOperations->getByteCounter();
		$this->extras['next_domain_name'] = $this->domainLabel( $byteOperations );
		$length                           = $byteOperations->getByteCounter() - $length;
		$this->extras['types']            = [];

		for ( $i = $byteOperations->getByteCounter(); $i < $ansHeader['length'] - $length + $byteOperations->getByteCounter(); $i ++ ) {
			$blockInfo = unpack( 'cwindow/clength', $byteOperations->getNextBytes( 2 ) );
			$blockData = unpack( 'C*', $byteOperations->getNextBytes( $blockInfo['length'] ) );
			$bitStr    = '';
			foreach ( $blockData as $r ) {
				$bitStr .= sprintf( '%08b', $r );
			}
			$blen = strlen( $bitStr );
			for ( $i = 0; $i < $blen; $i ++ ) {
				if ( $bitStr[ $i ] == '1' ) {
					$type = $blockInfo['window'] * 256 + $i;
					try {
						$type                    = DNSType::getById( $type );
						$this->extras['types'][] = $type->getType();
					} catch ( RuntimeException $e ) {
						if ( $type < 0 ) {
							$type = 65536 + $type;
						}
						$this->extras['types'][] = 'TYPE' . $type;
					}
				}
			}
		}
		$this->value = implode( ' ', $this->extras['types'] );
	}
}
