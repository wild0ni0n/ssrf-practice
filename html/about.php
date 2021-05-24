<?php
if (!file_exists('/tmp/test.db')) {
    header("Location: /index.php");
    exit;
} 
?>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<style>
    .bd-callout {
        margin-top: -.25rem;
        padding: 1.25rem;
        margin-top: 1.25rem;
        margin-bottom: 1.25rem;
        border: 1px solid #eee;
        border-left-width: .25rem;
        border-radius: .25rem;    
    }
    .bd-callout-warning {
        border-left-color: #f0ad4e;
    }

</style>
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
        <h4>概要</h4>

        <p>
            本プログラムは、Server Side Request Forgery(以下、SSRF)の脆弱性を社内でも検証できるように用意したものです。<br />
            実際に診断案件で確認されたSSRFを模しているため、余分な機能も実装しております。<br />
            SSRFの脆弱性を悪用し、隠されたメッセージ(<code>FLAG_****</code>)を見つけてください。見つけたFLAGは<a href="challenge.php">Challengeのページ</a>からFLAGの値を送信してください。<br />
            正しいFLAGを送信することができた場合はCompletionのリストにチェックが付きます。
        </p>
        <p>FLAGは全部で<strong>5</strong>個あります。</p>
        <div class="bd-callout bd-callout-warning">
            <p class="mb-0">本プログラムは複数のDockerコンテナで構成されています。</p>
            <p class="mb-0">WebサーバとPHPの実行環境が異なるため、一部のフラグを見つけるためには内部サーバを探索する必要があります</p>
        </div>
    </div>
    <div class="mt-2 pb-2">
        <h4>SSRFとは</h4>
        <p>SSRFは、攻撃者がWebアプリケーションに攻撃者が指定した任意のドメインにリクエストを矯正させる脆弱性です。</p>
        
        <p>SSRFの脆弱性がWebアプリケーションに存在する場合、Webアプリケーションに内部サーバへリクエストを送信させたり、攻撃者自身のドメインに対してリクエストを送信させて、不正なアクションやデータへのアクセスを可能にすることができます。</p>
    </div>
    <div class="mt-2 pb-2">
        <h4>参考情報</h4>
        <ul class="list-group">
            <li class="list-group-item"><a href="https://blog.tokumaru.org/2018/12/introduction-to-ssrf-server-side-request-forgery.html">SSRF(Server Side Request Forgery)徹底入門</a></li>
            <li class="list-group-item"><a href="https://speakerdeck.com/hasegawayosuke/ssrfji-chu">SSRF基礎</a></li>
            <li class="list-group-item"><a href="https://portswigger.net/web-security/ssrf">Server-side request forgery (SSRF)</a></li>
            <li class="list-group-item"><a href="https://gist.github.com/mrtc0/60ca6ba0fdfb4be0ba499c65932ab42e">cloud-service-metadata-api-list.md</a></li>
        </ul>
    </div>
</div>
</body>
</html>