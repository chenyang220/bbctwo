<?php

/**
 *  拼团
 * @author Str <tech40@yuanfeng021.com>
 */
class LiveCtl extends Controller
{
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
    }

    //根据shop_id获取申请信息
    public function getLiveInfoByShopId()
    {
        $shop_id = request_string('shop_id');
        $Live_ApplicationModel = new Live_ApplicationModel();
        $shop_live_info = $Live_ApplicationModel->getOneByWhere(array("shop_id" => $shop_id));
        switch ($shop_live_info['application_status']) {
            case 2:
                $shop_live_info['application_status_con'] = '审核通过';
                break;
            case 3:
                $shop_live_info['application_status_con'] = '审核未通过';
                break;
            case 4:
                $shop_live_info['application_status_con'] = '被关闭';
                break;
            default:
                $shop_live_info['application_status_con'] = '待审核';
                break;
        }
        $shop_live_info['application_time'] = date('Y-m-d H:i', $shop_live_info['application_time']);
        $shop_live_info['application_status_time'] = $shop_live_info['application_status_time'] ? date('Y-m-d H:i', $shop_live_info['application_status_time']) : '';
        $shop_live_info['application_stop_time'] = $shop_live_info['application_stop_time'] ? date('Y-m-d H:i', $shop_live_info['application_stop_time']) : '';
        return $this->data->addBody(-140, $shop_live_info);
    }


    //根据shop_id获取店铺信息
    public function getShopInfo()
    {
        $shop_id = request_string('shop_id');
        $Shop_BaseModel = new Shop_BaseModel();
        $shop_base = $Shop_BaseModel->getOne($shop_id);
        return $this->data->addBody(-140, $shop_base);
    }

    public function addOrEditApplication()
    {
        $live_application_id = request_string('live_application_id');
        $Live_ApplicationModel = new Live_ApplicationModel();
        $msg = '';
        if ($live_application_id) {//编辑
            $live_info = $Live_ApplicationModel->getOne($live_application_id);
            $cond_row['times'] = $live_info['times'] + 1;
            $cond_row['application_status'] = request_string('application_status');
            $cond_row['application_time'] = time();
            $flag = $Live_ApplicationModel->editApplication($live_application_id, $cond_row);
        } else {//添加
            $cond_row['shop_id'] = request_string('shop_id');

            //判断是否重复提交
            $live_res = $Live_ApplicationModel->getOneByWhere(array("shop_id" => request_string('shop_id')));
            if ($live_res) {
                if ($live_res['is_del'] == 1) {//平台删除数据后  重新申请
                    $cond_row['live_length'] = request_string('live_length');
                    $cond_row['application_time'] = time();
                    $cond_row['times'] = $live_res['times'] + 1;
                    $cond_row['application_status'] = 1;
                    $cond_row['is_del'] = 0;
                    $flag = $Live_ApplicationModel->editApplication($live_res['live_application_id'], $cond_row);
                } else {
                    $flag = false;
                    $msg = '已经提交成功';
                }
            } else {
                $cond_row['live_length'] = request_string('live_length');
                $cond_row['application_time'] = time();
                $cond_row['times'] = 1;
                $cond_row['application_status'] = 1;
                $flag = $Live_ApplicationModel->addApplication($cond_row);
            }
        }
        $cond_row['application_time'] = date('Y-m-d H:i', $cond_row['application_time']);
        if ($flag !== false) {
            return $this->data->addBody(-140, $cond_row, $msg ? $msg : 'success', 200);
        } else {
            return $this->data->addBody(-140, $cond_row, $msg ? $msg : 'failure', 250);
        }
    }

    public function getOnlineNum()
    {
        $room_id = request_string('roomID');
        $liveGoodsModel = new Live_GoodsModel();
        $condition = array(
            'roomId' => $room_id
        );
        $data = $liveGoodsModel->getOneByWhere($condition);

        //用户信息
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOne($data['user_id']);
        $data['user_name'] = $user_info['user_name'];
        $data['user_logo'] = $user_info['user_logo'];

        return $this->data->addBody(-140, $data);
    }

    public function editLive()
    {
        $room_id = request_string('roomID');
        $liveGoodsModel = new Live_GoodsModel();
        $condition = array(
            'roomId' => $room_id
        );
        $data = $liveGoodsModel->getOneByWhere($condition);

        $cond['online'] = $data['online'] + 1;
        $flag = $liveGoodsModel->editLiveGoods($data['live_id'], $cond);
        $data['flag'] = $flag;
        return $this->data->addBody(-140, $data);
    }

    public function getNum()
    {
        $StreamName = request_string('StreamName');
        $data = $this->getOnline($StreamName);
        return $this->data->addBody(-140, $data);
    }

    public function getOnline($StreamName, $flag = false, $time = '')
    {
        if ($flag) {
            $StartTime = date('Y-m-d H:i:s', strtotime("$time-6minute"));
            $EndTime = date('Y-m-d H:i:s', strtotime("$time-5minute"));
        } else {
            $StartTime = date('Y-m-d H:i:s', strtotime('-1minute'));
            $EndTime = date('Y-m-d H:i:s', time());
        }
        $Timestamp = time();
        $Action = "DescribeStreamPlayInfoList";
        $Nonce = rand(10000, 99999);
        $secretId = Web_ConfigModel::value('live_secretId');
        $param["Nonce"] = $Nonce;
        $param["Timestamp"] = $Timestamp;
        $param["SecretId"] = $secretId;
        $param["Action"] = $Action;
        $param["Version"] = "2018-08-01";
        $param["StartTime"] = $StartTime;
        $param["EndTime"] = $EndTime;
        $param["StreamName"] = $StreamName;
        ksort($param);
        $signStr = "POSTlive.tencentcloudapi.com/?";
        $signature = $this->sign($param, $signStr);

        $param["Signature"] = $signature;
        $url = "https://live.tencentcloudapi.com/";
        $headers = array(
            "Content-Type" => "application/x-www-form-urlencoded",
        );
        $ret = $this->postUrl($url, $param, $headers);
        $ret = json_decode($ret, true);
        return $ret['Response']['DataInfoList'][0];
    }

    public function liveCallback()
    {
        $callbackInfo = $_REQUEST;
        unset($callbackInfo['ctl']);
        unset($callbackInfo['met']);
        unset($callbackInfo['typ']);
        $str = pos(array_flip($callbackInfo)) . '"}';
        $backInfo = json_decode($str, true);
        $roomId = 'room' . substr($backInfo['stream_id'], 10);

        $Live_GoodsModel = new Live_GoodsModel();
        $liveGoods = $Live_GoodsModel->getOneByWhere(array('roomId' => $roomId));

        //在线人数
        // $online = $this->getOnline($backInfo['stream_id'], true, $backInfo['end_time']);
        // $OnlineNum = $online['Online']?$online['Online']:0;
        $OnlineNum = $liveGoods['online'] ? $liveGoods['online'] : 0;
        $data['FileId'] = $backInfo['file_id'];
        $data['Name'] = $roomId;
        $data['Description'] = $liveGoods['room_name'] . "_" . $OnlineNum;
        if ($liveGoods['room_img']) {
            $data['CoverData'] = $this->base64EncodeImage($liveGoods['room_img']);
        }
        $this->ModifyMediaInfo($data);
    }

    public function ModifyMediaInfo($data)
    {
        $Timestamp = time();
        $Nonce = rand(10000, 99999);
        $secretId = Web_ConfigModel::value('live_secretId');
        $param["Action"] = "ModifyMediaInfo";
        $param["Timestamp"] = $Timestamp;
        $param["Nonce"] = $Nonce;
        $param["SecretId"] = $secretId;
        $param["Version"] = "2018-07-17";
        $param["FileId"] = $data['FileId'];
        $param["Name"] = $data['Name'];
        $param["Description"] = $data['Description'];
        $param["CoverData"] = $data['CoverData'];
        $signStr = "POSTvod.tencentcloudapi.com/?";
        $signature = $this->sign($param, $signStr);
        $param["Signature"] = $signature;
//        $url = "https://vod.tencentcloudapi.com/?Action=ModifyMediaInfo&Version=" . urlencode("2018-07-17") . "&Timestamp=" . $Timestamp . "&Nonce={$Nonce}&SecretId=" . $secretId . "&Signature=" . urlencode($signature);
//        $url .=  "&FileId=" . $data['FileId'] . "&Name=" . $data['Name']  ."&Description=" . $data['Description'];
//        $ret = file_get_contents($url);
//        $ret = json_decode($ret, true);

        $url = "https://vod.tencentcloudapi.com/";
        $headers = array(
            "Content-Type" => "application/x-www-form-urlencoded",
        );
        $ret = $this->postUrl($url, $param, $headers);
        return $this->data->addBody(-140, $ret['Response']);
    }


    /** 把网络图片图片转成base64
     * @param string $img 图片地址
     * @return string
     */
    public function base64EncodeImage($img = '')
    {
        return base64_encode(file_get_contents($img));
    }

    public function getList($Offset = 0, $CommentValue = '')
    {
        $Timestamp = time();
        $Nonce = rand(10000, 99999);
        $secretId = Web_ConfigModel::value('live_secretId');
        $param["Action"] = "SearchMedia";
        $param["Timestamp"] = $Timestamp;
        $param["Nonce"] = $Nonce;
        $param["SecretId"] = $secretId;
        $param["Version"] = "2018-07-17";
        $param["Offset"] = $Offset * 10;
        $param["Limit"] = 10;
        if ($CommentValue != '') {
            $param["Text"] = $CommentValue;
        }
        $signStr = "POSTvod.tencentcloudapi.com/?";
        $signature = $this->sign($param, $signStr);
        $param["Signature"] = $signature;

        $url = "https://vod.tencentcloudapi.com/";
        $headers = array(
            "Content-Type" => "application/x-www-form-urlencoded",
        );
        $ret = $this->postUrl($url, $param, $headers);
        $ret = json_decode($ret, true);
        $Live_GoodsModel = new Live_GoodsModel();
        $data = array();
        foreach ($ret['Response']['MediaInfoSet'] as $k => $v) {
            $data[$k]['FileId'] = $v['FileId'];
            $data[$k]['roomID'] = $v['BasicInfo']['Name'];
            $data[$k]['roomImg'] = $v['BasicInfo']['CoverUrl'];
            $data[$k]['MediaUrl'] = $v['BasicInfo']['MediaUrl'];
            $data[$k]['Description'] = $v['BasicInfo']['Description'];
            $Description = explode("_", $v['BasicInfo']['Description']);
            $data[$k]['roomInfo'] = $Description[0];
            // $data[$k]['Online'] = $Description[1]?$Description[1]:0;

            $condition = array(
                'roomId' => $v['BasicInfo']['Name']
            );
            $info = $Live_GoodsModel->getOneByWhere($condition);
            $data[$k]['Online'] = $info['online'] ? $info['online'] : 0;

            $data[$k]['mixedPlayURL'] = '';
            $data[$k]['isBack'] = 1;
        }

        return $data;

    }

    //直播列表
    public function getLiveList()
    {
        $data = array();
        //轮播图
        $logos = $this->getBanners();
        $data['banner'] = $logos;

        $Offset = request_int('Offset');
        $CommentValue = request_string("CommentValue") ? request_string("CommentValue") : '';//筛选
        $rooms = request_row('rooms');;
        //直播列表
        if ($rooms) {
            $rooms = json_decode($rooms, true);
            $room_ids = array_column($rooms, 'roomID');
            $Live_GoodsModel = new Live_GoodsModel();
            $cond_row['roomId:IN'] = $room_ids;
            if ($CommentValue) {
                $cond_row['room_name:LIKE'] = "%" . $CommentValue . "%";
            }
            $liveGoods = $Live_GoodsModel->getByWhere($cond_row);
            $info = array();
            foreach ($liveGoods as $k => $v) {
                $info[$v['roomId']]['room_img'] = $v['room_img'];
                $info[$v['roomId']]['room_name'] = $v['room_name'];
            }
            $roomIds = array_column($liveGoods, 'roomId');

            foreach ($rooms as $k => $v) {
                if (in_array($v['roomID'], $roomIds)) {
                    $rooms[$k]['roomImg'] = $info[$v['roomID']]['room_img'];
                    $rooms[$k]['roomName'] = $info[$v['roomID']]['room_name'];

                    //在线人数
                    // $StreamName = '1400317205' . substr($v['roomID'], 4);
                    // $online = $this->getOnline($StreamName);
                    // $rooms[$k]['Online'] = $online['Online'];


                    $condition = array(
                        'roomId' => $v['roomID']
                    );
                    $info = $Live_GoodsModel->getOneByWhere($condition);
                    $rooms[$k]['Online'] = $info['online'] ? $info['online'] : 0;
                } else {
                    unset($rooms[$k]);
                }
            }
        }

        //回放列表
        $playback = $this->getList($Offset, $CommentValue);

        $data['rooms'] = $rooms;
        $data['playback'] = $playback;
        $data['lists'] = array_values(array_merge($rooms, $playback));

        return $this->data->addBody(-140, $data);
    }


    public function getBanners()
    {
        //轮播图
        $logos[0]['image'] = Web_ConfigModel::value('live_logo1');
        $logos[0]['url'] = Web_ConfigModel::value('live_url1');
        $logos[1]['image'] = Web_ConfigModel::value('live_logo2');
        $logos[1]['url'] = Web_ConfigModel::value('live_url2');
        $logos[2]['image'] = Web_ConfigModel::value('live_logo3');
        $logos[2]['url'] = Web_ConfigModel::value('live_url3');
        return $logos;
    }

    //签名
    function sign($param, $signStr)
    {
        $secretKey = Web_ConfigModel::value('live_secretKey');
        ksort($param);
        foreach ($param as $key => $value) {
            $signStr = $signStr . $key . "=" . $value . "&";
        }
        $signStr = substr($signStr, 0, -1);
        $signature = base64_encode(hash_hmac("sha1", $signStr, $secretKey, true));
        return $signature;
    }

    public function postUrl($url, $postData = false, $header = false)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回数据不直接输出

        //add header
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        //add ssl support
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    //SSL 报错时使用
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    //SSL 报错时使用
        }

        //add post data support
        if (!empty($postData)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }
        $content = curl_exec($ch); //执行并存储结果
        curl_close($ch);
        return $content;
    }
}