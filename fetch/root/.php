<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['uid'])) exit('ERR_NO_USER_ID'); // TODO

$q = isset($_POST['q']) ? strtolower($_POST['q']) : '';
$uid = $_SESSION['uid'];
$lang = $_SESSION['lang'];
$fetch = '';

// ----- Login required -----
if (isset($login_req) && $uid === 0) {
    require 'login.php';
    exit();
}

// ----- Language -----
preg_match('/\/[^\/]+\/[^\/]+$/', $_SERVER['SCRIPT_NAME'] , $path);
require '../../lang/'.$lang.$path[0];

// ----- Errors -----
// Error 404: Not found
function e404() {
    require '../../err/404.php';
    exit();
};

// ----- Feed -----
// Connect to mySQL
function conn($no_rows, $is_main, $fun) {
    global $q;

    $conn = new mysqli('localhost', 'root', 'qumbyv-nohrin-Vyhhy7', 'sm', 3306);
    if ($conn -> connect_error) eof('you got lost in space', $is_main);
    
    $q = $conn -> query($q);
    if ($q -> num_rows === 0) {
        if ($no_rows === false) {
            e404();
        }
        else {
            eof($no_rows, $is_main);
        }
    }

    while ($r = $q -> fetch_assoc()) {
        $fun($r);
    }

    $conn -> close();
};

// End of feed
function eof($msg, $is_main) {
    global $fetch;
    $msg = '<div class="p end">Looks like '.$msg.'</div>';
    
    exit($is_main ? '<main class="center">'.$msg.'</main>' : $fetch.$msg);
};

// Format number (e.g. 1000 => 1.0K)
function fnumber($n) {
    return
         $n < 1e3 ? $n:
        ($n < 1e6 ? (floor($n / 100) / 10).'K':
        (floor($n / 1e4) / 100).'M');
};

// Format string (i.e. make links clickable)
function fstring($n) {
    // $n = preg_replace('/\B[uc]\/[\w.-]+\b/', '<button data-f="z">$0</button>', $n); // Users/Communities etc.
    // $n = preg_replace('/\B[a-z]\/\d+\b/', '<button data-f="z">$0</button>', $n); // Lists/Posts etc.
    $n = preg_replace('/https:\/\/(www\.)?([^ ]+)/', '<a href="$0">$2</a>', $n); // Links
    return $n;
};

// Format date
function fdate($n) {
    $n = time() - $n;
    return
        $n > 31536e3 ? floor($n / 31536e3).'y' :
        ($n > 86400 ? floor($n / 86400).'d' :
        ($n > 3600 ? floor($n / 3600).'h' :
        floor($n / 60).'min'));
};
?>
