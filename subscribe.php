<?php 
session_start();
require_once 'module/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: register.php');
    exit();
}

$current_user = $_SESSION['user'];
$user_id = (int)$current_user['id'];

$query_user = "SELECT id, login, avatar FROM users WHERE id = $user_id";
$res_user = mysqli_query($link, $query_user);

if (!$res_user) {
    die('Ошибка SQL: ' . mysqli_error($link));
}

$current_user_db = mysqli_fetch_assoc($res_user);

if (!$current_user_db) {
    session_destroy();
    header('Location: register.php');
    exit();
}

$current_user = $current_user_db;
include('template/default/head.php');
include('template/default/header.php');
?>

<main class="index">
    <div class="subtitle">
        <div class="main-header">
            <h2>Главная</h2>
        </div>

        <div class="category">
            <div class="foru">
                <a href="index.php">Для вас</a>
            </div>
            <div class="follows active-profile">
                <a href="subscribe.php">Вы подписаны</a>
            </div>
        </div>
    </div>

    <div class="publ">
        <?php
        $query_follows = "SELECT author_id FROM follows WHERE follower_id = $user_id";
        $res_follows = mysqli_query($link, $query_follows);
        
        $followed_ids = '';
        $first = true;
        while ($follow = mysqli_fetch_assoc($res_follows)) {
            if (!$first) $followed_ids .= ',';
            $followed_ids .= $follow['author_id'];
            $first = false;
        }
        
        if ($followed_ids != '') {
            $query_posts = "SELECT * FROM posts WHERE user_id IN ($followed_ids) ORDER BY date DESC";
            $res_posts = mysqli_query($link, $query_posts);
            
            while ($post = mysqli_fetch_assoc($res_posts)):
                $post_user_id = $post['user_id'];
                
                $query_user = "SELECT login, avatar FROM users WHERE id = $post_user_id";
                $res_user = mysqli_query($link, $query_user);
                $user = mysqli_fetch_assoc($res_user);
                if (!$user) continue;
                
                $query_check = "SELECT * FROM likes WHERE user_id = $user_id AND posts_id = {$post['id']}";
                $res_check = mysqli_query($link, $query_check);
                $user_liked = ($res_check && mysqli_num_rows($res_check) > 0);
                
                $query_likes = "SELECT COUNT(*) as cnt FROM likes WHERE posts_id = {$post['id']}";
                $res_likes = mysqli_query($link, $query_likes);
                $likes_count = ($res_likes) ? mysqli_fetch_assoc($res_likes)['cnt'] : 0;
                
                $query_comments = "SELECT COUNT(*) as cnt FROM comments WHERE post_id = {$post['id']}";
                $res_comments = mysqli_query($link, $query_comments);
                $comments_count = ($res_comments) ? mysqli_fetch_assoc($res_comments)['cnt'] : 0;
        ?>
        <div class="publications">
            <div class="user">
                <a href="profile.php?id=<?php echo $post_user_id; ?>">
                    <div class="avatar">
                        <img src="/<?php echo $user['avatar'];?>" alt="ava">
                    </div>
                </a>
                <div class="username">
                    <h4><?php echo htmlspecialchars($user['login']); ?></h4>
                    <p>@<?php echo htmlspecialchars($user['login']); ?></p>
                </div>
            </div>
            <div class="description">
                <p><?php echo htmlspecialchars($post['description']); ?></p>
                <?php if ($post['image']): ?>
                    <img src="<?php echo $post['image']; ?>">
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
                    <i class="fa fa-eye" aria-hidden="true"></i>1.2k
                </div>
            </div>
        </div>
        <?php 
            endwhile;
        } else {
            echo '<p style="text-align:center;padding:20px;">Вы пока ни на кого не подписаны</p>';
        }
        ?>
    </div>
</main>

<?php include('template/default/footer.php'); ?>