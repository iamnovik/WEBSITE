<?php

//Запуск сессий;

session_start();
//если пользователь не авторизован

if (!(isset($_SESSION['Name'])))
{
//идем на страницу авторизации
    header('HTTP/1.0 403 Forbidden');
    exit;
};
//Выводим саму страницу для авторизованных пользователей

$title_page = "Severe Consequences";
$style_file = "css/style.css";
$video_path = "Images/videoback.MP4";

//header
$link_main = "index.php";
$link_about = "about.php";
$link_contact = "contact.php";
$link_login = "admin.php";
//footer
$footer_text = "© 2023 Все права защищены.";
$main = file_get_contents("templates/basket.html");
$main= str_replace(
    '{title_page}', $title_page, $main);
$main= str_replace(
    '{style_file}', $style_file, $main);



$header = file_get_contents('templates/header.html');
$header = str_replace('{main}',$link_main,$header);
$header = str_replace('{about}',$link_about,$header);
$header = str_replace('{contact}',$link_contact,$header);

    $header = str_replace('{login}',"basket.php",$header);
    $header = str_replace('{link}',$_SESSION['Name'],$header);



$main= str_replace(
    '{header}', $header, $main);


$footer = file_get_contents('templates/footer.html');


$footer = str_replace('{footer_info}', $footer_text,$footer);



$login = $_SESSION['Name'];
try {
    $connection = new PDO("mysql:host=localhost; dbname=shirts","root","");
} catch (PDOException $e) {
    echo 'Подключение не удалось: ' . $e->getMessage();
    exit();
}
$prices = $connection->prepare('SELECT * FROM shitt_prices ');
$prices->execute();
//$db_prices = $prices->fetchAll();
$links = $connection->prepare('SELECT * FROM shirt_links ');
$links->execute();
//$db_links = $links->fetchAll();
$names = $connection->prepare('SELECT * FROM shirt_name ');
$names->execute();
$db_names = $names->fetchAll();
$db = $connection->prepare("SELECT * FROM orders WHERE login = '$login'");
$db->execute();
$row = $db->fetch();
$i = 0;
$gallery_html ="";
foreach ($db_names as $item){
    $row_links = $links->fetch();
    $row_price = $prices->fetch();
    $gallery_item = file_get_contents("templates/galleryitem.html");
    $gallery_item = str_replace('{image}', $row_links['src'], $gallery_item);
    $gallery_item = str_replace('{alt}', $item['id'], $gallery_item);
    $gallery_item = str_replace('{link}', $row_links['inst_link'], $gallery_item);
    $gallery_item = str_replace('{price}', $row[$i+1], $gallery_item);
    $gallery_item = str_replace('{name}', $item['name'], $gallery_item);
    $gallery_item = str_replace('{id}',"delete".$i,$gallery_item);
    $gallery_item = str_replace('{text}',"Очистить заказ",$gallery_item);
    $i = $i + 1;
    $gallery_html = $gallery_html.$gallery_item;

}
$main = str_replace('{orders}',$gallery_html,$main);
if (isset($_POST['delete0'])) {
    echo("delete");
    $sth = $connection->prepare("UPDATE `orders` SET `rocket_count`=0 WHERE login = '$login'");
    $sth->execute();

}
if (isset($_POST['delete1'])) {

    $sth = $connection->prepare("UPDATE `orders` SET `praise_count`=0 WHERE login = '$login'");
    $sth->execute();
}
if (isset($_POST['delete2'])) {

    $sth = $connection->prepare("UPDATE `orders` SET `drugs_count`=0 WHERE login = '$login'");
    $sth->execute();
}
$main = str_replace(
    '{footer}', $footer, $main);
if (isset($_POST['out'])){
    session_unset();
    session_destroy();
    header("Location: index.php");

}
print $main;