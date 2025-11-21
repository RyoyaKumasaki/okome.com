<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <style>
    .custom-blue-navbar {
    background-color: #36E6EF;
    }
    .custom-blue-navbar a{
        color: #fff;
    }
    .custom-blue-hero {
    background-color: #27D1FB;
    }
    html {
        /* ページの高さをビューポートの高さに合わせる */
        height: 100%; 
    }
    body {
        /* Flexコンテナとし、子要素を縦方向に配置 */
        display: flex;
        flex-direction: column;
        min-height: 100vh; /* 最小高さをビューポートの高さに設定 */
        margin: 0; /* bodyのデフォルトマージンをリセット */
    }
    /* メインコンテンツ部分が領域を最大限占めるようにする */
    main {
        flex-grow: 1;
    }
    </style>
    <title>
        <?php echo isset($page_title) ? $page_title . ' | お米ドットコム' : 'お米ドットコム'; ?>
    </title>
</head>
<body style="background-color: #FFFFEB;">
    <main>