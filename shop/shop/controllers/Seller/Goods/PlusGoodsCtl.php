<?php
if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}
class Seller_Goods_PlusGoodsCtl extends Seller_Controller
{
    protected  $goodsPlusGoodsModel;
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->goodsPlusGoodsModel = new Plus_GoodsModel();
        $this->open_status = Web_ConfigModel::value('plus_switch')?:0;
        if(!$this->open_status)location_to('/index.php?ctl=Seller_Goods&met=online&typ=e');
    }

    /*
     * plus会员商品
     **/
    public function index()
    {
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        $cond_row['shop_id'] = Perm::$shopId;
        $common_name = request_string('common_name');
        if ($common_name) {
            $cond_row['common_name:LIKE'] = "%" . $common_name . "%";
        }
        $data = $this->goodsPlusGoodsModel->getPlusGoodsList($cond_row, array('create_time' => 'DESC'), $page, $rows);
        $data['items'] = $this->goodsPlusGoodsModel->reformPlusGoods($data['items']);
        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
        include $this->view->getView();
    }


    /*
     * 添加plus会员商品
     * */
    public function addPlusGoods(){
            include $this->view->getView();
    }


    /**
     * 保存PLUS会员商品
     */
    public function savePlusGoods(){
        $common_id_ids = request_row('common_id_ids'); //商品Common
        if(!$common_id_ids){
            $msg = '请选择商品!';
            $status = 250;
            return $this->data->addBody(-140, array(), $msg, $status);
        }
        $i = 0;
        foreach ($common_id_ids as $item){
            $price = $common_id = 0;
            $item  = explode('|',$item);
            $field_row = $common_row = array();
            list($common_id, $shop_id,$price) = $item;
            $shop_id = Perm::$shopId;
            $field_row['shop_id'] = $shop_id;
            $field_row['goods_common_id'] = $common_id;
            $field_row['create_time'] = get_time();
            $flag = $this->goodsPlusGoodsModel->insertPlusGoods($field_row, true);
            check_rs($flag, $rs_row);
            //修改商品表goods_common
            //$common_row['common_id'] = $common_id;
            $common_row['common_is_plus'] = Plus_Goods::COMMON_IS_PLUS_YES;
            $common_row['shop_id'] = $shop_id;
            $Goods_CommonModel = new Goods_CommonModel();
            $flag = $Goods_CommonModel->editCommon($common_id,$common_row);
            check_rs($flag, $rs_row);
        }
        $flag = is_ok($flag);
        if ($flag !== false){
            $msg = __('success');
            $status = 200;
        }else{
            $msg = __('failure');
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    /**
     * 获取店铺未参与分销的商品
     *
     * @access public
     */
    public function getGoodsListByShop()
    {
        $cond_row = array();

        //分页
        //$Yf_Page = new Yf_Page();
        //$Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 12;
        //$rows = $Yf_Page->listRows;
        //$offset = request_int('firstRow', 0);
        //$page = ceil_r($offset / $rows);

        //需要加入商品状态限定
        $cond_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
        $cond_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
        $cond_row['shop_status'] = Shop_BaseModel::SHOP_STATUS_OPEN;

        $cond_row['shop_id'] = Perm::$shopId;

        if (request_string('goods_typ') == 'virtual') {
          //  $cond_row['common_is_virtual'] = 1;//是否是虚拟商品
        } else {
          //  $cond_row['common_is_virtual'] = 0;
        }

        $goods_name = request_string('goods_name');
        if ($goods_name) {
            $cond_row['common_name:LIKE'] = "%" . $goods_name . "%";
        }
        $Goods_CommonModel = new Goods_CommonModel();
        //获取一键代发的商品
        $shop_id = Perm::$shopId;
        $supplier_end_list = $Goods_CommonModel->getSupplierSendCommonByShopId($shop_id);
        $supplier_end_common_id = is_array($supplier_end_list) ? array_column($supplier_end_list, 'common_id') : array();
        if ($supplier_end_common_id) {
            $cond_row['common_id:not IN'] = $supplier_end_common_id;
        }
        //排除活动商品common_id
        $Plus_GoodsModel = new Plus_GoodsModel();
        $active_common_id = $Plus_GoodsModel->getActiveCommonId();
        $goods_rows = $Goods_CommonModel->getCommonNormal($cond_row, array('common_id' => 'DESC'), 1, 5000);
        $data = $Goods_CommonModel->getRecommonRow($goods_rows);
        $newData = array();
        foreach ($data as $key => $val) {
            if (in_array($val['common_id'], $active_common_id)) {
                $data[$key]['is_join'] = 'true';
            } else {
                $newData[$key] = $val;
                $data[$key]['is_join'] = 'false';
            }
        }
        //$Yf_Page->totalRows = count($newData);
        //$page_nav = $Yf_Page->prompt();
        $rows = array();
        $data['items']= $newData;
        foreach ($data['items'] as $key => $value) {
            $rows[$value['common_id']] = $data['items'][$key];
            $rows[$value['common_id']]['id'] = $value['common_id'];
            $rows[$value['common_id']]['common'] = $value['common_id'];
            $rows[$value['common_id']]['price'] = format_money($value['common_price']);
            $rows[$value['common_id']]['image'] = $value['common_image'];
            $rows[$value['common_id']]['name'] = $value['common_name'];
            $rows[$value['common_id']]['goodsprice'] = $value['common_price'];
            $rows[$value['common_id']]['shopid'] = $value['shop_id'];
        }
        $rows = encode_json($rows);
        if ('json' == $this->typ){
            $this->data->addBody(-140, $data);
        }else{
            include $this->view->getView();
        }

    }

    /**
     *
     * 删除PLUS会会员商品
     */
    public function   delPlusGoods(){
        $id = request_row('id');
        $shop_id = Perm::$shopId;
        $rs_row = array();
        //先查询，是否合法再删除......
        foreach ((array)$id as $item){
            $ret = $this->goodsPlusGoodsModel->getOne($item);
            if(!$ret || ($ret['shop_id']!=$shop_id)) continue;
            $field_row = $common_row = array();
            $field_row['is_del'] = '1';
            $flag = $this->goodsPlusGoodsModel->editPlusGoods($item,$field_row);
            check_rs($flag, $rs_row);
            //修改商品表goods_common
            $common_row['common_is_plus'] = Plus_Goods::COMMON_IS_PLUS_NO;
            $Goods_CommonModel = new Goods_CommonModel();
            $flag = $Goods_CommonModel->editCommon($ret['goods_common_id'],$common_row);
            check_rs($flag, $rs_row);
        }
        $flag = is_ok($flag);
        if ($flag) {
            $msg = __('删除成功!');
            $status = 200;
        } else {
            $msg = __('删除失败！');
            $status = 250;
        }
        $data_re['id'] = $id;
        $this->data->addBody(-140, $data_re, $msg, $status);
    }

}

?>