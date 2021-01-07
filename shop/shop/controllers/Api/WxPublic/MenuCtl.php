<?php
session_start();
if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */

class Api_WxPublic_MenuCtl extends Api_Controller
{
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
        $this->wxpublicMsgMdl = new WxPublic_MessageModel();
        $this->wxpublicMenuMdl =new WxPublic_MenuModel();
	}


	public function publicSet(){
        $Web_ConfigModel = new Web_ConfigModel();
        $data_tmp = $Web_ConfigModel->getByWhere(array('config_type' => 'wechat_public'));
        if(!$data_tmp['wechat_public_token']['config_value']){
            $data_tmp['wechat_public_token']['config_value'] = md5(time());
        }
        if(!$data_tmp['wechat_public_call_url']['config_value'] ) {
            $data_tmp['wechat_public_call_url']['config_value'] = (isHttps() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']."/index.php?ctl=WxPublicTool_Index&met=index&t=".time();
        }
        $this->data->addBody(-140,($data_tmp) );
    }
    /**
     *
     * 公众号菜单类表
     */
    public function menuList(){
        $page  = request_int('page', 1);
        $rows  = request_int('rows', 100);
        $order_row = array('sort_num'=>'desc','operate_time'=>'desc');
        $ret = $this->wxpublicMenuMdl->getPublicMenuList($cond_row=array(), $order_row, $page, $rows);
        $data = array();
        if ( !empty($ret) )
        {
            foreach ($ret['items'] as &$item){
                $item['menu_type'] = WxPublic_MenuModel::$map[$item['menu_type']];
                if($item['parent_menu_id']){
                    $result  = $this->wxpublicMenuMdl->getOne($item['parent_menu_id']);
                    $result && $item['parent_menu_id'] = $result['menu_name'];
                }
                !$item['parent_menu_id'] && $item['parent_menu_id'] = '一级菜单';
            }
            $data = $ret;
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }


    /**
     *
     * 公共号模板消息列表
     */
	public function msgList(){
        $page  = request_int('page', 1);
        $rows  = request_int('rows', 100);
        $ret = $this->wxpublicMsgMdl->getPublicMsgList($cond_row=array(), $order_row=array(), $page, $rows);

        $data = array();
        if ( !empty($ret) )
        {
            foreach ($ret['items'] as &$item){
                $item['msg_type'] = WxPublic_MessageModel::$map['msg_type'][$item['msg_type']];
                $item['match_type'] = WxPublic_MessageModel::$map['match_type'][$item['match_type']];

            }
            $data = $ret;
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * @return string
     * 修改模板消息
     */
    public function EditMsgRow()
    {
        $id = request_string("id");
        $row['words'] = request_string("words");
        $row['match_type'] = request_string("match_type");
        $row['msg_type'] = request_string("msg_type");
        $row['content'] = request_string("content");
        $flag = $this->wxpublicMsgMdl->editPublicMsg($id,$row);
        if($flag){
            $msg    = '修改成功';
            $status = 200;
            $datas = array();
        }else{
            $msg    = '修改失败';
            $status = 250;
            $datas = array();
        }
        $this->data->addBody(-140, $row, $msg, $status);

    }



    public function AddMsgRow()
    {
        $words = request_string("words");

        $match_type = request_string("match_type");
        $msg_type = request_string("msg_type");
        $content = request_string("content");
        if(!$words || !$match_type || !$msg_type || !$content){
            return $this->data->addBody(-140, array(), __('参数不能为空'), 250);
        }
        //名称不超过25个字符
        if(mb_strlen($words,'utf-8') > 50){
            return $this->data->addBody(-140, array(), __('关键词不能超过50个字符'), 250);
        }
        $insert_row =array(
            'words' =>$words,
            'match_type'=>$match_type,
            'msg_type'=>$msg_type,
            'content'=>$content,
            'create_time'=>time()
        );
        $flag = $this->wxpublicMsgMdl->addPublicMsg($insert_row);
        if($flag){
            $msg    = '添加成功';
            $status = 200;
            $datas = array();
        }else{
            $msg    = '添加失败';
            $status = 250;
            $datas = array();
        }
        $this->data->addBody(-140, $datas, $msg, $status);
    }


    public function getMenuEditRow()
    {
        $id = request_int("id");
        $data    = $this->wxpublicMenuMdl->getOne($id);
        !$data && $data = array();

        //获取一级菜单
        $where  = array('parent_menu_id'=>0);
        $order_row = array('sort_num'=>'desc','operate_time'=>'desc');
        $menu_row= $this->wxpublicMenuMdl->getMenuListData($where,$order_row);
        $menu_row && $data['menu_row'] = $menu_row;
        !$data['menu_row'] &&  $data['menu_row'] =array();
        $this->data->addBody(-140, $data);
    }

    /**
     * @return string
     * 修改模板消息
     */
    public function EditMenuRow()
    {
        $data = array();
        $id = request_string("id");//主键
        //菜单名称
        $menu_name = request_string("menu_name");
        //菜单级别（0：一级菜单；否则为二级菜单）
        $parent_menu_id = request_string("menu_level");
        !$parent_menu_id && $parent_menu_id = 0 ;
        $data['parent_menu_id'] = $parent_menu_id;
        $menu_type = request_string("menu_type");
        //排序
        $sort_num = request_string('sort_num');
        $data['sort_num'] = $sort_num?intval($sort_num):1;
        if(!$data['sort_num'] || $data['sort_num']>100){
            $msg    = '对不起，请输入正确的排序值！[1-100]之间';
            $status = 250;
            $datas = array();
            return $this->data->addBody(-140, $datas, $msg, $status);
        }
        if(!$menu_name){
            $msg    = '菜单名称不能为空！';
            $status = 250;
            $datas = array();
            return $this->data->addBody(-140, $datas, $msg, $status);
        }
        //查询
        $one  =  $this->wxpublicMenuMdl->getOne($id);
        if(!$one){
            $msg    = '菜单不存在，非法操作！';
            $status = 250;
            $datas = array();
            return $this->data->addBody(-140, $datas, $msg, $status);
        }
        if($one['menu_name']!=$menu_name){
            //查询菜单名称是否存在
            $sql ="select * from ".$this->wxpublicMenuMdl->_tableName." where 1=1 and menu_name='{$menu_name}'";
            $one = $this->wxpublicMenuMdl->sql->getRow($sql);
            if($one){
                $msg    = '对不起，菜单名称已存在！';
                $status = 250;
                $datas = array();
                return $this->data->addBody(-140, $datas, $msg, $status);
            }
        }
        $data['menu_name'] = $menu_name;
        $flag =false;
        switch ($menu_type){
            case 1:
                $content = request_string('content');
                !$content && $flag = true;
                $content && $data['menu_msg'] = $content;
                break;
            case 2:
                $redirect_url = request_string('redirect_url');
                !$redirect_url && $flag = true;
                $redirect_url && $data['menu_url'] = $redirect_url;
                break;
            case 3:
                $xcx_id = request_string('xcx_id');
                $xcx_url = request_string('xcx_url');
                $wxxcx_pagepath =  request_string('wxxcx_pagepath');//小程序页面路径
                if(!$xcx_id || !$xcx_url || !$wxxcx_pagepath){
                    $flag = true;
                }
                $xcx_id &&  $data['wxxcx_id'] = $xcx_id;
                $xcx_url &&  $data['wxxcx_url'] = $xcx_url;
                $wxxcx_pagepath && $data['wxxcx_pagepath'] = $wxxcx_pagepath;
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
        $data['menu_type'] = $menu_type;
        $data['operate_time'] = time();//操作日期
        $flag = $this->wxpublicMenuMdl->editPublicMenu($id,$data);
        if($flag){
            $msg    = '修改成功';
            $status = 200;
            $datas = array();
        }else{
            $msg    = '修改失败';
            $status = 250;
            $datas = array();
        }
        $this->data->addBody(-140, $data, $msg, $status);

    }

    /**
     *  修改
     */
    public function getMsgEditRow()
    {
        $id = request_int("id");
        $data    = $this->wxpublicMsgMdl->getOne($id);

        !$data && $data = array();
        $this->data->addBody(-140, $data);
    }

    /**
     * @throws Exception
     * 删除公众号菜单
     */
    public function delPublicMenu()
    {
        $id             = request_int("id");
        $ret          = $this->wxpublicMenuMdl->getOne($id);
        if (!empty($ret))
        {
            //删除相关菜单
            $del = $this->wxpublicMenuMdl->removeRel($id);
            if ($del)
            {
                $status = 200;
                $msg    = __('删除成功！');
            }
            else
            {
                $status = 250;
                $msg    = __('删除失败！');
            }
        }
        else
        {
            $status = 250;
            $msg    = __('删除失败！');
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function delPublicMsg()
    {
        $shop_id             = request_int("id");
        $ret          = $this->wxpublicMsgMdl->getOne($shop_id);
        if (!empty($ret))
        {
            $del = $this->wxpublicMsgMdl->removeBase($shop_id);
            if ($del)
            {
                $status = 200;
                $msg    = __('删除成功！');
            }
            else
            {
                $status = 250;
                $msg    = __('删除失败！');
            }
        }
        else
        {
            $status = 250;
            $msg    = __('删除失败！');
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }


    /**
     *
     * 公众号菜单添加页面一级分类数据
     */
    public function getMenuAddRow(){
        $where  = array('parent_menu_id'=>0);
        $order_row = array('sort_num'=>'desc','operate_time'=>'desc');
        $data = $this->wxpublicMenuMdl->getMenuListData($where,$order_row);
        !$data  && $data = array();
        $this->data->addBody(-140, $data, 'success', 200);
    }


    /**
     *
     * 添加公众号菜单
     */
    public function AddMenuRow()
    {
        $data = array();
        //菜单名称
        $menu_name = request_string("menu_name");
        //菜单级别（0：一级菜单；否则为二级菜单）
        $parent_menu_id = request_string("menu_level");
        !$parent_menu_id && $parent_menu_id = 0 ;
        $data['parent_menu_id'] = $parent_menu_id;
        $menu_type = request_string("menu_type");
        //排序
        $sort_num = request_string('sort_num');
        $data['sort_num'] = $sort_num?intval($sort_num):1;
        if(!$data['sort_num'] || $data['sort_num']>100){
            $msg    = '对不起，请输入正确的排序值！[1-100]之间';
            $status = 250;
            $datas = array();
            return $this->data->addBody(-140, $datas, $msg, $status);
        }
        if(!$menu_name){
            $msg    = '菜单名称不能为空！';
            $status = 250;
            $datas = array();
            return $this->data->addBody(-140, $datas, $msg, $status);
        }
        //查询菜单名称是否存在
        $sql ="select * from ".$this->wxpublicMenuMdl->_tableName." where 1=1 and menu_name='{$menu_name}'";
        $one = $this->wxpublicMenuMdl->sql->getRow($sql);
        if($one){
            $msg    = '对不起，菜单名称已存在！';
            $status = 250;
            $datas = array();
            return $this->data->addBody(-140, $datas, $msg, $status);
        }
        $data['menu_name'] = $menu_name;
        $flag =false;
        switch ($menu_type){
            case 1:
                $content = request_string('content');
                !$content && $flag = true;
                $content && $data['menu_msg'] = $content;
                break;
            case 2:
                $redirect_url = request_string('redirect_url');
                !$redirect_url && $flag = true;
                $redirect_url && $data['menu_url'] = $redirect_url;
                break;
            case 3:
                $xcx_id = request_string('xcx_id');
                $xcx_url = request_string('xcx_url');
                $wxxcx_pagepath =  request_string('wxxcx_pagepath');//小程序页面路径
                if(!$xcx_id || !$xcx_url || !$wxxcx_pagepath){
                    $flag = true;
                }
                $xcx_id &&  $data['wxxcx_id'] = $xcx_id;
                $xcx_url &&  $data['wxxcx_url'] = $xcx_url;
                $wxxcx_pagepath && $data['wxxcx_pagepath'] = $wxxcx_pagepath;
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
        $data['menu_type'] = $menu_type;
        $data['operate_time'] = time();//操作日期
        $flag = $this->wxpublicMenuMdl->addPublicMenu($data);
        if($flag){
            $msg    = '添加成功';
            $status = 200;
            $datas = array();
        }else{
            $msg    = '添加失败';
            $status = 250;
            $datas = array();
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }


    /**
     * @throws Exception
     * 公众号创建菜单
     */
    public function wxPublicCreateMenu()
    {
        //微信公众号开关
        if(!Web_ConfigModel::value('wechat_public_status')){
            $status = 250;
            $msg    = __('同步失败！');
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
        }

        //微信按钮类型
        $mnu_map = array(
            1=>'click',//菜单的响应动作类型===>发送消息
            2=>'view',//网页跳转
            3=>'miniprogram'//小程序类型
        );

        //第二步 创建菜单
        $menu_mdl = new WxPublic_MenuModel();
        $sql = 'select * from '.$menu_mdl->_tableName." where parent_menu_id=0 order by sort_num,operate_time desc limit 3";
        $result  =  $menu_mdl->sql->getAll($sql);
        $objectArr = array();
        foreach ($result as $item){
            $arr = array(
                "name"=>$item['menu_name']
            );
            //判断是否有二级菜单
            $itemArr = array();
            if($item['id']){
                $sql = 'select * from '.$menu_mdl->_tableName." where parent_menu_id='{$item['id']}' limit 5";
                $ret  =  $menu_mdl->sql->getAll($sql);
                foreach ($ret  as $val){
                    $valArr = array(
                        "name"=>$val['menu_name']
                    );
                    switch ($val['menu_type']){
                        case 1://clickclick
                            $valArr['key'] = md5($val['id']);
                            break;
                        case 2://view
                            $valArr['url'] = $val['menu_url'];
                            break;
                        case 3://小程序
                            $valArr['url'] = $val['wxxcx_url'];
                            $valArr['appid'] = $val['wxxcx_id'];
                            $valArr['pagepath'] = $val['wxxcx_pagepath'];
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
                            $arr['key'] = md5($item['id']);
                            break;
                        case 2://view
                            $arr['url'] = $item['menu_url'];
                            break;
                        case 3://小程序
                            $arr['url'] = $item['wxxcx_url'];
                            $arr['appid'] = $item['wxxcx_id'];
                            $arr['pagepath'] = $item['wxxcx_pagepath'];
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
        $Web_ConfigModel =  new Web_ConfigModel();
        $token = $Web_ConfigModel->getWxPublicAccessToken();

        if(!$token['token']){
            $status = 250;
            $msg    = __('token获取失败！');
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
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

}

?>