<?php
session_start();
include('module/db.php');

if (!empty($_POST['password']) && !empty($_POST['login'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $password = mysqli_real_escape_string($link, $password);
    $query = "SELECT * FROM users WHERE login='$login'";
    $res = mysqli_query($link, $query);
    
    if ($res && mysqli_num_rows($res) > 0) {
        $user = mysqli_fetch_assoc($res);
        if (password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            header('Location: profile.php'); 
            exit;
        } else {
            $error = 'Неверный логин или пароль';
        }
    } else {
        $error = 'Неверный логин или пароль';
    }
}
include('template/default/head.php');
?>

<form class="form-auth" action="" method="POST">
    <h3 class="form-title-auth">Войти в X-clone</h3>
    <p class="auth-subtitle">С возвращением!! Пожалуйста, напишите<br> ваши данные.</p>
    <?php if (isset($error)): ?>
        <div style="color: red; text-align: center; margin-bottom: 15px;"><?php echo $error; ?></div>
    <?php endif; ?>
    <button class="continue">Продолжить с Google</button>
    <p class="auth-subtitle">Или с помощью email</p>
    <div>
        <p class="reg-name">Имя или email пользователя</p>
        <input name="login" class="input-auth" placeholder="Введите логин" required>
    </div>
    <div>
        <p class="reg-name">Пароль</p>
        <input name="password" class="input-auth" type="password" placeholder="Введите пароль" required>
    </div>
    <input class="button-auth" type="submit" name="войти" value="Войти">
    <p class="reg-auth">Еще нет аккаунта?<a href="register.php">Зарегистрироваться</a></p>
</form>