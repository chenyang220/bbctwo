<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class WxPublicTool_SellerWxCtl extends Controller
{
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
    }
          
    public function index(){
        $echostr = $_GET['echostr'];
        if($echostr){//接入微信公众号，配置服务器[url|token](分支)
            echo $this->valid($echostr);
            return ;
        }else{
            $this->reponseMsg();////自动消息回复（分支）
        }
    }
    public function reponseMsg(){
        //接收传入值
        $postArr = file_get_contents("php://input");
        $postObj = simplexml_load_string( $postArr );
        $toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        $msgType  =  'text';
        $template = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    </xml>";
        $info  = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
        libxml_disable_entity_loader(true);
        $values  =  json_decode(json_encode(simplexml_load_string($info,  'SimpleXMLElement',  LIBXML_NOCDATA)),  true);
        $Seller_SellerWxListModel= new Seller_SellerWxListModel();
        $msgInfo=$Seller_SellerWxListModel->getOneByWhere(array('wechat_public_start_id'=>$values['FromUserName']));
        $Seller_SellerWxModel= new Seller_SellerWxModel();
        $list=$Seller_SellerWxModel=$Seller_SellerWxModel->getOneByWhere(array('shop_id'=>$msgInfo['shop_id']));
        if( strtolower( $postObj->MsgType) == 'event'){
        
            //如果是关注事件(subscribe)
            if( strtolower($postObj->Event) == 'subscribe' ){
                //回复用户消息
                $toUser   = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time     = time();
                $msgType  =  'text';
                $web_name = $list['shop_name'];//店铺名称
                $content  = '欢迎关注['.$web_name.']微信公众号!';
                $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
                $info  = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                echo $info;
            }elseif (strtolower($postObj->Event) == 'click') {
                $key = strtolower($postObj->EventKey);
                $Seller_SellerWxMenuModel = new Seller_SellerWxMenuModel();
                $sql = "SELECT * from(SELECT *,MD5(wxpublic_menu_id) md from  `yf_seller_wxpublic_menu`)xx where  xx. md='{$key}'";
                $menu_msg=$Seller_SellerWxMenuModel->sql->getAll($sql); 
                $menu_msg = current($menu_msg);
                $toUser   = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time     = time();
                $msgType  =  'text';
                $content  = $menu_msg['menu_msg'];
                $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
                $info  = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                echo $info;
            }
        }
        //文本格式
        if('text'==($postObj->MsgType) && trim($postObj->Content)){
            //封装回复数据
            $content = $this->dealMsgData(trim($postObj->Content),$msgInfo['shop_id']);//传入用户回复值
            if($content){
               $this->replyXmlTpl($postObj,$content);//回复消息模板
            }
        }
    }
    /**
     * @param $val
     * @return string
     * 回复XML TPL
     */
    public function replyXmlTpl($postObj,$data,$msg_type='1'){//msg_type:1,文本消息；2：图文消息
        $template1 = "";
        switch ($msg_type){
            case 1:
                $template1 = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
                break;
            case 2:
                //图文数据格式：预留示例
                break;
        }
        if($template1){
            $fromUser = $postObj->ToUserName;//消息从哪里来
            $toUser   = $postObj->FromUserName;//发送给谁
            $time     = time();
            $msgType  = ($msg_type==1)?"text":'text';//消息类型可封装为map，这里暂固定tet
            echo sprintf($template1, $toUser, $fromUser,$time, $msgType, $data);
        }
    }
    private  function dealMsgData($val,$shop_id){
        $WxPublic_MessageModel = new Seller_SellerWxMsgModel();
        //查询用户输入值是否存在相关规则
        $result = $WxPublic_MessageModel->autoReplyList($val,$shop_id);

        $content = '';//回复内容
        foreach ($result as $key => $items){
            if($items['match_type']==1){//精准匹配
                ($val==$items['words']) && $content= $items['content'];
            }else{//模糊匹配
                if (function_exists('mb_strpos')) {
                    (mb_strpos($items['words'],$val, 0, 'UTF-8')!==false) && $content= $items['content'];
                } else {
                    (strpos($items['words'], $val)!==false) && $content= $items['content'];
                }
            }
            if($content) break;
        }
        return $content;
    }

    /**
     *验证方法
     */
    private function valid($echoStr)
    {
        if($this->checkSignature()){
            return  $echoStr;
        }
        return '';
    }

    /**
     *签名算法
     */
    private function checkSignature()
    {   
        $Seller_SellerWxListModel= new Seller_SellerWxListModel();
        $token  = $Seller_SellerWxListModel->getOneByWhere(array('shop_id'=>$_GET["t"]));
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = $token['wechat_public_token'];
      
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode('',$tmpArr );
        $tmpStr = sha1( $tmpStr );
        $str= $tmpStr ."==". $signature;
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

}
?>