<?php
session_start();
require_once 'module/db.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: register.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$author_id = isset($_GET['author_id']) ? (int)$_GET['author_id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($author_id == $user_id) {
    header('Location: profile.php?id=' . $author_id);
    exit();
}

if ($action == 'follow') {
    $query_check = "SELECT * FROM follows WHERE author_id = $author_id AND follower_id = $user_id";
    $res_check = mysqli_query($link, $query_check);

    if (mysqli_num_rows($res_check) == 0) {
        $query_follow = "INSERT INTO follows (author_id, follower_id, date) VALUES ($author_id, $user_id, NOW())";
        $res_follow = mysqli_query($link, $query_follow);
    }
} elseif ($action == 'unfollow') {
    $query_unfollow = "DELETE FROM follows WHERE author_id = $author_id AND follower_id = $user_id";
    $res_unfollow = mysqli_query($link, $query_unfollow);
}

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'profile.php?id=' . $author_id;
header('Location: ' . $redirect);
exit();
?>