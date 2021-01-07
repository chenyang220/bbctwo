/**
 * JS多语言翻译
 *
 * Translation.get('app.测试')
 * @type {Object}
 *
 */
var Translation = new Lang({
    messages: source,
    locale: $.cookie("lang_selected")
});

function __(str) {
    if ($.cookie('lang_selected') == "zh_CN") {
        return str;
    }
    // return Translation.get('app.'+str);
    var string = Translation.get("app." + str);
    // 如果string = app.str
    if (string == ("app." + str)) {
        // 使用百度翻译 php 往 app.js 文件里 写内容
        /*
        * var source = {
        *   "en_US.app": {
        *       "选择模板": "Select the template",
        *       "操作": "operation",
        *   }
        * };
        *
        * */
        var q = str;
        var appid = "20180612000175379";
        var key = "D97vhe8oNh28SpesJ_w3";
        var salt = (new Date).getTime();
        var str1 = appid + q + salt + key;
        var sign = MD5(str1);
        var yy = $.cookie("lang_selected") == "zh_CN" ? "zh":($.cookie("lang_selected") == "en_US" ? "en":"cht");
        $.ajax({
            url: "https://fanyi-api.baidu.com/api/trans/vip/translate",
            type: "post",
            dataType: "jsonp",
            data: {
                q: q,
                from: "auto",
                to: yy,
                appid: appid,
                salt: salt,
                sign: sign
            },
            success: function (msg) {
                console.log(msg);
                string = msg.trans_result[0].dst;
                
                /**
                 * 异步请求后台写入app.js文件的内容 做记录
                 * 只写一次
                 */
                var flag = true;
                if (flag) {
                    $.ajax({
                        url: BASE_URL + "/messages/TranslateJS.php",
                        type: "get",
                        dataType: "json",
                        data: {
                            js: 1,
                            str: q,
                            dst: msg.trans_result[0].dst
                        },
                        success: function (res) {
                            flag = false;
                            window.location.reload();
                            // return msg.trans_result[0].dst;
                        }
                    });
                }
            }
        });
    }
    return string;
}
