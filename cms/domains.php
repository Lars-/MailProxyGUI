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
<h2>Domains</h2>
<?php
$lm->table            = 'domains';
$lm->identity_name    = 'id';
$lm->grid_delete_link = "";
$lm->grid_sql         = "select d.id,d.domain, s.name as 'server', d.id from domains d join servers s on s.id=d.server;";
$lm->run();
?>
</body>
</html>


