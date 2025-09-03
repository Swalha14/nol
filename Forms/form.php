<?php

class form
{
    public function signup()
    {
        ?>
        
        <form action="signup_process.php" method="post">
            <h2>Sign Up</h2>
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br><br>
            
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><
    
            
            <input type="submit" value="Sign Up">
        </form>
        <?php
    }
}

?>