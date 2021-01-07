<?php if (!defined('ROOT_PATH')) exit('No Permission');?>

<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
</head>
<body>

<div class="freight">
	<div class="tabmenu">
		<ul>
        	<li><a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Transport&met=tplarea&typ=e"><?=__('售卖区域模板设置')?></a></li>
            <?php if(!$data['data']['id']) {?>
            <li  class="active bbc_seller_bg"><a href="javascript:void(0);"><?=__('添加售卖区域')?></a></li>
            <?php }else { ?>
            <li class="active bbc_seller_bg"><a href="javascript:void(0);"><?=__('编辑售卖区域')?></a></li>
            <?php }?>
        </ul>

    </div>


    <form id="form" method="post" >
    <div class="form-style">

    <input type="hidden" name="data" id="data" value="<?php $data['data'];?>">
    <input type="hidden" name="area_id" id="transport_area_id" value="<?=$data['data']['id']?>" />
        <dl class="dl">
            <dt><i><?=__('*')?></i><?=__('模板名称：')?></dt>
            <dd style="width:25%"><input type="text" class="text w120" name="area_name" id="area_name" value="<?=$data['data']['name']?>" /></dd>
        </dl>
        <dl class="dl">
            <dt><i><?=__('*')?></i><?=__('选择区域：')?></dt>
            <dd style="width:25%"><input type="radio" name="all_city" value="0" onclick="choose_area(0)" <?php if($data['data']['all_city'] == 0){?>checked="checked"<?php }?> />全国</dd>
            <dd style="width:25%"><input type="radio" name="all_city" value="1"  onclick="choose_area(1)" <?php if($data['data']['all_city'] == 1){?>checked="checked"<?php }?> />自定义</dd>
        </dl>
        <div id="address_ctiy_list" <?php if($data['data']['all_city'] == 0){?>style="display:none;"<?php }?>>
        <dl class="address_ctiy_list">
            <dt>
                <label class="checkbox"><input type="checkbox" name="selectAll" onclick="select_all()" id="selectAll" /><?=__('全选')?></label>
            </dt>

			<dd>
                <label onclick="search('all')" id="all_province" 
                    <?php if ($province == 'all') {?>
                    class='red'
                        <?php } ?>><?=__('全部')?></label>
				<p class="range iblock">
					<?php $range  = range('A','Z');?>
					<?php  foreach($range as $item){?>
					<b onclick='search("<?php echo $item;?>");'><span id="<?php echo $item;?>" 

                    <?php if ($search_province == $item) {?>
                    class='red'
                        <?php } ?>
                        ><?php echo $item;?></span></b>
					<?php } ?>
				</p>
				<div class="area-search iblock fr">
					<input type="text" name="search_area" placeholder="输入地区关键字">
					<i class="iconfont icon-search"  id="search_area" onclick="search();"></i>
				</div>
				
			</dd>
        </dl>
        <?php foreach($data['district'] as $key => $val){?>
        <?php if(isset($val['city'])){?>
            <dl class="address_ctiy_list">
                <dt>
                    <label class="checkbox province" >
                        <input type="checkbox" name="province[]" value="<?=($val['district_id'])?>" <?php if($data['data']['area_ids_arr'] && in_array($val['district_id'],$data['data']['area_ids_arr'])   || in_array($val['district_id'], $area_ids_arr_l)){?>checked="checked"<?php }?> />
                        <?=($val['district_name'])?>
                    </label>
                </dt>
                <dd>
                    <?php foreach($val['city'] as $citykey => $cityval){ ?>
                        <div class="city-module">
<label class="checkbox city city-second fz0 <?php if($cityval['area']){ ?> active mr0<?php }?>">
                                <input <?php if ($data['data']['area_ids_arr'] && in_array($cityval['district_id'], $data['data']['area_ids_arr']) || in_array($cityval['district_id'], $area_ids_arr_l)){ ?>checked="checked"  <?php } ?> type="checkbox" name="city[]" data-province="<?= ($val['district_id']) ?>" value="<?= ($cityval['district_id']) ?>"/>
                                <span 
<?php if(($district_parent_arr && in_array($cityval['district_id'],$district_parent_arr)) || $data['data']['area_parents_ids_arr'] && in_array($cityval['district_id'],$data['data']['area_parents_ids_arr'])){?>
                                    class="red"  
                                <?php }?>

                                    <?php if (in_array($cityval['district_id'], $district_parent_arr)){ ?> 
                                            class="active"  
                                        <?php } ?> 
                                >
                                <?= ($cityval['district_name']) ?>
                                </span>
                            </label>
							<?php if($cityval['area']){ ?>
							<i class="drap mr20"></i>
                            <ul class="area">
                            <?php foreach($cityval['area'] as $areakey => $areaval){?>
                                <li>
                                    <label class="checkbox city">
                                        <input <?php if($data['data']['area_ids_arr'] && in_array($areaval['district_id'],$data['data']['area_ids_arr'])  || in_array($areaval['district_id'], $area_ids_arr_l)){?>checked="checked" <?php }?> type="checkbox"  name="area[]" data-province="<?=($val['district_id'])?>" data-city="<?= ($cityval['district_id']) ?>" value="<?=($areaval['district_id'])?>" />
                                   <span 
                                        <?php if (in_array($areaval['district_id'], $district_parent_arr)){ ?> 
                                            class="active"  
                                        <?php } ?>
                                    >
                                        <?=($areaval['district_name']) ?>
                                    </span><!-- 搜索到的内容下划线加active -->
                                    </label>
                                </li>
                            <?php }?>
							</ul>
                            <?php }?>
                        </div>
                    <?php } ?>
                </dd>
            </dl>
        <?php }}?>
        </div>
        <dl>
            <dt></dt>
            <dd>
                <input type="hidden" id='area_ids_arr_search' name="area_ids_arr_search"  value="<?php echo($area_ids_arr_search);?>" />
                <input type="button" class="button bbc_seller_submit_btns" value="<?=__('确认提交')?>" />
            </dd>
        </dl>
    </div>
    </form>

    </table>
    </form>
</div>

<script type="text/javascript">
    var area_ids_arr_search_1 = $("input[name='area_ids_arr_search']").val();
    var bv_1 = area_ids_arr_search_1.replace(/^,+/,"").replace(/,+$/,"");
       $("input[name='province[]']").attr("checked", false);
       $("input[name='city[]']").attr("checked", false);
       $("input[name='area[]']").attr("checked", false);
    var area_ids_ar_l = bv_1.split(",")
    area_ids_ar_l.forEach(function(e){  
       $("input[name='province[]'][value="+e+"]").attr("checked", true);
       $("input[name='city[]'][value="+e+"]").attr("checked", true);
       $("input[name='area[]'][value="+e+"]").attr("checked", true);
    });

    $("input[name='search_area']").on('keypress', function (event) { 
        if (event.keyCode == "13") { 
            search();
        }
    })



    $("input").click(function () {
        var area_ids_arr_search = $("input[name='area_ids_arr_search']").val();
        var bv = area_ids_arr_search.replace(/^,+/,"").replace(/,+$/,"");
        var area_ids_arr = bv.split(",") 
        var re = $.inArray($(this).val(),area_ids_arr)
        if (re >= 0) {
            area_ids_arr.splice($.inArray($(this).val(),area_ids_arr),1);
        }
       $("#area_ids_arr_search").val(area_ids_arr);
    });

    function search (province_py) {
        var area_ids_arr_search = $("input[name='area_ids_arr_search']").val();

        var bv = area_ids_arr_search.replace(/^,+/,"").replace(/,+$/,"");

        var area_ids_arr = bv.split(",")
        area_ids_arr.forEach(function(e){  
           $("input[name='province[]'][value="+e+"]").attr("checked", true);
           $("input[name='city[]'][value="+e+"]").attr("checked", true);
           $("input[name='area[]'][value="+e+"]").attr("checked", true);
        });

        $.each($('input:checkbox:checked'),function(){
                area_ids_arr_search = area_ids_arr_search + ',' +$(this).val();
        });

        $("input[name='area_ids_arr_search']").val(area_ids_arr_search);
        if(typeof province_py == null || String(province_py) == "" || String(province_py) == "undefined") {
             province_py = '';
        } else {
            $("#"+province_py).addClass("red");
        }
        var search_area = $("input[name='search_area']").val();
        var transport_area_id = $("#transport_area_id").val();
        window.location.href = "<?= Yf_Registry::get('url') ?>?ctl=Seller_Transport&met=tplarea&act=area&search_province="+province_py + "&id=" + transport_area_id + "&search_area=" + search_area + "&area_ids_arr_search=" +area_ids_arr_search+ "&type=search";
    }

    function choose_area(type){

        if(type == 0){
            $('#address_ctiy_list').hide();
        }else{
            $('#address_ctiy_list').show();
        }

    }
    
    function select_all() {
        var obj = $('#selectAll');
        var cks = $("input");
        var ckslen = cks.length;
        for(var i=0;i<ckslen;i++) {
            if(cks[i].type === 'checkbox') {
                cks[i].checked = obj[0].checked;
            }
        }
        var selectAll = $("#selectAll").attr("checked");
        if (typeof  selectAll == 'undefined') {
            $("#area_ids_arr_search").val('');
        }

    }


    $(document).ready(function(){
        var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Transport&met=areaSubmit&typ=json';
        $(".bbc_seller_submit_btns").click(function () {
            var area_name = $("#area_name").val();
            if(!area_name){
                Public.tips({type: 1, content: '请填写模板名称'});
                return false;
            }
            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: $("#form").serialize(),
                success: function (a) {
                    if (a.status == 200) {
                        window.location.href = "<?= Yf_Registry::get('url') ?>?ctl=Seller_Transport&met=tplarea&typ=e";
                    } else {
                        Public.tips({type: 1, content: a.msg});
                    }
                }
            });
        });

        $('input[name="province[]"]').click(function(){
            var _self=this;
            if ($(this).attr('checked') == true){
                $('input[data-province="' + $(this).val() + '"]').each(function(){
                    if ($(this).attr('disabled') == false){
                        $(this).prop('checked', _self.checked);
                    }
                });
            }else{
                $('input[data-province="' + $(this).val() + '"]').each(function(){
                    $(this).prop('checked', _self.checked);
                });
            }
            if($('input[data-province="'+$(this).val()+'"]').size() == $('input[data-province="'+$(this).val()+'"]:checked').size()) {
                $(this).prop('checked', true);
            }else {
                $(this).prop('checked', false);
            }
        });
        $('input[name="city[]"]').click(function(){ 

            var bv = $("input[name='area_ids_arr_search']").val().replace(/^,+/,"").replace(/,+$/,"");
            var area_ids_arr = bv.split(",");
            var _self = this;
            $('input[data-city="' + $(this).val() + '"]').each(function (e,a) {
                $(this).prop('checked', _self.checked);
            });
           
            if (_self.checked) {
				$(this).next().addClass("red");
                if ($('input[data-province="'+$(this).attr('data-province')+'"]').size() == $('input[data-province="'+$(this).attr('data-province')+'"]:checked').size()) {
                    $('input[value="'+$(this).attr('data-province')+'"]').prop('checked', true);
                }
                $('input[data-city="' + $(this).val() + '"]').each(function (e,a) {
                    if ($.inArray($(this).val(),area_ids_arr) < 0) {
                       bv = bv + ',' + $(a).val();
                    }
                });
                bv = bv + ',' + $(_self).val();
                $("#area_ids_arr_search").val(bv);
            } else {
				$(this).next().removeClass("red");
                $('input[value="'+$(this).attr('data-province')+'"]').prop('checked', false);
                $('input[data-city="' + $(this).val() + '"]').each(function (e,a) {
                    if ($.inArray($(this).val(),area_ids_arr) >= 0) {
                        area_ids_arr.splice($.inArray($(this).val(),area_ids_arr),1);
                    }
                });
                $("#area_ids_arr_search").val(area_ids_arr);
            }
        });
        $('input[name="area[]"]').click(function () {
            var bv = $("input[name='area_ids_arr_search']").val().replace(/^,+/,"").replace(/,+$/,"");
            var area_ids_arr = bv.split(",");


            var _self = this;
            if (_self.checked) {
				$(this).parents(".city-module").find(".city-second span").addClass("red");
                if ($('input[data-city="' + $(this).attr('data-city') + '"]').size() == $('input[data-city="' + $(this).attr('data-city') + '"]:checked').size()) {
                    $('input[value="' + $(this).attr('data-city') + '"]').prop('checked', true);
                }
                if ($('input[data-province="' + $(this).attr('data-province') + '"]').size() == $('input[data-province="' + $(this).attr('data-province') + '"]:checked').size()) {
                    $('input[value="' + $(this).attr('data-province') + '"]').prop('checked', true);
                }

                bv = bv + ',' + $(this).val();
                $("#area_ids_arr_search").val(bv);
            } else {
				if($('input[data-city="' + $(this).attr('data-city') + '"]:checked').size()=="0"){
					$(this).parents(".city-module").find(".city-second span").removeClass("red");
				}
				
                $('input[value="' + $(this).attr('data-province') + '"]').prop('checked', false);
                $('input[value="' + $(this).attr('data-city') + '"]').prop('checked', false);
                area_ids_arr.splice($.inArray($(this).val(),area_ids_arr),1);
                $("#area_ids_arr_search").val(area_ids_arr);
            }
        });
    });
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>