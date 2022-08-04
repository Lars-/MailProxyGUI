<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;

final class LOC extends TextAnswerType {
	protected int $typeId = 29;
	protected string $type = 'LOC';

	public function decode( ByteOperations $byteOperations, array $ansHeader ): void {
		$data                      = unpack(
			'Cver/Csize/Choriz_pre/Cvert_pre/Nlatitude/Nlongitude/Naltitude',
			$byteOperations->getNextBytes( 20 )
		);
		$this->extras['horiz_pre'] = $this->precisionSizeNtoA( $data['horiz_pre'] ) / 100;
		$this->extras['vert_pre']  = $this->precisionSizeNtoA( $data['vert_pre'] ) / 100;
		$this->extras['altitude']  = ( $data['altitude'] - 10000000 ) / 100 / 100;
		$this->extras['size']      = $this->precisionSizeNtoA( $data['size'] ) / 100;

		if ( $data['latitude'] < 0 ) {
			$this->extras['latitude'] = ( $data['latitude'] + 2147483648 ) / 3600000;
		} else {
			$this->extras['latitude'] = ( $data['latitude'] - 2147483648 ) / 3600000;
		}
		if ( $data['longitude'] < 0 ) {
			$this->extras['longitude'] = ( $data['longitude'] + 2147483648 ) / 3600000;
		} else {
			$this->extras['longitude'] = ( $data['longitude'] - 2147483648 ) / 3600000;
		}
	}

	private function precisionSizeNtoA( $prec ): float|int {
		$mantissa = ( ( $prec >> 4 ) & 0x0f ) % 10;
		$exponent = ( ( $prec >> 0 ) & 0x0f ) % 10;

		return $mantissa * 10 ** $exponent;
	}
}
