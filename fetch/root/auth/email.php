<?php
// Verify pin
session_start();
if (!isset($_SESSION['signup'], $_POST['0'])) exit('no pin');
$data = $_SESSION['signup'];
$pepper = '$Ni/yGQ4QgY-y4K1$OaW4/a';
if (strlen($_POST['0']) !== 8 || strtolower($_POST['0']) !== $data[0]) exit('Invalid Pin'.'::'.$data[0]); // TODO: delete ".'::'.$data[0]"

// Create account
require '../../.sql.php';
$conn = conn();
$conn -> query('INSERT INTO subs (display_name) VALUES ("@'.$data[1].'");'); // TODO?: htmlspecialchars
$uid = $conn -> query('SELECT id from subs WHERE display_name = "@'.$data[1].'" LIMIT 1;');
if ($uid -> num_rows === 0) exit('fatal error, mega fail');
$uid = $uid -> fetch_assoc()['id'];
$conn -> query('INSERT INTO users (uid, name, email, pw) VALUES ('.$uid.',"'.$data[1].'","'.$data[2].'","'.password_hash(hash("sha3-512", $pepper.$data[3]), PASSWORD_ARGON2ID).'");');
$conn -> close();

// Create directory
$dir = '../../../uc/s/';
mkdir($dir.$uid, 0777, true);
copy($dir.'0/0.webp', $dir.$uid.'/0.webp');
session_destroy();
?>
