<?php

use LJPc\DoH\DNS;
use LJPc\DoH\DNSType;
use LJPc\MailProxyGui\Database;

require __DIR__ . '/vendor/autoload.php';
$db = Database::instance();

$headers = getallheaders();

if ( ! isset( $headers['Auth-Protocol'] ) ) {
	header( 'Auth-Status: No valid protocol specified' );
	exit;
}

$email    = $headers['Auth-User'];
$password = $headers['Auth-Pass'];

if ( empty( $email ) || empty( $password ) ) {
	header( 'Auth-Status: Username and password can not be empty' );
	exit;
}

if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) === false ) {
	header( 'Auth-Status: Not a valid username' );
	exit;
}
$domain     = explode( '@', $email )[1];
$domainInfo = Database::instance()->getDomainInfo( $domain );

$serverId = null;
if ( $domainInfo !== null ) {
	$serverId = $domainInfo['server'];
}
if ( $serverId === null ) {
	$dnsKeys = Database::instance()->getOption( 'dns_keys' );
	if ( ! is_array( $dnsKeys ) ) {
		$dnsKeys = [ 'mail' ];
	}
	foreach ( $dnsKeys as $dnsKey ) {
		$result  = DNS::query( "$dnsKey.$domain", DNSType::A() );
		$answers = $result->getAnswers();

		foreach ( $answers as $answer ) {
			$ip       = $answer->value;
			$serverId = Database::instance()->findServer( $ip );
			if ( $serverId !== null ) {
				break 2;
			}
		}
	}
	if ( $serverId !== null ) {
		Database::instance()->setDomain( $domain, $serverId );
		$domainInfo = Database::instance()->getDomainInfo( $domain );
	}
}

$server = Database::instance()->getServer( $serverId );
if ( ! is_array( $server ) ) {
	header( 'Auth-Status: No valid server found' );
	exit;
}

$userInfo   = Database::instance()->getUserInfo( $email );
$revalidate = false;
if ( ! is_array( $userInfo ) ) {
	$revalidate = true;
} else {
	$lastVerified = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $userInfo['last_verified'] );
	$now          = new DateTimeImmutable();
	$diff         = $now->diff( $lastVerified );
	if ( $diff->days > 7 ) {
		$revalidate = true;
	}
}
if ( $revalidate ) {
	try {
		$imapString = "{" . $server['internal_imap_host'] . ":" . $server['internal_imap_port'] . $server['imap_test_extra_variables'] . '}';
		$mbox       = imap_open( $imapString, $email, $password, OP_READONLY );
		if ( $mbox === false ) {
			header( 'Auth-Status: Invalid credentials' );
			exit;
		}
		imap_close( $mbox );
	} catch ( Exception $e ) {
		header( 'Auth-Status: ' . $e->getMessage() );
		exit;
	}
	Database::instance()->insertOrUpdateUser( $email, $domainInfo['id'] );
}

header( 'Auth-Status: OK' );
if ( $headers['Auth-Protocol'] === 'imap' ) {
	header( 'Auth-Server: ' . $server['internal_imap_host'] );
	header( 'Auth-Port: ' . $server['internal_imap_port'] );
} else {
	header( 'Auth-Server: ' . $server['internal_smtp_host'] );
	header( 'Auth-Port: ' . $server['internal_smtp_port'] );
}