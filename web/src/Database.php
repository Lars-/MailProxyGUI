<?php

namespace LJPc\MailProxyGui;

use SQLite3;

class Database {
	private SQLite3 $db;

	public function __construct() {
		$newDb = false;
		if ( ! file_exists( __DIR__ . '/../data/db.sqlite' ) ) {
			$newDb = true;
		}
		$this->db = new SQLite3( __DIR__ . '/../data/db.sqlite' );

		if ( $newDb ) {
			$this->initializeDB();
		}
	}

	private function initializeDB(): void {
		$this->db->exec( file_get_contents( __DIR__ . '/../migrations/servers.sql' ) );
		$this->db->exec( file_get_contents( __DIR__ . '/../migrations/users.sql' ) );

	}
}