<?php if (!defined('ROOT_PATH')) exit('No Permission');?>

<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
    <link href="<?=$this->view->css_com?>/webuploader.css" rel="stylesheet">
    <script src="<?=$this->view->js_com?>/webuploader.js"></script>
    <script src="<?=$this->view->js_com?>/upload/upload_image.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=5At3anZe83x8oOpFap42Gt8eHYpy3wm9&callback=baidu_lbs_geo"></script>
    <style>
        .query{
            background: #ddd;
            padding: 6px 10px;
            margin-right: 10px;
            cursor: pointer;
        }
    </style>
    </head>
    <body>

    <div class="tabmenu">
        <ul>
            <li ><a href="./index.php?ctl=Seller_Shop_Chain&met=chain&typ=e"><?=__('门店列表')?></a></li>

            <?php if($act == 'add') {?>
                <li  class="active bbc_seller_bg"><a href="javascript:void(0);"><?=__('添加门店')?></a></li>
            <?php }
            if($act == 'edit'){?>
                <li class="active bbc_seller_bg"><a href="javascript:void(0);"><?=__('编辑门店')?></a></li>
            <?php }?>
        </ul>

    </div>

    <?php if($act == 'add') {?>
        <form id="form" method="post" action="#"  >
            <div class="ncsc-form-default">
                <h3><?=__('门店账户注册')?></h3>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('登录名')?>：</dt>
                    <dd>
                        <input type="text" class="text w200" name="chain_user" id="chain_user"   value="">
                        <p class="hint"><?=__('登录名请使用中文、字母、数字、下划线（最低三个字符）。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt>
                        <i class="required">*</i>
                        <?=__('登录密码')?>：</dt>
                    <dd class="relative">
                        <input type="password" class="text w200" name="chain_pwd" id="chain_pwd"  autocomplete="off" value="">
                        <span class="new-eye-icon"></span>
                        <p class="hint"><?=__('密码请使用6--20个字符（区分大小写），由字母(必填)、数字(必填)、下划线(可选)组成。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt>
                        <i class="required">*</i>
                        <?=__('确认密码')?>：</dt>
                    <dd class="relative">
                        <input type="password" class="text w200" name="confirm_pwd" id="confirm_pwd" value="">
                        <span class="new-eye-icon"></span>
                        <p class="hint"><?=__('请再次输入登录密码，确保前后输入一致。')?></p>
                    </dd>
                </dl>
                <h3><?=__('门店相关信息')?></h3>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('门店名称')?>：</dt>
                    <dd>
                        <input type="text" class="text w200" name="chain_name" id="chain_name" value="">
                        <p class="hint"><?=__('请认真填写您的门店名称，以确保用户（购买者）线下到店自提时查找。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('所在地区')?>：</dt>
                    <dd>
                        <input type="hidden" name="address_area" id="t" value="" />
                        <input type="hidden" name="province_id" id="id_1" value="" />
                        <input type="hidden" name="city_id" id="id_2" value="" />
                        <input type="hidden" name="area_id" id="id_3" value="" />
                        <div id="d_2">
                            <select id="select_1" name="select_1" onChange="district(this);">
                                <option value="">--<?=__('请选择')?>--</option>
                                <?php foreach($district['items'] as $key=>$val){ ?>
                                    <option value="<?=$val['district_id']?>|1"><?=$val['district_name']?></option>
                                <?php } ?>
                            </select>
                            <select id="select_2" name="select_2" onChange="district(this);" class="hidden"></select>
                            <select id="select_3" name="select_3" onChange="district(this);" class="hidden"></select>
                        </div>
                        <p class="hint"><?=__('所在地区将直接影响购买者在选择线下自提时的地区筛选，因此请如实认真选择全部地区级。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('详细地址')?>：</dt>
                    <dd>
                        <input type="text" class="text w400" name="chain_address" id="chain_address" value="">
                        <span class="query"><?= __('查询') ?></span><span style="color: #fd3d53"><?= __('查询后地图上拾取坐标') ?></span>
                        <p class="hint"><?=__('请认真填写详细地址，以确保用户（购物者）线下到店自提时能最准确的到达您的门店。')?></p>
                    </dd>
                </dl>

                <dl class="dl">
                    <dt><i class="required">*</i><?=__('地图显示')?>：</dt>
                    <dd>
                        <div id="allmap" style="height:600px;border:1px solid gray"></div>
                        <div id="r-result">
                            <i class="required" style="color: red;">* </i>经度: <input id="longitude"  name='longitude' type="text"  class="text w400" value="" />
                            <i class="required" style="color: red;">* </i>纬度: <input id="latitude" name='latitude' type="text" class="text w400"  value=""/>
                        </div>
                    </dd>
                </dl>
                <dl class="dl" style="overflow: inherit;">
                    <dt><i class="required">*</i><?=__('联系电话')?>：</dt>
                    <dd>
                        <input type="text" class="text w200" name="chain_phone" id="re_user_mobile" value="">
                        <input type="hidden"  name="area_code" id="area_code" >
                        <p class="hint"><?=__('请认真填写门店联系电话，方便用户（购物者）通过该电话与您直接取得联系。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('营业时间')?>：</dt>
                    <dd>
                        <textarea class="textarea w400" maxlength="50" rows="2" name="chain_opening_hours" id="chain_opening_hours"></textarea>
                        <p class="hint"><?=__('如实填写您的线下门店营业时间，以免用户（购物者）在营业时间外到店产生误会。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt><i class="required"></i><?=__('交通线路')?>：</dt>
                    <dd>
                        <textarea class="textarea w400" maxlength="50" rows="2" name="chain_traffic_line" id="chain_traffic_line"></textarea>
                        <p class="hint"><?=__('如您的门店周围有公交、地铁线路到达，请填写该选项，多条线路请以“、”进行分隔。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('实拍照片')?>：</dt>
                    <dd>
                        <div class="image">
                            <img id="chainImage" height="160px" width="160px" src="" />
                            <input id="chainimagePath" name="chainimagePath" type="hidden" value=""  />
                        </div>
                        <div id="uploadButton" style="width: 81px;height: 28px;float: left;margin-top:5px;">
                            <i class="iconfont icon-tupianshangchuan"></i><?=__('图片上传')?></div>
                        <div><p class="hint"><?=__('将您的实体店面沿街图上传，方便用户（购物者）线下到店自提时能最准确直观的找到您的门店。')?></p></div>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt></dt>
                    <dd><input type="submit" class="button bbc_seller_submit_btns" value="<?=__('确认提交')?>" /></dd>
                </dl>
            </div>
        </form>
        <script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=5At3anZe83x8oOpFap42Gt8eHYpy3wm9"></script>
        <script type="text/javascript">
            // 百度地图API功能
              var map = new BMap.Map("allmap");            
              var point = new BMap.Point(116.331398,39.897445);
                map.centerAndZoom(point,11);  

                map.enableScrollWheelZoom();   //启用滚轮放大缩小，默认禁用
                map.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用      
              //单击获取点击的经纬度
              map.addEventListener("click",function(e){
                $("input[name='longitude']").val(e.point.lng);
                $("input[name='latitude']").val(e.point.lat);
              });
            map.addControl(new BMap.NavigationControl());
            var local = new BMap.LocalSearch(map, {
                renderOptions: {map: map}
            });
            $(".query").click(function () {
                var address = $("input[name='address_area']").val() + $("input[name='chain_address']").val();
                if (address != "") {
                    local.search(address);
                }
            });
            $("#re_user_mobile").intlTelInput({
//                utilsScript: "<?//= $this->view->js ?>///utils.js"
            });
            $(document).ready(function(){
                console.log(13);
                var ajax_url = './index.php?ctl=Seller_Shop_Chain&met=<?=$act?>Chain&typ=json';

                $('#form').validator({
                    ignore: '',
                    theme: 'yellow_right',
                    timely: 1,
                    stopOnError: false,
                    rules: {
                        password: [/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z_]{6,20}$/, '密码请使用6--20个字符（区分大小写），由字母(必填)、数字(必填)、下划线(可选)组成'],
                        tel:function(){
                            var area_code = $('#area_code').val();
                            var chain_phone = $('#re_user_mobile').val();
                            chain_phone = chain_phone.replace(/\s+/g,"");
                            var reg = /^1[3-9]\d{9}$/;
                            if(area_code==86 && !reg.test(chain_phone)){
                                return '<?=__('请输入正确的手机号码')?>';
                            }
                        },
                    },
                    fields: {
                        "chain_user": {
                            rule: "required;length[3~]",
                            msg: {
                                required :"<?=__('请填写门店登录名')?>",
                                length:"<?=__('请填写正确的门店登录名')?>"
                            }
                        },
                        'select_1':'required',
                        'select_2':'required',
                        //'select_3':'required',
                        'chain_name':'required',
                        'chain_address':'required',
                        'chain_phone':'required;tel',
                        'chain_opening_hours':'required',
                        'longitude':'required',
                        'latitude':'required',
                        'chainimagePath':{
                            rule: "required",
                            msg: {
                                required : "<?=__('请上传图片')?>"
                            }
                        },
                        "chain_pwd": {
                            rule: "required;length[6~20];password",
                            msg: {
                                required : "<?=__('请填写门店登录密码')?>",
                                length : "<?=__('请填写正确密码')?>"
                            }
                        },
                        "confirm_pwd": {
                            rule: "required;match(chain_pwd);password",
                            msg: {
                                required : "<?=__('请填写确认密码')?>",
                                match : "<?=__('与登录密码不同，请重新填写')?>"
                            }
                        },
                        'chain_name': {
                            rule: 'required;length[~25]',
                            length: "<?=__('门店名称不能超过25个字符')?>"
                        }
                    },
                    valid:function(form){
                        var me = this;
                        // 提交表单之前，hold住表单，防止重复提交
                        me.holdSubmit();
                        //表单验证通过，提交表单
                        $.ajax({
                            url: ajax_url,
                            data:$("#form").serialize(),
                            success:function(a){
                                if(a.status == 200)
                                {
                                    Public.tips.success("<?=__('操作成功！')?>");
                                    me.holdSubmit(false);
                                    setTimeout(' location.href="./index.php?ctl=Seller_Shop_Chain&met=chain&typ=e"',3000); //成功后跳转
                                }
                                else
                                {
                                    Public.tips.error(a.msg);

                                    me.holdSubmit(false);
                                    //setTimeout('window.location.reload();', 3000); //成功后跳转
                                }
                            }
                        });
                    }

                });
            });
        </script>


    <?php }
    if($act == 'edit' && $data){?>
        <form method="post" action="#" id="form" enctype="multipart/form-data">
            <div class="ncsc-form-default">
                <input type="hidden" name="chain_id" value="<?=@$data['chain_id']?>">
                <h3><?=__('门店账户注册')?></h3>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('登录名')?>：</dt>
                    <dd>
                        <input type="text" class="text w200" name="chain_user" id="chain_user" value="<?=@$data['chain_user']?>" readOnly="true">
                        <p class="hint"><?=__('登录名请使用中文、字母、数字、下划线（最低三个字符）。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt>
                        <i class="required">*</i>
                        <?=__('登录密码')?>：</dt>
                    <dd class="relative">
                        <input type="password" class="text w200" name="chain_pwd" id="chain_pwd" autocomplete="off"   value="">
                        <span class="new-eye-icon"></span>
                        <p id="rule" class="hint"><?=__('密码请使用6--20个字符（区分大小写），由字母(必填)、数字(必填)、下划线(可选)组成。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt>
                        <i class="required">*</i>
                        <?=__('确认密码')?>：</dt>
                    <dd class="relative">
                        <input type="password" class="text w200" name="confirm_pwd" id="confirm_pwd" value="">
                        <span class="new-eye-icon"></span>
                        <p class="hint"><?=__('请再次输入登录密码，确保前后输入一致。')?></p>
                    </dd>
                </dl>
                <h3><?=__('门店相关信息')?></h3>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('门店名称')?>：</dt>
                    <dd>
                        <input type="text" class="text w200" name="chain_name" id="chain_name" value="<?=@$data['chain_name']?>">
                        <p class="hint"><?=__('请认真填写您的门店名称，以确保用户（购买者）线下到店自提时查找。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('所在地区')?>：</dt>
                    <dd>
                        <input type="hidden" name="address_area" id="t" value="<?=@$data['chain_area']?>" />
                        <input type="hidden" name="province_id" id="id_1" value="<?=@$data['chain_province_id']?>" />
                        <input type="hidden" name="city_id" id="id_2" value="<?=@$data['chain_city_id']?>" />
                        <input type="hidden" name="area_id" id="id_3" value="<?=@$data['chain_county_id']?>" />

                        <?php if(@$data['chain_area']){ ?>
                            <div id="d_1"><?=@$data['chain_area'] ?>&nbsp;&nbsp;<a href="javascript:sd();"><?=__('编辑')?></a></div>
                        <?php } ?>

                        <div id="d_2"  class="<?php if(@$data['chain_area']) echo 'hidden';?>">
                            <select id="select_1" name="select_1" onChange="district(this);">
                                <option value="">--<?=__('请选择')?>--</option>
                                <?php foreach($district['items'] as $key=>$val){ ?>
                                    <option value="<?=$val['district_id']?>|1"><?=$val['district_name']?></option>
                                <?php } ?>
                            </select>
                            <select id="select_2" name="select_2" onChange="district(this);" class="hidden"></select>
                            <select id="select_3" name="select_3" onChange="district(this);" class="hidden"></select>
                        </div>
                        <p class="hint"><?=__('所在地区将直接影响购买者在选择线下自提时的地区筛选，因此请如实认真选择全部地区级。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('详细地址')?>：</dt>
                    <dd>
                        <input type="text" class="text w400" name="chain_address" id="chain_address" value="<?=@$data['chain_address']?>">
                        <span class="query"><?= __('查询') ?></span><span style="color: #fd3d53"><?= __('查询后地图上拾取坐标') ?></span>
                        <p class="hint"><?=__('请认真填写详细地址，以确保用户（购物者）线下到店自提时能最准确的到达您的门店。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('地图显示')?>：</dt>
                    <dd>
                        <div id="editallmap" style="height:600px;border:1px solid gray"></div>
                        <div id="edit-r-result">
                            <i class="required" style="color: red;">* </i>经度: <input id="longitude"  name='longitude' type="text"  class="text w400" value="<?=@$data['longitude']?>" />
                            <i class="required" style="color: red;">* </i>纬度: <input id="latitude" name='latitude' type="text" class="text w400"  value="<?=@$data['latitude']?>"/>
                        </div>
                    </dd>
                </dl>
                <dl class="dl" style="overflow: inherit;">
                    <dt><i class="required">*</i><?=__('联系电话')?>：</dt>
                    <dd>
                        <input type="text" class="text w200" name="chain_phone" id="re_user_mobile" value="<?=@str_replace(' ','',$data['chain_mobile'])?>">
                        <input type="hidden"  name="area_code" id="area_code" value="<?=@$data['area_code']?>">
                        <p class="hint"><?=__('请认真填写门店联系电话，方便用户（购物者）通过该电话与您直接取得联系。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('营业时间')?>：</dt>
                    <dd>
                        <textarea class="textarea w400" maxlength="50" rows="2" name="chain_opening_hours" id="chain_opening_hours"><?=@$data['chain_opening_hours']?></textarea>
                        <p class="hint"><?=__('如实填写您的线下门店营业时间，以免用户（购物者）在营业时间外到店产生误会。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt><i class="required"></i><?=__('交通线路')?>：</dt>
                    <dd>
                        <textarea class="textarea w400" maxlength="50" rows="2" name="chain_traffic_line" id="chain_traffic_line"><?=@$data['chain_traffic_line']?></textarea>
                        <p class="hint"><?=__('如您的门店周围有公交、地铁线路到达，请填写该选项，多条线路请以“、”进行分隔。')?></p>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt><i class="required">*</i><?=__('实拍照片')?>：</dt>
                    <dd>
                        <div class="image">
                            <img id="chainImage" height="160px" width="160px" src="<?=@$data['chain_img']?>" />
                            <input id="chainimagePath" name="chainimagePath" type="hidden" value="<?=@$data['chain_img']?>"  />
                        </div>
                        <div id="uploadButton" style="width: 81px;height: 28px;float: left;margin-top:5px;">
                            <i class="iconfont icon-tupianshangchuan"></i><?=__('图片上传')?></div>
                        <div><p class="hint"><?=__('将您的实体店面沿街图上传，方便用户（购物者）线下到店自提时能最准确直观的找到您的门店。')?></p></div>
                    </dd>
                </dl>
                <dl class="dl">
                    <dt></dt>
                    <dd><input type="submit" class="button bbc_seller_submit_btns" value="<?=__('确认提交')?>" /></dd>
                </dl>
            </div>
        </form>
            <script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=5At3anZe83x8oOpFap42Gt8eHYpy3wm9"></script>
        <script>
            // 百度地图API功能
              var editmap = new BMap.Map("editallmap");
              var longitude = $("input[name='longitude']").val();
              var latitude = $("input[name='latitude']").val();

              if (longitude == 0 || latitude == 0) {
                    longitude = 116.331398;
                    latitude = 39.897445;
              }
              var point = new BMap.Point(longitude,latitude);
                editmap.centerAndZoom(point,11);  
                editmap.enableScrollWheelZoom();   //启用滚轮放大缩小，默认禁用
                editmap.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用      
              //单击获取点击的经纬度
              editmap.addEventListener("click",function(e){
                $("input[name='longitude']").val(e.point.lng);
                $("input[name='latitude']").val(e.point.lat);
              });

              editmap.addControl(new BMap.NavigationControl());
              var local = new BMap.LocalSearch(editmap, {
                  renderOptions: {map: editmap}
               });
              $(".query").click(function(){
                  var address = $("input[name='address_area']").val() + $("input[name='chain_address']").val();
                  if (address != "") {
                      local.search(address);
                  }
              });

            $("#re_user_mobile").intlTelInput({
//                utilsScript: "<?//= $this->view->js ?>///utils.js"
            });
            /*检测密码*/
            var flag = true;
            var reg_number = <?=$reg_row['reg_number']['config_value'] ? $reg_row['reg_number']['config_value'] : 0 ?>;
            var reg_lowercase = <?=$reg_row['reg_lowercase']['config_value'] ? $reg_row['reg_lowercase']['config_value'] : 0 ?>;
            var reg_uppercase = <?=$reg_row['reg_uppercase']['config_value'] ? $reg_row['reg_uppercase']['config_value'] : 0 ?>;
            var reg_symbols = <?=$reg_row['reg_symbols']['config_value'] ? $reg_row['reg_symbols']['config_value'] : 0 ?>;
            var reg_pwdlength = <?=$reg_row['reg_pwdlength']['config_value'] ? $reg_row['reg_pwdlength']['config_value'] : 0 ?>;
            function pwdCallback() {
                var user_pwd = $("#chain_pwd").val();
                if (user_pwd) {
                    var reg = '';
                    reg_number > 0 && (reg += '0-9');
                    reg_lowercase > 0 && (reg += 'a-zA-Z');
                    reg_uppercase > 0 && (reg += 'A-Za-z0-9');
                    reg_symbols > 0 && (reg += '');
                    if (reg !== '') {
                        reg = new RegExp('^[' + reg + ']+$');
                        !reg.test(user_pwd) && (flag = false);

                    }

                    //纯数字组合
                    if (reg_number > 0) {
                        if (/^[0-9]*$/.test(user_pwd)) {
                            flag = flag && true;
                        } else {
                            flag = flag && false;
                        }
                    }

                    //纯英文字母组合
                    if (reg_lowercase > 0) {
                        if (/^[A-Za-z]+$/.test(user_pwd)) {
                            flag = flag && true;
                        } else {
                            flag = flag && false;
                        }
                    }

                    //数字英文字母组合
                    if (reg_uppercase > 0) {
                        if (/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]{0,20})$/.test(user_pwd)) {
                            flag = flag && true;
                        } else {
                            flag = flag && false;
                        }
                    }

                    //数字英文字母及符号组合
                    if (reg_symbols > 0) {
                        if (/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[`~!@#$%^&*()_\-+=<>?:"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、])[\da-zA-Z`~!@#$%^&*()_\-+=<>?:"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、]{0,20}$/.test(user_pwd)) {
                            flag = flag && true;
                        } else {
                            flag = flag && false;
                        }
                    }

                    if (reg_pwdlength > 0) {
                        if (user_pwd.length >= <?=$reg_row['reg_pwdlength']['config_value']?>) {
                            flag = flag && true;
                        } else {
                            flag = flag && false;
                        }
                    }
                }
                if(flag == false){
                    Public.tips.error($('#rule').html());
                }
            }
            $(document).ready(function(){

                var ajax_url = './index.php?ctl=Seller_Shop_Chain&met=<?=$act?>Chain&typ=json';


                var reg_str = '';
//            reg_number > 0 && (reg_str += '0-9');
//            reg_lowercase > 0 && (reg_str += 'a-zA-Z');
//            reg_uppercase > 0 && (reg_str += 'A-Za-z0-9');
//            reg_symbols > 0 && (reg_str += '');

                //纯数字组合
                if (reg_number > 0) {
                    reg_str = '/^[0-9]*$/';
                    $('#rule').html('密码由纯数字组成');
                }

                //纯英文字母组合
                if (reg_lowercase > 0) {
                    reg_str = '/^[A-Za-z]+$/';
                    $('#rule').html('密码由纯英文字母组合,包含大小写');
                }

                //数字英文字母组合
                if (reg_uppercase > 0) {
                    reg_str = '/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]{0,20})$/';
                    $('#rule').html('密码由数字英文字母组合');
                }

                //数字英文字母及符号组合
                if (reg_symbols > 0) {
                    reg_str = '/^(?=.*\\d)(?=.*[a-zA-Z])(?=.*[`~!@#$%^&*()_\\-+=<>?:"{}|,.\\/;\'\\\\[\\]·~！@#￥%……&*（）——\\-+={}|《》？：“”【】、；‘’，。、])[\\da-zA-Z`~!@#$%^&*()_\\-+=<>?:"{}|,.\\/;\'\\\\[\\]·~！@#￥%……&*（）——\\-+={}|《》？：“”【】、；‘’，。、]{0,20}$/';
                    $('#rule').html('密码由数字英文字母及符号组合');
                }

                var msg_tips =  $('#rule').html();
                $('#form').validator({
                    ignore: ':hidden',
                    theme: 'yellow_right',
                    timely: 1,
                    stopOnError: false,
                    rules: {
                        tel:function(){
                            var area_code = $('#area_code').val();
                            var chain_phone = $('#re_user_mobile').val();
                            chain_phone = chain_phone.replace(/\s+/g,"");
                            var reg = /^1[3-9]\d{9}$/;
                            if(area_code==86 && !reg.test(chain_phone)){
                                return '<?=__('请输入正确的手机号码')?>';
                            }
                        },
                    },
                    fields: {
                        "chain_user": {
                            rule: "required;length[3~]",
                            msg: {
                                required : "<?=__('请填写门店登录名')?>",
                                length: "<?=__('请填写正确的门店登录名')?>"
                            }
                        },
                        'select_1':'required',
                        'select_2':'required',
                        //'select_3':'required',
                        'chain_name':'required',
                        'chain_address':'required',
                        'chain_phone':'required;tel',
                        'chain_opening_hours':'required',
                        'chain_img':'required',
                        'longitude':'required',
                        'latitude':'required',
                        "chain_pwd": {
                            rule: "length[6~20];password",
                            msg: {
                                length : "<?=__('请填写正确密码')?>"
                            }
                        },
                        "confirm_pwd": {
                            rule: "match(chain_pwd)",
                            msg: {
                                match : "<?=__('与登录密码不同，请重新填写')?>"
                            }
                        },
                        'chain_name': {
                            rule: 'required;length[~25]',
                            length: "<?=__('门店名称不能超过25个字符')?>"
                        }
                    },
                    valid:function(form){
                        var me = this;
                        // 提交表单之前，hold住表单，防止重复提交
                        me.holdSubmit();
                        //表单验证通过，提交表单
                        if (flag == true){
                            $.ajax({
                                url: ajax_url,
                                data:$("#form").serialize(),
                                success:function(a){
                                    if(a.status == 200)
                                    {
                                        Public.tips.success("<?=__('操作成功！')?>");
                                        setTimeout(' location.href="./index.php?ctl=Seller_Shop_Chain&met=chain&typ=e"',3000); //成功后跳转
                                    }
                                    else
                                    {
                                        Public.tips.error("<?=__('操作失败！')?>");
                                    }
                                }
                            });
                        }else{
                            Public.tips.error($('#rule').html());
                        }

                    }

                });
            });
        </script>
    <?php }?>
    <script type="text/javascript" src="<?=$this->view->js?>/district.js"></script>

    <script>
        //图片上传
        $(function(){

            var uploadImage = new UploadImage({

                thumbnailWidth: 160,
                thumbnailHeight: 160,
                imageContainer: '#chainImage',
                uploadButton: '#uploadButton',
                inputHidden: '#chainimagePath',
                callback: function () {
                    $('#chainimagePath').isValid();
                }
            });
            
            $(".new-eye-icon").click(function () {
                if($(this). is('.active'))
                {
                    var id = $(this).prevAll("input").attr('id');
                    document.getElementById(id).type="password";
                    $(this).removeClass('active');

                }else {
                    var id = $(this).prevAll("input").attr('id');
                    document.getElementById(id).type="text";
                    $(this).addClass('active');
                }
            });


        })
    </script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>