<?php
if (!file_exists('/tmp/test.db')) {
    header("Location: /index.php");
    exit;
} 
?>

<html>
<head>
<?php include("header.php"); ?>
</head>
<body>
<?php include("nav.php"); ?>
<div class="container">
    <div class="mt-5 pb-2">
        <?php
        function h($arg) {
            return htmlspecialchars($arg);
        }

        $uploaddir = '/tmp/';
        $uploadfile = $uploaddir.uniqid();

        if (move_uploaded_file($_FILES['upfile']['tmp_name'], $uploadfile)) {
            $count = count( file( $uploadfile ) );
            echo $count."件のデータをアップロードします。よろしいですか？";
            echo "<form action=\"upload.php\" method=\"post\">";
            echo "<input type=\"hidden\" name=\"pid\" value=\"".h($_POST["pid"])."\">";
            echo "<input type=\"hidden\" name=\"tmp_file_path\" value=\"".$uploadfile."\">";
            echo "<input type=\"submit\" value=\"送信\">";
        } else {
            echo "ファイルアップロードに失敗しました。";
        }
        ?>
    </div>
</div>
</html>