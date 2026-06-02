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

    $checkQuery = "SELECT id FROM users WHERE email = '$email'";
    $checkResult = mysqli_query($db, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $infoMessage = "Такой пользователь уже существует! Перейдите на страницу входа. ";
        $infoMessage .= "<a href='/login.php'>Страница входа</a>";
    } else {
        $insertQuery = "INSERT INTO users (email, password) VALUES ('$email', '$password')";
        if (mysqli_query($db, $insertQuery)) {
            mysqli_close($db);
            header('Location: /login.php');
            die;
        } else {
            $infoMessage = "Помилка бази даних.";
        }
    }
    mysqli_close($db);

} elseif (!empty($_POST)) {
    $infoMessage = 'Заполните форму регистрации!';
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
        <div class="card-header bg-success text-light">
            Register form
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
                    <input type="submit" class="btn btn-primary" name="formRegister"/>
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