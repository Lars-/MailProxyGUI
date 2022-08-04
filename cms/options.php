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
<h2>Options</h2>
<?php
$lm->table              = 'options';
$lm->identity_name      = 'id';
$lm->grid_delete_link   = "";
$lm->grid_add_link      = '';
$lm->form_delete_button = '';

$lm->run();
?>
</body>
</html>


