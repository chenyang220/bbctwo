<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Api_Mb_CatImageCtl extends Api_Controller
{
    public $mbCatImageModel;

    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

        $this->mbCatImageModel = new Mb_CatImageModel();
    }

    public function catImageList()
    {
        $page                      = request_int('page', 1);
        $rows                      = request_int('rows', 100);

        $cat_image_list = $this->mbCatImageModel->getCatImageList($cond_row=array(), $order_row=array(), $page, $rows);

        //取出关联cat_name

        $data = array();

        if ( !empty($cat_image_list) )
        {
            $cat_ids = array_column($cat_image_list['items'], 'cat_id');
            $goodsCatModel = new Goods_CatModel();
            $cat_list = $goodsCatModel->getByWhere( array('cat_id:IN' => $cat_ids) );

            foreach ($cat_image_list['items'] as $key => $cat_img_data)
            {
                $cat_image_list['items'][$key]['cat_name'] = $cat_list[$cat_img_data['cat_id']]['cat_name'];
            }

            $data = $cat_image_list;
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }


    /**
     * 手机端模板
     * “广告条版块”只能添加一个
     */
    public function addCatImage()
    {
        $param                           = request_row('param');
        $rs = $this -> judgeCat($param['cat_id']);
        if($rs['status']){
            return $this->data->addBody(-140, $rs, $msg = $rs['msg'], $status = 250);
        }

        //判断cat_id是否已经增加
        $cat_img_data = $this->mbCatImageModel->getByWhere(array('cat_id'=>$param['cat_id']));
        if($cat_img_data)
        {
            return $this->data->addBody(-140, $rs, $msg = '该分类已经增加图片', $status = 250);
        }

        $insert_data['cat_id']          = $param['cat_id'];
        $insert_data['mb_cat_image']    = $param['mb_cat_image'];
        $insert_data['cat_adv_image']   = $param['cat_adv_image'];
        $insert_data['cat_adv_url']     = $param['cat_adv_url'];

        $mb_cat_image_id = $this->mbCatImageModel->addCatImage($insert_data, true);

        $data = array();

        if ($mb_cat_image_id)
        {
            $insert_data['mb_cat_image_id'] = $mb_cat_image_id;
            $data = $insert_data;
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function removeCatImage()
    {
        $mb_cat_image_id = request_int('mb_cat_image_id');
        $flag = $this->mbCatImageModel->removeCatImage($mb_cat_image_id);

        $data = array();
        if ($flag)
        {
            $data['mb_cat_image_id'] = $mb_cat_image_id;
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function editCatImage()
    {
        $param           = request_row('param');
        $mb_cat_image_id = $param['mb_cat_image_id'];

        //$update_data['cat_id']          = $param['cat_id'];
        $update_data['mb_cat_image']    = $param['mb_cat_image'];
        $update_data['cat_adv_image']   = $param['cat_adv_image'];
        $update_data['cat_adv_url']     = $param['cat_adv_url'];

        $flag = $this->mbCatImageModel->editCatImage($mb_cat_image_id, $update_data);

        $data = array();
        if ($flag !== false)
        {
            $update_data['mb_cat_image_id'] = $mb_cat_image_id;
            $data = $update_data;
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }

    /*
     * 判断添加的手机分类是否是符合要求的分类
     * */
    public function judgeCat($cat_id = 0)
    {
        $db = new YFSQL;
        $data = array();
        $sql = "select cat_parent_id from yf_goods_cat where cat_id in (select cat_parent_id from yf_goods_cat where cat_id = '".$cat_id."')";
        //$sql = "select cat_parent_id from yf_goods_cat where cat_id ='".$cat_id."' ";
        $rs = $db->find($sql);

        $sql2 = "select count(mb_cat_image_id) from yf_goods_cat where cat_id = '".$cat_id."'";
        $rs2 = $db->find($sql2);

        if(empty($rs) || $rs[0]['cat_parent_id'] == 0){
            $data['status'] = 1;
            $data['msg'] = __('请选择第三级分类!');
            return $data;
        }

        if( !empty($rs2)) {
            $data['data'] = $rs2;
            $data['status'] = 1;
            $data['msg'] = __('请选择未设置的分类!');
            return $data;
        }

        $data['status'] = 0;
        return $data;
    }

}
?>