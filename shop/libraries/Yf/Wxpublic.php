<?php
/**
 * 公众号操作类
 * @nsy 2020-03-23     
 */
class Yf_Wxpublic
{
    private $shop_id = 0; 
	private $token = null;
    public function __construct($shop_id){
		if($shop_id){
			$this->shop_id = $shop_id;
			$this->getToken();
		}
    }
	
	/**
	*
	* 获取token
	**/
	public function getToken(){
		$where = " shop_id = '{$this->shop_id}'";
		$sql ="select seller_wxpublic_id,shop_id,wechat_public_appid,wechat_public_secret,seller_wxpublic_status,wxpublic_access_token from yf_seller_wxpublic_list where {$where}";
		$common_model = new CommonModel();
		$result = $common_model->sql->getRow($sql);
		if(!$result){
			return false;
		}
		//token
        $access_token = $result['wxpublic_access_token'];
        $arr =array();
        if($access_token){
            $arr = explode("|",$access_token);
        }
        if(!$arr[0] || time()-$arr[1]>0){//token已过期
			$appid  = $result['wechat_public_appid'];
			$secret = $result['wechat_public_secret'];
			if(!$appid || !$secret){
				return array();
			}
            $data  = getAccToken($appid,$secret);
            if(!$data)return array();
            $wxpublic_access_token = $data['access_token']."|".((time()+$data['expires_in'])-300);
            $common_model->sql->exec("update yf_seller_wxpublic_list set wxpublic_access_token='{$wxpublic_access_token}' where seller_wxpublic_id = '{$result['seller_wxpublic_id']}'");
            $this->token = $data['access_token'];
        }else{
            $this->token =  $arr[0];
        }
	}

    /**
	 *
     * 发送模板消息
     */
    private function send($data){
		$result = array();
		if($this->token){
			$result = wxpublic_send($data,$this->token);
		}
		return $result;
    }
	
	/**
	*
	* 向微信公众号模板消息队列插入消息
	* @nsy 2020-03-24
	**/
	public static function addWxpublicTplMsg(Yf_Model $model,array $data){
		$sql = "INSERT INTO yf_seller_wxpublic_tplmessage " ;
		$sql .= "(shop_id,user_id,user_name,type,tpl_data,create_time)";
		$time = time();
		/*foreach($data as $items){
			$tpl_data = array(
				'first' => '恭喜您！购买的商品已支付成功，请留意物流信息哦！么么哒！~~',
				'keyword1' => $items['order_id'],
				'keyword2' => $items['goods_name'],
				'keyword3' => $items['order_money'],
				'keyword4' => $items['stauts'],,
				'keyword5' => $items['order_time'],,
				'remark' => '欢迎您的到来！'
			);
			$tpl = json_encode($tpl_data);
			$sql .= " VALUES('{$items['shop_id']}','{$items['buyer_user_id']}','{$items['buyer_user_name']}','1','{$tpl}','{$time}'); ";
		}*/
		foreach($data as $items){
			$tpl_data = array(
				'first'    => $items['first'],
				'keyword1' => $items['keyword1'],
				'keyword2' => $items['keyword2'],
				'keyword3' => $items['keyword3'],
				'keyword4' => $items['keyword4'],
				'keyword5' => $items['keyword5'],
				'remark'   => $items['remark']
			);
			//json 编码
			$tpl = decodeUnicode(json_encode($tpl_data));
			$sql .= " VALUES('{$items['shop_id']}','{$items['buyer_user_id']}','{$items['buyer_user_name']}','1','{$tpl}','{$time}'); ";
		}
		return $model->sql->exec($sql);
	}
	
	/**
	*
	* 获取用户绑定公众号对应的openid
	*
	**/
	public static function getWxOpenIdByUserId($user_id =0){
		$oid = 0;
		if(!$user_id){
			return $oid ;
		}
		$cache_group = 'Wxpublic';
        $Cache = Yf_Cache::create($cache_group);
        $cache_key = "getWxOpenIdByUserId_".$user_id;
        $cache_value = $Cache->get($cache_key);
		if(!$cache_value){
			$where = " bind_type=3 and user_id='{$user_id}'";
			$sql ="select bind_openid from ucenter_user_bind_connect where {$where} limit 1;";
			$common_model = new CommonModel();
			$cache_value = $common_model->sql->getRow($sql)['bind_openid'];
			$Cache->save($cache_value, $cache_key);
		}
		return  $cache_value;
	}
	
	/**
	*
	* 获取后台设置的模板消息推送状态
	**/
	public static function getWxPublixTplMsgStatus(){
		$cache_group = 'Wxpublic';
        $Cache = Yf_Cache::create($cache_group);
        $cache_key = "wxpublic_tpl_open_status";
        $cache_value = $Cache->get($cache_key);
		if(!$cache_value){
			$sql ="select config_value from yf_admin_web_config where config_key='{$cache_key}' limit 1;";
			$common_model = new CommonModel();
			$cache_value = $common_model->sql->getRow($sql)['config_value'];
			!$cache_value && $cache_value = 0;
			$Cache->save($cache_value, $cache_key);
		}
		return  $cache_value;
	}

   
}
?>