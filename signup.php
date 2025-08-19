<?php
session_start();
include 'db.php';
 
$message = "";
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
 
    if ($username && $password) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
 
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
 
        if ($stmt->rowCount() > 0) {
            $message = "🚫 Username already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed]);
            $_SESSION['user'] = $username;
            header("Location: index.php");
            exit;
        }
    } else {
        $message = "⚠️ All fields required!";
    }
}
?>
 
<!DOCTYPE html>
<html>
<head>
    <title>Signup - RawrBot</title>
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
        <h2>Sign up for RawrBot</h2>
        <div class="msg"><?= $message ?></div>
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Sign Up</button>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
</body>
</html>
 
 
