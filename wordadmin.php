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

<script language="JavaScript" type="text/javascript">
	function hidebox(rad){
		var rads=document.getElementsByName(rad.name);
		document.getElementById('Match').style.display="none" ;
	}
        function showbox(rad){
        	var rads=document.getElementsByName(rad.name);
        	document.getElementById('Match').style.display="inline" ;
	}

</script>
<?php
	if (isset($_POST['catid'])){
		$catid = $_POST['catid'];
		echo '<script>showDwords("' . $catid . '" ); </script>';	
	}
?>


</head>
<body>

<div id="header">
	<h1>Deny, Disrupt, Degrade and Deceive</h1>
</div>


<?php

	if (!isset($_SESSION['user_id'])) {
    		echo '<div class="message"> <p>Please <a href="login.php">log in</a> to access this page.</p></div>';
    		exit();
  	}
	else {
    		echo('<div class="message"><p>You are logged in as ' . $_SESSION['username'] . '</p></div>');
  	}
	

//SQL change Word Match Replacement, user and time. htmlspecialchars for inserted word

	if(isset($_POST['Replace'])){
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if(isset($_POST['Match'])){
			$match = htmlspecialchars($_POST['Match'], ENT_QUOTES);
		//	$match = mysqli_real_escape_string($dbc, $match);
		}else { $match = '';}
		
		$replace = htmlspecialchars($_POST['Replace'], ENT_QUOTES);
               // $replace = mysqli_real_escape_string($dbc, $replace);
		$catid   = $_POST['catid']; 
		$datatype = isset($_POST['dtype']) ? $_POST['dtype'] : 1;
		$user = $_SESSION['username'];
		if ((!empty($match) && !empty($replace) && $datatype == 1)
		 || (!empty($replace) && $datatype == 2)){
			$query = "select * from Match_Replacement where Match_term = '$match'  and replace_term = '$replace' and data_type_id = $datatype and cat_id = $catid";
			$result  = mysqli_query($dbc, $query);
			if(mysqli_num_rows($result) > 0) {
				echo '<h2>Identical Record already exists</p>';
			}
			else {
				$query = "INSERT INTO Match_Replacement (Match_term, replace_term, data_type_id, cat_id, Date_added, added_by) ".
					"values ('$match', '$replace', $datatype, $catid, NOW(), '$user')";
				mysqli_query($dbc, $query) or die ('Error Querying Database');
			
				echo '<div class="message"><p>New entry added </p></div>';
			}
			mysqli_close($dbc);
		}
		else {
			echo '<div class="message"><p>Your Missing something Dummy!!!</p></div>';
		}
	}		




	if(isset($_POST['todelete'])){
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		foreach ($_POST['todelete'] as $delete_id){
			$query = "DELETE FROM Match_Replacement WHERE match_id = $delete_id";
			mysqli_query($dbc, $query) or die ('Error Querying Database');
		}
		echo '<div class="message"><p>All you want has been removed</p></div>';
		mysqli_close($dbc);
	}


	if(isset($catid)){
	        switch($catid) {
                case 1:
                        $dtype = 'Deny';
                        break;
                case 2:
                        $dtype = 'Disrupt';
                        break;
                case 3:
                        $dtype = 'Degrade';
                        break;
                case 4:
                        $dtype = 'Deceive';
                        break;
       		 }	
	}




?>
	<form class="normform">
	<fieldset>
	<legend>Select Dtype</legend>
	<select name="D_cat" onchange="showDwords(this.value)">
<?php  (isset($dtype)) ? $option = $dtype : $option = '--select--';
	echo  '<option value="'. $catiid .'">'. $option . '</option>'; ?>
  	<option value="1">Deny</option>
  	<option value="2">Disrupt</option>
  	<option value="3">Degrade</option>
  	<option value="4">Decieve</option>
  	</select>
	</fieldset>
	</form>
	<br>
	<div id="word_table"></div>
	

</body>
</html>
