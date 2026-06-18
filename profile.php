<?php
session_start();
include('template/default/head.php');
include('template/default/header.php');
require_once('module/db.php');

if (!isset($_SESSION['user']['id'])) {
    echo '<div style="text-align: center; padding: 50px;">
            <h2>Для просмотра профиля необходимо войти в аккаунт</h2>
            <p><a href="author.php">Войти</a> или <a href="register.php">Зарегистрироваться</a></p>
           </div>';
    include('template/default/footer.php');
    exit();
}

$current_user_id = (int)$_SESSION['user']['id'];

$profile_id = (int)($_GET['id'] ?? $current_user_id);

$is_own_profile = ($profile_id === $current_user_id);

if ($is_own_profile) {
    $user_data = $_SESSION['user'];
} else {
    $query = "SELECT * FROM users WHERE id = $profile_id";
    $result = mysqli_query($link, $query);
    $user_data = $result ? mysqli_fetch_assoc($result) : null;
}

if (!$user_data) {
    die('Пользователь не найден');
}

$query_posts = "SELECT * FROM `posts` WHERE user_id = '$profile_id' ORDER BY date DESC";
$res_posts = mysqli_query($link, $query_posts);
$posts_count = mysqli_num_rows($res_posts);

$query_followers = "SELECT * FROM `follows` WHERE `author_id` = '$profile_id'";
$res_followers = mysqli_query($link, $query_followers);
$followers_count = mysqli_num_rows($res_followers);

$query_following = "SELECT * FROM `follows` WHERE `follower_id` = '$profile_id'";
$res_following = mysqli_query($link, $query_following);
$following_count = mysqli_num_rows($res_following);

$query_follow = "SELECT * FROM follows WHERE author_id = $profile_id AND follower_id = {$_SESSION['user']['id']}";
$res_follow = mysqli_query($link, $query_follow);
$is_follow = mysqli_num_rows($res_follow) > 0;

function formatNumber($num) {
    if ($num >= 1000) {
        return round($num / 1000, 1) . 'k';
    }
    return $num;
}
?>

<main class="index">
    <div class="subtitle-profile">
        <div class="main-header">
            <a href="index.php"><i class="fa fa-arrow-left" aria-hidden="true"></i><h2><?php echo htmlspecialchars($user_data['login']); ?></h2></a>
        </div>
        <p><?php echo $posts_count; ?> Публикаций</p>
    </div>

    <div class="header-cover">
        <div class="cover">
            <img src="images/png/cover.png" alt="cover">
        </div>
        <div class="avatar-cover">
            <?php 
            $avatar = !empty($user_data['avatar']) ? $user_data['avatar'] : 'images/default-avatar.png';
            ?>
            <img src="<?php echo htmlspecialchars($avatar);?>" alt="avatar">
            
            <?php if ($is_own_profile): ?>
                <button class="button-cover" onclick="location.href='edit_profile.php'">
                    Редактировать профиль
                </button>
            <?php else: ?>
                <button class="button-search"onclick="location.href='follows.php?author_id=<?php echo $profile_id; ?>
                    &action=<?php echo $is_follow ? 'unfollow' : 'follow'; ?>&redirect=profile.php?id=<?php echo $profile_id; ?>'">
                    <?php echo $is_follow ? 'Отписаться' : 'Подписаться'; ?>
                </button>
            <?php endif; ?>
        </div>
        <div class="username">
            <h2><?php echo htmlspecialchars($user_data['login']);?></h2>
            <p>@<?php echo htmlspecialchars($user_data['login']);?></p>
        </div>
        <div class="profile-description">
            <p><?php echo !empty($user_data['bio']) ? htmlspecialchars($user_data['bio']) : 'Описание пока не добавлено'; ?></p>
        </div>
        <div class="date">
            <i class="fa fa-calendar" aria-hidden="true"></i>
            <p>Присоединился: <?php echo date('F Y', strtotime($user_data['data'] ?? 'now')); ?></p>
        </div>
        <div class="followers">
            <div class="following"><p><?php echo $following_count; ?> Подписок</p></div>
            <div class="follower"><?php echo $followers_count; ?> Подписчиков</div>
        </div>
    </div>

    <div class="posts">
        <div class="name-post">
            <p class="active-profile">Публикации</p>
            <p class="answers">Ответы</p>
        </div>

        <div class="publ">
            <?php if ($posts_count > 0): ?>
                <?php while ($post = mysqli_fetch_assoc($res_posts)):
                    $user_id = $_SESSION['user']['id'];
                    $query_check = "SELECT * FROM likes WHERE user_id = $user_id AND posts_id = {$post['id']}";
                    $res_check = mysqli_query($link, $query_check);
                    $user_liked = ($res_check && mysqli_num_rows($res_check) > 0);
                    $query_likes = "SELECT COUNT(*) as cnt FROM likes WHERE posts_id = {$post['id']}";
                    $res_likes = mysqli_query($link, $query_likes);
                    $likes_count = ($res_likes) ? mysqli_fetch_assoc($res_likes)['cnt'] : 0;
                    $query_comments_count = "SELECT COUNT(*) as cnt FROM comments WHERE post_id = {$post['id']}";
                    $res_comments_count = mysqli_query($link, $query_comments_count);
                    $comments_count = ($res_comments_count) ? mysqli_fetch_assoc($res_comments_count)['cnt'] : 0;
                    ?>
                    <div class="publications">
                        <div class="user">
                            <div class="avatar">
                                <img src="<?php echo htmlspecialchars($avatar);?>" alt="ava">
                            </div>
                            <div class="username">
                                <h4><?php echo htmlspecialchars($user_data['login']);?></h4>
                                <p>@<?php echo htmlspecialchars($user_data['login']);?></p>
                            </div>
                        </div>

                        <div class="description">
                            <p><?php echo htmlspecialchars($post['description']);?></p>
                            <?php if (!empty($post['image'])):?>
                                <img src="<?php echo htmlspecialchars($post['image']);?>" class="description-profile" alt="post image">
                            <?php endif; ?>
                        </div>

                        <div class="indicators">
                            <div class="comments">
                                <a href="comments.php?post_id=<?php echo $post['id']; ?>">
                                    <i class="fa fa-comment-o" aria-hidden="true"></i>
                                    <span class="like-count"><?php echo $comments_count; ?></span>
                                </a>
                            </div>
                            <div class="likes">
                                <a href="likes.php?posts_id=<?php echo $post['id']; ?>">
                                    <i class="fa <?php echo $user_liked ? 'fa-heart' : 'fa-heart-o'; ?> like-icon <?php echo $user_liked ? 'liked' : ''; ?>" aria-hidden="true"></i>
                                    <span class="like-count"><?php echo $likes_count; ?></span>
                                </a>
                            </div>
                            <div class="views">
                                <i class="fa fa-eye" aria-hidden="true"></i><?php echo formatNumber(1200); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px;">
                    <p>У <?php echo $is_own_profile ? 'вас' : 'этого пользователя'; ?> пока нет публикаций</p>
                    <?php if ($is_own_profile): ?>
                        <a href="index.php" style="color: #1da1f2;">Создать публикацию</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include('template/default/footer.php'); ?>