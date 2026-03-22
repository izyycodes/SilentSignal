<?php
class ChatbotController {
    public function proxy() {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['messages'])) {
            echo json_encode(['content' => [['type' => 'text', 'text' => 'Invalid request.']]]);
            exit();
        }

        $apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
        if (empty($apiKey)) {
            echo json_encode(['content' => [['type' => 'text', 'text' => 'API key not configured.']]]);
            exit();
        }

        $contents = [];
        foreach ($input['messages'] as $msg) {
            $contents[] = [
                'role'  => $msg['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg['content']]]
            ];
        }

        $payload = json_encode([
            'system_instruction' => [
                'parts' => [['text' => $input['system'] ?? '']]
            ],
            'contents'         => $contents,
            'generationConfig' => [
                'maxOutputTokens' => 1000,
                'temperature'     => 0.7,
            ]
        ]);

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // cURL failed completely
        if ($response === false || empty($response)) {
            echo json_encode(['content' => [['type' => 'text', 'text' => 'Could not reach AI service. cURL error: ' . $curlError]]]);
            exit();
        }

        $data = json_decode($response, true);

        // Gemini returned an error
        if (isset($data['error'])) {
            $msg = $data['error']['message'] ?? 'Unknown error';
            echo json_encode(['content' => [['type' => 'text', 'text' => 'Gemini error: ' . $msg]]]);
            exit();
        }

        // Extract text
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) {
            echo json_encode(['content' => [['type' => 'text', 'text' => 'No response from AI. Raw: ' . $response]]]);
            exit();
        }

        http_response_code($httpCode);
        echo json_encode(['content' => [['type' => 'text', 'text' => $text]]]);
        exit();
    }
}