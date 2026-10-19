<?php
class ChatbotController {
    public function proxy() {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['messages'])) {
            echo json_encode(['content' => [['type' => 'text', 'text' => 'Invalid request.']]]);
            exit();
        }

        // Load API key from environment (.env file)
        $apiKey = $_ENV['GROQ_API_KEY'] ?? '';
        if (empty($apiKey)) {
            echo json_encode(['content' => [['type' => 'text', 'text' => 'API key not configured.']]]);
            exit();
        }

        // Build messages array — Groq uses "user" / "assistant" roles
        $messages = [
            ['role' => 'system', 'content' => $input['system'] ?? '']
        ];
        foreach ($input['messages'] as $msg) {
            $messages[] = [
                'role'    => $msg['role'], // already "user" or "assistant"
                'content' => $msg['content']
            ];
        }

        // Groq shut down all Llama models on Aug 16, 2026 (llama-3.3-70b-versatile included).
        // openai/gpt-oss-120b is Groq's recommended replacement.
        // To switch models later, set GROQ_MODEL in .env instead of editing this file.
        $model = $_ENV['GROQ_MODEL'] ?? 'openai/gpt-oss-120b';

        $payload = json_encode([
            'model'                 => $model,
            'messages'              => $messages,
            'max_completion_tokens' => 1500,
            'temperature'           => 0.7,
            'reasoning_effort'      => 'low', // gpt-oss "thinks" first; low keeps replies fast
        ]);

        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
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

        // Groq returned an error
        if (isset($data['error'])) {
            $errMsg = $data['error']['message'] ?? 'Unknown error';
            error_log('[SilentSignal] Groq error: ' . $errMsg);
            echo json_encode(['content' => [['type' => 'text', 'text' => 'Sorry, the assistant is unavailable right now. For emergencies, use the SOS button or call 911.']]]);
            exit();
        }

        // Extract text from OpenAI-compatible response
        $text = $data['choices'][0]['message']['content'] ?? null;
        if (!$text) {
            error_log('[SilentSignal] Empty Groq response: ' . $response);
            echo json_encode(['content' => [['type' => 'text', 'text' => 'Sorry, I didn\'t get a reply. Please try again.']]]);
            exit();
        }

        http_response_code($httpCode);
        echo json_encode(['content' => [['type' => 'text', 'text' => $text]]]);
        exit();
    }
}
