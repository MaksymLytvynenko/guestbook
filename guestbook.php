<?php
session_start();

$aConfig = require_once 'config.php';
$db = mysqli_connect($aConfig['host'], $aConfig['user'], $aConfig['pass'], $aConfig['name']);

if (!$db) {
    die("Помилка з'єднання з базою даних: " . mysqli_connect_error());
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $text = htmlspecialchars(trim($_POST['text'] ?? ''));

    if (!$email) $errors[] = "Please enter a valid email address.";
    if (empty($name)) $errors[] = "The 'Name' field cannot be empty.";
    if (empty($text)) $errors[] = "The 'Comment' field cannot be empty.";

    if (empty($errors)) {
        $emailEsc = mysqli_real_escape_string($db, $email);
        $nameEsc = mysqli_real_escape_string($db, $name);
        $textEsc = mysqli_real_escape_string($db, $text);

        $query = "INSERT INTO comments (email, name, text) VALUES ('$emailEsc', '$nameEsc', '$textEsc')";

        if (mysqli_query($db, $query)) {
            header("Location: guestbook.php");
            exit();
        } else {
            $errors[] = "Помилка бази даних: " . mysqli_error($db);
        }
    }
}

$query = "SELECT * FROM comments ORDER BY date DESC";
$dbResponse = mysqli_query($db, $query);

$commentsList = [];
if ($dbResponse) {
    $commentsList = mysqli_fetch_all($dbResponse, MYSQLI_ASSOC);
}

mysqli_close($db);
?>

<!DOCTYPE html>
<html>
<?php require_once 'sectionHead.php' ?>
<body>
<div class="container">
    <?php require_once 'sectionNavbar.php' ?>
    <br>

    <div class="card card-primary mb-4">
        <div class="card-header bg-primary text-light">
            GuestBook form
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?= implode('<br>', $errors) ?>
                        </div>
                    <?php endif; ?>

                    <form action="guestbook.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="text" class="form-label">Comment</label>
                            <textarea class="form-control" id="text" name="text" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-primary">
        <div class="card-header bg-body-secondary text-dark">
            Comments
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php if (!empty($commentsList)): ?>
                        <?php foreach ($commentsList as $comment): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <i class="fa-solid fa-user"></i> <?= htmlspecialchars($comment['name']) ?>
                                        (<a href="mailto:<?= htmlspecialchars($comment['email']) ?>"><?= htmlspecialchars($comment['email']) ?></a>)
                                    </h6>
                                    <p class="card-text"><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                                    <small class="text-muted"><?= $comment['date'] ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No comments yet. Be the first to post!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>