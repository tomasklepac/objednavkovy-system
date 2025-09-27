<?php
$pwd = $_GET['pwd'] ?? 'Test123!';
echo password_hash($pwd, PASSWORD_BCRYPT);
