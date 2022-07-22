<?php
$title = $q;

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
    'A simple social media website',
    'social,media',
    ['main', 'root/main'],
    ['main', 'root/main'],
    'fetch("'.$q.'");'
);
?>
