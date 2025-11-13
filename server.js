const express = require('express');
const cors = require('cors');

const app = express();
const PORT = 3000;

// ★【重要】ここにGoogle Gemini APIキーを設定してください
const GEMINI_API_KEY = 'AIzaSyCBW3vAWtkoLflv375Sudy6_fSiJCAebQg';

// CORSとExpressの組み込みミドルウェアを有効化
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// 同じフォルダにあるindex.htmlを公開する
app.use(express.static('.'));

// AIチャットのプロキシエンドポイント
app.post('/api/chat', async (req, res) => {
    const userQuestion = req.body.question;

    if (!GEMINI_API_KEY || GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY') {
        console.error("API Error: Gemini API key is not set or is the placeholder.");
        return res.status(500).json({ error: 'Gemini APIキーが設定されていません。server.jsをご確認ください。' });
    }
    if (!userQuestion) {
        return res.status(400).json({ error: 'Question is required.' });
    }

    try {
        console.log('Sending question to Gemini:', userQuestion);

        const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key=${GEMINI_API_KEY}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                contents: [{
                    role: 'user',
                    parts: [{ text: `あなたはお米専門のECサイトのAIコンシェルジュです。お米に関する質問に簡潔に、親しみやすい口調で回答してください。\n\n質問: ${userQuestion}` }]
                }],
                generationConfig: {
                    maxOutputTokens: 1000,
                    temperature: 0.7
                }
            })
        });

        if (!response.ok) {
            const errorDetails = await response.text();
            console.error('Error from Gemini API:', response.status, errorDetails);
            return res.status(response.status).json({ error: 'Gemini APIからのエラー: ' + errorDetails });
        }

        const data = await response.json();
        console.log('Response from Gemini:', JSON.stringify(data, null, 2));

        if (data && data.candidates && data.candidates.length > 0 && 
            data.candidates[0].content && data.candidates[0].content.parts && 
            data.candidates[0].content.parts.length > 0 && data.candidates[0].content.parts[0].text) {
            
            const aiResponse = {
                choices: [{
                    message: {
                        content: data.candidates[0].content.parts[0].text
                    }
                }]
            };
            res.json(aiResponse);
        } else {
            console.error('Unexpected Gemini response format or missing content:', data);
            res.status(500).json({ error: 'Geminiの応答形式が予期せぬものでした。' });
        }

    } catch (error) {
        console.error('Proxy Error during Gemini API call:', error);
        res.status(500).json({ error: 'Gemini APIとの通信中に予期せぬエラーが発生しました。詳細はサーバーログを確認してください。' });
    }
});

app.listen(PORT, () => {
    console.log(`サーバーが起動しました: http://localhost:${PORT}`);
});