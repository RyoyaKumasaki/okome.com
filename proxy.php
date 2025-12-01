<?php
// ヘッダーを設定し、クライアントにJSON形式で応答することを通知
header('Content-Type: application/json');

// ==========================================================
// ★【重要】設定エリア
// ==========================================================
// 実際の有効なキーに置き換えてください。
$geminiApiKey = 'AIzaSyCs1t4YnCsuSds1gCvulN7-ud52wzFLtIQ';
$modelName = 'gemini-2.5-pro'; // 利用するモデル名

// 仮のキー（プレースホルダー）
const PLACEHOLDER_KEY = 'YOUR_GEMINI_API_KEY';
// ==========================================================

// ブラウザから送られてきたJSONデータを受け取る
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true);
$userQuestion = $data['question'] ?? null; // null合体演算子で未定義の場合の警告を回避

// 1. APIキーの未設定チェックと仮キーの利用防止
// ハードコードされた仮のキーもチェック対象に追加
if (empty($geminiApiKey) || $geminiApiKey === PLACEHOLDER_KEY) {
    http_response_code(500);
    // JSON_UNESCAPED_UNICODEを追加して日本語の文字化けを防止
    echo json_encode(['error' => 'Gemini APIキーが設定されていません。proxy.phpをご確認ください。'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. ユーザー入力のチェック
if (empty($userQuestion)) {
    http_response_code(400); 
    echo json_encode(['error' => 'Question is required.']);
    exit;
}

// 3. Gemini APIへのリクエストURLを構築
$geminiApiEndpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$geminiApiKey}";

$requestData = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [['text' => "あなたはお米専門のECサイトのAIコンシェルジュです。お米に関する質問に簡潔に、親しみやすい口調で回答してください。\n\n質問: $userQuestion"]]
        ]
    ],
    'generationConfig' => [
        'maxOutputTokens' => 1000,
        'temperature' => 0.7
    ]
];

// 4. stream_context_createを使用したAPI呼び出し設定
$options = [
    'http' => [
        'method' => 'POST',
        // 重要な修正: ヘッダーの末尾に \r\n が必要
        'header' => "Content-Type: application/json\r\n", 
        'content' => json_encode($requestData)
    ]
];
$context = stream_context_create($options);

// APIを呼び出し、応答を取得
$geminiResponse = @file_get_contents($geminiApiEndpoint, false, $context);

// 5. API通信エラーをチェック
if ($geminiResponse === false) {
    // 応答ヘッダーからHTTPステータスコードを抽出
    $errorHeader = $http_response_header[0] ?? 'HTTP/1.1 500 Internal Server Error';
    $httpCode = explode(' ', $errorHeader)[1];

    http_response_code((int)$httpCode); 
    // JSON_UNESCAPED_UNICODEを追加
    echo json_encode(['error' => "API通信エラーが発生しました (Status: {$httpCode})。APIキー、モデル名、または利用制限を確認してください。", 'details' => $errorHeader], JSON_UNESCAPED_UNICODE);
    exit;
}

// 6. JSON応答をデコード
$geminiData = json_decode($geminiResponse, true);

// 7. 応答形式をチェックし、フロントエンドが期待する形式に変換
if (isset($geminiData['candidates'][0]['content']['parts'][0]['text'])) {
    $aiText = $geminiData['candidates'][0]['content']['parts'][0]['text'];
    $responseToFrontend = [
        // フロントエンドのJavaScript（OpenAI互換形式）に合わせる
        'choices' => [
            [
                'message' => [
                    'content' => $aiText
                ]
            ]
        ]
    ];
    // 日本語の文字化けを防止
    echo json_encode($responseToFrontend, JSON_UNESCAPED_UNICODE);
} else {
    // 応答にテキストが含まれていない場合 (例: 不適切なコンテンツとしてブロックされた場合)
    http_response_code(500); 
    // JSON_UNESCAPED_UNICODEを追加
    echo json_encode(['error' => 'Geminiの応答形式が予期せぬものでした。応答がブロックされた可能性があります。', 'details' => $geminiData], JSON_UNESCAPED_UNICODE);
}
?>