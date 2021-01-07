<?php

class JsdShareCtl extends Yf_AppController
{
    /*
     * 检测登录数据是否正确
     *
     *
     */
    public function getwiexinInfo()
    {
        $JSDK = new JSDK();
        $nowurl = request_string('nowurl');
        $difference_id = request_int('difference_id');
        $share_type = request_int('share_type');
        $uu_id = request_int('u');
        $Goods_BaseModel = new Goods_BaseModel();
        $Goods_CommonModel = new Goods_CommonModel();
        $Explore_BaseModel = new  Explore_BaseModel();
        $Shop_BaseModel = new Shop_BaseModel();
        $query = parse_url($nowurl,PHP_URL_QUERY);
        parse_str($query);
        if(isset($goods_id) && !empty($goods_id))
        {

            $goods_base = $Goods_BaseModel->getOne($goods_id);
            $common_id = $goods_base['common_id'];
            //获取商品详情信息
            $common_base = $Goods_CommonModel->getOne($common_id);
            $result['share_image'] = $common_base['common_image'];
            $result['share_title'] = $common_base['common_name'];
            $result['share_desc'] = $common_base['common_promotion_tips'];
            
        }else if($share_type == 2)
        {
            //获取发现详情信息
            $data = $Explore_BaseModel->getExploreDetail($difference_id);
            
            
            $result['share_image'] = $data['explore_images'][0]['images_url'];
            $result['share_title'] = $data['explore_base']['explore_title'];
            $result['share_desc'] = $data['explore_base']['explore_content'];
        }else if($share_type == 3)
        {
            //获取店铺详情信息
            $shop_base = $Shop_BaseModel->getOne($difference_id);
            
            $result['share_image'] = Web_ConfigModel::value('setting_logo');
            $result['share_title'] = $shop_base['shop_name'];
            $result['share_desc'] = $shop_base['shop_name'];
        }else{
            
            $result['share_image'] = Web_ConfigModel::value('setting_logo');
            $result['share_title'] = Web_ConfigModel::value('site_name');
            $result['share_desc'] = Web_ConfigModel::value('site_name');
            
        }

        $infos = $JSDK ->getSignPackage($nowurl);

        $conut = substr_count($nowurl, '?');
        if ($conut) {
            $nowurl = $nowurl . '&uu_id=' . $uu_id;
        }else{
            $nowurl = $nowurl . '?uu_id=' . $uu_id;
        }
        $data = array();
        $data["infos"] = $infos;
        $result['nowurl'] = $nowurl;
		$data['share_base'] = $result;
		$msg = "数据信息";
		$status = "200" ;
		return $this->data->addBody(-140, $data, $msg, $status);
		
		
    }

     
}

?>
