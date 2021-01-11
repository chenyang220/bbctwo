<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Explore_ImagesGoodsModel extends Explore_ImagesGoods
{

    /*
     * 心得详情页中获取心得商品信息
     * 默认展示5个，超过5个，下方出现：查看全部9（总数量）个商品
     */

    public function getGoodsSimple($explore_id = null)
    {
        $data = array();

        //1.根据explore_id查找images_id
        $img_sql = 'SELECT images_id FROM '.TABEL_PREFIX.'explore_images WHERE explore_id='.$explore_id;
        $images = $this -> sql -> getAll($img_sql);
        $images_id = array_column($images,'images_id');

        //2.根据images_id查找商品
        $goods_sum_sql = 'SELECT count(distinct a.goods_common_id)  sum FROM '.TABEL_PREFIX.'explore_images_goods a LEFT JOIN '.TABEL_PREFIX.'goods_common b ON a.goods_common_id = b.common_id LEFT JOIN  '.TABEL_PREFIX.'shop_base c ON b.shop_id = c.shop_id
         WHERE images_id IN('.implode(',',$images_id) .') AND goods_common_id!=0 AND b.common_state=1 AND b.common_verify=1 AND b.is_del=1 AND c.shop_status=3';

        $goods_sum = $this -> sql -> getAll($goods_sum_sql);
        $data['sum'] = $goods_sum[0]['sum'];

        $goods_sql = 'SELECT a.goods_common_id,b.common_name,b.common_price,b.common_image  
                      FROM '.TABEL_PREFIX.'explore_images_goods a  LEFT JOIN '.TABEL_PREFIX.'goods_common b ON a.goods_common_id = b.common_id LEFT JOIN '.TABEL_PREFIX.'shop_base c ON b.shop_id = c.shop_id
                      WHERE 1 AND a.images_id IN('.implode(',',$images_id) .') AND b.common_state=1 AND b.common_verify=1 AND b.is_del=1 AND c.shop_status=3 LIMIT 0,5';

        $goods = $this -> sql -> getAll($goods_sql);

        $data['goods'] = $goods;

        return $data;
    }

    /**
     * 根据心得图片id获取对应商品信息
     *
     * @access public
     */
    public function getGoodsByImagesId($images_id)
    {
        $images_goods = $this->getByWhere(array('images_id:IN'=> $images_id));
        $goods_common_id = array_column($images_goods, 'goods_common_id');
        
        //失效商品不显示
        $Goods_CommonModel = new Goods_CommonModel();
        $cond_common['common_id:IN'] = $goods_common_id;
        $cond_common['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
        $cond_common['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
        $cond_common['is_del'] = Goods_BaseModel::IS_DEL_NO;
        $goods_common = $Goods_CommonModel->getByWhere($cond_common);
        return array_values($goods_common);
    }
}

?>