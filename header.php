<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <style>
    body{
        background-color: #FFFFEB;
    }
    .custom-green-navbar {
    background-color: #9CEF36;
    }
    .custom-green-navbar a{
        color: #fff;
    }
    .custom-green-hero {
    background-color: #8AE51A;
    }
    .ai-chat-container {
            margin-top: 50px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .ai-chat-container h3 {
            color: #1a73e8;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .chat-area {
            height: 200px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            overflow-y: auto;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        .user-message {
            text-align: right;
            color: #007bff;
        }
        .ai-message {
            text-align: left;
            color: #333;
        }
        .chat-input {
            display: flex;
            gap: 10px;
        }
        .chat-input textarea {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
        }
        .chat-input button {
            background-color: #1a73e8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .chat-input button:hover {
            background-color: #165ab7;
        }
    </style>
    <title>
        <?php echo isset($page_title) ? $page_title . ' | お米ドットコム' : 'お米ドットコム'; ?>
    </title>
</head>
<body>