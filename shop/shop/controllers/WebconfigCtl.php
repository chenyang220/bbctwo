<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class WebconfigCtl extends Yf_AppController
{
	 


	 
	public function index()
	{ 
		$key = "'site_name','title','description','keyword',
                'category_title','category_description' , 'category_keyword', 
                'tg_title', 'tg_keyword', 'tg_description',
                'tg_title_content', 'tg_keyword_content', 'tg_description_content',
                'point_title', 'point_keyword', 'point_description',
                'point_title_content', 'point_keyword_content', 'point_description_content',
                'article_title', 'article_keyword', 'article_description',
                'article_title_content', 'article_keyword_content', 'article_description_content',
                'shop_title', 'shop_keyword', 'shop_description',
                'product_title', 'product_keyword', 'product_description'
                ";
		$sql = "
			SELECT *
			FROM `yf_web_config`
			where 
			config_key  in ($key)
		";
		$db = new YFSQL();
		$list = $db->find($sql);
		foreach($list as $v){
			$value = $v['config_value'];
			//替换其中的 {name}{sitename}
			$data[$v['config_key']] = $value;

		}
		 
		if ('json' == $this->typ)
		{
			$this->data->addBody(-140, $data);
		}
		 
	}

     public function findAuthor()
    {
        $Shop_BaseModel = new Shop_BaseModel();
        $shop_id_wap = request_int('shop_id_wap');
        $shop_id = request_int('shop_id');
        $mb = request_string('mb');
        $style = request_string('style');
        $sql = "
            SELECT *
            FROM `yf_web_config`
            where
            config_key  = 'exploreset'
        ";
        $db = new YFSQL();
        $list = $db->find($sql);
        if($shop_id_wap){
            $first_url =Yf_Registry::get('shop_wap_url')."tmpl/store.html?shop_id=$shop_id_wap&level=1";
        }else{
            $first_url = Yf_Registry::get('shop_wap_url').'index.html';
        }

        $setCat = Web_ConfigModel::value('setWapCat');
        if ($setCat != 2) {
            $cat_url = Yf_Registry::get('shop_wap_url') . 'tmpl/product_first_categroy.html';
        } else {
            $cat_url = Yf_Registry::get('shop_wap_url') . 'tmpl/product_first_categroy2.html';
        }
        if ($list[0]['config_value'] == 0) {
            $shop_base = $Shop_BaseModel->getOne($shop_id_wap);
            if($shop_id_wap){
                $footer_menu = [
                    '0'=>['type'=>'icon-home','type_active'=>'icon-home-active','name'=>'首页','url'=>$first_url],
                    '1'=>['type'=>'icon-kefu1','type_active'=>'icon-kefu1-active','name'=>'客服','url'=>Yf_Registry::get('shop_wap_url').'to_kefu=1&shop_name='.$shop_base['shop_name'].'&shop_logo='.$shop_base['shop_logo'].'&seller_name='.$shop_base['user_name']],
                    '2'=>['type'=>'icon-voucher','type_active'=>'icon-voucher-active','name'=>'订单','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/order_list.html'],
                    '3'=>['type'=>'icon-cart','type_active'=>'icon-cart-active','name'=>'购物车','url'=>Yf_Registry::get('shop_wap_url').'tmpl/cart_list.html'],
                    '4'=>['type'=>'icon-mine','type_active'=>'icon-mine-active','name'=>'我的','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/member.html'],
                ];
            }elseif ($mb == 'shop') {
                $footer_menu = [
                    '0'=>['type'=>'icon-home','type_active'=>'icon-home-active','name'=>'首页','url'=>"store" .  $style . ".html?shop_id=" .$shop_id],
                    '1'=>['type'=>'icon-class1','type_active'=>'icon-class1-active','name'=>'分类','url'=>"shop_goods_cat.html?shop_id=" . $shop_id . "&mb=shop&style=" . $style],
                    '2'=>['type'=>'icon-voucher','type_active'=>'icon-voucher-active','classs'=>'animation-up','onclick'=>'store_voucher()','name'=>'领券','url'=>'javascript:void(0)'],
                    '3'=>['type'=>'icon-kefu1','type_active'=>'icon-kefu1-active','name'=>'联系客服','onclick'=>'store_kefu()','url'=>'javascript:void(0)'],
                    '4'=>['type'=>'icon-mine','type_active'=>'icon-mine-active','name'=>'个人中心','url'=>'member/member.html'],
                ];
            }else{
                $footer_menu = [
                    '0'=>['type'=>'icon-home','type_active'=>'icon-home-active','name'=>'首页','url'=>$first_url],
                    '1'=>['type'=>'icon-class1','type_active'=>'icon-class1-active','name'=>'分类','url'=>$cat_url],
                    '2'=>['type'=>'icon-cart','type_active'=>'icon-cart-active','name'=>'购物车','url'=>Yf_Registry::get('shop_wap_url').'tmpl/cart_list.html'],
                    '3'=>['type'=>'icon-mine','type_active'=>'icon-mine-active','name'=>'我的','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/member.html'],
                ];
            }
            
        } else {
            //判断当前用户是否有未读信息
            $user_id = Perm::$userId;
            $message_sum = 0;
            if($user_id > 0) {
                $Explore_MessageModel = new Explore_MessageModel();
                $data = $Explore_MessageModel->getUnreadMeaasgeNum();
                $message_sum = $data['message_sum'];
            }
            if($shop_id_wap){
                $shop_base = $Shop_BaseModel->getOne($shop_id_wap);
                $footer_menu = [
                    '0'=>['type'=>'icon-home','type_active'=>'icon-home-active','name'=>'首页','url'=>$first_url],
                    '1'=>['type'=>'icon-class1','type_active'=>'icon-class1-active','name'=>'客服','url'=>Yf_Registry::get('shop_wap_url').'to_kefu=1&shop_name='.$shop_base['shop_name'].'&shop_logo='.$shop_base['shop_logo'].'&seller_name='.$shop_base['user_name']],
                    '2'=>['type'=>'icon-find','type_active'=>'icon-find-active','name'=>'订单','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/order_list.html'],
                    '3'=>['type'=>'icon-cart','type_active'=>'icon-cart-active','name'=>'购物车','url'=>Yf_Registry::get('shop_wap_url').'tmpl/cart_list.html'],
                    '4'=>['type'=>'icon-mine','type_active'=>'icon-mine-active','name'=>'我的','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/member.html'],
                ];
            }elseif ($mb == 'shop') {
                $footer_menu = [
                    '0'=>['type'=>'icon-home','type_active'=>'icon-home-active','name'=>'首页','url'=>"store" .  $style . ".html?shop_id=" .$shop_id],
                    '1'=>['type'=>'icon-class1','type_active'=>'icon-class1-active','name'=>'分类','url'=>"shop_goods_cat.html?shop_id=" . $shop_id . "&mb=shop&style=" . $style],
                    '2'=>['type'=>'icon-voucher','type_active'=>'icon-voucher-active','classs'=>'animation-up','onclick'=>'store_voucher()','name'=>'领券','url'=>'javascript:void(0)'],
                    '3'=>['type'=>'icon-kefu1','type_active'=>'icon-kefu1-active','name'=>'联系客服','onclick'=>'store_kefu()','url'=>'javascript:void(0)'],
                    '4'=>['type'=>'icon-mine','type_active'=>'icon-mine-active','name'=>'个人中心','url'=>'member/member.html'],
                ];
            }else{
                $footer_menu = [
                    '0'=>['type'=>'icon-home','type_active'=>'icon-home-active','name'=>'首页','url'=>$first_url],
                    '1'=>['type'=>'icon-class1','type_active'=>'icon-class1-active','name'=>'分类','url'=>$cat_url],
                    '2'=>['type'=>'icon-find','type_active'=>'icon-find-active','name'=>'发现','url'=>Yf_Registry::get('shop_wap_url').'tmpl/explore_list.html','sum'=>$message_sum],
                    '3'=>['type'=>'icon-cart','type_active'=>'icon-cart-active','name'=>'购物车','url'=>Yf_Registry::get('shop_wap_url').'tmpl/cart_list.html'],
                    '4'=>['type'=>'icon-mine','type_active'=>'icon-mine-active','name'=>'我的','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/member.html'],
                ];
            }

           

        }
        $data = $footer_menu;
        if ('json' == $this->typ)
        {
            $this->data->addBody(-140, $data);
        }
    }
}



