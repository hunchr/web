<?php
require '../index.php';

$title = $q;

// Homepage
if (!$q) {
    require '../../index.php';
    exit();
}

// Define file to fetch
if (!preg_match('/^[a-z]/', $q)) {
    if (preg_match('/^[@]/', $q)) {
        $q = $q[0].'","q='.$q;
    }
    else {
        exit('<main class="center">error (?)</main>');
    }
}

// Generate HTML
html(
    $title, // TODO
    'URL', // TODO
    '',
    'A modern social media website',
    'social,media',
    ['en','de'],
    ['en-US','de-DE'],
    ['main', 'root/main'],
    ['main', 'root/main'],
    'fetch("'.$q.'");'
);
?>
