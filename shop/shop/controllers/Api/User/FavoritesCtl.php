<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Api_User_FavoritesCtl extends Yf_AppController
{
	/**
	 * Constructor
	 *
	 * @param  string $ctl 控制器目录
	 * @param  string $met 控制器方法
	 * @param  string $typ 返回数据类型
	 * @access public
	 */
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);

        $this -> userFavoritesGoodsModel = new User_FavoritesGoodsModel();
        $this -> goodsBaseModel = new Goods_BaseModel();
        $this -> goodsCommonModel = new Goods_CommonModel();
        $this -> shopBaseModel = new Shop_BaseModel();
        $this -> goodsCatModel = new Goods_CatModel();
        $this -> userFavoritesShopModel = new User_FavoritesShopModel();
        $this -> userFootprintModel = new User_FootprintModel();
	}

    /**
     *收藏商品信息
     *
     * @access public
     */
    public function favoritesGoods()
    {
        $Yf_Page = new Yf_Page();
        $Yf_Page -> listRows = request_int('listRows') ? request_int('listRows') : 12;
        $rows = $Yf_Page -> listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        if($_REQUEST['curpage'])
        {
            $page = (int)$_REQUEST['curpage'];
        }

        $page_nav = '';
        $user_id = request_int('user_id');
        $cond_row['user_id'] = $user_id;
        $data = $this -> userFavoritesGoodsModel -> getFavoritesGoodsDetail($cond_row, array('favorites_goods_time' => 'DESC'), $page, $rows);
        if ($data) {
            //判断是否是虚拟商品或分销商品
            foreach ($data['items'] as $key => $val) {
                //获取common_id
                $goods_base = $this -> goodsBaseModel -> getOne($val['goods_id']);
                //获取common信息
                $goods_common = $this -> goodsCommonModel -> getOne($goods_base['common_id']);
                // $val['goods_image'] = $goods_common['common_image'];
                if ($goods_common['common_is_virtual'] || $goods_common['product_distributor_flag']) {
                    $data['items'][$key]['is_virtual'] = true;
                } else {
                    $data['items'][$key]['is_virtual'] = false;
                }
                // 把goods_common表的common_image值赋值给$data['items'][$key]['detail']['goods_image'];
                $data['items'][$key]['detail']['goods_image'] = $goods_common['common_image'];
            }
            $Yf_Page -> totalRows = $data['totalsize'];
            $page_nav = $Yf_Page -> prompt();

            $data['hasmore'] = $page >= $data['total'] ? false : true;
        }

        return $this -> data -> addBody(-140, $data);
    }

    /**
     *收藏店铺信息
     *
     * @access public
     */
    public function favoritesShop()
    {
        $Yf_Page = new Yf_Page();
        $Yf_Page -> listRows = request_int('listRows') ? request_int('listRows') : 10;
        $rows = $Yf_Page -> listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        if($_REQUEST['curpage'])
        {
            $page = (int)$_REQUEST['curpage'];
        }

        $user_id = request_int('user_id');
        $cond_row['user_id'] = $user_id;
        $data = $this -> userFavoritesShopModel -> getFavoritesShops($cond_row, array('favorites_shop_time' => 'DESC'), $page, $rows);
        if ($data['items']) {
            foreach ($data['items'] as $k => $v) {
                if ($v['shop_logo']) {
                    $data['items'][$k]['shop_logo'] = $v['shop_logo'];
                } else {
                    $data['items'][$k]['shop_logo'] = Web_ConfigModel::value('photo_shop_head_logo');
                }
                if($v['wap_shop_logo']){
                    $data['items'][$k]['wap_shop_logo'] = $v['wap_shop_logo'];
                }else{
                    $data['items'][$k]['wap_shop_logo'] = Web_ConfigModel::value('photo_shop_head_logo_wap');
                }
            }
        }
        $Yf_Page -> totalRows = $data['totalsize'];
        $page_nav = $Yf_Page -> prompt();

        $data['hasmore'] = $page >= $data['total'] ? false : true;

        $this -> data -> addBody(-140, $data);
    }
}

?>