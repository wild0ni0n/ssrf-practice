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
    <form action="hints.php" method="post">
        <div class="form-group">
            <label for="level">見たいヒントのHinを指定してください。</label>
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
            <p>インターネット上に公開されたドメインを持っていない場合は、Webサービスを利用しましょう<br />
            Burp Proユーザなら、Burp Collaboratorを使用してみましょう。Burp Proを持っていない方は、<a href="https://webhook.site/">Webhook.site</a>や<a href="https://postb.in/">PostBin</a>を利用しましょう。</p>
            <div class="bd-callout bd-callout-warning">
                <p class="mb-0">Webサービス提供者にリクエスト情報が知られてしまうため、実際の診断では信頼できるWebサービスかどうか確認しましょう。</p>
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
</body>
</html>