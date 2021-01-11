<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Seller_SellerWxListModel extends Seller_SellerWxList
{

	/**
	 * 读取分页列表
	 *
	 * @param  int $seller_wxpublic_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getSellerWxInfo($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		return $this->listByWhere($cond_row, $order_row, $page, $rows);
	}


	/**
     *
     * 获取公众号token
     */
    public  function getWxPublicAccessToken($shop_id){
    	$info = $this->getOneByWhere(array('shop_id'=>$shop_id));
        $appid              = $info['wechat_public_appid'];
        $secret             = $info['wechat_public_secret'];
        if(!$appid || !$secret){
            return array();
        }
        //wxpublic_access_token
        $wxpublic_access_token = $info['wxpublic_access_token'];
        $arr =array();
        if($wxpublic_access_token){
            $arr = explode("|",$wxpublic_access_token);
        }
        $token =array();
        if(!$arr[0] || time()-$arr[1]>0){//token不存在或者已过期
            $data  = getAccToken($appid,$secret);
            if(!$data)return array();
            $wxpublic_access_token = $data['access_token']."|".((time()+$data['expires_in'])-300);
            $this->editSellerWxList($info['seller_wxpublic_id'],array('wxpublic_access_token'=>$wxpublic_access_token));
            $token['token'] = $data['access_token'];
        }else{
            $token['token'] =  $arr[0];
        }
        return $token;
    }
}

?>