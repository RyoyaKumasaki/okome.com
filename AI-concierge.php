<div class="container ai-chat-container">
    <h3>AIお米コンシェルジュ</h3>
    <div class="chat-area" id="chat-area">
        <p class="ai-message">いらっしゃいませ！お米について何でもお尋ねください。おすすめの品種やレシピ、炊き方など、お答えします。</p>
    </div>
    <div class="chat-input">
        <textarea id="user-input" rows="3" placeholder="質問を入力してください..."></textarea>
        <button onclick="sendQuestion()">AIに質問する</button>
    </div>
</div>
<script>
    async function getAiResponse(question) {
        const API_ENDPOINT = 'proxy.php';

        try {
            const response = await fetch(API_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ question: question }),
            });

            if (!response.ok) {
                const errorData = await response.json();
                console.error('APIエラー:', errorData);
                return 'AIとの通信中にエラーが発生しました。';
            }

            const data = await response.json();
            const aiText = data.choices[0].message.content;
            return aiText;
        } catch (error) {
            console.error('ネットワークエラー:', error);
            return 'ネットワークに問題が発生しました。';
        }
    }

    async function sendQuestion() {
        const userInput = document.getElementById('user-input');
        const chatArea = document.getElementById('chat-area');
        const question = userInput.value.trim();
        if (!question) {
            return;
        }
        chatArea.innerHTML += `<p class="user-message">あなた: ${question}</p>`;
        userInput.value = '';

        chatArea.scrollTop = chatArea.scrollHeight;

        chatArea.innerHTML += `<p class="ai-message">AIお米コンシェルジュ: ...考え中</p>`;
        chatArea.scrollTop = chatArea.scrollHeight;

        const aiResponse = await getAiResponse(question);

        const thinkingMessage = chatArea.querySelector('.ai-message:last-child');
        if (thinkingMessage) {
            thinkingMessage.textContent = `AIお米コンシェルジュ: ${aiResponse}`;
        }

        chatArea.scrollTop = chatArea.scrollHeight;
    }
</script>