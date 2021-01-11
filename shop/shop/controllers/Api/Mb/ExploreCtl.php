<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Api_Mb_ExploreCtl extends Yf_AppController
{
    public $userInfoModel     = null;
    public $ExploreReportModel = null;

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
        
        $this->userInfoModel           = new User_InfoModel();
        $this->ExploreReportModel      = new Explore_ReportModel();
        $this->ExploreBaseModel       = new Explore_BaseModel();

    }
    
    /**
     *获取举报信息
     *
     * @access public
     */
    public function getExploreList()
    {
        
        $page = request_int('page', 1);
        $rows = request_int('rows', 10);

        $user_name   = request_string('exploreName');
        $explore_title  = request_string('exploreTitle');
        $explore_reason = request_int('reason',-1); //举报原因
        $explore_status = request_int('status'); //处理状态

        $data = $this->ExploreReportModel->getReportList($user_name, $explore_title, $explore_reason, $explore_status,$page,$rows);
        
        $this->data->addBody(-140, $data);

    }

    /**
     * 获取单条举报信息
     *
     * @access public
     * @return json
     */
    public function exploreinfo(){

        $report_id             = request_string('report_id');

        $data = $this->ExploreReportModel->getReportDetail($report_id);

        $this->data->addBody(-140, $data);
    }

    /**
     * 编辑举报信息页面,获取信息
     *
     * @access public
     */
    public function editexplore()
    {
        $report_id              = request_string('report_id');

        $data = $this->ExploreReportModel->getReportDetail($report_id);

        $this->data->addBody(-140, $data);
    }

    /**
     * 操作单条举报信息，审核 
     *
     * @access public
     */
    public function editDetail()
    {
        $edit_row = array();

        $report_id                      = request_string('report_id');
        $explore_id                     = request_string('explore_id');

        $report_handle      = request_string('report_handle'); //举报处理备注
        $report_status      = request_string('report_status');

        //开启事务
        $this->ExploreReportModel->sql->startTransactionDb();

        $flag = $this->ExploreReportModel->editReportDetail($report_id,$explore_id,$report_handle,$report_status);

        if ($flag && $this->ExploreReportModel->sql->commitDb())
        {
            $status = 200;
            $msg    = __('success');
        }
        else
        {   
            $this->ExploreReportModel->sql->rollBackDb();
            $status = 250;
            $msg    = __('failure');
        }

        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
        
    }

   /**
     * 操作单条举报信息，删除
     *
     * @access public
     */
    public function delExplore()
    {
        $edit_row = array();

        $report_id                       = request_int('report_id');

        
        $flag = $this->ExploreReportModel->removeReport($report_id);

        if ($flag)
        {
            $status = 200;
            $msg    = __('success');
        }
        else
        {
            $status = 250;
            $msg    = __('failure');
        }

        $data = array();

        $this->data->addBody(-140, $data);
        
    }


    //导出用户信息
    public function getInfoExcel()
    {
        $search_name = request_string("search_name");
        $user_type = request_int("user_type");
        $shop_source = request_int("shop_source");
        $limit = request_int("limit");
        $start_limit = request_int("start_limit");
        $is_limit = request_int("is_limit");
        $cond_row = array();
        if ($search_name)
        {
            $cond_row['search_name'] = $search_name;
        }
        if ($user_type)
        {
            $cond_row['user_type'] = $user_type;
        }
        if ($shop_source)
        {
            $cond_row['shop_source'] = $shop_source;
        }
        if ($limit)
        {
            $cond_row['limit'] = $limit;
        }
        if ($limit)
        {
            $cond_row['start_limit'] = $start_limit;
        }
        if ($is_limit)
        {
            $cond_row['is_limit'] = $is_limit;
        }
        $User_InfoModel = new User_InfoModel();
        $limits = $User_InfoModel->getCounts();
        if($is_limit)
        {
            $this->export($cond_row);
        }else{
            //保存地址
            $path = ROOT_PATH . '/shop/data/download/';
            $file_template = $path . time();//临时文件
            $url = $file_template . '/';
            for ($i = 0; $i < $limits; $i++) {
                $cond_row['limits'] = $i;
                $this->export($cond_row, $i, $url);
            }
            //打包
            $zip = new ZipArchive();
            $down_name = 'user_info.zip';
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
        $User_InfoModel = new User_InfoModel();
        $con = $User_InfoModel->getUserInfoExcel($cond_row);
        $tit = array(
            "序号",
            "会员ID",
            "会员名称",
            "会员邮箱",
            "会员手机",
            "会员性别",
            "真实姓名",
            "出生日期",
            "注册时间",
            "商家类型",
            "最后登录时间",
        );
        $key = array(
            "user_id",
            "user_name",
            "user_email",
            "user_mobile",
            "user_sex",
            "user_realname",
            "user_birthday",
            "user_regtime",
            "shop_type",
            "lastlogintime",
        );
        if(isset($i)&& is_numeric($i)){

            $this->download_excel("会员信息".$i, $tit, $con, $key,$url);

        }else{
            $this->excel("会员信息", $tit, $con, $key);
        }
    }

    public function download_excel($title,$tit,$con,$key,$url){
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
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T'
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

                $objPHPExcel->getActiveSheet()->setCellValue($letter[$k2 + 1] . ($k + 2), $v[$v2]);
            }
        }
        ob_end_clean();   //***这里再加一个
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        if (!file_exists($url) && !mkdir($url, 0777, true)) {
            return false;
        }
        $url = $url.time().'.xls';
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
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T'
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

                $objPHPExcel->getActiveSheet()->setCellValue($letter[$k2 + 1] . ($k + 2), $v[$v2]);
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