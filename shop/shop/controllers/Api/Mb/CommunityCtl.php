<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Api_Mb_CommunityCtl extends Api_Controller
{
    /**
     * Constructor
     * @param string $args 参数
     * @return void
     */
    public function __call($method, $args)
    {
        $config_type = $this->met;

        $config_type_row = request_row('config_type');

        if (!$config_type_row)
        {
            $config_type_row = array($config_type);
        }

        $Web_ConfigModel = new Web_ConfigModel();
        $sub_site_id = request_int('sub_site_id');
        $data = array();
        foreach ($config_type_row as $config_type)
        {
            if($config_type === 'slider' && $sub_site_id > 1){
                $data_tmp = $Web_ConfigModel->getByWhere(array('config_type' => $config_type.'_'.$sub_site_id));
            }else{
                $data_tmp = $Web_ConfigModel->getByWhere(array('config_type' => $config_type));
            }
            $data     = $data + $data_tmp;

            //系统环境上传变量
            if ('upload' == $config_type)
            {
                $sys_max_upload_file_size         = min(Yf_Utils_File::getByteSize(ini_get('upload_max_filesize')), Yf_Utils_File::getByteSize(ini_get('memory_limit')), Yf_Utils_File::getByteSize(ini_get('post_max_size'))) / 1024;
                $data['sys_max_upload_file_size'] = $sys_max_upload_file_size;
            }

            //商品设置中需要将默认商品图片也查出来
            if('goods' == $config_type)
            {
                $data_tmp = $Web_ConfigModel->getByWhere(array('config_type' => 'photo'));
                $data     = $data + $data_tmp;
            }

            //图片设置中需要将默认商品图片也查出来
            if('setting' == $config_type)
            {
                $data_tmp = $Web_ConfigModel->getByWhere(array('config_type' => 'photo'));
                $data     = $data + $data_tmp;
            }

            //站点设置
            if ('site' == $config_type)
            {
                //站点设置中将默认图片也查出来。后台电脑端基础设置和手机端基础设置需要上传图片
                $data_tmp = $Web_ConfigModel->getByWhere(array('config_type' => 'photo'));
                $data     = $data + $data_tmp;

                //系统可选语言包
                $file_row = scandir(LAN_PATH);

                $language_row = array();

                foreach ($file_row as $file)
                {
                    if ('.' != $file && '..' != $file && is_dir(LAN_PATH . '/' . $file))
                    {
                        $language_row[] = array(
                            'id' => $file,
                            'name' => $file
                        );
                    }
                }

                $data['language_row'] = $language_row;

                //系统可选风格
                $data['theme_row'] = array();
                $theme_dir         = APP_PATH . '/views/';
                $file_row          = scandir($theme_dir);

                $theme_row = array();

                foreach ($file_row as $file)
                {
                    if ('.' != $file && '..' != $file && is_dir($theme_dir . '/' . $file))
                    {
                        //判断风格
                        $file_path = $theme_dir . '/' . $file . '/config.php';
                        $theme_config = include $file_path;
                        if (file_exists($file_path) && SYS_TYPE==@$theme_config['theme_name'])
                        {
                            $theme_row[] = array(
                                'id' => $file,
                                'name' => $file,
                                'config' => $theme_config
                            );
                        }
                    }
                }
                $data['theme_row'] = $theme_row;
            }

            //插件设置
            if ('plugin' == $config_type)
            {
                $plugin_rows = array();
                //用户自定义
                $plugin_user_dir = APP_PATH . '/controllers/Plugin/';

                $file_row = scandir($plugin_user_dir);

                foreach ($file_row as $file)
                {
                    if ('.' != $file && '..' != $file && is_file($plugin_user_dir . '/' . $file))
                    {
                        $ext_row     = pathinfo($file);
                        $plugin_name = 'Plugin_' . $ext_row['filename'];

                        if ('Plugin_Perm' == $plugin_name)
                        {
                            continue;
                        }
                        try
                        {
                            if (class_exists($plugin_name))
                            {
                                $plugin_desc   = $plugin_name::desc();
                                $plugin_rows[] = array(
                                    'plugin_id' => $plugin_name,
                                    'plugin_name' => $plugin_name,
                                    'plugin_desc' => $plugin_desc
                                );
                            }
                        }
                        catch (Exception $e)
                        {

                        }
                    }
                }
                $data['plugin_rows'] = $plugin_rows;
            }


            //插件设置
            if ('sphinx' == $config_type)
            {
                if (extension_loaded("sphinx"))
                {
                    $data['sphinx_ext'] = 1;
                }
                else
                {
                    $data['sphinx_ext'] = 0;
                }

                if (extension_loaded("scws"))
                {
                    $data['scws_ext'] = 1;
                }
                else
                {
                    $data['scws_ext'] = 0;
                }
            }

            //
            //证书
            if ('licence' == $config_type)
            {
                //授权证书
                $licence_file = APP_PATH . '/data/licence/licence.lic';

                //本地检测, 为正常企业使用
                if (is_file($licence_file))
                {
                    $lic  = new Yf_Licence_Maker();
                    $licence_row = $lic->getData(file_get_contents($licence_file), file_get_contents(APP_PATH . '/data/licence/public.pem'));


                    $licence_row['company_name'] = $licence_row['license'];
                    if($licence_row['licence_domain']){
                        $licence_row['licence_effective_enddate'] = __('永久');
                    }

                    $licence_row['licence_domain'] = $licence_row['licence_domain'];
                    $licence_row['licence_key'] = file_get_contents($licence_file);

                    $data['licence'] = $licence_row;

                }
                else
                {

                    $licence_row['company_name'] = __('无');
                    $licence_row['licence_effective_enddate'] = __('无');
                    $licence_row['licence_domain'] = __('无');
                    $licence_row['licence_key'] = '';

                    $data['licence'] = $licence_row;
                }
            }
        }
        $this->data->addBody(-140, $data);
    }

    public function getExploreAllList(){
        $user_name = request_string('user_name');
        $explore_status = request_string('explore_status');
        if(request_string('user_name')){
           $cond_row['user_account'] = $user_name; 
        }
        if (request_string('explore_status') !== "") {
            $cond_row['explore_status'] = request_string('explore_status');
        } else {
            $cond_row['explore_status'] = 3;
        }
        $cond_row['is_del']=0;
        $page = request_int('page', 1);
        $rows = request_int('rows', 20);
        $Explore_BaseModel = new  Explore_BaseModel();
        $data = $Explore_BaseModel->getExploreListAll($cond_row, $order_row = array('explore_create_time' =>'DESC'), $page, $rows);
        $this->data->addBody(-140, $data);
    }

    public function getExploreList(){
        $explore_id =  request_int('explore_id');
        $Explore_BaseModel = new  Explore_BaseModel();
        $data = $Explore_BaseModel->getOne($explore_id);
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOne($data['user_id']);
        $data['user_mobile'] = $user_info['user_mobile'];
        //2.心得图片
        $Explore_ImagesModel = new Explore_ImagesModel();
        $explore_images = $Explore_ImagesModel->getByWhere(array('explore_id'=>$explore_id));
        $data['explore_images'] = array_values($explore_images);

        //5.心得商品
        $Explore_ImagesGoodsModel = new Explore_ImagesGoodsModel();
        $goods = $Explore_ImagesGoodsModel->getGoodsSimple($explore_id);
        $data['goods'] = $goods;
        if ($data) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('没有满足条件的结果哦');
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function editCommonVerify(){
        $explore_id =  request_int('explore_id');
        $explore_status = request_int('explore_status');
        $explore_verify_remark = request_string('explore_verify_remark');
        $Explore_BaseModel = new  Explore_BaseModel();
        $flag=$Explore_BaseModel->editBase($explore_id,array('explore_status'=>$explore_status,'explore_verify_remark'=>$explore_verify_remark));
        if ($flag) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('审核失败');
        }
        $this->data->addBody(-140, array(), $msg, $status);

    }
}