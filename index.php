<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
 <title>ChatGPT Clone</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #fef6f9;
      --bubble-user: #d4e3fc;
      --bubble-bot: #fdeacc;
      --input-bg: #fff;
      --text: #333;
      --shadow: rgba(0, 0, 0, 0.08);
      --border: #e0e0e0;
    }
 
    * {
      box-sizing: border-box;
    }
 
    body {
      margin: 0;
      padding: 0;
      font-family: 'Nunito', sans-serif;
      background-color: var(--bg);
    }
 
    .chat-container {
      max-width: 700px;
      margin: 40px auto;
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 24px var(--shadow);
      padding: 24px;
    }
 
    .logout {
      text-align: right;
      margin-bottom: 16px;
      font-size: 14px;
    }
 
    .logout a {
      color: #888;
      text-decoration: none;
    }
 
    h2 {
      margin-top: 0;
      font-weight: 700;
      color: #5d5d5d;
    }
 
    #chat-box {
      max-height: 400px;
      overflow-y: auto;
      padding-right: 8px;
      margin-bottom: 20px;
    }
 
    .message {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin-bottom: 16px;
    }
 
    .avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: 2px solid var(--border);
    }
 
    .bubble {
      padding: 12px 16px;
      border-radius: 14px;
      max-width: 80%;
      font-size: 15px;
      line-height: 1.4;
    }
 
    .user-bubble {
      background-color: var(--bubble-user);
      align-self: flex-end;
    }
 
    .bot-bubble {
      background-color: var(--bubble-bot);
    }
 
    .input-box {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }
 
    input[type="text"] {
      flex: 1;
      padding: 12px;
      border: 1px solid var(--border);
      border-radius: 12px;
      font-size: 15px;
      background-color: var(--input-bg);
    }
 
    button {
      padding: 12px 20px;
      background-color: #a3d8f4;
      color: #333;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      font-weight: bold;
    }
 
    button:hover {
      background-color: #90cbee;
    }
 
    @media (max-width: 600px) {
      .chat-container {
        margin: 20px;
        padding: 16px;
      }
 
      input[type="text"], button {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
 
<div class="chat-container">
  <div class="logout">
    Logged in as <strong><?= htmlspecialchars($_SESSION['user']) ?></strong> |
    <a href="logout.php">Logout</a>
  </div>
 
<h2>💬 ChatGPT Clone</h2>
 
 
  <div id="chat-box">
    <?php
    $stmt = $conn->prepare("SELECT * FROM messages WHERE username = ? ORDER BY id ASC");
    $stmt->execute([$_SESSION['user']]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "
        <div class='message'>
          <img src='user.png' class='avatar' />
          <div class='bubble user-bubble'>{$row['user_input']}</div>
        </div>
        <div class='message'>
          <div class='bubble bot-bubble'>{$row['ai_response']}</div>
        </div>";
    }
    ?>
  </div>
 
  <form id="chat-form">
    <div class="input-box">
      <input type="text" id="user_input" name="user_input" placeholder="Type your message..." required />
      <button type="submit">Send</button>
    </div>
  </form>
</div>
 
<script>
document.getElementById("chat-form").addEventListener("submit", async function(e) {
  e.preventDefault();
  const input = document.getElementById("user_input").value.trim();
  if (!input) return;
 
  const response = await fetch('send.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'user_input=' + encodeURIComponent(input)
  });
 
  const html = await response.text();
  document.getElementById("chat-box").innerHTML = html;
  document.getElementById("user_input").value = '';
 
  // Scroll to bottom
  const box = document.getElementById("chat-box");
  box.scrollTop = box.scrollHeight;
});
 
// Optional voice read-out
function speak(text) {
  const utter = new SpeechSynthesisUtterance(text);
  utter.lang = 'en-US';
  speechSynthesis.speak(utter);
}
 
// Auto-speak latest bot reply
setInterval(() => {
  const botMessages = document.querySelectorAll(".bot-bubble");
  if (botMessages.length > 0) {
    const last = botMessages[botMessages.length - 1].innerText;
    if (!window.lastSpoken || window.lastSpoken !== last) {
      window.lastSpoken = last;
      speak(last);
    }
  }
}, 1000);
</script>
 
</body>
</html>
 
