<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Api_Wx_CatImageCtl extends Api_Controller
{
    public $wxCatImageModel;

    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

        $this->wxCatImageModel = new Wx_CatImageModel();
    }

    public function catImageList()
    {
        $cat_image_list = $this->wxCatImageModel->getCatImageList();

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

        $insert_data['cat_id']          = $param['cat_id'];
        $insert_data['wx_cat_image']    = $param['wx_cat_image'];
        $insert_data['cat_adv_image']   = $param['cat_adv_image'];
        $insert_data['cat_adv_url']     = $param['cat_adv_url'];

        $wx_cat_image_id = $this->wxCatImageModel->addCatImage($insert_data, true);

        $data = array();

        if ($wx_cat_image_id)
        {
            $goodsCatModel = new Goods_CatModel();
            $cat_list = $goodsCatModel->getByWhere( array('cat_id:' => $insert_data['cat_id']) );
            $insert_data['cat_name'] = $cat_list[$insert_data['cat_id']]['cat_name'];
            $insert_data['wx_cat_image_id'] = $wx_cat_image_id;
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
        $wx_cat_image_id = request_int('wx_cat_image_id');
        $flag = $this->wxCatImageModel->removeCatImage($wx_cat_image_id);

        $data = array();
        if ($flag)
        {
            $data['wx_cat_image_id'] = $wx_cat_image_id;
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
        $wx_cat_image_id = $param['wx_cat_image_id'];

        $update_data['cat_id']          = $param['cat_id'];
        $update_data['wx_cat_image']    = $param['wx_cat_image'];
        $update_data['cat_adv_image']   = $param['cat_adv_image'];
        $update_data['cat_adv_url']     = $param['cat_adv_url'];

        $flag = $this->wxCatImageModel->editCatImage($wx_cat_image_id, $update_data);

        $data = array();
        if ($flag !== false)
        {
            $update_data['wx_cat_image_id'] = $wx_cat_image_id;
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

}
?>