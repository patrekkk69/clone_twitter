<?php
session_start();
require_once 'module/db.php';

if(!isset($_SESSION['user'])) {
    header('Location: register.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$post_id = (int)$_GET['post_id'];

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_comment'])) {
    $text = mysqli_real_escape_string($link, $_POST['comment']);
    $query_comment = "INSERT INTO comments (post_id, user_id, comment, date) VALUES ($post_id, $user_id, '$text', NOW())";
    mysqli_query($link, $query_comment);
    header("Location: comments.php?post_id=$post_id");
    exit();
}

$query_post = "SELECT * FROM posts WHERE id = $post_id";
$res_post = mysqli_query($link, $query_post);
$post = mysqli_fetch_assoc($res_post);

$user_query = "SELECT login, avatar FROM users WHERE id = {$post['user_id']}";
$user_res = mysqli_query($link, $user_query);
$post_author = mysqli_fetch_assoc($user_res);

$query_comments = "SELECT * FROM comments WHERE post_id = $post_id ORDER BY date DESC";
$res_comments = mysqli_query($link, $query_comments);

include('template/default/head.php');
include('template/default/header.php');
?>

<main class="index">
    <div class="subtitle">
        <div class="main-header">
            <a href="index.php">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                <h2>Вернуться</h2>
            </a>
        </div>
    </div>

    <div class="publ">
        <div class="publications">
            <div class="user">
                <a href="profile.php?id=<?php echo $post['user_id']; ?>">
                    <div class="avatar">
                        <img src="<?php echo $post_author['avatar'] ?: 'images/default-avatar.png'; ?>" alt="ava">
                    </div>
                </a>
                <div class="username">
                    <h4><?php echo htmlspecialchars($post_author['login']); ?></h4>
                    <p>@<?php echo htmlspecialchars($post_author['login']); ?></p>
                </div>
            </div>
            <div class="description">
                <p><?php echo htmlspecialchars($post['description']); ?></p>
                <?php if(!empty($post['image'])): ?>
                    <img src="<?php echo $post['image']; ?>" alt="post image">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="form-post-comment">
        <form method="POST">
            <div class="avatar">
                <img src="<?php echo $_SESSION['user']['avatar'] ?: 'images/default-avatar.jpg'; ?>" alt="avatar">
            </div>
            <input type="text" name="comment" class="input" placeholder="Написать комментарий..." required>
            <button type="submit" name="send_comment">Отправить</button>
        </form>
    </div>

    <div class="publ">
        <?php if (mysqli_num_rows($res_comments) > 0): ?>
            <?php while ($comment = mysqli_fetch_assoc($res_comments)): ?>
                <?php
                $user_query = "SELECT login, avatar FROM users WHERE id = {$comment['user_id']}";
                $user_res = mysqli_query($link, $user_query);
                $user = mysqli_fetch_assoc($user_res);
                ?>
                <div class="publications">
                    <div class="user">
                        <div class="avatar">
                            <img src="<?php echo $user['avatar'] ?: 'images/default-avatar.png'; ?>" alt="avatar">
                        </div>
                        <div class="username">
                            <h4><?php echo htmlspecialchars($user['login']); ?></h4>
                            <p>@<?php echo htmlspecialchars($user['login']); ?></p>
                            <small><?php echo date('d.m.Y H:i', strtotime($comment['date'])); ?></small>
                        </div>
                    </div>
                    <div class="description">
                        <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="comments">
                <p>Пока нет комментариев. Будьте первым!</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include('template/default/footer.php'); ?>