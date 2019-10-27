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
DEFINE('ELASTIC_HOST', 'https://6b9o9l9h74:yqegnzdccv@first-cluster-6104576857.us-east-1.bonsaisearch.net:443');

$globalPrices = [
    "daftari" => 15000,
    "pasport" => 10000,
    "enteghal" => 10000,
    "shenas_item" => 5000,
    "copy" => 1000
];


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


$app->get('/upload_test', function (Request $request, Response $response, $args) use ($twig, $app) {
    \Cloudinary::config(array(
        "cloud_name" => "dr4eclxx1",
        "api_key" => "169382814821652",
        "api_secret" => "Zq7MK4ARDH6lEbuUW2cBok6XB0o",
        "secure" => true
    ));
    $default_upload_options = array('tags' => 'basic_sample');
    $dir = "C:\\Users\\User\\Desktop\\google-images-download-master\\google_images_download\\downloads\\hm3\\";
    $files = scandir($dir);
    foreach ($files as $item) {
        $path = $dir . "" . $item;
        if (!is_dir($path)) {
            echo $path . "<br/>";
            $id = \Cloudinary\Uploader::upload(
                            $path, array_merge(
                                    $default_upload_options, array('public_id' => $item)
                            )
            );
            echo $id . "<br/>";
        }
    }
})->setName('upload_test');



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
    $response->getBody()->write($twig->render('main.twig', ["products" => $list, "user" => getCurrentUser(), "app_name" => APP_NAME, "app_site" => APP_SITE]));
})->setName('main');

$app->get('/register', function (Request $request, Response $response, $args) use ($twig, $app) {
    $referer = urldecode($_SERVER['HTTP_REFERER']);
    if (isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $response->getBody()->write($twig->render('register.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "redirect" => $referer]));
    }
})->setName('register');
$app->get('/login', function (Request $request, Response $response, $args) use ($twig, $app) {
    $referer = urldecode($_SERVER['HTTP_REFERER']);
    if (isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $response->getBody()->write($twig->render('login.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "redirect" => $referer]));
    }
})->setName('login');

$app->get('/newaddress', function (Request $request, Response $response, $args) use ($twig, $app) {
    $response->getBody()->write($twig->render('newaddress.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "user" => getCurrentUser(), "address" => null]));
})->setName('newaddress');

$app->get('/editaddress', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (!isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $id = $request->getQueryParams()["id"];
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM user_address WHERE user_id=? AND address_id=?");
        $stmt->bind_param("ii", getCurrentUser()["id"], $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if (mysqli_num_rows($result) != 0) {
            $stmt = $conn->prepare("SELECT * FROM address WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $addr = [
                "id" => $id,
                "address" => $row["address"],
                "mobile" => $row["mobile"],
                "point" => $row["point"],
                "name" => $row["name"]
            ];
            $response->getBody()->write($twig->render('newaddress.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "user" => getCurrentUser(), "address" => $addr]));
            return;
        }
        $stmt->close();
        $conn->close();
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    }
})->setName('ecitaddress');

$app->get('/logout', function (Request $request, Response $response, $args) use ($twig, $app) {
    session_unset();
    session_destroy();
    $referer = urldecode($_SERVER['HTTP_REFERER']);
    return $response->withRedirect($referer);
})->setName('logout');

$app->post('/dologin', function (Request $request, Response $response, $args) use ($twig, $app) {
    $redirect = $_POST["redirect"];
    if (isLogged()) {
        return $response->withRedirect($redirect);
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
            $stmt->close();
            $conn->close();
            return $response->withRedirect($redirect);
        } else {
            $stmt->close();
            $conn->close();
            $response->getBody()->write($twig->render('login.twig', ["error" => "رایانامه یا رمز اشتباه است", "app_name" => APP_NAME, "app_site" => APP_SITE]));
        }
    }
})->setName('dologin');

$app->post('/save_user', function (Request $request, Response $response, $args) use ($twig, $app) {
    $redirect = $_POST["redirect"];
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
            $last_id = $conn->insert_id;
            setCurrentUser($name, $last_id, $mail, $mobile);
            $stmt->close();
            $conn->close();
            return $response->withRedirect($redirect);
        } else {
            $stmt->close();
            $conn->close();
            $response->getBody()->write($twig->render('register.twig', ["error" => "شماره موبایل یا رایانامه قبلا ثبت شده است", "app_name" => APP_NAME, "app_site" => APP_SITE]));
        }
    }
})->setName('save_user');

$app->post('/save_address', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (!isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $id = (int) ($_POST["id"]);
        $name = $_POST["name"];
        $mobile = $_POST["mobile"];
        $address = $_POST["address"];
        $point = $_POST["point"];
        $conn = getConnection();
        if ($id == -1) {

            $stmt = $conn->prepare("INSERT INTO address(name,mobile,address,point)VALUES(?,?,?,?)");
            $stmt->bind_param("ssss", $name, $mobile, $address, $point);
            $stmt->execute();
            $result = $stmt->get_result();
            $last_id = $conn->insert_id;

            $stmt = $conn->prepare("INSERT INTO user_address(user_id,address_id)VALUES(?,?)");
            $stmt->bind_param("ii", getCurrentUser()["id"], $last_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE address SET name=?,mobile=?,address=?,point=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $mobile, $address, $point, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        }
        $conn->close();
        return $response->withRedirect("تعیین_آدرس");
    }
})->setName('save_address');

$app->get('/remove_address', function (Request $request, Response $response, $args) use ($twig, $app) {

    if (!isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $id = $request->getQueryParams()["id"];
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM user_address WHERE user_id=? AND address_id=?");
        $stmt->bind_param("ii", getCurrentUser()["id"], $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if (mysqli_num_rows($result) != 0) {
            $iid = (int) $id;
            $stmt = $conn->prepare("DELETE FROM address WHERE id=" . $iid);
            $stmt->execute();
        }

        $stmt->close();
        $conn->close();
        $response->getBody()->write(json_encode($id));
        return;
    }
})->setName('remove_address');

$app->get('/profile', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (!isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $response->getBody()->write($twig->render('profile.twig', ["user" => getCurrentUser(), "app_name" => APP_NAME, "app_site" => APP_SITE]));
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
        $stmt->close();
        $conn->close();
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    }
})->setName('update_user');


$app->get('/forget', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $response->getBody()->write($twig->render('forget.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE]));
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
            $stmt->close();
            $conn->close();
            $response->getBody()->write($twig->render('forget.twig', ["error" => "چنین ایمیلی در سیستم ثبت نشده است"]));
        } else {
            $row = $result->fetch_assoc();
            $message = "رمز عبور شما: " + $row["password"];
            sendMail($mail, $message, "بازیابی رمز", $twig);
            $stmt->close();
            $conn->close();
            $response->getBody()->write($twig->render('forget.twig', ["success" => true, "app_name" => APP_NAME, "app_site" => APP_SITE]));
        }
    }
})->setName('profile');


$app->get('/translators', function (Request $request, Response $response, $args) use ($twig, $app) {
    if (!isManagerLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("translators-login"));
    }
    $centerId = getCurrentManager()["id"];
    $center = getElasticCenter($centerId);
    $user = ["name" => $center->manager];
    $response->getBody()->write($twig->render('manage.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "user" => $user]));
})->setName('translators');

$app->get('/translators-login', function (Request $request, Response $response, $args) use ($twig, $app) {
    $referer = urldecode($_SERVER['HTTP_REFERER']);
    $response->getBody()->write($twig->render('mlogin.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "user" => getCurrentUser(), "redirect" => $referer]));
})->setName('translators-login');

$app->post('/domlogin', function (Request $request, Response $response, $args) use ($twig, $app) {
    $redirect = $_POST["redirect"];
    if (isManagerLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("translators"));
    } else {
        $mobile = $_POST["mobile"];
        $password = $_POST["password"];

        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM center WHERE username=? AND password=?");
        $stmt->bind_param("ss", $mobile, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if (mysqli_num_rows($result) != 0) {
            $row = $result->fetch_assoc();
            setCurrentManager($row["id"], $mobile);
            $stmt->close();
            $conn->close();
            return $response->withRedirect($app->getContainer()->get('router')->pathFor("translators"));
        } else {
            $stmt->close();
            $conn->close();
            $response->getBody()->write($twig->render('mlogin.twig', ["error" => "رایانامه یا رمز اشتباه است", "app_name" => APP_NAME, "app_site" => APP_SITE]));
        }
    }
})->setName('domlogin');

$app->post('/centers', function (Request $request, Response $response, $args) use ($twig, $app) {

    if (!isLogged()) {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    } else {
        $point = $_POST["point"];
        $centers = getElasticCenters($point);
//        $response->getBody()->write($centers);
        $response->getBody()->write($twig->render('centers.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "user" => getCurrentUser(), "centers" => $centers]));
        return;
    }
})->setName('centers');


$app->get('/{name}', function(Request $request, Response $response, $args) use ($twig, $app, $globalPrices) {
    $path = trim(urldecode($args["name"]));
    if ($path == "زبان_های_ترجمه_رسمی") {
        $response->getBody()->write($twig->render('lang.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "user" => getCurrentUser()]));
        return;
    }
    if ($path == "تعیین_آدرس") {
        $response->getBody()->write($twig->render('address.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "user" => getCurrentUser(), "addressList" => getUserAddressList()]));
        return;
    }
    if ($path == "لیست_دفاتر_ترجمه_رسمی") {
        $response->getBody()->write($twig->render('centers.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "user" => getCurrentUser(), "globalPrices" => $globalPrices]));
        return;
    }
    if ($path == "پیوست_مدارک_ترجمه_رسمی") {
        $response->getBody()->write($twig->render('upload.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "user" => getCurrentUser()]));
        return;
    }
    if ($path == "انتخاب_مدارک_ترجمه_رسمی") {
        return $response->withRedirect($app->getContainer()->get('router')->pathFor("main"));
    }
    $docType = getDocType($path);
    if ($docType != null) {
        $response->getBody()->write($twig->render('doc.twig', ["app_name" => APP_NAME, "app_site" => APP_SITE, "user" => getCurrentUser(), "doc" => $docType]));
        return;
    }
    $response->getBody()->write($path);
});

$app->run();

function getUserAddressList() {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM address LEFT JOIN user_address ON user_address.user_id=? AND address.id=user_address.address_id");
    $stmt->bind_param("i", getCurrentUser()["id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $list = array();
    while ($row = $result->fetch_assoc()) {
        array_push($list, [ "address" => $row["address"], "mobile" => $row["mobile"], "point" => $row["point"], "name" => $row["name"], "id" => $row["address_id"]]);
    }
    $stmt->close();
    $conn->close();
    return $list;
}

function getDocType($url) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM product WHERE url=?");
    $stmt->bind_param("s", $url);
    $stmt->execute();
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) == 0) {
        return null;
    }
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return ["id" => $row["id"], "title" => $row["title"], "price" => $row["price"], "extra" => $row["extra"], "desc" => $row["desc"], "url" => $row["url"], "category" => $row["category"], "fi" => $row["fi"]];
}

function isLogged() {
    return getCurrentUser() != null;
}

function isManagerLogged() {
    return getCurrentManager() != null;
}

function setCurrentUser($name, $id, $mail, $mobile) {
    $_SESSION["user"] = ["name" => $name, "id" => $id, "mail" => $mail, "mobile" => $mobile];
}

function setCurrentManager($id, $mobile) {
    $_SESSION["manager"] = ["id" => $id, "mobile" => $mobile];
}

function getCurrentUser() {
    if (isset($_SESSION["user"])) {
        return $_SESSION["user"];
    }
    return null;
}

function getCurrentManager() {
    if (isset($_SESSION["manager"])) {
        return $_SESSION["manager"];
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
    $body = $twig->render('mail.twig', ["title" => $title, "message" => $message, "app_site" => APP_SITE, "app_name" => APP_NAME, "app_site" => APP_SITE]);
    mail($to, $title, $body, $headers);
}

function getElasticCenter($id) {
    $header = array(
        "content-type: application/json",
        "Authorization: Basic NmI5bzlsOWg3NDp5cWVnbnpkY2N2"
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_URL, ELASTIC_HOST . "/centers/_doc/" . $id);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    $res = json_decode($res);
    $res = $res->_source;
    $res->id = $id;
    return $res;
}

function getElasticCenters($point) {
    $header = array(
        "content-type: application/json",
        "Authorization: Basic NmI5bzlsOWg3NDp5cWVnbnpkY2N2"
    );

    $param = '
        {
            "query" : {
                        "geo_distance":{
                                "distance":"200km",
                                "loc":"' . $point . '"
                        }
            },
                        "sort" : [
                                {
                                        "_geo_distance" : {
                                                "loc" : "' . $point . '",
                                                "order" : "asc",
                                                "unit" : "km",
                                                "mode" : "min",
                                                "distance_type" : "arc",
                                                "ignore_unmapped": true
                                        }
                                }
                        ]
        }';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_URL, ELASTIC_HOST . "/centers/_search");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
    $res = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    $res = json_decode($res);
    $res = $res->hits;
    $res = $res->hits;
    $array = [];
    foreach ($res as $item) {
        $correct = $item->_source;
        $correct->sort =  getDistance($item->sort[0]);
        //$correct["sort"] =  getDistance( $item->sort);
        array_push($array, $correct);
    }
    return $array;
}

function getDistance($sort){
    if($sort<1.0){
        return round($sort*1000)." متر";
    }
    else{
        return sprintf("%.2f", $sort)." کیلومتر";
    }
}