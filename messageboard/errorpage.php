<?php 
if(isset($_POST['yes'])) {
	$_SESSION['username'] = '';
	header("Location:login.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Programming Assignment 5 A Message Board using PHP and MySQL</title>
</head>

<body>
	<form method="POST">
		<label><b>UserName Or Password Incorrect</b></label><br/>
		<label><b>Do you want to Try logging in again ?  </b></label><br/>
		<input type="submit" value="YES" name="yes" /><br/><br/>
	</form>

</body>

</html>
