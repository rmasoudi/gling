<?php

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

DEFINE('HOST', '127.0.0.1');
DEFINE('USER', 'root');
DEFINE('PASSWORD', 'a');
DEFINE('DB_NAME', 'dling');

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

$path = ltrim($_SERVER['REQUEST_URI'], '/');
$elements = explode('/', $path);
array_shift($elements);

if (count($elements) == 1) {
    $path = urldecode($elements[0]);
    if ($path == "") {
        gotoMainPage($twig);
    } else if ($path == "ثبت_نام") {
        echo $twig->render('register.twig');
    } else if ($path == "register") {
        register();
    } else {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM product WHERE url=?");
        $stmt->bind_param("s", $path);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        #echo $twig->render('product.twig',["id"=>$row['id'],"title"=>$row['title'] );
        $stmt->close();
        $conn->close();
        showProduct(urldecode($path), $conn);
    }
} else {
    header('HTTP/1.1 404 Not Found');
    echo "not found";
}

function register($twig) {
    $name = $_POST["name"];
    $mail = $_POST["mail"];
    $password = $_POST["password"];
    $mobile = $_POST["mobile"];

    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM user WHERE mail=? OR mobile=?");
    $stmt->bind_param("ss", $mail, $mobile);
    $stmt->execute();
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) == 0) {
        $conn->prepare("INSERT INTO user(name,mail,mobile,password)VALUES(?,?,?,?)");
        $stmt->bind_param("ssss", $name, $mail, $mobile, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            gotoMainPage($twig);
        } else {
            
        }
    } else {
        echo json_encode($result);
    }
}

function getConnection() {
    $conn = new mysqli(HOST, USER, PASSWORD, DB_NAME);
    $conn->set_charset("utf8");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function gotoMainPage($twig) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM product");
    $stmt->execute();
    $result = $stmt->get_result();
    $list = array();
    while ($row = $result->fetch_assoc()) {
        array_push($list, [ "category" => $row["category"], "url" => $row["url"], "title" => $row["title"]]);
    }
    $stmt->close();
    $conn->close();
    echo $twig->render('main.twig', ["products" => $list]);
}
