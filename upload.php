<?php

	require_once('config.php');
	
  $requireddb = urldecode($_GET["db"]);     
  if ( $requireddb == "" || $requireddb < 7 )
  {
    	echo "Result:5";
    	die;
  }		
	
	if(!@mysql_connect("$DBIP","$DBUSER","$DBPASS"))
	{
		echo "Result:4";
		die();
	}
	
	mysql_select_db("$DBNAME");
	
	$username = urldecode($_GET["u"]);
	$password = urldecode($_GET["p"]);
	$salt = "trackmeuser";
	$password = MD5($salt.$password);
	
	$result=mysql_query("Select ID FROM users WHERE username = '$username' and password='$password'");
	if ( $row=mysql_fetch_array($result) )
	{
			$userid=$row['ID'];		// Good, user and password are correct.
	}
	else
	{
		echo "Result:1";
		die;		
	}
	
	$action = urldecode($_GET["a"]);     	
	
	if ( $action=="kml")
	{
		if ( !file_exists("routes") )
				mkdir("routes");
		
		$myfile = "routes/".$username.".kml";
	
		if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], "./$myfile" ))
		{
	    	echo"Result:0";
	    	die;
		}
		else
		{
				echo"Result:6";
				//print_r($_FILES);
				die;
		}
	}
	
	if ( $action =="pic" )
	{
		if ( !file_exists("pics") )
				mkdir("pics");
				
	  $newname = urldecode($_GET["newname"]);     	
	  
	  $ext = strtolower(substr(strrchr($newname, '.'), 1));				
	  if ( $ext != "jpg" && $ext != "bmp" && $ext != "gif" )
	  {
	  	echo "Result:7";
	  	die;
	  }
	  
		
		$myfile = "pics/".$newname;
	
		if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], "./$myfile" ))
		{
				
	    	echo"Result:0";
				//print_r($_FILES);	    	
	    	die;
		}
		else
		{
				echo"Result:6";
				//print_r($_FILES);
				die;
		}		
	}


?>
 		



