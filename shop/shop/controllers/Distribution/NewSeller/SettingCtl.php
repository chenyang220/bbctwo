<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Distribution_NewSeller_SettingCtl extends Seller_Controller
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
    }

    /**
     * 分销商品列表
     *
     * @access public
     */
    public function directsellerGoods(){
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $Goods_CommonModel = new Goods_CommonModel();
        $cond_row['shop_id'] = Perm::$shopId;
        $cond_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
        $cond_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
        $cond_row['common_is_directseller'] = 1;

        $common_name = request_string('common_name');
        if ($common_name) {
            $cond_row['common_name:LIKE'] = "%" . $common_name . "%";
        }

        $data = $Goods_CommonModel->getGoodsList($cond_row, array('common_id' => 'DESC'), $page, $rows);
        //print_r($data);
        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
        include $this->view->getView();
    }


    /**
     * 添加分销商品页面
     *
     * @access public
     */
    function addDirectsellerGoods()
    {
        include $this->view->getView();
    }

    /**
     * 新增分销商品
     *
     * @access public
     */
    function addGoods()
    {   
        $common_id_rows = request_row('join_act_common_id'); //商品Common
        if(request_float('common_c_first')&&request_float('common_c_second')&&request_float('common_a_first')&&request_float('common_a_second')){
            if ($common_id_rows) {
                $Goods_CommonModel = new Goods_CommonModel();
                foreach ($common_id_rows as $k => $v) {
                    $common_data['common_c_first'] = request_float('common_c_first'); //普通一级分佣比例
                    $common_data['common_c_second'] = request_float('common_c_second'); //普通二级分佣比例
                    $common_data['common_a_first'] = request_float('common_a_first'); //高级一级分佣比例
                    $common_data['common_a_second'] = request_float('common_a_second'); //高级二级分佣比例
                    $common_data['common_is_directseller'] = 1;
                    $common = $Goods_CommonModel->getOne($v);
                    $common_data['common_cps_commission'] = number_format((request_float('common_c_first') * $common['common_price'] / 100), 2, '.', '');
                    $flag = $Goods_CommonModel->editCommon($v, $common_data);
                }
            }
        }else{
             $msg = __('分佣比例不能为0');
             $status = 250;
             return $this->data->addBody(-140, array(), $msg, $status);
        }
        
        if ($flag !== false) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
       
    }

    //删除分销商品
    public function delGoods()
    {
        $id = request_row('id');
        $shop_id = Perm::$shopId;
        $rs_row = array();

        $Goods_CommonModel = new Goods_CommonModel();
        //判断商品是否是本店商品
        $data = $Goods_CommonModel->getByWhere(array(
            'common_id:in' => $id,
            'shop_id' => $shop_id
        ));
        $common_id_rows = array_values(array_column($data, 'common_id'));

        if ($common_id_rows) {
            foreach ($common_id_rows as $k => $v) {
                $common_data['common_is_directseller'] = 0;
                $common_data['common_c_first'] = 0; //普通一级分佣比例
                $common_data['common_c_second'] = 0; //普通二级分佣比例
                $common_data['common_a_first'] = 0; //高级一级分佣比例
                $common_data['common_a_second'] = 0; //高级二级分佣比例
                $common_data['common_cps_commission'] = 0;
                $edit_flag = $Goods_CommonModel->editCommon($v, $common_data);
                check_rs($edit_flag, $rs_row);
            }
        }

        $flag = is_ok($edit_flag);
        if ($flag) {
            $msg = __('删除分销商品成功');
            $status = 200;
        } else {
            $msg = __('删除失败！请重试');
            $status = 250;
        }
        $data_re['id'] = $id;
        $this->data->addBody(-140, $data_re, $msg, $status);
    }


    /**
     * 编辑分销商品分佣比例
     *
     * @access public
     */
    function editDirectsellerGoods()
    {
        $common_id = request_int('common_id');
        $Goods_CommonModel = new Goods_CommonModel();
        //二级分佣
        $common_data['common_c_first'] = request_float('c_first_rate'); //普通一级分佣比例
        $common_data['common_c_second'] = request_float('c_second_rate'); //普通二级分佣比例
        $common_data['common_a_first'] = request_float('a_first_rate'); //高级一级分佣比例
        $common_data['common_a_second'] = request_float('a_second_rate'); //高级二级分佣比例
        $common_data['common_is_directseller'] = 1;

        $common = $Goods_CommonModel->getOne($common_id);
        $common_data['common_cps_commission'] = number_format((request_float('common_c_first') * $common['common_price'] / 100), 2, '.', '');

        $flag = $Goods_CommonModel->editCommon($common_id, $common_data);

        if ($flag !== false) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, array(), $msg, $status);
    }

}

?>
