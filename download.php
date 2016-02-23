<?php
  
  require_once('config.php');
    
// DMR didn't see where this was used so removed.    
//  $requireddb = urldecode($_GET["db"]);     
//  if ( $requireddb == "" || $requireddb < 7 )
//  {
//      echo "<Result>5</Result>";
//      die;
//  }   
  
  if(!@mysql_connect("$DBIP","$DBUSER","$DBPASS"))
  {
    echo "<Result>4</Result>";
    die();
  }
  
  mysql_select_db("$DBNAME");
  
  $showbearings = 0;
  
// DMR Use the Cookies to get the user and password-->
  if($public_page != "yes" && isset($_COOKIE["username"]) && isset($_COOKIE["password"]))
  {
//      $loggedin = "yes";
      $username = $_COOKIE["username"];
      $password = $_COOKIE["password"];
  } else {
    echo "<Result>Not Logged in or this is not a private system</Result>";
    die();
  }

  
  
  $action = $_GET["a"];
//  $username = urldecode($_GET["u"]);
//  $password = urldecode($_GET["p"]);
  $datefrom = urldecode($_GET["df"]);
  $dateto = urldecode($_GET["dt"]);
  $tripname = urldecode($_GET["tn"]);
  $showbearings = urldecode($_GET["sb"]);
    
  
  $result=mysql_query("Select ID FROM users WHERE username = '$username' and password='$password'");
  if ( $row=mysql_fetch_array($result) )
  {
      $userid=$row['ID'];   // Good, user and password are correct.
  }
  else
  {
    echo "Error: User/password not valid.";
    die();    
  }
  
  
        
  
  if($action=="kml")
  {
    // Condition
    $cond = ""; 
    if ($tripname == "<None>" )   
      $cond = "WHERE FK_Trips_ID is null AND A1.FK_USERS_ID='$userid' ";
    else if ($tripname != "" )
      $cond = "INNER JOIN trips A2 ON A1.FK_Trips_ID=A2.ID AND A2.Name='$tripname' WHERE A1.FK_USERS_ID='$userid' ";
    else
      $cond = "LEFT JOIN trips A2 ON A1.FK_Trips_ID=A2.ID WHERE A1.FK_USERS_ID='$userid' ";     
                        
    if ( $datefrom != "" )
      $cond .=" and DateOccurred>='$datefrom' ";
    if ( $dateto != "" )
      $cond .=" and DateOccurred<='$dateto' ";                    
      
    $cond .=" order by dateoccurred asc"; 


    
    // Generate code for custom icons
    $customicons = "";  
    $sql = "select distinct A3.ID, A3.URL  from icons A3 inner join positions A1 on A1.fk_icons_id = A3.ID ";
    $sql = $sql.$cond;
        
    $result = mysql_query($sql);    
  
    while( $row = mysql_fetch_array($result) )
    {
      $customicons .="<Style id='CustomIcon".$row['ID']."'>";
        $customicons .="<IconStyle>";
          $customicons .="<Icon>";
            $customicons .="<href>".$row['URL']."</href>";
          $customicons .="</Icon>";
        $customicons .="</IconStyle>";
      $customicons .="</Style>";    
    }
    
    // Default icons
    $customicons .="<Style id='IconYellow'>";
      $customicons .="<IconStyle>";
        $customicons .="<scale>0.5</scale>";
        $customicons .="<Icon>";
          $customicons .="<href>http://labs.google.com/ridefinder/images/mm_20_yellow.png</href>";
          $customicons .="<x>0</x>";
          $customicons .="<y>0</y>";
          $customicons .="<w>32</w>";
          $customicons .="<h>32</h>";
        $customicons .="</Icon>";
      $customicons .="</IconStyle>";      
    $customicons .="</Style>";    
    
    $customicons .="<Style id='IconGreen'>";
      $customicons .="<IconStyle>";
        $customicons .="<Icon>";
          $customicons .="<href>http://labs.google.com/ridefinder/images/mm_20_green.png</href>";
        $customicons .="</Icon>";
      $customicons .="</IconStyle>";
    $customicons .="</Style>";
    
    $customicons .="<Style id='IconRed'>";
      $customicons .="<IconStyle>";
        $customicons .="<Icon>";
          $customicons .="<href>http://labs.google.com/ridefinder/images/mm_20_red.png</href>";
        $customicons .="</Icon>";
      $customicons .="</IconStyle>";
    $customicons .="</Style>";
    
    $currentpath="http://".$_SERVER['HTTP_HOST']."/".basename(getcwd());
    
    $customicons .="<Style id='IconArrow0'>";
      $customicons .="<IconStyle>";
        $customicons .="<scale>0.5</scale>";
        $customicons .="<Icon>";
          $customicons .="<href>".$currentpath."/arrow0.png</href>";
          $customicons .="<x>0</x>";
          $customicons .="<y>0</y>";
          $customicons .="<w>32</w>";
          $customicons .="<h>32</h>";
        $customicons .="</Icon>";
      $customicons .="</IconStyle>";      
    $customicons .="</Style>";  
    
    $customicons .="<Style id='IconArrow45'>";
      $customicons .="<IconStyle>";
        $customicons .="<scale>0.5</scale>";
        $customicons .="<Icon>";
          $customicons .="<href>".$currentpath."/arrow45.png</href>";
          $customicons .="<x>0</x>";
          $customicons .="<y>0</y>";
          $customicons .="<w>32</w>";
          $customicons .="<h>32</h>";
        $customicons .="</Icon>";
      $customicons .="</IconStyle>";      
    $customicons .="</Style>";      
    
    $customicons .="<Style id='IconArrow90'>";
      $customicons .="<IconStyle>";
        $customicons .="<scale>0.5</scale>";
        $customicons .="<Icon>";
          $customicons .="<href>".$currentpath."/arrow90.png</href>";
          $customicons .="<x>0</x>";
          $customicons .="<y>0</y>";
          $customicons .="<w>32</w>";
          $customicons .="<h>32</h>";
        $customicons .="</Icon>";
      $customicons .="</IconStyle>";      
    $customicons .="</Style>";      
        
        
    $customicons .="<Style id='IconArrow135'>";
      $customicons .="<IconStyle>";
        $customicons .="<scale>0.5</scale>";
        $customicons .="<Icon>";
          $customicons .="<href>".$currentpath."/arrow135.png</href>";
          $customicons .="<x>0</x>";
          $customicons .="<y>0</y>";
          $customicons .="<w>32</w>";
          $customicons .="<h>32</h>";
        $customicons .="</Icon>";
      $customicons .="</IconStyle>";      
    $customicons .="</Style>";          
    
    
    $customicons .="<Style id='IconArrow180'>";
      $customicons .="<IconStyle>";
        $customicons .="<scale>0.5</scale>";
        $customicons .="<Icon>";
          $customicons .="<href>".$currentpath."/arrow180.png</href>";
          $customicons .="<x>0</x>";
          $customicons .="<y>0</y>";
          $customicons .="<w>32</w>";
          $customicons .="<h>32</h>";
        $customicons .="</Icon>";
      $customicons .="</IconStyle>";      
    $customicons .="</Style>";      
    
    $customicons .="<Style id='IconArrow225'>";
      $customicons .="<IconStyle>";
        $customicons .="<scale>0.5</scale>";
        $customicons .="<Icon>";
          $customicons .="<href>".$currentpath."/arrow225.png</href>";
          $customicons .="<x>0</x>";
          $customicons .="<y>0</y>";
          $customicons .="<w>32</w>";
          $customicons .="<h>32</h>";
        $customicons .="</Icon>";
      $customicons .="</IconStyle>";      
    $customicons .="</Style>";      
    
    
    $customicons .="<Style id='IconArrow270'>";
      $customicons .="<IconStyle>";
        $customicons .="<scale>0.5</scale>";
        $customicons .="<Icon>";
          $customicons .="<href>".$currentpath."/arrow270.png</href>";
          $customicons .="<x>0</x>";
          $customicons .="<y>0</y>";
          $customicons .="<w>32</w>";
          $customicons .="<h>32</h>";
        $customicons .="</Icon>";
      $customicons .="</IconStyle>";      
    $customicons .="</Style>";      
    
    $customicons .="<Style id='IconArrow315'>";
      $customicons .="<IconStyle>";
        $customicons .="<scale>0.5</scale>";
        $customicons .="<Icon>";
          $customicons .="<href>".$currentpath."/arrow315.png</href>";
          $customicons .="<x>0</x>";
          $customicons .="<y>0</y>";
          $customicons .="<w>32</w>";
          $customicons .="<h>32</h>";
        $customicons .="</Icon>";
      $customicons .="</IconStyle>";      
    $customicons .="</Style>";      
    
    
    
    // Main query
    if ($tripname == "<None>" )   
      $sql = "select UNIX_TIMESTAMP(DateOccurred) as UnixDateOccured, DateOccurred,latitude, longitude,speed,altitude,fk_icons_id as customicon, null as tripname,A1.comments,A1.imageurl,A1.angle,A1.signalstrength,A1.signalstrengthmax,A1.signalstrengthmin,A1.batterystatus from positions A1 ";        
    else 
      $sql = "select UNIX_TIMESTAMP(DateOccurred) as UnixDateOccured, DateOccurred,latitude, longitude,speed,altitude,fk_icons_id as customicon, A2.Name as tripname,A1.comments,A1.imageurl,A1.angle,A1.signalstrength,A1.signalstrengthmax,A1.signalstrengthmin,A1.batterystatus from positions A1 ";         
          
    $sql = $sql.$cond;
                
          
    $result = mysql_query($sql);    
    $num_rows = mysql_num_rows($result);            
  
    $header = "<?xml version='1.0' encoding='utf-8' ?>";
    $header .= "<kml xmlns='http://earth.google.com/kml/2.0'>";
    $header .="<Document>";   
    
    // Styles     
    $header .=$customicons;
                  
    
    $output ="<NetworkLinkControl><minRefreshPeriod>12</minRefreshPeriod></NetworkLinkControl>";  
    
      $count = 0;
      $group = "";
      
    while( $row=mysql_fetch_array($result) )
    {
      $speedMPH = number_format($row['speed']*2.2369362920544,2);
      $speedKPH = number_format($row['speed']*3.6,2); 
      if ($row['altitude'] > 0) 
      {  
        $altitudeFeet = number_format($row['altitude']*3.2808399,2);
        $altitudeM = number_format($row['altitude'],2);     
      } else {
        $altitudeFeet = number_format(0,2);
        $altitudeM = number_format(0,2);
      }
      $angle = number_format($row['angle'],2);      
      
      if ( $count == $num_rows -1 ) // Last pushpin
      {
        $output .="<LookAt>";           
          $output .="<longitude>".$row['longitude']."</longitude>";   
          $output .="<latitude>".$row['latitude']."</latitude>";
          $output .="<range>900</range>";
          $output .="<tilt>65.01</tilt>";
          $output .="<heading>216</heading>";     
        $output .="</LookAt>";
        
        $output .="<visibility>1</visibility>";
        $output .="<open>0</open>";
        
        $output .="\n<Placemark>";  
        
        //  $output .="<TimeStamp><when>2007-09-12T15:07:27Z</when></TimeStamp>";
          $output .="\n  <TimeStamp><when>" . strftime("%Y-%m-%dT%TZ", $row['UnixDateOccured']) . "</when></TimeStamp>";
                              
          $output .="\n  <name>";
            if ( $row['tripname'] != "" )
              $output .="Trip: ".$row['tripname']." ";                  
            $output .=$row['DateOccurred'];                 
          $output .="</name>";          
                    
          $output .="\n  <description>";
            $output .="<![CDATA[User: <b>".$username."</b><hr>";
            $output .="<table><tr><td>Time: ".$row['DateOccurred']."</td></tr>";
            $output .="<tr><td>Trip: ".$row['tripname']."</td></tr>";
            $output .="<tr><td>Speed: ".$speedMPH." MPH (".$speedKPH." km/h)</td></tr>";
            
            if ( $row['altitude'] < 0 )
              $output .="<tr><td>Altitude: Unknown</td></tr>";
            else
              $output .="<tr><td>Altitude: ".$altitudeFeet." ft (".$altitudeM." m)</td></tr>";
                          
            if ($row['comments'] != "" )
              $output .="<tr><td>Comments: ".$row['comments']."</td></tr>";           
              
            if ($row['signalstrength'] != "" )
            {
              $output .="<tr><td>Signal Strength: ".$row['signalstrength']."dBm";
              if ($row['signalstrengthmax'] != "" && $row['signalstrengthmin'] != "" )
              {
                 $RANGE=($row['signalstrengthmax']-$row['signalstrengthmin'])/5;
 
                 if ( $row['signalstrength'] >= $row['signalstrengthmax'] - $RANGE )
                    $output .=" (Excellent)";
                 else if ( $row['signalstrength'] >= $row['signalstrengthmax'] - $RANGE*2 )
                    $output .=" (Very Good)";
                 else if ( $row['signalstrength'] >= $row['signalstrengthmax'] - $RANGE*3 )
                    $output .=" (Good)";
                 else if ( $row['signalstrength'] >= $row['signalstrengthmax'] - $RANGE*4 )
                    $output .=" (Poor)";                    
                 else 
                    $output .=" (Very Poor)";                                                                         
              }
              $output .="</td></tr>";                         
            }
            
            if ($row['batterystatus'] != "" )
            {
              $output .="<tr><td>Battery Status: ".$row['batterystatus']."%</td></tr>";
            }
            
              
            if ($row['imageurl'] != "" )
              $output .="<tr><td><a href='".$row['imageurl']."'><img src='".$row['imageurl']."' height='200' width='240'></a></td></tr>";                         
                            
            $output .="</table><hr><b>TrackMe. Created by Luis Espinosa</b><BR>http://www.luisespinosa.com]]>";
          $output .="</description>";
        
          if ( $row['customicon'] != "" )
            $output .="\n  <styleUrl>#CustomIcon".$row['customicon']."</styleUrl>";       
          else
            $output .="\n  <styleUrl>#IconRed</styleUrl>";                                    
        
          $output .="\n  <Point>";                          
            $output .="\n    <altitudeMode>clampedToGround</altitudeMode>";
            $output .="\n    <coordinates>".$row['longitude'].",".$row['latitude'].",".$altitudeM."</coordinates>";
          $output .="\n  </Point>";                         
          
        $output .="\n</Placemark>";
                
      }
      else // Rest of the pushpins
      {
          $output .="\n<Placemark>";
          
          
      //    $output .="<TimeStamp><when>2007-09-12T15:07:27Z</when></TimeStamp>";         
          $output .="\n  <TimeStamp><when>" . strftime("%Y-%m-%dT%TZ", $row['UnixDateOccured']) . "</when></TimeStamp>";         
          
          if ($row['comments'] != "" )
          {
            $output .="\n  <name>";
            $output .=$row['comments'];
            $output .="\n  </name>";
//          } else {
//            $output .="<name>";
//            $output .=$row['DateOccurred'];
//            $output .="</name>";
          }
                                          
          $output .="\n  <description>";
      //      $output .="<![CDATA[User: <b>".$username."</b><hr>";
      //      $output .="<table><tr><td>Time: ".$row['DateOccurred']."</td></tr>";


            $output .="<![CDATA[Time: <b>".$row['DateOccurred']."</b><hr>";
            $output .="<table><tr><td>User: ".$username."</td></tr>";

            $output .="<tr><td>Trip: ".$row['tripname']."</td></tr>";
            $output .="<tr><td>Speed: ".$speedMPH." MPH (".$speedKPH." km/h)</td></tr>";

            if ( $row['altitude'] < 0 )
              $output .="<tr><td>Altitude: Unknown</td></tr>";
            else
              $output .="<tr><td>Altitude: ".$altitudeFeet." ft (".$altitudeM." m)</td></tr>";            
                          
            if ($row['comments'] != "" )
              $output .="<tr><td>Comments: ".$row['comments']."</td></tr>";
            
            if ($row['signalstrength'] != "" )
            {
              $output .="<tr><td>Signal Strength: ".$row['signalstrength']."dBm";
              if ($row['signalstrengthmax'] != "" && $row['signalstrengthmin'] != "" )
              {
                 $RANGE=($row['signalstrengthmax']-$row['signalstrengthmin'])/5;
 
                 if ( $row['signalstrength'] >= $row['signalstrengthmax'] - $RANGE )
                    $output .=" (Excellent)";
                 else if ( $row['signalstrength'] >= $row['signalstrengthmax'] - $RANGE*2 )
                    $output .=" (Very Good)";
                 else if ( $row['signalstrength'] >= $row['signalstrengthmax'] - $RANGE*3 )
                    $output .=" (Good)";
                 else if ( $row['signalstrength'] >= $row['signalstrengthmax'] - $RANGE*4 )
                    $output .=" (Poor)";                    
                 else 
                    $output .=" (Very Poor)";                                                                         
              }

              $output .="</td></tr>";                         
            }
            
            if ($row['batterystatus'] != "" )
            {
              $output .="<tr><td>Battery Status: ".$row['batterystatus']."%</td></tr>";
            }
            

            if ($row['imageurl'] != "" )
              $output .="<tr><td><a href='".$row['imageurl']."'><img src='".$row['imageurl']."' height='200' width='240'></a></td></tr>";                         
              
            $output .="</table><hr><b>TrackMe. Created by Luis Espinosa</b><BR>http://www.luisespinosa.com]]>";
          $output .="\n  </description>";
          
          if ( $count == 0 )  // First pushpin
            $output .="\n  <styleUrl>#IconGreen</styleUrl>";                            
          else
          {
            if ( $row['customicon'] != "" )
              $output .="\n  <styleUrl>#CustomIcon".$row['customicon']."</styleUrl>";
            else
            {
                if ( $row['angle'] != "" && $showbearings == 1 )
                {                 
                  if ( $row['angle']<22.5 )
                    $output .="<styleUrl>#IconArrow0</styleUrl>";                           
                  else if ( $row['angle']<67.5 )
                    $output .="<styleUrl>#IconArrow45</styleUrl>";      
                  else if ( $row['angle']<112.5 )
                    $output .="<styleUrl>#IconArrow90</styleUrl>";                          
                  else if ( $row['angle']<157.5 )
                    $output .="<styleUrl>#IconArrow135</styleUrl>";                                             
                  else if ( $row['angle']<202.5 )
                    $output .="<styleUrl>#IconArrow180</styleUrl>";                                             
                  else if ( $row['angle']<247.5 )
                    $output .="<styleUrl>#IconArrow225</styleUrl>";                                                                 
                  else if ( $row['angle']<292.5 )
                    $output .="<styleUrl>#IconArrow270</styleUrl>";                                                                                     
                  else if ( $row['angle']<337.5 )
                    $output .="<styleUrl>#IconArrow315</styleUrl>";                                                                                                         
                  else
                    $output .="<styleUrl>#IconArrow0</styleUrl>";                                             
                }
                else
                  $output .="<styleUrl>#IconYellow</styleUrl>";                           
            }
          }
        
          $output .="\n  <Point>";                          
            $output .="\n    <altitudeMode>clampedToGround</altitudeMode>";
            // Add the Altitude to the point - by definition it's metric
//            $output .="<coordinates>".$row['longitude'].",".$row['latitude']."</coordinates>";
            $output .="\n    <coordinates>".$row['longitude'].",".$row['latitude'].",".$altitudeM."</coordinates>";
          $output .="\n  </Point>";                         
          
        $output .="\n</Placemark>";       
      }
      
      // Since we locked the altitude to the ground only send Lon and Lat to the path
      $group.=$row['longitude'].",".$row['latitude']." ";
//      $group.=$row['longitude'].",".$row['latitude'].",2 ";
          
      $count = $count + 1;          
    }   


    // Draw line    
    $output .="<Placemark>";                        
      $output .="<Style>";
        $output .="<LineStyle>";
          $output .="<color>ff000000</color>";                    
          $output .="<width>3</width>";                   
        $output .="</LineStyle>"; 
      $output .="</Style>";               
      
      $output .="<LineString>";
          $output .="<extrude>1</extrude>";                   
          $output .="<tessellate>1</tessellate>";                   
          $output .="<altitudeMode>clampedToGround</altitudeMode>";
          $output .="<coordinates>".$group."</coordinates>";
      $output .="</LineString>";                    
    $output .="</Placemark>";         
    
        
    $output .="</Document>";        
    $output .="</kml>";
    
    $output = $header.$output;
    
    
    // Create file
    $FileName = str_replace(" ", "_", $username . "_" . $tripname . "_" . $datefrom . "_" . $dateto . ".kml"); 
  
// No need to save to server    
//    if ( !file_exists("routes") )
//      mkdir("routes");

//    $file = "routes/".$FileName;   
//    $file_handle = fopen($file,"w");
//    fwrite($file_handle, $output);
//    fclose($file_handle);   
  
    // Set the name of the downloaded file                                 
    header("Content-type: text/kml");
    header("Content-Disposition:attachment;filename=" . $FileName);
    echo "$output"; 
  
  }
  else if ($action = "gpx") 
  {
    // Condition
    $cond = ""; 
    if ($tripname == "<None>" )   
      $cond = "WHERE FK_Trips_ID is null AND A1.FK_USERS_ID='$userid' ";
    else if ($tripname != "" )
      $cond = "INNER JOIN trips A2 ON A1.FK_Trips_ID=A2.ID AND A2.Name='$tripname' WHERE A1.FK_USERS_ID='$userid' ";
    else
      $cond = "LEFT JOIN trips A2 ON A1.FK_Trips_ID=A2.ID WHERE A1.FK_USERS_ID='$userid' ";     

    if ( $datefrom != "" )
      $cond .=" and DateOccurred>='$datefrom' ";
    if ( $dateto != "" )
      $cond .=" and DateOccurred<='$dateto' ";
      
    $cond .=" order by dateoccurred desc";

    // Main query
    if ($tripname == "<None>" ) {   
      $sql = "select UNIX_TIMESTAMP(DateOccurred) as DateOccured,latitude, longitude,speed,altitude,fk_icons_id as customicon, null as tripname,A1.comments,A1.imageurl,A1.angle from positions A1 ";
      $tripname = "None";       
    } else { 
      $sql = "select UNIX_TIMESTAMP(DateOccurred) as DateOccured,latitude, longitude,speed,altitude,fk_icons_id as customicon, A2.Name as tripname,A1.comments,A1.imageurl,A1.angle from positions A1 ";
    }         

    $sql = $sql.$cond;
    $result = mysql_query($sql);    
    $num_rows = mysql_num_rows($result);

    $n=0;
    $bounds_lat_min = 0;
    $bounds_lat_max = 0;
    $bounds_lon_min = 0;
    $bounds_lon_max = 0;
    $wptdata="";
    $trkptdata="<trk>\n";
    $trkptdata.="<trkseg>\n";
    while( $row=mysql_fetch_array($result) )
    {
      if(($row['latitude']<$bounds_lat_min && $bounds_lat_min!=0) || $bounds_lat_min==0) { $bounds_lat_min = $row['latitude']; }
      if(($row['latitude']>$bounds_lat_max && $bounds_lat_max!=0) || $bounds_lat_max==0) { $bounds_lat_max = $row['latitude']; }
      if(($row['longitude']<$bounds_lon_min && $bounds_lon_min!=0) || $bounds_lon_min==0) { $bounds_lon_min = $row['longitude']; }
      if(($row['longitude']>$bounds_lon_max && $bounds_lon_max!=0) || $bounds_lon_max==0) { $bounds_lon_max = $row['longitude']; }
      $speedMPH = number_format($row['speed']*2.2369362920544,2);
      $speedKPH = number_format($row['speed']*3.6,2);   
      $altitudeFeet = number_format($row['altitude']*3.2808399,2);
      $altitudeM = number_format($row['altitude'],2);     
      $angle = number_format($row['angle'],2);
      /*
      $wptdata.="<wpt lat=\"" . $row['latitude'] . "\" lon=\"" . $row['longitude'] . "\">\n";
      $wptdata.=" <ele>" . $row['altitude'] . "</ele>\n";
      $wptdata.=" <time>".date('Y-m-d',$row['DateOccured'])."T".date('H:i:s',$row['DateOccured'])."Z</time>\n";
      $wptdata.=" <name><![CDATA[".date('Y-m-d',$row['DateOccured'])."-".str_pad($n,3,"0", STR_PAD_LEFT)."]]></name>\n";
      //$wptdata.=" <cmt><![CDATA[".$row['comment']."]]></cmt>\n";
      //$wptdata.=" <desc><![CDATA[Speed: ".$speedMPH." MPH (".$speedKPH." km/h)]]></desc>\n";
      //$wptdata.=" <sym>Dot</sym>\n";
      //$wptdata.=" <type><![CDATA[Dot]]></type>\n";
      $wptdata.="</wpt>\n";*/
      $trkptdata.="<trkpt lat=\"" . $row['latitude'] . "\" lon=\"" . $row['longitude'] . "\">\n";
      $trkptdata.=" <ele>" . $altitudeM . "</ele>\n";
      $trkptdata.=" <time>".date('Y-m-d',$row['DateOccured'])."T".date('H:i:s',$row['DateOccured'])."Z</time>\n";
      $trkptdata.=" <desc><![CDATA[Lat.=" . $row['latitude'] . ", Long.=" . $row['logitude'] . ", Alt.=" . $altitudeM . ", Speed=".$speedKPH."Km/h, Course=" . $angle . "deg.]]></desc>\n";
      $trkptdata.="</trkpt>\n";
      $n++;
    }
    $trkptdata.="</trkseg>\n</trk>\n</gpx>";
    $header="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
    $header.="<gpx version=\"1.1\" creator=\"GPX-Exporter by Ulrich Wolf - http://wolf-u.li\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns=\"http://www.topografix.com/GPX/1/1\" xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd\">\n";
    $header.="<metadata>\n";
    $header.="  <name>".$tripname."</name>\n";
    $header.="  <desc>GPX-Track of TrackMe</desc>\n";
    $header.="  <author>\n";
    $header.="    <name>Ulrich Wolf</name>\n";
    $header.="    <link href=\"http://wolf-u.li\">\n";
    $header.="      <text>wolf-u.li</text>\n";
    $header.="    </link>\n";
    $header.="  </author>\n";
    $header.="  <time>".date('Y-m-d')."T".date('H:i:s')."Z</time>\n";
    $header.="  <keywords><![CDATA[Geocaching,Geotagging,GPS]]></keywords>\n";
    $header.="<bounds minlat=\"" . $bounds_lat_min . "\" minlon=\"" . $bounds_lon_min . "\" maxlat=\"" . $bounds_lat_max . "\" maxlon=\"" . $bounds_lon_max . "\"/>\n";
    $header.="</metadata>\n";
    

    // Create file
    $FileName = str_replace(" ", "_", $username . "_" . $tripname . "_" . $datefrom . "_" . $dateto . ".gpx"); 

// No need to save to server    
//    // Create file        
//    if ( !file_exists("routes") )
//      mkdir("routes");

//    $file = "routes/".$username.".gpx";   
//    $file_handle = fopen($file,"w");
//    fwrite($file_handle, $header.$wptdata.$trkptdata);
//    fclose($file_handle);   

    // Set the name of the downloaded file                                 
    header("Content-type: text/gpx");
    header("Content-Disposition:attachment;filename=" . $FileName);

    echo $header.$wptdata.$trkptdata;
    //echo "<Result>0</Result>";

  }
    

?> 