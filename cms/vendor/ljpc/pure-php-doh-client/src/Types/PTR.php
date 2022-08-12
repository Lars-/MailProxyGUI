<?php

namespace LJPc\DoH\Types;

final class PTR extends DomainAnswerType {
	protected int $typeId = 12;
	protected string $type = 'PTR';

	public function sanitizeInput( string $input ): string {
		if ( str_contains( $input, '.' ) ) {
			//IPv4
			if ( preg_match( '/[^.0-9]/', $input ) ) // '/[^a-z\d]/i' should also work.
			{
				//Invalid characters for IPv4
				return $input;
			}
			$ipParts = explode( '.', $input );

			$reversedIP = implode( '.', array_reverse( $ipParts ) );

			return $reversedIP . '.in-addr.arpa';
		}

		if ( str_contains( $input, ':' ) ) {
			//IPv6
			if ( preg_match( '/[^:0-9a-f]/i', $input ) ) // '/[^a-z\d]/i' should also work.
			{
				//Invalid characters for IPv4
				return $input;
			}

			$input = $this->expandIPv6($input);
			$input = str_replace(':','',$input);
			$input = strrev( $input );

			$reversedIP = implode( '.', str_split( $input ) );

			return $reversedIP . '.ip6.arpa';
		}

		return $input;
	}

	private function expandIPv6( $ip ): string {
		$hex = unpack( "H*hex", inet_pton( $ip ) );

		return substr( preg_replace( "/([A-f0-9]{4})/", "$1:", $hex['hex'] ), 0, - 1 );
	}
}
