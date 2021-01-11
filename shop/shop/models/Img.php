<?php 

/**
 * 图片缩放处理
 * 通用的类
 * 非常强大。
 * 无论是带 ! 还是直接输入 图片地址+宽+高，都可以在OSS、BOS上生成正确的URL
 */

class Img{
	static $_config;
	/**
	 *  https://shop.local.yuanfeng021.com/image.php/6/1529981826936558.jpg!200x60.jpg
	 *  https://aliyun/image.php/6/1529981826936558.jpg!200x60.jpg
	 */
	static function url($url, $w = null, $h = null){
        $flag = false;
        if(strpos($url,'!')!==false){
            $flag = true;
            preg_match('/!(\d+)x(\d+)/',$url,$mat);
            if($mat[1]){
                $w = $mat[1];
            }
            if($mat[2]){
                $h = $mat[2];
            }
        }
        
		if(strpos($url,'image.php') !== false){
			return image_thumb($url, $w, $h);
		}
		$ori_url = $url;
		if(!static::$_config){
			include APP_PATH.'/configs/bos.ini.php';
			static::$_config = $bos_config; 
		} 

		switch (static::$_config['open']) {
			case 'bos':
			 	$str = "@";
				if($w){
					$str .=',w_'.$w;
				}
				if($h){
					$str .=',h_'.$h;
				}  
				$str = str_replace('@,','@',$str);

				break;
			case 'ali':
				//阿里图片处理
				$str = "?x-oss-process=image/resize"; 
				if($w){
					$str .=',w_'.$w;
				}
				if($h){
					$str .=',h_'.$h;
				} 
				
				break;
			default:
				 
				 
				break;
		}

		if($flag == true){ 
			$url = substr($url,0,strpos($url,'!')).$str;
		}else{ 
			$url = $url.$str;
		} 
		if($w || $h){
			return $url;
		}
		return $ori_url; 

         
    
	}
}