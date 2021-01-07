<?php 
class CDN{
	 
	 static $i = 0;
	 static $count_num = 0;

    static function is_open(){
	 	if(Yf_Registry::get('cdn_image_urls')){
	 	    self::$count_num = count(Yf_Registry::get('cdn_image_urls'))-1;
	 		return true;
	 	}
	 	return false;
	 }
	 static function img($image_url, $width = 64, $height = 64){
	 	if(self::is_open()){
	 			$reg = '/(http|https):\/\/([^\/]+)/i';
		        preg_match($reg, $image_url,$preg_result);
		        if(!empty($preg_result) && $preg_result[0] == Yf_Registry::get('base_url') ){
		            $replace_url = self::current_url();
		            $image_url= preg_replace($reg, $replace_url, $image_url);
		        }
	 	}
        
        return image_thumb($image_url,$width,$height);
    }
    static function url($url){
	     if(self::is_open()){
             $reg = '/(http|https):\/\/([^\/]+)/i';
             preg_match($reg, $url,$preg_result);
             if(!empty($preg_result) && $preg_result[0] == Yf_Registry::get('base_url') ){
                 $replace_url = self::current_url();
                 $url= preg_replace($reg, $replace_url, $url);
             }
         }
         return $url;
    }
    /**
     * 随机cdn地址 
     */
    static function current_url(){
    	$image_urls =   Yf_Registry::get('cdn_image_urls');
        //$replace_url = array_rand($image_urls);
        if(self::$i > self::$count_num){
            self::$i = 0;
        }
        $replace_url = $image_urls[self::$i];
        self::$i++;
        return $replace_url;
    }

    static function content($body){ 
    	if(self::is_open()){ 
	    	$img = self::get_img($body,true);
	    	$base = Yf_Registry::get('base_url');
	    	if($img){
	    		foreach($img as $link){
	    			if(strpos($link,$base)!==false){
	    				$replace_url = self::current_url();
	    				$new = str_replace($base,'',$link); 
	    				$body = str_replace($link,$replace_url.$new,$body);
	    			}
	    		}
	    	}
	    }
    	return $body;
    }

    static function get_img($content,$all=false){ 
		$preg = '/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i'; 
		preg_match_all($preg,$content,$out);
		$img = $out[2];  
		if($all === true){
			return $img;
		}else if($all === false){
			return $img[0]; 
		}
		return $out[0];
	} 


}