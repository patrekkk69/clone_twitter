<?
session_start();
include ('template/default/head.php');
include ('template/default/header.php');
require_once ('module/db.php');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($link, $search);
    $query_user = "SELECT * FROM users WHERE login LIKE '%$search_safe%'";
} else {
    $query_user = "SELECT * FROM users";
}
$res_user = mysqli_query($link, $query_user);
?>

<main class="index">
    <div class="search-searcher">
        <div class="searcher">
            <i class="fa fa-search" aria-hidden="true"></i>
            <form method="GET" action="">
                <input name="search" class="input-searcher" type="search" placeholder="Поиск">
            </form>
        </div>
    </div>

    <div class="recommend-search">
        <?php while($user = mysqli_fetch_assoc($res_user)):
            $author_id = (int)$user['id'];
            $follower_id = (int)$_SESSION['user']['id'];
            $query_follow = "SELECT * FROM follows WHERE author_id = $author_id AND follower_id = {$_SESSION['user']['id']}";
            $res_follow = mysqli_query($link, $query_follow);
            $is_follow = mysqli_num_rows($res_follow) > 0;
        ?>
        <div class="recommend-user-search">

            <a href="profile.php?id=<?php echo $user['id']; ?>">
                <div class="avatar">
                    <img src="/<?php echo $user['avatar']; ?>" alt="ava">
                </div>
            </a>

            <div class="search-user-info">
                <h4><?php echo htmlspecialchars($user['login']); ?></h4>
                <p>@<?php echo htmlspecialchars($user['login']); ?></p>

                <div class="search-description">
                    <p><?php echo htmlspecialchars($user['bio']); ?></p>
                </div>
            </div>

            <?php if ($user['id'] == $_SESSION['user']['id']): ?>
                <button class="button-search-we" disabled>
                    Это вы
                </button>
            <?php else: ?>
                <button class="button-search"
                    onclick="location.href='follows.php?author_id=<?php echo $user['id']; ?>&action=<?php echo $is_follow ? 'unfollow' : 'follow'; ?>&redirect=search.php'">
                    <?php echo $is_follow ? 'Отписаться' : 'Подписаться'; ?>
                </button>
            <?php endif; ?>

        </div>
        <?php endwhile;?>
    </div>
</main>

<?include ('template/default/footer.php');?>