<?php
session_start();

if (!empty($_SESSION['auth'])) {
    header('Location: /admin.php');
    die;
}

$infoMessage = '';

if (!empty($_POST['email']) && !empty($_POST['password'])) {

    $aConfig = require_once 'config.php';
    $db = mysqli_connect($aConfig['host'], $aConfig['user'], $aConfig['pass'], $aConfig['name']);

    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    $query = "SELECT id FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($db, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['auth'] = true;

        mysqli_close($db);
        header("Location: admin.php");
        die;
    } else {
        $infoMessage = "Такого пользователя не существует или неверный пароль. ";
        $infoMessage .= "<a href='register.php'>Страница регистрации</a>";
    }

    mysqli_close($db);

} elseif (!empty($_POST)) {
    $infoMessage = 'Заполните форму авторизации!';
}
?>

<!DOCTYPE html>
<html>
<?php require_once 'sectionHead.php' ?>
<body>
<div class="container">
    <?php require_once 'sectionNavbar.php' ?>
    <br>
    <div class="card card-primary">
        <div class="card-header bg-primary text-light">
            Login form
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email"/>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input class="form-control" type="password" name="password"/>
                </div>
                <br>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" name="form"/>
                </div>
            </form>

            <?php
            if ($infoMessage) {
                echo '<hr/>';
                echo "<span style='color:red'>$infoMessage</span>";
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>