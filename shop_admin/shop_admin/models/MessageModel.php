<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class MessageModel extends Message
{
    const ORDER_MESSAGE   = 1;//订单信息
    const USER_MESSAGE = 3;//账户信息
    const OTHER_MESSAGE = 4;//其他信息
    const MESSAGE_SHOW = 0;//信息显示
    const MESSAGE_HIDE = 1;//信息隐藏

	public static $messagePhone = array(
		"0" => '关闭',
		"1" => '开启'
	);

	/**
	 * 读取分页列表
	 *
	 * @param  int $config_key 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getMessageList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		
		$data = $this->listByWhere($cond_row, $order_row, $page, $rows);
		
		return $data;
	}
	
	/**
	 * 删除选中的消息
	 *
	 * @param  array $config_array 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function removeMessageSelected($config_array = array())
	{

		foreach ($config_array as $key => $value)
		{
			$flag = $this->removeMessage($value);
		}
	}

	/**
	 * 读取详情
	 *
	 * @param  int $config_key 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getMessageDetail($order_row = array())
	{
		$data = $this->getOneByWhere($order_row);
		return $data;
	}

	/**
	 * 读数量
	 *
	 * @param  int $config_key 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getCount($cond_row = array())
	{
		return $this->getNum($cond_row);
	}

	
	/**
	 * 发送站内信,短信,邮件
	 *
	 * @param  int $config_key 主键值
	 * @return array $flag 返回的发送的状态
	 * @access public
	 */
    public function sendMessage($code, $message_user_id, $message_user_name, $order_id = NULL, $shop_name = NULL, $message_mold = 0, $message_type = 1,$end_time = Null,$common_id=NULL,$goods_id=NULL,$des=NULL, $start_time = Null,$goods_name=NULL,$av_amount=NULL,$freeze_amount=NULL,$ztm=NULL,$chain_name=NULL,$content_mobile=NULL,$area_code=NULL, $user_name = NULL)
    {
        $send_row['code'] = $code;
        $this->messageTemplateModel = new Message_TemplateModel();

        $de = $this->messageTemplateModel->getTemplateDetail($send_row);
        $user_row['user_id'] = $message_user_id;
        
        $this->userInfo = new User_InfoModel();
        $member = $this->userInfo->getUserInfo($user_row);
        $info = 0;
        $flag = false;
        if ($message_mold == 0)
        {
            $this->messageSettingModel = new Message_SettingModel();
            $message = $this->messageSettingModel->getSettingDetail($user_row);
            if($message)
            {
                $arr = explode(',', $message['message_template_all']);
                if (in_array($de['id'], $arr))
                {
                    $info   = 1;
                    $mobile = $member['user_mobile'];
                    $area_codes = $member['area_code']?:86;
                    $email  = $member['user_email'];
                }
               
            }else{
                $mobile = "";
                $email  = "";
                $area_codes="";
            }
        }
        else
        {
            $mobile = $member['user_mobile']?:86;
            $email  = $member['user_email'];
            $area_codes = $member['area_code'];
            $info   = 1;
        }

        if($content_mobile)
        {
            $mobile = $content_mobile;
        }
        if($area_code){
            $area_codes = $area_code;
        }
        
        //先判断平台是否开启站内信
        if($de['is_mail'] == 1)
        {
            //1。平台设置强制发送 2。平台未设置强制发送，用户选择发送
            if($de['force_mail'] == 1 || ($de['force_mail'] == 0 && $info == 1))
            {
                $me = $de['content_mail'];
                $time     = get_date_time();
                $web_name = Web_ConfigModel::value("site_name");

                $me = str_replace("[order_id]", $order_id, $me);
                $me = str_replace("[date]", $time, $me);
                $me = str_replace("[weburl_name]", $web_name, $me);
                $me = str_replace("[name]", $shop_name, $me);
                $me = str_replace("[shop_name]", $shop_name, $me);
                $me = str_replace("[end]", $end_time, $me);
                $me = str_replace("[start_time]", $start_time, $me);
                $me = str_replace("[weburl_url]", Yf_Registry::get('url'), $me);
                $me = str_replace("[common_id]", $common_id, $me);
                $me = str_replace("[goods_id]", $goods_id, $me);
                $me = str_replace("[des]", $des, $me);
                $me = str_replace("[av_amount]", $av_amount, $me);
                $me = str_replace("[freeze_amount]", $freeze_amount, $me);
                $me = str_replace("[goods_name]", $goods_name, $me);
                $me = str_replace("[ztm]", $ztm, $me);
                $me = str_replace("[chain_name]", $chain_name, $me);
                $me = str_replace("[user_name]", $user_name, $me);

                $orders_row['message_content']     = $me;
                $orders_row['message_create_time'] = $time;
                $orders_row['message_mold']        = $message_mold;
                $orders_row['message_type']        = $message_type;
                $orders_row['message_title']       = $de['name'];
                $orders_row['message_user_id']     = $message_user_id;
                $orders_row['message_user_name']   = $message_user_name;

                $flag = $this->addMessage($orders_row);
                $im_code = '';
                switch ($code)
                {
                    case 'place_your_order':
                        $im_code = '下单通知';
                        break;
                    case 'goods are not in stock':
                        $im_code = '您的商品库存不足';
                        break;
                    case 'Refund reminder':
                        $im_code = '退款提醒';
                        break;
                    case 'Refund automatic processing reminder':
                        $im_code = '退款自动处理提醒';
                        break;
                    case 'Return reminder':
                        $im_code = '退货提醒';
                        break;
                    case 'Return automatic processing reminder':
                        $im_code = '退货自动处理提醒';
                        break;
                    case 'Automatic handling reminder':
                        $im_code = '退货未收货自动处理提醒';
                        break;
                    case 'Settlement sheet for confirmation':
                        $im_code = '结算单等待确认提醒';
                        break;
                    case 'Settlement bill has been paid to remind':
                        $im_code = '结算单已经付款提醒';
                        break;
                    case 'ordor_complete_shipping':
                        $im_code = '发货通知';
                        break;
                    case 'Payment reminder':
                        $im_code = '付款成功提醒';
                        break;
                    case 'Refund return reminder':
                        $im_code = '退款退货提醒';
                        break;
                    case 'Redemption code is about to expire reminder':
                        $im_code = '兑换码即将到期提醒';
                        break;
                    case 'Balance change alert':
                        $im_code = '余额变动提醒';
                        break;
                    case 'Prepaid card balance change reminder':
                        $im_code = '充值卡余额变动提醒';
                        break;
                    case 'Red Alert':
                        $im_code = '红包使用提醒';
                        break;
                    case 'Self pick up code':
                        $im_code = '自提码';
                        break;
                    case 'credit return waring':
                        $im_code = '白条还款提醒';
                        break;
                    case 'virtual pick up code':
                        $im_code = '虚拟订单兑换码';
                        break;
                    case 'bargain success code':
                        $im_code = '砍价成功通知';
                        break;
                }
                if($im_code)
                {
                    $User_BaseModel = new User_BaseModel();
                    $user_base = $User_BaseModel->getOneByWhere(['user_id'=>$message_user_id]);
                    //向im发送消息
                    $im_url = Yf_Registry::get('im_api_url').'?'.'ctl=ImApi&met=pushMsg';
                    $im_typ = 'json';
                    $im_method = 'GET';
                    $im_receiver = $user_base['user_account'];
                    $im_param = [];
                    $im_param['receiver'] = $im_receiver;
                    $im_param['account_system'] = 'admin';
                    $im_param['msg_content'] = $me.'&*'.'#1'.'&*'.$im_code;
                    $im_param['push_type'] = 1;
                    $im_param['msg_type'] = 1;
                    //$im_result = get_url($im_url, $im_param, $im_typ, $im_method);
                }
                //极光推送
                $this->sellerBBCJpush($de['id'], $message_user_id, $me);
            }

        }
        //先判断后台是否开启了短信功能，用户手机号是否存在
        if($de['is_phone'] == 1  && $mobile)
        {
            //1.判断后台是否开启了强制接受  2.平台未开启强制接受，用户选择开启接受
            if($de['force_phone'] == 1  || ($de['force_phone'] == 0 && $info == 1))
            {
                $phone = $de['content_phone'];

                $sms_data = array();
                if(preg_match("/\[order_id\]/", $phone)) {$sms_data['order_id'] = $order_id;}
                if(preg_match("/\[date\]/", $phone)) {$sms_data['date'] = $time;}
                if(preg_match("/\[weburl_name\]/", $phone)) {$sms_data['weburl_name'] = $web_name;}
                if(preg_match("/\[name\]/", $phone)) {$sms_data['name'] = $shop_name;}
                if(preg_match("/\[shop_name\]/", $phone)) {$sms_data['shop_name'] = $shop_name;}
                if(preg_match("/\[end\]/", $phone)) {$sms_data['end'] = $end_time;}
                if(preg_match("/\[start_time\]/", $phone)) {$sms_data['start_time'] = $start_time;}
                if(preg_match("/\[weburl_url\]/", $phone)) {$sms_data['weburl_url'] = Yf_Registry::get('url');}
                if(preg_match("/\[common_id\]/", $phone)) {$sms_data['common_id'] = $common_id;}
                if(preg_match("/\[goods_id\]/", $phone)) {$sms_data['goods_id'] = $goods_id;}
                if(preg_match("/\[des\]/", $phone)) {$sms_data['des'] = $des;}
                if(preg_match("/\[av_amount\]/", $phone)) {$sms_data['av_amount'] = $av_amount;}
                if(preg_match("/\[freeze_amount\]/", $phone)) {$sms_data['freeze_amount'] = $freeze_amount;}
                if(preg_match("/\[goods_name\]/", $phone)) {$sms_data['goods_name'] = $goods_name;}
                if(preg_match("/\[ztm\]/", $phone)) {$sms_data['ztm'] = $ztm;}
                if(preg_match("/\[chain_name\]/", $phone)) {$sms_data['chain_name'] = $chain_name;}
                if(preg_match("/\[user_name\]/", $phone)) {$sms_data['user_name'] = $user_name;}

                $time     = get_date_time();
                $web_name = Web_ConfigModel::value("site_name");

                $phone = str_replace("[order_id]", $order_id, $phone);
                $phone = str_replace("[date]", $time, $phone);
                $phone = str_replace("[weburl_name]", $web_name, $phone);
                $phone = str_replace("[name]", $shop_name, $phone);
                $phone = str_replace("[shop_name]", $shop_name, $phone);
                $phone = str_replace("[end]", $end_time, $phone);
                $phone = str_replace("[start_time]", $start_time, $phone);
                $phone = str_replace("[weburl_url]", Yf_Registry::get('url'), $phone);
                $phone = str_replace("[common_id]", $common_id, $phone);
                $phone = str_replace("[goods_id]", $goods_id, $phone);
                $phone = str_replace("[des]", $des, $phone);
                $phone = str_replace("[av_amount]", $av_amount, $phone);
                $phone = str_replace("[freeze_amount]", $freeze_amount, $phone);
                $phone = str_replace("[goods_name]", $goods_name, $phone);
                $phone = str_replace("[ztm]", $ztm, $phone);
                $phone = str_replace("[chain_name]", $chain_name, $phone);
                $phone = str_replace("[user_name]", $user_name, $phone);
                $str = Sms::send($mobile,$area_codes, $phone, $de['baidu_tpl_id'],$sms_data);
                //Yf_Log::log($phone,Yf_Log::ERROR,'sms');
                $flag = true;

            }

        }
        //先判断后台是否开启了邮件功能，用户邮箱是否存在
        if($de['is_email'] == 1  && $email)
        {
            //1.判断后台是否开启了强制接受  2.平台未开启强制接受，用户选择开启接受
            if($de['force_email'] == 1  || ($de['force_email'] == 0 && $info == 1))
            {
                $emails = $de['content_email'];
                $title  = $de['title'];
                $time     = get_date_time();
                $web_name = Web_ConfigModel::value("site_name");
                $user_name = Web_ConfigModel::value("email_id");

                $emails = str_replace("[order_id]", $order_id, $emails);
                $emails = str_replace("[date]", $time, $emails);
                $emails = str_replace("[weburl_name]", $web_name, $emails);
                $emails = str_replace("[name]", $shop_name, $emails);
                $emails = str_replace("[shop_name]", $shop_name, $emails);
                $emails = str_replace("[end]", $end_time, $emails);
                $emails = str_replace("[start_time]", $start_time, $emails);
                $emails = str_replace("[weburl_url]", Yf_Registry::get('url'), $emails);
                $emails = str_replace("[common_id]", $common_id, $emails);
                $emails = str_replace("[goods_id]", $goods_id, $emails);
                $emails = str_replace("[user_name]", $user_name, $emails);
                $emails = str_replace("[des]", $des, $emails);
                $emails = str_replace("[av_amount]", $av_amount, $emails);
                $emails = str_replace("[freeze_amount]", $freeze_amount, $emails);
                $emails = str_replace("[goods_name]", $goods_name, $emails);
                $emails = str_replace("[ztm]", $ztm, $emails);
                $emails = str_replace("[chain_name]", $chain_name, $emails);
                $emails = str_replace("[user_name]", $user_name, $emails);

                $title  = str_replace("[weburl_name]", $web_name, $title);
                $str = Email::sendMail($email, $message_user_name, $title, $emails);
                $flag = true;
            }
        }
        //微信公众号消息通知
        $user_bind_wx = $this->is_bind_wxpublic($message_user_id);//是否绑定微信公众号
        $bnd_open_id = $user_bind_wx['data']['bind_openid'];//微信用户Openid
        $wechat_public_status_open = Web_ConfigModel::value('wechat_public_status');//微信公众号开关
        if($de['is_wechart_pulic'] == 1  && $user_bind_wx['status']==200 && $bnd_open_id && $wechat_public_status_open)
        {
            //1.判断后台是否开启了强制接受  2.平台未开启强制接受，用户选择开启接受
            if($de['force_wechart_public'] == 1  || ($de['force_wechart_public'] == 0 && $info == 1))
            {
                $data = array();
                $data['message_user_id'] = $message_user_id;//接收用户id
                $data['message_user_name'] = $message_user_name;//接收用户名称
                $data['order_id'] = $order_id;//订单号
                $data['shop_name'] = $shop_name;//商家店铺名称
                $data['end_time'] = $end_time;//结束时间
                $data['common_id'] = $common_id;//商品common_id
                $data['goods_id'] = $goods_id;//商品goods_id
                $data['des'] = $des;//商品审核失败原因
                $data['start_time'] = $start_time;//结算单等待确认开始时间
                $data['goods_name'] = $goods_name;//商品名称
                $tpl_data  =  $this->dealSendData($data);
                Sms::sendWxPublicMsg($tpl_data);//发送模板消息
                $flag = true;
            }

        }
        
        return $flag;
    }
    
    public function sellerBBCJpush($msg_id,$msg_user_id,$content){
        //极光推送，商家app
        $seller_msg = array(
            //需要发推送的ID，和卖家app一致，/vue/sms.js
            21,5,8,14,15,16,17,18,12,13,11,3,19,20,32
        );
        if(in_array($msg_id, $seller_msg)){
            //先查该用户是否允许发推送
            $app_notify_model = new App_NotifyModel();
            $app_notify_info = $app_notify_model->getOneByWhere(array('user_id'=>$msg_user_id));
            if($app_notify_info && ($app_notify_info['app_notify_voice'] || $app_notify_info['app_notify_vibrate'])){
                $client = new AppJpush();
                $result = $client->bbcwebJpush($msg_user_id, $content);
                return $result;
            } else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getMessageCount($user_id)
    {
        $cond_row = array();
        $cond_row['message_user_id'] = $user_id;
        $cond_row['message_mold'] = 0;
        $cond_row['message_isdelete'] = 0;
        $cond_row['message_islook'] = 0;

        $data = $this->getByWhere($cond_row);
        if (!empty($data)) {
            $flag = true;
        } else {
            $flag = false;
        }
        return $flag;
    }

    /**
     *
     * 判断用户是否绑定微信公众号
     */
    public function is_bind_wxpublic($user_id){
        $key = Yf_Registry::get('ucenter_api_key');;
        $url       = Yf_Registry::get('ucenter_api_url');
        $app_id    = Yf_Registry::get('ucenter_app_id');

        //本地读取远程信息
        $formvars              = array();
        $formvars['app_id']    = $app_id;
        $formvars['user_id'] = $user_id;
        $formvars['ctl'] = 'Api';
        $formvars['met'] = 'getUserWxBindFlag';
        $formvars['typ'] = 'json';
        $init_rs = get_url_with_encrypt($key, $url, $formvars);
        return $init_rs;
    }

    /**
     *
     * 处理发送数据
     */
    public function dealSendData($data){
        $code = $data['code'];
        switch ($code)
        {
            //下单通知
            case 'place_your_order':
                //根据订单号查询数据
                $order_id = $data['order_id'];
                $Order_BaseModel = new Order_BaseModel();
                $order_base = $Order_BaseModel->getOneByWhere(array('order_id'=>$order_id));
                $post_data = array(
                    //'touser'=>'ocpzy1Bk5LI_eo08MmpGaGhXCzjM',//接收者openid
                    //'template_id'=>'E8MazwWo4Tvg4gMCc2KIQn3Tnh6RSZkWSk_ydmL9q_4',//模板ID
                    //'url'=>'http://www.baidu.com',//模板跳转链接（海外帐号没有跳转能力）
                    'data' => array(//模板数据
                        "first"=>array(
                            'value'=>'您好，您有一个新订单，请尽快接单处理',//title
                            "color"=>"#173177",
                        ) ,
                        'keyword1'=>array(
                            'value'=> $order_base['order_id'],//订单编号
                            "color"=>"#173177",
                        ) ,
                        'keyword2'=>array(
                            'value'=> $order_base['buyer_user_name'],//客户昵称
                            "color"=>"#173177",
                        ) ,
                        'keyword3'=>array(
                            'value'=> $order_base['order_payment_amount'],//订单价格
                            "color"=>"#173177",
                        ) ,
                        'keyword4'=>array(
                            'value'=> $order_base['shop_name'],//订单标题
                            "color"=>"#173177",
                        ) ,
                        'keyword5' => array(
                            'value' => $order_base['order_create_time'],//订单截止时间
                            "color" => "#173177",
                        ),
                        "remark"=>array(
                            'value'=>'点击查看详情',//结束语
                            "color"=>"#173177",
                        ) ,
                    ));
                break;
            //您的商品库存不足
            case 'goods are not in stock':
                $goods_id = $data['goods_id'];
                $Goods_BaseModel = new Goods_BaseModel();
                $goods_base = $Goods_BaseModel->getOneByWhere(array('goods_id'=>$goods_id));
                $post_data = array(
                    //'touser'=>'ocpzy1Bk5LI_eo08MmpGaGhXCzjM',//接收者openid
                    //'template_id'=>'E8MazwWo4Tvg4gMCc2KIQn3Tnh6RSZkWSk_ydmL9q_4',//模板ID
                    //'url'=>'http://www.baidu.com',//模板跳转链接（海外帐号没有跳转能力）
                    'data' => array(//模板数据
                        "first" => array(
                            'value' => '您的商品库存不足',//title
                            "color" => "#173177",
                        ),
                        'keyword1' => array(
                            'value' => $goods_base['shop_name'],//店铺名称
                            "color" => "#173177",
                        ),
                        'keyword2' => array(
                            'value' => $goods_base['goods_name'],//商品名称
                            "color" => "#173177",
                        ),
                        'keyword3' => array(
                            'value' => $goods_base['goods_stock'],//库存数量
                            "color" => "#173177",
                        ),
                        "remark" => array(
                            'value' => '库存不足',//结束语
                            "color" => "#173177",
                        ),
                    ));
                break;
            //交易被投诉
            case 'Complaints_of_goods':
                $order_id = $data['order_id'];
                $Complain_BaseModel = new Complain_BaseModel();
                $complain_base = $Complain_BaseModel->getOneByWhere(array('order_id' => $order_id));
                $complain_id = $complain_base['complain_id'];
                $Complain_GoodsModel = new Complain_GoodsModel();
                $complain_goods = $Complain_GoodsModel->getOneByWhere(array('complain_id' => $complain_id));
                $post_data = array(
                    //'touser'=>'ocpzy1Bk5LI_eo08MmpGaGhXCzjM',//接收者openid
                    //'template_id'=>'E8MazwWo4Tvg4gMCc2KIQn3Tnh6RSZkWSk_ydmL9q_4',//模板ID
                    //'url'=>'http://www.baidu.com',//模板跳转链接（海外帐号没有跳转能力）
                    'data' => array(//模板数据
                        "first" => array(
                            'value' => '投诉通知',//title
                            "color" => "#173177",
                        ),
                        'keyword1' => array(
                            'value' => $complain_base['user_account_accuser'],//投诉人
                            "color" => "#173177",
                        ),
                        'keyword2' => array(
                            'value' => $complain_base['user_account_accused'],//被投诉人
                            "color" => "#173177",
                        ),
                        'keyword3' => array(
                            'value' => $complain_goods['goods_name'],//被投诉商品
                            "color" => "#173177",
                        ),
                        'keyword4' => array(
                            'value' => $complain_base['complain_content'],//被投诉原因
                            "color" => "#173177",
                        ),
                        "remark" => array(
                            'value' => '交易被投诉，请尽快处理',//结束语
                            "color" => "#173177",
                        ),
                    ));
                break;
            //商品审核失败
            case 'Commodity audit failed to remind':
                $common_id = $data['common_id'];
                $Goods_CommonModel = new Goods_CommonModel();
                $goods_common = $Goods_CommonModel->getOneByWhere(array('common_id'=> $common_id));
                $post_data = array(
                    //'touser'=>'ocpzy1Bk5LI_eo08MmpGaGhXCzjM',//接收者openid
                    //'template_id'=>'E8MazwWo4Tvg4gMCc2KIQn3Tnh6RSZkWSk_ydmL9q_4',//模板ID
                    //'url'=>'http://www.baidu.com',//模板跳转链接（海外帐号没有跳转能力）
                    'data' => array(//模板数据
                        "first" => array(
                            'value' => '商品审核失败',//title
                            "color" => "#173177",
                        ),
                        'keyword1' => array(
                            'value' => $goods_common['common_name'],//账号  商品名称
                            "color" => "#173177",
                        ),
                        'keyword2' => array(
                            'value' => $goods_common['common_verify_remark'],//内容
                            "color" => "#173177",
                        ),
                        'keyword3' => array(
                            'value' => data('Y-m-d H:i:s'),//时间
                            "color" => "#173177",
                        ),
                        "remark" => array(
                            'value' => '抱歉，审核失败！',//结束语
                            "color" => "#173177",
                        ),
                    ));
                break;
            //商品违规被下架
            case 'Commodity violation is under the shelf':
                $common_id = $data['common_id'];
                $Goods_CommonModel = new Goods_CommonModel();
                $goods_common = $Goods_CommonModel->getOneByWhere(array('common_id' => $common_id));
                $post_data = array(
                    //'touser'=>'ocpzy1Bk5LI_eo08MmpGaGhXCzjM',//接收者openid
                    //'template_id'=>'E8MazwWo4Tvg4gMCc2KIQn3Tnh6RSZkWSk_ydmL9q_4',//模板ID
                    //'url'=>'http://www.baidu.com',//模板跳转链接（海外帐号没有跳转能力）
                    'data' => array(//模板数据
                        "first" => array(
                            'value' => '商品违规下架',//title
                            "color" => "#173177",
                        ),
                        'keyword1' => array(
                            'value' => $goods_common['common_state_remark'],//违规内容
                            "color" => "#173177",
                        ),
                        'keyword2' => array(
                            'value' => '商品下架',//处理方式
                            "color" => "#173177",
                        ),
                        "remark" => array(
                            'value' => '对不起，您发布的内容以违规！',//结束语
                            "color" => "#173177",
                        ),
                    ));
                break;
                break;
            //退款提醒
            case 'Refund reminder':
                $order_id = $data['order_id'];
                $Order_BaseModel = new Order_BaseModel();
                $order_base = $Order_BaseModel->getOneByWhere(array('order_id' => $order_id));
                $Order_ReturnModel = new Order_ReturnModel();
                $return_goods = $Order_ReturnModel->getOneByWhere(array('order_id' => $order_id));
                $post_data = array(
                    //'touser'=>'ocpzy1Bk5LI_eo08MmpGaGhXCzjM',//接收者openid
                    //'template_id'=>'E8MazwWo4Tvg4gMCc2KIQn3Tnh6RSZkWSk_ydmL9q_4',//模板ID
                    //'url'=>'http://www.baidu.com',//模板跳转链接（海外帐号没有跳转能力）
                    'data' => array(//模板数据
                        "first" => array(
                            'value' => '退款提醒',//title
                            "color" => "#173177",
                        ),
                        'keyword1' => array(
                            'value' => $return_goods['goods_name'],//商品名称
                            "color" => "#173177",
                        ),
                        'keyword2' => array(
                            'value' => $order_base['order_id'],//订单号
                            "color" => "#173177",
                        ),
                        'keyword3' => array(
                            'value' => $order_base['order_refund_amount'],//退款金额
                            "color" => "#173177",
                        ),
                        "remark" => array(
                            'value' => '查看详情',//结束语
                            "color" => "#173177",
                        ),
                    ));
                break;
            //结算单等待确认提醒
            case 'Settlement sheet for confirmation':
                $post_data = array(
                    //'touser'=>'ocpzy1Bk5LI_eo08MmpGaGhXCzjM',//接收者openid
                    //'template_id'=>'E8MazwWo4Tvg4gMCc2KIQn3Tnh6RSZkWSk_ydmL9q_4',//模板ID
                    //'url'=>'http://www.baidu.com',//模板跳转链接（海外帐号没有跳转能力）
                    'data' => array(//模板数据
                        "first" => array(
                            'value' => '结算单等待确认提醒',//title
                            "color" => "#173177",
                        ),
                        'keyword1' => array(
                            'value' => $data['order_id'],//结算单号
                            "color" => "#173177",
                        ),
                        'keyword2' => array(
                            'value' => $data['end_time'],//时间
                            "color" => "#173177",
                        ),
                        "remark" => array(
                            'value' => '请尽快处理',//结束语
                            "color" => "#173177",
                        ),
                    ));
                break;
            //退款自动处理提醒
            case 'Refund automatic processing reminder':
                $post_data = array();
                break;
            //退货提醒
            case 'Return reminder':
                $post_data = array();
                break;
            //退货自动处理提醒
            case 'Return automatic processing reminder':
                $post_data = array();
                break;
            //退货未收货自动处理提醒
            case 'Automatic handling reminder':
                $post_data = array();
                break;
            //结算单已经付款提醒
            case 'Settlement bill has been paid to remind':
                $post_data = array();
                break;
            //发货通知
            case 'ordor_complete_shipping':
                $post_data = array();
                break;
            //付款成功提醒
            case 'Payment reminder':
                $post_data = array();
                break;
            //退款退货提醒
            case 'Refund return reminder':
                $post_data = array();
                break;
            //兑换码即将到期提醒
            case 'Redemption code is about to expire reminder':
                $post_data = array();
                break;
            //余额变动提醒
            case 'Balance change alert':
                $post_data = array();
                break;
            //充值卡余额变动提醒
            case 'Prepaid card balance change reminder':
                $post_data = array();
                break;
            //红包使用提醒
            case 'Red Alert':
                $post_data = array();
                break;
            //自提码
            case 'Self pick up code':
                $post_data = array();
                break;
            //白条还款提醒
            case 'credit return waring':
                $post_data = array();
                break;
            //虚拟订单兑换码
            case 'virtual pick up code':
                $post_data = array();
                break;
            //砍价成功通知
            case 'bargain success code':
                $post_data = array();
                break;
        }
        return $post_data;
    }

}

?>