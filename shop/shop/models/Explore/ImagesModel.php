<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Explore_ImagesModel extends Explore_Images
{
    //添加心得图片
    public function addExploreImages($images_url='',$type='',$poster_image='')
    {
        $add_row = array();
        $add_row['images_url'] = $images_url;
        $add_row['type'] = $type;
        $add_row['images_create_time'] = time();
        $add_row['poster_image'] = $poster_image;

        $data = $this->addImages($add_row,true);

        return $data;
    }

    //删除心得图片
    public function delExploreImages($images_id)
    {
        $rs_row = array();
        //1.删除绑定到该图片上的商品信息
        $Explore_ImagesGoodsModel = new Explore_ImagesGoodsModel();
        $id = $Explore_ImagesGoodsModel->getKeyByWhere(array('images_id'=>$images_id));

        //如果图片下有商品则删除图片下的商品
        if($id) {
            $del_flag = $Explore_ImagesGoodsModel->removeImagesGoods($id);
            check_rs($del_flag,$rs_row);
        }

        //2.删除图片
        $del_flag = $this->removeImages($images_id);
        check_rs($del_flag,$rs_row);

        return is_ok($rs_row);

    }

    //根据explore_id获取第一张图片信息
    public function getImageByExploreId($explore_id)
    {
        $image_list = [];
        foreach($explore_id as $k=>$v)
        {
            $image = [];
            $image = $this->getOneByWhere(array('explore_id' => $v), array('images_id' => 'ASC'));
            $image_list[$v] = $image['images_url'];
        }
        return array_values($image_list);
    }

    //根据explore_id获取第一张图片信息
    public function getImageId($image_id)
    {

        $image = $this->getOne(array('images_id' => $image_id));


        return $image;
    }

    public function getOneImageByExploreId($explore_id)
    {
        $image_list = [];
        foreach ($explore_id as $k => $v) {
            $image = [];
            $image = $this->getOneByWhere(array('explore_id' => $v), array('images_id' => 'ASC'));
            $image_list[$v]['images_url'] = $image['images_url'];
            $image_list[$v]['type'] = $image['type'];
        }
        return $image_list;
    }


}

?>