<?php
require_once('connections.php');

//Put refreshing header here

?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Deny, Disrupt, Degrade and Deceive</title>

 <!-- <link rel="stylesheet" href="css/styles.css?v=1.0"> -->

</head>
<body>


<?php


if (empty($_POST)) {

	echo '<h2>Please Enter Text to be Submitted Below</h2>';

	echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '"id="textinput">';
	echo '<textarea rows="20" cols = "50" name="source_text" form="textinput">Enter Text Here.....</textarea>';
	echo '<br />';
	echo '<input type="submit">';



}