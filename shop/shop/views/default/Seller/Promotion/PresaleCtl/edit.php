<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<div class="content">
	<div class="form-style">
        <form method="post" id="form" action="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Presale&met=edit&typ=e">
		
		<dl>
			<dt><i>*</i>预售名称：</dt>
			<dd><input value="<?=$data['presale_name']?>" name="presale_name" type="text" class="text w300"></dd>
		</dl>
		
		 <dl>
            <dt><i>*</i><?=__('预售定金开始时间')?>：</dt>
            <dd>
                <input type="text" value="<?=$data['presale_start_time']?>" readonly="readonly" autocomplete="off" name="presale_start_time" id="start_time" class="text w150 hasDatepicker"/><em><i class="iconfont icon-rili"></i></em>
               
            </dd>
        </dl>
        <dl>
            <dt><i>*</i><?=__('预售定金结束时间')?>：</dt>
            <dd>
                <input type="text" value="<?=$data['presale_end_time']?>"  readonly="readonly" autocomplete="off" name="presale_end_time" id="end_time" class="text w150"/><em><i class="iconfont icon-rili"></i></em>
               
            </dd>
        </dl>
		<dl>
            <dt><i>*</i><?=__('预售尾款时间')?>：</dt>
            <dd>
                <input type="text" value="<?=$data['presale_final_time']?>" readonly="readonly" autocomplete="off" name="presale_final_time" id="final_time" class="text w150"/><em><i class="iconfont icon-rili"></i></em> 起 72小时后结束
                
            </dd>
        </dl>	
        <dl>
			<dt><i>*</i>预售定金：</dt>
			<dd>
				<input value="<?=$data['presale_deposit']?>" name="presale_deposit" type="text" value="" class="text w150 ml4 mr4"><span>元</span>
				<p class="hint"><?=__('定金须在1-1000之间')?></p>
			</dd>
		</dl>
		<dl>
			<dt><i>*</i>购买上限：</dt>
			<dd>
				<input value="<?=$data['presale_lower_limit']?>" name="presale_lower_limit" type="text" value="0" class="text w150 ml4 mr4">
		</dl>
		<dl>
			<dt></dt>
			<input type="hidden" id="presale_id" value="<?=$data['presale_id']?>">
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
        
        
        $('#final_time').datetimepicker({
            controlType: 'select',
            maxDate:maxdate,
            format: 'Y-m-d H:i:s',
            onShow:function( ct ){
                this.setOptions({
                    minDate:($('#end_time').val() && (new Date(Date.parse($('#end_time').val().replace(/-/g, "/")))) > (new Date()))?(new Date(Date.parse($('#end_time').val().replace(/-/g, "/")))):(new Date())
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
                    var date2 = new Date(Date.parse(param.replace(/-/g, "/"))); //套餐开始时间

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
                },
                
                //自定义规则，结束时间大于开始时间
                finalGrateThansEndDate  : function(element, param, field)
                {
                    var s_time = $("#end_time").val();
                    var date1 = new Date(Date.parse(element.value.replace(/-/g, "/")));
                    var date2 = new Date(Date.parse(s_time.replace(/-/g, "/")));

                    return date1 > date2 || "尾款时间必须大于定金结束时间";
                }

            },
            fields: {
                'presale_name': 'required;length[~25]',
                'presale_start_time': 'required;greaterThanStartDate["<?=date('Y-m-d H:i:s')?>"];lessThanEndDate["<?=$combo['combo_end_time']?>"]',
                'presale_end_time': 'required;lessThanEndDate["<?=$combo['combo_end_time']?>"];startGrateThansEndDate;',
                'presale_deposit': 'required;',
                'presale_lower_limit': 'required;integer;range[0~9999999999]',
                'presale_final_time': 'required;lessThanEndDate["<?=$combo['combo_end_time']?>"];finalGrateThansEndDate;'
            },
			valid: function(form){
                var me = this;
                // 提交表单之前，hold住表单，并且在以后每次hold住时执行回调
                me.holdSubmit(function(){
                    Public.tips.error('正在处理中...');
                });
                var presale_id = $('#presale_id').val();
                $.ajax({
                    url: "index.php?ctl=Seller_Promotion_Presale&met=editPresale&typ=json&id="+presale_id,
                    data: $(form).serialize(),
                    type: "POST",
                    success:function(e){
                        if(e.status == 200)
                        {
                            var data = e.data;
                            Public.tips.success('操作成功!');
                            location.href="index.php?ctl=Seller_Promotion_Presale&met=index&typ=e";//成功后跳转
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