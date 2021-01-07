<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
<style type="text/css">
    .w150{
        width: 150px!important;
    }
    .hidden{
        display: none;
    }
</style>
    <div class="pc_user_about">
        <div class="recharge-content-top content-public clearfix">
            <ul class="tab">
                <li class="active"><a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=btinfo"><?=__('白条概览')?></a></li>
                <li><a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=btbill"><?=__('白条账单')?></a></li>
                <li><a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=btrefund"><?=__('白条还款')?></a></li>
            </ul>
        </div>
      <div class="wrap">
        <form id="form_company_info">
        <div class="qualification-filling-box">
            <div class="qualification-filling">
                <h4>公司及联系人信息</h4>
                <dl>
                    <dt><b>*</b>公司名称： </dt>
                    <dd><input type="text" name="shop_company_name" value="<?=$user_info['shop_company_name']?>"></dd>
                </dl>
                <dl>
                    <dt><b>*</b><?= __('所在地区：') ?></dt>
                    <dd><span class="w400">
                            <input type="hidden" name="address_area" id="t" value="<?= @$data['user_area'] ?>"/>
                            <input type="hidden" name="province_id" id="id_1" value="<?= @$data['user_provinceid'] ?>"/>
                            <input type="hidden" name="city_id" id="id_2" value="<?= @$data['user_cityid'] ?>"/>
                            <input type="hidden" name="area_id" id="id_3" value="<?= @$data['user_areaid'] ?>"/>
                            <?php if (@$data['user_area']) { ?>
                                <div id="d_1"><?= @$data['user_area'] ?>
                                    <a href="javascript:sd();"><?= __('编辑') ?></a>
                                </div>
                            <?php } ?>
                            <div id="d_2" class="<?php if (@$data['user_area']) {
                                echo 'hidden';
                            } ?>">
                                <select id="select_1" name="company_1" onChange="district(this);" class="w150">
                                    <option value="">--<?= __('请选择') ?>--</option>
                                    <?php foreach ($district['items'] as $key => $val) { ?>
                                        <option value="<?= $val['district_id'] ?>|1"><?= __($val['district_name']); ?></option>
                                    <?php } ?>
                                </select>
                                <select id="select_2" name="company_2" onChange="district(this);" class="w150" style="display: none"></select>
                                <select id="select_3" name="company_3" onChange="district(this);" class="w150" style="display: none"></select>
                            </div></span>
                    </dd>
                </dl>
                <dl>
                    <dt><b>*</b>公司详细地址： </dt>
                    <dd><input type="text" name="company_address_detail" value="<?=$user_info['company_address_detail']?>"></dd>
                </dl>
                <dl>
                    <dt><b>*</b>公司电话： </dt>
                    <dd><input type="text" name="company_phone" value="<?=$user_info['company_phone']?>"></dd>
                </dl>
                <dl>
                    <dt><b>*</b>联系人姓名： </dt>
                    <dd><input type="text" name="contacts_name" value="<?=$user_info['contacts_name']?>"></dd>
                </dl>
                <dl>
                    <dt><b>*</b>联系人号码： </dt>
                    <dd><input type="text" name="contacts_phone" value="<?=$user_info['contacts_phone']?>"></dd>
                </dl>
            </div>
            <div class="qualification-filling">
                <h4>营业执照信息（副本）</h4>
                <dl>
                    <dt><b>*</b>是否多证合一： </dt>
                    <dd>
                        <label class="label-radio"><input type="radio"  name="threeinone" value="1" checked onclick="is_threeinone(1)">是</label>
                        <label class="label-radio"><input type="radio" name="threeinone" value="0" onclick="is_threeinone(0)">否</label>
                    </dd>
                </dl>
                <dl>
                    <dt><b>*</b>营业执照号： </dt>
                    <dd class="password-num-dd">
                        <input type="text" name="business_id" value="<?=$user_info['business_id']?>">
                        <span class="input-tips-text">多证合一的请输入"统一社会信用代码"</span>
                    </dd>
                </dl>
                <dl>
                    <dt><b>*</b>营业执照所在地： </dt>
                        <dd class="password-num-dd">
                        <input type="text" name="business_license_location" value="<?=$user_info['business_license_location']?>">
                        <span class="input-tips-text">营业执照所在地必填</span>
                    </dd>
                </dl>
                <dl>
                    <dt><b>*</b>营业执照有效期： </dt>
                    <dd>
                        <div class="time-box">
                           <div class="iblock password-time-module"><input readonly="readonly" id="start_time"  name="business_licence_start" class="input-time" type="text" value="<?=$user_info['business_licence_start']?>"><label class="label-time"><i class="iconfont icon-shangcidenglushijian"></i></label></div>&nbsp;<em>-</em>&nbsp;<div class="iblock password-time-module"><input readonly="readonly" id="end_time" name="business_licence_end" class="input-time" type="text" value="<?=$user_info['business_licence_end']?>"><label class="label-time"><i class="iconfont icon-shangcidenglushijian"></i></label></div> 
                        </div>
                        
                        <span class="input-tips-text">结束时间不填代表永久。</span>
                    </dd>
                </dl>
                <dl>
                    <dt><b>*</b>营业执照电子版： </dt>
                    <dd class="pay-password-dd">
                        <div>
                            <input class="up-image-input" type="text" id="business_logo" readonly="readonly"  name="business_license_electronic" value="<?=$user_info['business_license_electronic']?>"><div class="iblock btn-up-image" id="business_upload">上传证件图片</div>
                        </div>
                        <p class="input-tips-text">请确保图片清晰，文字可辨并有清晰的红色公章。</p>
                    </dd>
                </dl>
            </div>
            <div class="qualification-filling hidden addition">
                <h4>组织机构代码证</h4>
                <dl>
                    <dt><b>*</b>组织机构代码： </dt>
                    <dd>
                        <input type="text" name="organization_code" value="<?=$user_info['organization_code']?>">
                    </dd>
                </dl>
                <dl>
                    <dt><b>*</b>组织机构代码证电子版： </dt>
                     <dd class="pay-password-dd">
                        <div>
                            <input class="up-image-input" type="text" id="organization_logo" readonly="readonly" name="organization_code_electronic" value="<?=$user_info['organization_code_electronic']?>"><div class="iblock btn-up-image" id="organization_upload">上传证件图片</div>   
                        </div>
                        <p class="input-tips-text">请确保图片清晰，文字可辨并有清晰的红色公章。</p>
                    </dd>
                </dl>
            </div>
            <div class="qualification-filling hidden addition">
                <h4>税务登记证</h4>
                <dl>
                    <dt><b>*</b>纳税人识别号： </dt>
                    <dd>
                        <input type="text" name="taxpayer_id" value="<?=$user_info['taxpayer_id']?>">
                    </dd>
                </dl>
                <dl>
                    <dt><b>*</b>税务登记证号： </dt>
                    <dd>
                        <input type="text" name="tax_registration_certificate" value="<?=$user_info['tax_registration_certificate']?>">
                    </dd>
                </dl>
                <dl>
                    <dt><b>*</b>税务登记证号电子版： </dt>
                     <dd class="pay-password-dd">
                        <div>
                            <input class="up-image-input" type="text" id="tax_registration_certificate_electronic" name="tax_registration_certificate_electronic" readonly="readonly" value="<?=$user_info['tax_registration_certificate_electronic']?>"><div class="iblock btn-up-image" id="tax_registration_certificate_upload" >请上传证件图片</div>   
                        </div>
                        <p class="input-tips-text">请确保图片清晰，文字可辨并有清晰的红色公章。</p>
                    </dd>
                </dl>
                
            </div>
          </div>
          </form>
              <div class="pc_trans_btn"><a href="javascript:void(0)" id="btn_apply_company_next" class="btn_big btn_active">确定提交</a></div>
        </div>
    </div>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
<link href="<?= $this->view->css_com ?>/jquery/plugins/datepicker/dateTimePicker.css?ver=<?= VER ?>" rel="stylesheet"
      type="text/css">
<script type="text/javascript" src="<?=$this->view->js?>/district.js"></script>
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.datetimepicker.js"></script>
 <script type="text/javascript">
        $(document).ready(function(){
            $('#start_time').datetimepicker({
                controlType: 'select',
                format:"Y-m-d",
                timepicker:false,

            });

            $('#end_time').datetimepicker({
                controlType: 'select',
                format:"Y-m-d",
                timepicker:false,
                onShow:function( ct ){
                    this.setOptions({
                        minDate:($('#start_time').val())
                    })
                }
            });

            var ajax_url = './index.php?ctl=Info&met=btactivation&typ=json';
            $('#form_company_info').validator({
                ignore: ':hidden',
                theme: 'yellow_right',
                timely: 1,
                stopOnError: false,
                rules: {
                    tel:function(){
                        var area_code = $('#area_code').val();
                        var contacts_phone = $('#re_user_mobile').val();
                        var reg = /^1[3-9]\d{9}$/;
                        if(area_code==86 && !reg.test(contacts_phone)){
                            return '<?=__('请输入正确的手机号码')?>';
                        }
                    },
                    zjtel:[/(^0\d{2,3}[-]?\d{5,9}$)|(^[1][0-9]{10}$)/,'<?=__('请输入正确的电话号码')?>'],
                    daima:[/^[a-zA-Z0-9]{8}-[a-zA-Z0-9]$/,'<?=__('请输入正确的组织机构代码')?>'],
                    times:function(element, params){
                        var start_time = $('#start_time').val();
                        var end_time = $('#end_time').val();
                        if(start_time>end_time && end_time){
                            return '<?=__('不能小于起始时间')?>';
                        }
                    }
                },
                fields: {
                    'shop_company_name': 'required;',
                    'shop_company_address':'required;length[1~40]',
                    'company_address_detail':'required;',
                    'company_phone':'required;zjtel',
                    'contacts_name':'required;',
                    'contacts_phone':'required;zjtel',
                    'business_id':'required;',
                    'business_license_location':'required;',
                    'business_licence_start':'required;',
                    'business_licence_end':'times;',
                    'business_license_electronic':'required;',
                    'organization_code':'required;daima',
                    'organization_code_electronic':'required;',
                    'taxpayer_id':'required;',
                    'tax_registration_certificate':'required;',
                    'tax_registration_certificate_electronic':'required;',
                    'yingye_1':'required;',
                    'yingye_2':'required;',
                    'yingye_3':'required;',
                    'company_1':'required;',
                    'company_2':'required;',
                    'company_3':'required;',
                },
                valid:function(form){
                    //表单验证通过，提交表单
                    $.ajax({
                        url: ajax_url,
                        data:$("#form_company_info").serialize(),
                        success:function(a){
                            if(a.status == 200)
                            {
                                location.href=a.data.url;
                            }
                            else
                            {
                                if(typeof(a.msg) == 'undefined' || !a.msg){
                                    Public.tips.error("<?=__('操作失败')?>");
                                }else{
                                    Public.tips.error(a.msg);
                                }
                                return false;
                            }
                        }
                    });
                }

            });
        })
        $('#btn_apply_company_next').click(function() {
            $("#form_company_info").submit();
        });
        function is_threeinone($value){
            if($value == 1){
                $("input[name='organization_code']").val('');
                $("input[name='organization_code_electronic']").val('');
                $("input[name='taxpayer_id']").val('');
                $("input[name='tax_registration_certificate']").val('');
                $("input[name='tax_registration_certificate_electronic']").val('');

                $('.addition').hide();
            }else{
                $('.addition').show();
            }
        }
    </script>
    <script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js_com?>/upload/upload_image.js" charset="utf-8"></script>
    <link href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css?ver=<?= VER ?>" rel="stylesheet"
          type="text/css">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js"
            charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js"
            charset="utf-8"></script>
    <script>
        //图片上传
        $(function(){

            business_uploads = new UploadImage({
                uploadButton: '#business_upload',
                inputHidden: '#business_logo'
            });

            organization_upload = new UploadImage({
                uploadButton: '#organization_upload',
                inputHidden: '#organization_logo'
            });

            tax_registration_certificate_upload = new UploadImage({
                uploadButton: '#tax_registration_certificate_upload',
                inputHidden: '#tax_registration_certificate_electronic'
            });

        })
    </script>