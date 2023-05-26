<?php
session_start();
//main
$title_page = "Severe Ideology";
$style_file = "css/style.css";
$video_path = "Images/videoback.MP4";


$gallery = [
    ['image' => "Images/message.jpg", 'alt' => "1"],
    ['image' => "Images/director.jpg", 'alt' => "2"],
    ['image' => "Images/back.jpg", 'alt' => "3"],
];
$photos = [
    ['image' => "panks/nodrugs.jpg", 'alt' => "1"],
    ['image' => "panks/punkqueen.jpg", 'alt' => "2"],
    ['image' => "panks/2day.jpg", 'alt' => "3"],
    ['image' => "panks/12.jpg", 'alt' => "4"],
    ['image' => "panks/donothing.jpg", 'alt' => "5"],
    ['image' => "panks/moose.jpg", 'alt' => "6"],
    ['image' => "panks/idk.jpg", 'alt' => "7"],
    ['image' => "panks/racism.jpg", 'alt' => "8"],
    ['image' => "panks/selfup.jpg", 'alt' => "9"],
];



//header
$link_main = "index.php";
$link_about = "about.php";
$link_contact = "contact.php";
$link_login = "admin.php";
//footer
$footer_text = "© 2023 Все права защищены.";


$main = file_get_contents("Pages/contact.html");
$video = file_get_contents("templates/videoback.html");
$video = str_replace('{videopath}', $video_path, $video);
$main = str_replace('{video}',$video,$main);


$main= str_replace(
    '{title_page}', $title_page, $main);
$main= str_replace(
    '{style_file}', $style_file, $main);
$main = str_replace('{videopath}', $video_path, $main);
$gallery_html ="";
foreach ($gallery as $item){
    $gallery_item = file_get_contents("templates/gallery_idea.html");
    $gallery_item = str_replace('{image}', $item['image'], $gallery_item);
    $gallery_item = str_replace('{alt}', $item['alt'], $gallery_item);
    $gallery_html = $gallery_html.$gallery_item;
}
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
}


$main= str_replace(
    '{header}', $header, $main);

$footer = file_get_contents('templates/footer.html');


$footer = str_replace('{footer_info}', $footer_text,$footer);

$main = str_replace(
    '{footer}', $footer, $main);

print $main;
