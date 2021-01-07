<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<?php if (Web_ConfigModel::value('bargain_status') == 1) { ?>
    <div class="content">
        <form id="form">
            <div class="form-style">
            <dl>
                <dt><i>*</i><?= __('参与商品') ?>：</dt>
                <dd>
                    <div class="selected-goods fn-hide" style="display:none">
                        <div class="goods-image"><img src=""/></div>
                        <div class="goods-name"></div>
                        <div class="goods-spec"></div>
                        <div class="goods-price"><?= __('销售价') ?>：<span></span></div>
                    </div>
                    <a class="bbc_seller_btns button button_blue btn_show_search_goods" href="javascript:void(0);"><?= __('选择商品') ?></a>

                    <input type="hidden" name="goods_id" id="goods_id"/>
                    <input type="hidden" name="common_id" id="common_id"/>
                    <div class="search-goods-list fn-clear">
                        <div class="search-goods-list-hd">
                            <label><?= __('搜索店内商品') ?></label>
                            <input type="text" name="goods_name" class="text w200" id="key" value="" placeholder="请输入商品名称"/>
                            <a class="button btn_search_goods" href="javascript:void(0);"><i class="iconfont icon-btnsearch"></i><?= __('搜索') ?></a>
                        </div>
                        <div class="search-goods-list-bd fn-clear"></div>
                        <a href="javascript:void(0);" class="close btn_hide_search_goods">X</a>
                    </div>
                    <p class="hint"><?= __('虚拟商品不参加砍价') ?></p>
                    <p class="hint"><?= __('商品在砍价活动时间内，不可参加其他促销活动') ?>。</p>
                </dd>
            </dl>
            <dl>
                <dt><i>*</i><?= __('活动开始时间') ?>：</dt>
                <dd>
                    <input type="text" autocomplete="off" readonly name="start_time" id="start_time" class="text w120"/><em><i class="iconfont icon-rili"></i></em>
                </dd>
            </dl>
            <dl>
                <dt><i>*</i><?= __('活动结束时间') ?>：</dt>
                <dd>
                    <input type="text" autocomplete="off" readonly="readonly" name="end_time" id="end_time" class="text w120"/><em><i class="iconfont icon-rili"></i></em>
                </dd>
            </dl>
            <dl>
                <dt><?= __('商品原价') ?>：</dt>
                <dd>
                    <input type="text" name="goods_price" readonly class="text w200" aria-required="true">
                </dd>
            </dl>
            <dl>
                <dt><i>*</i><?= __('商品底价') ?>：</dt>
                <dd>
                    <input type="text" name="bargain_price" class="text w200" aria-required="true">
                </dd>
            </dl>
            <dl>
                <dt><?= __('商品库存') ?>：</dt>
                <dd>
                    <input type="text" name="goods_stock" readonly class="text w200" aria-required="true">
                </dd>
            </dl>
            <dl>
                <dt><i>*</i><?= __('砍价库存') ?>：</dt>
                <dd>
                    <input type="text" name="bargain_stock" class="text w200" aria-required="true">
                </dd>
            </dl>
            <dl>
                <dt><?= __('砍价规则') ?>：</dt>
                <dd>
                    <div>
                        <input type="radio" class="mr4" name="bargain_type" value="1" checked>
                        <span><?= __('共') ?></span><input type="text" value="" class="text w50 ml4 mr4" name="type_num"><span><?= __('刀砍至底价') ?></span>
                    </div>
                    <div>
                        <input type="radio" class="mr4" name="bargain_type" value="2">
                        <span><?= __('每人最多可砍') ?></span><input type="text" value="" class="text w50 ml4 mr4" name="type_price"><span><?= __('元。（不计砍价次数，砍到底价为止，低于商品原价与商品底价之前的差价）') ?></span>
                    </div>
                </dd>
            </dl>
            <dl>
                <dt><?= __('活动分享描述') ?>：</dt>
                <dd>
                    <input type="text" name="bargain_desc" class="text w400" aria-required="true" maxlength="25"><span id="num">1</span><span>/25</span>
                </dd>
            </dl>
            <dl>
                <dt></dt>
                <dd><input type="submit" class="button bbc_seller_submit_btns" value="提交"></dd>
            </dl>
        </div>
        </form>
    </div>
<?php } else { ?>
     <div class="notic_close"><?= __('平台未开启砍价活动') ?></div>
<?php } ?>
    <script type="text/javascript">
        $(document).ready(function () {

            $(".btn_show_search_goods").on('click', function () {
                $('.search-goods-list').show();
                $('.btn_search_goods').click();
            });
            $(".btn_hide_search_goods").on('click', function () {
                $('.search-goods-list').hide();
            });

            //分享描述字数控制
            $("input[name='bargain_desc']").keyup(function () {
                var lengths = $(this).val().length;
                if (lengths > 25) {
                    $(this).val($(this).val().substring(0, 24));
                    lengths = 25;
                }
                if (lengths <= 0) {
                    lengths = 0;
                }
                $("#num").html(lengths);
            });

            var combo_end_time = $.trim("<?=$combo['combo_end_time']?>");
            var maxdate = new Date(Date.parse(combo_end_time.replace(/-/g, "/")));

            $('#start_time').datetimepicker({
                controlType: 'select',
                minDate: new Date(),
                format: 'Y-m-d H:i:s',
                onShow: function (ct) {
                    this.setOptions({
                        maxDate: ($('#end_time').val() && (new Date(Date.parse($('#end_time').val().replace(/-/g, "/"))) < maxdate)) ? (new Date(Date.parse($('#end_time').val().replace(/-/g, "/")))) : maxdate
                    })
                }
            });
            $('#end_time').datetimepicker({
                controlType: 'select',
                maxDate: maxdate,
                format: 'Y-m-d H:i:s',
                onShow: function (ct) {
                    this.setOptions({
                        minDate: ($('#start_time').val() && (new Date(Date.parse($('#start_time').val().replace(/-/g, "/")))) > (new Date())) ? (new Date(Date.parse($('#start_time').val().replace(/-/g, "/")))) : (new Date())
                    })
                }
            });

            //搜索店铺商品
            $('.btn_search_goods').on('click', function () {
                var url = "index.php?ctl=Seller_Promotion_Bargain&met=getBargainGoods&typ=e";
                var key = $("#key").val();
                url = key ? url + "&goods_name=" + key : url;
                $('.search-goods-list-bd').load(url);
            });
            //分页
            $('.search-goods-list-bd').on('click', '.page a', function () {
                $('.search-goods-list-bd').load($(this).attr('href'));
                return false;
            });

            //选中商品的商品信息
            $('.search-goods-list-bd').on('click', '[data-type="btn_add_goods"]', function () {
                var goods_id = $(this).attr('data-id');
                var common_id = $(this).attr('common-id');
                var goods_name = $(this).parents("li").find(".goods-name").html();
                var goods_spec = $(this).parents("li").find(".goods-spec").html();
                var goods_price = $(this).parents("li").find(".goods-price span").html();
                var goods_image = $(this).parents("li").find("img").attr("src");
                var goods_stock = $(this).parents("li").find("input[name='goods_stock']").val();
                $("input[name='goods_id']").val(goods_id);
                $("input[name='common_id']").val(common_id);
                $(".selected-goods").find("img").attr("src", goods_image);
                $(".selected-goods").find(".goods-name").html(goods_name);
                $(".selected-goods").find(".goods-spec").html(goods_spec);
                $(".selected-goods").find(".goods-price").find("span").html(goods_price);
                $("input[name='goods_price']").val(goods_price);
                $("input[name='goods_stock']").val(goods_stock);
                $(".selected-goods").show();
                $(".goods_price").show();
                $('.search-goods-list').hide();
                $('#goods_id').isValid();
                $("#pintuan_stock").isValid();
            });

            //表单提交验证
            $('#form').validator({
                debug: true,
                theme: 'yellow_right',
                timely: true,
                stopOnError: true,
                rules: {
                    checkStock: function (element) {
                        var goods_stock = $("input[name='goods_stock']").val();
                        var bargain_stock = $("input[name='bargain_stock']").val();
                        if (!/^[0-9]*[1-9][0-9]*$/.test(bargain_stock)) {
                            return '<?=__("砍价库存为正整数")?>';
                        }

                        if (Number(bargain_stock) > Number(goods_stock)) {
                            return '<?=__("砍价库存必须小于商品库存")?>';
                        }
                    },
                    checkPrice: function (element) {
                        var goods_price = $("input[name='goods_price']").val();
                        if (goods_price) {
                            if (Number(goods_price) <= element.value) {
                                return '<?=__("砍价底价必须小于商品原价！")?>';
                            }
                        }

                        if (isNaN(element.value)) {
                            return '<?=__("请输入正确的价格格式")?>';
                        }

                        if (!/^[0-9]\d*(.\d{1,2})?$/.test(element.value)) {
                            return '<?=__("价格保留小数点后两位")?>';
                        }
                    },
                    //自定义规则,大于当前时间，如果通过返回true，否则返回错误消息
                    greaterThanStartDate: function (element, param, field) {
                        var date1 = new Date(Date.parse((element.value).replace(/-/g, "/")));//开始时间
                        param = JSON.parse(param);
                        var date2 = new Date(Date.parse(param.replace(/-/g, "/"))); //套餐开始时间

                        return date1 > date2 || '<?=__("活动开始时间不能小于")?>' + param;
                    },
                    //自定义规则，小于套餐活动结束时间
                    lessThanEndDate: function (element, param, field) {
                        var date1 = new Date(Date.parse((element.value).replace(/-/g, "/")));//选择的结束时间
                        param = JSON.parse(param);
                        var date2 = new Date(Date.parse(param.replace(/-/g, "/")));  //套餐结束时间
                        return date1 < date2 || '<?=__("活动结束时间不能大于")?>' + param;
                    },
                    //自定义规则，结束时间大于开始时间
                    startGrateThansEndDate: function (element, param, field) {
                        var s_time = $("#start_time").val();
                        var date1 = new Date(Date.parse(element.value.replace(/-/g, "/")));
                        var date2 = new Date(Date.parse(s_time.replace(/-/g, "/")));

                        if (date1 <= date2) {
                            return '<?=__("结束时间必须大于开始时间")?>';
                        }
                    },
                    check: function (element) {
                        var bargain_type = $("input[name='bargain_type']:checked").val();
                        if (bargain_type == 2) {
                            var goods_price = $("input[name='goods_price']").val();
                            var bargain_price = $("input[name='bargain_price']").val();
                            var order_price = (Number(goods_price) - Number(bargain_price)).toFixed(2);
                            if (!/^[0-9]\d*(.\d{1,2})?$/.test(element.value)) {
                                return '<?=__("价格保留小数点后两位")?>';
                            }
                            if (Number(element.value) >= order_price) {
                                return '<?=__("最多可砍价格低于商品原价与商品底价之前的差价")?>';
                            }
                        }
                    },
                },
                fields: {
                    'goods_id': 'required;',
                    'start_time': 'required;greaterThanStartDate["<?=date('Y-m-d H:i:s')?>"];lessThanEndDate["<?=$combo['combo_end_time']?>"]',
                    'end_time': 'required;lessThanEndDate["<?=$combo['combo_end_time']?>"];startGrateThansEndDate;',
                    'bargain_price': 'required;checkPrice',
                    'bargain_stock': 'required;checkStock',
                    'type_num': 'integer(+);range(2 ~);check',
                    'type_price': 'range(0.01~1000000);check',
                },
                valid: function (form) {
                    var _this = this;
                    // 提交表单之前，hold住表单，并且在以后每次hold住时执行回调
                    _this.holdSubmit(function () {
                        Public.tips.error('<?=__('正在处理中...')?>');
                    });
                    var bargain_type = $("input[name='bargain_type']:checked").val();
                    var bargain_num_price = $("input[name='bargain_type']:checked").next().next().val();
                    var data = {
                        goods_id: $("input[name='goods_id']").val(),
                        start_time:$("#start_time").val(),
                        end_time:$("#end_time").val(),
                        goods_price: $("input[name='goods_price']").val(),
                        bargain_price: $("input[name='bargain_price']").val(),
                        bargain_stock: $("input[name='bargain_stock']").val(),
                        bargain_type: bargain_type,
                        bargain_num_price: bargain_num_price,
                        bargain_desc: $("input[name='bargain_desc']").val(),
                    };
                    $.ajax({
                        url: "index.php?ctl=Seller_Promotion_Bargain&met=addBargain&typ=json",
                        data: data,
                        type: "POST",
                        success: function (e) {
                            if (e.status == 200) {
                                var data = e.data;
                                Public.tips.success('操作成功!');
                                var dest_url = "index.php?ctl=Seller_Promotion_Bargain&met=index&typ=e";//成功后跳转
                                setTimeout(window.location.href = dest_url, 3000);
                            }
                            else {
                                Public.tips.error(e.msg);
                            }
                            _this.holdSubmit(false);
                        }
                    });
                }
            });

        });
    </script>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>