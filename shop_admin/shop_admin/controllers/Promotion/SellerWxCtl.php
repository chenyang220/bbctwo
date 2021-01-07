<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */

class Promotion_SellerWxCtl extends AdminController
{
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
    }
	
	/**
	* 修改商家公众号，消息模板推送状态
	* @nsy 2020-03-31
	**/
	public function openWxtplmsg(){
		//id
		$id = request_int("id",0);
		//status
		$status = request_int("status",0);
		if(!$id){
			return  $this->data->addBody(-140, array(), '参数不能为空！', 250);
		}
		$time = time();
		//保存设置
		$sql = "INSERT INTO yf_seller_wxpublic_tplmsgstate 
				(shop_id,status,opt_time) 
				VALUES ({$id},{$status},{$time}) 
				ON DUPLICATE KEY UPDATE shop_id={$id},status={$status},opt_time={$time};";
		(new CommonModel())->sql->exec($sql);
		return $this->data->addBody(-140, array(), '操作成功', 200);
	}
}