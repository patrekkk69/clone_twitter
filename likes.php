<?php
session_start();
require_once 'module/db.php';

$user_id = $_SESSION['user']['id'];
$posts_id = isset($_GET['posts_id']) ? (int)$_GET['posts_id'] : 0;

if(!$posts_id) {
    header('Location: index.php');
    exit();
}

$query_check = "SELECT * FROM likes WHERE user_id = $user_id AND posts_id = $posts_id";
$res_check = mysqli_query($link, $query_check);

if (mysqli_num_rows($res_check) > 0) {
    $query_delete = "DELETE FROM likes WHERE user_id = $user_id AND posts_id = $posts_id";
    $res_delete = mysqli_query($link, $query_delete);
} else {
    $query_insert = "INSERT INTO likes (user_id, posts_id, date) VALUES ($user_id, $posts_id, CURDATE())";
    $res_insert = mysqli_query($link, $query_insert);
}

header('Location: index.php');
exit();
?>