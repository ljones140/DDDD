<?php


require('connections.php');
session_start();
  // If the session vars aren't set, try to set them with a cookie
 	if (!isset($_SESSION['user_id'])) {
    		if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
      			$_SESSION['user_id'] = $_COOKIE['user_id'];
      			$_SESSION['username'] = $_COOKIE['username'];
    		}
 	 }


?>
<!doctype html>

<html lang="en">
<head>
<meta charset="utf-8">

  <title>Deny, Disrupt, Degrade and Deceive</title>

 <link rel="stylesheet" type="text/css"  href="dddd.css">
<script>
function showDwords(str) {
    if (str == "") {
        document.getElementById("word_table").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("word_table").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","wordtable.php?q="+str,true);
        xmlhttp.send();
    }
}
</script>



</head>
<body>

<div id="header">
	<h1>Deny, Disrupt, Degrade and Deceive</h1>
</div>


<?php

	if (!isset($_SESSION['user_id'])) {
    		echo '<p>Please <a href="login.php">log in</a> to access this page.</p>';
    		exit();
  	}
	else {
    		echo('<p>You are logged in as ' . $_SESSION['username'] . '</p>');
  	}
	

//SQL change Word Match Replacement, user and time. htmlspecialchars for inserted word

	if(isset($_POST['newword'])){
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$newword = mysqli_real_escape_string($dbc, $_POST['newword']);
		if (!empty($newword)){
			$query = "SELECT * FROM word where word = '$newword'";
			$result  = mysqli_query($dbc, $query);
			if(mysqli_num_rows($result) > 0) {
				echo '<h2>Word Already Exists</p>';
			}
			else {
				$query = "INSERT INTO word (word) values ('$newword')";
				mysqli_query($dbc, $query) or die ('Error Querying Database');
			
				echo '<p>New Word Added: ' . $newword . '</p>';
			}
			mysqli_close($dbc);
		}
		else {
			echo '<p>Your word is Empty Dummy!!!</p>';
		}
	}		




	if(isset($_POST['todelete'])){
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		foreach ($_POST['todelete'] as $delete_id){
			$query = "DELETE FROM word WHERE wordid = $delete_id";
			mysqli_query($dbc, $query) or die ('Error Querying Database');
		}
		echo '<p>Words removed</p>';
		mysqli_close($dbc);
	}


?>
	<form>
	<select name="D_cat" onchange="showDwords(this.value)">
  	<option value="">Select a D Type:</option>
  	<option value="1">Deny</option>
  	<option value="2">Disrupt</option>
  	<option value="3">Degrade</option>
  	<option value="4">Decieve</option>
  	</select>
	</form>
	<br>
	<div id="word_table"><b>Table Here</b></div>


	


		
	</form>

</body>
</html>
