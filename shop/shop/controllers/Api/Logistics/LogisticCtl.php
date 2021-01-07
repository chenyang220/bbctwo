<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}
class Api_Logistics_LogisticCtl extends Api_Controller
{
    public function index(){

        $channel = 'kuaidiniao';

        $order_id   = request_string('order_id');  //订单编号
        $express_id = request_string('express_id'); //物流公司编码
        $nu         = request_string('shipping_code'); //物流单号
        $ExpressModel = new ExpressModel();
        $express_row = $ExpressModel->getOne($express_id);

        if ('kuaidi100' == $channel)
        {
            $api_id = Web_ConfigModel::value('kuaidi100_app_id');
            $api_sceret = Web_ConfigModel::value('kuaidi100_app_key');


            if($express_id && $nu)
            {
                $express_row = $ExpressModel->getOne($express_id);

                if ($express_row)
                {
                    $express_pinyin = $express_row['express_pinyin'];
                    $str = lookorder($express_pinyin, $nu, $api_id, $api_sceret);
                    if(isset($str['status']) && $str['status'] == 200)
                    {
                        foreach ($str['data'] as $key => $val)
                        {
                            $time    = $val['time'];
                            $context = $val['context'];

                            $deliver_info[] = [
                                'time'=>$time,
                                'context'=>$context,
                            ];
                        }
                    }


                }

            }
        }
        elseif ('kuaidiniao' == $channel)
        {
            Web_ConfigModel::value('kuaidiniao_status');

            $e_business_id = Web_ConfigModel::value('kuaidiniao_e_business_id');
            $app_key = Web_ConfigModel::value('kuaidiniao_app_key');


            $express_code = $express_row['express_pinyin_kdn'];


            $api = new Api_KdNiao($e_business_id, $app_key);
            $request_rows =
                array (
                    'OrderCode' =>   $order_id,  //订单编号
                    'ShipperCode' => $express_code, //物流公司编码
                    'LogisticCode' => $nu            //物流单号
                );
            $rs_str =  $api->getOrderTracesByJson($request_rows);
            Yf_Log::log(encode_json($rs_str), Yf_Log::ERROR, 'db');

            if($rs_str && isset($rs_str['Success']) && $rs_str['Success'] && $rs_str['Traces'])
            {
                foreach ($rs_str['Traces'] as $key => $val)
                {
                    $time    = $val['AcceptTime'];
                    $context = $val['AcceptStation'];
                    $deliver_info[] = [
                        'time'=>$time,
                        'context'=>$context,
                    ];
                }
            }
            else
            {
                {
                    //如果快递鸟快递查询没有成功就用阿里快递查询
                    {
                        $r = Kuaidi::find(['num'=>$nu,'type'=>$express_row['express_pinyin']]);
                        //判断返回值是否是数组格式的，不是数组格式的返回值报错
                        if(is_array($r))
                        {
                            foreach ($r as $key => $val)
                            {
                                $time    = $val['time'];
                                $context = $val['status'];
                                $deliver_info[] = [
                                    'time'=>$time,
                                    'context'=>$context,
                                ];
                            }

                        }

                    }

                }
            }
        }
        elseif('ali' == $channel)
        {
            $r = Kuaidi::find(['num'=>$nu,'type'=>$express_row['express_pinyin']]);

            //判断返回值是否是数组格式的，不是数组格式的返回值报错
            if(is_array($r))
            {
                foreach ($r as $key => $val)
                {
                    $time    = $val['time'];
                    $context = $val['status'];
                    $deliver_info[] = [
                        'time'=>$time,
                        'context'=>$context,
                    ];
                }

            }
        }

         $this->data->addBody(-140, $deliver_info);
    }


//http://api.ickd.cn/?id=[]&secret=[]&com=[]&nu=[]&type=[]&encode=[]&ord=[]&lang=[]
    /*com	必须	快递公司代码（英文），所支持快递公司见如下列表
    nu	必须	快递单号，长度必须大于5位
    id	必须	授权KEY，申请请点击快递查询API申请方式
    在新版中ID为一个纯数字型，此时必须添加参数secret（secret为一个小写的字符串）
    secret	必选(新增)	该参数为新增加，老用户可以使用申请时填写的邮箱和接收到的KEY值登录http://api.ickd.cn/users/查看对应secret值
    type	可选	返回结果类型，值分别为 html | json（默认） | text | xml
    encode	可选	gbk（默认）| utf8
    ord	可选	asc（默认）|desc，返回结果排序
    lang	可选	en返回英文结果，目前仅支持部分快递（EMS、顺丰、DHL）*/
   public function lookorder($com, $nu, $api_id, $api_sceret)
    {
        //爱查快递
        $url2="http://api.ickd.cn/?com=".$com."&nu=".$nu."&id=".$api_id."&secret=".$api_sceret."&type=html&encode=utf8";

        //快递100  show=[0|1|2|3]
        /*$url2="http://api.kuaidi100.com/api?id=$api_id&com=$com&nu=$nu&valicode=[]&show=2&muti=1&order=desc";
        $con = file_get_contents($url2);*/

        $post_data = array();
        $post_data["customer"] = "$api_id";
        $key= "$api_sceret" ;
        $post_data["param"] = '{"com":"'.$com.'","num":"'.$nu.'"}';

        $url='http://poll.kuaidi100.com/poll/query.do';
        $post_data["sign"] = md5($post_data["param"].$key.$post_data["customer"]);
        $post_data["sign"] = strtoupper($post_data["sign"]);
        $o="";
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
        }
        $post_data=substr($o,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $data = str_replace("\&quot;",'"',$result );
        $data = json_decode($data,true);
        return $data;
    }
}