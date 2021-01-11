<?php 
/**
 * 优化使网站变快
 */

/**
 * 如果是JSON格式的，直接输出。
 */
if( check_is_json($data) ){
	echo $data;
	exit;
}
/**
 * 如果是HTML实体，需要把图片 CSS JS地址全部走CDN。
 */

//开启CDN配置，自动URL会被替换
if(CDN::is_open()){

	$_config = [
		//JS处理
		'script'=>'src',
		//图片处理
		'img'=>'src',
		// CSS 处理
		'link'=>'href',
	];
	foreach($_config as $k=>$v){
		$css = YF_local_img($data,$k,$v);
		$data = YF_replace_output($data,$css);
	} 

}
//输出HTML页面 
echo $data;


function YF_replace_output($data,$array){
	$base_uri = Yf_Registry::get('base_url');
	$root_uri = Yf_Registry::get('root_uri');
	if($array){
		foreach($array as $v){
			$rep  = $base_uri.$v;
			$new = CDN::current_url().$root_uri.$v; 
			$data = str_replace($rep,$new,$data);
		}
	}  
	return $data;
}



function check_is_json($string) {
     json_decode($string);
     return (json_last_error() == JSON_ERROR_NONE);
}

function YF_local_img($content , $tag1 = 'img' , $tag2 = 'src'){ 
		$base_uri = Yf_Registry::get('base_url');
		$base_uri = str_replace('.','\.',$base_uri);
		$base_uri = str_replace('/','\/',$base_uri); 

		$root_path  = Yf_Registry::get('root_uri');

		$preg = '/<\s*'.$tag1.'\s+[^>]*?'.$tag2.'\s*=\s*(\'|\")'.$base_uri.'(.*?)\\1[^>]*?\/?\s*>/i'; 
		
		preg_match_all($preg,$content,$out);
		 
		$img = $out[2];
		if($img) { 
			$num = count($img); 
			for($j=0;$j<$num;$j++){ 
				$i = $img[$j]; 
				if( (strpos($i,"http://")!==false || strpos($i,"https://")!==false ) 
					&& strpos($i,$base_uri)===false)
				{
					unset($img[$j]);
				}
			}
		} 
		return $img;
		 
} 

