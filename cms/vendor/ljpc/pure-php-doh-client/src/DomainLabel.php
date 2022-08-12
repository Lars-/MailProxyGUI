<?php

namespace LJPc\DoH;

trait DomainLabel {
	private ByteOperations $domainLabelByteOperations;

	private function domainLabel( ByteOperations $byteOperations ): string {
		$this->domainLabelByteOperations = $byteOperations;

		$count  = 0;
		$labels = $this->domainLabels( $this->domainLabelByteOperations->getByteCounter(), $count );
		$domain = implode( ".", $labels );

		$this->domainLabelByteOperations->dismissBytes( $count );

		return $domain;
	}

	private function domainLabels( $offset, &$counter = 0 ): array {
		$labels      = [];
		$startoffset = $offset;
		$return      = false;
		while ( ! $return ) {
			$label_len = ord( $this->domainLabelByteOperations->getSpecificBytes( $offset ++, 1 ) );
			if ( $label_len <= 0 ) {
				$return = true;
			} // end of data
			elseif ( $label_len < 64 ) // uncompressed data
			{
				$labels[] = $this->domainLabelByteOperations->getSpecificBytes( $offset, $label_len );
				$offset   += $label_len;
			} else // label_len>=64 -- pointer
			{
				$nextitem       = $this->domainLabelByteOperations->getSpecificBytes( $offset ++, 1 );
				$pointer_offset = ( ( $label_len & 0x3f ) << 8 ) + ord( $nextitem );
				// Branch Back Upon Ourselves...
				$pointer_labels = $this->domainLabels( $pointer_offset );
				foreach ( $pointer_labels as $ptr_label ) {
					$labels[] = $ptr_label;
				}
				$return = true;
			}
		}
		$counter = $offset - $startoffset;

		return $labels;
	}
}
