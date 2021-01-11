<?php

/**
 * Created by PhpStorm.
 * User: rd04
 * Date: 2017/11/14
 * Time: 15:00
 */
class Api_Promotion_PinTuanCtl extends Api_Controller
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
     * 获取拼团列表
     * @throws Exception
     */
    public function getPinTuanList()
    {
        $pintuanName = request_string('pintuan_name');
        $shopName = request_string('shop_name');
        $status = request_string('pintaun_status');

        $cond_row = array();

        if ($status != '-1') {
            $cond_row['status'] = $status;
        }

        if ($pintuanName) {
            $cond_row['name:LIKE'] = "%$pintuanName%";
        }
        
        if (request_string('shop_name')) {
            $shopModel = new Shop_BaseModel;
            $shopRows = $shopModel->getByWhere([
                'shop_name:LIKE'=> "%$shopName%"
            ]);

            if ($shopRows) {
                $shopIds = array_column($shopRows, 'shop_id');
                $cond_row['shop_id:IN'] = $shopIds;
            }
        }


        $pinTuanModel = new PinTuan_Base;
        $data = $pinTuanModel->getPinTuanList($cond_row);
        $this->data->addBody(-140, $data, 'success', 200);
    }

    /**
     * 获取套餐列表
     */
    public function getComboList()
    {
        $comboModel = new PinTuan_Combo;
        $data = $comboModel->listByWhere();
        $this->data->addBody(-140, $data, 'success', 200);
    }

    /**
     * 获取套餐详情
     */
    public function getDetail()
    {
        $detailModel = new PinTuan_Detail;
        $data = $detailModel->getByWhere([
            'pintuan_id'=> request_string('pintuan_id'),
        ]);

        if ($data) {
            $goodsModel = new Goods_BaseModel;
            $goodsIds = array_column($data, 'goods_id');
            $goodsRows = $goodsModel->getBase($goodsIds);
            foreach ($data as $k=> $item) {
                $data[$k]['goods_name'] = $goodsRows[$item['goods_id']]['goods_name'];
                $data[$k]['goods_stock'] = $goodsRows[$item['goods_id']]['goods_stock'];
            }
        }

        $data = array_values($data);
        $this->data->addBody(-140, $data, 'success', 200);
    }

    public function removePintuan()
    {
        $id = $_REQUEST['id'];

        $pinTuanModel = new PinTuan_Base;
        $flag = $pinTuanModel->removePinTuanActItem($id);

        if ($flag) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }

        $this->data->addBody(-140, [], $msg, $status);
    }

    public function gePinTuantGoodsList()
    {
        $PinTuan_Base = new PinTuan_Base();
        $page          	= request_int('page', 1);
        $rows          	= request_int('rows', 12);
        $name 	= trim(request_string('name'));   //活动名称
        $status	= request_int('status',1);		   //活动状态 1可用

        $Shop_BaseModel = new Shop_BaseModel();
        $shop_cond['shop_name:LIKE'] = '%' . trim(request_string('shop_name')) . '%';//店铺名称
        if($shop_cond) {
            $shop_list = $Shop_BaseModel->getByWhere($shop_cond);
            $shop_ids = array_column($shop_list,'shop_id');
        }
        if($shop_ids) {
            $cond_row['shop_id:IN'] = $shop_ids;
        }
        if ($status)
        {
            $cond_row['status'] = $status;
        }
        if ($name)
        {
            $cond_row['name:LIKE'] = '%' .$name . '%';
        }

        //拼团时间
        $cond_row['start_time:<'] = date('Y-m-d H:i:s');
        $cond_row['end_time:>'] = date('Y-m-d H:i:s');

        $data = $PinTuan_Base->getPinTuanGoods($cond_row, array('id' => 'DESC'), $page, $rows);

        $this->data->addBody(-140, $data,$cond_row);
    }
}