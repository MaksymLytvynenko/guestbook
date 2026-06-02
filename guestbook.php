<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions

session_start();
$filename = 'comments.csv';
$errors = [];

function getComments($file) {
    $comments = [];
    if (file_exists($file)) {
        $fileStream = fopen($file, "r");
        if ($fileStream) {
            while (!feof($fileStream)) {
                $jsonString = fgets($fileStream);
                if (!empty(trim($jsonString))) {
                    $array = json_decode($jsonString, true);
                    if (!empty($array)) {
                        $comments[] = $array;
                    }
                }
            }
            fclose($fileStream);
        }
    }
    return array_reverse($comments);
}

// TODO 2: ROUTING

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $text = htmlspecialchars(trim($_POST['text'] ?? ''));

    if (!$email) $errors[] = "Please enter a valid email address.";
    if (empty($name)) $errors[] = "The 'Name' field cannot be empty.";
    if (empty($text)) $errors[] = "The 'Comment' field cannot be empty.";

    if (empty($errors)) {
        $commentData = [
                'email' => $email,
                'name' => $name,
                'text' => $text,
                'date' => date('Y-m-d H:i:s')
        ];

        $jsonString = json_encode($commentData);
        $fileStream = fopen($filename, 'a');

        if ($fileStream) {
            fwrite($fileStream, $jsonString . "\n");
            fclose($fileStream);
            header("Location: guestbook.php");
            exit();
        } else {
            $errors[] = "Failed to open the file for writing.";
        }
    }
}

$commentsList = getComments($filename);

// TODO 3: CODE by REQUEST METHODS (ACTIONS) GET, POST, etc. (handle data from request): 1) validate 2) working with data source 3) transforming data

// TODO 4: RENDER: 1) view (html) 2) data (from php)

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
                                        <i class="fa-solid fa-user"></i> <?= $comment['name'] ?>
                                        (<a href="mailto:<?= $comment['email'] ?>"><?= $comment['email'] ?></a>)
                                    </h6>
                                    <p class="card-text"><?= nl2br($comment['text']) ?></p>
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