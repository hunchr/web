<?php
$name = isset($_POST['0']) ? strtolower(htmlspecialchars($_POST['0'])) : '';
$mail = isset($_POST['1']) ? strtolower($_POST['1']) : '';
$pw = isset($_POST['2']) ? $_POST['2'] : '';
$conf = isset($_POST['3']) && $_POST['3'] === $pw ? true : false;

// Validate inputs
if (!$conf) exit('Passwords Do Not Match;The passwords do not match.;Okay'); // TODO: lang
if (!preg_match('/^[\w.-]{2,20}$/', $name)) exit('Invalid Username;The username must be between 2 and 20 characters long and can only contain letters (a-z), numbers (0-9), periods (.), underscores (_), or hyphens (-).;Okay');
if (!preg_match('/^[\w!#$%&\'*+\/=?^`{|}~-]+(\.[\w!#$%&\'*+\/=?^`{|}~-]+)*@([a-z0-9-]+\.)+[a-z]{2,24}$/', $mail)) exit('Invalid Email;The email address is not valid.;Okay');
if (!(preg_match('/[^a-z0-9]/i', $pw) && preg_match('/\d/', $pw) && preg_match('/[A-Z]/', $pw) && preg_match('/[a-z]/', $pw) && strlen($pw) > 7)) exit('Invalid Password;The password must be at least 8 characters long and must contain an uppercase and lowercase character, a digit, and a special character.;Okay');

// Check if username is available
require '../../.sql.php';
$conn = conn() -> query('SELECT name FROM users WHERE name = "'.$name.'";');
if ($conn -> num_rows !== 0) exit('Username Taken;This username is already taken by another user.;Okay');
$conn -> close();

// Check if username contains forbidden words // TODO
if (preg_match('/nigger|hitler/', $name)) exit('Username Not Available;todo;Okay');

// Verify email
session_start();
$pin = preg_replace('/\+|\//', 'a', strtolower(base64_encode(random_bytes(6)))); // 1 in 2821109907456 probability to guess correctly
$_SESSION['signup'] = [$pin, $name, $mail, $pw];
// TODO: Send mail with pin
?>
