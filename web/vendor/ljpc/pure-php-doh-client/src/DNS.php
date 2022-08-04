<?php

namespace LJPc\DoH;

use LJPc\DoH\Servers\AdGuard;
use LJPc\DoH\Servers\CloudFlare;
use LJPc\DoH\Servers\DoHServer;
use LJPc\DoH\Servers\Google;
use LJPc\DoH\Servers\Quad9;
use LJPc\DoH\Types\Type;
use RuntimeException;

class DNS {
	private static ByteOperations $byteOperations;
	private static array $header = [];
	private static array $servers = [
		CloudFlare::class,
		Google::class,
		Quad9::class,
		AdGuard::class,
	];
	private static int $queryCounter = 0;

	public static function setServers( array $servers ) {
		self::$servers = [];
		foreach ( $servers as $server ) {
			if ( $server instanceof DoHServer ) {
				self::$servers[] = $server::class;
				continue;
			}
			if ( class_exists( $server ) ) {
				self::$servers[] = $server;
			}
		}
	}

	public static function query( string $domainName, Type $type, string $useSpecificServer = null ): DNSQueryResult {
		$domainName = $type->sanitizeInput( $domainName );
		$dnsQuery   = DNSRequest::create( $domainName, $type );
		[ $server, $rawAnswer ] = self::requestAnswer( $dnsQuery, $useSpecificServer );

		self::$byteOperations = new ByteOperations( $rawAnswer );

		self::processHeader();

		//Query counter is not important for the answer
		if ( self::getHeader()['qdcount'] > 0 ) {
			for ( $i = 0; $i < self::getHeader()['qdcount']; $i ++ ) {
				$c = 1;
				while ( $c !== 0 ) {
					$c = hexdec( bin2hex( self::$byteOperations->getNextBytes( 1 ) ) );
				}
				self::$byteOperations->dismissBytes( 4 );
			}
		}

		$dnsQueryResults = new DNSQueryResult();

		$dnsQueryResults->setServer( $server );

		$answerAmount = self::getHeader()['ancount'];
		for ( $i = 0; $i < $answerAmount; $i ++ ) {
			try {
				$dnsQueryResults->addAnswer( new DNSRecord( self::$byteOperations ) );
			} catch ( RuntimeException $e ) {
			}
		}

		$authorityResultAmount = self::getHeader()['nscount'];
		for ( $i = 0; $i < $authorityResultAmount; $i ++ ) {
			try {
				$dnsQueryResults->addAuthorityRecord( new DNSRecord( self::$byteOperations ) );
			} catch ( RuntimeException $e ) {
			}
		}

		$authorityResultAmount = self::getHeader()['arcount'];
		for ( $i = 0; $i < $authorityResultAmount; $i ++ ) {
			try {
				$dnsQueryResults->addAuthorityRecord( new DNSRecord( self::$byteOperations ) );
			} catch ( RuntimeException $e ) {
			}
		}

		return $dnsQueryResults;
	}

	private static function requestAnswer( string $dnsQuery, string $specificServer = null ): array {
		if ( $specificServer === null ) {
			$serverCount = count( self::$servers );
			if ( $serverCount === 0 ) {
				throw new RuntimeException( 'No DoH servers available, please set at least one server to continue' );
			}

			$data         = null;
			$triedServers = 0;
			$useServer    = null;
			while ( $data === null && $triedServers < $serverCount ) {
				/** @var DoHServer $useServer */
				$useServer = new self::$servers[ self::$queryCounter % $serverCount ];
				if ( ! method_exists( $useServer, 'call' ) ) {
					self::$queryCounter ++;
					$triedServers ++;
					$useServer = new self::$servers[ self::$queryCounter % $serverCount ];
				}
				self::$queryCounter ++;
				$triedServers ++;

				$data = $useServer->call( $dnsQuery );
			}
			if ( $data === null || $useServer === null ) {
				throw new RuntimeException( 'No response from any server' );
			}
		} else {
			$useServer = new $specificServer;
			$data      = $useServer->call( $dnsQuery );
			if ( $data === null || $useServer === null ) {
				throw new RuntimeException( 'No response from any server' );
			}
		}

		return [ $useServer::class, $data ];
	}

	private static function processHeader() {
		self::$header = unpack( "nid/nspec/nqdcount/nancount/nnscount/narcount", self::$byteOperations->getNextBytes( 12 ) );
	}

	private static function getHeader(): array {
		return self::$header;
	}
}
