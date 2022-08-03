<?php
$headers = getallheaders();

$serverMap = [

];

if ( ! isset( $headers['Auth-Protocol'] ) ) {
	header( 'Auth-Status: Klopt niet' );
	header( 'Auth-Wait: 3' );
	exit;
}

$email    = $headers['Auth-User'];
$password = $headers['Auth-Pass'];

if ( empty( $email ) || empty( $password ) ) {
	header( 'Auth-Status: Ongeldige gegevens' );
	exit;
}

if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) === false ) {
	header( 'Auth-Status: Geen geldig e-mailadres' );
	exit;
}
$domain = explode( '@', $email )[1];

$hostInfo = false;
if ( $hostInfo === false ) {
	$ipAddress = dns_get_record( 'mail.' . $domain, DNS_A );
	if ( count( $ipAddress ) > 0 ) {
		$hostInfo = $ipAddress[0]['ip'];
		if ( ! in_array( $hostInfo, array_keys( $serverMap ) ) ) {
			header( 'Auth-Status: Geen server gevonden' );
			exit;
		}
		$hostInfo = $serverMap[ $hostInfo ];
	} else {
		header( 'Auth-Status: Geen server gevonden' );
		exit;
	}
}

$emailKnown = false;
if ( $emailKnown === false ) {
	try {
		$mbox = imap_open( "{" . $hostInfo . ":993/imap/ssl/novalidate-cert/notls}", $email, $password, OP_READONLY, 1 );
		if ( $mbox === false ) {
			header( 'Auth-Status: Ongeldige gegevens' );
			exit;
		}
		imap_close( $mbox );

	} catch ( Exception $e ) {
		header( 'Auth-Status: ' . $e->getMessage() );
		exit;
	}
}

header( 'Auth-Status: OK' );
header( 'Auth-Server: ' . $hostInfo );
if ( $headers['Auth-Protocol'] === 'imap' ) {
	header( 'Auth-Port: 143' );
} else {
	header( 'Auth-Port: 25' );
}