<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

class Api_Shop_LabelCtl extends Api_Controller
{
    public function addLabelset ()
    {
        $label_name = trim(request_string("label_name"));
        $label_tag_sort = request_string("label_tag_sort");
        $label_logo = request_string("label_logo");
        $Label_BaseModel = new Label_BaseModel();
        $Label_Base = $Label_BaseModel->getOneByWhere(array("label_name"=>$label_name));

        if ($Label_Base) {
            return $this->data->addBody(-140, array(), "标签已存在请勿重复添加！", 250);
        }
 		$rows['label_name'] = $label_name;
		$rows['create_time'] = get_date_time();
        $rows['label_tag_sort'] = $label_tag_sort;
        $rows['label_logo'] = $label_logo;
        $flag = $Label_BaseModel->addLabelBase($rows);
        if ($flag) {
        	$msg = "添加标签成功";
        	$status = 200;
        } else {
        	$msg = "添加标签失败";
        	$status = 250;
        }
        
        $this->data->addBody(-140, array(), $msg, $status);
    }

    public function check() {
        $shop_id = request_int("shop_id");
        $Shop_BaseModel = new  Shop_BaseModel();
        $edit_Shop_Base = $Shop_BaseModel->editBase($shop_id,array("label_is_check"=>1));
        if ($edit_Shop_Base) {
            $msg = "店铺标签审核成功";
            $status = 200;
        } else {
            $msg = "店铺标签审核失败！";
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    public function getLabelList ()
    {
        $rows = request_string("rows");
        $page = request_string("page");
        $Label_BaseModel = new Label_BaseModel();
        $Label_Base = $Label_BaseModel->getLabelBaseList(array(),array(),$page,$rows);
        if ($Label_Base) {
            $msg = "标签查询成功";
            $status = 200;
        } else {
            $msg = "无标签";
            $status = 200;
        }
        
        $this->data->addBody(-140, $Label_Base, $msg, $status);
    }

    public function editLabelBase ()
    {
        $id = request_int("id");
        $label_name = trim(request_string("label_name"));
        $label_tag_sort = request_string("label_tag_sort");
        $label_logo = request_string("label_logo");
        $Label_BaseModel = new Label_BaseModel();
        $edit['label_name'] = $label_name;
        $edit['label_tag_sort'] = $label_tag_sort;
        $edit['label_logo'] = $label_logo;
        $Label_Base = $Label_BaseModel->editLabelBase($id,$edit);
        if ($Label_Base) {
            $msg = "标签修改成功";
            $status = 200;
        } else {
            $msg = "标签修改失败！";
            $status = 250;
        }
        
        $this->data->addBody(-140, array(), $msg, $status);
    }

    public function delShopLabel ()
    {
        $id = request_int("id");

        $Shop_BaseModel = new Shop_BaseModel();

        $Shop_Base = $Shop_BaseModel->getByWhere(array("label_id"=>$id));

        $Goods_CommonModel = new Goods_CommonModel();

        $Goods_Common = $Goods_CommonModel->getByWhere(array("label_id"=>$id));
        if ($Shop_Base || $Goods_Common) {
            return $this->data->addBody(-140, array(), "商家店铺和商品与此表签有关联，无法删除！", $status);
        }
        $Label_BaseModel = new Label_BaseModel();
        $Label_Base = $Label_BaseModel->removeLabelBase($id);
        if ($Label_Base) {
            $msg = "标签删除成功";
            $status = 200;
        } else {
            $msg = "标签删除失败！";
            $status = 250;
        }
        
        $this->data->addBody(-140, array(), $msg, $status);
    }
}

