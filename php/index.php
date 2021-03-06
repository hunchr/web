<?php
$q = isset($_GET['q']) ? $_GET['q'] : false;

// Redirect request
if (!$q) {
    require 'homepage.php';
}
else if (!strpos($q, '/')) {
    require 'root.php';
}
else {
    // if (preg_match('/\.php$/', $q)) {
    //     require '../err/404.php';
    //     exit();
    // }

    echo 'todo: other program: '.$q; // TODO
}

// HTML template
function html($title, $url, $base, $description, $keywords, $css, $js, $script) {
    // ----- CSP -----
    $nonce = base64_encode(random_bytes(18));

    header('Content-Type: text/html; charset=utf-8');
    header('Content-Security-Policy: default-src \'self\'; script-src \'nonce-'.$nonce.'\' \'strict-dynamic\' \'unsafe-eval\' https: \'unsafe-inline\'; object-src \'none\'; connect-src \'self\'; base-uri \'self\'');
    
    // ----- Cache -----
    // header('Cache-Control: max-age=31536000');

    // ----- Login -----
    session_start();

    $uid = 0;
    $lang = 'en-US';
    $langs = ['en','de'];
    $langs_cc = ['en-US','de-DE'];
    $preferences = '"';
    $logged_in = false;

    // User
    if (isset($_COOKIE['auth'])) {
        $auth = explode(';', base64_decode($_COOKIE['auth']), 5); // [username; lang; display; date; token]

        // Valid auth token
        if (count($auth) === 5) {
            $lang = $auth[1];
            $auth[0] = htmlspecialchars($auth[0]);

            if (preg_match('/light/', $auth[2])) $preferences .= ' data-light';
            if (preg_match('/nsfw/', $auth[2])) $preferences .= ' data-nsfw';
            
            if (isset($_SESSION['uid'])) {
                $logged_in = true;
            }
            // Log in
            else {
                require '../../.sql.php';

                $conn = conn();
                $qy = $conn -> query(
                    'SELECT uid, token
                    FROM auth
                    WHERE uid = (
                        SELECT uid
                        FROM users u
                        WHERE u.name = "'.$auth[0].'"
                    )
                    AND created_at = "'.htmlspecialchars($auth[2]).'"
                    LIMIT 1;'
                );

                // Verify password
                if ($qy -> num_rows !== 0) {
                    $qy = $qy -> fetch_assoc();

                    if (password_verify(hash("sha3-512", $_COOKIE['auth']), $qy['token'])) {
                        $uid = $qy['uid'];
                        $logged_in = true;
                    }
                }

                $conn -> close();
            }
        }
    }

    // Guest
    if (!$logged_in) {
        preg_match_all('/[a-z]+(?=;)/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang);
        $lang = $lang[0][0];
        $auth = ['guest']; // [username]

        // Delete (invalid) auth token
        unset($_COOKIE['auth']);
        setrawcookie('auth', "", time() - 3600);
    }

    // ----- Session variables -----
    $lang_supported = array_search($lang, $langs);
    $lang = $lang_supported ? $langs_cc[$lang_supported] : $langs_cc[0]; // TODO

    $_SESSION['lang'] = $lang;
    // $_SESSION['lang'] = 'de-DE'; // TODO: remove (this is just a test case)
    $_SESSION['uid'] = $uid;

    // ----- HTML -----
    $out = '
    <!DOCTYPE html>
    <html lang="'.$lang.$preferences.'>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
        <title>'.$title.'</title>
        <meta name="description" content="'.$description.'">
        <meta name="keywords" content="aluay,'.$keywords.','.$title.'">
        <meta name="theme-color" content="#000000">
        <meta name="referrer" content="no-referrer">
        <meta name="robots" content="index,follow">
        <meta property="og:title" content="'.$title.'">
        <meta property="og:description" content="'.$description.'">
        <meta property="og:url" content="http://localhost/'.$base.$url.'">
        <meta property="og:image" content="http://localhost/_/img/open-graph.png">
        <meta property="og:locale" content="'.$lang.'">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="aluay">
        <meta name="twitter:card" content="summary">
        <link rel="canonical" href="http://localhost/'.$base.$url.'">
    ';

    foreach ($css as $url) {
        // $out .= '<link rel="preload" href="/'.$url.'.css" as="style" onload="this.onload=null;this.rel=\"stylesheet\"">';
        $out .= '<link rel="stylesheet" href="/css/'.$url.'.css">';
    }
    
    foreach ($js as $url) {
        $out .= '<script src="/js/'.$url.'.js" nonce="'.$nonce.'" defer></script>';
    }

    $out .= '
        <script nonce="'.$nonce.'">
            const username = "'.$auth[0].'",
                  base = "'.($base ? $base : 'root/').'";

            window.addEventListener("DOMContentLoaded", () => {
                '.$script.'
            });
        </script>
        <link rel="icon" type="image/svg+xml" href="/img/favicon.svg">
        <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
        <link rel="manifest" href="/manifest.json">
    </head>
    <body>
    <nav>
        <button data-f="_A" aria-label="Back" id="home">
            <img src="/img/apple-touch-icon.png" alt="Logo">
            <svg viewBox="0 0 8 8"><path d="M8 3.5H1.91L4.71.7 4 0 0 4l4 4 .7-.7-2.79-2.8H8v-1z"/></svg>
        </button>
        <h1>'.$title.'</h1>
        <input type="text" placeholder="Search">
        <div id="bn">
            <button data-f="__" data-n="home" aria-label="Home"><svg viewBox="0 0 6 6"><path d="M3 0 0 3v3h2V4h2v2h2V3L3 0z"/></svg></button>
            <button data-f="__" data-n="explore" aria-label="Explore"><svg viewBox="0 0 8 8"><circle cx="4" cy="4" r=".5"/><path d="M4 0C1.79 0 0 1.79 0 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm1 5L2 6l1-3 3-1-1 3z"/></svg></button>
            <button data-f="na" aria-label="Create new post"><svg viewBox="0 0 8 8"><path d="M8 3.5H4.5V0h-1v3.5H0v1h3.5V8h1V4.5H8v-1z"/></svg></button>
            <button data-f="__" data-n="inbox" aria-label="Inbox"><svg viewBox="0 0 8 8"><path d="M0 0v8h8V0H0zm7 5H5.5c0 .83-.67 1.5-1.5 1.5S2.5 5.83 2.5 5H1V1h6v4z"/></svg></button>
            <button data-f="_C" aria-label="Open side navigation"><img src="/uc/s/'.$uid.'/0.webp" alt="pfp" draggable="false"></button>    
        </div>
        <button data-f="_B" aria-label="Search" id="search">
            <svg viewBox="0 0 8 8"><path d="M8 7.29 5.44 4.73C5.79 4.24 6 3.64 6 3c0-1.66-1.34-3-3-3S0 1.34 0 3s1.34 3 3 3c.65 0 1.24-.21 1.73-.56L7.29 8 8 7.29zM1 3c0-1.1.9-2 2-2s2 .9 2 2a2.012 2.012 0 0 1-.99 1.72C3.71 4.9 3.37 5 3 5c-1.1 0-2-.9-2-2z"/></svg>
            <svg viewBox="0 0 8 8"><path d="M6.35 2.35 8 .71 7.29 0 5.65 1.65 4 3.29 2.35 1.65.71 0 0 .71l1.65 1.64L3.29 4 0 7.29.71 8 4 4.71 7.29 8 8 7.29 4.71 4l1.64-1.65z"/></svg>
        </button>
        <menu id="sn">
            <button data-f="_C"><svg viewBox="0 0 6 5"><path d="M0 0h6v1H0zM0 2h6v1H0zM0 4h6v1H0z"/></svg><div>Close</div></button>
            <button data-f="__" data-n="profile"><svg viewBox="0 0 4 4"><circle cx="2" cy="1" r="1"/><path d="M0 4c0-1.1.9-2 2-2s2 .9 2 2"/></svg><div>Profile</div></button>
            <button data-f="__" data-n="communities"><svg viewBox="0 0 5.5 4"><circle cx="2" cy="1" r="1"/><path d="M0 4c0-1.1.9-2 2-2s2 .9 2 2M3.5 0c-.19 0-.35.06-.5.15.29.17.5.48.5.85s-.21.67-.5.85c.15.09.31.15.5.15.55 0 1-.45 1-1s-.45-1-1-1z"/><path d="M3.5 2c-.17 0-.34.03-.5.07.86.22 1.5 1 1.5 1.93h1c0-1.1-.9-2-2-2z"/></svg><div>Communities</div></button>
            <button data-f="__" data-n="lists"><svg viewBox="0 0 10 9.5"><path d="M0 0v7.5L2.5 6h5V0H0z"/><path d="M8.5 2v5h-5l-1 .6V8h5L10 9.5V2H8.5z"/></svg><div>Lists</div></button>
            <button data-f="__" data-n="saved"><svg viewBox="0 0 8 7.61"><path d="m4 6.31-2.47 1.3L2 4.86 0 2.91l2.76-.41L4 0l1.24 2.5L8 2.91 6 4.86l.47 2.75L4 6.31z"/></svg><div>Saved</div></button>
            <button data-f="__" data-n="liked"><svg viewBox="0 0 8 7.5"><path d="m4 7.5 3.56-3.93c.67-.91.56-2.2-.29-2.98-.92-.84-2.34-.77-3.18.14-.04.04-.05.09-.08.13-.03-.04-.06-.09-.09-.13C3.07-.18 1.65-.25.73.59c-.86.78-.96 2.06-.3 2.98"/></svg><div>Liked</div></button>
            <button data-f="__" data-n="settings"><svg viewBox="0 0 6.2 6"><path d="M6.2 2.37 5.2.64l-1.1.63V0h-2v1.27L1 .63 0 2.37 1.1 3 0 3.63l1 1.73 1.1-.63V6h2V4.73l1.1.63 1-1.73L5.1 3l1.1-.63zM3.1 4c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg><div>Settings</div></button>
            <button data-f="__" data-n="premium"><svg viewBox="0 0 7 8"><path d="M3.5 0 0 1.5v1S0 7 3.5 8C7 7 7 2.5 7 2.5v-1L3.5 0zm0 6.86V4H1.14C1 3.39 1 2.93 1 2.93v-.71l2.5-1.08V4h2.36c-.22 1-.81 2.41-2.36 2.86z"/></svg><div>Premium</div></button>
            <button data-f="__" data-n="help"><svg viewBox="0 0 8 8"><path d="M4 0C1.79 0 0 1.79 0 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm.5 7h-1V6h1v1zm0-1.5h-1C3.5 3.88 5 4 5 3c0-.55-.45-1-1-1s-1 .45-1 1H2c0-1.1.9-2 2-2s2 .9 2 2c0 1.25-1.5 1.38-1.5 2.5z"/></svg><div>Help</div></button>
            <button data-f="__" data-n="feedback"><svg viewBox="0 0 6 7.5"><path d="M0 0v6h1.75L3 7.5 4.25 6H6V0H0zm3.71 3.71L3 5l-.71-1.29L1 3l1.29-.71L3 1l.71 1.29L5 3l-1.29.71z"/></svg><div>Feedback</div></button>
            <button data-f="__" data-n="policy"><svg viewBox="0 0 7 8"><path d="M3.5 0 0 1.5v1S0 7 3.5 8c.87-.25 1.53-.72 2.02-1.28L4.51 5.71c-.3.18-.64.28-1.01.28-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2c0 .37-.11.71-.28 1.01l.88.88C7 4.3 7 2.49 7 2.49V1.5L3.5 0z"/><circle cx="3.5" cy="4" r="1"/></svg><div>Privacy Policy</div></button>
            <button data-f="__" data-n="terms"><svg viewBox="0 0 8 8"><path d="m.004 4.17 1.393-1.394 2.086 2.086L2.09 6.255zM2.777 1.39 4.17-.001l2.086 2.086-1.393 1.393zM1.735 2.43l.693-.692 5.565 5.564-.693.693z"/></svg><div>Terms</div></button>
            <button data-f="__" data-n="apps"><svg viewBox="0 0 4 4"><path d="M0 3h1v1H0zM0 0h1v1H0zM0 1.5h1v1H0zM3 3h1v1H3zM3 0h1v1H3zM3 1.5h1v1H3zM1.5 3h1v1h-1zM1.5 0h1v1h-1zM1.5 1.5h1v1h-1z"/></svg><div>More Apps</div></button>
        </menu>
    </nav>
    </body>
    </html>
    ';

    echo $out;
};
?>
