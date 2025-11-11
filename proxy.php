<?php
header('Content-Type: application/json');

// ★ここにあなたのGemini APIキーを設定してください
// 実際の有効なキーに置き換えてください。
$geminiApiKey = 'AIzaSyCBW3vAWtkoLflv375Sudy6_fSiJCAebQg'; // 仮のキー。ご自身の有効なキーに置き換えてください。

// ブラウザから送られてきたJSONデータを受け取る
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true);
$userQuestion = $data['question'];

if (empty($geminiApiKey) || $geminiApiKey === 'YOUR_GEMINI_API_KEY') {
    http_response_code(500);
    echo json_encode(['error' => 'Gemini APIキーが設定されていません。proxy.phpをご確認ください。']);
    exit;
}

if (empty($userQuestion)) {
    http_response_code(400); // ユーザーからの入力エラーは400 Bad Requestが適切
    echo json_encode(['error' => 'Question is required.']);
    exit;
}

// Gemini APIへのリクエスト
// ★★★ここをあなたのAPIキーで利用可能なモデル名に変更してください★★★
// 例: gemini-1.0-pro, text-bison-001 など
$geminiApiEndpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key=$geminiApiKey";
$requestData = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [['text' => "あなたはお米専門のECサイトのAIコンシェルジュです。お米に関する質問に簡潔に、親しみやすい口調で回答してください。\n\n質問: $userQuestion"]]
        ]
    ],
    'generationConfig' => [
        'maxOutputTokens' => 1000, // 応答の最大トークン数
        'temperature' => 0.7
    ]
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($requestData)
    ]
];
$context = stream_context_create($options);

// APIを呼び出し、応答を取得
// @を付けてfile_get_contentsのエラー出力を抑制し、手動で処理
$geminiResponse = @file_get_contents($geminiApiEndpoint, false, $context);

// API通信エラーをチェック
if ($geminiResponse === false) {
    // 応答ヘッダーを取得して、Geminiからのエラーコードを特定
    // $http_response_headerはfile_get_contents後に自動的に設定されるグローバル変数
    $errorHeader = $http_response_header[0] ?? 'HTTP/1.1 500 Internal Server Error';
    $httpCode = explode(' ', $errorHeader)[1]; // "500" の部分を抽出

    // クライアントに適切なHTTPステータスコードを返す
    http_response_code((int)$httpCode); 
    echo json_encode(['error' => "API通信エラーが発生しました (Status: $httpCode)。Gemini APIのURL、APIキー、利用可能なモデル名を確認してください。", 'details' => $errorHeader]);
    exit;
}

// JSON応答をデコード
$geminiData = json_decode($geminiResponse, true);

// 応答形式をチェックし、フロントエンドが期待する形式に変換
if (isset($geminiData['candidates'][0]['content']['parts'][0]['text'])) {
    $aiText = $geminiData['candidates'][0]['content']['parts'][0]['text'];
    $responseToFrontend = [
        'choices' => [
            [
                'message' => [
                    'content' => $aiText
                ]
            ]
        ]
    ];
    echo json_encode($responseToFrontend);
} else {
    // 応答にテキストが含まれていない場合の詳細ログ
    http_response_code(500); // サーバー内部での応答処理の問題として500を返す
    echo json_encode(['error' => 'Geminiの応答形式が予期せぬものでした。詳細: ' . json_encode($geminiData, JSON_UNESCAPED_UNICODE)]);
}
?>