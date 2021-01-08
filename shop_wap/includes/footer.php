<?php include 'translatejs.php';?>
<?php
$data = ob_get_contents();
ob_clean();
$data = preg_replace_callback('|.*</head>|', function() use ($_js_header) {
    return $_js_header . '</head>';
}, $data);


/**
 * SEO 整体替换。
 * @var array
 */
$cache_seo = false;
$flag = false;
$cache_path = __DIR__.'/../cache/seo.cache.php';
if($cache_seo === true){
	$seo = file_get_contents($cache_path);
	if($seo){
		$seo = json_decode($seo , true)['data'];
		$flag = true;
	} 
}
//写本地缓存
if($flag === false){
		$opts=array(
		        "http"=>array(
		                "method"=>"GET",
		                "timeout"=>3
		         ),
		);
		////创建数据流上下文
		$context = stream_context_create($opts);  
		$seo_data = @file_get_contents($ShopUrl.'?ctl=Webconfig&typ=json',false,$context);
		$seo = json_decode($seo_data,true);
		$seo = $seo['data']; 
		if($cache_seo === true){
			file_put_contents($cache_path,$seo_data);
		}
}

 
include __DIR__.'/seo.php';
$uri = $_SERVER['REQUEST_URI']?:$_SERVER['SCRIPT_NAME'];
$uri = substr($uri , strrpos($uri,'/')+1);
$uri = substr($uri , 0 , strrpos($uri,'.'));
if(!$uri){
	$uri = '/';
}
//公用的SEO 
$description = $seo['description'];
$keyword = $seo['keyword'];

$find = $seo_match[$uri];
if($find){
	$seo_title =  $seo[$find['title']];
	if(strpos($seo_title,'{') !== false){
		$seo_title = str_replace('{sitename}',$seo['site_name'],$seo_title);
		$seo_title = str_replace('{name}',$find['name'],$seo_title);
	}

	//处理每个页面是否有独立的  keyword   description
	if($find['keyword']){
		$keyword = $seo[$find['keyword']];
	}

	if($find['description']){
		$description = $seo[$find['description']];
	}

    if (strpos($keyword, '{') !== false) {
        $keyword = str_replace('{sitename}', $seo['site_name'], $keyword);
        $keyword = str_replace('{name}', $find['name'], $keyword);
    }
    if (strpos($description, '{') !== false) {
        $description = str_replace('{sitename}', $seo['site_name'], $description);
        $description = str_replace('{name}', $find['name'], $description);
    }

}
$data = preg_replace_callback('|.*</title>|', function() use (
	$seo_title,
	$keyword,
	$description

) {
	$str = "";
	if($seo_title){
		$str =  "<title>".$seo_title . '</title>';
	} 
    $str .= "\n<meta name=\"description\" content=\"".$description."\"/>\n
<meta name=\"keywords\" content=\"".$keyword."\"/>\n
			";
	return $str;
}, $data);
 

echo $data;

?>