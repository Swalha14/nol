<?php
require_once 'ClassAutoLoad.php';

// Pre-fill email if passed in query string
$emailPrefill = isset($_GET['email']) ? $_GET['email'] : '';

$message = '';

if (isset($_POST['verify'])) {
    $email = trim($_POST['email']);
    $code  = trim($_POST['code']);

    try {
        $stmt = $conn->prepare("SELECT id, verification_code, code_expires_at 
                                FROM users 
                                WHERE email = :email AND is_verified = 0");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['verification_code'] === $code && strtotime($user['code_expires_at']) > time()) {
                // Mark user as verified
                $update = $conn->prepare("UPDATE users 
                                          SET is_verified = 1, verification_code = NULL, code_expires_at = NULL 
                                          WHERE id = :id");
                $update->execute([':id' => $user['id']]);

                $message = "<p style='color:green;'> Your account has been verified! You can now <a href='signin.php'>log in</a>.</p>";
            } else {
                $message = "<p style='color:red;'> Invalid or expired code. Please try again.</p>";
            }
        } else {
            $message = "<p style='color:red;'> No unverified account found with this email.</p>";
        }
    } catch (PDOException $e) {
        $message = "<p style='color:red;'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Account</title>
</head>
<body>
    <h2>Verify Your Account</h2>
    <p>We’ve sent a 6-digit code to your email. Enter it below to activate your account:</p>

    <?php if (!empty($message)) echo $message; ?>

    <form method="POST" action="verify.php">
        <input type="email" name="email" placeholder="Your Email" value="<?php echo htmlspecialchars($emailPrefill); ?>" required><br><br>
        <input type="text" name="code" placeholder="Verification Code" required><br><br>
        <button type="submit" name="verify">Verify</button>
    </form>
</body>
</html>
