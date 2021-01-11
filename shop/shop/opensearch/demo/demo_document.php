<?php
require_once("Config.inc.php");

use OpenSearch\Client\DocumentClient;
$data = $_GET['data'];
$data = json_decode($data,true);
$docs_to_upload = array();
foreach ($data as $key=>$val){
	$item = array();
	$item['cmd'] = 'ADD';
	$item["fields"] = array(
        "common_id" => $val['common_id'],
        "common_name" => $val['common_name']
    );
    $docs_to_upload[] = $item;
}
$tableName = 'yf_goods_common';
$documentClient = new DocumentClient($client);

$json = json_encode($docs_to_upload);
$ret = $documentClient->push($json, $appName, $tableName);
print_r(json_decode($ret->result, true));
echo $ret->traceInfo->tracer;
