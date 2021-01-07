<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * Api接口, 让App等调用
 *
 *
 * @category   Game
 * @package    User
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2015 远丰仁商
 * @version    1.0
 * @todo
 */
class Api_Paycen_PayCardCtl extends Api_Controller
{
    public $cardInfoModel    = null;

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

        $this->cardInfoModel    = new Card_InfoModel();

    }

    //获取支付卡列表
    public function getCardBaseList()
    {
        $cardname  = request_string('cardName');   //卡片名称
        $beginDate = request_string('beginDate');
        $endDate   = request_string('endDate');
        $appid     = request_int('appid');

        $page = request_string('page', 1);
        $rows = request_string('rows', 20);

        $Card_BaseModel = new Card_BaseModel();
        $data           = $Card_BaseModel->getBaseList($cardname, $appid, $beginDate, $endDate, $page, $rows);


        $Card_InfoModel = new Card_InfoModel();
        foreach ($data['items'] as $key => $val)
        {
            $card_used_num                        = $Card_InfoModel->getCardusednumBy($val['card_id']);
            $data['items'][$key]['card_used_num'] = $card_used_num;

            $card_new_num                        = $Card_InfoModel->getCardnewnumBy($val['card_id']);
            $data['items'][$key]['card_new_num'] = $card_new_num;
        }

        if ($data)
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {
            $msg    = 'failure';
            $status = 250;
        }
        fb($data);
        $this->data->addBody(-140, $data, $msg, $status);
    }
    //添加购物卡
    public function add()
    {
        $Buyer_TestModel           = new Card_BaseModel();
        $data                       = array();
        $data['card_id']         = request_int('card_id');                  //卡id
        $data['card_name']       = request_string('card_name');             //卡名称
        $data['card_num']        = request_string('card_num');              //卡数量
        $data['card_start_time']        = request_string('card_start_time');//卡开始有效时间
        $data['card_end_time']        = request_string('card_end_time');    //卡的有效结束时间card_desc
        $data['card_desc']        = request_string('card_desc');            //卡的描述
        $data_rows =array();
        $data_rows['m'] = request_float('money');
        $data_rows['p'] = request_float('point');
        $data['card_prize'] = json_encode($data_rows);                      //卡的积分和金额
        $flag = $Buyer_TestModel->addBase($data, true);
        if ($flag)
        {
            $msg    = 'failure';
            $status = 250;
        }
        else
        {
            $msg    = 'success';
            $status = 200;
        }
        $this->data->addBody(-140, $data, $msg, $status);

    }

    //添加支付卡信息
    public function addCardBase()
    {
        $card_id    = request_int('card_id');       //卡id
        $card_name  = request_string('card_name');  //卡名称
        $card_num   = request_int('card_num');      //卡数量
        $start_time = request_string('card_start_time');  //卡有效开始时间
        $end_time   = request_string('card_end_time');    //卡有效结束时间
        $card_desc  = request_string('card_desc');   //卡的描述
        $money      = request_float('money');
        $point      = request_float('point');

        $Card_BaseModel = new Card_BaseModel();
        $Card_InfoModel = new Card_InfoModel();

        //开启事物
        $Card_BaseModel->sql->startTransactionDb();

        $card_data = $Card_BaseModel->getBase($card_id);
        if ($card_data)
        {
            return $this->data->addBody(-140, [], '此卡号已存在，请重新填写', 250);
        }
        else
        {
            for ($i = 1; $i <= $card_num; $i++)
            {
                $add_row = array();
                $add_row = array(
                    'card_code' => $card_id . str_pad($i, 4, "0", STR_PAD_LEFT),
                    'card_password' => rand(100000, 999999),
                    'card_id' => $card_id,
                    'card_time' => date("Y-m-d H:i:s"),
                    'card_money' => $money,
                );
                $Card_InfoModel->addInfo($add_row);
            }

            $prize = array();
            if ($money)
            {
                $prize['m'] = $money;
            }
            if ($point)
            {
                $prize['p'] = $point;
            }

            $card_prize = $prize;

            $card_add_array = array(
                'card_id' => $card_id,
                'card_name' => $card_name,
                'card_prize' => $card_prize,
                'card_desc' => $card_desc,
                'card_start_time' => $start_time,
                'card_end_time' => $end_time,
                'card_num' => $card_num,
            );

            $flag = $Card_BaseModel->addBase($card_add_array);
        }

        if ($flag && $Card_BaseModel->sql->commitDb())
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {
            $Card_BaseModel->sql->rollBackDb();
            $msg    = 'failure';
            $status = 250;
        }

        $str = '';
        if($money)
        {
            $str .='金额:'.$money.'; ';
        }
        if($point)
        {
            $str .='积分:'.$point . '; ';
        }

        $card_add_array['card_cprize'] = $str;
        $card_add_array['card_used_num'] = 0;
        $card_add_array['card_new_num'] = $card_num;
        $this->data->addBody(-140, $card_add_array, $msg, $status);
    }








    /*
      * 删除购物卡
      */
    public function remove()
    {
        $Card_BaseModel     = new Card_BaseModel();

        $cat_id = request_int('cat_id');
        if ($cat_id)
        {
            $flag = $Card_BaseModel->removeBase($cat_id);


        }
        if ($flag)
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {

            $msg    = 'failure';
            $status = 250;

        }

        $data['cat_id'] = $cat_id;
        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function delCardBase()
    {
        $card_id = request_int('card_id');

        $Card_InfoModel = new Card_InfoModel();
        $Card_BaseModel = new Card_BaseModel();
        $used_num       = $Card_InfoModel->getCardusednumBy($card_id);
        $flag = false;
        if ($used_num)
        {
            return  $this->data->addBody(-140, array(), '购物卡已被使用', 250);
        }
        else
        {
            //删除充值卡card_info
            $Card_InfoModel->sql->startTransactionDb();
            $res1 = $Card_BaseModel->removeBase($card_id);
            if(!$res1){
                $Card_InfoModel->sql->rollBackDb();
            }else{
                $res2 = $Card_InfoModel->delCardByCid($card_id);
                if(!$res2){
                    $Card_InfoModel->sql->rollBackDb();
                }else{
                    $flag = true;
                }
            }
        }

        if ($flag && $Card_InfoModel->sql->commitDb())
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {
            $msg    = 'failure';
            $status = 250;
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    //修改支付卡信息
    public function editCardBase()
    {
        $id         = request_int('card_id');  //卡号
        $card_name  = request_string('card_name'); //卡名称
        $card_num   = request_int('card_num');  //数量
        $start_time = request_string('card_start_time');   //开始时间
        $end_time   = request_string('card_end_time');    //结束时间
        $card_desc  = request_string('card_desc');
        $money      = request_float('money');    //金额
        $point      = request_float('point');    //积分

        //获取充值卡信息
        $Card_BaseModel = new Card_BaseModel();
        $card_base_row  = current($Card_BaseModel->getBase($id));

        $flag = true;


        //判断充值卡数量是否改变
        $diff_num = $card_num - $card_base_row['card_num'];

        $Card_InfoModel = new Card_InfoModel();
        if ($diff_num > 0)
        {
            //查找最后一张充值卡
            $last_card_code = $Card_InfoModel->getListCardcodeByCid($id);

            for ($i = 1; $i <= $diff_num; $i++)
            {
                $filed = array(
                    'card_code' => $last_card_code + $i,
                    'card_password' => rand(100000, 999999),
                    'card_id' => $id,
                    'card_fetch_time' => date('Y-m-d H:i:s'),
                );
                $Card_InfoModel->addInfo($filed);
            }
        }
        elseif ($diff_num < 0)
        {
            $num = abs($diff_num);
            //删除充值卡
            $Card_InfoModel->delCardByCid($id, $num);
        }

        $prize = array();
        if ($money)
        {
            $prize['m'] = $money;
        }
        if ($point)
        {
            $prize['p'] = $point;
        }

        $card_prize = $prize;

        $filed_array = array(
            'card_name' => $card_name,
            'card_num' => $card_num,
            'card_prize' => $card_prize,
            'card_start_time' => $start_time,
            'card_end_time' => $end_time,
            'card_desc' => $card_desc,
        );

        $flag = $Card_BaseModel->editBase($id, $filed_array);

        if ($flag === false)
        {
            $msg    = 'failure';
            $status = 250;
        }
        else
        {
            $msg    = 'success';
            $status = 200;
        }

        $this->data->addBody(-140, $filed_array, $msg, $status);
    }

    /**
     * 获取购物卡详情
     *
     * @access public
     */
    public function getCardlist($return = false)
    {
        $card_id = request_int('card_id');
        $data    = $this->cardInfoModel->getListCardInfoByCardId($card_id);

        $this->data->addBody(-140, $data);
        if ($return)
        {
            return $data;
        }
        else
        {
            $this->data->addBody(-140, $data);
        }
    }
    //批量删除
//    public function removeBaseSelected($config_array = array())
//    {
//
//        foreach ($config_array as $key => $value)
//        {
//            $flag = $this->removeBase($value);
//        }
//    }

    /**
     * 获取购物卡详情
     *
     * @access public
     */
    public function getCardListExcel()
    {
        ob_get_clean();
        $limit = request_int("limit",10);
        $start_limit = request_int("start_limit",0);
        $is_limit = request_int("is_limit",0);
        $action = request_string('action','all');
        $cond_row = array();

        if ($limit) {
            $cond_row['limit'] = $limit;
        }
        if ($limit) {
            $cond_row['start_limit'] = $start_limit;
        }
        if ($is_limit) {
            $cond_row['is_limit'] = $is_limit;
        }
        if ($action) {
            $cond_row['action'] = $action;
        }
        $Card_InfoModel = new Card_InfoModel();
        $limits = $Card_InfoModel->getCounts($action);

        if ($is_limit) {
            $this->export($cond_row);
        } else {
            //保存地址
            $path = ROOT_PATH . '/paycenter/data/download/';
            $file_template = $path . time();//临时文件
            $url = $file_template . '/';
            for ($i = 0; $i < $limits; $i++) {
                $cond_row['limits'] = $i;
                $this->export($cond_row, $i, $url);
            }
            //打包
            $zip = new ZipArchive();
            $down_name = 'payment_card.zip';
            $file_name = $path . $down_name;

            if ($zip->open($file_name, ZipArchive::CREATE) === TRUE) {
                $this->addFileToZip($url, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
                $zip->close(); //关闭处理的zip文件
            }
            $fp = fopen($file_name, "r");
            $file_size = filesize($file_name);//获取文件的字节

            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length:" . $file_size);
            Header("Content-Disposition: attachment; filename=$down_name");
            $buffer = 1024; //设置一次读取的字节数，每读取一次，就输出数据（即返回给浏览器）
            $file_count = 0; //读取的总字节数
            //向浏览器返回数据 如果下载完成就停止输出，如果未下载完成就一直在输出。根据文件的字节大小判断是否下载完成
            while(!feof($fp) && $file_count < $file_size) {
                $file_con = fread($fp, $buffer);
                $file_count += $buffer;
                echo $file_con;
            }
            fclose($fp);
            //下载完成后删除压缩包，临时文件夹
            if ($file_count >= $file_size) {
                unlink($file_name);
                exec("rm -rf ".$file_template);
            }
        }
    }

    /**压缩文件夹
     * @param $path
     * @param $zip
     */
    function addFileToZip($path,$zip){
        $handler = opendir($path); //打开当前文件夹由$path指定。
        while(($filename = readdir($handler)) !== false){
            if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..'，不要对他们进行操作
                if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                    addFileToZip($path."/".$filename, $zip);
                }else{ //将文件加入zip对象
                    $zip->addFile($path."/".$filename,$filename);
                }
            }
        }
        @closedir($path);
    }


    public function export($cond_row,$i="",$url="")
    {
        $Card_InfoModel = new Card_InfoModel();
        $con = $Card_InfoModel->getCardInfoExcel($cond_row);
        $tit = array(
            "序号",
            "卡号",
            "卡密",
            "卡金额",
        );
        $key = array(
            "card_code",
            "card_password",
            "card_money",
        );

        if(isset($i)&& is_numeric($i)){
            $this->download_excel("购物卡信息".$i, $tit, $con, $key,$url,$i);
        }else{
            $this->excel("购物卡信息", $tit, $con, $key);
        }
    }

    public function download_excel($title,$tit,$con,$key,$url,$i){
        ob_end_clean();   //***这里再加一个
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("mall_new");
        $objPHPExcel->getProperties()->setLastModifiedBy("mall_new");
        $objPHPExcel->getProperties()->setTitle($title);
        $objPHPExcel->getProperties()->setSubject($title);
        $objPHPExcel->getProperties()->setDescription($title);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $letter = array(
            'A',
            'B',
            'C',
            'D',
        );
        foreach ($tit as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[$k] . "1", $v);
        }
        foreach ($con as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[0] . ($k + 2), $k + 1);
            foreach ($key as $k2 => $v2)
            {

                $objPHPExcel->getActiveSheet()->setCellValue($letter[$k2 + 1] . ($k + 2), ' '.$v[$v2]);
            }
        }
        ob_end_clean();   //***这里再加一个
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        if (!file_exists($url) && !mkdir($url, 0777, true)) {
            return false;
        }
        $url = $url.time().$i.'.xls';
        $objWriter->save($url);
    }

    function excel($title, $tit, $con, $key)
    {
        ob_end_clean();   //***这里再加一个
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("mall_new");
        $objPHPExcel->getProperties()->setLastModifiedBy("mall_new");
        $objPHPExcel->getProperties()->setTitle($title);
        $objPHPExcel->getProperties()->setSubject($title);
        $objPHPExcel->getProperties()->setDescription($title);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $letter = array(
            'A',
            'B',
            'C',
            'D',
        );
        foreach ($tit as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[$k] . "1", $v);
        }
        foreach ($con as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[0] . ($k + 2), $k + 1);
            foreach ($key as $k2 => $v2)
            {

                $objPHPExcel->getActiveSheet()->setCellValue($letter[$k2 + 1] . ($k + 2), ' '.$v[$v2]);
            }
        }
        ob_end_clean();   //***这里再加一个
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$title.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }


}

?>