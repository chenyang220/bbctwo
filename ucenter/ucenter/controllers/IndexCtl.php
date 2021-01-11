<?php
if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class IndexCtl extends Yf_AppController
{
    /**
     * Author:  Michael
     * Notes:   UCenter入口判断是否登录，需要跳转的页面
     * Date:    2018年8月29日22:05:58
     *
     *
     * PS:      本框架不能实现 跨控制器访问 不同的方法[有同控制器访问不同方法  即：setMet('该控制器下的方法名称')]
     *          故直接使用location_to跳转到某一个指定页面,如果需要携带参数,请自行以get方式在后面拼接 ? or & 参数名和参数值
     *          除非你用curl...
     *
     * Notice:  不要在跳转 de URL 内拼接 './index.php' 之类的字符串,以免其他接口的请求误入...乱入...
     *          eg:https://ucenter.local.yuanfeng021.com/ucenter/data/upload/images/1535545013.png
     *          跳转这个url的时候,入口文件就会先从images往后拼接'./index.php'...接着拼接你的页面控制器和方法...
     *          很显然 /ucenter/data/upload/images/image 这个东西只是个文件/图片的路径，却被误解析成了 项目路径...
     *          所以，请使用框架内封装好的方法,Yf_Registry::get('XXXX');
     *
     * @throws \Exception
     */
    public function index()
    {
        // 如果 用户已经登录了，就直接跳转至getUserInfo界面
        if (Perm::checkUserPerm()) {
            // 真区间就直接去getUserInfo界面
            $url = Yf_Registry::get('url') . '?ctl=User&met=getUserInfo';
        } else {
            // 如果 用户尚未登录，就直接跳转至 login index界面
            $url = Yf_Registry::get('url') . '?ctl=Login';
            // 检查u和k  是否存在回调地址,登录成功后还返回  登录回调的地址
            if (isset($_REQUEST['callback']) && $_REQUEST['callback']) {
                $k = $_COOKIE[Perm::$cookieName];
                $u = $_COOKIE[Perm::$cookieId];
                // 重新赋值给 $url
                $url = $_REQUEST['callback'] . '&us=' . $u . '&ks=' . urlencode($k);
            }
        }
        location_to($url);
    }
    
    public function main()
    {
        include $this->view->getView();
    }

    public function getBindInfo()
    {
        $user_id = request_int('user_id');
        $User_BindConnectModel = new User_BindConnectModel();
        $user_info = $User_BindConnectModel->getOneByWhere(array('user_id' => $user_id));
        $this->data->addBody(-140, $user_info);
    }
    
    public function img()
    {
        $user_id = request_int('user_id');
        if ($user_id) {
            $User_InfoModel = new User_InfoModel();
            $user_row = $User_InfoModel->getOne($user_id);
            if ($user_row) {
                $User_InfoDetailModel = new User_InfoDetailModel();
                $user_info_row = $User_InfoDetailModel->getOne($user_row['user_name']);
                //原图
                if ($user_info_row['user_avatar']) {
                    location_to($user_info_row['user_avatar']);
                } else {
                    $this->get_avatar();
                }
            } else {
                $this->get_avatar();
            }
        } else {
            $this->get_avatar();
        }
    }
    
    /**
     * 默认头像设置
     */
    protected function get_avatar()
    {
        $img_url = Web_ConfigModel::value('user_default_avatar');
        $host = $_SERVER['HTTP_HOST'] ? :$_SERVER['SERVER_NAME'];
        if ($img_url && strpos($img_url, $host) !== false) {
        } else {
            $img_url = Yf_Registry::get('static_url') . '/images/default_user_portrait.gif';
        }
        location_to($img_url);
    }

    /**
     * 默认头像设置
     */
    protected function getUserAvatar()
    {
        $img_url = Web_ConfigModel::value('user_default_avatar');
        $data['image'] = $img_url;
        $this->data->addBody(-140, $data);
    }

    //shop_admin获取用户列表
    public function getUserList()
    {
        $page = $_REQUEST['page'];
        $rows = $_REQUEST['rows'];
        $user_for = $_REQUEST['user_for'];
        $app_id = $_REQUEST['app_id'];

        $User_InfoModel = new User_InfoModel();
        if($user_for){
            $cond_row['user_for'] = $user_for;
        }else{
            $cond_row['user_for:IN'] = array('UCenter', 'PayCenter');
        }
        $data = $User_InfoModel->getInfoList($cond_row,array(),$page,$rows);
        return $this->data->addBody(-140, $data, 'success', 200);
    }

    //添加、编辑
    public function addOrEditAccountInfo()
    {
        $user_id = $_REQUEST['user_id'];

        $cond_row = array();
        $user_name = $_REQUEST['user_name'];
        if($user_name){
            $cond_row['user_name'] = $user_name;
        }
        $password = $_REQUEST['password'];
        if ($password) {
            $cond_row['password'] = md5($password);
        }

        $User_InfoModel = new User_InfoModel();
        if($user_id) {
            //编辑
            if($cond_row){
                $flag = $User_InfoModel->editInfo($user_id,$cond_row);
            }else{
                $flag = true;
            }
        }else{
          //添加
            if($user_name){
                $info = $User_InfoModel->getOneByWhere(array('user_name'=> $user_name));
                if($info){
                    $flag = false;
                    $msg = '用户名已存在';
                }else{
                    $Db = Yf_Db::get('ucenter');
                    $seq_name = 'user_id';
                    $user_id = $Db->nextId($seq_name);
                    $cond_row['user_id'] = $user_id;
                    $flag = $User_InfoModel->addInfo($cond_row, true);
                }
            }
        }
        $data['id'] = $flag;
        $data['user_id'] = $user_id;
        $data['row'] = $cond_row;
        if($flag !== false){
            $msg = 'success';
            $status = 200;
        }else{
            $msg = $msg?$msg:'failure';
            $status = 250;
        }
        return $this->data->addBody(-140, $data, $msg, $status);
    }

    //用户信息
    public function getUserInfo()
    {
        $user_id = $_REQUEST['user_id'];
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOne($user_id);
        return $this->data->addBody(-140, $user_info,'success',200);
    }

    //删除用户
    public function delAccount(){
        $id = $_REQUEST['id'];
        $sql = "DELETE FROM ucenter_user_info WHERE user_id={$id}";
        $User_InfoModel = new User_InfoModel();
        $res = $User_InfoModel->sql->getAll($sql);
        if ($res !== false) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        return $this->data->addBody(-140, array(), $msg, $status);
    }
      
}

?>