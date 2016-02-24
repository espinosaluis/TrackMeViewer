<?php

	require_once("config.php");
	
  $requireddb = urldecode($_GET["db"]);     
  if ( $requireddb == "" || $requireddb < 8 )
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
	
		
	$displayedname = mysql_real_escape_string($_GET["u"]);
	$id = mysql_real_escape_string($_GET["id"]);
		
	// User not specified
	if ( $id == "" )
	{
		echo "Result:3";
		die();
	}
	
	
	$action = $_GET["a"];
	
//	sleep(10);
	
	
	if ($action=="noop")
	{
		echo "Result:0";
		die();		
	}
			
	
	if ($action == "update" )
	{
		$lat = $_GET["lat"];
		$long = $_GET["long"];
		$acc = $_GET["acc"];
		$pub = $_GET["pub"];
		$name = $_GET["dn"];
		$dateoccurred = urldecode($_GET["do"]);		
		
		if ( $pub == "" ) $pub="1";
		
		$sql = "Select id FROM cloud WHERE id = '$id'";
		$result=mysql_query($sql);
		$nume=mysql_num_rows($result);	
		if ( $nume > 0 )
		{
			  $sql = "Update cloud set public='$pub', latitude='$lat', longitude='$long', dateoccurred='$dateoccurred', ";
			  
			  if ( $acc <> "" ) 
					$sql.="accuracy='$acc',";								
				else
					$sql.="accuracy=null,";								
					
				if ( $name <> "" ) 
					$sql.="displayname='$name' ";								
				else
					$sql.="displayname=null ";								
					
			  $sql.= "where id='$id'";		
			  
			  
			  			  
				mysql_query($sql);			
		}
		else 
		{
			  $sql = "Insert into cloud (id,latitude,longitude,dateoccurred,accuracy,displayname,public) values('$id','$lat','$long','$dateoccurred', ";		  
			  
			  if ( $acc <> "" ) 
					$sql.="'$acc',";								
				else
					$sql.="null,";								
					
				if ( $name <> "" ) 
					$sql.="'$name',";								
				else
					$sql.="null,";				
					
				$sql.=$pub;
			  
			  $sql.=")";
			  
			  
				mysql_query($sql);
		}
		
		echo "Result:0";		
		die();		
	}
	
	if ($action == "show" )
	{
		$lat = $_GET["lat"];
		$long = $_GET["long"];
		$datefrom = $_GET["df"];
				
		$output = ""; 		
		
		$sql = "SELECT(  DEGREES(     ACOS(        SIN(RADIANS( latitude )) * SIN(RADIANS(".$lat.")) +";
		$sql.= "COS(RADIANS( latitude )) * COS(RADIANS(".$lat.")) * COS(RADIANS( longitude - ".$long.")) ) * 60 * 1.1515 * 1.609344 "; // multiplied by 1.609344 for km
		$sql.= ")  ) AS distance, id, latitude, longitude, accuracy, dateoccurred, displayname,public FROM cloud where id<>'".$id."'";
		
		if ( $datefrom != "" )
			$sql.=" and dateoccurred>='$datefrom' ";
		
		$sql.=" order by distance asc limit 0,100";
		 
							
		$result=mysql_query($sql);	
		while( $row=mysql_fetch_array($result) )
    {
    	$output.=$row['id']."|".$row['latitude']."|".$row['longitude']."|".$row['dateoccurred']."|".$row['accuracy']."|".$row['distance']."|".$row['displayname']."|".$row['public']."\n";    		  
    }    
   		
		echo "Result:0|$output";		
		die();
	}

	

?>

