<?
session_start();
require_once 'module/db.php';

$query_nav = "SELECT * FROM `nav`";
$res_nav = mysqli_query($link, $query_nav);
?>
<header> 
    <nav>
        <div class="container">
            <h1 class="logo"><a href="index.php">X-clone</a></h1>
            <ul class="nav">
                <?php while($nav = mysqli_fetch_assoc($res_nav)):?>
                <li><?php echo $nav['icon'];?><a href="./<?php echo $nav['link'];?>"><?php echo $nav['name'];?></a></li>
                <?php endwhile;?>
            </ul>
        </div>
    </nav>
    <div class="post">
        <button><a href="index.php">Создать публикацию</a></button>
        <div class="user">
            <?php if (isset($_SESSION['user'])): ?>
                <div class="avatar">
                    <?php 
                    $avatar = isset($_SESSION['user']['avatar']) && !empty($_SESSION['user']['avatar']) 
                    ? $_SESSION['user']['avatar'] : 'images/default-avatar.png';
                    ?>
                    <img src="<?php echo $avatar; ?>" alt="avatar">
                </div>
                <div class="username">
                    <h4><?php echo $_SESSION['user']['login']; ?></h4>
                    <p>@<?php echo $_SESSION['user']['login']; ?></p>
                </div>
                <a href="logout.php" >
                    <i class="fa fa-sign-out fa-2x" aria-hidden="true"></i>
                </a>
            <?php else: ?>
            <button class="login-btn"><a href="register.php"><i class="fa fa-user-circle fa-2x" aria-hidden="true"></a></i></button>
            <?php endif; ?>
        </div>
    </div>
</header>
