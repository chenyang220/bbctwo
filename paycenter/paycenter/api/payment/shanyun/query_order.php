<?php
   require_once '../../../configs/config.ini.php';
   header('content-type:text/html;charset=utf-8');
   queryPayStatus();
   /**
    * 查询订单支付状态
    * 
    * @dateTime  2020-07-24 
    * @author fzh   UPDATE `pay_union_order` SET `uorder_id` = '' WHERE `uorder_id` is null;
    * @license   仅限本公司授权用户使用。
    * @version   3.8.1
    */
   function queryPayStatus(){
        $order_model = new Union_OrderModel();
        $con_row = array();  
        $con_row['payType:<>'] = 1;
        $con_row['order_state_id'] = 2;
        $con_row['notify_data:<>'] = '';
        $result = $order_model->getOneByWhere($con_row,array('create_time'=>'DESC'));
        if ($result) {
            $order_model->editUnionOrder($result['union_order_id'],array('payType'=>1));
            $waybillno = $result['notify_data']['orderno'];
            $queryId = $result['notify_data']['queryId'];
            if($result['Access_mode']=='PC'){
                $qrtype = 'qr';
            }else if($result['Access_mode']=='mobile_phone'){
                $qrtype = 'h5';
            }else if($result['Access_mode']=='mobile_APP'){
                $qrtype = 'app';
            }elseif($result['Access_mode']=='xcx'){
                $qrtype = 'xcx';
            }
            $merid = Web_ConfigModel::value('yunshan_merid');
            $params = array();
            $params['mer_id'] = $merid; //固定值 v2
            $params['waybillno'] = $waybillno;
            $params['queryId'] = $queryId;
            $params['qrtype'] = $qrtype;
            $params['signType'] = 'MD5';
            $mac = signsyl($params);
            $params['mac'] = $mac;
            $action = 'https://dhjt.chinaums.com/queryService/UmsWebPayQuery';
            $form_html = create_html($params, $action);
            $form_html = json_decode($form_html,true);
            // echo '<pre>';
            // print_r($form_html);die;
            //日志请不要删除
            Yf_Log::log($waybillno, Yf_Log::INFO, 'queryorder');
            Yf_Log::log($form_html, Yf_Log::INFO, 'queryorder');
            if ($form_html['code'] == '00') {
                if ($form_html['status'] == 'TRADE_SUCCESS') {
                    $Consume_DepositModel = new Consume_DepositModel();
                    $Consume_DepositModel->notifyShopYl($waybillno, $result['buyer_id']);
                }
            }
            
        }
   }

    /**
     * 签名
     * 
     * @dateTime  2020-07-24
     * 
     * @author fzh
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
    function  signsyl($postData){
        $md5Key = Web_ConfigModel::value('yunshan_key');
        ksort($postData);
        $sign = '';
        foreach ($postData as $v) {
            $sign .= $v;
        }
        $sign = strtoupper(md5($sign . $md5Key));
        return $sign;
    }

     /**
     * 请求资源
     * 
     * @dateTime  2020-07-24
     * @author fzh
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
     function create_html($params, $action) {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $action);
        //设置头文件的信息作为数据流输出
        //        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }