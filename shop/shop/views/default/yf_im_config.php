<!--
/**
 * Created by PhpStorm.
 * Author: Michael Ma
 * Date: 2018年08月28日
 * Time: 11:31:25
 *
 * Notes：统一配置IM的文件代码
 *
 */
 对应表
 IM 状态        im_statu             0-关;1-开
 IM URL         im_url              IM的地址
 IM Api URL     im_api_url          IM的App token    未使用到,但是token是隐含使用加密的
 IM App Key     im_api_key          IM的App app_id
 IM Secret      im_admin_api_url    IM的App secret
 -->
<?php if (Yf_Registry::get('im_statu')) { ?>
    <!-- layui.js 资源加载-->
    <script src="//im.yuanfeng.cn/layui/layui.js"></script>
    <!--请确保该WEB已经引入jQuery-->
    <script id="YFIM" type="text/javascript">
        $(function () {
            var param = {
                app_id: "<?= Yf_Registry::get('im_api_key');?>",
                token: "<?= Yf_Registry::get('im_api_url');?>"
            }, url = "<?= Yf_Registry::get('im_url')?>/app";
            // YF商城JS取cookie的方式,如其他WEB请自行更换获取cookie的方式即可[PS：IM用户注册的用户名 免登陆,该请求已经处理登录/注册]
            if ($.cookie("user_account")) {
                $.get(url, param, function (res) {
                    $("#YFIM").before(res.code);
                }, "jsonp");
            }
        });
    </script>
<?php } ?>