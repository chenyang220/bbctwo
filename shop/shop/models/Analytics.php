<?php

/**
 * Description of AnalyticsModel
 *
 * @author tech40@yuanfeng021.com  & tech35@yuanfeng021.com
 */
class Analytics {
    public  $key = null;
    public  $url = null;
    public  $app_id = null;
    
    public function __construct(){
        $this->key = Yf_Registry::get('analytics_api_key');
        $this->url = Yf_Registry::get('analytics_api_url');
        $this->app_id = Yf_Registry::get('analytics_app_id');
    }

    /**
     * 商家中心 --- 店铺概况
     * @param array $formvars
     * @return boolean
     */
    public function getGeneralInfo($formvars = array()){
        if(!$formvars){
            return false;
        }
        $formvars['app_id']    = $this->app_id;
        $init_rs = get_url_with_encrypt($this->key, sprintf('%s?ctl=Api_Shop_Getdata&met=getGeneralInfo&typ=json', $this->url), $formvars);
        return $init_rs;
    }
    
     /**
     * 商家中心 --- 商品详情
     * @param array $formvars
     * @return boolean
     */
    public function getGoodsAnalytics($formvars = array()){
        if(!$formvars){
            return false;
        }
        $formvars['app_id']    = $this->app_id;
        $init_rs = get_url_with_encrypt($this->key, sprintf('%s?ctl=Api_Shop_Getdata&met=getGoodsAnalytics&typ=json', $this->url), $formvars);
        return $init_rs;
    }
    
     /**
     * 商家中心 --- 热卖商品
     * @param array $formvars
     * @return boolean
     */
    public function getGoodsHot($formvars = array()){
        if(!$formvars){
            return false;
        }
        $formvars['app_id']    = $this->app_id;
        $init_rs = get_url_with_encrypt($this->key, sprintf('%s?ctl=Api_Shop_Getdata&met=getGoodsHot&typ=json', $this->url), $formvars);
        return $init_rs;
    }
    
     /**
     * 商家中心 --- 运营报告
     * @param array $formvars
     * @return boolean
     */
    public function getOperationArea($formvars = array()){
        if(!$formvars){
            return false;
        }
        $formvars['app_id']    = $this->app_id;
        $init_rs = get_url_with_encrypt($this->key, sprintf('%s?ctl=Api_Shop_Getdata&met=getOperationArea&typ=json', $this->url), $formvars);

        return $init_rs;
    }
    
    //获取商品详情 2017.3.14 hp
    public function getGoodsDetail($formvars = array())
    {
        if(!$formvars){
            return false;
        }
        $formvars['app_id']    = $this->app_id;
        $init_rs = get_url_with_encrypt($this->key, sprintf('%s?ctl=Api_Shop_Getdata&met=getGoodsDetail&typ=json', $this->url), $formvars);

        return $init_rs;
    }

    //获取商品详情 2017.3.15 hp
    public function goodsAnalysis($formvars = array())
    {
        if(!$formvars){
            return false;
        }
        $formvars['app_id']    = $this->app_id;
        $init_rs = get_url_with_encrypt($this->key, sprintf('%s?ctl=Api_Shop_Getdata&met=getGoodsAnalysis&typ=json', $this->url), $formvars);

        return $init_rs;
    }

    //获取订单地域详情 2017.3.16 hp
    public function getAreaData($formvars = array())
    {
        if(!$formvars){
            return false;
        }

        $formvars['app_id']    = $this->app_id;
        $init_rs = get_url_with_encrypt($this->key, sprintf('%s?ctl=Api_Shop_Getdata&met=getAreaData&typ=json', $this->url), $formvars);
        return $init_rs;
    }

    //商品分析统计
    public function goodsAnalysisInfo($cond)
    {
        $Order_BaseModel = new Order_BaseModel();
        $stime = date('Y-m-d 00:00:00', strtotime($cond['stime']));
        $etime = date('Y-m-d 23:59:59', strtotime($cond['etime']));

        //商品订单
        $order_sql = "SELECT *,FROM_UNIXTIME(unix_timestamp(order_goods_time),'%Y-%m-%d') as time FROM yf_order_goods WHERE shop_id = " . $cond['shop_id'] . " AND order_goods_time >= '" . $stime . "' AND order_goods_time <= '" . $etime . "' AND order_goods_status IN (2,3,4,5,6) AND goods_id = " . $cond['product_id'] . " GROUP BY order_goods_time";
        $order_list = $Order_BaseModel->sql->getAll($order_sql);

        //商品足迹
        $Goods_BaseModel = new Goods_BaseModel();
        $goods_base = $Goods_BaseModel->getOne($cond['product_id']);
        $follow_sql = "SELECT *,footprint_date AS time FROM yf_user_footprint WHERE common_id = " . $goods_base['common_id'] . " AND footprint_time >= '" . $stime . "' AND footprint_time <= '" . $etime . "' GROUP BY footprint_date";
        $User_FootprintModel = NEW User_FootprintModel();
        $foot_list = $User_FootprintModel->sql->getAll($follow_sql);

        //商品评论
        $Goods_EvaluationModel = new Goods_EvaluationModel();
        $evaluation_sql = "SELECT *,FROM_UNIXTIME(unix_timestamp(create_time),'%Y-%m-%d') as time FROM yf_goods_evaluation WHERE create_time >= '" . $stime . "' AND create_time <= '" . $etime . "' AND goods_id = " . $cond['product_id'] . " GROUP BY create_time";
        $evaluation_list = $Goods_EvaluationModel->sql->getAll($evaluation_sql);

        //商品收藏
        $User_FavoritesShopModel = new User_FavoritesShopModel();
        $favourite_sql = "SELECT *,FROM_UNIXTIME(unix_timestamp(favorites_goods_time),'%Y-%m-%d') as time FROM yf_user_favorites_goods WHERE favorites_goods_time >= '" . $stime . "' AND favorites_goods_time <= '" . $etime . "' AND goods_id = " . $cond['product_id'] . " GROUP BY favorites_goods_time";
        $favourite_list = $User_FavoritesShopModel->sql->getAll($favourite_sql);

        $starttime = strtotime($stime);
        $endtime = strtotime($etime);
        $second = $endtime - $starttime;
        $day = floor($second / (3600 * 24)); //共有多少天

        //时间-轴
        $categories = array();//时间段
        $data_order = array();//销售量
        $data_sales = array();//销售额
        $data_pv_num = array();//访问、浏览量
        $data_followr = array();//关注
        $data_conversion = array();//转化率
        $data_score = array();//商品评分
        for ($i = 0; $i <= $day; $i++) {
            $time = date("Y-m-d", $starttime);
            //时间段
            $categories[] = date("m-d", $starttime);

            //销售量
            $data_order[] = $this->array_sum($order_list, 'order_goods_num',$time);

            //销售额
            $data_sales[] = $this->array_sum($order_list, 'order_goods_payment_amount', $time);

            //浏览量
            $data_pv_num[] = $day_total = $this->array_sum($foot_list, '', $time);

            //评分
            $data_score[] = $this->array_sum($evaluation_list, '', $time);

            //关注
            $data_followr[] = $this->array_sum($favourite_list, '', $time);

            //转化率
            $data_conversion[] = (count($order_list) / $day_total) ?: 0;

            //明天时间
            $starttime = $starttime + 3600 * 24;
        }

        $data = array();
        $data['time'] = $time;
        $data['data_order'] = $data_order;
        $data['categories'] = $categories;
        $data['data_sales'] = $data_sales;
        $data['data_followr'] = $data_followr;
        $data['data_conversion'] = $data_conversion;
        $data['data_pv_num'] = $data_pv_num;
        $data['data_score'] = $data_score;
        $data['starttime'] = $starttime;

        $data['x_data'] = json_encode($categories);
        $data['y_data_order'] = json_encode($data_order);
        $data['y_data_sales'] = json_encode($data_sales);
        $data['y_data_followr'] = json_encode($data_followr);
        $data['y_data_conversion'] = json_encode($data_conversion);
        $data['y_data_pv_num'] = json_encode($data_pv_num);
        $data['y_data_score'] = json_encode($data_score);

        return $data;
    }

    public function array_sum($arr, $target_key_sum,$time)
    {
        $sum = 0;

        foreach ($arr as $k => $v) {
            if($time == $v['time']){
                if($target_key_sum){
                    $sum += $v[$target_key_sum];
                }else{
                    $sum += 1;
                }
            }
        }

        return $sum;
    }

}
