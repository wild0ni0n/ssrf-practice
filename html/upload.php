<?php
if (!file_exists('/tmp/test.db')) {
    header("Location: /index.php");
    exit;
} 


try {
    $db = new SQLite3('/tmp/test.db');

    $stmt = $db->prepare("SELECT count(*) FROM project_member WHERE pid = :pid");
    $stmt->bindValue(':pid', $_POST["pid"]);
    $result = $stmt->execute();
    $count = $result->fetchArray()[0];
    
    $context = stream_context_create([
        'http' => [
            'header' => 'Cookie: secret=FLAG_9E96EBD40C',
            'timeout' => 5,
            'user_agent' => 'ua',
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ]);

    if($_POST["mode"] === "import") {
        $url = $_POST['url'];
    }else {
        $url = $_POST['tmp_file_path'];
    }

    if(parse_url($url, PHP_URL_HOST) === "169.254.169.254") {
        $path = 'http://nginx'.parse_url($url, PHP_URL_PATH);
    } else {
        $path = $url;
    }
    if (!preg_match('/^(http|https):\/\/.*/i', $path) && preg_match('/\.php$/i', $path) ) {
        throw new Exception();
    }
    $data = file_get_contents($path, FALSE, $context);
    if(!$data) {
        throw new Exception();
    }
    $data = explode("\r\n", $data);
    foreach($data as $v) {
        $stmt = $db->prepare("INSERT INTO project_member(pmid, uid, pid, role) VALUES (:pmid, :uid, :pid, 0)");
        $stmt->bindValue(':pmid', ++$count);
        $stmt->bindValue(':uid', $v);
        $stmt->bindValue(':pid', $_POST["pid"]);
        $stmt->execute();
    }
} catch (\Exception $e)  {
    $db->close();
    die("ファイル取り込みに失敗しました");
}
header("Location: /project.php?pid=".$_POST['pid']);
$db->close();
?>