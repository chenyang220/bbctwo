<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
    include $this->view->getTplPath() . '/' . 'header.php';
?>
<link rel="stylesheet" href="<?= $this->view->css ?>/security.css">
<link rel="stylesheet" href="<?= $this->view->css ?>/intlTelInput.css">
<script src="<?= $this->view->js ?>/intlTelInput.js"></script>
</div>
<div class="form-style-layout">
    <div class="form-style">
        <div class="step clearfix">
            <dl class="step-first current">
                <dt><?= __('1.验证身份') ?></dt>
            </dl>
            <dl class="current">
                <dt><?= __('2')?><?=$action?><?= __('手机')?></dt>
                <dd></dd>
            </dl>
            <dl class="">
                <dt><?= __('3')?><?=$action?><?= __('完成')?></dt>
                <dd></dd>
            </dl>
        </div>
        <form id="form" name="form" method="post">
            <input type="hidden" value="mobile_verify" name="act">
            <div class="bind-area">
                <dl class="clearfix">
                    <dt><em class="icon-must">*</em><?= __('手机：') ?></dt>
                    <dd class="tl">
                        <?php if ($op = "mobile" && $data['user_mobile_verify'] != 1 && $data['user_mobile']) { ?>
                            <input type="hidden" name="user_mobile" id="re_user_mobile" value="<?= $data['user_mobile'] ?>" />
                            <input type="hidden" name="area_code" id="area_code" value="<?= $data['area_code'] ?>" />
                            <?= $data['user_mobile'] ?>
                        <?php } else { ?>
                            <input type="text" name="user_mobile" id="re_user_mobile" class="text" value="" />
                            <input type="hidden" name="area_code" id="area_code" value="<?= $data['area_code'] ?>" />
                        <?php } ?>
                    </dd>
                </dl>
                <dl>
                    <dt><em>*</em><?= __('图形验证码') ?>：</dt>
                    <dd>
                        <input type="text" name="img_yzm" id="img_yzm" maxlength="6" class='text' placeholder="<?= __('请输入验证码') ?>" default="<i class=&quot;i-def&quot;></i><?= __('看不清？点击图片更换验证码') ?>" />
                        <img onClick="get_randfunc(this);" title="<?= __('换一换') ?>" class="img-code fl wp43" style="cursor:pointer;" src='./libraries/rand_func.php' />
                    </dd>
                </dl>
                <dl class="clearfix">
                    <dt><em class="icon-must">*</em><?= __('手机验证码：') ?></dt>
                    <dd>
                        <input type="text" name="yzm" id="yzm" class="text fl mr10" value="" />
                        <input type="button" class="send fl btn-send" data-type="mobile" value="<?= __('获取手机验证码') ?>" />
                    </dd>
                </dl>
                <dl class="foot">
                    <dt>&nbsp;</dt>
                    <dd class="tl">
                       <input type="submit" value="<?= __('提交') ?>" class="big-submit">
                    </dd>
                </dl>
            </div>
        </form>
    </div>
</div>

<!-- 弹框 -->
<div class="dialog-alert">
    <div class="dis-table">
        <div class="table-cell">
            <div class="bind-tips">
                <div class="tips-text">
                    <div class="dis-table">
                        <div class="table-cell">
                            <p style="margin-top: 15px;"><i class="icon-unbind Js-icon"></i>
                            <span class="text Js_body"></span>
                            </p>
                            <div class="Js_operation"><a href="javascript:;" class="btn btn-sure"><?= __('关联') ?></a><a href="javascript:;" class="btn btn-cancel"><?= __('取消') ?></a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var icon = '<i class="iconfont icon-exclamation-sign"></i>';
    $(".btn-send").click(function () {
        var patrn = /^1\d{10}$/;
        var val = $('#re_user_mobile').val();
        var area_code = $('#area_code').val();
        if (!val) {
            Public.tips.error("<?=__('请填写手机')?>");
        } else if (!patrn.test(val) && area_code == '86') {
            Public.tips.error("<?=__('请填写正确的手机')?>");
        } else {
            var img_yzm = $('#img_yzm').val();
            $.post(SITE_URL + '?ctl=User&met=getMobileYzm&typ=json', 'mobile=' + val + '&yzm=' + img_yzm + '&area_code=' + area_code, function (resp) {
                if (resp.status == 200) {
                    t = setTimeout(countDown, 1000);
                } else {
                    $('.img-code').click();
                    $(".btn-send").attr("disabled", false);
                    $(".btn-send").attr("readonly", false);
                    $("#re_user_mobile").attr("readonly", false);
                    Public.tips.error(resp.msg);
                }
            }, 'json');
        }
    });
    var delayTime = 60;
    var msg = "<?=__('获取验证码')?>";
    
    function countDown() {
        delayTime--;
        $(".btn-send").val(delayTime + "<?=__('秒后重新获取')?>");
        if (delayTime == 0) {
            delayTime = 60;
            $(".btn-send").val(msg);
            $(".btn-send").removeAttr("disabled");
            $(".btn-send").removeAttr("readonly");
            $("#re_user_mobile").removeAttr("disabled");
            $("#re_user_mobile").removeAttr("readonly");
            clearTimeout(t);
        } else {
            t = setTimeout(countDown, 1000);
        }
    }
    $("#re_user_mobile").intlTelInput({
        utilsScript: "<?= $this->view->js ?>/utils.js"
    });
    $(".big-submit").click(function () {
        var ajax_url = SITE_URL + '?ctl=User&met=editMobileInfo&typ=json';
        $('#form').validator({
            ignore: ':hidden',
            theme: 'yellow_right',
            timely: 1,
            stopOnError: false,
            fields: {
                'user_mobile': 'required;',
                'yzm': 'required;',
            },
            valid: function (form) {
                //表单验证通过，提交表单
                $.ajax({
                    url: ajax_url,
                    data: $("#form").serialize(),
                    success: function (a) {
                        if (a.status == 200) {
                            Public.tips.success("<?=__('操作成功！')?>");
                            location.href = SITE_URL + "?ctl=User&met=security";
                        } else {
                            switch(a.msg)
                            {
                            case 'undefined':
                                Public.tips.error("<?=__('操作失败！')?>");
                                break;
                            case '该手机已经被使用':
                                $('.table-cell span').html("<?=__('该手机号已经与用户名为"+ a.data.name +"的账号绑定，请确认是否关联')?>");
                               
                                $(".dialog-alert").show();
                                break;
                            default:
                                Public.tips.error(a.msg);
                                break;
                            }

                            return false;
                        }
                    }
                });
            }
        });
    });
    
    $(function(){
        //取消解除绑定，关闭解除绑定页面
        $(".dialog-alert").on("click", ".btn-cancel", function() {
          $(".dialog-alert").hide();
        });

        //确认解除绑定
        $(".btn-sure").click(function(){
            var mobile = $('#re_user_mobile').val();
            //解除用户绑定
            var ajaxurl = './index.php?ctl=Login&met=bindmobile&typ=json&mobile='+mobile;
            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                async: false,
                success: function (respone)
                {
                    $(".dialog-alert").hide();
                    if(respone.status == 200)
                    {
                        Public.tips.success("<?=__('操作成功！')?>");
                        location.href = SITE_URL + "?ctl=User&met=security";
                    }
                    else
                    {
                        Public.tips.error(respone.msg);
                    }
                }
            });
        })
    });


    //点击验证码
    function get_randfunc(obj) {
        var sj = new Date();
        url = obj.src;
        obj.src = url + '?' + sj;
    }
</script>
</div>
</div>
</div>
<?php
    include $this->view->getTplPath() . '/' . 'footer.php';
?>
