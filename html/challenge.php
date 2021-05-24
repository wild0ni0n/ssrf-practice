<?php
if (!file_exists('/tmp/test.db')) {
    header("Location: /index.php");
    exit;
} 
if(isset($_POST["flag"])) {
    $flag = $_POST["flag"];
    $iscorrect = FALSE;
    if($flag === "FLAG_A7D3C0BA0F") {
        $iscorrect = TRUE;
        setcookie("FLAG_A7D3C0BA0F", "1");
    }
    else if($flag === "FLAG_9E96EBD40C") {
        $iscorrect = TRUE;
        setcookie("FLAG_9E96EBD40C", "1");
    }
    else if($flag === "FLAG_3488619AE9") {
        $iscorrect = TRUE;
        setcookie("FLAG_3488619AE9", "1");
    }
    else if($flag === "FLAG_726C6BEDAE") {
        $iscorrect = TRUE;
        setcookie("FLAG_726C6BEDAE", "1");
    }
    else if($flag === "FLAG_0F363AF467") {
        $iscorrect = TRUE;
        setcookie("FLAG_0F363AF467", "1");
    }
}
?>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="/index.php">SSRF-Practice</a>
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="/index.php">Home</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/about.php">About</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/challenge.php">Challenge</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/hints.php">Hints</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
<div class="mt-5 pb-2">
        <h4>FLAG</h4>
        <p>見つけたFLAGの値を送信してください。
        <form action="challenge.php" method="post" class="form-inline">
            <div class="mb-2 mr-3">
                <input type="text" class="form-control" placeholder="FLAG_123456789" name="flag">
            </div>
            <button type="submit" class="btn btn-primary mb-2">送信</button>
        </form>
        <?php
        if(isset($_POST["flag"])) {
            if($iscorrect) {
                print('<div class="p-3 mb-2 bg-success text-white">');
                print('    Congratulation! Flag is correct.');
                print('</div>');
            } else {
                print('<div class="p-3 mb-2 bg-danger text-white">');
                print('    Invalid Flag.');
                print('</div>');
            }
        }
        ?>

        <h4>Completion</h4>
        <ul class="list-group">
        <?php if(isset($_COOKIE['FLAG_A7D3C0BA0F'])): ?>
            <li class="list-group-item list-group-item-light">✅ No.1 Solved</li>
        <?php else: ?>
            <li class="list-group-item list-group-item-light">❌ No.1 Unsolved Challenge</li>
        <?php endif ?>

        <?php if(isset($_COOKIE['FLAG_9E96EBD40C'])): ?>
            <li class="list-group-item list-group-item-light">✅ No.2 Solved</li>
        <?php else: ?>
            <li class="list-group-item list-group-item-light">❌ No.2 Unsolved Challenge</li>
        <?php endif ?>

        <?php if(isset($_COOKIE['FLAG_3488619AE9'])): ?>
            <li class="list-group-item list-group-item-light">✅ No.3 Solved</li>
        <?php else: ?>
            <li class="list-group-item list-group-item-light">❌ No.3 Unsolved Challenge</li>
        <?php endif ?>

        <?php if(isset($_COOKIE['FLAG_726C6BEDAE'])): ?>
            <li class="list-group-item list-group-item-light">✅ No.4 Solved</li>
        <?php else: ?>
            <li class="list-group-item list-group-item-light">❌ No.4 Unsolved Challenge</li>
        <?php endif ?>

        <?php if(isset($_COOKIE['FLAG_0F363AF467'])): ?>
            <li class="list-group-item list-group-item-light">✅ No.5 Solved</li>
        <?php else: ?>
            <li class="list-group-item list-group-item-light">❌ No.5 Unsolved Challenge</li>
        <?php endif ?>
        </ul>
</div>
</body>
</html>
