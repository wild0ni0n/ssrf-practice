<?php
function h($arg) {
    return htmlspecialchars($arg);
}


$init = FALSE;
if (!file_exists('/tmp/test.db')) {
    $init = TRUE;
} 

# DB接続
$db = new SQLite3('/tmp/test.db');

# 初期化
if($init) {
    $db->exec('CREATE TABLE users (uid VARCHAR(255) PRIMARY KEY, name varchar(10), memo varchar(20))');

    $db->exec('CREATE TABLE projects (id INTEGER PRIMARY KEY AUTOINCREMENT, name varchar(255))');

    $db->exec('CREATE TABLE project_member (id INTEGER PRIMARY KEY AUTOINCREMENT, pmid INTEGER, uid VARCHAR(255), pid INTEGER, role INTEGER, delflag INTEGER default 0)');

    # 初期ユーザ
    $db->exec("INSERT INTO users(uid, name, memo) VALUES ('5ee233f8c764', 'Taro', 'hogehoge')");
    $db->exec("INSERT INTO users(uid, name, memo) VALUES ('5ee23302443e', 'Sato', 'fugafuga')");
}

if (isset($_POST['mode'])) {
    # プロジェクト作成
    if ($_POST['mode'] == 'create_project') {
        $stmt = $db->prepare("INSERT INTO projects(name) VALUES (:val)");
        $stmt->bindValue(':val', $_POST['project_name']);
        $result = $stmt->execute();

        if (!$result) {
            die('クエリーが失敗しました。');
        } else {
            print('プロジェクトを作成しました。<br>');
        }
    } 

    # ユーザ作成
    if ($_POST['mode'] == 'create_user') {
        $id = uniqid('');
        $stmt = $db->prepare('INSERT INTO users(uid, name, memo) VALUES (:id, :name, :memo)');
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':name', $_POST['user_name']);
        $stmt->bindValue(':memo', $_POST['user_memo']);
        $result = $stmt->execute();
        
        if (!$result) {
            die('クエリーが失敗しました。');
        }else{
            print('ユーザを作成しました。<br>');
        }
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
                <!--
                # 作業ログ
                # 2020/6/11
                # プライベートIPが指定されていたことによりhints.phpがリンク切れを起こしていた問題を修正
                # 修正前: <a class="nav-link" href="https://<?= $_SERVER['SERVER_ADDR'] ?>/hints.php">Hints</a>
                # 修正後: <a class="nav-link" href="/hints.php">Hints</a>
                -->
                <a class="nav-link" href="/hints.php">Hints</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <div class="row mt-5 pb-4">
        <div class="col py-3 px-lg-3 border bg-light">
            <h5>プロジェクト作る</h5>
            <form action="index.php" method="post" class="form-inline">
                <input type="hidden" name="mode" value="create_project">
                <div class="mb-2 mr-3">
                    <input type="text" class="form-control" placeholder="プロジェクト名" name="project_name">
                </div>
                <button type="submit" class="btn btn-primary mb-2">作成</button>
            </form>
        </div>
        <div class="col py-3 px-lg-3 border bg-light">
            <h5>ユーザを作成する</h5>
            <form action="index.php" method="post" class="form-inline">
                <input type="hidden" name="mode" value="create_user"">
                <div class="form-group mb-2">
                    <input type="text" class="form-control" placeholder="ユーザ名" name="user_name">
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <input type="text" class="form-control" placeholder="備考" name="user_memo">
                </div>
                <button type="submit" class="btn btn-primary mb-2">作成</button>
            </form>
        </div>
    </div>

    <h4>プロジェクト一覧</h4>
    <div class="p-4">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Name</th>
                    <th scope="col">#</th>
                </tr>
            </thead>
        <tbody>
            <?php 
            $result = $db->query("SELECT id, name FROM projects");
            while ($row = $result->fetchArray()) {
                print("<tr>");
                print("<td>".h($row['id'])."</td>");
                print("<td>".h($row['name'])."</td>");
                print("<td><a href=\"project.php?pid=".h($row['id'])."\">詳細</a></td>");
                print("</tr>");
            }
            ?>
        </tbody>
        </table>
    </div>

    <h4>ユーザ一覧</h4>
    <div class="p-4">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">UserID</th>
                    <th scope="col">Name</th>
                </tr>
            </thead>
        <tbody>

        <?php 
        $result = $db->query('SELECT uid, name FROM users');
        while ($row = $result->fetchArray()) {
            print("<tr>");
            print("<td>".h($row['uid'])."</td>");
            print("<td>".h($row['name'])."</td>");
        print("</tr>");
        }
        ?>
        </tbody>
        </table>
    </div>

    <div class="p-4 float-right">
        <a href="admin.php"><button type="button" class="btn btn-info">管理者専用ページ</button></a>
    </div>
</div>
<?php 
$db->close();
?>
</body>
</html>