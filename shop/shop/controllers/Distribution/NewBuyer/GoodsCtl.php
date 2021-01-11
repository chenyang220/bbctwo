<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     windfnn
 */
class Distribution_NewBuyer_GoodsCtl extends Buyer_Controller
{
    public $directseller_model = null;
    public $directseller_goodsModel = null;

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
     * 
     * 商品库
     * @access public
     */
    public function commodityLibrary(){
        $type=request_string("type");
        $user_id=Perm::$userId;
        $NewDistribution_ShopDirectsellerGoodsCommonModel= new NewDistribution_ShopDirectsellerGoodsCommonModel();
        $goods=$NewDistribution_ShopDirectsellerGoodsCommonModel->getByWhere(array('user_id'=>$user_id));
        $goods_ids = array_column($goods, 'goods_common_id');
        if($type=='pending' && $goods_ids){
            $cond_good_row['common_id:NOT IN'] = $goods_ids;
        }elseif($type=='distributed'){
            if (empty($goods_ids)) {
                $goods_ids = 0;
            }
            $cond_good_row['common_id:IN'] = $goods_ids;
        }
        $cond_good_row['common_is_directseller'] = 1;
        if (request_string('orderkey')) {
            $cond_good_row['common_name:LIKE'] = '%' . request_string('orderkey') . '%'; //商品名称搜索
        }

        $cond_good_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;  //正常上架商品
        $cond_good_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;  //审核通过

        

        $data = array();

        $Goods_CommonModel = new Goods_CommonModel();
        $data = $Goods_CommonModel->getCommonList($cond_good_row, $order_row);
        $data['type']=$type;


        $this->data->addBody(-140, $data);
    }


    /**
     * 
     * 添加分销店铺推荐商品
     * @access public
     */ 
     public function addRecommendGoods(){
        $user_id=Perm::$userId;
        $common_ids=request_string("cid");
        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $distribution_shop=$Distribution_DistributionShop->getOneByWhere(array("user_id"=>$user_id));
        $rs_row = array();
        if($distribution_shop){           
            $NewDistribution_ShopDirectsellerGoodsCommonModel= new NewDistribution_ShopDirectsellerGoodsCommonModel();
            foreach (json_decode($common_ids) as $value) {
                $goods_info=$NewDistribution_ShopDirectsellerGoodsCommonModel->getOneByWhere(array('distribution_shop_id'=>$distribution_shop['distribution_shop_id'],'goods_common_id'=>$value));
                $flag=$NewDistribution_ShopDirectsellerGoodsCommonModel->editShopDirectsellerGoodsCommon($goods_info['distribution_goods_id'],array('recommend'=>1));
                check_rs($flag, $rs_row);
            }
        }else{
            $msg = tips('440');
            $status = 440;
            $this->data->addBody(-140, array(), $msg, $status); 
        }
        $flag=is_ok($rs_row);
        if ($rs_row&&$flag) {
            $status = 200;
            $msg = tips('200');
        } else {
            $msg = tips('250');
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
     }

     /**
     * 
     * 添加分销商品到分销店铺
     * @access public
     */
     public function addDistributionGoods(){
        $user_id=Perm::$userId;
        $common_ids=request_string("cid");
        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $distribution_shop=$Distribution_DistributionShop->getOneByWhere(array("user_id"=>$user_id));
        $rs_row = array();
        if($distribution_shop){           
            $NewDistribution_ShopDirectsellerGoodsCommonModel= new NewDistribution_ShopDirectsellerGoodsCommonModel();
            $NewDistribution_common=$NewDistribution_ShopDirectsellerGoodsCommonModel->getByWhere(array('distribution_shop_id'=>$distribution_shop['distribution_shop_id']));
            if($NewDistribution_common){
                $in_common_ids=array_column($NewDistribution_common, 'goods_common_id');
            }else{
                $in_common_ids=array(); 
            }
            foreach (json_decode($common_ids) as $value) {
                $Goods_CommonModel= new Goods_CommonModel();
                $goods_info=$Goods_CommonModel->getOne($value);
                if(in_array($value, $in_common_ids)){
                    $flag=false;
                }else{
                    $flag=$NewDistribution_ShopDirectsellerGoodsCommonModel->addShopDirectsellerGoodsCommon(array('distribution_shop_id'=>$distribution_shop['distribution_shop_id'],'goods_common_id'=>$value,'user_id'=>$user_id,'shop_id'=>$goods_info['shop_id'],'add_time'=>time()));
                }
                check_rs($flag, $rs_row);
            }
        }else{
            $msg = tips('440');
            $status = 440;
            $this->data->addBody(-140, array(), $msg, $status); 
        }
        $flag=is_ok($rs_row);
        if ($rs_row&&$flag) {
            $status = 200;
            $msg = tips('200');
        } else {
            $msg = tips('250');
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
     }
     /**
     * 
     * 下架分销小店商品
     * @access public
     */  
     public function removeDistributionGoods(){
        $user_id=Perm::$userId;
        $common_ids=request_string("cid");
        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $distribution_shop=$Distribution_DistributionShop->getOneByWhere(array("user_id"=>$user_id));
        $rs_row = array();
        if($distribution_shop){
            $NewDistribution_ShopDirectsellerGoodsCommonModel= new NewDistribution_ShopDirectsellerGoodsCommonModel();
            foreach (json_decode($common_ids) as $value) {
                $info=$NewDistribution_ShopDirectsellerGoodsCommonModel->getOneByWhere(array('distribution_shop_id'=>$distribution_shop['distribution_shop_id'],'goods_common_id'=>$value));
                if($info){
                    $flag=$NewDistribution_ShopDirectsellerGoodsCommonModel->removeShopDirectsellerGoodsCommon($info['distribution_goods_id']);
                    check_rs($flag, $rs_row);
                }                
            }
        }else{
            $msg = tips('440');
            $status = 440;
            $this->data->addBody(-140, array(), $msg, $status); 
        }
        $flag=is_ok($rs_row);
        if ($rs_row&&$flag) {
            $status = 200;
            $msg = tips('200');
        } else {
            $msg = tips('250');
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
     }

    /**
     * 
     * 分销小店信息
     * @access public
     */
     public function getDistributionShopInfo(){
        $user_id=Perm::$userId;
        $data=array();
        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $data=$Distribution_DistributionShop->getOneByWhere(array("user_id"=>$user_id));
        if($data){
            $status = 200;
            $msg = tips('200');
        }else{
            $msg = tips('440');
            $status = 440;
        }
        $this->data->addBody(-140, $data, $msg, $status);
     }

     /**
     * 
     * 分销小店信息保存
     * @access public
     */
     public function saveDistributionShopInfo(){
        $distribution_name=request_string("distribution_name");
        $distribution_logo=request_string("distribution_logo");
        $distribution_desc=request_string("distribution_desc");
        $distribution_phone=request_string("distribution_phone");
        $distribution_template=request_int("distribution_template");       
        $user_id=Perm::$userId;
        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $list=$Distribution_DistributionShop->getOneByWhere(array("user_id"=>$user_id));
        if($list){
            $flag=$Distribution_DistributionShop->editBase($list['distribution_shop_id'],array('distribution_name'=>$distribution_name,'distribution_logo'=>$distribution_logo,'distribution_desc'=>$distribution_desc,'distribution_phone'=>$distribution_phone,'distribution_template'=>$distribution_template));
            if($flag){
                $status = 200;
                $msg = tips('200');
            }else{
                $status = 240;
                $msg = tips('240');
            }
        }else{
           $msg = tips('440');
           $status = 440; 
        }
        $this->data->addBody(-140, array(), $msg, $status);
     }

    /**
     * 
     * 分销小店分享展示
     * @access public
     */
     public function getDistributionShopDetail(){
        $distribution_shop_id = request_int("distribution_shop_id");
        $data=array();
        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $data=$Distribution_DistributionShop->getOne($distribution_shop_id);
        if($data){
            $status = 200;
            $msg = tips('200');
        }else{
            $msg = tips('440');
            $status = 440;
        }
        $this->data->addBody(-140, $data, $msg, $status);
     }

    /**
     * 
     * 分销小店商品数据展示
     * @access public
     */
     public function getDistributionShopIndex(){
        $user_id=Perm::$userId;
        $data=array();
        $NewDistribution_ShopDirectsellerGoodsCommonModel= new NewDistribution_ShopDirectsellerGoodsCommonModel();
        $recommend_list=$NewDistribution_ShopDirectsellerGoodsCommonModel->listByWhere(array('user_id'=>$user_id,'recommend'=>1), array(), 1, 6);
        if($recommend_list['items']){
            $goods_ids = array_column($recommend_list['items'], 'goods_common_id');
            if (empty($goods_ids)) {
                $goods_ids = 0;
            }
        }
        $Goods_CommonModel = new Goods_CommonModel();
         $cond_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
         $cond_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
         $cond_row['common_is_directseller'] = 1;
         $cond_row['common_id:IN'] = $goods_ids;
         $recommend_list = $Goods_CommonModel->getGoodsList($cond_row);
         $data['recommend'] = $recommend_list['items'];

        $hot=$NewDistribution_ShopDirectsellerGoodsCommonModel->listByWhere(array('user_id'=>$user_id), array(), 1, 6);
        $common_ids = array_column($hot['items'], 'goods_common_id');
        if (empty($common_ids)) {
            $common_ids = 0;
        }
         $cond['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
         $cond['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
         $cond['common_is_directseller'] = 1;
         $cond['common_id:IN'] = $common_ids;
         $hot_list = $Goods_CommonModel->getGoodsList($cond);
        $data['hot'] = $hot_list['items'];
        $data['userId'] = $user_id;
        $this->data->addBody(-140, $data);
     }

    /**
     * 
     * 分销小店商品数据展示2
     * @access public
     */
    public function getDistributionShopList(){
        $distribution_shop_id = request_int("distribution_shop_id");
        $data=array();
        $NewDistribution_ShopDirectsellerGoodsCommonModel= new NewDistribution_ShopDirectsellerGoodsCommonModel();
        $recommend_list=$NewDistribution_ShopDirectsellerGoodsCommonModel->listByWhere(array('distribution_shop_id'=>$distribution_shop_id,'recommend'=>1), array(), 1, 6);
        if($recommend_list['items']){
            $goods_ids = array_column($recommend_list['items'], 'goods_common_id');
            if (empty($goods_ids)) {
                $goods_ids = 0;
            }
        }
        $Goods_CommonModel = new Goods_CommonModel();
        $recommend_list=$Goods_CommonModel->getGoodsList(array('common_id:IN'=>$goods_ids));
        $data['recommend'] = $recommend_list['items'];

        $hot=$NewDistribution_ShopDirectsellerGoodsCommonModel->listByWhere(array('distribution_shop_id'=>$distribution_shop_id), array(), 1, 6);
        $common_ids = array_column($hot['items'], 'goods_common_id');
        if (empty($common_ids)) {
            $common_ids = 0;
        }
        $hot_list = $Goods_CommonModel->getGoodsList(array('common_id:IN'=>$common_ids));
        $data['hot'] = $hot_list['items'];
        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $list=$Distribution_DistributionShop->getOne($distribution_shop_id);
        $data['userId'] = $list['user_id'];
        $this->data->addBody(-140, $data);
     }

    /**
     * 
     * 分销员身份判断
     * @access public
     */
    public function checkUserDistributionType(){
        $user_id = Perm::$userId;
        $User_InfoModel = new User_InfoModel();
        $info = $User_InfoModel->getOne($user_id);
        $data['type'] = $info['distributor_type'];
        $data['num'] = Web_ConfigModel::value('distribution_invitations');

        //判断是否已经加入分销店铺
        $common_id = request_int('common_id');

        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $distribution_shop = $Distribution_DistributionShop->getOneByWhere(array("user_id" => $user_id));

        $Goods_CommonModel = new Goods_CommonModel();
        $goods_info = $Goods_CommonModel->getOne($common_id);

        $NewDistribution_ShopDirectsellerGoodsCommon = new NewDistribution_ShopDirectsellerGoodsCommon();
        $row = array(
            'distribution_shop_id' => $distribution_shop['distribution_shop_id'],
            'goods_common_id' => $common_id,
            'user_id' => $user_id,
            'shop_id' => $goods_info['shop_id']
        );
        $info = $NewDistribution_ShopDirectsellerGoodsCommon->getByWhere($row);

        if($info){
            $data['is_directseller_goods'] = 1;
        }else{
            $data['is_directseller_goods'] = 0;
        }
        $this->data->addBody(-140, $data); 
    }

    /**
     * 
     * 分销小店分享二维码
     * @access public
     */
    public function shareDistribution(){
        $user_id = Perm::$userId?Perm::$userId:request_int('uuid');
        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $data=$Distribution_DistributionShop->getOneByWhere(array("user_id"=>$user_id));
        $url = Yf_Registry::get('shop_wap_url').'tmpl/member/distribution_shop_detail.html?sid='.$data['distribution_shop_id'];
        $qrCode = Yf_Registry::get('base_url').'/shop/api/qrcode.php?data='.$url;
        include  LIB_PATH.'/phpqrcode/qrlib.php'; 
        $pngname = $user_id.".png";
        $path = ROOT_PATH.'/image/qrcode';
        $file_name = $path.'/'.$pngname;
        if (!is_dir($path)) {
            mkdir($path,0777,true);
        }
        if(!is_file($file_name)){
            QRcode::png($url, $file_name); 
        }
        $data['qrCode'] = Yf_Registry::get('base_url').'/image/qrcode/'.$pngname;
        $this->data->addBody(-140, $data, 'success', '200'); 
    }

     /**
     * 
     * 分销小店提现余额显示
     * @access public
     */
     public function getUserCommission(){
        $userId = Perm::$userId;
        $User_InfoModel = new User_InfoModel();
        $data = $User_InfoModel->getOne($userId);
        $this->data->addBody(-140, $data, 'success', '200');
     }

     /**
     * 
     * 分销小店提现余额
     * @access public
     */
     public function withdraw(){
        $userId = Perm::$userId;
        $User_InfoModel = new User_InfoModel();
        $Distribution_WithdrawLog=new Distribution_WithdrawLog();
        $data = $User_InfoModel->getOne($userId);
        $withdraw= request_float("amount");
        if($withdraw>$data['user_directseller_commission']){
            $this->data->addBody(-140, $data, '提现金额不能高于余额', '250');
        }
        //开启事物
        $User_InfoModel->sql->startTransactionDb();
        $commission = $data['user_directseller_commission']-$withdraw;
        $flag = $User_InfoModel->editInfo($userId,array('user_directseller_commission'=>$commission));
        //将需要确认的订单号远程发送给Paycenter修改订单状态
        //远程修改paycenter中的订单状态
        $key      = Yf_Registry::get('paycenter_api_key');
        $url         = Yf_Registry::get('paycenter_api_url');
        $paycenter_app_id = Yf_Registry::get('paycenter_app_id');
        $formvars = array();

        $formvars['order_id']    = 'TX-'. date('Ymd-His', time());
        $formvars['user_id'] = $userId;
        $formvars['user_money'] = $withdraw;
        $formvars['reason'] = '佣金提现';
        $formvars['app_id']        = $paycenter_app_id;
        $formvars['type']       = 'row';

        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=directsellerOrder&typ=json', $url), $formvars);
        $condition['withdraw_amount']=$withdraw;
        $condition['user_id']=$userId;
        $condition['withdraw_time']=date('Y-m-d H:i:s', time());
        if($flag && $User_InfoModel->sql->commitDb()){
            $status = 200;
            $msg    = __('success');
            $condition['withdraw_status']=1;
            $Distribution_WithdrawLog->addBase($condition);
        }else{
            $User_InfoModel->sql->rollBackDb();
            $m      = $User_InfoModel->msg->getMessages();
            $msg    = $m ? $m[0] : __('failure');
            $status = 250;
            $condition['withdraw_status']=2;
            $Distribution_WithdrawLog->addBase($condition);
        }
        $this->data->addBody(-140, array(), $msg, $status);
     }


     /**
     * 
     * 分销小店提现日志
     * @access public
     */
     public function getWithdrawLog(){
        $userId = Perm::$userId;
        $Distribution_WithdrawLog= new Distribution_WithdrawLog();
        $data=$Distribution_WithdrawLog->getByWhere(array('user_id'=>$userId));
        $this->data->addBody(-140, array_values($data));
     }

    /**
     * 
     * 礼包商品数据
     * @access public
     */
    public function getPackageGoods()
    {
        $page = request_int('page', 1);
        $cond_row['cat_id'] = 9002;
        $Goods_CommonModel = new Goods_CommonModel();
        $data = $Goods_CommonModel->getGoodsList($cond_row, array(), $page, 10);
        $data['gprice'] = Web_ConfigModel::value('distribution_gprice');
        if ($data['items']) {
            $status = 200;
            $msg = __('success');
            $data['goods'] = array_values($data['items']);
        } else {
            $status = 250;
            $msg = __('failure');
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 
     * 更多商品列表
     * @access public
     */ 
    public function getMoreGoods(){
       $status = request_string("status"); 
       $distribution_shop_id = request_int("distribution_shop_id");
       $data =array();
       $goods_ids = 0;
       $NewDistribution_ShopDirectsellerGoodsCommonModel= new NewDistribution_ShopDirectsellerGoodsCommonModel();
       if($status=="hot"){
            $goods_list=$NewDistribution_ShopDirectsellerGoodsCommonModel->getByWhere(array('distribution_shop_id'=>$distribution_shop_id));
            $goods_ids = array_column($goods_list, 'goods_common_id');
       }elseif($status=="recommend"){
            $goods_list=$NewDistribution_ShopDirectsellerGoodsCommonModel->getByWhere(array('distribution_shop_id'=>$distribution_shop_id,'recommend'=>1));
            $goods_ids = array_column($goods_list, 'goods_common_id');
       }
       $Goods_CommonModel = new Goods_CommonModel();
       $goods_info=$Goods_CommonModel->getByWhere(array('common_id:IN'=>$goods_ids));
       if ($goods_info) {
            $data['goods'] = array_values($goods_info);
            $data['userId'] = Perm::$userId;
        }
        $this->data->addBody(-140, $data); 
    }

    /**
     * PC首页
     *
     * @access public
     */
    public function index()
    {
        $user_id=Perm::$userId;
        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $shop=$Distribution_DistributionShop->getOneByWhere(array('user_id'=>$user_id));
        $NewDistribution_ShopDirectsellerGoodsCommonModel = new NewDistribution_ShopDirectsellerGoodsCommonModel();
        $common_list=$NewDistribution_ShopDirectsellerGoodsCommonModel->getByWhere(array("user_id"=>$user_id));
        $common_ids = array_column($common_list, 'goods_common_id');
        $cond_good_row['common_is_directseller'] = 1;
        $cond_good_row['common_id:IN']=$common_ids;
        if (request_string('keywords')) {
            $cond_good_row['common_name:LIKE'] = '%' . request_string('keywords') . '%'; //商品名称搜索
        }

        $cond_good_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;  //正常上架商品
        $cond_good_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;  //审核通过

        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $act = request_string('act');
        $actorder = request_string('actorder', 'DESC');

        if ($act !== '') {
            //销量
            if ($act == 'sales') {
                $order_row['common_salenum'] = $actorder;
            }
            //佣金排序
            if ($act == 'commission') {
                if (request_string('actorder')) {
                    $order_row['common_cps_commission'] = $actorder;
                } else {
                    $order_row['common_cps_commission'] = 'ASC';
                }
            }
            //时间排序
            if ($act == 'uptime') {
                $order_row['common_add_time'] = $actorder;
            }
        } else {
            $order_row['common_id'] = 'DESC';
        }

        //获取推广商品
        $data = array();
        $Goods_CommonModel = new Goods_CommonModel();
        $data = $Goods_CommonModel->getCommonList($cond_good_row, $order_row, $page, $rows);
        $data['user_id'] = Perm::$userId;

        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
        if ($this->typ == "json") {
            $this->data->addBody(-140, $data);
        } else {
            include $this->view->getView();
        }
    }


    /**
     * pc编辑分销店铺名称
     *
     * @access public
     */
    public function setShopName(){
        $distribution_name=request_string('user_directseller_shop');
        $user_id=Perm::$userId;
        $Distribution_DistributionShop = new Distribution_DistributionShop();
        $list=$Distribution_DistributionShop->getOneByWhere(array("user_id"=>$user_id));
        if($list){
            $flag=$Distribution_DistributionShop->editBase($list['distribution_shop_id'],array('distribution_name'=>$distribution_name));
            if($flag){
                $status = 200;
                $msg = tips('200');
            }else{
                $status = 240;
                $msg = tips('240');
            }
        }else{
           $msg = tips('440');
           $status = 440; 
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }
}

?>
