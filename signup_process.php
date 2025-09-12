<?php
/*
require_once 'ClassAutoLoad.php';

// Check if signup button was pressed
if (isset($_POST['signup'])) {
    $username = $_POST['username'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // (Optional) Save user in database here...

    // 2. Prepare mail content
    $mailContent = [
        'name_from'  => 'ICS C Community',
        'email_from' => 'no-reply@icscommunity.com',
        'name_to'    => $username,   // Personalize greeting
        'email_to'   => $email,      // User’s email
        'subject'    => 'Welcome to ICS 2.2! Account Verification',
        'body'       => "
            <p>Hello <b>$username</b>,</p>
            <p>You requested an account on ICS 2.2.</p>
            <p>In order to use this account you need to 
               <a href='http://yourdomain.com/verify.php?email=$email'>Click Here</a> 
               to complete the registration process.</p>
            <br>
            <p>Regards,<br>Systems Admin<br>ICS 2.2</p>
        "
    ];

    // 3. Send email
    $ObjSendMail->Send_Mail($conf, $mailContent);
}
    */

require_once 'ClassAutoLoad.php';   // loads $conf, $ObjSendMail, $conn, etc.

// Check if the signup form was submitted
if (isset($_POST['signup'])) {
    // Collect form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required!");
    }

    try {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() > 0) {
            die("This email is already registered. Please use another one.");
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) 
                                VALUES (:username, :email, :password)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);

        // Prepare welcome email
        $mailContent = [
            'name_from'  => $conf['site_name'],
            'email_from' => $conf['smtp_user'],  // your Gmail sender
            'name_to'    => $username,
            'email_to'   => $email,
            'subject'    => 'Welcome to ' . $conf['site_name'],
            'body'       => "Hi $username,<br><br>
                             Welcome to <b>{$conf['site_name']}</b>!<br>
                             This is a new semester, let’s make the most of it.<br><br>
                             Regards,<br>{$conf['site_name']} Team"
        ];

        // Send the email
        $ObjSendMail->Send_Mail($conf, $mailContent);

        echo " Registration successful! A welcome email has been sent to $email";

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    die("Invalid request.");
}

