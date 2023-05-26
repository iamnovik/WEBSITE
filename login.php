
<?php
function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
}
session_start();
try {
    $connection = new PDO("mysql:host=localhost; dbname=shirts","root","");
} catch (PDOException $e) {
    echo 'Подключение не удалось: ' . $e->getMessage();
    exit();
}
if (isset($_POST['login']) && isset($_POST['password']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
// получаем данные из формы с авторизацией
    $login = $_POST['login'];
    $password = $_POST['password'];
    $user = $connection->prepare("SELECT * FROM users WHERE login ='$login'");
    $user->execute();
    $data = $user->fetch();

//проверка пароля и логина
    if (($user->rowCount() > 0) ) {
        if((md5($password) == $data['password'])){
            $hash = md5(generateCode(10));
            $sth = $connection->prepare("UPDATE `users` SET `hash`='$hash' WHERE login = '$login'");
            $sth->execute();

            $_SESSION['Name'] = $login;
// идем на страницу для авторизованного пользователя
            header("Location: basket.php");
        }

    } else {


        session_unset();
        session_destroy();
        header("Location: admin.php");

    }
}
