<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<!--添加参加分佣的商品模板--->
<table style="display:none;">
    <tbody id="goods-sku-item-tpl">
        <tr data-goods-id="__id">
            <td width="50">
                <input type="hidden" name="common_id_ids[]" value="__id|__shopid|__goodsprice" />

                <div>
                    <div class="pic-thumb">
                        <img alt="" data-src="__image" style="max-width:36px;max-height:36px;border:solid 1px #ccc;"/>
                    </div>
                </div>
            </td>
            <td class="tl"><a> __name </a></td>
            <td class="goods-price" width="90"> __price </td>
            <td width="50">
                <span class="handel">
                    <a href="javascript:void(0);" class="remove-join-goods" data-good-id="__id">
                        <i class="iconfont icon-quxiaodingdan"></i><p><?=__('移除')?></p>
                    </a>
                </span>
            </td>
        </tr>
    </tbody>
</table>


<div class="form-style">
    <form id="form" action="" method="post" onSubmit="return checkPrice();">
        <dl>
            <dt><?=__('参与PLUS会员商品')?>：</dt>
            <dd>
                <p> <span></span> </p>
                <table class="table-list-style mb15">
                    <thead>
                        <tr>
                            <th class="tl" colspan="2"><?=__('商品名称')?></th>
                            <th width="90"><?=__('商品价格')?></th>
                            <th width="90"><?=__('操作')?></th>
                        </tr>
                    </thead>
                    <tbody class="join-act-goods-sku"></tbody>
                </table>

                <div class="mm"><a href="javascript:void(0)" data-increase-id='1' class="button btn-ctl-select-goods bbc_seller_btns"><?=__('选择商品')?></a></div>

                <div class="div-shop-goods-box search-goods-list">
                    <div id="cou-sku-options"></div>
                    <a class="btn_hide_search_goods close" href="javascript:void(0);" style="display:none;">&#215;</a>
                </div>
            </dd>
        </dl>
        <dl>
            <dt></dt>
            <dd>
                <input type="submit" class="button button_blue bbc_seller_submit_btns" value="<?=__('提交')?>">
                <input type="hidden" name="act" value="save">
            </dd>
        </dl>

    </form>
</div>
 
<!-- 设置商品模板 -->
<table style="display:none;">
    <tbody id="goods-sku-temp">
        <tr data-cou-level-selected-sku="__id" data-level="__level">
            <td width="50">
                <div>
                    <div class="pic-thumb">
                        <img alt="" data-src="__image" style="max-width:36px;max-height:36px;"/>
                    </div>
                </div>
            </td>
            <td class="tl"><a href="<?=Yf_Registry::get('url')?>?ctl=Goods_Goods&met=goods&type=goods&gid=__id" target="_blank"> __name </a></td>
            <td width="100" nctype="bundling_data_price"><s> __price </s></td>
            <td width="100"><input type="text" class="text w50" name="rule_levle[__level][skus][__id]" value="__goodsprice" data-max-price="__goodsprice" /></td>
            <td width="70">
                <span>
                    <a href="javascript:void(0);" class="" remove-goods-sku="__id"> <i class="iconfont icon-quxiaodingdan"></i><p><?=__('移除')?></p></a>
                </span>
            </td>
        </tr>
    </tbody>
</table>

<script>

$(function(){

	$('.btn-ctl-select-goods').click(function(){
		$(this).hide();
        $('.btn_hide_search_goods').show();		//关闭按钮显示
        $('.search-goods-list').show();
		
		var increase_id = $(this).attr('data-increase-id');
		var url = SITE_URL + '?ctl=Seller_Goods_PlusGoods&met=getGoodsListByShop&typ=e';
        $('#cou-sku-options').load(url,{id:increase_id});
	});

    //分页
    $('#cou-sku-options').on('click', '.page a', function() {
        $('#cou-sku-options').load($(this).attr('href'));
        return false;
    });
	
    $('.btn_hide_search_goods').click(function() {
        $(this).hide();
        $('#cou-sku-options').html('');
        $('.btn-ctl-select-goods').show();
    });

    /*移除商品 */
    $('body').on('click','.remove-join-goods',function(){
        $(this).parents('tr').remove();
        var id = $(this).attr('data-good-id');
        $("div[btn-disabled='"+id+"']").hide();//按钮隐藏
        //$("div[btn-enabled='"+id+"']").show();//按钮显示
        $("div[btn-enabled='"+id+"']").css({display:"inline-block"});//按钮显示
        $("div[btn-enabled='"+id+"']").addClass('button');//按钮显示
    });
	
	//$(".join-act-goods-sku tr[data-goods-id='"+id+"']")

	$('body').on('click','.rm_btn_status',function(){
       var id = $(this).attr('data-id');
	   $(".join-act-goods-sku tr[data-goods-id='"+id+"']").remove();
	   $("div[btn-disabled='"+id+"']").hide();//按钮隐藏
        //$("div[btn-enabled='"+id+"']").show();//按钮显示
        $("div[btn-enabled='"+id+"']").css({display:"inline-block"});//按钮显示
        $("div[btn-enabled='"+id+"']").addClass('button');//按钮显示
    });
	 
	
    var nextId = (function(){
        var i = 10000;
        return function() {
            return ++i;
        };
    })();
 
	//提交设置
    $('#form').validator({
        debug:true,
        ignore: ':hidden',
        theme: 'yellow_right',
        timely: false,
        stopOnError: false,
        fields: {
			'join_act_common_id':'required;'
        },
        valid: function(form){
            var me = this;
            // 提交表单之前，hold住表单，并且在以后每次hold住时执行回调
            me.holdSubmit(function(){
                Public.tips.error("<?=__('正在处理中...')?>");
            });
            $.ajax({
                url: SITE_URL  + "?ctl=Seller_Goods_PlusGoods&met=savePlusGoods&typ=json",
                data: $(form).serialize(),
                async:'false',
                type: "POST",
                success:function(e){
                    if(e.status == 200)
                    {
                        Public.tips.success("<?=__('操作成功!')?>");
                        setTimeout(window.location.href='index.php?ctl=Seller_Goods_PlusGoods&met=index&typ=e',5000);
                    }
                    else
                    {
                        Public.tips.error(e.msg);
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