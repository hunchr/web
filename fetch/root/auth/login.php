<?php
$name = isset($_POST['0']) ? strtolower(htmlspecialchars($_POST['0'])) : '';
$pw = isset($_POST['1']) ? $_POST['1'] : '';

if (!preg_match('/^[\w.-]{2,20}$/', $name) || !preg_match('/[^a-z0-9]/i', $pw)) exit('Wrong Password;The password you\'ve entered is incorrect.;Okay');

// Get user data and compare passwords
require '../../.sql.php';
$pepper = '$Ni/yGQ4QgY-y4K1$OaW4/a';
$conn = conn();
$q = $conn -> query('SELECT uid, pw, lang, display FROM users WHERE name = "'.$name.'";');
if ($q -> num_rows === 0) exit('Wrong Username;This username is currently not taken.;Okay');
$q = $q -> fetch_assoc();
if (!password_verify(hash("sha3-512", $pepper.$pw), $q['pw'])) exit('Wrong Password;The password you\'ve entered is incorrect.;Okay');

// Generate auth token
$uid = $q['uid'];
$time = date('Y-m-d H:i:s');
$token = base64_encode($name.';'.$q['lang'].';'.$q['display'].';'.$time.';'.bin2hex(random_bytes(96)));
$conn -> query('INSERT INTO auth VALUES ('.$uid.',"'.password_hash(hash("sha3-512", $token), PASSWORD_ARGON2ID).'","'.$time.'");');
$conn -> close();

// Set cookie to remember user (expires after 2 years)
setrawcookie('auth', $token, time() + 6.307e7, "/");
session_start();
$_SESSION['uid'] = $uid;
?>
