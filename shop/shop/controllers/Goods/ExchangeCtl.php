<?php
    
    echo '122121';die;
    if (!defined('ROOT_PATH')) {
        exit('No Permission');
    }
    /**
     * Created by PhpStorm.
     * User: mashu
     * Date: 2018/6/14 0014
     * Time: 17:17
     */
    
    /**
     * @author     Michael
     */
    class Seller_Goods_Exchange extends Seller_Controller
    {
        // public $shopBaseModel = null;
        
        /**
         * Constructor
         *
         * @param  string $ctl 控制器目录
         * @param  string $met 控制器方法
         * @param  string $typ 返回数据类型
         *
         * @access public
         */
        public function __construct(&$ctl, $met, $typ)
        {
            parent::__construct($ctl, $met, $typ);
            $this -> shopBaseModel = new Shop_BaseModel();
        }
        
        public function exchange()
        {
            echo 22222;die;
            include $this->view->getView();
        }
        
        public function editExchange()
        {
            // 商家店铺id
            // $shop_id = Perm::$shopId;
            $shop_id          = request_int('shop_id');
            
            // 手动设置的汇率项
            $shop_manual = request_int('manual');
            // 实时自动获取汇率
            $shop_real = request_int('real');
            //设置的汇率值
            $shop_exchange_rate = request_float('shop_exchange_rate');
            
            // 初始化编辑字段值
            $edit_shop_row = array();
            
            // 如果手动的，则修改  shop_exchange_rate 字段值：
            if ($shop_manual == 1){
                $edit_shop_row['shop_exchange_rate'] = $shop_exchange_rate;
            }
            
            if ($shop_real == 2) {
                // 请求接口实时查询，修改 shop_exchange_rate 字段值
                //调用方法
                $edit_shop_row['shop_exchange_rate'] = $this->getExchangeRate('CNY',"MYR");
            }
            
            $flag = $this->shopBaseModel->editBase($shop_id,$edit_shop_row);
            
            if ($flag === false){
                $status = 250;
                $msg    = __('failure');
            } else {
                $status = 200;
                $msg    = __('success');
            }

            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        public function exchangeApiSet()
        {
            include $this->view->getView();
        }
        
        // 实时获取汇率
    
        private  function getExchangeRate($from_Currency,$to_Currency)
        {
            $amount = urlencode($amount);
            $from_Currency = urlencode($from_Currency);
            $to_Currency = urlencode($to_Currency);
            $url = "download.finance.yahoo.com/d/quotes.html?s=".$from_Currency.$to_Currency."=X&f=sl1d1t1ba&e=.html";
            $ch = curl_init();
            $timeout = 0;
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $rawdata = curl_exec($ch);
            curl_close($ch);
            $data = explode(',', $rawdata);
            return $data[1];
        }
    }
