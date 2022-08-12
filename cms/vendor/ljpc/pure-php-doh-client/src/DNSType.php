<?php

namespace LJPc\DoH;

use LJPc\DoH\Types\A;
use LJPc\DoH\Types\AAAA;
use LJPc\DoH\Types\CAA;
use LJPc\DoH\Types\CNAME;
use LJPc\DoH\Types\DNAME;
use LJPc\DoH\Types\DNSKEY;
use LJPc\DoH\Types\DS;
use LJPc\DoH\Types\KEY;
use LJPc\DoH\Types\LOC;
use LJPc\DoH\Types\MX;
use LJPc\DoH\Types\NS;
use LJPc\DoH\Types\NSEC;
use LJPc\DoH\Types\PTR;
use LJPc\DoH\Types\RRSIG;
use LJPc\DoH\Types\SOA;
use LJPc\DoH\Types\SPF;
use LJPc\DoH\Types\SRV;
use LJPc\DoH\Types\TXT;
use LJPc\DoH\Types\Type;
use LJPc\DoH\Types\URI;
use RuntimeException;

/**
 * @method static A A()
 * @method static NS NS()
 * @method static CNAME CNAME()
 * @method static SOA SOA()
 * @method static PTR PTR()
 * @method static MX MX()
 * @method static TXT TXT()
 * @method static AAAA AAAA()
 * @method static LOC LOC()
 * @method static SRV SRV()
 * @method static DNAME DNAME()
 * @method static DS DS()
 * @method static RRSIG RRSIG()
 * @method static NSEC NSEC()
 * @method static DNSKEY DNSKEY()
 * @method static SPF SPF()
 * @method static URI URI()
 * @method static CAA CAA()
 */
class DNSType {
	public static function __callStatic( string $name, array $arguments ) {
		$types = self::getAll();
		if ( ! isset( $types[ $name ] ) ) {
			throw new RuntimeException( 'Type ' . $name . ' is not implemented' );
		}

		return new $types[ $name ];
	}

	public static function getAll(): array {
		return [
			'A'      => A::class,       // 1
			'NS'     => NS::class,      // 2
			'CNAME'  => CNAME::class,   // 5
			'SOA'    => SOA::class,     // 6
			'PTR'    => PTR::class,     // 12
			'MX'     => MX::class,      // 15
			'TXT'    => TXT::class,     // 16
			'KEY'    => KEY::class,     // 25
			'AAAA'   => AAAA::class,    // 28
			'LOC'    => LOC::class,     // 29
			'SRV'    => SRV::class,     // 33
			'DNAME'  => DNAME::class,   // 39
			'DS'     => DS::class,      // 43
			'RRSIG'  => RRSIG::class,   // 46
			'NSEC'   => NSEC::class,    // 47
			'DNSKEY' => DNSKEY::class,  // 48
			'SPF'    => SPF::class,     // 99
			'URI'    => URI::class,     // 256
			'CAA'    => CAA::class,     // 257
		];
	}

	public static function getById( int $id ): Type {
		$types = self::getAll();
		foreach ( $types as $type ) {
			/** @var Type $typeClass */
			$typeClass = new $type;
			if ( $typeClass->getTypeId() === $id ) {
				return $typeClass;
			}
		}
		throw new RuntimeException( 'Type ' . $id . ' is not implemented' );
	}
}
