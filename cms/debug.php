<?php

use LJPc\DoH\DNS;
use LJPc\DoH\DNSType;
use LJPc\MailProxyGui\Database;

function debug( $email, $password ) {
	if ( empty( $email ) || empty( $password ) ) {
		echo '❌ Username and password can not be empty';

		return;
	}

	if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) === false ) {
		echo '❌ Not a valid username';

		return;
	}

	require __DIR__ . '/vendor/autoload.php';
	$domain = explode( '@', $email )[1];

	echo "✅ Domain extracted from username: $domain\n\n";

	$serverId = null;
	if ( $serverId === null ) {
		$dnsKeys = Database::instance()->getOption( 'dns_keys' );
		if ( ! is_array( $dnsKeys ) ) {
			$dnsKeys = [ 'mail' ];
		}
		echo "DNS keys to check: " . implode( ", ", $dnsKeys ) . "\n";


		foreach ( $dnsKeys as $dnsKey ) {
			$result  = DNS::query( "$dnsKey.$domain", DNSType::A() );
			$answers = $result->getAnswers();

			foreach ( $answers as $answer ) {
				$ip = $answer->value;
				echo "Value for $dnsKey.$domain: $ip\n";
				$serverId = Database::instance()->findServer( $ip );
				if ( $serverId !== null ) {
					echo "✅ Server found!\n";
					break 2;
				}
			}
		}
	}

	$server = Database::instance()->getServer( $serverId );
	if ( ! is_array( $server ) ) {
		echo "❌ No valid server found, probably something wrong with the DNS\n";

		return;
	}

	try {
		$imapString = "{" . $server['internal_imap_host'] . ":" . $server['internal_imap_port'] . $server['imap_test_extra_variables'] . '}';
		echo "\nConnecting to IMAP: $imapString with specified email and password\n";
		$mbox = imap_open( $imapString, $email, $password, OP_READONLY );
		if ( $mbox === false ) {
			echo "❌ Invalid credentials!\n";

			return;
		}
		imap_close( $mbox );
	} catch ( Exception $e ) {
		echo "❌ Error: " . $e->getMessage() . "\n";

		return;
	}
    echo "✅ Successfully connected to IMAP server\n";

	echo "\n✅ Everything should work\n";
	echo "The proxy will use these internal settings to connect:\n";
	echo "IMAP: " . $server['internal_imap_host'] . ':' . $server['internal_imap_port'] . "\n";
	echo "SMTP: " . $server['internal_smtp_host'] . ':' . $server['internal_smtp_port'] . "\n";
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <link rel='stylesheet' type='text/css' href='style.css'>
    <meta name='robots' content='noindex,nofollow'>
</head>
<body>
<?php echo file_get_contents( __DIR__ . '/instructions.html' ); ?>
<h2>Debug</h2>
<form method="post" action="">
    <input type="email" placeholder="Email" name="email">
    <input type="password" placeholder="Password" name="password">

    <input type='submit' value='Run debug'>
</form>
<?php if ( isset( $_POST['email'] ) && isset( $_POST['password'] ) ) {
	$email    = $_POST['email'];
	$password = $_POST['password'];
	?>
    <pre><?php debug( $email, $password ); ?></pre>
<?php } ?>
</body>
</html>