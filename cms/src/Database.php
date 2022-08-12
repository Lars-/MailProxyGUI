<?php

namespace LJPc\MailProxyGui;

use mysqli;

class Database {
	private static self $instance;
	private mysqli $db;

	private function __construct() {
		$this->db = new mysqli( "db", "root", "3h0aZvklAqkZgNmoubOfNb7p7PAID4CQ", "database" );
		if ( $this->db->connect_error ) {
			die( "Connection failed: " . $this->db->connect_error );
		}
	}

	public static function instance(): self {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function getServer( int $id ): bool|array|null {
		$result = $this->db->query( "SELECT * FROM servers WHERE id = $id" );
		if ( $result === false ) {
			return false;
		}

		return $result->fetch_assoc();
	}

	public function findServer( string $ip ): ?int {
		$stmt = $this->db->prepare( "SELECT * FROM servers WHERE external_host = ?" );
		$stmt->bind_param( "s", $ip );
		$stmt->execute();
		$result = $stmt->get_result();

		if ( $result->num_rows > 0 ) {
			$row = $result->fetch_assoc();

			return (int) $row['id'];
		}

		return null;
	}

	public function setOption( string $key, string|array $value ): void {
		if ( is_array( $value ) ) {
			$value = serialize( $value );
		}
		if ( $this->getOption( $key ) !== null ) {
			$stmt = $this->db->prepare( "UPDATE options SET `value` = ? WHERE `key` = ?" );
			$stmt->bind_param( "ss", $value, $key );
		} else {
			$stmt = $this->db->prepare( "INSERT INTO options (`key`, `value`) VALUES (?, ?)" );
			$stmt->bind_param( "ss", $key, $value );
		}
		$stmt->execute();
	}

	public function getOption( string $key ) {
		$stmt = $this->db->prepare( "SELECT value FROM options WHERE `key` = ?" );
		$stmt->bind_param( "s", $key );
		$stmt->execute();

		$result = $stmt->get_result();
		if ( $result->num_rows > 0 ) {
			$row = $result->fetch_assoc();
			if ( $this->isSerialized( $row['value'] ) ) {
				return unserialize( $row['value'] );
			}

			return $row['value'];
		}

		return null;
	}

	private function isSerialized( $data, bool $strict = true ): bool {
		// If it isn't a string, it isn't serialized.
		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
		if ( 'N;' === $data ) {
			return true;
		}
		if ( strlen( $data ) < 4 ) {
			return false;
		}
		if ( ':' !== $data[1] ) {
			return false;
		}
		if ( $strict ) {
			$lastc = substr( $data, - 1 );
			if ( ';' !== $lastc && '}' !== $lastc ) {
				return false;
			}
		} else {
			$semicolon = strpos( $data, ';' );
			$brace     = strpos( $data, '}' );
			// Either ; or } must exist.
			if ( false === $semicolon && false === $brace ) {
				return false;
			}
			// But neither must be in the first X characters.
			if ( false !== $semicolon && $semicolon < 3 ) {
				return false;
			}
			if ( false !== $brace && $brace < 4 ) {
				return false;
			}
		}
		$token = $data[0];
		switch ( $token ) {
			case 's':
				if ( $strict ) {
					if ( '"' !== substr( $data, - 2, 1 ) ) {
						return false;
					}
				} else if ( ! str_contains( $data, '"' ) ) {
					return false;
				}
			// Or else fall through.
			case 'a':
			case 'O':
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b':
			case 'i':
			case 'd':
				$end = $strict ? '$' : '';

				return (bool) preg_match( "/^{$token}:[0-9.E+-]+;$end/", $data );
		}

		return false;
	}

	public function getDomainInfo( string $domain ): ?array {
		$stmt = $this->db->prepare( "SELECT * FROM domains WHERE domain = ?" );
		$stmt->bind_param( "s", $domain );
		$stmt->execute();
		$result = $stmt->get_result();
		if ( $result->num_rows > 0 ) {
			return $result->fetch_assoc();
		}

		return null;
	}

	public function setDomain( string $domain, int $serverId ): void {
		$stmt = $this->db->prepare( "INSERT INTO domains (`domain`, `server`) VALUES (?, ?)" );
		$stmt->bind_param( "si", $domain, $serverId );
		$stmt->execute();
	}

	public function getUserInfo( string $email ): ?array {
		$stmt = $this->db->prepare( "SELECT * FROM users WHERE username = ?" );
		$stmt->bind_param( "s", $email );
		$stmt->execute();
		$result = $stmt->get_result();
		if ( $result->num_rows > 0 ) {
			return $result->fetch_assoc();
		}

		return null;
	}

	public function insertOrUpdateUser( string $email, int $domain ): void {
		$stmt = $this->db->prepare( "INSERT INTO users (`username`, `domain`, `last_verified`) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE `domain` = VALUES(`domain`), `last_verified` = VALUES(`last_verified`)" );
		$stmt->bind_param( "si", $email, $domain );
		$stmt->execute();
	}
}