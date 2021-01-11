//shop-setting-form
$(function () {
    
    if ($("#shop-setting-form").length > 0) {
        var catorageCombo = Business.timeZoneCombo($("#time_zone"), {
            editable: false,
            extraListHtml: "",
            //addOptions: {value: -1, text: '选择类别'},
            defaultSelected: null,
            trigger: true,
            width: 120 * 3,
            callback: {
                onChange: function (data) {
                    //alert(this.getText());
                    $("#time_zone_id").val(this.getValue());
                }
            }
        }, "customertype");
        
        catorageCombo.selectByValue(time_zone_id);
        
        
        var languageCombo = Business.languageCombo($("#language"), {
            editable: false,
            extraListHtml: "",
            //addOptions: {value: -1, text: '选择类别'},
            defaultSelected: null,
            trigger: true,
            width: 120 * 3,
            callback: {
                onChange: function (data) {
                    //alert(this.getText());
                    $("#language_id").val(this.getValue());
                }
            }
        });
        
        languageCombo.selectByValue(language_id);
        
        
        var dateFormatCombo = Business.categoryCombo($("#date_format_combo"), {
            editable: false,
            extraListHtml: "",
            //addOptions: {value: -1, text: '选择类别'},
            defaultSelected: null,
            trigger: true,
            width: 120 * 3,
            callback: {
                onChange: function (data) {
                    //alert(this.getText());
                    $("#date_format").val(this.getValue());
                }
            }
        }, "date_format");
        
        dateFormatCombo.selectByValue(date_format_combo);
        
        var timeFormatCombo = Business.categoryCombo($("#time_format_combo"), {
            editable: false,
            extraListHtml: "",
            //addOptions: {value: -1, text: '选择类别'},
            defaultSelected: null,
            trigger: true,
            width: 120 * 3,
            callback: {
                onChange: function (data) {
                    //alert(this.getText());
                    $("#time_format").val(this.getValue());
                }
            }
        }, "time_format");
        
        timeFormatCombo.selectByValue(time_format_combo);
        
        
        $("#shop-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                //'icp_number': 'required;email;'
            },
            valid: function (form) {
                var me = this;
                // 提交表单之前，hold住表单，防止重复提交
                me.holdSubmit();
                
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#shop-setting-form").serialize(), function (data) {
                            if (data.status == 200) {
                                parent.Public.tips({content: __("修改操作成功！")});
                                parent.location.reload();
                            } else {
                                parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                            }
                            
                            // 提交表单成功后，释放hold，如果不释放hold，就变成了只能提交一次的表单
                            me.holdSubmit(false);
                        });
                    },
                    function () {
                        me.holdSubmit(false);
                    });
            }
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
    
    if ($("#shop-setting-form-wap").length > 0) {
        $("#shop-setting-form-wap").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                //'icp_number': 'required;email;'
            },
            valid: function (form) {
                var me = this;
                // 提交表单之前，hold住表单，防止重复提交
                me.holdSubmit();
                
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#shop-setting-form-wap").serialize(), function (data) {
                            if (data.status == 200) {
                                parent.Public.tips({content: __("修改操作成功！")});
                            } else {
                                parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                            }
                            
                            // 提交表单成功后，释放hold，如果不释放hold，就变成了只能提交一次的表单
                            me.holdSubmit(false);
                        });
                    },
                    function () {
                        me.holdSubmit(false);
                    });
            }
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
    
    if ($("#shop-setting-form-pc").length > 0) {
        $("#shop-setting-form-pc").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                //'icp_number': 'required;email;'
            },
            valid: function (form) {
                var me = this;
                // 提交表单之前，hold住表单，防止重复提交
                me.holdSubmit();
                
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#shop-setting-form-pc").serialize(), function (data) {
                            if (data.status == 200) {
                                parent.Public.tips({content: "修改操作成功！"});
                            }
                            else {
                                parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                            }
                            
                            // 提交表单成功后，释放hold，如果不释放hold，就变成了只能提交一次的表单
                            me.holdSubmit(false);
                        });
                    },
                    function () {
                        me.holdSubmit(false);
                    });
            }
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
    if ($("#shop-setting-form-wxapp").length > 0) {
        $("#shop-setting-form-wxapp").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                //'icp_number': 'required;email;'
            },
            valid: function (form) {
                var me = this;
                // 提交表单之前，hold住表单，防止重复提交
                me.holdSubmit();
                
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#shop-setting-form-wxapp").serialize(), function (data) {
                            if (data.status == 200) {
                                parent.Public.tips({content: "修改操作成功！"});
                            }
                            else {
                                parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                            }
                            
                            // 提交表单成功后，释放hold，如果不释放hold，就变成了只能提交一次的表单
                            me.holdSubmit(false);
                        });
                    },
                    function () {
                        me.holdSubmit(false);
                    });
            }
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
    if ($("#theme-setting-form").length > 0) {
        var themeCombo = Business.themeCombo($("#theme"), {
            editable: false,
            extraListHtml: "",
            //addOptions: {value: -1, text: '选择类别'},
            defaultSelected: null,
            trigger: true,
            width: 120 * 3,
            callback: {
                onChange: function (data) {
                    //alert(this.getText());
                    $("#theme_id").val(this.getValue());
                }
            }
        });
        
        themeCombo.selectByValue(theme_id);
        
        $("#theme-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                //'icp_number': 'required;email;'
            },
            valid: function (form) {
                var me = this;
                // 提交表单之前，hold住表单，防止重复提交
                me.holdSubmit();
                
                parent.$.dialog.confirm("修改风格后，有可能需要修改对应的首页模板、首页幻灯片、首页联动小图, 是否继续？", function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#theme-setting-form").serialize(), function (data) {
                            if (data.status == 200) {
                                parent.Public.tips({content: "修改操作成功！"});
                            }
                            else {
                                parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                            }
                            
                            // 提交表单成功后，释放hold，如果不释放hold，就变成了只能提交一次的表单
                            me.holdSubmit(false);
                        });
                    },
                    function () {
                        me.holdSubmit(false);
                    });
            }
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});


//防灌水
$(function () {
    if ($("#dump-setting-form").length > 0) {
        $("#dump-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#dump-setting-form").serialize(), function (data) {
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
        });
    }
});

//upload
$(function () {
    if ($("#upload-setting-form").length > 0) {
        $("#upload-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "upload[image_max_filesize]": "required;integer[+];range[0~" + max_upload_file_size + "]",
                "upload[image_allow_ext]": "required;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#upload-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
});

//shop api


$(function () {
    if ($("#shop_api-setting-form").length > 0) {
        
        $("#shop_api-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "shop_api[shop_api_url]": "required;",
                "shop_api[shop_api_key]": "required;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=editShopApi&typ=json", $("#shop_api-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
        
    }
    
    if ($("#ucenter-shop_api-setting-form").length) {
        
        
        $("#ucenter-shop_api-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "ucenter_api[ucenter_api_url]": "required;",
                "ucenter_api[ucenter_api_key]": "required;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=editUcenterApi&typ=json", $("#ucenter-shop_api-setting-form").serialize(), function (data) {
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
        }).on("click", "a.ucenter-submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
        
        
    }
    
    if ($("#paycenter-shop_api-setting-form").length) {
        
        $("#paycenter-shop_api-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "paycenter_api[paycenter_api_url]": "required;",
                "paycenter_api[paycenter_api_key]": "required;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=editPaycenterApi&typ=json", $("#paycenter-shop_api-setting-form").serialize(), function (data) {
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
        }).on("click", "a.paycenter-submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
    if ($("#im-shop_api-setting-form").length > 0) {
        
        $("#im-shop_api-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "im_api[im_api_url]": "required;",
                "im_api[im_api_key]": "required;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=editImApi&typ=json", $("#im-shop_api-setting-form").serialize(), function (data) {
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


//email  msg
$(function () {
    if ($("#email_msg-setting-form").length > 0) {
        $("#email_msg-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "email[email_host]": "required;",
                "email[email_addr]": "required;",
                "email[email_pass]": "required;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#email_msg-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
        
        
        $("#send_test_email").click(function () {
            $.ajax({
                type: "POST",
                url: SITE_URL + "?ctl=Config&met=testEmail&typ=json",
                data: $("#email_msg-setting-form").serialize(),
                error: function () {
                    alert("测试邮件发送失败，请重新配置邮件服务器");
                },
                success: function (html) {
                    alert(html.msg);
                },
                dataType: "json"
            });
        });
        
    }
    
    
    if ($("#sms-setting-form").length > 0) {
        $("#sms-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "sms[sms_account]": "required;",
                "sms[sms_pass]": "required;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#sms-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
        
        
        $("#send_test_sms").click(function () {
            $.ajax({
                type: "POST",
                url: SITE_URL + "?ctl=Config&met=testSms&typ=json",
                data: $("#sms-setting-form").serialize(),
                error: function () {
                    alert("发送失败，请重新配置短信账号");
                },
                success: function (html) {
                    alert(html.msg);
                },
                dataType: "json"
            });
        });
        
    }
});

//消息模板

//促销设置
$(function () {
    if ($("#promotion-setting-form").length > 0) {
        $("#promotion-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            /* fields: {
                'voucher[promotion_voucher_price]': 'required;integer[+0];',
                'voucher[promotion_voucher_storetimes_limit]': 'required;integer[+];',
                'voucher[promotion_voucher_buyertimes_limit]': 'required;integer[+];range[1~20]'
            }, */
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#promotion-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});


//团购幻灯片设置
$(function () {
    if ($("#slider-setting-form").length > 0) {
        $("#slider-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#slider-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});

//首页幻灯
$(function () {
    if ($("#index_slider-setting-form").length > 0) {
        $("#index_slider-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#index_slider-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});

//首页幻灯
$(function () {
    if ($("#index_liandong-setting-form").length > 0) {
        $("#index_liandong-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#index_liandong-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});

//入驻幻灯
$(function () {
    if ($("#join_slider-setting-form").length > 0) {
        $("#join_slider-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#join_slider-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});

//代金券设置
$(function () {
    if ($("#voucher-setting-form").length > 0) {
        $("#voucher-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "voucher[promotion_voucher_price]": "required;integer[+0];",
                "voucher[promotion_voucher_storetimes_limit]": "required;integer[+];",
                // "voucher[promotion_voucher_buyertimes_limit]": "required;integer[+];range[1~20]"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#voucher-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});


//满送设置
$(function () {
    if ($("#mansong-form").length > 0) {
        $("#mansong-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "mansong[promotion_mansong_price]": "required;integer[+0];"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#mansong-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//满送设置


//限时活动
$(function () {
    if ($("#discount-form").length > 0) {
        $("#discount-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "discount[promotion_discount_price]": "required;integer[+0];"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#discount-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//限时活动


//加价购
$(function () {
    if ($("#increase-form").length > 0) {
        $("#increase-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "increase[promotion_increase_price]": "required;integer[+0];"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#increase-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});

//团购
$(function () {
    if ($("#groupbuy-setting-form").length > 0) {
        $("#groupbuy-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "groupbuy[groupbuy_price]": "required;integer[+0];",
                "groupbuy[groupbuy_review_day]": "required;integer[+0];"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#groupbuy-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
});

//砍价
$(function () {
    if ($("#bargain-setting-form").length > 0) {
        $("#bargain-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "bargain_buy[bargain_buy_price]": "required;integer[+0];",
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#bargain-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
});

//二级域名
$(function () {
    if ($("#shop_domain_form").length > 0) {
        $("#shop_domain_form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "domain[domain_modify_frequency]": "required;integer[+];"
                
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#shop_domain_form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});


//收款信息设置
$(function () {
    if ($("#account-setting-form").length > 0) {
        $("#account-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "set_account[company_name]": "required;",
                "set_account[bank_name]": "required;",
                "set_account[bank_account]": "required;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#account-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
});

//商城设置
$(function () {
    if ($("#setting-setting-form").length > 0) {
        
        $("#setting-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "setting[setting_email]": "email;"
                
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#setting-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//商城防灌水
$(function () {
    if ($("#dumps-setting-form").length > 0) {
        //console.log($('#dumps-setting-form').serialize());
        $("#dumps-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#dumps-setting-form").serialize(), function (data) {
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
        });
    }
});

//运营设置
$(function () {
    if ($("#operation-setting-form").length > 0) {
        /*$('#operation-setting-form').on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__('修改立马生效,是否继续？'), function () {
                    Public.ajaxPost(SITE_URL + '?ctl=Config&met=edit&typ=json', $('#operation-setting-form').serialize(), function (data) {
                        if (data.status == 200) {
                            parent.Public.tips({content: '修改操作成功！'});
                        }
                        else {
                            parent.Public.tips({type: 1, content: data.msg || '操作无法成功，请稍后重试！'});
                        }
                    });
                });
        });*/
        
        $("#operation-setting-form").validator({
            rules: {},
            fields: {},
            valid: function (form) {
                // 表单验证通过，提交表单
                //var fenxiao_cycle = $("#fenxiao_cycle").val();
                var fenxiao_first = $("#fenxiao_first").val();
                var fenxiao_second = $("#fenxiao_second").val();
                var fenxiao_third = $("#fenxiao_third").val();
                var fenxiao_min = $("#fenxiao_min").val();
                // if (!$("#fenxiao_cycle").is(":hidden") && !/^[1-9][0-9]?$/.test(fenxiao_cycle)) {
                //     parent.Public.tips({type: 1, content: "结算周期必须为1-100整数！"});
                //     return false;
                // }
                // if (!fenxiao(fenxiao_first) || !fenxiao(fenxiao_second) || !fenxiao(fenxiao_third) || !fenxiao(fenxiao_min)) {
                //     parent.Public.tips({type: 1, content: "佣金比例必须为" + fenxiao_min + "-100两位小数！"});
                //     return false;
                // }
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#operation-setting-form").serialize(), function (data) {
                        if (data.status == 200) {
                            parent.Public.tips({content: "修改操作成功！"});
                        }
                        else {
                            parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                        }
                    });
                });
            }
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
    function fenxiao(value) {
        var min = $("#fenxiao_min").val();
        if (Number(value) >= Number(min) && Number(value) < 100) {
            return true;
        } else {
            return false;
        }
    }
});
//seo设置 首页
$(function () {
    if ($("#seo-setting-form").length > 0) {
        $("#seo-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#seo-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});

//seo设置 团购
$(function () {
    if ($("#tg-setting-form").length > 0) {
        $("#tg-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#tg-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//seo设置 品牌
$(function () {
    if ($("#brand-setting-form").length > 0) {
        $("#brand-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#brand-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//seo设置 积分
$(function () {
    if ($("#point-setting-form").length > 0) {
        $("#point-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#point-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//seo设置 文章
$(function () {
    if ($("#article-setting-form").length > 0) {
        $("#article-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#article-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//seo设置 店铺
$(function () {
    if ($("#store-setting-form").length > 0) {
        console.log($("#store-setting-form").serialize());
        $("#store-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#store-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//seo设置 商品
$(function () {
    if ($("#product-setting-form").length > 0) {
        $("#product-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#product-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//seo设置 商品分类
$(function () {
    if ($("#category-setting-form").length > 0) {
        $("#category-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#category-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});


//seo设置 文章
$(function () {
    if ($("#information-setting-form").length > 0) {
        $("#information-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#information-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});


//seo设置 sns
$(function () {
    if ($("#sns-setting-form").length > 0) {
        $("#sns-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#sns-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});

//会员积分设置
$(function () {
    if ($("#points-setting-form").length > 0) {
        $("#points-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "points[points_reg]": "integer[+0];",
                "points[points_login]": "integer[+0];",
                "points[points_evaluate]": "integer[+0];",
                "points[points_recharge]": "integer[+0];",
                "points[points_order]": "integer[+0];"
                
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#points-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//会员经验值设置
$(function () {
    if ($("#grade-setting-form").length > 0) {
        $("#grade-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "grade[grade_login]": "integer[+0];",
                "grade[grade_evaluate]": "integer[+0];",
                "grade[grade_recharge]": "integer[+0];",
                "grade[grade_order]": "integer[+0];"
                
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#grade-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//图片管理-上传参数
$(function () {
    if ($("#parameters-setting-form").length > 0) {
        $("#parameters-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#parameters-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//图片管理-默认图片
$(function () {
    if ($("#acquiesce-setting-form").length > 0) {
        $("#acquiesce-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#acquiesce-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//搜索管理-默认搜索词
$(function () {
    if ($("#search-setting-form").length > 0) {
        $("#search-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#search-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});


//
$(function () {
    if ($("#shop-sphinx-form").length > 0) {
        $("#shop-sphinx-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#shop-sphinx-form").serialize(), function (data) {
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
        });
    }
});


$(function () {
    if ($("#kuaidi-setting-form").length > 0) {
        $("#kuaidi-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#kuaidi-setting-form").serialize(), function (data) {
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
        });
    }
});


$(function () {
    if ($("#kuaidiniao-setting-form").length > 0) {
        $("#kuaidiniao-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#kuaidiniao-setting-form").serialize(), function (data) {
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
        });
    }
});


$(function () {
    if ($("#logistics-setting-form").length > 0) {
        $("#logistics-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#logistics-setting-form").serialize(), function (data) {
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
        });
    }
});


$(function () {
    if ($("#licence-setting-form").length > 0) {
        $("#licence-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=editLicence&typ=json", $("#licence-setting-form").serialize(), function (data) {
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
        });
    }
});

$(function () {
    $("#setting-mobile_wx").on("click", "a.submit-btn", function (e) {
        parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
            Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#setting-mobile_wx").serialize(), function (data) {
                if (data.status == 200) {
                    parent.Public.tips({content: "修改操作成功！"});
                }
                else {
                    parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                }
            });
        });
    });
    
});

$(function () {
    $("#app-setting-form").on("click", "a.submit-btn", function (e) {
        parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
            Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#app-setting-form").serialize(), function (data) {
                if (data.status == 200) {
                    parent.Public.tips({content: "修改操作成功！"});
                }
                else {
                    parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                }
            });
        });
    });
    
});


//开启分销员设置
$(function () {
    if ($("#directseller-setting-form").length > 0) {
        $("#directseller-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#directseller-setting-form").serialize(), function (data) {
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
        });
    }
});

//开启IM设置
$(function () {
    if ($("#shop-im-form").length > 0) {
        $("#shop-im-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#shop-im-form").serialize(), function (data) {
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
        });
    }
});


//城市分站
$(function () {
    if ($("#subsite-setting-form").length > 0) {
        $("#subsite-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#subsite-setting-form").serialize(), function (data) {
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
        });
    }
});

//城市分站
$(function () {
    if ($("#live-setting-form").length > 0) {
        $("#live-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#live-setting-form").serialize(), function (data) {
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
        });
    }
});

//退款退货设置
$(function () {
    if ($("#plat-setting-form").length > 0) {
        $("#plat-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#plat-setting-form").serialize(), function (data) {
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
        });
    }
});

//专题栏目
$(function () {
    if ($("#column-setting-form").length > 0) {
        $("#column-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#column-setting-form").serialize(), function (data) {
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
        });
    }
});

//首页模板设置
$(function () {
    if ($("#index-mb-setting").length > 0) {
        $("#index-mb-setting").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#index-mb-setting").serialize(), function (data) {
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
        });
    }
});

//主题风格
$(function () {
    $("#manage-form").on("click", "a.submit-btn", function (e) {
        parent.$.dialog.confirm(__("确定立马生效,是否继续？"), function () {
                Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#manage-form").serialize(), function (data) {
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
    });
    
});

//供应商入驻幻灯
$(function () {
    if ($("#supplier_slider-setting-form").length > 0) {
        $("#supplier_slider-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#supplier_slider-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});

//积分商城首页的广告
$(function () {
    if ($("#promotiom-setting-form").length > 0) {
        $("#promotiom-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=promotionConfig&typ=json", $("#promotiom-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});

//供应商首页幻灯片
$(function () {
    if ($("#supplier_index-setting-form").length > 0) {
        $("#supplier_index-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#supplier_index-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});

//供应商首页小图
$(function () {
    if ($("#supplier_index_img-setting-form").length > 0) {
        $("#supplier_index_img-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#supplier_index_img-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }

    //设置商城logo
    if ($("#mall-setting-form").length > 0) {
        $("#mall-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                //'icp_number': 'required;email;'
            },
            valid: function (form) {
                var me = this;
                // 提交表单之前，hold住表单，防止重复提交
                me.holdSubmit();
                
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#mall-setting-form").serialize(), function (data) {
                            if (data.status == 200) {
                                parent.Public.tips({content: "修改操作成功！"});
                            }
                            else {
                                parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                            }
                            
                            // 提交表单成功后，释放hold，如果不释放hold，就变成了只能提交一次的表单
                            me.holdSubmit(false);
                        });
                    },
                    function () {
                        me.holdSubmit(false);
                    });
            }
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
    if ($("#analytics-shop_api-setting-form").length) {
        $("input[name^='analytics_api']").click(function () {
            var container = $("#container", window.parent.document);
            if ($(this).val() == 1) {
                container.find("#analytics").show();
            } else {
                container.find("#analytics").hide();
            }
        });
        $("#analytics-shop_api-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "analytics_api[analytics_api_url]": "required;",
                "analytics_api[analytics_app_name]": "required;",
                "analytics_api[analytics_api_key]": "required;",
                "analytics_api[analytics_app_id]": "required;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=editAnalyticsApi&typ=json", $("#analytics-shop_api-setting-form").serialize(), function (data) {
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
        }).on("click", "a.analytics-submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
});

//自营店铺是否显示
$(function () {
    if ($("#selfshop-setting-form").length > 0) {
        $("#selfshop-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#selfshop-setting-form").serialize(), function (data) {
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
        });
    }
});

//后台手机设置
$(function () {
    if ($("#redPacketSet-form").length > 0) {
        $("#redPacketSet-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#redPacketSet-form").serialize(), function (data) {
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
        });
    }
});

//社区管理设置
$(function () {
    if ($("#communitySet-form").length > 0) {
        $("#communitySet-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#communitySet-form").serialize(), function (data) {
                        if (data.status == 200) {
                            parent.Public.tips({content: "修改操作成功！"});
                            setTimeout(function () { parent.location.reload(); }, 1000);                            
                        }
                        else {
                            parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                        }
                    });
                },
                function () {
                });
        });
    }
});


//plus会员设置
$(function () {
    if ($("#plus-setting-form").length > 0) {
        $("#plus-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "plus[plus_switch]": "required;integer[+0];",
                "plus[plus_shopping_mode]": "required;integer[+0];",
                "plus[plus_shopping_price]": "required;floatpot2;",
                "plus[plus_probationership]": "required;integer[+0];",
                "plus[plus_desc]": "required;"
                
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#plus-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    } 
});

//plus会员权益设置
$(function () {
    if ($("#plus-quity-setting-form").length > 0) {
        $("#plus-quity-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            rules:{
                plus_rate:function(element) {
                    var user_grade_rate_min = $("#user_grade_rate_min").text();
                    if(!/^\d+(\.\d{1,2})?$/.test(element.value))
                    {
                      return "小数点后只能保留两位！";
                    }
                    if(Number(element.value) >= Number(user_grade_rate_min))
                    {
                      return "plus折扣率必须高于会员最高折扣！";
                    }
                },
                plus_red_packet:function(element) {
                    var plus_quota = $("#plus_quota").val();
                    if(!/^\d+(\.\d{1,2})?$/.test(element.value))
                    {
                      return "小数点后只能保留两位！";
                    }
                    if(Number(plus_quota) >= Number(element.value))
                    {
                      return "消费金额必须大于红包金额！";
                    }
                },
                plus_integral:function(element) {
                    if(!/^\d+(\.\d{1,2})?$/.test(element.value))
                    {
                      return "小数点后只能保留两位！";
                    }
                    if(Number(element.value)<=1)
                    {
                      return "须填写>1的数字！";
                    }
                },
                plus_general_red:function(element) {
                    if(!/^\d+(\.\d{1,2})?$/.test(element.value))
                    {
                      return "小数点后只能保留两位！";
                    }
                },
                plus_quota:function(element) {
                    if(!/^\d+(\.\d{1,2})?$/.test(element.value))
                    {
                      return "小数点后只能保留两位！";
                    }
                },
            },
            fields: {
                "plus[plus_rate]": "required;plus_rate", 
                "plus[plus_integral]": "required;plus_integral;",
                "plus[plus_general_red]": "required;plus_general_red;",
                "plus[plus_quota]": "required;plus_quota",
                "plus[plus_red_packet]": "required;plus_red_packet;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#plus-quity-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    } 
});

//我的二维码设置
$(function () {
    if ($("#my_qrcode_img_form").length > 0) {
        $("#my_qrcode_img_form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#my_qrcode_img_form").serialize(), function (data) {
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
        });
    }
});
//商家公众号价格
$(function () {
    if ($("#sellerWx-setting-form").length > 0) {
        $("#sellerWx-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "seller_wx[sellerWx_price]": "required;integer[+0];", 
                "seller_wx[sellerWx_day]": "required;integer[+0];",
                "seller_wx[sellerWx_bank]": "required;",
                "seller_wx[sellerWx_number]": "required;integer[+0];",
                "seller_wx[sellerWx_user]": "required;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#sellerWx-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }  
});

//商家公众号价格
$(function () {
    if ($("#distribution-setting-form").length > 0) {
        $("#distribution-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "distribution_cf[distribution_invitations]": "required;integer;", 
                "distribution_cf[distribution_gprice]": "required;integer;",
                "distribution_cf[direct_reward]": "required;integer[+0];",
                "distribution_cf[indirect_reward]": "required;integer[+0];",
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#distribution-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }  
});

//wap端专题栏目
$(function () {
    if ($("#column-wap-setting-form").length > 0) {
        $("#column-wap-setting-form").on("click", "a.submit-btn", function (e) {
            parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                    Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#column-wap-setting-form").serialize(), function (data) {
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
        });
    }
});
//二维码设置
$(function () {
    console.log($("#qrcode-setting-form").length);
    if ($("#qrcode-setting-form").length > 0) {
        $("#qrcode-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#qrcode-setting-form").serialize(), function (data) {
                            if (data.status == 200) {
                                parent.Public.tips({content: "操作成功！"});
                            }else {
                                parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                            }
                        });
                    },
                    function () {
                    
                    });
            }
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});

//秒杀活动
$(function () {
    if ($("#seckill-form").length > 0) {
        $("#seckill-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "seckill[promotion_seckill_price]": "required;integer[+0];"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#seckill-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }


    
});
//秒杀活动

//预售活动
$(function () {
    if ($("#presale-form").length > 0) {
        $("#presale-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "presale[promotion_presale_price]": "required;integer[+0];"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#presale-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
    
});
//预售活动


//WAP设置分类模板
$(function () {
    if ($("#setting-setCat").length > 0) {
        $("#setting-setCat").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {},
            valid: function (form) {
                // var setCat = $("input[name='setCat']:checked").val();
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#setting-setCat").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
});

//营业执照设置
$(function () {
    if ($("#business-setting-form").length > 0) {
        $("#business-setting-form").validator({
            // ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "business[shop_company_name]": "required;",
                "business[business_id]": "required;",
                "business[legal_person]": "required;",
                "business[business_license_location]": "required;",
                "business[company_registered_capital]": "required;",
                "business[business_licence_start]": "required;",
                "business[business_licence_end]": "required;",
                "business[company_address_detail]": "required;",
                "business[business_sphere]": "required;",
                "business[business_license_electronic]": "required;"
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#business-setting-form").serialize(), function (data) {
                            if (data.status == 200) {
                                Public.tips({content: "修改操作成功！"});
                            }
                            else {
                                Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                            }
                        });
                    },
                    function () {

                    });
            }
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
});

//同城配送-买家配送费设置
$(function () {
    if ($("#sdelivery-setting-form").length > 0) {
        $("#sdelivery-setting-form").validator({
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: true,
            fields: {
                "set_delivery[delivery]": "required;integer[+0];",
            },
            valid: function (form) {
                parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {
                        Public.ajaxPost(SITE_URL + "?ctl=Config&met=edit&typ=json", $("#sdelivery-setting-form").serialize(), function (data) {
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
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }
});