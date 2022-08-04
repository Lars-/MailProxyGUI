<?php
error_reporting( E_ALL );

if ( ! ob_start( 'ob_gzhandler' ) ) {
	ob_start();
}

header( 'Content-Type: text/html; charset=utf-8' );

include( 'lazy_mofo.php' );

try {
	$dbh = new PDO( "mysql:host=db;dbname=database;", 'root', '3h0aZvklAqkZgNmoubOfNb7p7PAID4CQ' );
} catch ( PDOException $e ) {
	die( 'pdo connection error: ' . $e->getMessage() );
}

$lm = new lazy_mofo( $dbh, 'en-us' );
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
<h2>Servers</h2>
<?php
$lm->table                                           = 'servers';
$lm->identity_name                                   = 'id';
$lm->grid_delete_link                                = "";
$lm->form_delete_button                              = '';
$lm->form_input_control['imap_test_extra_variables'] = [ "type" => "prefilledTestData" ];
$lm->form_input_control['name']                      = [ "type" => "text" ];
$lm->form_input_control['external_host']             = [ "type" => "text" ];
$lm->form_input_control['internal_imap_host']        = [ "type" => "text" ];
$lm->form_input_control['internal_imap_port']        = [ "type" => "prefilledImapPort" ];
$lm->form_input_control['internal_smtp_host']        = [ "type" => "text" ];
$lm->form_input_control['internal_smtp_port']        = [ "type" => "prefilledSmtpPort" ];
$lm->rename['internal_imap_host']                    = 'Internal IMAP host IP';
$lm->rename['internal_imap_port']                    = 'Internal IMAP port';
$lm->rename['internal_smtp_host']                    = 'Internal SMTP host IP';
$lm->rename['internal_smtp_port']                    = 'Internal SMTP port';

function prefilledImapPort( $column_name, $value, $command, $called_from ) {
	global $lm;
	$val = $lm->clean_out( $value );
	if ( empty( $val ) ) {
		$val = '143';
	}

	return "<input type='number' name='$column_name' value='$val' min='1' max='65535'>";
}

function prefilledSmtpPort( $column_name, $value, $command, $called_from ) {
	global $lm;
	$val = $lm->clean_out( $value );
	if ( empty( $val ) ) {
		$val = '25';
	}

	return "<input type='number' name='$column_name' value='$val' min='1' max='65535'>";
}

function prefilledTestData( $column_name, $value, $command, $called_from ) {
	global $lm;
	$val = $lm->clean_out( $value );
	if ( empty( $val ) ) {
		$val = '/imap/novalidate-cert/notls';
	}

	return "<input type='text' name='$column_name' value='$val' size='35'>";
}

$lm->run();
?>
</body>
</html>


