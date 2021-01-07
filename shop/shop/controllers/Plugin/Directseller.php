<?php
/**
 * 分销模块
 *
 *
 * @category   Framework
 * @package    Plugin
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2010远丰仁商
 * @version    1.0
 * @todo
 */
class Plugin_Directseller implements Yf_Plugin_Interface
{
    //解析函数的参数是pluginManager的引用
    public function __construct()
    {
        //注册这个插件
        //第一个参数是钩子的名称
        //第二个参数是pluginManager的引用
        //第三个是插件所执行的方法
        Yf_Plugin_Manager::getInstance()->register('rec_goods', $this, 'recGoods');
        Yf_Plugin_Manager::getInstance()->register('regDone', $this, 'regDone');
    }

    public static function desc()
    {
        return '分销员系统，使用分销员分佣时，请勿关闭！变更成功后请刷新浏览器';
    }

    public function recGoods()
    {

        $data = array();
        $rec = request_string('rec');

        if($rec)
        {
            $cond_row = array();
            $cond_row['shop_directseller_goods_common_code'] = $rec;
            $Distribution_ShopDirectsellerGoodsCommonModel = new Distribution_ShopDirectsellerGoodsCommonModel();
            $recImages = $Distribution_ShopDirectsellerGoodsCommonModel->getOne($cond_row);

            setcookie('yf_recserialize',$rec,time()+60*60*24*3);

            if(!empty($recImages['directseller_images_image']))
            {
                $data = explode(',',$recImages['directseller_images_image']);
            }
        }

        return $data;
    }

    /**
     * 注册完成后，判断是否需要建立分佣关系
     *
     * @return mixed
     */
    public function regDone($user_id)
    {
        $rec = $_COOKIE['yf_recserialize'];
        $User_InfoModel = new User_InfoModel();
        $DistributionShop= new Distribution_DistributionShop();
        if($rec) {
            $b= (strpos($rec,"u"));
            $e= (strpos($rec,"s"));
            $data['user_parent_id'] = substr($rec,$b+1,$e-1);
            //普通分销员升级判断
            $flag=$User_InfoModel->editBase($data['user_parent_id'],array('subordinate_num'=>1),true);
            if($flag){
                $subordinate=$User_InfoModel->getOne($data['user_parent_id']);
                if((int)$subordinate['subordinate_num']>=(int)Web_ConfigModel::value('distribution_invitations')){
                    $User_InfoModel->editInfo($data['user_parent_id'],array('distributor_type'=>1));
                    $time=time();
                    $images=Yf_Registry::get('shop_api_url').'shop/static/default/images/Bitmap.png';
                    $DistributionShop->addBase(array('user_id'=>$data['user_parent_id'],'distribution_name'=>$subordinate['user_name']."的小店",'distribution_logo'=>Yf_Registry::get('shop_api_url').'shop/static/default/images/Bitmap.png','add_time'=>time()));
                }
            }
        }
        if($_COOKIE['yf_recuserparentid']){
            $user_parent_id = $_COOKIE['yf_recuserparentid'];
        }elseif($_COOKIE['uu_id']){
            $user_parent_id = $_COOKIE['uu_id'];
        }
        if($user_parent_id) {
            $data['user_parent_id'] = $user_parent_id;
            //普通分销员升级判断
            $flag=$User_InfoModel->editBase($user_parent_id,array('subordinate_num'=>1),true);
            if($flag){
                $subordinate=$User_InfoModel->getOne($user_parent_id);
                if((int)$subordinate['subordinate_num']>=(int)Web_ConfigModel::value('distribution_invitations')){
                    $User_InfoModel->editInfo($user_parent_id,array('distributor_type'=>1));
                    $time=time();
                    $images=Yf_Registry::get('shop_api_url').'shop/static/default/images/Bitmap.png';
                    $DistributionShop->addBase(array('user_id'=>$user_parent_id,'distribution_name'=>$subordinate['user_name']."的小店",'distribution_logo'=>Yf_Registry::get('shop_api_url').'shop/static/default/images/Bitmap.png','add_time'=>time()));
                }
            }
        }



        /* $User_BaseModel = new User_BaseModel();
        $User_BaseModel->editBase($userid,$data); */

        
        $User_InfoModel->editInfo($user_id,$data);


        return true;
    }
}
?>