<?php

	require_once("config.php");
    require_once("database.php");
	

    function run($connection)
    {
  $requireddb = urldecode($_GET["db"]);     
  if ( $requireddb == "" || $requireddb < 8 )
  {
            return "Result:5";
  }	
	
	
        if(!@mysql_connect("$connection[host]","$connection[user]","$connection[pass]"))
	{
            return "Result:4";
	}
	
        mysql_select_db("$connection[name]");
	
		
	// Check username and password
	$username = mysql_real_escape_string($_GET["u"]);
	$password = mysql_real_escape_string($_GET["p"]);
	
	// User not specified
	if ( $username == "" || $password == "" )
	{
            return "Result:3";
	}
	
	$salt = "trackmeuser";
	$password = MD5($salt.$password);
	
	$result=mysql_query("Select ID, Enabled FROM users WHERE username = '$username' and password='$password'");
	if ( $row=mysql_fetch_array($result) )
	{
		$userid=$row['ID'];		// Good, user and password are correct.
		
		$enabled = $row['Enabled'];
		if ($enabled == 0 )
		{
            return "User disabled. Please contact system administrator";
		}
	}
	else
	{
		$result=mysql_query("Select 1 FROM users WHERE username = '$username'");
		$nume=mysql_num_rows($result);	
		if ( $nume > 0 )
		{
                return "Result:1"; // user exists, password incorrect.
		}					
		
		mysql_query("Insert into users (username,password) values('$username','$password')");			

		$result=mysql_query("Select ID FROM users WHERE username = '$username' and password='$password'");
		if ( $row=mysql_fetch_array($result) )
		{
			$userid=$row['ID'];	// User created correctly.	
		}
		else
		{		
			echo "Result:2"; // Unable to find user that was just created.
			die();		
		}
		
	}	
	
	
	
	$tripname = urldecode($_GET["tn"]);	
	$action = $_GET["a"];
	
	
	
	
	if ($action=="noop")
	{
            return "Result:0";
	}
			
	
	if ($action == "sendemail" )
	{
		$to = $_GET["to"];
		$body = $_GET["body"];
		$subject = $_GET["subject"];
		
		if ( $subject == "" )
			$subject = "Notification alert";
		
		mail($to,$subject, $body, "From: TrackMe Alert System\nX-Mailer: PHP/");		
		
		echo "Result:0";		
		die();		
	}

	
	
	if( $action=="geticonlist")
	{

		$iconlist = "";
		$result = mysql_query("select name from icons order by name");					
		while( $row=mysql_fetch_array($result) )
		{
			$iconlist.=$row['name']."|";
		}

		$iconlist = substr($iconlist, 0, -1);		  
		echo "Result:0|$iconlist";
		die();
	}
		
	


	if($action=="upload")
	{				
		$tripid = 'null';
		$locked = 0;
		
		if ( $tripname != "" )
		{			
			$result=mysql_query("Select ID, Locked FROM trips WHERE FK_Users_ID = '$userid' and name='$tripname'");
			if ( $row=mysql_fetch_array($result) )
			{
				$tripid=$row['ID'];		
				$locked=$row['Locked'];		
			}
			else // Trip doesn't exist. Let's create it.
			{
				mysql_query("Insert into trips (FK_Users_ID,Name) values('$userid','$tripname')");				
				
				$result=mysql_query("Select ID, Locked FROM trips WHERE FK_Users_ID = '$userid' and name='$tripname'");
				if ( $row=mysql_fetch_array($result) )
				{
					$tripid=$row['ID'];							
					$locked=$row['Locked'];		
				}
				
				if ( $tripid == 'null' )
				{
					echo "Result:6"; // Unable to create trip.
					die();					
				}				
			}
		}
		
		
		if ( $locked==1 )
		{
			echo "Result:8"; // Trip is locked
			die();								
		}
	
		$lat = $_GET["lat"];
		$long = $_GET["long"];
		$dateoccurred = urldecode($_GET["do"]);		
		$altitude = urldecode($_GET["alt"]);
		$angle = urldecode($_GET["ang"]);
		$speed = urldecode($_GET["sp"]);		
		$iconname = urldecode($_GET["iconname"]);
		$comments = urldecode($_GET["comments"]);		
		$imageurl = urldecode($_GET["imageurl"]);		
		$cellid = urldecode($_GET["cid"]);		
		$signalstrength = urldecode($_GET["ss"]);		
		$signalstrengthmax = urldecode($_GET["ssmax"]);		
		$signalstrengthmin = urldecode($_GET["ssmin"]);		
	  $batterystatus = urldecode($_GET["bs"]);	
	  $uploadss = urldecode($_GET["upss"]);	
	
		
		$iconid='null';		
		if ($iconname != "" ) 
		{
				$result=mysql_query("Select ID FROM icons WHERE name = '$iconname'");
				if ( $row=mysql_fetch_array($result) )
					$iconid=$row['ID'];							
		}
		

		$sql = "Insert into positions (FK_Users_ID,FK_Trips_ID,latitude,longitude,dateoccurred,fk_icons_id,speed,altitude,comments,imageurl,angle,signalstrength,signalstrengthmax,signalstrengthmin,batterystatus) values('$userid',$tripid,'$lat','$long','$dateoccurred',$iconid,";
			
		if ($speed == "" ) 
			$sql.="null,";
		else
			$sql.="'".$speed."',";					
			
	  if ($altitude == "" ) 
			$sql.="null,";
		else
			$sql.="'".$altitude."',";										
			
		if ($comments == "" ) 
			$sql.="null,";
		else
			$sql.="'".$comments."',";		

		if ($imageurl == "" ) 
			$sql.="null,";
		else
			$sql.="'".$imageurl."',";					
			
		if ($angle == "" ) 
			$sql.="null,";
		else
			$sql.="'".$angle."',";		
			
		if ($uploadss == 1 )
		{
			if ($signalstrength == "" ) 
				$sql.="null,";
			else
				$sql.=$signalstrength.",";								
				
			if ($signalstrengthmax == "" ) 
				$sql.="null,";
			else
				$sql.=$signalstrengthmax.",";								
				
			if ($signalstrengthmin == "" ) 
				$sql.="null,";
			else
				$sql.=$signalstrengthmin.",";														
		}
		else
			$sql.="null,null,null,";
			
		if ($batterystatus == "" ) 
			$sql.="null";
		else
			$sql.=$batterystatus;																	

			
			
		$sql.=")";
		

		$result = mysql_query($sql);	
		if (!$result) 
		{
			echo "Result:7|".mysql_error();		
			die();		
		}
		
		$upcellext = urldecode($_GET["upcellext"]);				
		if ($upcellext == 1 && $cellid != "" )
		{
			$sql = "Insert into cellids(cellid,latitude,longitude,signalstrength,signalstrengthmax,signalstrengthmin) values ('$cellid','$lat','$long',";
			
			if ($signalstrength == "" ) 
				$sql.="null,";
			else
				$sql.=$signalstrength.",";								
			
			if ($signalstrengthmax == "" ) 
				$sql.="null,";
			else
				$sql.=$signalstrengthmax.",";								
			
			if ($signalstrengthmin == "" ) 
				$sql.="null";
			else
				$sql.=$signalstrengthmin;							
				
			$sql.=")";			
					
			mysql_query($sql);
		}

		
		echo "Result:0";		
		die();		
	}
	
	
	
	
	
	
	
	
	
	
		
	if($action=="updatepositiondata" || $action=="updateimageurl")
	{		
		$id = urldecode($_GET["id"]);		
		$ignorelocking = urldecode($_GET["ignorelocking"]);
		
		if ( $id == "" )
		{
			echo "Result:6"; // id not specified
			die();
		}
		
		if ($ignorelocking == "" )
			$ignorelocking = 0;
		
		$locked = 0;
		$result=mysql_query("Select Locked FROM trips A1 INNER JOIN positions A2 ON A2.FK_TRIPS_ID=A1.ID WHERE A2.FK_Users_ID = '$userid' and A2.ID='$id'");
		if ( $row=mysql_fetch_array($result) )
		{
			 $locked = $row['Locked'];			 
			 if ( $locked == 1 && $ignorelocking == 0 )
			 {
			 		echo "Result:8";	
			 		die();
			 }
		}
		else
		{
			 echo "Result:7"; // trip not found.
			 die();					
		}
		
		
		$sql = "Update positions set ";
		
		if ( isset($_GET["imageurl"]))
		{		
			$imageurl = urldecode($_GET["imageurl"]);
			
			if ($imageurl != "" )
			{		
				$iconid='null';		
				$result=mysql_query("Select ID FROM icons WHERE name = 'Camera'");
				if ( $row=mysql_fetch_array($result) )
							$iconid=$row['ID'];							
			
				$sql.=" fk_icons_id=$iconid, imageurl='$imageurl',";			
			}
			else
				$sql.=" imageurl=null,";			
			
	  }
	  
	  if ( isset($_GET["comments"]))
		{				
			$comments = urldecode($_GET["comments"]);				
			
			if ( $comments == "" )
				$sql.=" comments=null,";							
			else
				$sql.=" comments='$comments',";			
				
	  }	 
		 		 	 		 		 
		$sql.="ID=ID where id=$id AND fk_users_id='$userid'";
		
		 		 
		mysql_query($sql);		 	 
		echo "Result:0";
  	die();	
	}
	
	
	if($action=="delete")
	{	
		 $locked = 0;
		 $tripid = "";
		 $result=mysql_query("Select ID,Locked FROM trips WHERE FK_Users_ID = '$userid' and name='$tripname'");
		 if ( $row=mysql_fetch_array($result) )
		 {
		 	 $tripid=$row['ID'];
			 $locked = $row['Locked'];
			 
			 if ( $locked == 1 )
			 {
			 		echo "Result:8";	
			 		die();
			 }
		 }
		 else
		 {
		 	  echo "Result:7"; // trip not found.
				die();					
		 }	 	
		
		if ( $tripname == "<None>" )			
			$sql = "DELETE FROM positions WHERE FK_Trips_ID is null ";
		else if ( $tripname != "" )
			$sql = "DELETE FROM positions WHERE FK_Trips_ID='$tripid' ";
		else
			$sql = "DELETE FROM positions WHERE 1=1 ";
					
		$sql.= " and FK_Users_ID = '$userid' ";

		$datefrom = urldecode($_GET["df"]);
		$dateto = urldecode($_GET["dt"]);
		
		if ( $datefrom != "" )
			$sql.=" and DateOccurred>='$datefrom' ";
		if ( $dateto != "" )
			$sql.=" and DateOccurred<='$dateto' ";			
			
						
		mysql_query($sql);
		
		echo "Result:0";
		die();		
	} 	
	
	if($action=="deletepositionbyid")
	{		
		$positionid = urldecode($_GET["positionid"]);
		if ( $positionid == "" )
		{
			echo "Result:6";
			die();		
		}
		
		$locked = 0;
		$result=mysql_query("Select Locked FROM trips A1 INNER JOIN positions A2 ON A2.FK_TRIPS_ID=A1.ID WHERE A2.FK_Users_ID = '$userid' and A2.ID='$positionid'");
		if ( $row=mysql_fetch_array($result) )
		{
			 $locked = $row['Locked'];			 
			 if ( $locked == 1 )
			 {
			 		echo "Result:8";	
			 		die();
			 }
		}
		else
		{
			 echo "Result:7"; // trip not found.
			 die();					
		}	 	
		
			
		$sql = "DELETE FROM positions WHERE ID='$positionid' AND FK_USERS_ID='$userid'";
						
		mysql_query($sql);
		
		echo "Result:0";
		die();		
	}
	

	
	
	
	if($action=="findclosestpositionbytime")
	{	
		$date = urldecode($_GET["date"]);
		
		if ( $date == "" )
		 {
			echo "Result:6"; // date not specified
		 	die();
		 }
		 
		$sql = "SELECT ID,dateoccurred FROM positions ";
		$sql.= "WHERE dateoccurred = (SELECT MIN(dateoccurred) ";
		$sql.= "FROM positions WHERE ABS(TIMESTAMPDIFF(SECOND,'$date',dateoccurred))= ";
		$sql.= "(SELECT MIN(ABS(TIMESTAMPDIFF(SECOND,'$date',dateoccurred))) ";
		$sql.= "FROM positions WHERE FK_USERS_ID='$userid') AND FK_USERS_ID='$userid') ";
		$sql.= "AND FK_USERS_ID='$userid'";
	
		$result=mysql_query($sql);	
		
		if ( $row=mysql_fetch_array($result) )
		{
			echo "Result:0|".$row['ID']."|".$row['dateoccurred'];
		}						
		else
			echo "Result:7"; // No positions from user found

		
		die();		
	} 	
	
	
	
	if($action=="findclosestpositionbyposition")
	{	
		
		$lat = $_GET["lat"];
		$long = $_GET["long"];
		
		if ( $lat == "" || $long== "" )
		 {
			echo "Result:6"; // position not specified
		 	die();
		 }
		 
		
		$sql = "SELECT(  DEGREES(     ACOS(        SIN(RADIANS( latitude )) * SIN(RADIANS(".$lat.")) +";
		$sql.= "COS(RADIANS( latitude )) * COS(RADIANS(".$lat.")) * COS(RADIANS( longitude - ".$long.")) ) * 60 * 1.1515 ";
		$sql.= ")  ) AS distance,ID, dateoccurred FROM positions WHERE FK_Users_ID = '$userid' order by distance asc limit 0,1";
					
		$result=mysql_query($sql);	
		
		if ( $row=mysql_fetch_array($result) )
		{
			echo "Result:0|".$row['ID']."|".$row['dateoccurred']."|".$row['distance'];
		}						
		else
			echo "Result:7"; // No positions from user found
			
		

		die();		
	} 
	
	
	
	if($action=="findnearbypushpins")
	{	
		
		$lat = $_GET["lat"];
		$long = $_GET["long"];
		$radius = $_GET["radius"];
					
		if ( $lat == "" || $long== "" )
		{
			echo "Result:6"; // position not specified
		 	die();
		}
		
		
		if ( $radius == "" )
		   $radius = 50.0;		  
		 
		$sql = "SELECT latitude, longitude, distance,  positioncomments, positionimageurl, tripname  FROM ( SELECT z.latitude, z.longitude, p.radius, p.distance_unit ";
    $sql.= "* DEGREES(ACOS(COS(RADIANS(p.latpoint)) * COS(RADIANS(z.latitude)) * COS(RADIANS(p.longpoint - z.longitude)) + SIN(RADIANS(p.latpoint)) ";
		$sql.= "* SIN(RADIANS(z.latitude)))) AS distance,  z.comments AS positioncomments, z.imageurl as positionimageurl, TT.name as tripname FROM positions AS z   LEFT JOIN trips TT on TT.ID = z.fk_trips_id JOIN (   /* these are the query parameters */ ";
		$sql.= "SELECT  ".$lat."  AS latpoint,  ".$long." AS longpoint, ".$radius." AS radius,      111.045 AS distance_unit ) AS p ON 1=1 WHERE ";
		$sql.= "z.fk_users_id='$userid' and ( z.comments <>'' or z.imageurl<>'') ";
		
		if ( $tripname != "" )
		{
			$tripid = "";
			
			$result=mysql_query("Select ID FROM trips WHERE FK_Users_ID = '$userid' and name='$tripname'");
			
			if ( $row=mysql_fetch_array($result) )
			 		$tripid=$row['ID'];					 		
			
			if ( $tripid <> "" )
		 		$sql.= "and ( z.fk_trips_id<>".$tripid." or z.fk_trips_id is null ) "; 
		}
		 
		$sql.= "and z.latitude BETWEEN p.latpoint  - (p.radius / p.distance_unit) AND p.latpoint  + (p.radius / p.distance_unit) ";
    $sql.= "AND z.longitude BETWEEN p.longpoint - (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint)))) AND p.longpoint + (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint)))) ";
 		$sql.= ") AS d WHERE distance <= radius ORDER BY distance LIMIT 15";
 		 								
		$result=mysql_query($sql);
		
		$output = ""; 		
				
		while( $row=mysql_fetch_array($result) )
		{
			$output.=$row['latitude']."|".$row['longitude']."|".$row['distance']."|".$row['positioncomments']."|".$row['positionimageurl']."|".$row['tripname']."\n";
		}						
		
		echo "Result:0|$output";			
			
		
		die();		
	}
	
	if($action=="findclosestbuddy")
	{	
		$result=mysql_query("Select latitude,longitude FROM positions WHERE fk_users_id='$userid' order by dateoccurred desc limit 0,1");
		
		if ( $row=mysql_fetch_array($result) )
		{	
			/*
			$sql = "SELECT(  DEGREES(     ACOS(        SIN(RADIANS( latitude )) * SIN(RADIANS(".$row['latitude'].")) +";
			$sql.= "COS(RADIANS( latitude )) * COS(RADIANS(".$row['latitude'].")) * COS(RADIANS( longitude - ".$row['longitude'].")) ) * 60 * 1.1515 ";
			$sql.= ")  ) AS distance,dateoccurred,fk_users_id FROM positions WHERE FK_Users_ID <> '$userid' order by distance asc limit 0,1";
						
			$result=mysql_query($sql);	
			
			if ( $row=mysql_fetch_array($result) )
			{
				echo "Result:0|".$row['distance']."|".$row['dateoccurred']."|".$row['fk_users_id'];
			}						
			else
				echo "Result:7"; // No positions from other users found
			*/
			
			echo "Result:7";			

		}
		else
			echo "Result:6"; // No positions for selected user

		die();		
	} 
	
	
	
	
	
	// Trips
	if ($action=="gettripinfo")
	{
		if ( $tripname == "" )
		{
			echo "Result:6"; // trip not specified
			die();
		}
		
		$result=mysql_query("Select ID,Locked,Comments FROM trips WHERE FK_Users_ID = '$userid' and name='$tripname'");
		if ( $row=mysql_fetch_array($result) )
		{
			 $output.=$row['ID']."|".$row['Locked']."|".$row['Comments']."\n";    		  
		}
		else
		{
		 	  echo "Result:7"; // trip not found.
				die();					
		}					
    		
		echo "Result:0|$output";		
		die();
	}
	
	if ($action=="gettripfull" || $action=="gettriphighlights")
	{
		if ( $tripname == "" )
		{
			echo "Result:6"; // trip not specified
			die();
		}
		
		$tripid = "";
		$result=mysql_query("Select ID FROM trips WHERE FK_Users_ID = '$userid' and name='$tripname'");
		if ( $row=mysql_fetch_array($result) )
		{
			 $tripid=$row['ID'];					 		
		}
		else
		{
		 	  echo "Result:7"; // trip not found.
				die();					
		}		
				
    $output = ""; 		
    $result = mysql_query("select latitude,longitude,ImageURL,Comments,A2.URL IconURL, dateoccurred, A1.ID, A1.Altitude, A1.Speed, A1.Angle  from positions A1 left join icons A2 on A1.FK_Icons_ID=A2.ID where fk_trips_id='$tripid' order by dateoccurred");
    while( $row=mysql_fetch_array($result) )
    {
    	$output.=$row['latitude']."|".$row['longitude']."|".$row['ImageURL']."|".$row['Comments']."|".$row['IconURL']."|".$row['dateoccurred']."|".$row['ID']."|".$row['Altitude']."|".$row['Speed']."|".$row['Angle']."\n";    		  
    }
    		
		echo "Result:0|$output";		
		die();
	}
	
		
	if( $action=="gettriplist")
	{
		$order = $_GET["order"];

		
		$triplist = "";
		$sql = "SELECT A1.locked, A1.comments, A1.name, 
		(select min( A2.dateoccurred ) from positions A2 where A2.FK_TRIPS_ID=A1.ID) AS startdate, 
		(select max( A2.dateoccurred ) from positions A2 where A2.FK_TRIPS_ID=A1.ID) AS enddate, 
		(SELECT TIMEDIFF(max( A2.dateoccurred ),min( A2.dateoccurred )) from positions A2 where A2.FK_TRIPS_ID=A1.ID) AS totaltime,
		(select count(*) from positions A2 where A2.FK_TRIPS_ID=A1.ID AND A2.Comments is not null) as totalcomments,
		(select count(*) from positions A2 where A2.FK_TRIPS_ID=A1.ID AND A2.ImageURL is not null) as totalimages,
		(select IFNULL(max(speed), 0) from positions A2 where A2.FK_TRIPS_ID=A1.ID) as maxspeed,
		(select IFNULL(min(altitude), 0) from positions A2 where A2.FK_TRIPS_ID=A1.ID) as minaltitude,		
		(select IFNULL(max(altitude), 0) from positions A2 where A2.FK_TRIPS_ID=A1.ID) as maxaltitude
		from trips A1 where A1.FK_USERS_ID='$userid' ";
		
		$datefrom = urldecode($_GET["df"]);
		$dateto = urldecode($_GET["dt"]);
		
		if ( $datefrom != "" )
			$sql.=" and (select min( A2.dateoccurred ) from positions A2 where A2.FK_TRIPS_ID=A1.ID)>='$datefrom' ";
		if ( $dateto != "" )
			$sql.=" and (select min( A2.dateoccurred ) from positions A2 where A2.FK_TRIPS_ID=A1.ID)<='$dateto' ";			
		
		
		if ( $order == "" || $order == "0" )
			$sql.= " order by name";
		else
			$sql.= " order by startdate desc";
		
		

		$result = mysql_query($sql);					
		
		while( $row=mysql_fetch_array($result) )
		{
			$triplist.=$row['name']."|"
			.$row['startdate']."|"
			.$row['enddate']."|"
			.$row['comments']."|"
			.$row['locked']."|"
			.$row['totaltime']."|"
			.$row['totalcomments']."|"
			.$row['totalimages']."|"
			.$row['maxspeed']."|"
			.$row['minaltitude']."|"
			.$row['maxaltitude']
			."\n";			
		}

		$triplist = substr($triplist, 0, -1);		  
		echo "Result:0|$triplist";
		die();
	}
	
	if ( $action=="updatetripdata" )
	{				
		 if ( $tripname == "" )
		 {
			echo "Result:6"; // trip not specified
		 	die();
		 }
		 
		 $tripid = "";
		 $locked = 0;
		 $result=mysql_query("Select ID, Locked FROM trips WHERE FK_Users_ID = '$userid' and name='$tripname'");
		 if ( $row=mysql_fetch_array($result) )
		 {
			 $tripid=$row['ID'];		
			 $locked = $row['Locked'];
			 
			 if ( $locked == 1 )
			 {
			 		echo "Result:8";	
			 		die();
			 }
		 }
		 else
		 {
		 	  echo "Result:7"; // trip not found.
				die();					
		 }	 	
		 
	 
		 $sql = "Update trips set ";
		 
		 if ( isset($_GET["comments"]))
		 {
				$comments = urldecode($_GET["comments"]);
		 			 
		 		if ( $comments != "" )
					$sql.=" comments='$comments',";
				else
					$sql.=" comments=null,";				
		 }
		 	 		 		 
		 $sql.="id=id where id='$tripid' AND FK_Users_ID = '$userid'";
		 		 
		 mysql_query($sql);		 	 
		 echo "Result:0";
  	 die();			 	
	}	
	
	if ( $action=="updatelocking" )
	{				
		 if ( $tripname == "" )
		 {
			echo "Result:6"; // trip not specified
		 	die();
		 }
		 
		 $tripid = "";
		 $result=mysql_query("Select ID FROM trips WHERE FK_Users_ID = '$userid' and name='$tripname'");
		 if ( $row=mysql_fetch_array($result) )
		 {
			 $tripid=$row['ID'];		
		 }
		 else
		 {
		 	  echo "Result:7"; // trip not found.
				die();					
		 }	 	
		 
		 $locked = urldecode($_GET["locked"]);
		 
		 $sql = "Update trips set locked='$locked' where id='$tripid' AND FK_Users_ID = '$userid'";
		 		 		 		 
		 mysql_query($sql);		 	 
		 echo "Result:0";
  	 die();			 	
	}
	
	if ( $action=="deletetrip" )
	{		
		 if ( $tripname == "" )
		 {
			echo "Result:6"; // trip not specified
		 	die();
		 }
		 		 
		 $tripid = "";
		 $locked = 0;
		 $result=mysql_query("Select ID, Locked FROM trips WHERE FK_Users_ID = '$userid' and name='$tripname'");
		 if ( $row=mysql_fetch_array($result) )
		 {
			 $tripid=$row['ID'];		
			 $locked = $row['Locked'];
			 
			 if ( $locked == 1 )
			 {
			 		echo "Result:8";	
			 		die();
			 }
			 
			 mysql_query("delete from positions where fk_trips_id='$tripid' AND FK_Users_ID = '$userid'");
			 mysql_query("delete from trips where id='$tripid' AND FK_Users_ID = '$userid'");			 			 
			 
			 echo "Result:0";
			 die();			 
		 }
		 else
		 {
		 	  echo "Result:7"; // trip not found.
				die();					
		 }	 		
	}
	
	if ( $action=="addtrip" )
	{				
		 if ( $tripname == "" )
		 {
			echo "Result:6"; // trip not specified
		 	die();
		 }
		 	 		 
		 mysql_query("Insert into trips (name,fk_users_id) values ('$tripname','$userid')");		 	 
		 echo "Result:0";
  	 die();			 	
	}	
	
	if ( $action=="renametrip" )
	{				
		 if ( $tripname == "" )
		 {
			echo "Result:6"; // trip not specified
		 	die();
		 }
		 
		 $newname = $_GET["newname"];		 
		 if ( $newname == "" )
		 {
			echo "Result:9"; // new name not specified
		 	die();
		 }
		 
		 
		 $locked = 0;
		 $result=mysql_query("Select Locked FROM trips WHERE FK_Users_ID = '$userid' and name='$tripname'");
		 if ( $row=mysql_fetch_array($result) )
		 {
			 $locked = $row['Locked'];
			 
			 if ( $locked == 1 )
			 {
			 		echo "Result:8";	
			 		die();
			 }
		 }
		 else
		 {
		 	  echo "Result:7"; // trip not found.
				die();					
		 }	 	
		 
		 
		 $result=mysql_query("Select ID FROM trips WHERE FK_Users_ID = '$userid' and name='$newname'");			
		 if ( $row=mysql_fetch_array($result) )
		 {
		 		echo "Result:10"; // new name already exists
		 		die();
		 }		
		 		 
		 mysql_query("Update trips set name='$newname' where name='$tripname' AND FK_Users_ID = '$userid'");		 	 
		 echo "Result:0";
  	 die();			 	
	}	
    }

    // Run by default when included/required, unless __norun is set to true
    if (!isset($__norun) || !$__norun) {
        echo run(toConnectionArray($DBIP, $DBNAME, $DBUSER, $DBPASS));
    }

?>

