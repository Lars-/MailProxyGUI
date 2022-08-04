<?php

namespace LJPc\DoH;

use LJPc\DoH\Types\Type;

class DNSRequest {
	public static function create( string $domainName, Type $type ): string {
		$dnsQuery = self::generateQuery( $domainName, $type );

		return self::encodeQuery( $dnsQuery );
	}

	private static function generateQuery( string $domainName, Type $type ): string {
		$typeHex = dechex( $type->getTypeId() );
		if ( strlen( $typeHex ) % 2 !== 0 ) {
			$typeHex = '0' . $typeHex;
		}
		$typePart = chr( 0 ) . hex2bin( $typeHex );
		if ( $type->getTypeId() > 255 ) {
			$typePart = hex2bin( $typeHex );
		}

		return "\xab\xcd" . chr( 1 ) . chr( 0 ) .
		       chr( 0 ) . chr( 1 ) .  /* qdc */
		       chr( 0 ) . chr( 0 ) .  /* anc */
		       chr( 0 ) . chr( 0 ) .  /* nsc */
		       chr( 0 ) . chr( 0 ) .  /* arc */
		       self::encodeDomainName( $domainName ) .
		       $typePart .
		       chr( 0 ) . chr( 1 );  /* qclass */
	}

	private static function encodeDomainName( string $domainName ): string {
		$retVal = "";

		$domainNamePieces = explode( '.', $domainName );
		foreach ( $domainNamePieces as $domainPiece ) {
			$retVal .= chr( strlen( $domainPiece ) ) . $domainPiece;
		}

		return $retVal . chr( 0 );
	}

	private static function encodeQuery( string $dnsQuery ): string {
		return str_replace( "=", "", base64_encode( $dnsQuery ) );
	}
}
