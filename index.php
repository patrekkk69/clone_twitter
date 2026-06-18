<?php 
session_start();
require_once 'module/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: register.php');
    exit();
}

$current_user = $_SESSION['user'];

$query_user = "SELECT login, avatar FROM users WHERE id = {$current_user['id']}";
$res_user = mysqli_query($link, $query_user);
$current_user_db = mysqli_fetch_assoc($res_user);

if (!$current_user_db) {
    session_destroy();
    header('Location: register.php');
    exit();
}

$current_user = $current_user_db;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_post'])) {
    $user_id = $_SESSION['user']['id'];
    $description = mysqli_real_escape_string($link, $_POST['post_text']);
    
    if (!file_exists('downloads/')) {
        mkdir('downloads/', 0777, true);
    }
    
    $image = '';
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
        $image = 'downloads/' . $filename;
        move_uploaded_file($_FILES['picture']['tmp_name'], $image);
    }
    
    $query = "INSERT INTO posts (user_id, description, image, date) VALUES ('$user_id', '$description', '$image', NOW())";
    mysqli_query($link, $query);
    
    header('Location: index.php');
    exit();
}

$query_post = "SELECT * FROM posts ORDER BY date DESC";
$res_post = mysqli_query($link, $query_post);

include('template/default/head.php');
include('template/default/header.php');
?>
<main class="index">
    <div class="subtitle">
        <div class="main-header">
            <h2>Главная</h2>
        </div>

        <div class="category">
            <div class="foru active-profile">
                Для вас
            </div>
            <div class="follows">
                <a href="subscribe.php">Вы подписаны</a>
            </div>
        </div>
    </div>

    <form class="form-post" action="" method="POST" enctype="multipart/form-data">
        <div class="avatar">
            <img src="<?php echo $current_user['avatar'] ?: 'images/default-avatar.jpg'; ?>" alt="avatar">
        </div>
        <input name="post_text" class="input" placeholder="Что происходит?" required>
        <input type="file" name="picture" id="picture" style="display:none">
        <label for="picture"><i class="fa fa-file-image-o"></i></label>
        <button type="submit" name="submit_post">Пост</button>
    </form>

    <div class="publ">
        <?php while ($post = mysqli_fetch_assoc($res_post)):
            $post_user_id = $post['user_id'];
            $query_user_post = "SELECT login, avatar FROM users WHERE id = $post_user_id";
            $res_user_post = mysqli_query($link, $query_user_post);
            $user = mysqli_fetch_assoc($res_user_post);
            if (!$user) continue;
            
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
        <?php endwhile; ?>
    </div>
</main>

<?php include('template/default/footer.php'); ?>