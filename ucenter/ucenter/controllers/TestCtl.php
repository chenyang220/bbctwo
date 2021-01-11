<?php
class TestCtl extends Yf_AppController
{

	public function addUser()
	{
		


	}
	
	public function sendMsg()
    {
        // $mobile = request_string('mobile');
        //
        // //发送短消息
        // $message_model = new Message_TemplateModel();
        // $pattern = array('/\[weburl_name\]/', '/\[yzm\]/');
        // $replacement = array(Web_ConfigModel::value("site_name"), $check_code);
        // $message_info = $message_model -> getTemplateInfo(array('code' => 'regist_verify'), $pattern, $replacement);
        // if (!$message_info['is_phone']) {
        //     $this -> data -> addBody(-140, array(), __('信息内容创建失败'), 250);
        // }
        // $contents = $message_info['content_phone'];
        //
        // $result = Sms::sendTXY($mobile, $contents);
        $result = Sms::sendTXY();
    }

    public function editTemplateEmail()
    {
        $messageTemplateModel = new Message_TemplateModel();
        $id = request_int('id');
        $field['force_email'] = request_int('force_email');
        $field['title'] = request_string('title');
        $field['content_email'] = request_string('content_email');
        $field['is_email'] = request_int('is_email');
        $flag = $messageTemplateModel->editTemplate($id, $field);
        if ($flag !== false)
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
        $this->data->addBody(-140, $data, $msg, $status);
    }

}
