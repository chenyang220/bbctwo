<?php 
/**
 * 目前支持百度bos,
 * 
 * https://cloud.baidu.com/doc/BOS/ProductDescription.html#.E6.A6.82.E8.BF.B0
 * 后一个3.2.0.p1将支持阿里oss
 * @var [type]
 */
if(Web_ConfigModel::value('remote_image_status') == 'ali')
{
	$is_open = 'ali';
}else if(Web_ConfigModel::value('remote_image_status') == 'bos'){
	$is_open = 'bos';
}else{
	$is_open = '';
}
$bos_config = [
	'open'=>$is_open,//ali阿里云  bos 百度云 
	'ali'=>[
			'accessKeyId'=>Web_ConfigModel::value('remote_ali_id'),
			'secretAccessKey'=>Web_ConfigModel::value('remote_ali_key'),
			'endpoint'=> Web_ConfigModel::value('remote_ali_endpoint'),
			'bucketName'=> Web_ConfigModel::value('remote_ali_bucketName'),
			'cdn'=> Web_ConfigModel::value('remote_ali_cdn'),
			'delete'=> Web_ConfigModel::value('remote_ali_yes'),
	],
	'bos'=>[
			'accessKeyId'=>Web_ConfigModel::value('remote_bos_id'),
			'secretAccessKey'=>Web_ConfigModel::value('remote_bos_key'),
			'endpoint'=> Web_ConfigModel::value('remote_bos_endpoint'),
			'bucketName'=> Web_ConfigModel::value('remote_bos_bucketName'),
			'cdn'=> Web_ConfigModel::value('remote_ali_cdn'),
			'delete'=> Web_ConfigModel::value('remote_bos_yes'),
	],
	
];
 
return $bos_config;