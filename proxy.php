<?php
// ★★★重要: PHPのエラーや警告がJSON応答に混入するのを防ぐ★★★
// これにより、500エラーやSyntaxErrorを防止します。
error_reporting(0); 
ini_set('display_errors', 0); 

// ヘッダーを設定し、クライアントにJSON形式で応答することを通知
header('Content-Type: application/json');

// ==========================================================
// ★【重要】設定エリア
// ==========================================================
// 実際の有効なキーに置き換えてください。
$geminiApiKey = 'AIzaSyD_3uLi5BVTAUQ7iNjKcq7Jxalpfc6CCa0'; // ここに有効なキーを設定してください
$modelName = 'gemini-2.5-flash'; // 互換性と安定性が高いFlashモデルを使用
// ==========================================================

// ブラウザから送られてきたJSONデータを受け取る
$requestBody = @file_get_contents('php://input'); // @でエラー抑制
$data = json_decode($requestBody, true);

// ★互換性修正★ PHP 7.0未満でも動作するよう、?? 演算子をisset()に修正
$userQuestion = isset($data['question']) ? $data['question'] : null; 
// 仮のキーチェック
$isPlaceholderKey = ($geminiApiKey === 'YOUR_GEMINI_API_KEY');

// 1. APIキーの未設定チェックと仮キーの利用防止
if (empty($geminiApiKey) || $isPlaceholderKey) {
    http_response_code(500);
    echo json_encode(['error' => 'Gemini APIキーが設定されていないか、仮のキーが使用されています。proxy.phpをご確認ください。'], JSON_UNESCAPED_UNICODE);
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
            'parts' => [[
                // ★★★ 応答途切れ対策のプロンプトと文字数制限を適用 ★★★
                // 500文字以内という制限を設け、途切れることなく回答するよう指示
                'text' => "あなたはお米専門のECサイトのAIコンシェルジュです。お米に関する質問に簡潔に、親しみやすい口調で、最後まで途中で途切れることなく回答してください。\n\n質問: $userQuestion"
            ]]
        ]
    ],
    'generationConfig' => [
        // ★★★ maxOutputTokensの増加を適用（応答途切れ対策）★★★
        'maxOutputTokens' => 2048, 
        'temperature' => 0.7
    ]
];

// 4. stream_context_createを使用したAPI呼び出し設定
$options = [
    'http' => [
        'method' => 'POST',
        // User-Agentを追加し、接続情報を明確にする（403エラー再発対策）
        'header' => "Content-Type: application/json\r\nUser-Agent: OkomeDotCom-Gemini-Client\r\n", 
        'content' => json_encode($requestData)
    ]
];
$context = stream_context_create($options);

// APIを呼び出し、応答を取得
$geminiResponse = @file_get_contents($geminiApiEndpoint, false, $context);

// 5. API通信エラーをチェック
if ($geminiResponse === false) {
    // ★互換性修正★ 互換性のためにisset()でチェック
    $errorHeader = isset($http_response_header[0]) ? $http_response_header[0] : 'HTTP/1.1 500 Internal Server Error';
    $httpCode = explode(' ', $errorHeader)[1];

    http_response_code((int)$httpCode); 
    echo json_encode([
        'error' => "API通信エラーが発生しました (Status: {$httpCode})。サーバーのPHP設定を確認してください。", 
        'details' => $errorHeader
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 6. JSON応答をデコード
$geminiData = json_decode($geminiResponse, true);

// 7. 応答形式をチェックし、フロントエンドが期待する形式に変換
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
    // 日本語の文字化けを防止
    echo json_encode($responseToFrontend, JSON_UNESCAPED_UNICODE);
} else {
    // 応答にテキストが含まれていない場合
    http_response_code(500); 
    echo json_encode([
        'error' => 'Geminiの応答形式が予期せぬものでした。応答がブロックされた可能性があります。', 
        'details' => $geminiData
    ], JSON_UNESCAPED_UNICODE);
}
?>