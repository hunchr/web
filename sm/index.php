<?php
$title = $q;

if (!preg_match('/^[a-z]/', $q)) {
    if (preg_match('/^[@]/', $q)) {
        $q = $q[0].'","q='.$q;
    }
    else {
        exit('<main class="center">error (?)</main>');
    }
}

html(
    $title, // TODO
    '',
    'Description', // TODO
    'social,media',
    ['en','de'],
    ['_/main', 'sm/main'],
    ['_/main', 'sm/main'],
    'console.log("'.$q.'");'
);
?>
