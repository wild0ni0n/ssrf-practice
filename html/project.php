<?php
if (!file_exists('/tmp/test.db')) {
    header("Location: /index.php");
    exit;
} 
?>

<html>
<head>
<meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta http-equiv="X-UA-Compatible" content="ie=edge" />
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

<?php
function h($arg) {
    return htmlspecialchars($arg);
}

function export($file_name, $data)
{
    $fp = fopen('php://output', 'w');

    foreach ($data as $row) {
        fputcsv($fp, $row, ',', '"');
    }
    fclose($fp);
    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename={$file_name}");
    header('Content-Transfer-Encoding: binary');
    exit;
}

if( isset($_GET['pid']) ) {
    # DB接続
    $db = new SQLite3('/tmp/test.db');

    # プロジェクト取得
    $stmt = $db->prepare('SELECT count(id), name FROM projects WHERE id=:val');
    $stmt->bindValue(':val', $_GET['pid']);

    $result = $stmt->execute();
    $row = $result->fetchArray();
    if(!$row[1]) {
        die('存在しないプロジェクトです');
    }

} else {
    die('存在しないプロジェクトです');
}

if (isset($_POST['mode'])) {
    # プロジェクト参加
    if ($_POST['mode'] == 'join') {
        # ユーザIDが存在するか確認
        $stmt = $db->prepare("SELECT count(*) FROM users WHERE uid = :uid");
        $stmt->bindValue(':uid', $_POST["userid"]);
        $result = $stmt->execute();
        $row = $result->fetchArray();
        if(!$row[0]) {
            die('存在しないユーザIDです');
        }
                
        # 現在のプロジェクトメンバーをカウント
        $stmt = $db->prepare("SELECT count(*) FROM project_member WHERE pid = :pid");
        $stmt->bindValue(':pid', $_GET["pid"]);
        $result = $stmt->execute();
        $count = $result->fetchArray()[0];
    
        $stmt = $db->prepare("INSERT INTO project_member(pmid, uid, pid, role) VALUES (:pmid, :uid, :pid, 0)");
        $stmt->bindValue(':pmid', ++$count);
        $stmt->bindValue(':uid', $_POST["userid"]);
        $stmt->bindValue(':pid', $_GET["pid"]);
        $stmt->execute();
    }
    
    # ロール変更
    if ($_POST['mode'] == 'change') {
        $stmt = $db->prepare("SELECT role FROM project_member WHERE pmid = :pmid AND pid = :pid");
        $stmt->bindValue(':pmid', $_POST['pmid']);
        $stmt->bindValue(':pid', $_GET['pid']);
        $result = $stmt->execute();
        $current_role = $result->fetchArray()[0];

        $changed_role = $current_role === 0? 1:0; 

        $stmt = $db->prepare("UPDATE project_member SET role = :role WHERE pmid = :pmid AND pid = :pid");
        $stmt->bindValue(':role', $changed_role);
        $stmt->bindValue(':pmid', $_POST['pmid']);
        $stmt->bindValue(':pid', $_GET['pid']);
        $result = $stmt->execute();

        if (!$result) {
            die('クエリーが失敗しました。');
        }
    }

    # プロジェクトから脱退
    if ($_POST['mode'] == 'delete') {
        # PMIDが存在するか確認
        $stmt = $db->prepare("UPDATE project_member SET delflag = 1 WHERE pmid = :pmid AND pid = :pid");
        $stmt->bindValue(':pmid', $_POST['pmid']);
        $stmt->bindValue(':pid', $_GET['pid']);
        $result = $stmt->execute();
        if(!$result) {
            die('存在しないPMIDです');
        }
    }

}
?>

<div class="container">
    <div class="mt-5 pb-4">
        <h4>プロジェクトに参加しているユーザ一覧</h4>
        <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">PMID</th>
                <th scope="col">Name</th>
                <th scope="col">Role</th>
            </tr>
        </thead>
        <tbody>

            <?php
                $stmt = $db->prepare("SELECT pm.pmid, users.name, pm.role FROM users JOIN project_member pm ON pm.uid = users.uid WHERE pm.pid = :pid AND pm.delflag = 0");
                $stmt->bindValue(':pid', $_GET['pid']);

                $result = $stmt->execute();
                while ($row = $result->fetchArray()) {
                    print("<tr>");
                    print("<td>".h($row['pmid'])."</td>");
                    print("<td>".h($row['name'])."</td>");
                    $role = $row['role'] === 0 ? '市民' : '人狼';
                    print("<td>".$role."</td>");
                    print("</tr>");
                }
            ?>

        </tbody>
        </table>
    </div>
    
    <h4>プロジェクトに参加させる</h4>
    <div class="row p-4">
        <div class="col py-3 px-lg-3 border bg-light">
            <h5>ユーザIDを指定して追加</h5>
            <form action="project.php?pid=<?= $_GET['pid'] ?>" method="post" class="form-inline">
                <input type="hidden" name="mode" value="join">
                <div class="mb-2 mr-3">
                    <input type="text" class="form-control" placeholder="ユーザID" name="userid">
                </div>
                <button type="submit" class="btn btn-primary mb-2">追加</button>
            </form>
        </div>
        <div class="col py-3 px-lg-3 border bg-light">
            <h5>ファイルアップロード</h5>
            <p class="text-secondary">ファイルからユーザIDを取り込みます。<a href="sample-user-list.txt" download="sample-user-list.txt">サンプルファイル</a></p>
            <form action="confirm.php" method="post" enctype="multipart/form-data" class="form-inline">
                <input type="hidden" name="pid" value="<?= h($_GET['pid']) ?>">
                <div class="mb-2 mr-3">
                    <input type="file" class="form-control-file" name="upfile" id="upfile">
                </div>
                <button type="submit" class="btn btn-primary mb-2">アップロード</button>
            </form>
        </div>
    </div>

    <h4>プロジェクトメンバーの設定</h4>
    <div class="row p-4">
        <div class="col py-3 px-lg-3 border bg-light">
            <h5>ユーザの役割を変更する</h5>
            <p class="text-secondary">ユーザの役割を市民から人狼、または人狼から市民に変更します。</p>
            <form action="#" method="post" class="form-inline">
                <input type="hidden" name="mode" value="change"">
                <div class="mb-2 mr-3">
                    <input type="text" class="form-control" name="pmid" value="" placeholder="PMID">
                </div>
                <button type="submit" class="btn btn-primary mb-2">変更</button>
            </form>
        </div>
        <div class="col py-3 px-lg-3 border bg-light">
            <h5>プロジェクトから脱退させる</h5>
            <form action="project.php?pid=<?= $_GET['pid'] ?>" method="post" class="form-inline">
                <input type="hidden" name="mode" value="delete"">
                <div class="mb-2 mr-3">
                    <input type="text" class="form-control" name="pmid" value="" placeholder="PMID">
                </div>
                <button type="submit" class="btn btn-primary mb-2">脱退</button>
            </form>
        </div>
    </div>

    <h4>エクスポート</h4>
    <div class="p-4">
        <form action="download.php?pid=<?= $_GET['pid'] ?>" method="post">
            <input type="hidden" name="mode" value="download">
            <button type="submit" class="btn btn-primary mb-2">プロジェクトに参加しているユーザリストをダウンロードする</button>
        </form>
    </div>
</div>
<?php 
$db->close();
?>
</body>
</html>