<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Deny, Disrupt, Degrade and Deceive</title>

 <link rel="stylesheet" type="text/css"  href="dddd.css"> 

</head>
<body>

<?php

echo '<div id="header">';
if (!isset($dtype)){
        echo '<h1>Deny, Disrupt, Degrade and Deceive</h1>';
} else {

        echo '<h1>' . $dtype . '</h1>';
}
echo '</div>';

?>
