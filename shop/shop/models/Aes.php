 <?php 
 class Aes {

    public $iv = '0000000000000000'; #Same as in JAVA              
    public $key = 'U1MjU1M0FDOUZ.Qz'; #Same as in JAVA

    function __construct() {
        $this->key = Yf_Registry::get('im_api_key'); 
        $this->iv = Yf_Registry::get('im_admin_api_url'); 
        
        $this->key = hash('sha256', $this->key, true);
    }

    function encrypt($str) {
        if(is_array($str)){
            $str = json_encode($str);
        }
        $iv = $this->iv;
        $td = @mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        @mcrypt_generic_init($td, $this->key, $iv);
        $block = @mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = @mcrypt_generic($td, $str);
        @mcrypt_generic_deinit($td);
        @mcrypt_module_close($td);
        return trim(base64_encode($encrypted));
    }

    function decrypt($code) {
        $iv = $this->iv;
        $td = @mcrypt_module_open('rijndael-128', '', 'cbc', '');
        @mcrypt_generic_init($td, $this->key, $iv);
        $str = @mdecrypt_generic($td, base64_decode($code));
        $block = @mcrypt_get_block_size('rijndael-128', 'cbc');
        @mcrypt_generic_deinit($td);
        @mcrypt_module_close($td);

        if($res = @json_decode($str)){
            return  $res;
        }
        return $str;
        //return $this->strippadding($str);             
    }

    /*
      For PKCS7 padding
     */
    private function addpadding($string, $blocksize = 16) {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }

    private function strippadding($string) {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }

}