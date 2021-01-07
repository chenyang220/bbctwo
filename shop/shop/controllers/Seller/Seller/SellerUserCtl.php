<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author
 * 商家公众号
 */
class Seller_Seller_SellerUserCtl extends Seller_Controller
{

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

    public function user(){


        $User_InfoModel = new User_InfoModel();
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        $cond_row = array();
        $cond_row['user_is_shop'] = Perm::$shopId;
          
        if (request_int('user_id')) {
            $cond_row['user_id'] = request_int('user_id');
        }

        if (request_string('user_name')) {
            $cond_row['user_name'] = request_string('user_name');
        }
        
        $data = $User_InfoModel->listByWhere($cond_row, array(), $page, $rows);
        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
      
        include $this->view->getView();
    }
    
    public function points(){
        $user_id = request_int('user_id');
        $user_name = request_string('user_name');
        $User_InfoModel = new User_InfoModel();
        $Points_LogModel = new Points_LogModel();
        $cond_row = array();
        $cond_row['user_is_shop'] = Perm::$shopId;
        $user_list = $User_InfoModel->getByWhere($cond_row);
        $user_ids = array_column($user_list, 'user_id');

        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        $row = array();
        $row['user_id:IN'] = $user_ids;
        if($user_id){
            $row['user_id'] = $user_id;
        }
        if($user_name){
            $row['user_name'] = $user_name;
        }
        $data = $Points_LogModel->listByWhere($row,array(),$page,$rows);
        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
       
        include $this->view->getView();
    }

    public function label(){
        $label_id = request_int('label_id');
        $label_name = request_string('label_name');
        $cond_row = array();
        if($label_id){
            $cond_row['label_id'] = $label_id;
        }
        if($label_name){
            $cond_row['label_name'] = $label_name;
        }
        $cond_row['shop_id'] = Perm::$shopId;
        $User_LabelModel = new User_LabelModel();
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $data = $User_LabelModel->listByWhere($cond_row,array(),$page,$rows);
        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
        include $this->view->getView();
    }

    public function editUser(){
        $user_id = request_int('user_id');
        $User_InfoModel = new User_InfoModel();
        $data = $User_InfoModel->getOne($user_id);
        //查找会员标签
        $User_LabelModel = new User_LabelModel();
        $label = $User_LabelModel->getByWhere();
        include $this->view->getView();

    }


    public function editUsers(){
        $user_id = request_int('user_id');
        $User_InfoModel = new User_InfoModel();
        $User_LabelModel = new User_LabelModel();
        $cond_row = array();
        $cond_row['user_sex'] = request_string('user_sex')=='男'? 1 :2;
        $cond_row['user_label_id'] = request_string('user_label');
        $cond_row['user_realname'] = request_string('user_realname');
        $cond_row['user_birthday'] = request_string('user_birthday');

        $label = $User_LabelModel->getOne($cond_row['user_label_id']);
        $cond_row['user_label_name'] = $label['label_name'];
        $flag = $User_InfoModel->editInfo($user_id,$cond_row);
        if($flag){
            $status = 200;
            $msg = '编辑成功';
        }else{
            $status = 250;
            $msg = '编辑失败';
        }

        $this->data->addBody(-140,array(),$msg,$status);
    }


    public function addLabel(){
        include $this->view->getView();
    }


    public function addLabels(){
        $shop_id = Perm::$shopId;
        $cond_row = array();

        $User_LabelModel = new User_LabelModel();

        $cond_row['label_name'] = request_string('label_name');
        $cond_row['label_sort'] = request_string('label_sort');
        $cond_row['label_desc'] = request_string('label_desc');
        $cond_row['label_img'] = request_string('label_img');
        $cond_row['shop_id'] = $shop_id;

        $flag = $User_LabelModel->addLabel($cond_row);

        if($flag){
            $status = 200;
            $msg = '添加成功';
        }else{
            $status = 250;
            $msg = '添加失败';
        }

        $this->data->addBody(-140,array(),$msg,$status);
    }


    public function editLabel(){
        $label_id = request_int('label_id');
        $User_LabelModel = new User_LabelModel();
        $data = $User_LabelModel->getOne($label_id);
        include $this->view->getView();
    }


    public function editLabels(){

        $label_id = request_int('label_id');

        $cond_row = array();

        $User_LabelModel = new User_LabelModel();

        $cond_row['label_name'] = request_string('label_name');
        $cond_row['label_sort'] = request_string('label_sort');
        $cond_row['label_desc'] = request_string('label_desc');
        $cond_row['label_img'] = request_string('label_img');

        $flag = $User_LabelModel->editLabel($label_id,$cond_row);

        if($flag){
            $status = 200;
            $msg = '添加成功';
        }else{
            $status = 250;
            $msg = '添加失败';
        }

        $this->data->addBody(-140,array(),$msg,$status);
    }

    public function removeLabel(){
        $label_id = request_int('label_id');

        $User_LabelModel = new User_LabelModel();

        $flag = $User_LabelModel->removeLabel($label_id);

        if($flag){
            $status = 200;
            $msg = '删除成功';
        }else{
            $status = 250;
            $msg = '删除失败';
        }

        $this->data->addBody(-140,array(),$msg,$status);
    }


     public function getUserExcel()
    {
        ob_get_clean();
        $user_id = request_int('user_id');
        $user_name = request_string('user_name');
        $shop_id = Perm::$shopId;

        //导出类型(0:分页导出，1：全部导出)
        $type = 1;
        $cond_row = array();
        if ($user_id) {
            $cond_row['user_id'] = $user_id;
        }
        if ($user_name) {
            $cond_row['user_name'] = $user_name;
        }
        if($shop_id){
            $cond_row['user_is_shop'] = $shop_id;
        }
       
        if($type){
            $header = array(
                
                "会员ID",
                "会员名称",
                "会员邮箱",
                "会员手机号",
                "会员性别",
                "会员标签",
                "真实姓名",
                "出生日期",
                "注册日期",
                "最后登录时间",
               
            );
            $User_InfoModel = new User_InfoModel();
            $data = $User_InfoModel->getUserInfoExcel($cond_row,true);
            $datas = array();
            foreach ($data as $key => $value) {
                $datas[$key]['user_id'] = $value['user_id'];
                $datas[$key]['user_name'] = $value['user_name'];
                $datas[$key]['user_email'] = $value['user_email'];
                $datas[$key]['user_mobile'] = $value['user_mobile'];
                $datas[$key]['user_sex'] = $value['user_sex'];
                $datas[$key]['user_label_name'] = $value['user_label_name'];
                $datas[$key]['user_realname'] = $value['user_realname'];
                $datas[$key]['user_birthday'] = $value['user_birthday'];
                $datas[$key]['user_regtime'] = $value['user_regtime'];
                $datas[$key]['lastlogintime'] = $value['lastlogintime'];
            }
            exportExcel($header,$datas);
            die('导出成功！');
        }
       
        if ($is_limit) {
            $this->export($cond_row);
        } else {
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
}
