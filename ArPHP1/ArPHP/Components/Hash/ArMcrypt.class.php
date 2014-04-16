<?php
/**
 * class Db default class\PDO 
 *
 * @author assnr <ycassnr@gmail.com>
 */

/**
 * abstract Db class.
 */
class ArMcrypt extends ArComponent {

    private $_key;

    static public function init($config = array(), $class = __CLASS__) {
        $self = parent::init($config, $class);
        $self->_key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
        $self->_ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        return $self;
    }

    public function encrypt($plaintext)
    {

        # --- ENCRYPTION ---

        # the key should be random binary, use scrypt, bcrypt or PBKDF2 to
        # convert a string into a key
        # key is specified using hexadecimal
        // $key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
        $key = $this->_key;
        
        # show key size use either 16, 24 or 32 byte keys for AES-128, 192
        # and 256 respectively
        $key_size =  strlen($key);

        // echo "Key size: " . $key_size . "\n";
        
        # create a random IV to use with CBC encoding
        // $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv_size = $this->_ivSize;
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        
        # creates a cipher text compatible with AES (Rijndael block size = 128)
        # to keep the text confidential 
        # only suitable for encoded input that never ends with value 00h
        # (because of default zero padding)
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,
                                     $plaintext, MCRYPT_MODE_CBC, $iv);

        # prepend the IV for it to be available for decryption
        $ciphertext = $iv . $ciphertext;
        
        # encode the resulting cipher text so it can be represented by a string
        $ciphertext_base64 = base64_encode($ciphertext);

        return str_replace(array('/', '\\'), array('---', '+++'), $ciphertext_base64);

        
    }

    public function decrypt($ciphertext_base64)
    {
        # === WARNING ===

        # Resulting cipher text has no integrity or authenticity added
        # and is not protected against padding oracle attacks.
        
        # --- DECRYPTION ---
        $key = $this->_key; 
        $iv_size = $this->_ivSize;
        $ciphertext_dec = base64_decode(str_replace(array('---', '+++'), array('/', '\\'), $ciphertext_base64));
        
        # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);
        
        # retrieves the cipher text (everything except the $iv_size in the front)
        $ciphertext_dec = substr($ciphertext_dec, $iv_size);

        # may remove 00h valued characters from end of plain text
        $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
                                        $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
        
        return trim($plaintext_dec);
 
    }

}
