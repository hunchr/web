<?php
// Connect to mySQL
function conn() {
    $conn = new mysqli('localhost', 'root', 'qumbyv-nohrin-Vyhhy7', 'sm', 3306);
    if ($conn -> connect_error) exit('Error 500;The database made an oopsie. Please try again later.;Okay');
    return $conn;
};
?>
