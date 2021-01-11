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
 * @author     banchangle <1427825015@qq.com>
 * @copyright  Copyright (c) 2016, 班常乐
 * @version    1.0
 * @todo
 */
class Api_Paycen_PayWithdrawCtl extends Api_Controller
{
    /**
     *支付会员
     *
     * @access public
     */
    
    function getPayWithdrawList() {
       
        $Consume_WithdrawModel = new Consume_WithdrawModel();


        $cond_row = array();
        $user_account = request_string('userName');


        if($user_account){
            $UserModel = new User_BaseModel();
            $cond['user_account:LIKE'] = '%' . $user_account . '%';

            $user_base = $UserModel->getByWhere($cond);
            $user_ids = array_values(array_column($user_base, 'user_id'));
            $cond_row['pay_uid:IN'] = $user_ids;
        }

        $data           = $Consume_WithdrawModel->getWithdrawList($cond_row);
        if(isset($data['items']) && $data['items']) {
            //获取用户信息
            $uid = array();
            foreach ($data['items'] as $value){
                $uid[] = $value['pay_uid'];
            }
            $uid = array_unique($uid);
            if(count($uid) > 1){
                $where = array('user_id:IN'=>$uid);
            }else{
                $uid_str = array_shift($uid);
                $where = array('user_id'=>$uid_str);
            }
            $UserModel = new User_BaseModel();
            $user_info = $UserModel->getPayBaseList($where);
            $user_account_array = array();
            foreach ($user_info['items'] as $val){
                $user_account_array[$val['user_id']] = $val['user_account'];
            }
            foreach ($data['items'] as $key=>$v){
                $data['items'][$key]['user_account'] = $user_account_array[$v['pay_uid']];
                $data['items'][$key]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            }
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

        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    function getEditWithdraw() {
        $id = request_int("id");
        $Consume_WithdrawModel = new Consume_WithdrawModel();
        $data = $Consume_WithdrawModel->getOne($id);
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
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    function editWithdrawRow() {
            $id = request_int("id");
            $data['is_succeed'] = request_int("is_succeed");
            $data['bankflow'] = request_string("bankflow");
            $data['remark'] = request_string("remark");
            $data['check_time'] = time();
            if('hp' == request_string('typ'))
            {
                echo '<pre>';print_r($data);exit;
            }
            $Consume_WithdrawModel = new Consume_WithdrawModel();
            $Withdrawlist = $Consume_WithdrawModel->getOne($id);
            $flag = $Consume_WithdrawModel->editWithdraw($id,$data);
            if ($flag!==false)
            {
                 if( $data['is_succeed'] == 3)
                 {

                     //实例化流水表
                     $Consume_RecordModel = new Consume_RecordModel();
                     //用充值的订单id查询出流水表信息
                     $cond_row['order_id'] = $Withdrawlist['orderid'];
                     $record_list = $Consume_RecordModel->getOneByWhere($cond_row);

                     //更改流水表的信息
                     $flag1 = $Consume_RecordModel->editRecord($record_list['consume_record_id'],array("record_status"=>2));

                 if($flag1!==false)
                 {
                     //修改用户的冻结金额
                     $User_ResourceModel = new User_ResourceModel();
                     $user_resource      = current($User_ResourceModel->getResource($record_list['user_id']));
                     $resource_edit_row['user_money_frozen'] = $user_resource['user_money_frozen'] + $record_list['record_money']*1;
                     $flag2 = $User_ResourceModel->editResource($record_list['user_id'], $resource_edit_row);

                     if($flag2!==false)
                     {
                         $msg    = 'success';
                         $status = 200;
                     }
                     else
                     {
                         $msg    = 'failure';
                         $status = 250;
                     }

                 }
                 else
                 {
                     $msg    = 'failure';
                     $status = 250; 
                 }
                }
                 elseif($data['is_succeed'] == 4)
                 {
                    //实例化流水表
                     $Consume_RecordModel = new Consume_RecordModel();
                     //用充值的订单id查询出流水表信息
                     $cond_row['order_id'] = $Withdrawlist['orderid'];
                     $record_list = $Consume_RecordModel->getOneByWhere($cond_row);

                     //更改流水表的信息
                     $flag1 = $Consume_RecordModel->editRecord($record_list['consume_record_id'],array("record_status"=>RecordStatusModel::RECORD_CANCEL));
                     if($flag1!==false)
                     {
                         //修改用户的冻结金额
                         $User_ResourceModel = new User_ResourceModel();
                         $user_resource      = current($User_ResourceModel->getResource($record_list['user_id']));
                         $resource_edit_row['user_money']        = $user_resource['user_money'] - $record_list['record_money']*1;
                         $resource_edit_row['user_money_frozen'] = $user_resource['user_money_frozen'] + $record_list['record_money']*1;
                         $flag2 = $User_ResourceModel->editResource($record_list['user_id'], $resource_edit_row);

                         if($flag2!==false)
                         {
                             $msg    = 'success';
                             $status = 200;
                         }
                         else
                         {
                             $msg    = 'failure';
                             $status = 250;
                         }

                     }
                 }
                 
            }
            else
            {
                $msg    = 'failure';
                $status = 250;
            }
            $data['id'] =$id ;
            
            $this->data->addBody(-140, $data, $msg, $status);
    }
    
    function editWithdrawRowWx()
    {
    	$id = request_int("id");
    	$data['is_succeed'] = request_int("is_succeed");
    	$data['remark'] = request_string("remark");
    	$data['check_time'] = time();
    	
    	if($data['is_succeed'] == 3)
    	{
    		$user_id = request_int('user_id');		//提现用户id
    		$amount = request_float('amount');		//提现金额
    		//判断微信
    		$key = Yf_Registry::get('ucenter_api_key');
    		$url       = Yf_Registry::get('ucenter_api_url');
    		$app_id    = Yf_Registry::get('ucenter_app_id');
    		$server_id = Yf_Registry::get('server_id');
    		//开通ucenter
    		//本地读取远程信息
    		$formvars              = array();
    		$formvars['user_id'] = $user_id;
    		$formvars['app_id']    = $app_id;
    		$formvars['server_id'] = $server_id;
    		
    		$formvars['ctl'] = 'Api_User';
    		$formvars['met'] = 'getUserBind';
    		$formvars['typ'] = 'json';
    		
    		$init_rs = get_url_with_encrypt($key, $url, $formvars);
    		
    		if(isset($init_rs['data']['bind_openid']) && !$init_rs['data']['bind_openid'])
    		{
    			$msg    = '请使用微信登录，绑定微信';
    			$status = 250;
    		}else{
    			$openid = $init_rs['data']['bind_openid'];
    			
    			$WxModel = new WxModel();
    			$order_sn = date('YmdHis').rand(1000, 9999); //订单号
    			
    			$new_amount = $amount * 100;
    			$res = $WxModel->tixian($new_amount,$openid);
    			 
    			 
    			$return_info = json_decode($res,true);
    			//提现成功修改记录表的状态
    			if($return_info['con'] == 'ok' && $return_info['error'] == 0)
    			{
    				$Consume_WithdrawModel = new Consume_WithdrawModel();
    				$Withdrawlist = $Consume_WithdrawModel->getOne($id);
    				$flag = $Consume_WithdrawModel->editWithdraw($id,$data);
    				
    				if($flag != false)
    				{
    					//实例化流水表
    					$Consume_RecordModel = new Consume_RecordModel();
    					//用充值的订单id查询出流水表信息
    					$cond_row['order_id'] = $Withdrawlist['orderid'];
    					$record_list = $Consume_RecordModel->getOneByWhere($cond_row);
    					 
    					//更改流水表的信息
    					$flag1 = $Consume_RecordModel->editRecord($record_list['consume_record_id'],array("record_status"=>2));
                        $User_ResourceModel = new User_ResourceModel();
    					$flag2 = $User_ResourceModel->editResource($user_id,array('user_money'=>'-'.$amount),ture);
                        if($flag1 != false && $flag2 !=false)
    					{
    					    $msg    = 'success';
    					    $status = 200;
    					}
    					else
    					{
    					    $msg    = 'failure1';
    					    $status = 250;
    					}
    					
    				}
    				
    			}else{
    				$msg    = $return_info['errmsg'];
     				$status = 250;
    			}
    		     
    		}
    	}
    	elseif($data['is_succeed'] == 4)
    	{
    		$Consume_WithdrawModel = new Consume_WithdrawModel();
    		$Withdrawlist = $Consume_WithdrawModel->getOne($id);
    		$flag = $Consume_WithdrawModel->editWithdraw($id,$data);
    	    //实例化流水表
    	    $Consume_RecordModel = new Consume_RecordModel();
    	    //用充值的订单id查询出流水表信息
    	    $cond_row['order_id'] = $Withdrawlist['orderid'];
    	    $record_list = $Consume_RecordModel->getOneByWhere($cond_row);
    	 
    	    //更改流水表的信息
    	    $flag1 = $Consume_RecordModel->editRecord($record_list['consume_record_id'],array("record_status"=>RecordStatusModel::RECORD_CANCEL));
    	    if($flag1 != false)
    		{
    			$msg    = 'success';
    		    $status = 200;
    		}else{
    		    			
    		    $msg    = 'failure';
    		    $status = 250;
    		}
    	 }
    	 
    	 $this->data->addBody(-140, array(), $msg, $status);
		
    }

    function editWithdrawRowZfb()
    {
        $id = request_int("id");
        $data['is_succeed'] = request_int("is_succeed");
        $data['remark'] = request_string("remark");
        $data['check_time'] = time();
        $Consume_WithdrawModel = new Consume_WithdrawModel();
        $Withdrawlist = $Consume_WithdrawModel->getOne($id);
        if($data['is_succeed'] == 3)
        {   
            $user_id = request_int('user_id');      //提现用户id
            $amount = request_float('amount');      //提现金额
            
            $AlipayModel = new AlipayModel();
            $order_sn = date('YmdHis').rand(1000, 9999); //订单号
        
            $res = $AlipayModel->withdraw($amount,$Withdrawlist['withdraw_identity'],$Withdrawlist['withdraw_name'],$Withdrawlist['con'],$Withdrawlist['orderid']);
             
             
            $return_info = json_decode($res,true);
            //提现成功修改记录表的状态
            if($return_info['con'] == 'ok' && $return_info['error'] == 0)
            {
                $flag = $Consume_WithdrawModel->editWithdraw($id,$data);
                
                if($flag != false)
                {
                    //实例化流水表
                    $Consume_RecordModel = new Consume_RecordModel();
                    //用充值的订单id查询出流水表信息
                    $cond_row['order_id'] = $Withdrawlist['orderid'];
                    $record_list = $Consume_RecordModel->getOneByWhere($cond_row);
                     
                    //更改流水表的信息
                    $flag1 = $Consume_RecordModel->editRecord($record_list['consume_record_id'],array("record_status"=>2));
                    $User_ResourceModel = new User_ResourceModel();
                    $flag2 = $User_ResourceModel->editResource($user_id,array('user_money'=>'-'.$amount),ture);
                    if($flag1 != false && $flag2 !=false)
                    {
                        $msg    = 'success';
                        $status = 200;
                    }
                    else
                    {
                        $msg    = 'failure1';
                        $status = 250;
                    }
                    
                }
                
            }else{
                $msg    = 'failure2';
                $status = 250;
            }
                 
            
        }
        elseif($data['is_succeed'] == 4)
        {
            $flag = $Consume_WithdrawModel->editWithdraw($id,$data);
            //实例化流水表
            $Consume_RecordModel = new Consume_RecordModel();
            //用充值的订单id查询出流水表信息
            $cond_row['order_id'] = $Withdrawlist['orderid'];
            $record_list = $Consume_RecordModel->getOneByWhere($cond_row);
         
            //更改流水表的信息
            $flag1 = $Consume_RecordModel->editRecord($record_list['consume_record_id'],array("record_status"=>RecordStatusModel::RECORD_CANCEL));
            if($flag1 != false)
            {
                $msg    = 'success';
                $status = 200;
            }else{
                            
                $msg    = 'failure';
                $status = 250;
            }
         }
         
         $this->data->addBody(-140, array(), $msg, $status);
        
    }
 
}
?>