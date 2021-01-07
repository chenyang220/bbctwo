<?php 
/**
 * 商品搜索
 * 一定是快速的搜索出来。
 */
class GoodSearch{
/*
 

 */

	static function pager($size = 12){

		// 分类ID
		
		// 商品关键词
		// 商品详情
		// 店铺关键词
		// googs_common 下要有产品的。
		// 是上架的
		// 地区
		// 促销商品
		// 显示有货
		// 
		// 评论数
		// 价格
		// 销量
		// common_is_virtual
		// common_price
		// 
		/*
		//获取商品信息及活动信息
		$goods_base = $this->getGoodsInfoAndPromotionById($goods_id);


		if($is_del == true){
            if($goods_base['is_del'] == Goods_BaseModel::IS_DEL_YES){
                return null;
            }
        }
        //获取商品Common信息
		$Goods_CommonModel = new Goods_CommonModel();

		//判断是否为代发货的分销商品
        $goods_common = $Goods_CommonModel->getSupplierCommon($goods_common);
        
		 */
		$where = "
				
				
				AND a.common_state = 1 # 上架的
				
				AND a.common_verify = 1 # 审核通过
		";

        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $size);

        switch (request_string('op2')) {
        	case 'active':
        		//  仅显示折扣商品
        		$where .= " 
        			# 仅显示折扣商品 
        			AND  a.common_is_xian != 0 
        			AND a.common_is_jia != 0
        			# 仅显示折扣商品  
        			";
        		break; 
        	default:
        		# code...
        		break;
        } 

        switch (request_string('op1')) {
        	case 'havestock':
        		// a.common_stock
        		// b.goods_stock
        		//
        		$where .= " AND  a.common_stock > 0 # 库存 > 0 ";
        		break;
        	
        	default:
        		# code...
        		break;
        }


        $sql = " 
        SELECT
			{#select#}
			FROM
				yf_goods_common a
				LEFT JOIN yf_goods_base b ON a.common_id = b.common_id 
			WHERE
				1 = 1   
				".$where."
				#AND  a.common_name Like '%男子%'
			GROUP BY
				a.common_id #同一个common 只显示一个。同时是对应下的goods要有库存
			#ORDER BY 
			 # a.shop_self_support desc
			";

			$select = "
			b.goods_id as good,
			a.common_price,
			b.goods_id,a.common_image,
			a.common_salenum,#销售数
			a.common_evaluate,#评论数
			a.shop_self_support,#是否是自营
				a.common_id,a.common_name,a.cat_id,a.cat_name
				,b.shop_id,b.shop_name
				";

		$sql = str_replace('{#select#}',$select,$sql);
		 

		$sql_count =  'select  count(*) num from ('.$sql.") a "; 
		//$sql_count = " SELECT FOUND_ROWS() ";
		$db = new YFSQL();
		 
		$data = $db->pager([
		 	'sql_count' => $sql_count,
		 	'sql' => $sql,
		 	 
		 	'size'=>$size
		]); 
		//print_r($data);

		$data['items'] = $data['data'];
		unset($data['data']);
		$data['recommend_row'] = self::tuiguang(); 
		 

		echo "<!-- \n   ";
		echo $sql."\n";
		echo $sql_count."\n"; 
		print_r($data);
		echo "\n-->\n";

		return $data;
	}

	static function tuiguang(){
			//分类id
            $cat_id = request_int('cat_id');
			//获取推广商品
            $Goods_RecommendModel = new Goods_RecommendModel();
            $recommond_cond_row = array();
            $recommond_order_row = array();
            //如果有查找的分类就显示该分类下的推广商品，如果没有传递分类就显示最新设置的分类推广
            if ($cat_id) {
                $recommond_cond_row['goods_cat_id'] = $cat_id;
            } else {
                $recommond_order_row['goods_recommend_id'] = 'DESC';
            }
            $sub_site_id  = self::site()['sub_site_id'];

            //如果有分站，查询分站
            if ($sub_site_id) {
                $recommond_cond_row['sub_site_id'] = $sub_site_id;
            }
            
           return  $Goods_RecommendModel->getRccommonGoodsInfo($recommond_cond_row, $recommond_order_row);

	}
	static $_shop_id;
	/**
	 * 当前用户的SHOP_ID
	 */
	static function shop_id(){
		if(!is_numeric(self::$_shop_id)){ 
			//查找当前用户是否是商品店铺的子账号
            $Seller_BaseModel = new Seller_BaseModel();
            $seller_row = array();
            $seller_row['user_id'] = Perm::$userId;
            $seller_info = $Seller_BaseModel -> getByWhere($seller_row);
            $seller_info = array_pop($seller_info);
            self::$_shop_id = 0;
            if ($seller_info) {
                self::$_shop_id = $seller_info['shop_id'];
            }
		}
			
        return self::$_shop_id;
	}

	static function site(){
		//pc分站
        if (isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
            $sub_site_id = $_COOKIE['sub_site_id'];
            $pc_site = true;
        }
        
        //wap分站
        if (request_string('ua') === 'wap') {
            $sub_site_id = request_int('sub_site_id');
            $wap_site = true;
            
        }
        return ['sub_site_id'=>$sub_site_id,'wap'=>$wap_site,'pc'=>$pc_site];
	}
}