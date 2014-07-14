<?php
require('connections.php');
require('functions.php');
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

  <h1>Deny, Disrupt, Degrade and Deceive</h1>

<?php

//need to decide on variable names here 
if (!isset($_POST['source_text']) && !isset($_POST['d_cat'])) { 

	echo '<h2>Please Enter Text to be Submitted Below</h2>';
	echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '"id="textinput">';
	echo '<textarea rows="20" cols = "50" name="source_text" form="textinput">Enter Text Here.....</textarea>';
	echo '<br />';
	echo '<input type="submit">';
	echo '</form>';

}

//for inserting text box entered text
if (isset($_POST['source_text'])){
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	/* check connection */
	if (mysqli_connect_errno()) {
    	printf("Connect failed: %s\n", mysqli_connect_error());
    	exit();
	}
	$source_text = mysqli_real_escape_string($dbc, $_POST['source_text']);
	$query = "INSERT INTO Source_Article (Article_Text,Date_Created) VALUES ('$source_text',now())";
	mysqli_query($dbc, $query);
	$article_id = mysqli_insert_id($dbc);
	mysqli_close($dbc);

	if ($article_id > 0) {
		diplaytexttoprocess($article_id);
	} else echo '<h4>Error</h4>';
}





?>

</body>
</html>
