<div class="container ai-chat-container">
    <h3 class="title is-4 has-text-centered">AIお米コンシェルジュ</h3>
    
    <!-- チャットエリア -->
    <div class="chat-area" id="chat-area">
        <!-- 初期メッセージ -->
        <div class="ai-message-wrapper">
            <div class="ai-message">
                いらっしゃいませ！お米について何でもお尋ねください。おすすめの品種やレシピ、炊き方など、お答えします。
            </div>
        </div>
    </div>

    <!-- 入力エリア -->
    <div class="chat-input">
        <textarea id="user-input" rows="3" placeholder="質問を入力してください..."></textarea>
        <button id="send-button" onclick="sendQuestion()">AIに質問する</button>
    </div>
</div>

<script>
    // ★PHPプロキシファイルを参照
    const API_ENDPOINT = 'proxy.php'; 
    
    // 連続送信防止用のフラグ
    let isSending = false;

    /**
     * Gemini APIとの通信を行う
     * @param {string} question - ユーザーからの質問
     * @returns {Promise<string>} AIからの応答テキスト
     */
    async function getAiResponse(question) {
        try {
            const response = await fetch(API_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ question: question }),
            });

            if (!response.ok) {
                let errorData;
                try {
                    errorData = await response.json();
                } catch (e) {
                    // JSONデコードに失敗した場合（PHPエラーのHTMLなどが返ってきた場合）
                    const rawText = await response.text();
                    console.error('JSONデコードエラー (サーバーがJSON以外を返しました):', rawText);
                    return `🚨サーバーエラー (HTTP ${response.status})。proxy.phpが不正な応答を返しました。proxy.phpのコードを確認してください。`;
                }

                console.error('APIエラー (HTTP Status ' + response.status + '):', errorData);
                // サーバーからのエラーメッセージを表示 (proxy.phpからのerrorフィールドを想定)
                return `AIとの通信中にエラーが発生しました。詳細: ${errorData.error || '不明なエラー'}`;
            }

            // 正常応答をJSONとしてパース
            const data = await response.json();
            
            // 応答形式のチェック
            if (data && data.choices && data.choices.length > 0 && data.choices[0].message && data.choices[0].message.content) {
                return data.choices[0].message.content;
            } else {
                console.error('予期せぬAPI応答形式:', data);
                return 'AIからの応答形式が不正です。proxy.phpの応答処理を確認してください。';
            }

        } catch (error) {
            // ネットワークエラー、またはJSONパースエラー（SyntaxError）
            console.error('ネットワーク/パースエラー:', error);
            
            // JSONパースエラーの場合の専用メッセージ
            if (error instanceof SyntaxError) {
                return 'サーバー応答の解析に失敗しました。サーバーが不正なJSONを返しました。';
            }
            
            return 'ネットワーク接続に問題が発生しました。';
        }
    }

    /**
     * 質問を送信し、チャットエリアを更新する
     */
    async function sendQuestion() {
        if (isSending) return; // 二重送信防止
        
        const userInput = document.getElementById('user-input');
        const chatArea = document.getElementById('chat-area');
        const sendButton = document.getElementById('send-button');
        const question = userInput.value.trim();
        
        if (!question) {
            return;
        }
        
        // 状態を送信中に変更し、ボタンを無効化
        isSending = true;
        sendButton.disabled = true;

        // 1. ユーザーメッセージを表示
        chatArea.innerHTML += `<div class="user-message-wrapper"><div class="user-message">${question}</div></div>`;
        userInput.value = '';
        
        chatArea.scrollTop = chatArea.scrollHeight;

        // 2. 「考え中」メッセージを表示
        // .ai-thinkingクラスで簡単に識別できるようにする
        chatArea.innerHTML += `<div class="ai-message-wrapper ai-thinking-wrapper"><div class="ai-message ai-thinking">AIお米コンシェルジュ: ...考え中</div></div>`;
        chatArea.scrollTop = chatArea.scrollHeight;

        let aiResponse = '';
        try {
            // 3. AI応答の取得
            aiResponse = await getAiResponse(question);
        } catch (e) {
            aiResponse = '予期せぬ致命的なエラーが発生しました。';
            console.error("Critical error during send:", e);
        } finally {
            // 4. 「考え中」メッセージを応答に置き換え
            const thinkingWrapper = chatArea.querySelector('.ai-thinking-wrapper:last-child');
            const thinkingMessage = thinkingWrapper ? thinkingWrapper.querySelector('.ai-thinking') : null;
            
            if (thinkingMessage) {
                const isError = aiResponse.includes('エラー') || aiResponse.includes('SyntaxError') || aiResponse.includes('🚨');
                
                if (isError) {
                    // エラー応答の場合、赤色で表示
                    thinkingMessage.style.backgroundColor = '#fbecec';
                    thinkingMessage.style.color = '#c62828';
                    thinkingMessage.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${aiResponse}`; // アイコン追加
                } else {
                    // 正常な応答の場合は、AIの名前を追記
                    thinkingMessage.textContent = aiResponse;
                }
                
                thinkingMessage.classList.remove('ai-thinking');
                thinkingWrapper.classList.remove('ai-thinking-wrapper');
            }
            
            // 5. 状態をリセット
            isSending = false;
            sendButton.disabled = false;
            chatArea.scrollTop = chatArea.scrollHeight;
        }
    }
</script>