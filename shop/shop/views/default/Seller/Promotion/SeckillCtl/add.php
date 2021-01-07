<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<div class="content">
	<div class="form-style">
        <form method="post" id="form" action="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Seckill&met=add&typ=e">
		<dl>
			<dt>秒杀活动类型：</dt>
			<dd>精品秒杀</dd>
		</dl>
		<dl>
			<dt>秒杀活动名称：</dt>
			<dd><input name="seckill_name" type="text" class="text w300"></dd>
		</dl>
		
		 <dl>
            <dt><i>*</i><?=__('开始时间')?>：</dt>
            <dd>
                <input type="text" readonly="readonly" autocomplete="off" name="seckill_start_time" id="start_time" class="text w120 hasDatepicker"/><em><i class="iconfont icon-rili"></i></em>
                <?php if(!$shop_type){ ?>
                <p class="hint"><?=__('开始时间不能为空且不能早于')?><?=$combo['combo_start_time']?></p>
                <?php } ?>
            </dd>
        </dl>
        <dl>
            <dt><i>*</i><?=__('结束时间')?>：</dt>
            <dd>
                <input type="text" readonly="readonly" autocomplete="off" name="seckill_end_time" id="end_time" class="text w120"/><em><i class="iconfont icon-rili"></i></em>
                <?php if(!$shop_type){ ?>
                <p class="hint"><?=__('结束时间不能为空且不能晚于')?><?=$combo['combo_end_time']?></p>
                <?php } ?>
            </dd>
        </dl>
		<dl>
			<dt>每天秒杀时间段：</dt>
			<dd>
				<label class="mr20"><input class="mr4" name="seckill_time_slot" type="radio" value="1">0:00~2:00</label><label class="mr20"><input class="mr4"  name="seckill_time_slot" type="radio" value="2">8:00~10:00</label>
				<label class="mr20"><input class="mr4" name="seckill_time_slot" type="radio" value="3">10:00~12:00</label><label class="mr20"><input class="mr4" name="seckill_time_slot" type="radio" value="4">12:00~14:00</label>
				<label class="mr20"><input class="mr4" name="seckill_time_slot" type="radio" value="5">14:00~16:00</label><label class="mr20"><input class="mr4" name="seckill_time_slot" type="radio" value="6">16:00~18:00</label>
                <label class="mr20"><input class="mr4" name="seckill_time_slot" type="radio" value="7">18:00~20:00</label><label class="mr20"><input class="mr4" name="seckill_time_slot" type="radio" value="8">20:00~22:00</label>
                <label class="mr20"><input class="mr4" name="seckill_time_slot" type="radio" value="9">22:00~24:00</label>
			</dd>
		</dl>
		<dl>
			<dt>每人限购：</dt>
			<dd>
				<label class="mr20">
					<input name="is_limit" type="checkbox" class="mr4">开启限购
				</label>
				<span>每人限购</span><input name="seckill_lower_limit" type="text" value="1" class="text w50 ml4 mr4"><span>件</span>
			</dd>
		</dl>
		<dl>
			<dt></dt>
			<dd><input type="submit" class="button bbc_seller_submit_btns" value="提交"></dd>
		</dl>
        </form>
	</div>
</div>
<script>
	$(document).ready(function(){

		$('.btn-ctl-select-goods').click(function(){
			$(this).hide();
	        $('.btn_hide_search_goods').show();		//关闭按钮显示
	        $('.search-goods-list').show();
		});
		$('.btn_hide_search_goods').click(function() {
	        $(this).hide();
	        $('.search-goods-list').hide();
	        $('.btn-ctl-select-goods').show();
	    });
         //日历插件
         $('#start_time').datetimepicker({
            controlType: 'select',
            minDate:new Date(),
             format: 'Y-m-d H:i:s',
			onShow:function( ct ){
			this.setOptions({
				maxDate:($('#end_time').val() && (new Date(Date.parse($('#end_time').val().replace(/-/g, "/"))) < maxdate))?(new Date(Date.parse($('#end_time').val().replace(/-/g, "/")))):maxdate
				})
			}
		});

        var combo_end_time = $.trim("<?=$combo['combo_end_time']?>");
        var maxdate =  new Date(Date.parse(combo_end_time.replace(/-/g, "/")));
        $('#end_time').datetimepicker({
            controlType: 'select',
            maxDate:maxdate,
            format: 'Y-m-d H:i:s',
            onShow:function( ct ){
                this.setOptions({
                    minDate:($('#start_time').val() && (new Date(Date.parse($('#start_time').val().replace(/-/g, "/")))) > (new Date()))?(new Date(Date.parse($('#start_time').val().replace(/-/g, "/")))):(new Date())
                })
            }
        });


        $('#form').validator({
            ignore: ':hidden',
            theme: 'yellow_right',
            timely: 1,
            stopOnError: false,
            rules: {
                //自定义规则,大于当前时间，如果通过返回true，否则返回错误消息
                greaterThanStartDate : function(element, param, field)
                {
                    var date1 = new Date(Date.parse((element.value).replace(/-/g, "/")));//开始时间
                    param = JSON.parse(param);
                    var date2 = new Date(Date.parse(param.replace(/-/g, "/")));	//套餐开始时间

                    return date1 > date2 || "活动开始时间不能小于"+ param;
                },
                //自定义规则，小于套餐活动结束时间
                lessThanEndDate  : function(element, param, field)
                {
                    var date1 = new Date(Date.parse((element.value).replace(/-/g, "/")));//选择的结束时间
                    param = JSON.parse(param);
                    var date2 = new Date(Date.parse(param.replace(/-/g, "/")));  //套餐结束时间
                    return date1 < date2 || "活动结束时间不能大于"+ param;
                },
                //自定义规则，结束时间大于开始时间
                startGrateThansEndDate  : function(element, param, field)
                {
                    var s_time = $("#start_time").val();
                    var date1 = new Date(Date.parse(element.value.replace(/-/g, "/")));
                    var date2 = new Date(Date.parse(s_time.replace(/-/g, "/")));

                    return date1 > date2 || "结束时间必须大于开始时间";
                }

            },
            fields: {
                'seckill_name': 'required;length[~25]',
                'seckill_start_time': 'required;greaterThanStartDate["<?=date('Y-m-d H:i:s')?>"];lessThanEndDate["<?=$combo['combo_end_time']?>"]',
                'seckill_end_time': 'required;lessThanEndDate["<?=$combo['combo_end_time']?>"];startGrateThansEndDate;',
                'secki_lower_limit': 'required;integer[+];range[1~9999999999]'
            },
			valid: function(form){
                var me = this;
                // 提交表单之前，hold住表单，并且在以后每次hold住时执行回调
                me.holdSubmit(function(){
                    Public.tips.error('正在处理中...');
                });
                $.ajax({
                    url: "index.php?ctl=Seller_Promotion_Seckill&met=addSeckill&typ=json",
                    data: $(form).serialize(),
                    type: "POST",
                    success:function(e){
                        if(e.status == 200)
                        {
                            var data = e.data;
                            Public.tips.success('操作成功!');
                            location.href="index.php?ctl=Seller_Promotion_Seckill&met=index&op=manage&typ=e&id="+data.seckill_id;//成功后跳转
                        }
                        else
                        {
                            Public.tips.error('操作失败！');
                        }
                        me.holdSubmit(false);
                    }
                });
            }
        });


    });
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>
</script>