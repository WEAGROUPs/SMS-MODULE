<?php
    require_once('ialertsmsclass.php');
    
if (isset($_GET['from']) && isset($_GET['message']) && isset($_GET['time']) ) {
 
    $phone = $_GET['from'];
    $msg = $_GET['message'];
    $Testing_time = $_GET['time'];
     $addsenders_number=$msg." from ".$phone;
      $thanking_sender="Thanks for alerting the world first and faster";

             $sendto=changeclass($msg,$phone,$time);
             smsmediscasend($sendto,$addsenders_number);
             smsmediscasend($phone,$thanking_sender);      
      }
    
  

      //send this if the sms has been recieved by the script

     

  else {
      
      smsmediafailed();
  } 
     
   

        
?>