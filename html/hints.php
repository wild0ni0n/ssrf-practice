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
    <form action="hints.php" method="post">
        <div class="form-group">
            <label for="level">見たいヒントのHintを指定してください。</label>
            <select class="form-control" id="level" name="hint">
                <option value="1">SSRFの脆弱性を探す</option>
                <option value="2">内部ファイルを見つける</option>
                <option value="3">リクエストを覗く</option>
                <option value="4">管理者ページを閲覧する</option>
                <option value="5">内部ネットワークを探索する</option>
                <option value="6">内部サーバにアクセスする</option>
                <option value="7">クラウドメタデータにアクセスする</option>
                
            </select>
        </div>
        <button type="submit" class="btn btn-primary mb-2">FLAGのヒントを見る</button>
    </form>

    <?php if(isset($_POST['hint'])):?>
        <?php if($_POST['hint'] === "1"): ?>
            <p>まずは怪しい所を探しましょう。</p>
            <p>ユーザからの入力値で内部パスや内部ファイルを指定すべきではありません。</p>
        <?php elseif($_POST['hint'] === "2"): ?>
            <p>内部パスがリクエストで渡されている場合、改ざんすることで重要情報を含んだファイルを参照できる可能性があります。<br />
            OSがデフォルトで用意しているファイルを指定してみましょう。<br />
            例えばLinuxであれば、<code>/etc/hosts</code>や<code>/etc/passwd</code>があります</p>
            <p>これは、パストラバーサルに分類される脆弱性ですがパスだけでなくURLも指定できる場合、SSRFの可能性もあります。</p>
        <?php elseif($_POST['hint'] === "3"): ?>
            <p>攻撃者が保有するドメインにリクエストを送信させることで、サーバリクエスト情報を取得することができます。<br />
            Webアプリケーションによっては、重要情報やトークン値などが漏えいする可能性があります。<br />
            <p>本アプリでは<a href="http://localhost:8888">攻撃者が用意したサーバ</a>があります。このサーバはアクセス時のリクエストを記録し、記録したリクエストを<code>/view-log</code>のページから閲覧することができます。
            また、docker内部からは<a href="http://attacker_server:8888">http://attacker_server:8888</a>でアクセスすることができます。(このURLは、ユーザからはアクセスできないので注意してください。)</p>
            <p>自身でサーバを用意できない場合は、Webサービスを利用する方法もあります。<br />
            Burp Proユーザなら、Burp Collaboratorを使用してみましょう。Burp Proを持っていない方は、<a href="https://webhook.site/">Webhook.site</a>などが挙げられます。</p>
            <div class="bd-callout bd-callout-warning">
                <p class="mb-0">Webサービス提供者にリクエスト情報が知られてしまうため、実際に利用する場合は、信頼できるWebサービスかどうか確認しましょう。</p>
            </div>
        <?php elseif($_POST['hint'] === "4"): ?>
            <p>SSRFの脆弱性を悪用することで、ユーザが直接では到達できないページやバックエンドのシステムにアクセスできる可能性があります。<br />
            例えば、外部からのアクセスを遮断するファイヤーウォールやIPアドレス制限をかけているような状況において、SSRFを悪用することで認証を迂回してアクセスします。</p>
            <p>注意する点として、必ずしもアプリケーションとWebサーバが同じサーバで構成されているとは限りません。<br />
            アクセスに失敗する場合は、内部ネットワークの探索から始めてみましょう</p>
        <?php elseif($_POST['hint'] === "5"): ?>
            <p>デバッグ情報やWebサーバからのメッセージ、HTMLコメントからプライベートIPを探してみましょう。<br />
            プライベートIPから内部のサブネットを推測し、他のホストにアクセスできるか試してみましょう。</p>
        <?php elseif($_POST['hint'] === "6"): ?>
            <p>レスポンスサイズやレスポンスタイムを確認することで内部ネットワークに存在するホストにアクセスできているか確認することができます。</p>
        <?php elseif($_POST['hint'] === "7"): ?>
            <p>各クラウドサービスはインスタンスからのみアクセスできるメタデータを保持しています。<br />
            Webアプリケーションがクラウド上の配置されている場合、インスタンスのクレデンシャル情報が漏えいする可能性があります。</p>
        <?php endif ?>
    <?php endif ?>
</div>
</div>
</body>
</html>