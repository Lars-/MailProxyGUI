<?php

namespace LJPc\DoH;

use JsonSerializable;

class DNSRecord implements JsonSerializable {
	use DomainLabel;

	public string $domainName;
	public int $ttl;
	public string $type;
	public array $extras;
	public string $value;
	private ByteOperations $byteOperations;

	public function __construct( ByteOperations $byteOperations ) {
		$this->byteOperations = $byteOperations;

		$this->domainName = $this->domainLabel($this->byteOperations);
		$ansHeader        = unpack( "ntype/nclass/Nttl/nlength", $this->byteOperations->getNextBytes( 10 ) );
		$this->ttl        = $ansHeader['ttl'];

		$type       = DNSType::getById( $ansHeader['type'] );
		$this->type = $type->getType();
		$type->decode( $this->byteOperations, $ansHeader );

		$this->extras = $type->getExtras();
		$this->value  = $type->getValue();
	}

	public function jsonSerialize(): array {
		return [
			'domainName' => $this->domainName,
			'ttl'        => $this->ttl,
			'type'       => $this->type,
			'extras'     => $this->extras,
			'value'      => $this->value,
		];
	}
}
