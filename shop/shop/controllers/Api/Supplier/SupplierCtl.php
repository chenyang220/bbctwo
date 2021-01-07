<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author
 */
class Api_Supplier_SupplierCtl extends Api_Controller
{
    //申请
    public $shopDistributorModel =  null;
    public function apply()
    {
        $shop_id = request_string('shop_id');
        $shopGoodCatModel = new Shop_GoodCatModel();
        $shopBaseModel = new Shop_BaseModel();
        $this->shopDistributorModel = new Distribution_ShopDistributorModel();
        if(request_string('shop_distributor_id'))
        {
            $distributor_info = $this->shopDistributorModel->getOne(request_string('shop_distributor_id'));
            $distributor_cat = explode(',',$distributor_info['distributor_cat_ids']);
            $distributor_new_cat = explode(',',$distributor_info['distributor_new_cat_ids']);
            $distributor_cat_name = array();
            $distributor_new_cat_name = array();

            if($distributor_cat)
            {
                foreach($distributor_cat as $k=>$v)
                {
                    $temp = $shopGoodCatModel->getOneByWhere(array('shop_goods_cat_id'=>$v));
                    if($temp)
                    {
                        $distributor_cat_name[] = $temp['shop_goods_cat_name'];
                    }
                }

            }
            $distributor_cat_name = implode(',',$distributor_cat_name);
            if($distributor_new_cat)
            {
                foreach($distributor_new_cat as $k=>$v)
                {
                    $temp = $shopGoodCatModel->getOneByWhere(array('shop_goods_cat_id'=>$v));
                    if($temp)
                    {
                        $distributor_new_cat_name[] = $temp['shop_goods_cat_name'];
                    }
                }

            }
            $distributor_new_cat_name = implode(',',$distributor_new_cat_name);

            $shop_id = $distributor_info['shop_id'];
            $data['distributor_cat_name']=$distributor_cat_name;
            $data['distributor_new_cat_name']=$distributor_new_cat_name;
        }else{
            $data['distributor_cat_name']=[];
            $data['distributor_new_cat_name']=[];
        }
        $cat_row = array();
        $shop_cat=array();
        $shop_info = array();
        if($shop_id)
        {
            $shop_info = $shopBaseModel->getOneByWhere(array('shop_id'=>$shop_id));
            $cat_row['shop_id'] = $shop_id;
            $shop_cat  = $shopGoodCatModel->getGoodCatList($cat_row, array());
        }
        $data['shop_info'] = $shop_info;
        $data['shop_cat'] = $shop_cat;
        $this->data->addBody(-140, $data);
    }
    //添加
    public function addSupplier()
    {
        $shop_id = request_int('shop_id');
        $shop_cat = request_row('chk');
        $user_id = request_int('user_id');
        $shopGoodCatModel = new Shop_GoodCatModel();
        $this->shopDistributorModel = new Distribution_ShopDistributorModel();
        /* if(!empty($shop_cat))
       {
            foreach ($shop_cat as $key => $value)
           {
                $cat_info = $shopGoodCatModel->getOneByWhere(array('shop_goods_cat_id'=>$value));
                $cat_name [] = $cat_info['shop_goods_cat_name'];
            }
        } */
        $cat_ids = implode(',',$shop_cat);
        $shopBaseModel = new Shop_BaseModel();
        $shop_info = $shopBaseModel -> getOneByWhere(['user_id'=>$user_id]);
        if(empty($shop_info)){
            $status = 250;
            $msg = __('您的店铺信息有误');
            $data = array();
            return $this->data->addBody(-140,$data,$msg,$status);
        }
        if($shop_info['shop_type']==2){
            $status = 250;
            $msg = __('供货商不能申请分销');
            $data = array();
            return $this->data->addBody(-140, $data,$msg,$status);
        }
        $user_shop_id = $shop_info['id'];
        $supplier_row = array();
        $supplier_row['shop_id'] =  $shop_id;
        $supplier_row['distributor_id'] = $user_shop_id;
        $supplier_row['shop_distributor_time'] = get_date_time();
        if(request_string('act') && request_string('act') == "edit")
        {
            $supplier_row['distributor_new_cat_ids'] = $cat_ids;
            $shop_distributor_id = request_string('shop_distributor_id');
            $flag = $this->shopDistributorModel->editShopDistributor($shop_distributor_id,$supplier_row);
        }else
        {
            $supplier_row['distributor_enable'] = 0;
            $supplier_row['distributor_new_cat_ids'] = $cat_ids;
            $flag = $this->shopDistributorModel->addShopDistributor($supplier_row);
        }
        if($flag !== false)
        {
            $status = 200;
            $msg  = __('success');
        }else{
            $status = 250;
            $msg = __('failure');
        }
        $data = array();
        $this->data->addBody(-140, $data,$msg,$status);
    }
}