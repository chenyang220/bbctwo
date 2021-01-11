<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 *
 *
 * @category   Framework
 * @package    __init__
 * @author     Xinze <xinze@live.cn>
 * @copyright  Copyright (c) 2010, 朱羽婷
 * @version    1.0
 * @todo
 */
class Ve_Ylpays 
{
	
	public  $notify_url = ''; //接受通知的信息地址信息
	public  $md5Key ; // 银联的加密秘钥 
    public  $oneurl; //
    public  $yunshantixiankey ; // 提现的秘钥 

	/**
	 * 初始化配置信息
	 * 
	 * @dateTime  2020-06-29
	 * 
	 * @author fzh
	 * @copyright https://www.yuanfeng.cn
	 * @license   仅限本公司授权用户使用。
	 * @version   3.8.1
	 */
	public function __construct()
	{
        $this->oneurl  =  Web_ConfigModel::value('yunshan_url').'/entryService/UmsWithdrawals'; // 单笔订单提现测试地址
		$this->returnurl  = Web_ConfigModel::value('yunshan_url').'/queryService/UmsWebPayRefund'; // 退款接口信息
		$this->md5Key = Web_ConfigModel::value('yunshan_key');
		$this->notify_url = Yf_Registry::get('shop_api_url')."/shop/api/txnotify.php" ;  // 通知清算结构清算
		$this->checkurl = Web_ConfigModel::value('yunshan_url').'/entryService/UmsWithdrawals'; // 提现查询的接口
	    // 提现的秘钥
		$this->yunshantixiankey = Web_ConfigModel::value('yunshantixian_key');
	}

    
	// 通知大华清算
	public function notifytodahua($params){
		$params['responseurl'] = $this->notify_url;
        $mac = $this -> signsyl($params,$this->yunshantixiankey); // 签名
        $params['mac']  = $mac;  // 签名  以上参数值和密钥拼接后的MD5值   必填写
        $params = json_encode($params);
        $html_form =$this-> create_html($params,  $this->oneurl);
        $html_form = json_decode($html_form,true);
	   // 4  成功  5 失败   0 处理中  1 待处理
		$res = array();
		$res["status"] = "250";   // 默认是失败
		$res["msg"] = $html_form["errMsg"];
        if ($html_form['errCode'] == '00'){
			 if($html_form["txstatus"] == "4"){
			      $res["status"] = "200" ;  // 成功
			 }elseif($html_form["txstatus"] == "5"){
			      $res["status"] = "250";  // 失败
			 }elseif($html_form["txstatus"] == "0"){
			      $res["status"] = "300";  // 处理中
			 }elseif($html_form["txstatus"] == "1"){
			      $res["status"] = "300";  // 待处理
			 }
        }
        return  $res;
	}
  
	/**
	 * 通知退款
	 *
	 * 
	 * @dateTime  2020-05-21
	 * @author fzh
	 * @link      https://github.com/mustify
	 * @copyright https://www.yuanfeng.cn
	 * @license   仅限本公司授权用户使用。
	 * @version   3.8.1
	 * @param     array  $uorderspam  退款訂單信息
	 */
	public function  refundorder($uorderspam){
	   
        $Order_BaseModel = new Order_BaseModel();
		$sql="select * from  `pay_web_config`  where config_type='yunshan'";
		$yuninfo = $Order_BaseModel->sql->getAll($sql);
		$config = array();
		foreach($yuninfo as $k=>$v){
			$config[$v["config_key"]]  = $v['config_value'];
		}
		$md5key = $config["yunshan_key"]; 
		$mchid = $config["yunshan_mchid"]; 

		if(!$md5key){
			$re = array();
			$re["status"] = 250 ;
			$re["msg"] = "银联配置参数不存在" ;
	        return $re ;
        }
		$uorder = $uorderspam["uorder"] ;
	    $order_id  = $uorderspam["order_id"] ; // 订单
		$veyfeecash = $uorderspam["veyfeecash"] ; // 这个订单的平台的佣金
		$verealcash  = $uorderspam["verealcash"] ;  // 实际收款
		$veyingcash  = $uorderspam["veyingcash"] ;  // 应收款
		$vepayshopnumer  =  $uorderspam["vepayshopnumer"] ;// app端支付功能
		$cbpayshopnumer  =  $uorderspam["cbpayshopnumer"] ;// pc端支付
		$xcxpayshopnumer  =  $uorderspam["xcxpayshopnumer"] ;// 小程序
        // 不同的支付方式传的支付方式不一样
		if($uorder['Access_mode']=='PC'){
			$vepayshopnumer = $cbpayshopnumer ;
			$qrtype = 'qr';
		}else if($uorder['Access_mode']=='mobile_phone'){
			$vepayshopnumer = $cbpayshopnumer ;
			$qrtype = 'h5';
		}else if($uorder['Access_mode']=='mobile_APP'){
			$vepayshopnumer = $vepayshopnumer ;
			$qrtype = 'app';
		}elseif($uorder['Access_mode']=='xcx'){
		    $vepayshopnumer = $xcxpayshopnumer ;
		    $qrtype = 'xcx';
		}

		$return_cash  = $uorderspam["return"]["return_cash"] ; // 退款金额
		$return_code =  $uorderspam["return"]["return_code"] ; // 退款单号
 		$scales =  $return_cash/$veyingcash;     // 退款的钱 / 订单的钱 
        // 小程序
		if($uorder['vetranstype'] == "xcx_pay"){
		   $uorder['vedevicetype'] = "android";
		}
        
        $platformAmount  = round($veyfeecash*$scales,2); 
        $totalAmount  = $return_cash -  $platformAmount ; // 单位是元
		$totalAmount = round($totalAmount,2) ;
        $subOrders = array();
        $subOrders[0]['totalAmount'] = $totalAmount * 100;
        $subOrders[0]['mid'] =  $vepayshopnumer;
        $params = array();
        $params['mer_id'] = $config['yunshan_merid'];
        $params['order_no'] = $uorder["union_order_id"];
        $params['qrtype'] = $qrtype;
        $params['queryId'] = $uorder["vequeryid"];
        $params['refund_amt'] = $return_cash;
         //处理退款单号
         $return_code = explode("-", $return_code ) ;
		 $return_code = implode("",$return_code) ;
		 $return_code = substr($return_code,0,28) ; // 截取28个字符串
	     $return_code = trim($return_code);

        $params['refund_no'] = $return_code;//$return_code;
        $params['platformAmount'] = $platformAmount * 100; //单位分
        $params['subOrders'] = json_encode($subOrders,JSON_UNESCAPED_UNICODE);
        $params['refund_desc'] = 'refund_desc';
        $params['signType'] = 'MD5';
        $mac = $this -> signsyl($params,$config['yunshan_key']); // 签名
        $params['mac'] = $mac;
        $params = json_encode($params);
        $html_form =$this-> create_html($params,  $this->returnurl);
        $html_form = json_decode($html_form,true);
		$res = array();
		$res["status"] = "0" ;   // 默认是失败
		$res["msg"] = "" ; 
        if (is_array($html_form)){
            if($html_form['code']=='00'){
		       $res["status"] = "200" ;  // 成功
			   $res["msg"] = $html_form['msg'] ;  // 成功
               // 更新退款成功
               // 更新成退款成功的状态信息
			    $formvars = array();
                $formvars['app_id'] = Yf_Registry::get('shop_app_id');
				$formvars['order_id']  =  $order_id ;
				$formvars['returncash']  =  $return_cash ;
                $key = Yf_Registry::get('shop_api_key');
                $url = Yf_Registry::get('paycenter_api_url');
                $ureses  = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=updateVEUorderCsh&typ=json', $url), $formvars);
            }else{
			   $res["msg"] = $html_form['msg']; 
			}
        }

        return  $res;
	}

/**
 * 退款成功，更新合并订的状态信息
 * 
 * @dateTime  2020-05-21
 * @author fzh
 * @link      https://github.com/mustify
 * @copyright https://www.yuanfeng.cn
 * @license   仅限本公司授权用户使用。
 * @version   3.8.1
 */
 public  function updateUorderStatus($orders){
	    $Order_BaseModel = new Order_BaseModel();
        $uorder = $orders["uorder"] ;
		$orderlist = explode(",", $uorder["inorder"]) ;
		$ifup = "1" ; // 默认需要更新  1     2 不需要更新
		foreach($orderlist  as $k=>$v){
		   $where = array();
		   $where["order_id"] =$v  ;
		   $orderinfo = $Order_BaseModel -> getOneByWhere($where );
		   if($orderinfo["order_refund_amount"] > 0 || $orderinfo["order_return_num"] > 0){
		   }else{
		     $ifup  = "2" ;  // 有一个没有退款的，都不需要更新订单成退款的订单信息
		   }
		}
		if($ifup == "1"){
			$formvars = array();
			$formvars['app_id'] = Yf_Registry::get('shop_app_id');
			$formvars['union_order_id']  =  $uorder["union_order_id"]   ;
			$key = Yf_Registry::get('shop_api_key');
			$url = Yf_Registry::get('paycenter_api_url');
			$ureses  = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=updateVEUveststus&typ=json', $url), $formvars);
		}
 }



    /**
     * 簽名
     * 
     * @author fzh
     * @link      https://github.com/mustify
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
    public function  signsyl($params=array(),$md5Key){
		ksort($params);
		$sign = '';
		foreach ($params as $v) {
		    $sign .= $v;
		}
		$sign = strtoupper(md5($sign . $md5Key));
		return $sign;
	}

    /**
     * 請求接口數據
     * 
     * @dateTime  2020-05-21
     * @author fzh
     * @link      https://github.com/mustify
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
	public function create_html($params, $action) {
	    $params = json_decode($params,true);
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
		$paramsdata =  http_build_query($params)  ;
        curl_setopt($curl, CURLOPT_POSTFIELDS, $paramsdata);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
   }

	/**
	 * 提现状态查询
	 * 
	 * @dateTime  2020-06-28
	 * @author fzh
	 * @copyright https://www.yuanfeng.cn
	 * @license   仅限本公司授权用户使用。
	 * @version   3.8.1
	 */
	public function  checkFlowsStatus($pm){
		$checkurl = $this->checkurl  ;
	    $yunshantixiankey  = $this->yunshantixiankey  ;
		$md5Key =   $yunshantixiankey  ; // 提现的秘钥信息
        $params = array();
        $params['transType'] = "query" ;//  交易类型 必填写
        $params['mchntNo'] =  $pm["mchntNo"]; // 交易请求时间   yyyyMMddHHmmss    必填写
        $params['cleardate'] = $pm["cleardate"] ;  // 交易类型  必须填写   必填写
        $params['banktrace']  = $pm["banktrace"] ; // 业务数据  json数据   必填写
		$params['orderno']  = $pm["orderno"] ; 
        $params['signType']  = "MD5" ;
		$mac = $this -> signsyl($params,$md5Key); // 签
		$params['mac']  = $mac;
        $params = json_encode($params);
        $html_form =$this-> create_html($params, $checkurl);
        $res = json_decode($html_form,true);
        return  $res;
	}
}

?>