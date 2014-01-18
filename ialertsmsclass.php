
<?php  include("config.php"); 

   class ialert
   {
       protected $msg;
       protected $phone;
       protected $time;
       Protected $latitude;
       Protected $longitude;
       protected $journ_id;
       protected $phone_number;

        public function setname($msg,$phone,$time){
              $this->msg=$msg;
              $this->phone=$phone;
              $this->time=$time;

        }

        public function getname(){
        	//return $this->msg;
        	return $this->phone;
        }

         public function filter(){
               $msg=$this->msg;

 $counter = strpos($msg, ",");
     
        
         $k= $counter + 1;
         $m=strlen($msg);

     for($i=$k; $i < $m; $i++){
      
                  $position=$msg[$i];
               if($msg[$i]==","){
                         $counter2=$i;
               }
      }
                  
                             
     for($i=$k; $i < $counter2; $i++){
         $city[]= $msg[$i];

            
     }
             
             echo"<br/>";
     for($i=$counter2+1; $i < $m; $i++){
         $additional[]= $msg[$i];
            
     }   
               
                    echo"<br/>";
              $citystr=implode("", $city);
                  echo $citystr;
     
                    echo"<br/>";
              $additionalstr=implode("", $additional);
                  echo $additionalstr;
                  // $this->latitude=0.315804;
                  // $this->longitude=32.5762466;
            $address="$additionalstr,$citystr,Uganda";
         $prepAddr = str_replace(' ','+',$address);
        $geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
        $output= json_decode($geocode);
        if($output->status == OK)
        {
        $this->latitude = $output->results[0]->geometry->location->lat;
         $this->longitude= $output->results[0]->geometry->location->lng;   
              
          }    

          else {
          	     $info="Please use this format: ialert [space] tell your story, district, home area ";
          	smsmediscasend($this->phone,$info);
          }

         }

         public function getuserid(){
         	    $latitude=$this->latitude;
         	    $longitude=$this->longitude;

		//querying the selected table from the database
		$result5 = mysql_query("select * from location");
	 if(!$result5){
		die("Database connection failed" . mysql_error());
		}	
		
		else
		{
		//constructing the journalists table
		
			while($row5 = mysql_fetch_array($result5)){
			
			
			$journs_ids[] = $row5["userid"];
							
			$journs_latitudes[] = $row5["latitude"];
			
			$journs_longitudes[] = $row5["longitude"];
			
			}
		
		
		//calculating the distance between the source and the various journalists
			for($j=0; $j<sizeof($journs_ids); $j++){
				$jlat = $latitude - $journs_latitudes[$j];
				
			
				$jlongt = $longitude - $journs_longitudes[$j];
				
				$fd[$j] =  sqrt(pow($jlat,2) + pow($jlongt,2));
			}
			
	//displaying the distance between the source and the various journalists
		
		
	//calculating as well as displaying the nearest journalist to the source
		 for($i=0; $i < sizeof($journs_ids); $i++)
			{
				if($fd[$i]== min($fd))
				{
					$position = $i;
					//echo $i;
					
					
				}
			}	
		
			    $this->journ_id=$journs_ids[$position];
			 
			 	
                 
         }
           

   return $this->journ_id;
  
}

   public function getphone(){
         $id=$this->getuserid();
     $sql="select * from users where id='{$id}'";
      

$res=mysql_query($sql);
$rw=mysql_fetch_array($res);

if($res)
{

$this->phone_number=$rw["phone_number"];
}

  return $this->phone_number;
   }  

   public function insertintodb(){
   	        $message=$this->msg;
   	        $number=$this->phone;
   	        $time=$this->time;
   	        $latitude=$this->latitude;
   	        $longitude=$this->longitude;
   	        $id=$this->journ_id;
   	            echo "<br/>";
               echo $latitude;
      $sql = ("INSERT INTO otherphones(message,sender_phone,time,latitude,longitude,userid) VALUES('$message','$number','$time','$latitude','$longitude','$id')");

    	    $RESULT=mysql_query($sql) ;       // use    (execute query or trigger_error(mysql_error().$sql to find error);
    	
  

   }


}         
           // this is my magic constructor!!!!!!! manshallah

          function changeclass($msg,$phone,$time){

               $obj = new ialert();
              $obj->setname($msg,$phone,$time);
              $obj->filter();
              $obj->getuserid(); 
              $obj->insertintodb();
              $sendto=$obj->getphone();
           var_dump($obj);
             echo "<br.>";
             echo $sendto;

             return $sendto;
          }


          // sms media apis

           function smsmediscasend($getsender , $getsms){

      $phone="256703744226";
      $sendmsg=rawurlencode($getsms);
  
       $url='http://lambda.smsmedia.ug/api/capi/send.php?sender='.$getsender.'&dest=8198&user=alimulondo&pass=alimulondo&code=alimulondo&content='.$sendmsg.'';  

  $ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
   $content = curl_exec($ch);
     exec(GET);
    
    
   }

     // in case sms not recieved by the script, send this

    function smsmediafailed(){
           $getsender=rawurlencode("256703744226");
            $getsms=rawurlencode("No sms recieved, check usesmsmedia.php");

  
       $url='http://lambda.smsmedia.ug/api/capi/send.php?sender='.$getsender.'&dest=8198&user=alimulondo&pass=alimulondo&code=alimulondo&content='.$getsms.'';  

  $ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
   $content = curl_exec($ch);
     exec(GET);
    
   }

     



?>  