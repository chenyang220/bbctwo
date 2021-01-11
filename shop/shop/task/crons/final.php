<?php
if (!defined('ROOT_PATH'))
{
	if (is_file('../../../shop/configs/config.ini.php'))
	{
		require_once '../../../shop/configs/config.ini.php';
	}
	else
	{
		die('请先运行index.php,生成应用程序框架结构！');
	}

	//不会重复包含, 否则会死循环: web调用不到此处, 通过crontab调用
	$Base_CronModel = new Base_CronModel();
	$rows = $Base_CronModel->checkTask(); //并非指执行自己, 将所有需要执行的都执行掉, 如果自己达到执行条件,也不执行.

	//终止执行下面内容, 否则会执行两次
	return ;
}


Yf_Log::log(__FILE__, Yf_Log::INFO, 'crontab');

$file_name_row = pathinfo(__FILE__);
$crontab_file = $file_name_row['basename'];

fb($crontab_file);
//执行任务
		
		
		
        $Order_BaseModel = new Order_BaseModel();
      
		$message = new MessageModel();
		
		$cond_row['order_status'] = 20;

		$cond_row['final_message'] = 0;
		
		$data = $Order_BaseModel->getByWhere($cond_row);
		foreach($data as $key=>$val){
			//发送站内信
			
			if(time()>=strtotime($val['presale_final_time'])){	
				$orders_row['message_content']     = '亲爱的用户您好，您的订单'.$val['order_id'].'请支付尾款！';
				$orders_row['message_create_time'] = get_date_time();
				$orders_row['message_mold']        = 0;
				$orders_row['message_type']        = 1;
				$orders_row['message_title']       = '尾款提醒';
				$orders_row['message_user_id']     = $val['buyer_userid'];
								
				$flag = $message->addMessage($orders_row);

				//发送短信
				$message_model = new Message_TemplateModel();
				$pattern = ['[order_id]'=>$val['order_id']];
				$replacement = [$val['order_id']];
				$message_info = $message_model->getTemplateInfo(['code' => 'presale final'], $pattern, $replacement);
				$mobile = $val['final_mobile'];
				$contents = $message_info['content_phone'];
				$result = Sms::send($mobile,86, $contents, $message_info['baidu_tpl_id'],['order_id'=>$val['order_id']]);

				$edit_row['final_message'] = 1;
				$flag = $Order_BaseModel->editBase($val['order_id'],$edit_row);
				
			}
		}
		
		
		
		
	



$flag = true;
return $flag;
?>