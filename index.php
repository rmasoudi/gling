<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

DEFINE('HOST', '127.0.0.1');
DEFINE('USER', 'root');
DEFINE('PASSWORD', 'a');
DEFINE('DB_NAME', 'dling');
DEFINE('APP_NAME', 'جیلینگ');
DEFINE('APP_SITE', 'gling.ir');
session_start();


$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);
$config = [
    'settings' => [
        'displayErrorDetails' => true,
        'logger' => [
            'name' => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
];
$app = new \Slim\App($config);
$app->get('/', function (Request $request, Response $response, $args) use ($twig, $app) {
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
    $response->getBody()->write($twig->render('main.twig', ["products" => $list, "user" => getCurrentUser()]));
})->setName('main');

$app->get('/register', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $response->getBody()->write($twig->render('register.twig', []));
    }
})->setName('register');
$app->get('/login', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $response->getBody()->write($twig->render('login.twig', []));
    }
})->setName('login');
$app->get('/logout', function (Request $request, Response $response, $args) use ($twig, $app) {
    session_unset();
    session_destroy();
    return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
})->setName('logout');

$app->post('/dologin', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $mail = $_POST["mail"];
        $password = $_POST["password"];

        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM user WHERE mail=? AND password=?");
        $stmt->bind_param("ss", $mail, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if (mysqli_num_rows($result) != 0) {
            $row = $result->fetch_assoc();
            setCurrentUser($row["name"], $row["id"], $row["mail"], $row["mobile"]);
            return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
        } else {
            $response->getBody()->write($twig->render('login.twig', ["error" => "رایانامه یا رمز اشتباه است"]));
        }
    }
})->setName('dologin');

$app->post('/save_user', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
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
            $stmt = $conn->prepare("INSERT INTO user(name,mail,mobile,password)VALUES(?,?,?,?)");
            $stmt->bind_param("ssss", $name, $mail, $mobile, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            $response->getBody()->write($twig->render('register.twig', ["success" => true]));
        } else {
            $response->getBody()->write($twig->render('register.twig', ["error" => "شماره موبایل یا رایانامه قبلا ثبت شده است"]));
        }
    }
})->setName('save_user');

$app->get('/profile', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (!isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $response->getBody()->write($twig->render('profile.twig', ["user" => getCurrentUser()]));
    }
})->setName('profile');

$app->post('/update_user', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (!isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $name = $_POST["name"];
        $password = $_POST["password"];
        $mobile = $_POST["mobile"];
        $mail = $_POST["mail"];
        $id = getCurrentUser()["id"];

        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE user SET name=?,password=?,mobile=?,mail=? WHERE id=?");
        $stmt->bind_param("sssss", $name, $password, $mobile, $mail, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        setCurrentUser($name, $id, $mail, $mobile);
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    }
})->setName('update_user');


$app->get('/forget', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $response->getBody()->write($twig->render('forget.twig', []));
    }
})->setName('forget');

$app->post('/sendpass', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $mail = $_POST["mail"];
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM user WHERE mail=?");
        $stmt->bind_param("s", $mail);
        $stmt->execute();
        $result = $stmt->get_result();
        if (mysqli_num_rows($result) == 0) {
            $response->getBody()->write($twig->render('forget.twig', ["error" => "چنین ایمیلی در سیستم ثبت نشده است"]));
        } else {
            $row = $result->fetch_assoc();
            $message = "رمز عبور شما: " + $row["password"];
            sendMail($mail, $message, "بازیابی رمز", $twig);
            $response->getBody()->write($twig->render('forget.twig', ["success" => true]));
        }
    }
})->setName('profile');

$app->get('/{name}', function(Request $request, Response $response, $args) use ($twig, $app) {
    $response->getBody()->write($args["name"]);
});
$app->run();

function isLogged() {
    return getCurrentUser() != null;
}

function setCurrentUser($name, $id, $mail, $mobile) {
    $_SESSION["user"] = ["name" => $name, "id" => $id, "mail" => $mail, "mobile" => $mobile];
}

function getCurrentUser() {
    if (isset($_SESSION["user"])) {
        return $_SESSION["user"];
    }
    return null;
}

function getConnection() {
    $conn = new mysqli(HOST, USER, PASSWORD, DB_NAME);
    $conn->set_charset("utf8");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function sendMail($to, $message, $title, $twig) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <info@' + APP_SITE + '>' . "\r\n";
    $body = $twig->render('mail.twig', ["title" => $title, "message" => $message, "app_site" => APP_SITE, "app_name" => APP_NAME]);
    mail($to, $title, $body, $headers);
}
