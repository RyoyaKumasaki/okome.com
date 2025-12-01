<?php
// PHPコードのエラーや警告がブラウザに出力されるのを抑制します。
error_reporting(0); 

// ウェブページの基本的なHTML構造
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>サーバーIPアドレス確認</title>
    <!-- Bulma CSSを読み込み、既存のデザインに合わせます -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #f7f7f7;
            padding: 40px;
            text-align: center;
        }
        .ip-box {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .ip-address {
            font-size: 2rem;
            font-weight: bold;
            color: #1a73e8;
            word-break: break-all;
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="ip-box">
        <h1 class="title is-4">ロリポップサーバーの外部IPアドレス</h1>
        <p class="subtitle is-6">このアドレスをGoogle Cloud ConsoleのAPIキー制限に設定してください。</p>
        
        <?php
        // 外部サービスを利用して、このサーバーの外部アクセス用IPアドレスを取得します
        // file_get_contentsは外部URLの取得に最も簡単な方法です。
        $external_ip = @file_get_contents('http://ifconfig.co/ip');
        
        if ($external_ip === false) {
            // 取得失敗
            echo '<div class="notification is-danger">';
            echo '外部IPアドレスの取得に失敗しました。一時的なネットワークの問題か、ロリポップの管理画面からサーバー情報をご確認ください。';
            echo '</div>';
        } else {
            // 取得成功
            $ip_address = trim($external_ip);
            echo '<div class="notification is-info">';
            echo 'このサーバーがGoogle APIへのアクセスに使用するIPアドレスは:';
            echo '</div>';
            echo '<p class="ip-address">' . htmlspecialchars($ip_address) . '</p>';
            echo '<p class="mt-4">👆 **このIPアドレス**をコピーして、Google Cloud ConsoleのAPIキー制限（アプリケーションの制限 → IPアドレス）に設定してください。</p>';
        }
        ?>
    </div>
</body>
</html>