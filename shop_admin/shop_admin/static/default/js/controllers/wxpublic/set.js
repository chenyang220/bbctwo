$(function () {
    //微信公众号设置
    if ($("#wechat-public-api-setting-form").length > 0) {
        $("#wechat-public-api-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "wechat_public[wechat_public_name]": "required;",
                "wechat_public[wechat_public_start_id]": "required;",
                "wechat_public[wechat_public_wxaccount]": "required;",
                "wechat_public[wechat_public_call_url]": "required;",
                "wechat_public[wechat_public_token]": "required;",
                "wechat_public[wechat_public_appid]": "required;",
                "wechat_public[wechat_public_secret]": "required;"
            },
            valid: function (form) { //alert(SITE_URL + "?ctl=Config&met=editWechatPublic&typ=json");

                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {

                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#wechat-public-api-setting-form").serialize(), function (data) {
                            if (data.status == 200) {
                                parent.Public.tips({content: "修改操作成功！"});
                            }
                            else {
                                parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                            }
                        });
                    },
                    function () {
                    });
            }
        }).on("click", "a.im-submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});


