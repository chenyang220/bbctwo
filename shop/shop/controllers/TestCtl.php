<?php

class TestCtl extends Controller
{

    public function test()
    {
        $secretId = "AKIDXpzsRjdhupb5IEFGMCk0FRM1OWAXtaLq";
        $Action = "DescribeStreamPlayInfoList";
        $signature = $this->sign();
        $StartTime = "2020-05-14 00:00:00";
        $EndTime = "2020-05-14 22:00:00";
        $PlayDomain = "live.yuanfengtest.com";
        $Timestamp = time();
        $Nonce = rand(10000, 99999);
        $url = "https://live.tencentcloudapi.com/?Action=" . $Action  . "&StartTime=" . urlencode($StartTime) . "&EndTime=" . urlencode($EndTime) . "&Version=" . urlencode("2018-08-01") . "&Timestamp=" . $Timestamp . "&Nonce={$Nonce}&SecretId=" . $secretId . "&Signature=" . urlencode($signature);
//        $url = "https://live.tencentcloudapi.com/?Action=" . $Action  . "&PlayDomain=" . $PlayDomain . "&StartTime=" . $StartTime . "&EndTime=" . $EndTime . "&Version=2018-08-01&Timestamp=". $Timestamp ."&Nonce=12&SecretId=" .$SecretId  . "&Signature=". urlencode($Signature);
        $header = array(
            'Host:' . 'live.tencentcloudapi.com',
            'Content-Type:' . 'application/x-www-form-urlencoded'
        );
        $test = $this->http_post($url,$header,false);
        var_dump($test);
        var_dump($url);
        echo $url;
    }

    public function sign()
    {
//        $StartTime = date('Y-m-d 00:00:00', time());;
        $StartTime = date('Y-m-d H:i:s', strtotime('-1minute'));

        $EndTime = date('Y-m-d H:i:s', time());;
        $Timestamp = time();
        $Action = "DescribeStreamPlayInfoList";
        $Nonce = rand(10000, 99999);
        $secretId = "AKIDXpzsRjdhupb5IEFGMCk0FRM1OWAXtaLq";
        $secretKey = "MS9akd5NO8bDaiZEOIwPKzamlIYYGeqp";
        $param["Nonce"] = $Nonce;
        $param["Timestamp"] = $Timestamp;
        $param["SecretId"] = $secretId;
        $param["Action"] = "DescribeStreamPlayInfoList";
        $param["Version"] = "2018-08-01";
        $param["StartTime"] = $StartTime;
        $param["EndTime"] = $EndTime;
        $param["StreamName"] = '1400317205_1722bb18591';
        ksort($param);
        $signStr = "GETlive.tencentcloudapi.com/?";
        foreach ($param as $key => $value) {
            $signStr = $signStr . $key . "=" . $value . "&";
        }
        $signStr = substr($signStr, 0, -1);
        $signature = base64_encode(hash_hmac("sha1", $signStr, $secretKey, true));
        $url = "https://live.tencentcloudapi.com/?Action=" . $Action . "&StartTime=" . urlencode($StartTime) . "&EndTime=" . urlencode($EndTime) . "&Version=" . urlencode("2018-08-01") . "&Timestamp=" . $Timestamp . "&Nonce={$Nonce}&SecretId=" . $secretId . "&Signature=" . urlencode($signature);
        $url .= "&StreamName=1400317205_1722bb18591";
        $ret = file_get_contents($url);
        $ret = json_decode($ret, true);
        return $this->data->addBody(-140, $ret['Response']);

    }



    public function test1()
    {
        $Action = "DescribeStreamPlayInfoList";
        $StartTime = "2020-05-14 00:00:00";
        $EndTime = "2020-05-15 00:00:00";

        $url = "https://live.tencentcloudapi.com/?Action=DescribeStreamPlayInfoList&StartTime=" . urlencode($StartTime) . "&EndTime=" . urlencode($EndTime) . "&Version=" . urlencode('2018-08-01');
//        $url = "https://live.tencentcloudapi.com/";
        $header = array(
            'Authorization:' . $this->v3(),
            'Content-Type:' . 'application/x-www-form-urlencoded',
            'Host:' . 'live.tencentcloudapi.com',
            'X-TC-Action:' . 'DescribeStreamPlayInfoList',
            'X-TC-Version:' . '2017-03-12',
            'X-TC-Timestamp:' . time(),
            'X-TC-Region:' . 'ap-shanghai',
        );
        $post_data = array(
            'Action' => $Action,
            'StartTime' => urlencode($StartTime),
            'EndTime' => urlencode($EndTime),
            'Version' => urlencode('2018-08-01')
        );
        $test = $this->http_post($url, $header,false);
        var_dump($test);
    }


    public function http_post($url, $header, $content)
    {
        $ch = curl_init();
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        $response = curl_exec($ch);
        if ($error = curl_error($ch)) {
            die($error);
        }
        curl_close($ch);
        //var_dump($response);
        return $response;
    }

    public function v3()
    {
        $secretId = "AKIDXpzsRjdhupb5IEFGMCk0FRM1OWAXtaLq";
        $secretKey = "MS9akd5NO8bDaiZEOIwPKzamlIYYGeqp";
        $host = "vod.tencentcloudapi.com";
        $service = "vod";
        $timestamp = time();
        $algorithm = "TC3-HMAC-SHA256";

        // step 1: build canonical request string
        $httpRequestMethod = "POST";
        $canonicalUri = "/";
        $canonicalQueryString = "";
        $canonicalHeaders = "content-type:application/json; charset=utf-8\n" . "host:" . $host . "\n";
        $signedHeaders = "content-type;host";
        $payload = '{"Limit": 1, "Filters": [{"Values": ["\u672a\u547d\u540d"], "Name": "instance-name"}]}';
        $hashedRequestPayload = hash("SHA256", $payload);
        $canonicalRequest = $httpRequestMethod . "\n"
            . $canonicalUri . "\n"
            . $canonicalQueryString . "\n"
            . $canonicalHeaders . "\n"
            . $signedHeaders . "\n"
            . $hashedRequestPayload;
//        echo $canonicalRequest . PHP_EOL;

        // step 2: build string to sign
        $date = gmdate("Y-m-d", $timestamp);
        $credentialScope = $date . "/" . $service . "/tc3_request";
        $hashedCanonicalRequest = hash("SHA256", $canonicalRequest);
        $stringToSign = $algorithm . "\n"
            . $timestamp . "\n"
            . $credentialScope . "\n"
            . $hashedCanonicalRequest;
//        echo $stringToSign . PHP_EOL;

        // step 3: sign string
        $secretDate = hash_hmac("SHA256", $date, "TC3" . $secretKey, true);
        $secretService = hash_hmac("SHA256", $service, $secretDate, true);
        $secretSigning = hash_hmac("SHA256", "tc3_request", $secretService, true);
        $signature = hash_hmac("SHA256", $stringToSign, $secretSigning);
//        echo $signature . PHP_EOL;

        // step 4: build authorization
        $authorization = $algorithm
            . " Credential=" . $secretId . "/" . $credentialScope
            . ", SignedHeaders=content-type;host, Signature=" . $signature;
//        return   $authorization . PHP_EOL;
//        $action = 'SearchMedia';
//        $version = '2017-03-12';
//        $region = 'ap-shanghai';
//        $curl = "curl -X POST https://" . $host
//            . ' -H "Authorization: ' . $authorization . '"'
//            . ' -H "Content-Type: application/json; charset=utf-8"'
//            . ' -H "Host: ' . $host . '"'
//            . ' -H "X-TC-Action: ' . $action . '"'
//            . ' -H "X-TC-Timestamp: ' . $timestamp . '"'
//            . ' -H "X-TC-Version: ' . $version . '"'
//            . ' -H "X-TC-Region: ' . $region . '"'
//            . " -d '" . $payload . "'";
//        echo $curl . PHP_EOL;

//        $url = "https://vod.tencentcloudapi.com/?Action=SearchMedia&Version=2018-07-17";
        $url = "https://vod.tencentcloudapi.com/";
        $post['Action'] = 'SearchMedia';
        $post['Version'] = '2018-07-17';
        $header = array(
            'Authorization:' . $authorization,
            'Content-Type:' . 'application/json; charset=utf-8',
            'Host:' . 'vod.tencentcloudapi.com',
            'X-TC-Action:' . 'SearchMedia',
            'X-TC-Version:' . '2018-07-17',
            'X-TC-Timestamp:' . $timestamp,
            'X-TC-Region:' . 'ap-shanghai',
        );

        $test = $this->http_post($url, $header, $post);
        var_dump($test);
    }


    public  function callback()
    {
        $a = $_REQUEST;
        file_put_contents('./abc.php',var_export($a,true));
    }

    public function testsub()
    {
        $backInfo['stream_id'] = '1400317205_1722cb9f6f1';
        $roomId = 'room' . substr($backInfo['stream_id'], 10);
        echo $roomId;
    }

    public function test123()
    {

        $stime = date("Y-m-d H:i:s", time() - 24 * 3600 * 358);
        $etime = date("Y-m-d H:i:s", time() - 24 * 3600*357);
        $Order_BaseModel = new Order_BaseModel();
        $sql = "SELECT A.order_id,A.order_status,A.shop_id,A.payment_number,A.buyer_user_id,A.order_shipping_code,A.order_receiver_address,A.order_create_time,A.order_goods_amount,A.order_payment_amount,A.order_discount_fee,A.order_refund_amount,A.order_return_num,A.order_from,B.return_type,B.order_goods_id,B.order_goods_name,B.order_goods_num,B.order_goods_price,B.return_cash FROM yf_order_base A LEFT JOIN yf_order_return B ON A.order_id = B.order_number WHERE order_create_time >= '{$stime}' AND order_create_time <= '{$etime}' ";
        $order_list = $Order_BaseModel->sql->getAll($sql);

        $analytics_data = array();
        $analytics_data['order_list'] = $order_list;
        $a = Yf_Plugin_Manager::getInstance()->trigger('analyticsUpdateOrdersStatus',$analytics_data);
       echo '<pre>';
           print_r($a);
       echo '</pre>';
       die;
    }


    public function group()
    {
        $groupBuyBaseModel = new GroupBuy_BaseModel();//团购
        $Goods_CommonModel = new Goods_CommonModel();
        $cond_row_groupbuy['groupbuy_state'] = GroupBuy_BaseModel::NORMAL;//状态正常
        $cond_row_groupbuy['groupbuy_endtime:<'] = get_date_time();//活动到期
        $bases = $groupBuyBaseModel->getByWhere($cond_row_groupbuy);
        $groupbuy_id_row = array_column($bases,'groupbuy_id');
        $group_common_id = array_column($bases, 'common_id');

        if ($groupbuy_id_row) {
            $field_row_groupbuy['groupbuy_state'] = GroupBuy_BaseModel::FINISHED;
            $groupBuyBaseModel->editGroupBuy($groupbuy_id_row, $field_row_groupbuy);

            $field_row['common_is_tuan'] = 0;
            $Goods_CommonModel->editCommon($group_common_id, $field_row);
        }
    }
    public function ag()
    {
        $user_id = 10565;
        $common_ids = json_encode([471]);
        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $distribution_shop = $Distribution_DistributionShop->getOneByWhere(array("user_id" => $user_id));

        $rs_row = array();
        if ($distribution_shop) {
            $NewDistribution_ShopDirectsellerGoodsCommonModel = new NewDistribution_ShopDirectsellerGoodsCommonModel();
            $NewDistribution_common = $NewDistribution_ShopDirectsellerGoodsCommonModel->getByWhere(array('distribution_shop_id' => $distribution_shop['distribution_shop_id']));
            if ($NewDistribution_common) {
                $in_common_ids = array_column($NewDistribution_common, 'goods_common_id');
            } else {
                $in_common_ids = array();
            }

            foreach (json_decode($common_ids) as $value) {
                $Goods_CommonModel = new Goods_CommonModel();
                $goods_info = $Goods_CommonModel->getOne($value);

                if (in_array($value, $in_common_ids)) {
                    $flag = false;
                } else {
                    $row = array(
                        'distribution_shop_id' => $distribution_shop['distribution_shop_id'],
                        'goods_common_id' => $value,
                        'user_id' => $user_id,
                        'shop_id' => $goods_info['shop_id'],
                        'add_time' => time()
                    );
                    echo '<pre>';
                        print_r($row);
                    echo '</pre>';
                    die;
                    $flag = $NewDistribution_ShopDirectsellerGoodsCommonModel->addShopDirectsellerGoodsCommon($row);
                }
                check_rs($flag, $rs_row);
            }
        } else {
            $msg = tips('440');
            $status = 440;
            $this->data->addBody(-140, array(), $msg, $status);
        }
        $flag = is_ok($rs_row);
        echo '<pre>';
            print_r($rs_row);
        echo '</pre>';
        die;
        if ($rs_row && $flag) {
            $status = 200;
            $msg = tips('200');
        } else {
            $msg = tips('250');
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    public function getIPLoc_sina($queryIP)
    {
//        $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $queryIP;
        $url = 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $queryIP;
        var_dump($url);
        $ch = curl_init($url);
        //curl_setopt($ch,CURLOPT_ENCODING ,'utf8');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
        $location = curl_exec($ch);
        $location = json_decode($location, true);
        curl_close($ch);
        return $location;
    }

    /**
     *  调用淘宝API根据IP查询地址
     */

    public function ip_address($ip)
    {
        $durl = 'https://api.map.baidu.com/location/ip?ip=' . $ip . '&ak=5At3anZe83x8oOpFap42Gt8eHYpy3wm9&coor=bd09ll';
        $data = file_get_contents($durl);
        $info = json_decode($data);
        $address = $info->{'address'};
        if(strstr($address, 'CN')){
            $country = 'CN';
        }else{
            $country = '';
        }
        $data = array();
        $data = array(
            'province' => $info->{'content'}->{'address_detail'}->{'province'},
            'city' => $info->{'content'}->{'address_detail'}->{'city'},
            'country' => $country,
        );
        return $data;
    }


        public function ip()
    {
        $ip = "36.149.32.0";
        $area_array = $this->ip_address($ip);
        if (!is_array($area_array) || !isset($area_array['country'])) {
            return '';
        }
        if (isset($area_array['province'])) {
            switch ($area_array['province']) {
                case '北京':
                case '上海':
                case '天津':
                case '重庆':
                    return $area_array['province'];
//                    break;
                case '新疆':
                    $area_array['province'] .= '维吾尔自治区';
                    break;
                case '西藏':
                case '内蒙古':
                    $area_array['province'] .= '自治区';
                    break;
                case '宁夏':
                    $area_array['province'] .= '回族自治区';
                    break;
                case '广西':
                    $area_array['province'] .= '壮族自治区';
                    break;
                case '香港':
                case '澳门':
                    $area_array['province'] .= '特别行政区';
                    return $area_array['province'];
//                    break;
                default :
//                    $area_array['province'] .= '省';
                    $area_array['province'];
                    break;
            }
        } else {
            return $area_array['country'];
        }
        if (isset($area_array['city'])) {
            switch ($area_array['city']) {
                case '迪庆':
                case '甘南':
                case '海北':
                case '黄南':
                case '果洛':
                case '玉树':
                case '甘孜':
                    $area_array['city'] .= '藏族自治州';
                    break;
                case '怒江':
                    $area_array['city'] .= '傈僳族自治州';
                    break;
                case '大理':
                    $area_array['city'] .= '白族自治州';
                    break;
                case '楚雄':
                case '凉山':
                    $area_array['city'] .= '彝族自治州';
                    break;
                case '红河':
                    $area_array['city'] .= '哈尼族彝族自治州';
                    break;
                case '德宏':
                    $area_array['city'] .= '傣族景颇族自治州';
                    break;
                case '文山':
                    $area_array['city'] .= '壮族苗族自治州';
                    break;
                case '西双版纳':
                    $area_array['city'] .= '傣族自治州';
                    break;
                case '大兴安岭':
                case '铜仁':
                case '毕节':
                case '海东':
                case '阿勒泰':
                case '塔城':
                case '吐鲁番':
                case '哈密':
                case '阿克苏':
                case '喀什':
                case '和田':
                case '阿里':
                case '那曲':
                case '日喀则':
                case '山南':
                case '林芝':
                case '昌都':
                    $area_array['city'] .= '地区';
                    break;
                case '湘西':
                case '恩施':
                    $area_array['city'] .= '土家族苗族自治州';
                    break;
                case '神农架':
                    $area_array['city'] .= '林区';
                    break;
                case '湘西':
                case '恩施':
                    $area_array['city'] .= '土家族苗族自治州';
                    break;
                case '临夏':
                case '昌吉':
                    $area_array['city'] .= '回族自治州';
                    break;
                case '延边':
                    $area_array['city'] .= '朝鲜族自治州';
                    break;
                case '黔东':
                    $area_array['city'] .= '南苗族侗族自治州';
                    break;
                case '黔南':
                case '黔西南':
                    $area_array['city'] .= '布依族苗族自治州';
                    break;
                case '海西':
                    $area_array['city'] .= '蒙古族藏族自治州';
                    break;
                case '阿坝':
                    $area_array['city'] .= '藏族羌族自治州';
                    break;
                case '临高':
                case '澄迈':
                case '屯昌':
                case '定安':
                    $area_array['city'] .= '县';
                    break;
                case '昌江':
                case '白沙':
                case '乐东':
                case '陵水':
                    $area_array['city'] .= '黎族自治县';
                    break;
                case '博尔塔拉':
                case '巴音郭楞':
                    $area_array['city'] .= '蒙古自治州';
                    break;
                case '伊犁':
                    $area_array['city'] .= '哈萨克自治州';
                    break;
                case '克孜勒苏':
                    $area_array['city'] .= '柯尔克孜自治州';
                    break;
                case '兴安':
                case '锡林郭勒':
                case '阿拉善':
                    $area_array['city'] .= '盟';
                    break;
                case '台湾':
                case '香港':
                case '澳门':
                    $area_array['city'] .= '';
                    break;
                default :
//                    $area_array['city'] .= '市';
                    $area_array['city'];
                    break;
            }
        } else {
            echo  $area_array['province'] . "====";
        }
        echo  $area_array['province'] . ' ' . $area_array['city'];
    }

    public function member()
    {
        $analytics_data = [
            'user_id' => '10138',
            'ip' => '36.149.32.0',
        ];
       $a =  Yf_Plugin_Manager::getInstance()->trigger('analyticsMemberUpdate', $analytics_data);
        echo '<pre>';
            print_r($a);
        echo '</pre>';
        die;
    }


}
