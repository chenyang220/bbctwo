<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * 导入店铺分类下的商品
 * 
 * @author fzh  2019-02-26
 * @copyright [上海远丰集团科技有限公司]
 * @license   [远丰集团]
 * @version   []
 */
class Seller_Goods_TBImportCtl extends Seller_Controller
{
    public $shopBaseModel;
    public $goodsTypeModel;
    public $goodsCatModel;
    public $goodsBrandModel;
    public $goodsSpecModel;
    public $goodsPropertyValueModel;
    public $goodsSpecValueModel;
    public $goodsCommonModel;
    public $goodsBaseModel;
    public $goodsCommonDetailModel;
    public $shopGoodsCat;
    public $areaModel;
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
        $this ->shopBaseModel = new Shop_BaseModel();
        $this ->goodsTypeModel = new Goods_TypeModel();
        $this ->goodsCatModel = new Goods_CatModel();
        $this ->goodsBrandModel = new Goods_BrandModel();
        $this ->goodsSpecModel = new Goods_SpecModel();
        $this ->goodsPropertyValueModel = new Goods_PropertyValueModel();
        $this ->goodsSpecValueModel = new Goods_SpecValueModel();
        $this ->goodsCommonModel = new Goods_CommonModel();
        $this ->goodsBaseModel = new Goods_BaseModel();
        $this ->goodsCommonDetailModel = new Goods_CommonDetailModel();
        $this ->shopGoodsCat = new Shop_GoodsCat();
        $this ->areaModel = new Transport_AreaModel();
    }

    public function importFile()
    {
        $shopGoodsCatModel = new Shop_GoodsCatModel();
        $shop_goods_cat_rows = $shopGoodsCatModel->getByWhere( array('shop_id'=> Perm::$shopId) );
        include $this->view->getView();
    }

    public function importImage()
    {   
        include $this->view->getView();
    }


    /**
     * @throws Exception
     * excel批量导入商品
     * @nsy 2019-08-01
     */
    public function addGoods()
    {
        $stime=microtime(true);//执行开始时间
        require_once LIB_PATH.'/vendor/Excel/reader.php';
        $file_path = request_string("file_path");//xls文件路径
        //商品共用字段
        $cat_id = request_int('goods_category_id'); //分类id
        $cat_name = request_string('goods_category_name'); //分类名
        $province_id = request_int('province_id'); //省id
        $city_id = request_int('city_id'); //市id
        $file_path = DATA_PATH."/upload".$file_path;
        if(!is_file($file_path)){
            return $this -> data -> addBody(-140, array(), __('文件不存在！'), 250);
        }
        $data = new Spreadsheet_Excel_Reader($file_path);
        if(!$data ){
            return $this -> data -> addBody(-140, array(), __('读取文件失败'), 250);
        }
        //店铺信息
        $shop_base = $this ->shopBaseModel-> getBase(Perm::$shopId);
        $shop_base = current($shop_base);
        $common_data['shop_status'] = $shop_base['shop_status'];  //插入店铺状态
        $goods_cat_base = $this ->goodsCatModel-> getCat($cat_id);
        $goods_cat_base = current($goods_cat_base);
        //sgcate_id
        //$sgcate_id = request_row('sgcate_id');
        $sgcate_id = request_row('store_goods_category');
        $shop_cat_id = '';
        if ($sgcate_id && is_array($sgcate_id)) {
            $shop_cat_id = ",".implode(",",$sgcate_id).",";
        }else{
            $shop_cat_id = ",".$sgcate_id.",";
        }
        //yf_goods_common 表的数据准备
        $common_data['shop_id'] = $shop_base['shop_id'];      //店铺id
        $common_data['shop_name'] = $shop_base['shop_name'];  //店铺名称
        $common_data['shop_cat_id'] = $shop_cat_id;           //店铺分类id
        $common_data['type_id'] = $goods_cat_base['type_id'];  //类型id
        $common_data['shop_self_support'] = $shop_base['shop_self_support'] == 'true' ? 1 : 0;     //是否自营
        $common_data['district_id'] = $shop_base['district_id']; //设置地区
        $common_data['cat_id'] = $cat_id;                   //商品分类id
        $common_data['cat_name'] = $cat_name;               //商品分类名称
        $common_data['common_goods_from'] = Goods_CommonModel::GOODS_FROM_OUTSIDEIMPORT; //添加类型
        //商品所在地
        $common_location = array();
        $province_id && $common_location[] = $province_id;
        $city_id &&  $common_location[] = $city_id;
        $common_location &&  $common_data['common_location'] = $common_location;
        //本店分类
        $sgcate_id &&  $common_data['shop_goods_cat_id'] = json_encode($sgcate_id);                 //shop_goods_cat_id
        //获取运费模板信息
        $shop_id = Perm::$shopId;
        $template_model = new Transport_TemplateModel();
        $transport_template = $template_model -> getOpenTemplate($shop_id);
        if (!$transport_template) {
             return $this -> data -> addBody(-140, array(), __('请设置运费模板'), 250);
        }
        /**
         * 验证商品编码和商品货号的唯一性
         * @var [type]
         */
        $common_goods_codes = $this ->goodsCommonModel-> sql->getAll("SELECT  DISTINCT common_code  from yf_goods_common where 1 and common_code!=''");
        $goods_codes_arr =array();
        foreach ($common_goods_codes as $it){
            $goods_codes_arr[] = $it['common_code'];
        }
        unset($common_goods_codes);
        $i=0;
        $msg = '';
        $return = array();
        //循环处理:大数组，可以考虑拆分
        $this ->goodsCommonModel->sql->startTransactionDb();
        foreach($data->sheets[0]['cells'] as $k => &$items){
            if($k==1 || $k==2){
                unset($items);
                continue;
            }
            $name               = $items[1];
            $promotion_tips     = $items[2];
            $price              = $items[3];
            $market_price       = $items[4];
            $cost_price         = $items[5];
            $code               = $items[6];
            $stock              = $items[7];
            $imagePath          = $items[8];
            $transport_area_id  = $items[9];
            $cubage             = $items[10];
            $state              = $items[11];
            $is_recommend       = $items[12];

            if (!$name) {
                $msg= __('商品名称必填');
            }
            $matche_row = array();
            //有违禁词
            if (Text_Filter::checkBanned($name, $matche_row) || Text_Filter::checkBanned($promotion_tips, $matche_row) || Text_Filter::checkBanned($body, $matche_row)) {
                $msg = __('含有违禁词');
            }
            if (!$price) {
                $msg = __('商品价格必填');
            }
            if (!$market_price) {
                $msg = __('市场价必填') ;
            }
            if ($price>$market_price) {
                $msg =  __('市场价不能低于商品价格');
            }
            if (!$stock) {
                $msg=  __('商品库存必填');
            }
            if (!$imagePath) {
                $msg =  __('商品图片必填');
            }
            //售卖区域
            if (!$transport_area_id) {
                $msg = __('请设置售卖区域');
            }
            $check_area = $this ->areaModel -> checkArea($transport_area_id, Perm::$shopId);
            if (!$check_area) {
                $msg = __('售卖区域数据有误');
            }
            if (!$cubage) {
                $msg =  __('商品重量(单位kg)必填');
            }
            //商品发布
            if (!$state) {
                $msg=  __('商品发布必填');
            }
            if (!in_array($state, array(0,1))) {
                $msg = __('发布类型错误');
            }
            //商品推荐
            if (!$is_recommend){
                $msg = __('商品推荐必填');
            }
            if (!in_array($is_recommend, array(1,2))) {
                $msg = __('商品推荐类型错误');
            }
            if(in_array($code,$goods_codes_arr,true)){
                $msg = __('商品货号'.$k.'不唯一');
            }
            if($msg){
                break;
            }
            $common_data['common_name'] = $name;     //商品名称
            $common_data['brand_id'] = 0;             //品牌id 默认没有品牌id
            $common_data['common_promotion_tips'] = $promotion_tips;        //商品广告词
            $common_data['common_image'] = $imagePath;                      //商品主图
            $common_data['common_price'] = $price;                          //商品价格
            $common_data['common_market_price'] = $market_price;
            $common_data['common_cost_price'] = $cost_price;                //成本价
            $common_data['common_stock'] = $stock;                            //商品库存
            $common_data['common_alarm'] = 0;                            //库存预警值
            $common_data['common_code'] = $code;                  //商家编号
            $common_data['common_cubage'] = $cubage;                     //商品重量
            $common_data['common_is_return'] = 1;                        // 默认7天无理由退货
            $common_data['common_state'] = $state;
            $common_data['common_is_recommend'] = $is_recommend;         //商品推荐
            $common_data['common_add_time'] = get_date_time();           //商品添加时间
            $common_data['common_edit_time'] = get_date_time();
            $common_data['transport_area_id'] = $transport_area_id;    //销售区域主键
            //0不限购
            $common_data['common_limit'] = 0;
            //判断商品是否需要审核
            $goods_verify_flag = Web_ConfigModel::value('goods_verify_flag');
            if ($goods_verify_flag == 1) {
              $common_data['common_verify'] = 10; //待审核
            }else{
              $common_data['common_verify'] = 1;  //通过
            }
            $common_data['common_invoices'] = 1; //商品默认支持开发票
            //默认是立即发布，则发布时间为当前添加时间
            $common_data['common_sell_time'] = $common_data['common_add_time'];
            $common_id = $this ->goodsCommonModel->addCommon($common_data, true);
            if ($common_id ) {
                //库存配置
                //判断  修改的只修改
                //取出已有的所有goods_id
                $goods_base = $this -> goodsBaseModel -> getByWhere(array('common_id' => $common_id));
                if (!empty($goods_base)) {
                    $goods_base_ids = array_column($goods_base, 'goods_id');
                }
                $goods_data['cat_id'] = $common_data['cat_id'];                    //商品分类id
                $goods_data['common_id'] = $common_id;                                //商品公共表id
                $goods_data['shop_id'] = $common_data['shop_id'];                    //shop_id
                $goods_data['shop_name'] = $common_data['shop_name'];                //shop_name
                $goods_data['goods_name'] = $common_data['common_name'];                //商品名称
                $goods_data['goods_promotion_tips'] = $common_data['common_promotion_tips'];    //促销提示
                $goods_data['goods_is_recommend'] = $common_data['common_is_recommend'];        //商品推荐
                $goods_data['goods_image'] = $common_data['common_image'];                //商品主图
                //加入goods_id 冗余数据
                $goods_ids = array();
                $goods_data['goods_price'] = $common_data['common_price'];                //商品价格
                $goods_data['goods_market_price'] = $common_data['common_market_price'];  //市场价
                $goods_data['goods_stock'] = $common_data['common_stock'];                //商品库存
                $goods_data['goods_alarm'] = $common_data['common_alarm'];                //库存预警值
                $goods_data['goods_code'] = $common_data['common_code'];                //商家编号货号
                $goods_id = $this ->goodsBaseModel -> addBase($goods_data, true);
                !$goods_id && $i++;
                $add_ids[] = $goods_id;
                if (empty($goods_ids)) {
                    $goods_ids[] = array(
                        'goods_id' => $goods_id,
                        'color' => 0
                    );
                }
                $edit_common_data['goods_id'] = $goods_ids;
                $this -> goodsCommonModel -> editCommon($common_id, $edit_common_data);
                $return['common_id'] = $common_id;
            } else {
               $i++;
            }
            unset($items);
            if($i){
                break;
            }
        }

        if($i||$msg){
            $status = 250;
            $title = '导入失败!'.$msg;
            $this -> goodsCommonModel -> sql -> rollBackDb();
        }else{
            $status = 200;
            $title = '导入成功！';
            $this ->goodsCommonModel->sql->commitDb();
        }
        @unlink($file_path);
        $etime=microtime(true);//获取程序执行结束的时间
        $total=$etime-$stime;  //计算差值
        $return['total_time'] = $total;
        $this -> data -> addBody(-140, $return, $title, $status);
    }

    /**
     *   excel 导入店铺商品
     *   导入字段如下：
     *
     *   name    商品名称
     *   promotion_tips 副标题
     *   price    商品价格
     *   market_price    市场价
     *   cost_price  成本价
     *   code 商品货号
     *   stock   商品库存
     *   imagePath   商品图片
     *   transport_area_id    售卖区域
     *   cubage  商品重量(单位kg)
     *   state   商品发布
     *   is_recommend     商品推荐
     *   code 商家货号
     *   导入商品 旧版本
     */
    public function addGoodsBase()
    {
        $file_path = request_string("file_path");
        $file_path = "./shop/data/upload$file_path";

        $csv_string = $this->unicodeToUtf8(file_get_contents($file_path));

        $handle = fopen($file_path, "w");
        fwrite($handle, $csv_string);
        fclose($handle);

        $reader_csv = new PHPExcel_Reader_CSV();
        $reader_csv->setDelimiter("\t")->setEnclosure("");
        $php_excel = $reader_csv->load($file_path);
        //excell 数据 数据包含保单头信息
        $sheet_data = $php_excel->getActiveSheet()->toArray(null,true,true,true);
        //弹出excel 1、2行的中文信息和字段名称
        array_shift($sheet_data);
        array_shift($sheet_data);


        //导入商品的共用字段
        $cat_id = request_int('goods_category_id'); //分类id
        $cat_name = request_string('goods_category_name'); //分类名
        $province_id = request_int('province_id'); //省id
        $city_id = request_int('city_id'); //市id

        //店铺信息
        $shop_base = $this ->shopBaseModel-> getBase(Perm::$shopId);
        $shop_base = current($shop_base);
        $common_data['shop_status'] = $shop_base['shop_status'];  //插入店铺状态
        $goods_cat_base = $this ->goodsCatModel-> getCat($cat_id);
        $goods_cat_base = current($goods_cat_base);
        $shop_cat_id = ',';
        $sgcate_id = request_row('sgcate_id');
        $sgcate_id = request_row('store_goods_category');

        if (empty($sgcate_id)) {
            $shop_cat_id .= ',';
        } else {
            foreach ($sgcate_id as $key => $val) {
                $shop_cat_id .= $val . ',';
            }
        }

        $common_id = ''; //添加商品
        $spec = array();

        //yf_goods_common 表的数据准备
        $common_data['shop_id'] = $shop_base['shop_id'];      //店铺id
        $common_data['shop_name'] = $shop_base['shop_name'];  //店铺名称
        $common_data['shop_cat_id'] = $shop_cat_id;           //店铺分类id
        $common_data['type_id'] = $goods_cat_base['type_id'];  //类型id
        $common_data['shop_self_support'] = $shop_base['shop_self_support'] == 'true' ? 1 : 0;     //是否自营
        $common_data['district_id'] = $shop_base['district_id']; //设置地区
        $common_data['cat_id'] = $cat_id;                   //商品分类id
        $common_data['cat_name'] = $cat_name;               //商品分类名称
        $common_data['common_goods_from'] = Goods_CommonModel::GOODS_FROM_OUTSIDEIMPORT; //添加类型
        //商品所在地
        if ($province_id) {
            $common_location = array();
            $common_location[] = $province_id;
            if ($city_id) {
                $common_location[] = $city_id;
            }
            $common_data['common_location'] = $common_location; //商品所在地
        }
        //本店分类
        if (!empty($sgcate_id)) {
            $common_data['shop_goods_cat_id'] = $sgcate_id;                 //shop_goods_cat_id
        }
        //获取运费模板信息
        $shop_id = Perm::$shopId;
        $template_model = new Transport_TemplateModel();
        $transport_template = $template_model -> getOpenTemplate($shop_id);
        if (!$transport_template) {
           return $this -> data -> addBody(-140, array(), __('请设置运费模板'), 250);
        }

      $codes = array();
       // 字段验证

      foreach (@$sheet_data as $k => $rowData) {
            $name               = $rowData['A'];
            $promotion_tips     = $rowData['B'];
            $price              = $rowData['C'];
            $market_price       = $rowData['D'];
            $cost_price         = $rowData['E'];
            $code               = $rowData['F'];
            $stock              = $rowData['G'];
            $imagePath          = $rowData['H'];
            $transport_area_id  = $rowData['I'];
            $cubage             = $rowData['J'];
            $state              = $rowData['K'];
            $is_recommend       = $rowData['L'];

            array_push($codes, $code);
            //商品名称
            if (empty($name)) {
               return $this->data ->addBody(-140, array(), __('商品名称必填'), 250);
            }else{
               $matche_row = array();
                //有违禁词
                if (Text_Filter::checkBanned($name, $matche_row) || Text_Filter::checkBanned($promotion_tips, $matche_row) || Text_Filter::checkBanned($body, $matche_row)) {
                    return $this -> data -> addBody(-140, array(), __('含有违禁词'), 250);
                }
            }
            if (empty($price)) {
               return $this->data ->addBody(-140, array(), __('商品价格必填'), 250);
            }
            if (empty($market_price)) {
               return $this->data ->addBody(-140, array(), __('市场价必填'), 250);
            }else{
                if ($price>$market_price) {
                    return $this->data ->addBody(-140, array(), __('市场价不能低于商品价格'), 250);
                }
            }
            if (empty($stock)) {
               return $this->data ->addBody(-140, array(), __('商品库存必填'), 250);
            }
            if (empty($imagePath)) {
               return $this->data ->addBody(-140, array(), __('商品图片必填'), 250);
            }
            //售卖区域
            if ($transport_area_id) {
                $check_area = $this ->areaModel -> checkArea($transport_area_id, Perm::$shopId);
                if (!$check_area) {
                    return $this ->data -> addBody(-140, array(), __('售卖区域数据有误'), 250);
                }
            } else {
                return $this ->data -> addBody(-140, array(), __('请设置售卖区域'), 250);
            }

            if (empty($cubage)) {
               return $this->data ->addBody(-140, array(), __('商品重量(单位kg)必填'), 250);
            }
            //商品发布
            if (empty($state)) {
               return $this->data ->addBody(-140, array(), __('商品发布必填'), 250);
            }else{
                if (!in_array($state, array(0,1))) {
                   return $this->data ->addBody(-140, array(), __('发布类型错误'), 250);
                }
            }
            //商品推荐
            if (empty($is_recommend)) {
               return $this->data ->addBody(-140, array(), __('商品推荐必填'), 250);
            }else{
                if (!in_array($is_recommend, array(1,2))) {
                   return $this->data ->addBody(-140, array(), __('商品推荐类型错误'), 250);
                }
            }

        }
        /**
         * 验证商品编码和商品货号的唯一性
         * @var [type]
         */
        $common_goods = $this ->goodsCommonModel-> getByWhere();
        foreach ($common_goods as $key => $goods) {
            array_push($codes, $goods['common_code']);
        }
        //剔除空值
        $codes = array_filter($codes);
        //统计重复值的数量
        $codes = array_count_values($codes);
        foreach ($codes as $k=>$code_detalis) {
            if ($code_detalis>=2) {
              // return $this->data ->addBody(-140, array(), __('商品编码'.$k.'不唯一'), 250);
            }
        }
        //读取数据
        foreach (@$sheet_data as $k => $rowData) {
            $name               = $rowData['A'];
            $promotion_tips     = $rowData['B'];
            $price              = $rowData['C'];
            $market_price       = $rowData['D'];
            $cost_price         = $rowData['E'];
            $code               = $rowData['F'];
            $stock              = $rowData['G'];
            $imagePath          = $rowData['H'];
            $transport_area_id  = $rowData['I'];
            $cubage             = $rowData['J'];
            $state              = $rowData['K'];
            $is_recommend       = $rowData['L'];

            $common_code = $code;

            $common_data['common_name'] = $name;     //商品名称
            $common_data['brand_id'] = 0;             //品牌id 默认没有品牌id
            $common_data['common_promotion_tips'] = $promotion_tips;        //商品广告词
            $common_data['common_image'] = $imagePath;                      //商品主图
            $common_data['common_price'] = $price;                          //商品价格
            $common_data['common_market_price'] = $market_price;

            $common_data['common_cost_price'] = $cost_price;                //成本价
            $common_data['common_stock'] = $stock;                            //商品库存
            $common_data['common_alarm'] = 0;                            //库存预警值
            $common_data['common_code'] = $common_code;                  //商家编号
            $common_data['common_cubage'] = $cubage;                     //商品重量
            $common_data['common_is_return'] = 1;                        // 默认7天无理由退货
            $common_data['common_state'] = $state;
            $common_data['common_is_recommend'] = $is_recommend;         //商品推荐
            $common_data['common_add_time'] = get_date_time();           //商品添加时间
            $common_data['common_edit_time'] = get_date_time();
             $common_data['transport_area_id'] = $transport_area_id;    //销售区域主键
            //0不限购
            $common_data['common_limit'] = 0;
            $common_data['common_verify'] = 1;
            $common_data['common_invoices'] = 1; //商品默认支持开发票

            //默认是立即发布，则发布时间为当前添加时间
            $common_data['common_sell_time'] = $common_data['common_add_time'];
            $this ->goodsCommonModel->sql->startTransactionDb();

            $common_id = $this ->goodsCommonModel->addCommon($common_data, true);
            //同步分销
            $flag = true;
            if ($common_id && $flag && $this ->goodsCommonModel->sql->commitDb()) {
                //库存配置
                //判断  修改的只修改
                //取出已有的所有goods_id
                $goods_base = $this -> goodsBaseModel -> getByWhere(array('common_id' => $common_id));
                if (!empty($goods_base)) {
                    $goods_base_ids = array_column($goods_base, 'goods_id');
                }
                $goods_data['cat_id'] = $common_data['cat_id'];                    //商品分类id
                $goods_data['common_id'] = $common_id;                                //商品公共表id
                $goods_data['shop_id'] = $common_data['shop_id'];                    //shop_id
                $goods_data['shop_name'] = $common_data['shop_name'];                //shop_name
                $goods_data['goods_name'] = $common_data['common_name'];                //商品名称
                $goods_data['goods_promotion_tips'] = $common_data['common_promotion_tips'];    //促销提示
                $goods_data['goods_is_recommend'] = $common_data['common_is_recommend'];        //商品推荐
                $goods_data['goods_image'] = $common_data['common_image'];                //商品主图
                //加入goods_id 冗余数据
                $goods_ids = array();
                $color_ids = array();
                $edit_goods_ids = array();
                $retain_flag = false;
                $down_flag = false;
                
                $goods_data['goods_price'] = $common_data['common_price'];                //商品价格
                $goods_data['goods_market_price'] = $common_data['common_market_price'];  //市场价
                $goods_data['goods_stock'] = $common_data['common_stock'];                //商品库存
                $goods_data['goods_alarm'] = $common_data['common_alarm'];                //库存预警值
                $goods_data['goods_code'] = $common_data['common_code'];                //商家编号货号
                $goods_id = $this ->goodsBaseModel -> addBase($goods_data, true);
                $add_ids[] = $goods_id;
                if (empty($goods_ids)) {
                    $goods_ids[] = array(
                        'goods_id' => $goods_id,
                        'color' => 0
                    );
                }
                $edit_common_data['goods_id'] = $goods_ids;
                $test_id = $this -> goodsCommonModel -> editCommon($common_id, $edit_common_data);
                /*if ($common_base['common_parent_id']) {//如果是分销商更改数据，改变修改完的跳转链接
                    $data['dist_goods'] = 1;
                }*/
                $data['common_id'] = $common_id;
                $this -> data -> addBody(-140, $data, __('success'), 200);
            } else {
                $this -> goodsCommonModel -> sql -> rollBackDb();
                $this -> data -> addBody(-140, array(), !empty($msg)? __($msg): __('failure'), 250);
            }
        }
    }

    /**
     * 转码
     * 
     * @author fzh
     */
    function unicodeToUtf8($str, $order = "little")
    {
        $utf8string ="";
        $n=strlen($str);
        for ($i=0;$i<$n ;$i++ )
        {
            if ($order=="little")
            {
                $val = str_pad(dechex(ord($str[$i+1])), 2, 0, 0) .
                    str_pad(dechex(ord($str[$i])),      2, 0, 0);
            }
            else
            {
                $val = str_pad(dechex(ord($str[$i])),      2, 0, 0) .
                    str_pad(dechex(ord($str[$i+1])), 2, 0, 0);
            }
            $val = intval($val,16); // 由于上次的.连接，导致$val变为字符串，这里得转回来。
            $i++; // 两个字节表示一个unicode字符。
            $c = "";
            if($val < 0x7F)
            { // 0000-007F
                $c .= chr($val);
            }
            elseif($val < 0x800)
            { // 0080-07F0
                $c .= chr(0xC0 | ($val / 64));
                $c .= chr(0x80 | ($val % 64));
            }
            else
            { // 0800-FFFF
                $c .= chr(0xE0 | (($val / 64) / 64));
                $c .= chr(0x80 | (($val / 64) % 64));
                $c .= chr(0x80 | ($val % 64));
            }
            $utf8string .= $c;
        }
        /* 去除bom标记 才能使内置的iconv函数正确转换 */
        if (ord(substr($utf8string,0,1)) == 0xEF && ord(substr($utf8string,1,2)) == 0xBB && ord(substr($utf8string,2,1)) == 0xBF)
        {
            $utf8string = substr($utf8string,3);
        }
        return $utf8string;
    }
}

?>
