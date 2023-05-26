<?php

//Запуск сессий;

session_start();
if ((isset($_SESSION['Name']))){
    header('HTTP/1.0 403 Forbidden');
    exit;
}
//если пользователь не авторизован

//Выводим саму страницу для авторизованных пользователей

$title_page = "Severe Consequences";
$style_file = "css/reg.css";
$video_path = "Images/videoback.MP4";

//header
$link_main = "index.php";
$link_about = "about.php";
$link_contact = "contact.php";
$link_login = "admin.php";
//footer
$footer_text = "© 2023 Все права защищены.";
$main = file_get_contents("templates/admin.html");
$main= str_replace(
    '{title_page}', $title_page, $main);
$main= str_replace(
    '{style_file}', $style_file, $main);



$header = file_get_contents('templates/header.html');
$header = str_replace('{main}',$link_main,$header);
$header = str_replace('{about}',$link_about,$header);
$header = str_replace('{contact}',$link_contact,$header);
$header = str_replace('{login}',$link_login,$header);
$header = str_replace('{link}',"Вход",$header);



$main= str_replace(
    '{header}', $header, $main);


$footer = file_get_contents('templates/footer.html');


$footer = str_replace('{footer_info}', $footer_text,$footer);

$main = str_replace(
    '{footer}', $footer, $main);

print $main;

