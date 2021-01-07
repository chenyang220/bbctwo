<?php

    /**
     * @param $template_id_short,$access_token
     * 模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
     * @return template_id
     */
    function  wxpublic_api_add_template($template_id_short,$access_token){
        $url ="https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=".$access_token;
        $post_data = json_encode(array('template_id_short'=>$template_id_short));
        $ret = curlMet($url,$post_data);
        if(!$ret){
            return array();
        }else{
            return json_decode($ret,true);
        }
    }

    /**
     *
     * 获取微信公众号消息模板列表
     * 获取已添加至帐号下所有模板列表，可在微信公众平台后台中查看模板列表信息
     */
    function wxpublic_get_all_private_template($access_token){
        $url ="https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=".$access_token;
        $ret = curlMet($url);
        if(!$ret){
            return array();
        }else{
            return json_decode($ret,true);
        }
    }

    /**
     * @param $data array
     * @param $access_token
     * @return array|mixed
     * 发送模板消息
     */
    function wxpublic_send($data,$access_token){
        $url ="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $post_data = json_encode($data);
        $ret = curlMet($url,$post_data);
        if(!$ret){
            return array();
        }else{
            return json_decode($ret,true);
        }
    }

    /**
     * @param $appid
     * @param $secret
     * @return array|mixed
     * 获取微信公众号token信息
     */
      function getAccToken($appid,$secret){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
        $result = curlMet($url);
        if(!$result){
            return array();
        }
        return  json_decode($result,true);
      }

    /**
     * 创建公众号自定义菜单
     *
     */
    function wxpublic_create_menu($data,$access_token){
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=". $access_token;
        $json = decodeUnicode(json_encode($data));
        $result = curlMet($url,$json);
        if(!$result){
            return array();
        }else{
            return json_decode($result,true);
        }
    }

    /**
     *
     * 微信公众号设置所属行业
     *  data:{"industry_id1":"1", "industry_id2":"4"}
     */
    function wxpublic_set_industry($access_token,$data){

        $url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=". $access_token;
        $json = json_encode($data);
        $result = curlMet($url,$json);
        if(!$result){
            return array();
        }else{
            return json_decode($result,true);
        }
    }

    /**
     * 导出Excel功能
     * @param array $header 头部标题
     * @param array $data 数据
     * @param string $file_name 文件名
     */
   function exportExcel(array $header,array $data,$file_name=''){
        ob_end_clean();
       !$file_name && $file_name = date("Y-m-d").".xls";
       //组装头部标题
        $head_txt = "<tr>";
        foreach ($header as $v) {
            $head_txt .= "<td>$v</td>";
        }
        $head_txt .= "</tr>";
        $html = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"\r\nxmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<head>\r\n<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">\r\n</head>\r\n<body>";
        $html .="<table border=1>" . $head_txt;
        $html .= '';
        //组装实体数据部分
        foreach ($data as $key => $rt) {
            $html .= "<tr>";
            foreach ($rt as $v) {
                $html .= "<td >{$v}</td>";
            }
            $html .= "</tr>\n";
        }
        $html .= "</table></body></html>";
        ob_end_clean();
        header("Content-Type: application/vnd.ms-excel; name='excel'");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $file_name);
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        exit($html);
    }

    /**
     * @param $url
     * @param $data
     * @return mixed|string
     *
     * CURL POST请求方法
     */
    function curlMet($url,$data=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }

        curl_close($ch);
        return $tmpInfo;
    }
	
	function decodeUnicode($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
            create_function(
                '$matches',
                'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
            ),
            $str);
    }

	
	/**
     * @return bool
     * http/https判断
     */
    function isHttps()
    {
        if (defined('HTTPS') && HTTPS) return true;
        if (!isset($_SERVER)) return FALSE;
        if (!isset($_SERVER['HTTPS'])) return FALSE;
        if ($_SERVER['HTTPS'] === 1) {  //Apache
            return TRUE;
        } elseif ($_SERVER['HTTPS'] === 'on') { //IIS
            return TRUE;
        } elseif ($_SERVER['SERVER_PORT'] == 443) { //其他
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @param $array_A
     * @param $array_B
     * @return bool
     * 判断两个数组是否有交集
     */
    function get_array_intersection($array_A,$array_B){
        if(!isset($array_A) || !isset($array_B) || !is_array($array_A) || !is_array($array_B) || empty($array_A) || empty($array_B)){
            return false;
        }
        foreach($array_A as $temp){
            if(in_array($temp,$array_B)){
                return true;
            }
        }
        unset($array_A,$array_B,$temp);
        return false;
    }

    /**
     * array_column
     * php版本小于5.5建议使用
     * 备注：如果php版本小于5.5，且存在array_column函数，建议在php.ini禁用内置的该函数（disable_functions =array_column）
     * @nsy 2019-10-15
     */
    if (!function_exists('array_column')) {
        function array_column($array, $columnKey, $indexKey = null)
        {
            $result = array();
            foreach ($array as $subArray) {
                if (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
                    $result[] = is_object($subArray)?$subArray->$columnKey: $subArray[$columnKey];
                } elseif (array_key_exists($indexKey, $subArray)) {
                    if (is_null($columnKey)) {
                        $index = is_object($subArray)?$subArray->$indexKey: $subArray[$indexKey];
                        $result[$index] = $subArray;
                    } elseif (array_key_exists($columnKey, $subArray)) {
                        $index = is_object($subArray)?$subArray->$indexKey: $subArray[$indexKey];
                        $result[$index] = is_object($subArray)?$subArray->$columnKey: $subArray[$columnKey];
                    }
                }
            }
            return $result;
        }
    }

	
	