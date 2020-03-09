<!DOCTYPE html>

<html>
<style>* { margin: 0; padding: 0 }</style>
<body>



<?php
require_once 'demo-lib.php';
demo_init(); // this just enables nicer output

// if there are many files in your Dropbox it can take some time, so disable the max. execution time
set_time_limit( 0 );

require_once 'DropboxClient.php';

/** you have to create an app at @see https://www.dropbox.com/developers/apps and enter details below: */
/** @noinspection SpellCheckingInspection */
$dropbox = new DropboxClient( array(
	'app_key' => "m1uyj0unrqb3zy3",      // Put your Dropbox API key here
	'app_secret' => "v5lk1j3xujq1k53",   // Put your Dropbox API secret here
	'app_full_access' => false,
) );


/**
 * Dropbox will redirect the user here
 * @var string $return_url
 */
$return_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?auth_redirect=1";

// first, try to load existing access token
$bearer_token = demo_token_load( "bearer" );

if ( $bearer_token ) {
	$dropbox->SetBearerToken( $bearer_token );
} elseif ( ! empty( $_GET['auth_redirect'] ) ) // are we coming from dropbox's auth page?
{
	// get & store bearer token
	$bearer_token = $dropbox->GetBearerToken( null, $return_url );
	demo_store_token( $bearer_token, "bearer" );
} elseif ( ! $dropbox->IsAuthorized() ) {
	// redirect user to Dropbox auth page
	$auth_url = $dropbox->BuildAuthorizeUrl( $return_url );
	die( "Authentication required. <a href='$auth_url'>Continue.</a>" );
}
?>

<?php

//CODE STARTS HERE

$account_information = $dropbox->GetAccountInfo();
echo "<p><h1 style='text-align: center;'>Project 6</h1></p>";
echo "<p style='text-align: center;'>Name: <b>".$account_information->display_name."</b></p>";
echo "<p style='text-align: center;'>Email ID: <b>".$account_information->email."</b></p>";
echo "<p style='text-align: center;'>App Name : <b>cse5335_sxs6868</b>" ;
	
echo "</br>" ;
echo "</br>" ;
echo "</br>" ;	
echo "</br>" ;
echo "<hr/>" ;
	
?>
<!--<img id="img" style="float:right; margin-top: 5px; width: 30%; margin-right: 450px;"></img>-->
<img id="img" style="float:right; margin-top: 5px; width: 30%; "></img>
<?php
//Upload File
$files = $dropbox->GetFiles("",false);
//To upload the selected image. If the file is selected, then upload
if(isset($_FILES['file']))
{
     $file = $_FILES['file']['name'];               
	 $dropbox->uploadFile($_FILES['file']['tmp_name'], $file);
}



//Display Files
$allFiles = $dropbox->GetFiles("",false);//Get all files on Dropbox
if(!empty($allFiles)&& !isset($_GET['delete'])) {
	if(count($allFiles)===0){		
		 echo '<h1 style="text-align: center;">No images in the application folder please upload the images to see the list</h1>';
	}
	echo "<table style='width:100%'>";
	foreach ($allFiles as $img)
	{     
	     $imgUrl=$dropbox->GetLink($img,false);
		 $displayImage = "image('".$imgUrl."')";
		 echo '<form action="album.php" method="GET">';
		 echo '<a style="font-size: xx-large;" onclick="'.$displayImage.'">'.basename($img->path).'</a><br />  		 
		 <p><a href="album.php?download='.basename($img->path).'">Download</a><br />';
		 echo'<a href="album.php?delete='.basename($img->path).'">Delete</a><br /><br /><br /></p>';
		 echo '</form>';
		 
    }
	echo "</table>";
}



//Delete File
$files = $dropbox->GetFiles("",false);//Get all files on Dropbox
if(isset($_GET['delete']))
{	
	$file      = $files[$_GET['delete']] ;
	$dropbox->Delete($file->path);
	$newList = $dropbox->GetFiles("",false);	
	echo "<table style='width:100%'>";
	if(count($newList)===0){		
		 echo '<h1 style="text-align: center;">No images in the application folder please upload the images to see the list</h1>';
	}
	foreach ($newList as $img)
	{     
	     $imgUrl=$dropbox->GetLink($img,false);
		 $displayImage = "image('".$imgUrl."')";
		 echo '<form action="album.php" method="GET">';
		 echo '<a style="font-size: xx-large;" onclick="'.$displayImage.'">'.basename($img->path).'</a><br />  		 
		 <p><a href="album.php?download='.basename($img->path).'">Download</a><br />';
		 echo'<a href="album.php?delete='.basename($img->path).'">Delete</a><br /><br /><br /></p>';
		 echo '</form>';
		 
    }
	echo "</table>";
	
}	
	
//Download Files
$files = $dropbox->GetFiles("",false);//Get all files on Dropbox
if (isset($_GET['download'])) {
	$file      = $files[$_GET['download']] ;
	//File will be downloaded in the downloads folder in the project directory
	$test_file = "downloads/"."Download Date - ".date("Y-m-d")." and Name -  ". basename( $file->path );
	$dropbox->DownloadFile( $file, $test_file ) ;
}
?>

	
	<form enctype="multipart/form-data" action="album.php" method="POST">
	  <fieldset>
		<!--the max size is taken from DropboxClient.php -->
		 <legend>Upload:</legend>
		Choose Image: <input name="file" type="file" accept="image/*" />
		<input type="submit" value="upload photo" />
		  </fieldset>
	</form>
	<br>


</body>
<script type="text/javascript">
	function image(url) {
		console.log("url " + url);
		document.getElementById('img').src = url;
	}

</script>


</html>