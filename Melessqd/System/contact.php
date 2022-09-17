<br />
<?php
$system = true;
if (function_exists('spamcheck')){
}else{
function spamcheck($field)
  {
 
//stripos() performs a case insensitive regular expression match
        if (stripos($field, 'to:') !== false || stripos($field, 'cc:') !== false) 
    {
    return TRUE;
    }
  else
    {
    return FALSE;
    }
  }
  }
  
  if (function_exists('displaycontactform')){
}else{
  function displaycontactform()
  {
      global $target;
      global $section;
      global $subsection;
      echo "
      <div id=\"editbox\">
          <form method='post' action='?target=$target";
              if (isset($section)){
                  echo "&ampsection=$section";
              }
              if (isset($subsection)){
                  echo "&ampsubsection=$subsection";
              }
              echo "'>
                  <input name='referrer' type='hidden' value='";
              if(isset($_SERVER['HTTP_REFERER'])){
                  echo validate($_SERVER['HTTP_REFERER'],'hd');
              }else{
                  echo "null";
              }
              echo "' readonly />
                  <input name='time' type='hidden' value='" . time() . "' readonly />
                  <input name='times' type='hidden' value='";
                  if (isset($_REQUEST['times'])){
                      echo validate($_REQUEST['times'],'hd')+1;
                  }else{
                      echo 0;
                  }
                  echo "' readonly />
<table>
<tr>
<td>Email:
</td>
<td>
<input name='email' type='text' size='30' value='";
    if (isset($_REQUEST['email'])){
      echo validate($_REQUEST['email'],'hd');
    } 
    echo "' required />
</td>
</tr>
<tr>
<td>Subject:
</td>
<td>
";

echo "<input name='subject' type='text' size='30' value='";
    if (isset($_REQUEST['subject'])){
      echo validate($_REQUEST['subject'],'hd');
    }
echo "' required />";

echo "
</td>
</tr>
<tr>
<td valign=top>
Message:
</td>
<td>
<textarea name='message' rows='15' cols='40' required>";

if (isset($_REQUEST['message'])){
      echo validate($_REQUEST['message'],'hd');
    }

echo "</textarea>
</td>
</tr>

<tr>
<td colspan=\"2\"> ";
if (isset($_POST['data'])){
		echo "<input type=\"checkbox\" name=\"data\" required checked />";
	}else{
		echo "<input type=\"checkbox\" name=\"data\" required />";
	}
	echo "I agree that my data can be used for the provision of this service";
echo "</td>
    </tr>

<tr>
<td>
</td>
<td>";

Displaycaptcha();

echo "</td>
<td align=right>
<p>
  <input type='submit' />
</p>
</td>
</tr>
</table>
  </form>
  </div>";
  }
  }
  

//if "email" is filled out, send email
if (isset($_REQUEST['email']))
  {
  //check if the email address is invalid
  $mailcheck = spamcheck($_REQUEST['email']);
  // check captcha 
  $captcha = CheckCaptcha();
  
  $message = validate($_REQUEST['message'],'hd');
  $subject = validate($_REQUEST['subject'],'hd');
  $orignialmessage = $message;
  $Junk_Check = Junk_Check($message . " " . $subject);
  $wordcount = str_word_count($orignialmessage,0);
  
   $message .= "
            Data: " . validate($_REQUEST['data'],'hd') . "  
            Captcha: " . validate($_REQUEST['Captcha'],'hd') . " : $captcha
            Referrer: " . validate($_REQUEST['referrer'],'hd') . "
            Referrer: " . validate($_SERVER['HTTP_REFERER'],'hd') . " 
            TimeStamp: " . validate($_REQUEST['time'],'hd');
            $ProcessTime = time() - validate($_REQUEST['time'],'hd');
            if ($ProcessTime <= 1){
                $ProcessTime = 1;
            }
   
   $message .= " 
           ProcessTime: " . $ProcessTime . " 
           WordCount: " . $wordcount . "
           Times Loaded: " . validate($_REQUEST['times'],'hd');
   
   $spamvalue = 0;
   if (strlen(validate($_REQUEST['referrer'],'hd'))==0){
       $spamvalue++;
   }
   if (validate($_REQUEST['referrer'],'hd') == validate($_SERVER['HTTP_REFERER'],'hd')){
       $spamvalue++;
   }
   if (str_word_count($orignialmessage . " " . $subject,0)/$ProcessTime >= 4){
       $spamvalue += round((str_word_count($orignialmessage . " " . $subject,0)/$ProcessTime));
   }
   if (validate($_REQUEST['times'],'hd') != 1){
       $spamvalue += validate($_REQUEST['times'],0);
   }
   // #todo: paramerterise the spamscore words.
   str_replace(["    ","   ","  ","https","!","earning"], ["    ","   ","  ","https","!","earning"], strtolower($orignialmessage . " " . $subject), $count1);
   $spamvalue += $count1;
   
   $message .= "
           SpamValue: " . $spamvalue;
        
    if ($mailcheck==TRUE || $captcha=='Fail' || $Junk_Check==TRUE || $spamvalue >= 50 || $spamvalue >= $wordcount)
    {
    echo parameters('JunkCheckFailMessage');
    // todo: update to log, pause and lock out junk users
    
    if (validate($_REQUEST['times'],0) >= 5){
        // do not re-display 
    }else{
        echo $spamvalue;
    sleep(1+(2*$spamvalue)); // pause before re-displaying
    displaycontactform();
    }}
  else
    { 
    //send email
    $to = parameters('ContactEmail');
    $email = validate($_REQUEST['email'],'hd');
    $subject = validate($_REQUEST['subject'],'hd');
    mail("$to", "Subject: $subject",
    $message, "From: $email" );
    echo "Thank you for using our mail form";
    }
  }
else
//if "email" is not filled out, display the form
  {
    
    displaycontactform();
  }
?>
