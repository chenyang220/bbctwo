<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author
 * 商家公众号
 */
class Seller_Seller_SellerWxCtl extends Seller_Controller
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

    public function index(){
        $this->showType();
        $edit = request_int('edit');
        $Seller_SellerWxModel= new Seller_SellerWxModel();
        $seller_wx_data=$data=$Seller_SellerWxModel->getOneByWhere(array('shop_id'=>Perm::$shopId));
        $Seller_SellerWxListModel= new Seller_SellerWxListModel();
        $seller_wx_info=$info=$Seller_SellerWxListModel->getOneByWhere(array('shop_id'=>Perm::$shopId));

        //获取公众号价格和支付信息
        $sellerWx_price=Web_ConfigModel::value("sellerWx_price");//一年价格
        $sellerWx_day=Web_ConfigModel::value("sellerWx_day");//过期天数
        $sellerWx_alicode=Web_ConfigModel::value("sellerWx_alicode");//支付宝收款码
        $sellerWx_wxcode=Web_ConfigModel::value("sellerWx_wxcode");//微信收款码
        $sellerWx_user=Web_ConfigModel::value("sellerWx_user");//收款人
        $sellerWx_number=Web_ConfigModel::value("sellerWx_number");//银行账号
        $sellerWx_bank=Web_ConfigModel::value("sellerWx_bank");//开户行
        $sum_price =  $sellerWx_price*$data['years'];
        $years = isset($data['years'])?$data['years']:1;

        $y = substr($data['end_time'],0,4);
        $m = substr($data['end_time'],5,2);
        $d = substr($data['end_time'],8,2);
        
        $yy = date('Y');
        $mm = date('m');
        $dd = date('d');
        
        $ddd = $d-$dd;
        $expire = 0;
        if($y==$yy&&$m=$mm&&$ddd<$sellerWx_day){
            $expire = 1;
        }

        if(empty($data)||$data['step']==1){
            $this->view->setMet('index_type');
        }elseif($data['step']==2){
            $this->view->setMet('index_type');
        }elseif($data['step']==3){
            $this->view->setMet('index_type');
        }elseif($data['status']!=2&&$data['step']==4){
            $this->view->setMet('index_type');
        }elseif($edit){
            $data['step'] = 1;
            $this->view->setMet('index_type');
        }
        else{
            $this->view->setMet('index');
        }

        // if(empty($data) || $data['status']==0){
        //     $this->view->setMet('index_type_1');
        // }elseif($data['show_type']==0&&$data['status']!=0){
        //     $this->view->setMet('index_type_2');
        // }else{
        //     $this->view->setMet('index'); 
        // }
        include $this->view->getView();
    }
    //公众号菜单列表
    public function wxMenu(){
        $Seller_SellerWxModel= new Seller_SellerWxModel();
        $seller_wx_data=$Seller_SellerWxModel->getOneByWhere(array('shop_id'=>Perm::$shopId));
        $Seller_SellerWxListModel= new Seller_SellerWxListModel();
        $seller_wx_info=$Seller_SellerWxListModel->getOneByWhere(array('shop_id'=>Perm::$shopId));

        $Seller_SellerWxMenuModel= new Seller_SellerWxMenuModel();
        $ret=$Seller_SellerWxMenuModel->getByWhere(array('shop_id'=>Perm::$shopId));
        $data = array();
        if (!empty($ret))
        {
            foreach ($ret as $k => $item){
                $item['menu_type'] = WxPublic_MenuModel::$map[$item['menu_type']];
                if($item['parent_menu_id']){
                    $result  = $Seller_SellerWxMenuModel->getOne($item['parent_menu_id']);
                    $result && $ret[$k]['parent_menu_name'] = $result['menu_name'];
                }
                !$item['parent_menu_id'] && $ret[$k]['parent_menu_name'] = '一级菜单';
            }
            $data = $ret;
        }
        if($_REQUEST['action']=='edit'){
            $wxpublic_menu_id=request_int("id");
            $menu_list=$Seller_SellerWxMenuModel->getOne($wxpublic_menu_id);
           $this->view->setMet('wx_menu_edit'); 
        }elseif($_REQUEST['action']=='add'){
            $this->view->setMet('wx_menu_add');
        }else{
            $this->view->setMet('wx_menu');
        }
        include $this->view->getView();
    }
    //公众号菜单保存
    public function saveSellerWxMenu(){
        $Seller_SellerWxMenuModel= new Seller_SellerWxMenuModel();
        $wxpublic_menu_id = request_int("wxpublic_menu_id");
        $con_row['menu_name'] = request_string("menu_name");
        $con_row['shop_id'] = Perm::$shopId;
        $con_row['sort_num'] = request_string("sort_num"); 
        $con_row['parent_menu_id'] = request_int("menu_level");
        $con_row['menu_type'] = request_int("menu_type");
        $con_row['operate_time'] = date('Y-m-d h:i:s', time());
        if(!$con_row['sort_num'] || $con_row['sort_num']>100){
            $msg    = '对不起，请输入正确的排序值！[1-100]之间';
            $status = 250;
            $datas = array();
            return $this->data->addBody(-140, $datas, $msg, $status);
        }
        if(!$con_row['menu_name']){
            $msg    = '菜单名称不能为空！';
            $status = 250;
            $datas = array();
            return $this->data->addBody(-140, $datas, $msg, $status);
        }
        
        $flag =false;
        switch ($con_row['menu_type']){
            case 1:
                $content = request_string('content');
                !$content && $flag = true;
                $content && $con_row['menu_msg'] = $content;
                break;
            case 2:
                $redirect_url = request_string('redirect_url');
                !$redirect_url && $flag = true;
                $redirect_url && $con_row['menu_url'] = $redirect_url;
                break;
            default:
                break;
        }
        if($flag){
            $msg    = '参数不能为空！';
            $status = 250;
            $datas = array();
            return $this->data->addBody(-140, $datas, $msg, $status);
        }
        $data['menu_name'] = $menu_name;
        if($wxpublic_menu_id){
            $flag=$Seller_SellerWxMenuModel->editSellerWxMenu($wxpublic_menu_id,$con_row);
            if($flag){
                $msg ='保存成功！';
                $status = 200; 
            }else{
                $msg = '保存失败！';
                $status = 250;
            }           
        }else{
            //查询菜单名称是否存在
            $sql ="select * from ".$Seller_SellerWxMenuModel->_tableName." where 1=1 and menu_name='{$con_row['menu_name']}' and shop_id='{$con_row['shop_id']}'";
            $one = $Seller_SellerWxMenuModel->sql->getRow($sql);
            if($one){
                $msg    = '对不起，菜单名称已存在！';
                $status = 250;
                $datas = array();
                return $this->data->addBody(-140, $datas, $msg, $status);
            }
            $flag=$Seller_SellerWxMenuModel->addSellerWxMenu($con_row);
            if($flag){
                $msg ='保存成功！';
                $status = 200; 
            }else{
                $msg = '保存失败！';
                $status = 250;
            }
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    //删除公众号菜单
    public function removeSellerWxMenu(){
        $wxpublic_menu_id = request_int("wxpublic_menu_id");
        $Seller_SellerWxMenuModel= new Seller_SellerWxMenuModel();
        $menu=$Seller_SellerWxMenuModel->getOneByWhere(array('shop_id'=>Perm::$shopId,'wxpublic_menu_id'=>$wxpublic_menu_id));
        if($menu){
            $flag = $Seller_SellerWxMenuModel->removeSellerWxMenu($wxpublic_menu_id);
            if($flag){
                $msg ='删除成功！';
                $status = 200; 
            }else{
                $msg = '删除失败！';
                $status = 250;
            }
        }else{
            $msg = '删除失败,未找到菜单数据！';
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    //微信公众号同步菜单
    public function wxPublicCreateMenu(){
        //微信按钮类型
        $mnu_map = array(
            1=>'click',//菜单的响应动作类型===>发送消息
            2=>'view',//网页跳转
        );
        $shop_id=Perm::$shopId;
        //第二步 创建菜单
        $menu_mdl = new Seller_SellerWxMenuModel();
        $sql = 'select * from '.$menu_mdl->_tableName." where parent_menu_id=0 and shop_id='{$shop_id}' order by sort_num,operate_time desc limit 3";
        $result  =  $menu_mdl->sql->getAll($sql);
        $objectArr = array();
        foreach ($result as $item){
            $arr = array(
                "name"=>$item['menu_name']
            );
            //判断是否有二级菜单
            $itemArr = array();
            if($item['wxpublic_menu_id']){
                $sql = 'select * from '.$menu_mdl->_tableName." where parent_menu_id='{$item['wxpublic_menu_id']}' and shop_id='{$shop_id}' limit 5";
                $ret  =  $menu_mdl->sql->getAll($sql);
                foreach ($ret  as $val){
                    $valArr = array(
                        "name"=>$val['menu_name']
                    );
                    switch ($val['menu_type']){
                        case 1://clickclick
                            $valArr['key'] = md5($val['wxpublic_menu_id']);
                            break;
                        case 2://view
                            $valArr['url'] = $val['menu_url'];
                            break;
                    }
                    $valArr['type']=$mnu_map[$val['menu_type']];
                    $itemArr[] = $valArr;
                }
                $itemArr && $arr['sub_button'] = $itemArr;
                if(!$itemArr){
                    $arr['type']=$mnu_map[$item['menu_type']];
                    switch ($item['menu_type']){
                        case 1://clickclick
                            $arr['key'] = md5($item['wxpublic_menu_id']);
                            break;
                        case 2://view
                            $arr['url'] = $item['menu_url'];
                            break;
                    }
                }
            }
            $objectArr[] = $arr;
        }
        $data=array(
            'button' =>$objectArr
        );
        //调用获取token
        $Seller_SellerWxListModel= new Seller_SellerWxListModel(); 
        $token  = $Seller_SellerWxListModel->getWxPublicAccessToken(Perm::$shopId);
        if(!$token['token']){
            $status = 250;
            $msg    = __('token获取失败！');
            $data = array();
            return $this->data->addBody(-140, $data, $msg, $status);
        }
        $ret = wxpublic_create_menu($data,$token['token']);
        if ($ret['errcode']=='0' && $ret['errmsg']=="ok")
        {
            $status = 200;
            $msg    = __('同步成功！');
        }
        else
        {
            $status = 250;
            $msg    = __('同步失败！');
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    //自动回复
    public function wxCallback(){
        $Seller_SellerWxModel= new Seller_SellerWxModel();
        $seller_wx_data=$Seller_SellerWxModel->getOneByWhere(array('shop_id'=>Perm::$shopId));
        $Seller_SellerWxListModel= new Seller_SellerWxListModel();
        $seller_wx_info=$Seller_SellerWxListModel->getOneByWhere(array('shop_id'=>Perm::$shopId));

        $Seller_SellerWxMsgModel = new Seller_SellerWxMsgModel();
        $data=$Seller_SellerWxMsgModel->getByWhere(array('shop_id'=>Perm::$shopId));  
        if($_REQUEST['action']=='add'){
            $this->view->setMet('wx_callback_add'); 
        }elseif ($_REQUEST['action']=='edit') {
            $wxpublic_message_id=request_int("id");
            $msg_list=$Seller_SellerWxMsgModel->getOne($wxpublic_message_id);
            $this->view->setMet('wx_callback_edit');
        }else{
            $this->view->setMet('wx_callback');
        }
        include $this->view->getView();
    }

    //自动回复新增回复
    public function saveSellerWxMsg(){
        $wxpublic_message_id=request_int("wxpublic_message_id");
        $Seller_SellerWxMsgModel = new Seller_SellerWxMsgModel();
        $cond_row['words'] = request_string("words");
        $cond_row['match_type'] = request_int("match_type");
        $cond_row['msg_type'] = request_int("msg_type");
        $cond_row['content'] = request_string("content");
        $cond_row['shop_id'] = Perm::$shopId;
        $cond_row['create_time'] = date('Y-m-d h:i:s', time());
        if(!$cond_row['words'] || !$cond_row['match_type'] || !$cond_row['msg_type'] || !$cond_row['content']){
            return $this->data->addBody(-140, array(), __('参数不能为空'), 250);
        }
        //名称不超过25个字符
        if(mb_strlen($cond_row['words'],'utf-8') > 50){
            return $this->data->addBody(-140, array(), __('关键词不能超过50个字符'), 250);
        }
        if($wxpublic_message_id){
            $flag=$Seller_SellerWxMsgModel->editSellerWxMsg($wxpublic_message_id,$cond_row);
            if($flag){
                $msg ='保存成功！';
                $status = 200; 
            }else{
                $msg = '保存失败！';
                $status = 250;
            }
        }else{
            $flag=$Seller_SellerWxMsgModel->addSellerWxMsg($cond_row);
            if($flag){
                $msg ='保存成功！';
                $status = 200; 
            }else{
                $msg = '保存失败！';
                $status = 250;
            }
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    //删除自动回复
    public function removeSellerWxMsg(){
        $wxpublic_message_id = request_int("wxpublic_message_id");
        $Seller_SellerWxMsgModel = new Seller_SellerWxMsgModel();
        $menu=$Seller_SellerWxMsgModel->getOneByWhere(array('shop_id'=>Perm::$shopId,'wxpublic_message_id'=>$wxpublic_message_id));
        if($menu){
            $flag = $Seller_SellerWxMsgModel->removeSellerWxMsg($wxpublic_message_id);
            if($flag){
                $msg ='删除成功！';
                $status = 200; 
            }else{
                $msg = '删除失败！';
                $status = 250;
            }
        }else{
            $msg = '删除失败,未找到数据！';
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    //行业设置
    public function wxIndustrySetting(){
        array('shop_id'=>Perm::$shopId);
		//载入微信公众号行业配置文件
		$industry_ini = array();
		$industry_ini_file =  INI_PATH . '/industry.ini.php';
		if(is_file($industry_ini_file)){
			$industry_ini = include_once  $industry_ini_file;
		}
        //行业配置数组
		$data = $industry_ini;
        $this->view->setMet('wx_industry_setting');
        include $this->view->getView();
    }
	

	/**
	*
	* 商家微信公众行业设置
	* @nsy 2020-03-24 修改
	*/
    public function saveIndustrySetting(){
		$shop_id = Perm::$shopId;
		if(!$shop_id){
			return $this->data->addBody(-140, array(), "参数异常!", 250);
		}
        $main_industry=request_string('main_industry');
        $sub_industry=request_string('sub_industry');
		if($main_industry==$sub_industry){
			return $this->data->addBody(-140, array(), "主行业和副行业不能相同！", 250);
		}
        //设置行业同步至微信公众号
        $arr = array(
            'industry_id1' => $main_industry,
			'industry_id2' => $sub_industry
        );
        $result = $this->sync_industry($arr);
		$msg = "微信公众号所属行业设置失败";
		$status = 250;
		$time = time();
		//设置成功
        if($result['errcode']=='0' && $result['errmsg']=='ok'){
			//组装保存sql
			$save_sql = "INSERT INTO yf_seller_wxpublic_industry 
				(shop_id,industry_id1,industry_id2,opt_time) 
				VALUES ({$shop_id},{$main_industry},{$sub_industry},{$time}) 
				ON DUPLICATE KEY UPDATE shop_id={$shop_id},industry_id1={$main_industry},industry_id2={$sub_industry},opt_time={$time};";
			(new CommonModel())->sql->exec($save_sql);
			$msg = "微信公众号所属行业设置成功";
			$status = 200;
		}
        $this->data->addBody(-140, array(), $msg, $status);
    }
	
    //行业设置保存
    public function saveIndustrySetting_base(){
        $main_industry=request_string('main_industry');
        $sub_industry=request_string('sub_industry');
        $Seller_SellerWxIndustryModel= new Seller_SellerWxIndustryModel();
        $list=$Seller_SellerWxIndustryModel->getOneByWhere(array('shop_id'=>Perm::$shopId));
        //设置行业同步至微信公众号
        $arr = array(
            'industry_id1'=>$main_industry
        );
        $sub_industry &&  $arr['industry_id2'] = $sub_industry;
        $flag1 = $this->sync_industry($arr);
        if($flag1){
            if($list){
                $flag=$Seller_SellerWxIndustryModel->editSellerWxIndustry($list['wxpublic_industry_id'],array('main_industry'=>$main_industry,'sub_industry'=>$sub_industry,'update_time'=>date('Y-m-d h:i:s', time())));
                if($flag){
                    $msg ='保存成功！';
                    $status = 200; 
                }else{
                    $msg = '保存失败！';
                    $status = 250;
                }
            }else{
                $flag=$Seller_SellerWxIndustryModel->addSellerWxIndustry(array('main_industry'=>$main_industry,'sub_industry'=>$sub_industry,'shop_id'=>Perm::$shopId,'update_time'=>date('Y-m-d h:i:s', time())));
                if($flag){
                    $msg ='保存成功！';
                    $status = 200; 
                }else{
                    $msg = '保存失败！';
                    $status = 250;
                }
            }
        }else{
            $msg='所属行业每个月可设置一次，本次操作失败！';
            $status = 250;
        }
        
        $this->data->addBody(-140, array(), $msg, $status);
    }
    //公众号申请
    public function saveApplication(){
        $step = request_int('step');
        
        $Seller_SellerWxModel= new Seller_SellerWxModel();
        $shopModel = new Shop_BaseModel();
        $shop=$shopModel->getOne(Perm::$shopId);
        $data=$Seller_SellerWxModel->getOneByWhere(array('shop_id'=>Perm::$shopId));

        if($step==1){
            $wechat_public_name=request_string('wechat_public_name');
            $year = request_int('year');
            if(empty($data)){
                $flag=$Seller_SellerWxModel->addSellerWx(array('shop_id'=>Perm::$shopId,'wx_public_name'=>$wechat_public_name,'shop_name'=>$shop['shop_name'],'step'=>2,'years'=>$year,'time'=>date('Y-m-d h:i:s', time())));
                if($flag){
                    $msg = __('提交申请成功！');
                    $status = 200;
                }else{
                    $msg = __('提交申请失败！');
                    $status = 250;
                }
            }else{
                //则修改
                $flag=$Seller_SellerWxModel->editSellerWx($data['id'],array('wx_public_name'=>$wechat_public_name,'step'=>2,'years'=>$year,'status'=>0,'time'=>date('Y-m-d h:i:s', time())));
                if($flag){
                    $msg = __('提交申请成功！');
                    $status = 200;
                }else{
                    $msg = __('提交申请失败！');
                    $status = 250;
                }
                
            }
        }else if($step==2){
            //更新步骤
            $flag = $Seller_SellerWxModel->editSellerWx($data['id'],array('step'=>3,'status'=>0));
            if($flag){
                    $msg = __('成功！');
                    $status = 200;
            }else{
                $msg = __('失败！');
                $status = 250;
            }
        }else{

            $pay_images = request_string('pay_images');
            //更新上传凭证和步骤信息
            $flag = $Seller_SellerWxModel->editSellerWx($data['id'],array('step'=>4,'pay_images'=>$pay_images,'status'=>0));
            if($flag){
                    $msg = __('成功！');
                    $status = 200;
            }else{
                $msg = __('失败！');
                $status = 250;
            }

        }
        
        $this->data->addBody(-140, array(), $msg, $status);
    }

    //清除申请数据
    public function removeApplication(){
       $Seller_SellerWxModel= new Seller_SellerWxModel();
       $data= $Seller_SellerWxModel->getOneByWhere(array('shop_id'=>Perm::$shopId,'status'=>1)); 
       if($data){
           $flag= $Seller_SellerWxModel->removeSellerWx($data['id']);
           if($flag){
               $msg = '提交申请成功！';
               $status = 200; 
           }else{
               $msg = '提交申请失败！';
               $status = 250; 
           }
       }else{
            $msg = '未找到信息';
            $status = 250;
       }
       $this->data->addBody(-140, array(), $msg, $status);
    }

    public function showType(){
        $Seller_SellerWxModel= new Seller_SellerWxModel();
        $data= $Seller_SellerWxModel->getOneByWhere(array('shop_id'=>Perm::$shopId,'status'=>2));
        if($data){
           $flag= $Seller_SellerWxModel->editSellerWx($data['id'],array('show_type'=>1));
           if($flag){
               $msg = '提交申请成功！';
               $status = 200; 
           }else{
               $msg = '提交申请失败！';
               $status = 250; 
           }
       }else{
            $msg = '未找到信息';
            $status = 250;
       }
       $this->data->addBody(-140, array(), $msg, $status);
    }

    //保存商家公众号信息
    public function saveSellerWxList(){
        $cond_row=array();
        $cond_row['shop_id']=Perm::$shopId;
        $cond_row['seller_wxpublic_status'] =request_int('seller_wxpublic_status');
        $cond_row['wechat_public_name'] =request_string('wechat_public_name');
        $cond_row['wechat_public_start_id'] =request_string('wechat_public_start_id');
        $cond_row['wechat_public_wxaccount'] =request_string('wechat_public_wxaccount');
        $cond_row['wechat_public_call_url'] =request_string('wechat_public_call_url');
        $cond_row['wechat_public_token'] =request_string('wechat_public_token');
        $cond_row['wechat_public_appid'] =request_string('wechat_public_appid');
        $cond_row['wechat_public_secret'] =request_string('wechat_public_secret');
        $Seller_SellerWxListModel= new Seller_SellerWxListModel();
        $info=$Seller_SellerWxListModel->getOneByWhere(array('shop_id'=>Perm::$shopId));
        $Seller_SellerWxModel= new Seller_SellerWxModel();
        $data=$Seller_SellerWxModel->getOneByWhere(array('shop_id'=>Perm::$shopId));
        if(!$cond_row['wechat_public_token']){
            $str = date('Y-m-d h:i:s',time());
            $strArr =  str_split($str,1);
            $number = '';
            foreach ($strArr as $letter) {
              $number = $number . ord($letter);
            }
            $cond_row['wechat_public_token'] =substr($number,0,31);
        }
        if(!$cond_row['wechat_public_call_url'] ) {
            $cond_row['wechat_public_call_url'] = (isHttps() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']."/index.php?ctl=WxPublicTool_SellerWx&met=index&t=".Perm::$shopId;
        }
        if($info){
            $flag=$Seller_SellerWxListModel->editSellerWxList($info['seller_wxpublic_id'],$cond_row);
            if($flag){
                $msg = '保存成功！';
                $status = 200;
            }else{
                $msg = '保存失败！';
                $status = 251;
            }            
        }else{
            $flag=$Seller_SellerWxListModel->addSellerWxList($cond_row);
            if($flag){
                $msg = '保存成功！';
                $status = 200;
            }else{
                $msg = '保存失败！';
                $status = 252;
            }
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    /**
     *
     * 同步行业设置到微信公众号
     */
    public function sync_industry($data){
        $Seller_SellerWxListModel= new Seller_SellerWxListModel(); 
        $token  = $Seller_SellerWxListModel->getWxPublicAccessToken(Perm::$shopId);
        $result = wxpublic_set_industry($token['token'],$data);
        if($result['errcode']=='0' && $result['errmsg']=='ok'){
            return true;
        }else{
            return false;
        }
    }
}
