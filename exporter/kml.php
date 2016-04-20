<?php

    require_once("base.php");

    class KMLExporter extends Exporter
    {

        private function create_icon($id, $href, $scaled)
        {
            $code = "<Style id='$id'>";
            $code .= "<IconStyle>";
            if ($scaled)
                $code .="<scale>0.5</scale>";
            $code .= "<Icon>";
            $code .= "<href>$href</href>";
            if ($scaled) {
                $code .= "<x>0</x>";
                $code .= "<y>0</y>";
                $code .= "<w>32</w>";
                $code .= "<h>32</h>";
            }
            $code .= "</Icon>";
            $code .= "</IconStyle>";
            $code .= "</Style>";
            return $code;
        }

        public function export($showbearings)
        {
            // Temporarily cache local variables like $db as long as it isn't using $this
            // A future patch is removing the usage anyway so a more through change is not
            // necessary.
            $username = $this->username;

    
    $customicons = "";  
    
    $currentpath="http://".$_SERVER['HTTP_HOST']."/".basename(getcwd());

            foreach (array("yellow", "green", "red") as $color)
            {
                $id = "Icon" . ucfirst($color);
                $href = $currentpath . "/mm_20_$color.png";
                $customicons .= $this->create_icon($id, $href, $color === "yellow");
            }

            for ($angle = 0; $angle < 360; $angle += 45)
            {
                $id = "IconArrow$angle";
                $href = $currentpath . "/arrow$angle.png";
                $customicons .= $this->create_icon($id, $href, true);
            }
            $result = $this->exec_sql(true);
            $result = $result->fetchAll();
  
    $header = "<?xml version='1.0' encoding='utf-8' ?>";
    $header .= "<kml xmlns='http://earth.google.com/kml/2.0'>";
    $header .="<Document>";   
                  
    
    $output ="<NetworkLinkControl><minRefreshPeriod>12</minRefreshPeriod></NetworkLinkControl>";  
    
      $group = "";
            $iconIds = array();
      
    for ($count = 0; $count < count($result); $count++)
    {
        $row = $result[$count];
                $this->simulate_old($row);
                if ($row["FK_Icons_ID"])
                    $iconIds[] = $row["FK_Icons_ID"];
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
      $row["UnixDateOccured"] = strtotime($row["DateOccurred"]);
      
      if ( $count == count($result) - 1) // Last pushpin
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
    
            // Generate code for custom icons
            $iconIds = array_unique($iconIds, SORT_NUMERIC);
            if (count($iconIds) > 0)
            {
                // Wrap icon ids in an array as it should add the array itself as a parameter, not each value
                $params = implode(',', array_fill(0, count($iconIds), '?'));
                $result = $this->db->exec_sql("SELECT ID, URL FROM icons WHERE ID IN ($params)", $iconIds);
                while( $row = $result->fetch() )
                {
                    $customicons .= $this->create_icon("CustomIcon$row[ID]", $row["URL"], false);
                }
            }
            $output = $header.$customicons.$output;
    
    
            return $output;
  
        }
    }

?>
