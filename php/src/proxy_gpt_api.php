<?php
header("Content-type: text/event-stream");
header("Cache-Control: no-cache");
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // nginxの場合はこれをセット

@ini_set('zlib.output_compression',0);
@ini_set('implicit_flush',1); // これで自動フラッシュになっているはずではある
@ini_set('output_buffering','Off');

function extractContentPart($jsonStr)
{
    $decoded = json_decode($jsonStr, true);
    if (!isset($decoded['choices'][0]['delta']['content'])) {
        return null;
    }
    $content = $decoded['choices'][0]['delta']['content'];
    $content = nl2br($content);
    return $content;
}

function sendSseMessage($ch, $chunk)
{
    if (connection_aborted()) {
        return 0;
    }

    $datas = explode('data: ', $chunk);
    foreach ($datas as $data) {
        $content = extractContentPart($data);

        if ($content != null) {
            // echo "event: message\n";
            echo "data: " . $content . "\n\n";
            ob_flush();
            // ob_end_flush();
            flush();
            // usleep(10000);
        }
    }

    return strlen($chunk);
}

define('OPENAI_API_KEY', getenv('OPENAI_API_KEY'));

$requestData = [
    'model' => 'gpt-3.5-turbo',
    'stream' => true,
    'messages' => [
        [
            'role' => 'user',
            'content' => 'いま京都市内を観光するなら、どこに行くべきですか？'
        ]
    ],
    'temperature' => 0.2
];

$requestHttpHeaders = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . OPENAI_API_KEY
];

$curlOptions = [
    CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => json_encode($requestData),
    CURLOPT_HTTPHEADER => $requestHttpHeaders,
    CURLOPT_WRITEFUNCTION => "sendSseMessage"
];

$ch = curl_init();
curl_setopt_array($ch, $curlOptions);
curl_exec($ch);
curl_close($ch);

echo "event: stop\n";
echo "data: stopped\n\n";

ob_end_flush();
