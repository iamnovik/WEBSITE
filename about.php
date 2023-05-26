<?php
session_start();
date_default_timezone_set("Europe/Minsk"); // устанавливаем часовую зону

use PHPMailer\PHPMailer\PHPMailer;
require 'C:/wamp64/mailer/PHPMailer-master/src/Exception.php';
require 'C:/wamp64/mailer/PHPMailer-master/src/PHPMailer.php';
require 'C:/wamp64/mailer/PHPMailer-master/src/SMTP.php';

//main
$title_page = "Severe Shop";
$style_file = "css/style.css";
$video_path = "Images/videoback.MP4";

$photos = [
  ['image' => "Pages/machine1.jpg", 'alt' => "1"],
    ['image' => "Pages/machine2.jpg", 'alt' => "2"],
    ['image' => "Pages/machine3.jpg", 'alt' => "3"],
    ['image' => "Pages/praise1.jpg", 'alt' => "4"],
    ['image' => "Pages/praise2.jpg", 'alt' => "5"],
    ['image' => "Pages/praise3.jpg", 'alt' => "6"],
    ['image' => "Pages/rocket1.jpg", 'alt' => "7"],
    ['image' => "Pages/rocket2.jpg", 'alt' => "8"],
    ['image' => "Pages/rocket3.jpg", 'alt' => "9"],
];
try {
    $connection = new PDO("mysql:host=localhost; dbname=shirts","root","");
} catch (PDOException $e) {
    echo 'Подключение не удалось: ' . $e->getMessage();
    exit();
}

// Запрос списка новостей из базы данных
$prices = $connection->prepare('SELECT * FROM shitt_prices ');
$prices->execute();
//$db_prices = $prices->fetchAll();
$links = $connection->prepare('SELECT * FROM shirt_links ');
$links->execute();
//$db_links = $links->fetchAll();
$names = $connection->prepare('SELECT * FROM shirt_name ');
$names->execute();
$db_names = $names->fetchAll();



//header
$link_main = "index.php";
$link_about = "about.php";
$link_contact = "contact.php";
$link_login = "admin.php";


//footer
$footer_text = "© 2023 Все права защищены.";


$main = file_get_contents("Pages/about.html");
$video = file_get_contents("templates/videoback.html");
$video = str_replace('{videopath}', $video_path, $video);
$main = str_replace('{video}',$video,$main);

$main= str_replace(
    '{title_page}', $title_page, $main);
$main= str_replace(
    '{style_file}', $style_file, $main);
$gallery_html = "";

$i = 1;
foreach ($db_names as $item){
    $row_links = $links->fetch();
    $row_price = $prices->fetch();
    $gallery_item = file_get_contents("templates/galleryitem.html");
    $gallery_item = str_replace('{image}', $row_links['src'], $gallery_item);
    $gallery_item = str_replace('{alt}', $item['id'], $gallery_item);
    $gallery_item = str_replace('{link}', $row_links['inst_link'], $gallery_item);
    $price = $row_price['belrub'].("BY ").$row_price['dollar'].("$ ").$row_price['rus_rub'].("RUB");
    $gallery_item = str_replace('{price}', $price, $gallery_item);
    $gallery_item = str_replace('{name}', $item['name'], $gallery_item);
    $gallery_item = str_replace('{id}',"button".$i,$gallery_item);
    $gallery_item = str_replace('{text}',"Добавить в корзину",$gallery_item);
    $i = $i + 1;
    $gallery_html = $gallery_html.$gallery_item;

}


$main = str_replace('{videopath}', $video_path, $main);
$main = str_replace('{gallery}',$gallery_html,$main);
$gallery_html ="";
$gallery_html_temp ="";
$x = 1;
foreach ($photos as $item){
    $gallery_item = file_get_contents("templates/photoitem.html");
    $gallery_item = str_replace('{image}', $item['image'], $gallery_item);
    $gallery_item = str_replace('{alt}', $item['alt'], $gallery_item);
    $gallery_html_temp = $gallery_html_temp.$gallery_item;
    if($x % 3 == 0){
        $temp = '<div class="photogall">'.$gallery_html_temp;
        $temp = $temp.'</div>';
        $gallery_html .= $temp;
        $gallery_html_temp ="";
    }
    $x++;
}
$main = str_replace('{photos}',$gallery_html,$main);



$header = file_get_contents('templates/header.html');
$header = str_replace('{main}',$link_main,$header);
$header = str_replace('{about}',$link_about,$header);
$header = str_replace('{contact}',$link_contact,$header);
if (!(isset($_SESSION['Name']))){
    $header = str_replace('{login}',$link_login,$header);
    $header = str_replace('{link}',"Вход",$header);
}

else{
    $header = str_replace('{login}',"basket.php",$header);
    $header = str_replace('{link}',$_SESSION['Name'],$header);

    $log = $_SESSION['Name'];
    $orders = $connection->prepare("SELECT * FROM orders WHERE login = '$log'");
    $orders->execute();
    $row = $orders->fetch();

    if (isset($_POST['button1'])) {
        $count = $row['rocket_count'];
        $count++;
        $sth = $connection->prepare("UPDATE `orders` SET `rocket_count`='$count' WHERE login = '$log'");
        $sth->execute();

    }
    if (isset($_POST['button2'])) {
        $count = $row['praise_count'];
        $count++;
        $sth = $connection->prepare("UPDATE `orders` SET `praise_count`='$count' WHERE login = '$log'");
        $sth->execute();
    }
    if (isset($_POST['button3'])) {
        $count = $row['drugs_count'];
        $count++;
        $sth = $connection->prepare("UPDATE `orders` SET `drugs_count`='$count' WHERE login = '$log'");
        $sth->execute();
    }
}


$main= str_replace(
    '{header}', $header, $main);


$footer = file_get_contents('templates/footer.html');


$footer = str_replace('{footer_info}', $footer_text,$footer);

$main = str_replace(
    '{footer}', $footer, $main);


$filename = 'reviews.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {


    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';
    $mail_rev = isset($_POST['mail'])?$_POST['mail']:'';
    $mail = new PHPMailer();

// Настройка параметров подключения к SMTP-серверу
    $mail->isSMTP();
    $mail->Host = 'smtp.yandex.ru';
    $mail->SMTPAuth = true;
    $mail->Username = 'novikvlad10';
    $mail->Password = 'dwnmmwiebnzuwemo';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->CharSet = "utf-8";

// Настройка параметров email-сообщения
    $mail->setFrom('novikvlad10@yandex.ru', 'SevereShop');
    $mail->addAddress(isset($_POST['mail'])?$_POST['mail']:'');
    $mail->Subject = 'Заказ';
    $mail->Body = 'Спасибо за заказ';
    //if (!$mail->send()) {
    //    echo 'Error: ' . $mail->ErrorInfo;
    //} else {
    //    echo 'Message sent!';
    //}
    $mail->clearAddresses();
    $mail->clearCustomHeaders();
    $mail->clearAttachments();
    $mail->clearReplyTos();
    if ($name && $message && $mail_rev) {
        $reviews = preg_replace('/https?:\/\/(?!bsuir\.by)[^\s]+/', '#Внешние ссылки запрещены#', $message);
        $res = $connection->prepare("SELECT name FROM reviewers WHERE name = '$name'");
        $res->execute();
        //$temp = $connection->prepare("SELECT * FROM comments ");


        if ($res->rowCount() > 0 ){
            $id = $connection->prepare("SELECT id FROM reviewers WHERE name ='$name'");
            $id->execute();
            $id = $id->fetch()['id'];
            //$sql1 = "INSERT INTO `reviewers`(`id`, `name`, `mail`) VALUES ('$id','$name','$mail_rev')";
            $sql2 = "INSERT INTO `comments`(`id`, `comment`) VALUES ('$id','$message')";
        }else{
            $sql1 = "INSERT INTO `reviewers`( `name`, `mail`) VALUES ('$name','$mail_rev')";
            $sql2 = "INSERT INTO `comments`( `comment`) VALUES ('$message')";
            $connection->exec($sql1);

        }

        $connection->exec($sql2);


    }

}


$res = $connection->prepare("SELECT * FROM reviewers");
$res->execute();
//$res->fetchAll();
$str = "";
foreach ($res as $item){
    $id = $item['id'];

    $temp = $connection->prepare("SELECT comment FROM comments WHERE id = '$id'");
    $temp->execute();
    $str = $str.("<p>").$item['name'].("</p>");
    foreach ($temp as $value){
        $str = $str.$value['comment'].(", ");
    }
    $str = $str.("<br>");
}


$main = str_replace('{reviewers}',$str,$main);



print $main;
