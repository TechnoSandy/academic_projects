<?php 

if(isset($_POST["Register"])) {
	try {
		if(strlen($_POST["username"])!=0 && strlen($_POST["password"])!=0 && strlen($_POST["fullname"])!=0 && strlen($_POST["email"])!=0 ){
        $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("insert into users values('".$_POST["username"]."','" . md5($_POST["password"]). "','".$_POST["fullname"]."','".$_POST["email"]."')");
		$stmt->execute();
		$dbh->commit();
		header("Location:login.php");			
		}else{
			echo 'Do not enter empty values in the text fields!';
		}	
	}  catch (PDOException $error) {
		print "PDOException: " . $error->getMessage() . "<br/>";
		die();
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
<link rel="stylesheet" href="register.css">
	<meta charset="UTF-8">
	<title>Programming Assignment 5 A Message Board using PHP and MySQL</title>
</head>
<!--USER TABLE SCHEMA users ( username, password, fullname, email ) Hence take input for these parameters-->

<body>
	<form method="POST">
	<fieldset>
	<legend>New Registration:</legend>
		<label><b>Username: </b><input type="text" name="username"/></label><br/>
		<label><b>Password: </b><input type="password" name="password"/></label><br/>
		<label><b>Full Name: </b><input type="text" name="fullname"/></label><br/>
		<label><b>Email Id: </b><input type="text" name="email"/></label><br/>
		<input type="submit" value="Register" name="Register" /><br/><br/>
		</fieldset>
	</form>

</body>

</html>
