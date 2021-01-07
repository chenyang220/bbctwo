<?php

/**
 *  拼团
 *  @author Str <tech40@yuanfeng021.com>
 */
class BillCtl extends Controller {
    public function __construct(&$ctl, $met, $typ) {
        parent::__construct($ctl, $met, $typ);
    }

    //海报
    public function getVeBill() {
        $data = array();
        //商品信息
        $goods_id   = request_int('goods_id');
        $common_id  = request_int('common_id');
        $user_id    = Perm::$userId;
        $type_wxapp = request_string('type_wxapp');

        if (!$goods_id && $common_id) {
            $Goods_CommonModel = new Goods_CommonModel();
            $goods_common      = $Goods_CommonModel->getOne($common_id);
            if ($type_wxapp == 'wxapp') {
                $goods_common['common_image'] = $goods_common['common_image'];
            } else {
                $goods_common['common_image'] = $this->base64EncodeImage($goods_common['common_image']);
            }
            $data['goods_img']        = $goods_common['common_image'];
            $data['goods_price']      = $goods_common['common_price'];
            $data['goods_price_more'] = $goods_common['common_market_price'];
            $data['goods_name']       = $goods_common['common_name'];
            $data['goods_pro_name']   = $goods_common['common_promotion_tips'];
        } else if ($goods_id && !$common_id) {
            $Goods_BaseModel = new Goods_BaseModel();
            $goods_info      = $Goods_BaseModel->getOne($goods_id);
            if ($type_wxapp == 'wxapp') {
                $goods_info['goods_image'] = $goods_info['goods_image'];
            } else {
                $goods_info['goods_image'] = $this->base64EncodeImage($goods_info['goods_image']);
            }
            $data['goods_img']        = $goods_info['goods_image'];
            $data['goods_price']      = $goods_info['goods_price'];
            $data['goods_price_more'] = $goods_info['goods_market_price'];
            $data['goods_name']       = $goods_info['goods_name'];
            $data['goods_pro_name']   = $goods_info['goods_promotion_tips'];
        } else {
            $msg    = 'FAILED';
            $status = 250;
            return $this->data->addBody(-140, [], $msg, $status);
        }
        //用户信息
        $k                 = request_string('k');
        $u                 = request_int('u');
        $user_info         = $this->getVeUser($k, $u);
        $data['user_name'] = $user_info['wxName'];
        $data['user_info'] = $user_info;
        if ($type_wxapp == 'wxapp') {
            $data['user_logo'] = $user_info['wxlogo'] ?: $user_info['user_logo'];
        } else {
            $data['user_logo'] = $user_info['user_logo'];
        }
        $data['head_img'] = $this->base64EncodeImage($data['user_logo']);
        //商品二维码（分销）

        //根据商品类型 跳转不同的商品详情页
        if (!$goods_info['is_video']) {
            //普通商品
            $url = urlencode(Yf_Registry::get('shop_wap_url') . '/tmpl/product_detail.html?goods_id=' . $goods_id . '&uu_id=' . $user_id);
        } else if ($goods_info['is_video'] == 1) {
            //免费视频
            $url = urlencode(Yf_Registry::get('shop_wap_url') . '/tmpl/ve/product_fvideo_detail.html?goods_id=' . $goods_id . '&uu_id=' . $user_id);
        } else if ($goods_info['is_video'] == 2) {
            //付费视频
            $url = urlencode(Yf_Registry::get('shop_wap_url') . '/tmpl/ve/product_video_detail.html?goods_id=' . $goods_id . '&uu_id=' . $user_id);
        } else if ($goods_info['is_video'] == 3) {
            //视频专栏
            if ($goods_id) {
                $url = urlencode(Yf_Registry::get('shop_wap_url') . '/tmpl/ve/video_column_detail.html?goods_id=' . $goods_id . '&uu_id=' . $user_id);
            } else if ($common_id) {
                $url = urlencode(Yf_Registry::get('shop_wap_url') . '/tmpl/ve/video_column_detail.html?common_id=' . $common_id . '&uu_id=' . $user_id);
            }

        }
        $qrCode          = Yf_Registry::get('base_url') . '/shop/api/qrcode.php?data=' . $url;
        $data['er_code'] = $this->base64EncodeImage($qrCode);
        $poster_head = Web_ConfigModel::value('mall_poster',0);
        if ($poster_head) {
           $data['poster_head'] = $this->base64EncodeImage($poster_head);
        }
        if ($type_wxapp == 'wxapp') {
            $qrCode          = Yf_Registry::get('base_url') . '/shop/api/qrcode.php?data=' . $url;
            $data['er_code'] = $qrCode;
        }
        $msg    = 'success';
        $status = 200;
        return $this->data->addBody(-140, $data, $msg, $status);
    }

    //海报
    public function getBill() {
        $data = array();
        //商品信息
        $goods_id        = request_int('goods_id');
        $user_id         = Perm::$userId;
        $type_wxapp      = request_string('type_wxapp');
        $Goods_BaseModel = new Goods_BaseModel();
        $goods_info      = $Goods_BaseModel->getOne($goods_id);
        if ($type_wxapp == 'wxapp') {
            $goods_info['goods_image'] = $goods_info['goods_image'];
        } else {
            $goods_info['goods_image'] = $this->base64EncodeImage($goods_info['goods_image']);
        }
        $data['goods_info'] = $goods_info;

        //店铺海报
        $BillModel          = new BillModel();
        $row['shop_id']     = $goods_info['shop_id'];
        $bill_info          = $BillModel->getOneByWhere($row);
        $data['bill_image'] = $this->base64EncodeImage($bill_info['bill_image']);
        if ($type_wxapp == 'wxapp') {
            $data['bill_image'] = $bill_info['bill_image'];
        }
        //用户信息
        $k                 = request_string('k');
        $u                 = request_int('u');
        $user_info         = $this->getUser($k, $u);
        $data['user_name'] = $user_info['wxName'];
        $user_info['wxlogo'] ? $user_info['wxlogo'] : $user_info['img'];
        $data['user_logo'] = $this->base64EncodeImage($user_info['wxlogo']);
        if ($type_wxapp == 'wxapp') {
            $data['user_logo'] = $user_info['wxlogo'];
        }
        $data['user_logo'] = $user_info['wxlogo'];
        //商品二维码（分销）
        $url            = urlencode(Yf_Registry::get('shop_wap_url') . '/tmpl/product_detail.html?goods_id=' . $goods_id . '&uu_id=' . $user_id);
        $qrCode         = Yf_Registry::get('base_url') . '/shop/api/qrcode.php?data=' . $url;
        $data['qrCode'] = $this->base64EncodeImage($qrCode);
        if ($type_wxapp == 'wxapp') {
            $qrCode         = Yf_Registry::get('base_url') . '/shop/api/qrcode.php?data=' . $url;
            $data['qrCode'] = $qrCode;
        }
        $msg    = 'success';
        $status = 200;
        return $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 把网络图片图片转成base64
     * 
     * @dateTime  2020-07-21
     * @author fzh
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     * @param     string   $img   图片地址      
     */
    public function base64EncodeImage($img = '') {
        $imagetype = exif_imagetype($img);
        $mime_type = image_type_to_mime_type($imagetype);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $img);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $info = curl_exec($curl);
        curl_close($curl);
        $base64  = chunk_split(base64_encode($info));
        return 'data:' . $mime_type . ';base64,' . $base64;
    }

    //用户信息
    public function getUser($k, $u) {
        $user_id             = Perm::$userId ?: 10002;
        $bind_avator         = '';
        $key                 = Yf_Registry::get('ucenter_api_key');
        $url                 = Yf_Registry::get('ucenter_api_url');
        $app_id              = Yf_Registry::get('ucenter_app_id');
        $formvars            = [];
        $formvars['user_id'] = $user_id;
        $formvars['u']       = $u;
        $formvars['k']       = $k;
        $formvars['app_id']  = $app_id;
        $url                 = sprintf('%s?ctl=%s&met=%s&typ=%s', $url, 'Api_User', 'getUserBind', 'json');
        $init_rs             = get_url_with_encrypt($key, $url, $formvars);
        if (200 == $init_rs['status'] && $init_rs['data']['ret']['bind_avator']) {
            $bind_avator = $init_rs['data']['ret']['bind_avator'];
            $wx_name     = $init_rs['data']['ret']['bind_nickname'];
        }
        $user_name     = $user_logo     = '';
        $user_info_mdl = new User_InfoModel();
        $result        = $user_info_mdl->getInfo($user_id);
        $result        = current($result);
        $user_img      = $result['user_logo'];
        if ((!$bind_avator || !$wx_name) && $user_id) {
            $user_name = $result['user_name'];
            $user_logo = empty($result['user_logo']) ? Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id=' . $user_id : $result['user_logo'];
            unset($result);
        }
        $txt  = Web_ConfigModel::value('myqrcode_describe');
        $img  = Web_ConfigModel::value('myqrcode_bgimg');
        $data = array(
            'txt'       => $txt,
            'img'       => $img,
            'user_logo' => $user_img,
            'wxlogo'    => $bind_avator ? $bind_avator : $user_logo,
            'wxName'    => $wx_name ? $wx_name : $user_name,
        );
        return $data;
    }


    //用户信息
    public function getVeUser($k, $u) {
        $user_id             = Perm::$userId ?: 10002;
        $bind_avator         = '';
        $key                 = Yf_Registry::get('ucenter_api_key');
        $url                 = Yf_Registry::get('ucenter_api_url');
        $app_id              = Yf_Registry::get('ucenter_app_id');
        $formvars            = [];
        $formvars['user_id'] = $user_id;
        $formvars['u']       = $u;
        $formvars['k']       = $k;
        $formvars['app_id']  = $app_id;
        $url                 = sprintf('%s?ctl=%s&met=%s&typ=%s', $url, 'Api_User', 'getUserBind', 'json');
        $init_rs             = get_url_with_encrypt($key, $url, $formvars);
        if (200 == $init_rs['status'] && $init_rs['data']['ret']['bind_avator']) {
            $bind_avator = $init_rs['data']['ret']['bind_avator'];
            $wx_name     = $init_rs['data']['ret']['bind_nickname'];
        }
        $user_name     = $user_logo     = '';
        $user_info_mdl = new User_InfoModel();
        $result        = $user_info_mdl->getInfo($user_id);
        $result        = current($result);
        $user_img      = $result['user_logo'];
        if ((!$bind_avator || !$wx_name) && $user_id) {
            $user_name = $result['user_name'];
            $user_logo = empty($result['user_logo']) ? Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id=' . $user_id : $result['user_logo'];
            unset($result);
        }
        // $txt  = Web_ConfigModel::value('myqrcode_describe');
        // $img  = Web_ConfigModel::value('myqrcode_bgimg');
        $data = array(
            // 'txt'       => $txt,
            // 'img'       => $img,
            'user_logo' => $user_img,
            'wxlogo'    => $bind_avator ? $bind_avator : $user_logo,
            'wxName'    => $wx_name ? $wx_name : $user_name,
        );
        return $data;
    }

    /**
     *功能：实现下载远程图片保存到本地
     *参数：文件url,保存文件目录,保存文件名称，使用的下载方式
     *当保存文件名称为空时则使用远程文件原来的名称
     */
    public function getImage() {

        $save_dir     = request_string('save_dir');
        $filename     = request_string('filename');
        $user_id      = request_int('u');
        $goods_id     = request_int('goods_id');
        $common_id    = request_int('common_id');
        $goods_base64 = request_string('data64_file');
        if (trim($goods_base64) == '') {
            return $this->data->addBody(-140, array('file_name' => '', 'save_path' => ''), '图片不存在', 250);
        }
        if (trim($save_dir) == '') {
            $url_prefix     = APP_PATH . '/data/upload';
            $save_dir       = sprintf('/media/%s/%d/%s/', Yf_Registry::get('server_id'), $user_id, 'poster');
            $save_dir_final = $url_prefix . $save_dir;
        }

        if (trim($filename) == '') {
            if ($goods_id) {
                $filename = 'poster' . $user_id . $goods_id . '.png';
            } else if ($common_id) {
                $filename = 'poster' . $user_id . $common_id . '.png';
            }
        }
        //创建保存目录  (!!!!!!最好将图片保存 循环使用 暂时没有处理!!!!!!)
        if (!file_exists($save_dir_final) && !mkdir($save_dir_final, 0777, true)) {
            return $this->data->addBody(-140, array('file_name' => $filename, 'save_path' => $save_dir_final, 'error' => 5), '创建目录失败', 250);
        }
        //获取远程文件所采用的方法
        ob_start();
        readfile($goods_base64);
        $img = ob_get_contents();
        ob_end_clean();

        //文件大小
        $fp2 = @fopen($save_dir_final . $filename, 'a');
        fwrite($fp2, $img);
        fclose($fp2);
        unset($img, $goods_base64);
        // $resp = array('file_name' => $filename, 'save_path' => $save_dir_final . $filename, 'error' => 0);
        $url  = Yf_Registry::get('base_url') . '/' . APP_DIR_NAME . '/data/upload' . $save_dir . $filename;
        $resp = array('file_name' => $filename, 'save_path' => $url, 'error' => 0);
        return $this->data->addBody(-140, $resp, 'success', 200);
    }
}