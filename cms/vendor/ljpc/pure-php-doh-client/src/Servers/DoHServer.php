<?php

namespace LJPc\DoH\Servers;

abstract class DoHServer {
	protected string $url = '';

	public function call( $dnsQuery ): ?string {
		$ch = curl_init();

		$headers = [ 'Accept: application/dns-udpwireformat', 'Content-type: application/dns-udpwireformat' ];

		curl_setopt( $ch, CURLOPT_URL, $this->url . "?dns=$dnsQuery" );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'LJPc-PHP-DoH-Client' );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

		$output = curl_exec( $ch );
		if ( $output === false ) {
			return null;
		}

		return $output;
	}
}
