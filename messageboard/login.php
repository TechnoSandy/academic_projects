<?php
//start session to store session object
session_start();
error_reporting(E_ALL);
ini_set('display_errors','On');

//username storage variable for session
$_SESSION['username']='';

//Redirect to register page
if(isset($_POST['register'])) {
	header("Location:register.php");
}

//Login 
if(isset($_POST["login"])) {
	try {
 		$dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("select * from users where username='".$_POST["username"]."' and password='".md5($_POST["password"])."'");
		$stmt->execute();
		//initially zero
		$userCount = 0;
		//There will be only one user with matching username and password in the database
		foreach($stmt as $row) {
			$userCount = $userCount + 1;
		}
		//save username in session for session management and allow user to post
		if($userCount===1) {
			$_SESSION['username'] = $_POST["username"];
			header("Location:board.php");
		}
		if($userCount===0) {
			$_SESSION['username'] = '';
			echo 'Username or Password is wrong!!!!!';
			header("Location:errorpage.php");
		}
	} catch (PDOException $error) {
		print "PDOException: " . $error->getMessage() . "<br/>";
		die();
	}
}
?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
	<link rel="stylesheet" href="login.css">
		<meta charset="UTF-8">
		<title>Programming Assignment 5 A Message Board using PHP and MySQL</title>
	</head>

	<body>
		<form method="POST">
		<fieldset>
		<legend>Login Page:</legend>
			<label><b>Registered User Please Login</b></label><br/>
			<label><b>Username: </b><input type="text" name="username"/></label><br/>
			<label><b>Password: </b><input type="password" name="password"/></label><br/>
			<input type="submit" value="Login" name="login" /><br/><br/>
			<label><b>Not Registered Yet ? Sign up below </b></label><br/>
			<input type="submit" value="Register" name="register" /><br/>
			</fieldset>
		</form>

	</body>

	</html>
