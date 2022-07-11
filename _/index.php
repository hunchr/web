<?php
$q = isset($_GET['q']) ? $_GET['q'] : false;

// Redirect
if (!$q) {
    require '../index.php';
}
else if (!strpos($q, '/')) {
    require '../sm/index.php';
}
else {
    if (preg_match('/\.php$/', $q)) {
        require 'errors/404.php';
        exit();
    }

    echo 'TODO: other app = '.$q;
}

// HTML template
function html($base, $description, $keywords, $available_langs, $css, $js) {
    global $q;

    // ----- CSP -----
    $nonce = base64_encode(random_bytes(18));

    header('Content-Type: text/html; charset=utf-8');
    header('Content-Security-Policy: default-src \'self\'');
    header('Content-Security-Policy: script-src \'nonce-'.$nonce.'\' \'strict-dynamic\' \'unsafe-eval\' \'unsafe-inline\'');

    // ----- Language -----
    session_start();

    $uid = 0;
    $preferences = 'data-base="'.$base.'"';

    // User
    if (isset($_COOKIE['auth'])) {
        $auth = explode(';', base64_decode($_COOKIE['auth']), 5); // [username; lang; light, show-nsfw; date; token]
        
        if (preg_match('/light/', $auth[2])) $preferences .= ' data-light';
        if (preg_match('/nsfw/', $auth[2])) $preferences .= ' data-nsfw';

        // Log in
        if (!isset($_SESSION['uid'])) {
            exit('TODO: Logging in..');

            $_SESSION['uid'] = $uid; // TODO

        }
    }
    // Guest
    else {
        preg_match_all('/[a-z]+(?=;)/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $preferred_langs);
        $auth = ['guest', $preferred_langs[0]]; // [username; lang]
    }

    // Set language
    $_SESSION['lang'] = in_array($auth[1], $available_langs) ? $auth[1] : 'en';

    // ----- HTML -----
    $out = '
    <!DOCTYPE html>
    <html lang="'.$_SESSION['lang'].'" data-name="'.htmlspecialchars($auth[0]).'" '.$preferences.'>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
        <title>$title</title>
        <meta name="description" content="'.$description.'">
        <meta name="keywords" content="hunchr,'.$keywords.','.$q.'">
        <meta name="theme-color" content="#000000">
        <meta name="referrer" content="no-referrer">
        <meta name="robots" content="index,follow">
        <meta property="og:title" content="$title">
        <meta property="og:description" content="'.$description.'">
        <meta property="og:url" content="http://localhost/'.$base.'">
        <meta property="og:image" content="http://localhost/_/img/open-graph.png">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="hunchr">
        <meta name="twitter:card" content="summary">
    ';

    foreach ($css as $url) {
        $out .= '<link rel="stylesheet" href="/'.$url.'.css">';
    }
    
    foreach ($js as $url) {
        $out .= '<script src="/'.$url.'.js" nonce="'.$nonce.'" defer></script>';
    }

    $out .= '
        <link rel="icon" type="image/svg+xml" href="/_/img/favicon.svg">
        <link rel="apple-touch-icon" sizes="180x180" href="/_/img/apple-touch-icon.png">
        <link rel="manifest" href="/manifest.json">
    </head>
    <body>
    <nav>
        nav
    </nav>
    </body>
    </html>
    ';

    echo $out;
};
?>
