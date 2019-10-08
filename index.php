<?php

require __DIR__ . '/vendor/autoload.php';
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$mysql_host     = '127.0.0.1'; 
$mysql_user     = 'root';          
$mysql_pass     = 'a';          
$mysql_database = 'dling';

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

$path = ltrim($_SERVER['REQUEST_URI'], '/'); 
$elements = explode('/', $path);                
array_shift($elements);

if(count($elements) == 1){
	$path=$elements[0];
	$conn = new mysqli($mysql_host, $mysql_user, $mysql_pass,$mysql_database);
	$conn->set_charset("utf8");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}	
	if($path==""){
		
		$stmt = $conn->prepare( "SELECT * FROM product");
		$stmt->execute();
		$result = $stmt->get_result();
		$list=array();
		while ($row = $result->fetch_assoc()) {
			array_push($list,[ "category"=>$row["category"],"url"=>$row["url"],"title"=>$row["title"] ]);
		}
		$stmt->close();	
		$conn->close();	
		echo $twig->render('main.twig',["products"=>$list] );
	}
	else{
		$param=urldecode($path);
		$stmt = $conn->prepare( "SELECT * FROM product WHERE url='{$param}'");
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		#echo $twig->render('product.twig',["id"=>$row['id'],"title"=>$row['title'] );
		$stmt->close();
		$conn->close();
		showProduct(urldecode($path),$conn);
	}
	
}
else{
			header('HTTP/1.1 404 Not Found');
			echo "not found";
}

