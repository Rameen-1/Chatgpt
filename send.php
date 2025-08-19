<?php
session_start();
include 'db.php';
 
$user_input = $_POST['user_input'] ?? '';
$username = $_SESSION['user'] ?? 'guest';
 
function getAIResponse($input) {
    $apiKey = 'AIzaSyB8Ep3xl7IPNWR-Q0eoWFtNDhtlM0PH9zM';
 
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";
 
    $postData = json_encode([
        "contents" => [
            [
                "parts" => [
                    ["text" => $input]
                ]
            ]
        ]
    ]);
 
    $headers = ["Content-Type: application/json"];
 
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
 
    $response = curl_exec($ch);
 
    if (curl_errno($ch)) {
        return "⚠️ CURL Error: " . curl_error($ch);
    }
 
    $result = json_decode($response, true);
 
    if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return "⚠️ Gemini Error: " . htmlspecialchars($response);
    }
 
    return $result['candidates'][0]['content']['parts'][0]['text'];
}
 
if ($user_input) {
    $ai_response = getAIResponse($user_input);
 
    // Save to DB with username
    $stmt = $conn->prepare("INSERT INTO messages (username, user_input, ai_response) VALUES (?, ?, ?)");
    $stmt->execute([$username, $user_input, $ai_response]);
 
    // Load only current user's messages
    $stmt = $conn->prepare("SELECT * FROM messages WHERE username = ? ORDER BY id ASC");
    $stmt->execute([$username]);
 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "
        <div class='message'>
          <img src='user.png' class='avatar' />
          <span class='user'>You:</span> {$row['user_input']}
        </div>
        <div class='message'>
          <img src='bot.png' class='avatar' />
          <span class='bot'>Bot:</span> {$row['ai_response']}
        </div>";
    }
}
?>
