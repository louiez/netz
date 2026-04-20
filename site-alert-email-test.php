<?php
require_once('site-monitor.conf.php');

function Validate_String($string, $return_invalid_chars = true)
         {
         $valid_chars = "1234567890-_.^~abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
         $invalid_chars = "";
         
         if($string == null || $string == "")
            return(true);
         
         //For every character on the string.   
         for($index = 0; $index < strlen($string); $index++)
            {
            $char = substr($string, $index, 1);
            
            //Is it a valid character?
            if(strpos($valid_chars, $char) === false)
              {
              //If not, is it already on the list of invalid characters?
              if(strpos($invalid_chars, $char) === false)
                {
                //If it's not, add it.
                if($invalid_chars == "")
                   $invalid_chars .= $char;
                else
                   $invalid_chars .= ", " . $char;
                }
              }
            }
            
         //If the string does not contain invalid characters, the function will return true.
         //If it does, it will either return false or a list of the invalid characters used
         //in the string, depending on the value of the second parameter.
         if($return_invalid_chars == true && $invalid_chars != "")
           {
           $last_comma = strrpos($invalid_chars, ",");
           
           if($last_comma != false)
              $invalid_chars = substr($invalid_chars, 0, $last_comma) . 
              " and " . substr($invalid_chars, $last_comma + 1, strlen($invalid_chars));
                      
           return($invalid_chars);
           }
         else
           return($invalid_chars == ""); 
         }


function Verify_Email_Address($email_address)
{
	//Assumes that valid email addresses consist of user_name@domain.tld
	$at = strpos($email_address, "@");
      	$dot = strrpos($email_address, ".");
      	if($at === false || $dot === false || $dot <= $at + 1 ||$dot == 0 || $dot == strlen($email_address) - 1){
            return(false);
         }   
         $user_name = substr($email_address, 0, $at);
         $domain_name = substr($email_address, $at + 1, strlen($email_address));
// See if the Domain has an MX (mail) server address
	$mx=`dig +short mx $domain_name`;
	if ($mx == ""){echo "Invalid Domain ". $domain_name . "<br>";}
        if(Validate_String($user_name) === false || Validate_String($domain_name) === false || $mx == ""){
         	return(false);
         }
         return(true);
}

// Mail header Optional but required to stop being flagged as spam by some servers
$headers = "From: ".$smtp_from_address."\r\n" .
           "Reply-To: ".$smtp_from_address."\r\n" .
           "X-Mailer: PHP/" . phpversion();

$message = "Alert Email Test for " . $_GET['site'];
$subject = "Alert Email Test for " . $_GET['site'];
$emails=explode(",",$_GET['email']);

foreach ($emails as $email)
{
      if ($email != "" ){
		if (Verify_Email_Address($email)){
			//$mail->AddAddress(trim($email));
			$to.=trim($email).",";
			$validEM=1;
		}
	}
}
if ($validEM > 0){
	//if(!$mail->Send()){
	if(!mail($to, $subject, $message, $headers)) {
		echo "There was an error sending the message<br>";
		echo trim($_GET['email'])."<br>";
		echo $mail->ErrorInfo;
	}
	else{
 		echo "Message sent ";
	}
}else{
	echo "No Valid Address ";
}

?>
