<?php

namespace LJPc\DoH\Types;

use LJPc\DoH\ByteOperations;

abstract class Type {
	protected int $typeId = 0;
	protected string $type = '';
	protected array $extras = [];
	protected string $value = '';

	abstract public function decode( ByteOperations $byteOperations, array $ansHeader ): void;

	public function sanitizeInput( string $input ): string {
		return $input;
	}

	public function getTypeId(): int {
		return $this->typeId;
	}

	public function getExtras(): array {
		return $this->extras;
	}

	public function getValue(): string {
		return $this->value;
	}

	public function getType(): string {
		return $this->type;
	}
}
