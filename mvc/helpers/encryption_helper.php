<?php 
if (!function_exists('encrypt_data')) {
    function encrypt_data($data) {
      
      
    
        // Store a string into the variable which
        // need to be Encrypted
        $simple_string = $data;
         
         
        // Store the cipher method
        $ciphering = "AES-128-CTR";
        
        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        
        // Non-NULL Initialization Vector for encryption
        $encryption_iv = '1234567891011121';
        
        // Store the encryption key
        $encryption_key = "srinivas";
        
        // Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt($simple_string, $ciphering,
              $encryption_key, $options, $encryption_iv);
        
        // Display the encrypted string
        // echo "Encrypted String: " . $encryption . "\n";
        return $encryption;
    }
}



if (!function_exists('decrypt_data')) {
    function decrypt_data($data) { 
        $encryption = $data;
 
 
// Store the cipher method
$ciphering = "AES-128-CTR";

// Use OpenSSl Encryption method
$iv_length = openssl_cipher_iv_length($ciphering);
$options = 0;

// Store the encryption key
$encryption_key = "srinivas"; 

// Non-NULL Initialization Vector for decryption
$decryption_iv = '1234567891011121';

// Store the decryption key
$decryption_key = "srinivas";

// Use openssl_decrypt() function to decrypt the data
$decryption=openssl_decrypt ($encryption, $ciphering,
		$decryption_key, $options, $decryption_iv);

// Display the decrypted string
// echo "Decrypted String: " . $decryption;
    return $decryption;
    }
}

?>