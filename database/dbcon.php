<?php
	$root = $_SERVER["DOCUMENT_ROOT"];
	require_once $root . '/vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable($root);
	$dotenv->safeLoad();

	# get db connection
	$host = $_ENV['DB_HOST'];
	$port = $_ENV['DB_PORT'];
	$dbname = $_ENV['DB_DATABASE'];
	$username = $_ENV['DB_USERNAME'];
	$password = $_ENV['DB_PASSWORD'];

	try{
		$conn = new mysqli($host, $username, $password, $dbname, $port);
	}catch(Exception $e){
        echo json_encode(["status" => 500,"message" => $e->getMessage()]) ;
        exit();
	}
?>