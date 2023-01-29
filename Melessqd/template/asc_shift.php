<?php

// To detect if functions are availble to page
$shiftloaded = True;

function getCipher(){
//old $ciphering BF-ECB
   if (!isset($ciphering)){
      $ciphering = "AES-256-CTR";
   }
   return $ciphering;
}


// Old Encrypt/decrypt functuions, backward compatablity
function asc_shift($string, $amount) {

	if ($amount == 'enc'){
            if(!$string){return false;}
            $key = $DataKey;
            $text = $value;
            //$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
            //$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            //$crypttext = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $text, MCRYPT_MODE_ECB, $iv);
            $crypttext = openssl_encrypt($text, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
            return trim(base64_encode($crypttext)); //encode for cookie
	}

	if($amount == 'dec'){
            if(!$string){return false;}
            $key = $DataKey;
            $crypttext = base64_decode($value); //decode cookie
            //$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
            //$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            //$decrypttext = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
            $decrypttext = openssl_decrypt($crypttext, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
            return trim($decrypttext);
	}

}

// Encrypt Data for saving
function encrypt($value){
   if(!$value){return false;}
   global $DataKey;
   $key = $DataKey;
   $text = $value;
   // MCRYPT - OLD
   //$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
   //$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   //$crypttext = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $text, MCRYPT_MODE_ECB, $iv);
   //OPENSSL -- New encryption
   $blockSize = 8;
    $len = strlen($value);
    $paddingLen = intval(($len + $blockSize - 1) / $blockSize) * $blockSize - $len;
    $padding = str_repeat("\0", $paddingLen);
    $text = $value . $padding;
   $crypttext = openssl_encrypt($text, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
   return trim(base64_encode($crypttext)); //encode for cookie
}

// Decrypt Saved Data
function decrypt($value){
   if(!$value){return false;}
   global $DataKey;
   $key = $DataKey;
   $crypttext = base64_decode($value); //decode cookie
   // MCRYPT - OLD
   //$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
   //$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   //$decrypttext = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
   //OpenSSL -- New Decryption
   $decrypttext = openssl_decrypt($crypttext, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
   return trim($decrypttext);
}

//Encrypt data for passing front end data
function encryptfe($value){

   // Store the cipher method
$ciphering = getCipher();

   if(!$value){return false;}
   global $PostKey;
   $key = $PostKey;
   $text = $value;
   // mcrypt - OLD
   //$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
   //$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   //$crypttext = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $text, MCRYPT_MODE_ECB, $iv);
   //OpenSSL -- New 
   //echo $text;
   if (!isset($_SESSION['iv'])){
      $iv = openssl_random_pseudo_bytes(16);
      $_SESSION['iv']=$iv;
   }else{
      $iv = $_SESSION['iv'];
   }

    $blockSize = 8;
    $paddingLen= 0;
   // echo $value;
    $len = strlen($value);
    $paddingLen = intval(($len + $blockSize - 1) / $blockSize) * $blockSize - $len;
    $padding = str_repeat("\0", $paddingLen);
 //   echo $padding;
    $data = $value . $padding;
//    echo $data;
    // $key = make_openssl_blowfish_key($key);
    // echo $data;
//    echo $key;
 //   echo 'pd:' . $paddingLen;
    $crypttext = openssl_encrypt($data, $ciphering, $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
 //   echo 'ct: ' . $crypttext;
   //$crypttext = openssl_encrypt($text, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
   //echo $crypttext;
   //echo base64_encode($encrypted);
   return trim(base64_encode($crypttext)); //encode for cookie
}

//Decrypt data passed in font end
function decryptfe($value){
   if(!$value){return false;}
   $ciphering = getCipher();
     if (!isset($_SESSION['iv'])){
      $iv = openssl_random_pseudo_bytes(16);
      $_SESSION['iv']=$iv;
   }else{
      $iv = $_SESSION['iv'];
   }

   global $PostKey;
   $key = $PostKey;
   $crypttext = base64_decode($value); //decode cookie
   // mcrypt - OLD
   //$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
   //$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   //$decrypttext = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
   //OpenSSL -- New
   $decrypttext = openssl_decrypt($crypttext, $ciphering, $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
   return trim($decrypttext);
}

?>
