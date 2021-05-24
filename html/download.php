<?php
if (!file_exists('/tmp/test.db')) {
    header("Location: /index.php");
    exit;
} 

function export($file_name, $data)
{
    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename={$file_name}");
    header('Content-Transfer-Encoding: binary');

    $fp = fopen('php://output', 'w');
    $header = array("Project Mmeber ID", "User ID", "Name");
    fputcsv($fp, $header, ',', '"');
    
    foreach ($data as $line) {
        fputcsv($fp, $line, ',', '"');
    }
    fclose($fp);
    exit;
}


if ($_POST['mode'] == 'download') {
    $db = new SQLite3('/tmp/test.db');

    $stmt = $db->prepare("SELECT pm.pmid, pm.uid, u.name FROM project_member pm LEFT OUTER JOIN users u ON pm.uid = u.uid WHERE pm.pid = :pid AND pm.delflag = 0");
    $stmt->bindValue(':pid', $_GET['pid']);

    $result = $stmt->execute();
    
    $row = array();
    while($res = $result->fetchArray(SQLITE3_ASSOC)){
        $row[] = array($res['pmid'], $res['uid'], $res['name']);
    }
    
    export("user.csv", $row);
} else {
    die("ダウンロードに失敗しました");
}

?>