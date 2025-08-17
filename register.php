<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'includes/db_connect.php'; // Database connection
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role']; // 'Admin' or 'User'

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role]);

    echo "User registered successfully!";
}
?>
<form method="POST" action="">
    <input type="text" name="username" placeholder="Username" required />
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <select name="role">
        <option value="User">User</option>
        <option value="Admin">Admin</option>
    </select>
    <button type="submit">Register</button>
</form>
