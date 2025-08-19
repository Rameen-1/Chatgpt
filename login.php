<?php
session_start();
include 'db.php';
 
$message = "";
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
 
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $message = "🚫 Invalid username or password!";
    }
}
?>
 
<!DOCTYPE html>
<html>
<head>
    <title>Login - RawrBot</title>
    <style>
        body { font-family: sans-serif; background: #eef; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #aaa; }
        input { margin-bottom: 10px; padding: 10px; width: 100%; border-radius: 5px; border: 1px solid #ccc; }
        button { padding: 10px; width: 100%; background: #1a73e8; color: white; border: none; border-radius: 5px; }
        .msg { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Login to RawrBot</h2>
        <div class="msg"><?= $message ?></div>
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
        <p>New here? <a href="signup.php">Create an account</a></p>
    </form>
</body>
</html>
 
