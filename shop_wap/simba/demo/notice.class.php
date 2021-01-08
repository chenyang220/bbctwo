<?php

/* 
 * 消息通知api
 */

require_once 'src/Api.class.php';

class notice {

    private $access_token;

    public function __construct($access_token) {
        $this->access_token = $access_token;
        $this->api = new simba\oauth\Api();
    }

    /* 格式化输出
     * 这里只是简单输出
     */

    public function format($result) {
        print_r($result);
    }
    
    
    /*业务消息通知*/
    public function notice_send(){
        $msg = array(
            'subject' => '测试消息主题', 
            'content' => '测试消息内容',
            'sender'=> '66209676', 
            'receivers' =>'66209676'
        );
        $result = $this->api->simba_business_notice_send($this->access_token, $msg);
        $this->format($result);
    }
    

}