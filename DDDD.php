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
	$source_text = $_POST['source_text'];
	insertsource_article($source_text);
	
	if ($article_id > 0) {
		displaytexttoprocess($article_id);
	} else echo '<h4>Error</h4>';
}





?>

</body>
</html>
