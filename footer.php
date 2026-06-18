<?php 
session_start();
require_once 'module/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($link, $search);
    $query_user = "SELECT * FROM users WHERE login LIKE '%$search_safe%'";
} else {
    $query_user = "SELECT * FROM users LIMIT 2 OFFSET 1";
}
$res_user = mysqli_query($link, $query_user);
?>
<div class="right-block">
        <div class="search">
            <i class="fa fa-search" aria-hidden="true"></i>
            <form method="GET" action="">
                <input name="search" class="input-searcher" type="search" placeholder="Поиск">
            </form>
        </div>

        <div class="recommendations">
            <h2>Рекомендации</h2>
            <?php while($user = mysqli_fetch_assoc($res_user)):
                $author_id = (int)$user['id'];
                $follower_id = (int)$_SESSION['user']['id'];
                $query_follow = "SELECT * FROM follows WHERE author_id = $author_id AND follower_id = {$_SESSION['user']['id']}";
                $res_follow = mysqli_query($link, $query_follow);
                $is_follow = mysqli_num_rows($res_follow) > 0;
            ?>
            <div class="recommend-user one">
                <div class="avatar">
                    <img src="/<?php echo $user['avatar'];?>" alt="ava">
                </div>
                    <div class="username-recommendations">
                        <h4><?php echo $user['login'];?></h4>
                        <p>@<?php echo $user['login'];?></p>
                    </div>
                <?php if ($user['id'] == $_SESSION['user']['id']): ?>
                    <button class="button-search-we-recommend" >Это вы</button>
                <?php else: ?>
                    <button class="button-search-recommend"
                        onclick="location.href='follows.php?author_id=<?php echo $user['id']; ?>&action=<?php echo $is_follow ? 'unfollow' : 'follow'; ?>&redirect=search.php'">
                        <?php echo $is_follow ? 'Отписаться' : 'Подписаться'; ?>
                    </button>
                <?php endif; ?>
            </div>
            <?php endwhile;?>
            <p  class="more"><a href="search.php">Показать еще</a></p>
        </div>
    </div>
</body>
</html>