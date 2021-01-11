<?php

require_once("../OpenSearch/Autoloader/Autoloader.php");

use OpenSearch\Client\OpenSearchClient;

$accessKeyId = 'LTAI4GBu5ioKtQQpuSzy4N3q';
$secret = 'ibPYL5EFC9LLG7I9dBQ7D6qPbkbvwf';
$endPoint = 'http://opensearch-cn-shenzhen.aliyuncs.com';
$appName = 'mahaolin';
$suggestName = '';
$options = array('debug' => true);

$client = new OpenSearchClient($accessKeyId, $secret, $endPoint, $options);