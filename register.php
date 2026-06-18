<?php
session_start();
include('module/db.php');

$arrFieldErorr = [];

if (!empty($_POST)) {
    if ($_POST['login'] == '') {
        $arrFieldErorr['login'] = 'Логин не должен быть пустой строкой';
    }
    
    if ($_POST['password'] == '') {
        $arrFieldErorr['password'] = 'Пароль не должен быть пустой строкой';
    }
}

if (!empty($_POST['login']) and !empty($_POST['password']) and !empty($_POST['confirm'])
and empty($arrFieldErorr)
and ($_POST['password'] == $_POST['confirm'])) {
    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    
    $query = "SELECT * FROM users WHERE login='$login'";
    $user = mysqli_fetch_assoc(mysqli_query($link, $query));
    
    if (empty($user)) {
        $query = "INSERT INTO users SET login='$login', password='$password', email='$email'";
        
        if (mysqli_query($link, $query)) {
            $last_id = mysqli_insert_id($link);
            
            if ($last_id) {
                $query = "SELECT * FROM users WHERE id=$last_id";
                $res = mysqli_query($link, $query);
                $user = mysqli_fetch_assoc($res);
                unset($user['password']);
                $_SESSION['user'] = $user;
                
                header('Location: profile.php');
                exit;
            } else {
                echo "Error: Пользователь не был добавлен <br>";
            }
            
        } else {
            echo "Error: " . mysqli_error($link) . "<br>";
        }
    } else {
        echo "Error: Данный логин уже занят.<br>";
    }
    
} else {
    if (!empty($_POST) && $_POST['password'] != $_POST['confirm']) {
        echo "Ошибка: Пароль и подтверждение пароля должны совпадать.<br>";
    }
include('template/default/head.php');
?>

    <div class="reg-header">
        <h2>X-clone</h2>
        <p>Присоединяйтесь к разговору в режиме реального времени.</p>
    </div>
    <form class="form-reg" action="" method="POST">
        <?if (!empty($arrFieldErorr) && $arrFieldErorr['login']) {?>
            <div class="error"><?=$arrFieldErorr['login']?></div>
        <?}
        $inputValue = !empty($_POST) && $_POST['login'] ? $_POST['login']: '';
        ?>
        <p class="reg-name">Логин (имя пользователя)</p><br/>
        <input class="reg-input" name="login" value="<?=$inputValue?>" placeholder="@username">
        <br/>
        <?$inputValue = !empty($_POST) && $_POST['email'] ? $_POST['email']: '';?>
        <p class="reg-name">Email</p><br/>
        <input class="reg-input" name="email" type="text" value="<?=$inputValue?>" placeholder="email@example.com">
        <br/>
        <?if (!empty($arrFieldErorr) && $arrFieldErorr['password']) {?>
            <div class="error"><?=$arrFieldErorr['password']?></div>
        <?}
        $inputValue = !empty($_POST) && $_POST['password'] ? $_POST['password']: '';?>
        <p class="reg-name">Пароль</p><br/>
        <input class="reg-input" name="password" type="password" autocomplete="off" value="<?=$inputValue?>" placeholder="Введите пароль">
        <br/>
        <?$inputValue = !empty($_POST) && $_POST['confirm'] ? $_POST['confirm']: '';?>
        <p class="reg-name">Повторите пароль</p><br/>
        <input class="reg-input" name="confirm" type="password" autocomplete="off" value="<?=$inputValue?>" placeholder="Повторите пароль">
        <br/>
        <button class="reg-button" type="submit">Создать аккаунт<i class="fa fa-arrow-right" aria-hidden="true"></i></button>
        <br>
        <p class="reg-auth">Уже есть аккаунт?<a href="./author.php">Войти</a></p>
    </form>
<?}?>