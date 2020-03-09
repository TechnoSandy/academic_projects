<html>

<head>
	<title>Message Board</title>
</head>
<link rel="stylesheet" href="board.css">

<body>
	<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors','On');


try {
	//connect to database 
  	$dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	//check if user is logged in or redirect to login page
	if(strlen($_SESSION['username'])!==0) {
		echo "<p><h1 id='Heading'>Post New Messages</h1></p>";
		echo "<form action='' method='GET'> <p><textarea rows='10' cols='30' type='text' name='message' style='width: 100%;' /></textarea></p>";
		echo "<p><button value='New Message'name='newMessage'>New Message</button> </p>";
	    echo "</form>";
		echo "<form action='' method='GET'> <p> <button value='Logout' name='logout' >Logout</button></p>";
		echo "</form>";
		
		if (isset($_GET['message']) && $_GET['message']!==''){		 
			$dbh->beginTransaction();
			$dbh->exec('insert into posts(id,postedby,datetime,message) values ("' . uniqid() . '","' . $_SESSION['username'] . '","' . date("Y-m-d H:i:s") . '","' . $_GET['message'] . '");')or die(print_r($dbh->errorInfo(), true));
			$dbh->commit();
		    $_GET['message']='';
		}
		
    // Show the list

		$sql = "select posts.id, users.username, users.fullname, posts.datetime, posts.replyto,posts.message from posts,users where posts.postedby = users.username ORDER BY posts.datetime DESC;";
        $result = $dbh->query($sql);
		while ($posts = $result->fetch(PDO::FETCH_ASSOC))
        {	
				echo "<table style='width:100%'>";
				echo "<tr>"; 
				echo "<th>MSG ID</th>";
				echo "<th>Posted By</th>";
				echo "<th>Time</th>";
				echo "<th>Message</th>";
				echo "<th>Reply To</th>";
				echo "<th></th>";
				echo "</tr>";
				echo "<tr>"; 
				echo "<th>". $posts['id'] ."</th>";
				echo "<th>". $posts['username']."</th>";
				echo "<th>". $posts['datetime']."</th>";
				echo "<th>". $posts['message'] ."</th>";
				//check if the reply is null 
				if (isset($posts['replyto'])){
                echo "<th>" . $posts['replyto']."</th>" ;
                }
				else{
				echo "<th> This is new message !!</th>" ;
				}
				echo "</tr>";
		        echo "</table>";
				echo "<br>";
				echo "<form action='' method='GET'> <p><input type='text' name=' " . $posts['id'] . "replyMsg'/> </p>";
				echo "<p><button name='" . $posts['id'] . "' >Reply To</button> </p>";
				echo "<br>";
				echo "</form>";

			
			if (!empty($_GET[$posts['id'] . 'replyMsg']) )
            {
                $message = $_GET[$posts['id'] . 'replyMsg'];
                $dbh->beginTransaction();
                $dbh->exec('insert into posts(id,replyto,postedby,datetime,message) values ("' . uniqid() . '","' . $posts['id'] . '","' . $_SESSION['username'] . '","' . date("Y-m-d H:i:s") . '","' . $message . '");')
                or die(print_r($dbh->errorInfo(), true));
                $dbh->commit();
               header('Location:board.php');
            }

        }



	// destroy session
	if (isset($_GET['logout'])) {
       // remove all session variables
		session_unset(); 
		// destroy the session 
		session_destroy(); 
        header('Location:login.php');
    }
		
	}else{
		// if user is not logged in then redirect to login page
		header("Location:login.php");	
	}
	

}catch (PDOException $error) {
			print "PDOException: " . $error->getMessage() . "<br/>";
			die();
}
?>
</body>

</html>
<br/><br/>
