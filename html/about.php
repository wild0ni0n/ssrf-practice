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
        <h4>概要</h4>

        <p>
            本プログラムは、Server Side Request Forgery(以下、SSRF)の脆弱性を社内でも検証できるように用意したものです。<br />
            実際に診断案件で確認されたSSRFを模しているため、余分な機能も実装しております。<br />
            SSRFの脆弱性を悪用し、隠されたメッセージ(<code>FLAG_****</code>)を見つけてください。見つけたFLAGは<a href="challenge.php">Challengeのページ</a>からFLAGの値を送信してください。<br />
            正しいFLAGを送信することができた場合はCompletionのリストにチェックが付きます。
        </p>
        <p>FLAGは全部で<strong>5</strong>個あります。</p>
        <p>
            2021/8月 追記: 脆弱性の仕組みを分かりやすくするため直接的な機能と脆弱性を追加しました。
        </p>
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
        <h4>環境について</h4>
        <p>本アプリでは以下のような構成で動作しています。</p>
        <img src="topology.png" class="img-fluid" style="max-width: 85%; , height: auto; margin-bottom:15px;">
        <ul>
            <li>
                <code>Nginx</code>: webサーバのコンテナです。PHP-FPMと連携してSSRF-Practiceを動かしています
                <ul>
                    <li><code>/admin.php</code>: 管理者用ページです。内部ネットワークからのみアクセスできるページです。</li> 
                </ul>
            </li>
            <li><code>PHP-FPM</code>: アプリケーションコンテナです。</li>
            <li><code>Secret Web Server</code>: dockerの内部ネットワークにあるwebサーバです。SSRF-Practiceにアクセスしてくるユーザからは本来到達できないサーバです。</li>
            <li>
                <code>Attacker's server</code>: 攻撃者が用意したサーバです。ユーザは<a href="http://localhost:8888">http://localhost:8888</a>でアクセスが可能です。docker内部からは<code>http://attacker_server:8888</code>でアクセスできます。このサーバにアクセスしてきたGET,POSTのリクエストを記録します。
                <ul>
                    <li><code>/view-log</code>: 記録したリクエストを表示します。</li>
                </ul>
            </li>
        </ul>
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