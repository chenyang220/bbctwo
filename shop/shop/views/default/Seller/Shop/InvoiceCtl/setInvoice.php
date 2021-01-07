<html>
<head>
    <meta charset="UTF-8">
    <link type="text/css" rel="stylesheet" href="<?= $this->view->css ?>/shop-cart.css">
    <link type="text/css" rel="stylesheet" href="<?= $this->view->css ?>/base.css">
    <link type="text/css" rel="stylesheet" href="<?= $this->view->css ?>/headfoot.css">
    <script type="text/javascript" src="<?=$this->view->js_com?>/jquery.js"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/shop_order_invoice.js"></script>
    <script  type="text/javascript" src="<?=$this->view->js?>/piao.js"></script>
    <!--    <script  type="text/javascript" src="--><?//=$this->view->js_com?><!--/plugins/jquery.dialog.js"></script>-->
    <!--    <link type="text/css" rel="stylesheet" href="--><?//= $this->view->css_com ?><!--/jquery/plugins/dialog/green.css">-->
    <link href="<?= $this->view->css ?>/tips.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $this->view->css ?>/intlTelInput.css">
    <script type="text/javascript" src="<?= $this->view->js_com?>/plugins/jquery.cookie.js"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/common.js"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/district.js"></script>
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.toastr.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js ?>/intlTelInput.js"></script>
</head>
<body marginwidth="0" marginheight="0" style="font-size:12px;">
<input type="hidden" id="hasBook" value="false">
<input type="hidden" id="hasCommon" value="true">
<input type="hidden" id="invokeInvoiceBasicService" value="true">
<div class="invoice-thickbox" id="invoice-tab">
    <style>
        html, body {
            height: auto;
        }

        .tab-nav .label {
            width: 120px;
            line-height: 29px;
            text-align: right;
        }

        .itext-box {
            width: 185px;
            height: 30px !important;
        }

        .n-radio-box {
            margin-top: 3px;
        }

        .n-radio-box input {
            vertical-align: middle;
            margin: 0 5px;

        }

        .btn-9 {
            padding: 3px 30px;
            box-sizing: inherit;
        }

        .btn_save {
            background: #e02222;
            color: #fff !important;
        }

        .btn_save:hover {
            background: #e45050;
        }

    </style>
    <div class="form mt20" id="invoice_head">
        <div class="item"> <span class="label"><em><?=__('*')?></em><?=__('模板名称：')?></span>
            <div class="fl">
                <input type="text" class="itext-box" id="generalInvoice_invoice_name" name="invoiceName" value="<?php echo isset($data['invoice_name']) ?  $data['invoice_name'] : '';?>" onblur="check_InvoiceName('invoice_name')" maxlength="30" onfocus="if(value == defaultValue){value='';}">
                <span class="message" id="generalInvoice_invoice_name_error"></span> </div>
        </div>

        <div class="item"> <span class="label"><em><?=__('*')?></em><?=__('模板状态：')?></span>
            <div class="fl n-radio-box">
                <input type="radio" name="is_use" value="2" <?php if ($data['is_use'] == 2){echo "checked";}else{ echo "";}?> >开启
                <input class="ml20" type="radio" name="is_use" value="1" <?php if ($data['is_use'] == 1 || empty($data['is_use'])){echo "checked";}?> >关闭
            </div>
        </div>
        <div class="tab-nav clearfix">
            <span class="label fl "><em><?=__('*')?></em><?=__('发票类型：')?></span>
            <ul class="fl">
                <li id="click_1" class="tab-nav-item tab-item-selected" value="1" clstag="pageclick|keycount|trade_201602181|17"><p><?=__('普通发票')?></p><b></b></li>
                <li id="click_2" class="tab-nav-item" value="2" clstag="pageclick|keycount|trade_201602181|19"><p><?=__('电子发票')?></p><b></b></li>
                <li id="click_3" class="tab-nav-item" value="3" clstag="pageclick|keycount|trade_201602181|18"><p><?=__('增值税专用发票')?></p><b></b></li>
            </ul>
        </div>
        <!-- 普通发票 -->
        <div id="noraml" class="tab-con form">
            <div class="item"> <span class="label"><?=__('发票抬头：')?></span>
                <div class="fl">
                    <div class="invoice-list invoice-tit-list" id="invoice-tit-list">
                        <div class="js-person invoice-item <?php if ($data['invoice_title'] == "个人" || empty($data['invoice_title'])){echo "invoice-item-selected";}?>" style="cursor:pointer" onclick="selected(this);hideCompanyTaxNum('#noraml');" >
                            <div id="invoice-1" style="cursor:pointer"> <span class="hide">
              <input type="hidden" value="4">
              </span> <span class="fore2" id="invoice-r1-58325" name="usualInvoiceList" value="58325">
              <input type="text" class="itxt" title_type="1" data-r="<?=__('个人')?>" value="<?=__('个人')?>" disabled="disabled" >
              <b></b></span> </div>
                        </div>

                        <div class="invoice-item js-company <?php if ($data['invoice_title'] != "个人" && $data['invoice_title']){echo "invoice-item-selected";}?>" style="cursor:pointer" onclick="selected(this);showCompanyTaxNum('#noraml');" onmouseover="show_op(this)" onmouseout="hide_op(this)">
                            <div id="invoice-2"> <span class="hide">
                  <input type="hidden" value="4">
                  </span> <span class="fore2">
                      <input type="text" style="cursor:pointer" class="itxt" title_type="2" maxlength="50" value="<?php if ($data['invoice_title'] != "个人" && $data['invoice_title']){echo $data['invoice_title'];}?>"  placeholder="新增企业发票抬头">
                  <input type="hidden" name="invoice_id" id="invoice_id" value="<?php echo isset($data['invoice_id']) ?  $data['invoice_id'] : '';?>">

                  <b></b></span>
                            </div>
                        </div>
                    </div>
                    <!--新增单位发票-->
                </div>
            </div>

            <div class="tab-box js-company-tax-num" style="display: <?php if ($data['invoice_title'] != "个人" && $data['invoice_title']){echo "block";}else{echo "none";}?>">
                <div class="tab-con" style="display: block;">
                    <div class="item">
                        <span class="label">纳税人识别号：</span>
                        <input type="text" class="js-i-company-tax-num itxt" placeholder="请输入纳税人识别号" value="<?php echo isset($data['invoice_code']) ?  $data['invoice_code'] : '';?>" style="margin-top: 5px; width: 334px;height:32px;">
                    </div>
                </div>
            </div>
            <div class="tab-box" >
                <div class="tab-con" id="fapiao">
                    <div class="form">
                        <!--                        <div class="item"> <span class="label">--><?//=__('发票内容：')?><!--</span>-->
                        <!--                            <div class="fl">-->
                        <!--                                <div class="invoice-list">-->
                        <!--                                    <ul id="electro_book_content_radio" class="content_radio">-->
                        <!--                                        <li class="invoice-item invoice-item-selected" id="electro-invoice-content-1" name="normal-normalContent" value="--><?//=__('明细')?><!--" style="cursor:pointer"> <p>--><?//=__('明细')?><!--</p><b></b> </li>-->
                        <!--                                        <li class="invoice-item" id="electro-invoice-content-22" name="normal-normalContent" value="--><?//=__('办公用品')?><!--" style="cursor:pointer"> <p>--><?//=__('办公用品')?><!--</p><b></b> </li>-->
                        <!--                                        <li class="invoice-item" id="electro-invoice-content-3" name="normal-normalContent" value="--><?//=__('电脑配件')?><!--" style="cursor:pointer"> <p>--><?//=__('电脑配件')?><!--</p><b></b> </li>-->
                        <!--                                        <li class="invoice-item" id="electro-invoice-content-19" name="normal-normalContent" value="--><?//=__('耗材')?><!--" style="cursor:pointer"> <p>--><?//=__('耗材')?><!--</p><b></b> </li>-->
                        <!--                                    </ul>-->
                        <!--                                </div>-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                        <div class="item">
                            <div class="fl">
                                <div class="invoice-list">
                                    <ul>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="item"> <span class="label">&nbsp;</span>
                            <div class="fl" >
                                <div class="op-btns"> <a  class="btn-9 btn_save" onclick="save_Invoice(this)"><?=__('保存')?></a> <a href="#none" class="btn-9 ml10" onclick="quxiao()"><?=__('取消')?></a> </div>
                                <div class="ftx-03 mt10"> <?=__('温馨提示：发票的开票金额不包括代金券、积分支付部分')?><br>
                                    <!--<a target="_blank" class="ftx-05">发票信息相关问题&gt;&gt;</a>--> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 电子发票-->
        <div id="Electronics" class="tab-con ui-switchable-panel-selected" style="display:none">
            <form id="eform">

                <div class="prompt-box"><?=__('电子发票是税局认可的有效凭证，其法律效力、基本用途及使用规定同纸质发票，如需纸质票可自行下载打印。如您本次购买的商品暂未实现电子发票开具，我们将自动更换为普通发票（纸质）同商品一并送达，请您注意查收。')?><br>
                    <!--<a target="_blank" class="ftx-05" href="http://help.jd.com/user/issue/list-182-184.html">什么是电子发票</a>-->
                </div>
                <input type="hidden" id="invoice_ceshi1" name="invoice_ceshi1" value="">
                <div class="form">
                    <div class="item"> <span class="label"><?=__('发票抬头：')?></span>
                        <div class="fl">
                            <div class="invoice-list invoice-tit-list" id="invoice-tit-list">
                                <div class="invoice-item <?php if ($data['invoice_title'] == "个人" || empty($data['invoice_title'])){echo "invoice-item-selected";}?>" style="cursor:pointer" onclick="selected(this);hideCompanyTaxNum('#Electronics');">
                                    <div id="invoice-2" style="cursor:pointer"> <span class="hide">
                  <input type="hidden" value="4">
                  </span> <span class="fore2">
                  <input type="text" style="cursor:pointer" class="itxt"  title_type="1" value="<?=__('个人')?>" disabled="disabled">
                  <b></b></span> </div>
                                </div>
                                <div class="invoice-item <?php if ($data['invoice_title'] != "个人" && $data['invoice_title']){echo "invoice-item-selected";}?>" onclick="selected(this);showCompanyTaxNum('#Electronics');" style="cursor:pointer">
                                    <div id="invoice-2" style="cursor:pointer"> <span class="hide">
                  <input type="hidden" value="5">
                  </span> <span class="fore2 selec" id="electroCompanyRemark" name="electroCompanyName">
                  <input type="text" class="itxt itxt04" title_type="2" maxlength="50" placeholder="<?=__('请填写公司发票抬头')?>" value="<?php if ($data['invoice_title'] != "个人" && $data['invoice_title']){echo $data['invoice_title'];}?>">
                  <b></b></span> </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-box js-company-tax-num" style="display: <?php if ($data['invoice_title'] != "个人" && $data['invoice_title']){echo "block";}else{echo "none";}?>">
                        <div class="tab-con" style="display: block;">
                            <div class="item">
                                <span class="label">纳税人识别号：</span>
                                <input type="text" class="js-i-company-tax-num itxt" placeholder="请输入纳税人识别号" value="<?php if(isset($data['invoice_code'])){ echo $data['invoice_code']; }?>" style="margin-top: 5px; width: 334px;height:32px;">
                            </div>
                        </div>
                    </div>

                    <!--                    <div class="item"> <span class="label">--><?//=__('发票内容：')?><!--</span>-->
                    <!--                        <div class="fl">-->
                    <!--                            <div class="invoice-list">-->
                    <!--                                <ul id="electro_book_content_radio_T" class="content_radio">-->
                    <!--                                    <li class="invoice-item invoice-item-selected" id="electro-invoice-content-2" name="electro-normalContent" value="--><?//=__('明细')?><!--" style="cursor:pointer"> <p>--><?//=__('明细')?><!--</p><b></b> </li>-->
                    <!--                                    <li class="invoice-item" id="electro-invoice-content-4" name="electro-normalContent" value="--><?//=__('办公用品')?><!--" style="cursor:pointer"> <p>--><?//=__('办公用品')?><!--</p><b></b> </li>-->
                    <!--                                    <li class="invoice-item" id="electro-invoice-content-6" name="electro-normalContent" value="--><?//=__('电脑配件')?><!--" style="cursor:pointer"> <p>--><?//=__('电脑配件')?><!--</p><b></b> </li>-->
                    <!--                                    <li class="invoice-item" id="electro-invoice-content-5" name="electro-normalContent" value="--><?//=__('耗材')?><!--" style="cursor:pointer"> <p>--><?//=__('耗材')?><!--</p><b></b> </li>-->
                    <!--                                </ul>-->
                    <!--                            </div>-->
                    <!--                        </div>-->
                    <!--                    </div>-->
                    <div class="item">
                        <div class="fl">
                            <div class="invoice-list">
                                <ul>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="item"> <span class="label"><em><?=__('*')?></em><?=__('收票人手机：')?></span>
                        <div class="fl">
                            <input type="text" class="itxt itxt03" id="re_user_mobile" name="invoice_rec_phone"  onblur="check_electroInvoicePhone()" maxlength="11" onfocus="if(value == defaultValue){value='';}" value="<?php if(isset($data['invoice_rec_phone'])){ echo $data['invoice_rec_phone']; }?>">
                            <input type="hidden" id="area_code" name="area_code" value="<?php if(isset($data['area_code'])){ echo $data['area_code']; }?>">
                            <span class="message" id="e_consignee_mobile_error"></span>
                        </div>
                    </div>
                    <div class="item"> <span class="label"><?=__('收票人邮箱：')?></span>
                        <div class="fl">
                            <input type="text" class="itxt itxt03" id="e_consignee_email" name="invoice_rec_email" value="<?php if(isset($data['invoice_rec_email'])){ echo $data['invoice_rec_email']; }?>" onblur="check_electroInvoiceEmail(this)" onfocus="if(value == defaultValue){value='';}">
                            <span class="message" id="e_consignee_email_error"></span>
                        </div>
                    </div>
                    <div class="item"> <span class="label">&nbsp;</span>
                        <div class="fl">
                            <div class="op-btns"> <a class="btn-9 btn_save" onclick="save_Invoice(this)"><?=__('保存')?></a> <a class="btn-9 ml10" onclick="quxiao()"><?=__('取消')?></a> </div>
                            <div class="ftx-03 mt10"> <?=__('温馨提示：发票的开票金额不包括代金券、积分支付部分')?><br>
                                <!--<a  target="_blank" class="ftx-05">发票信息相关问题&gt;&gt;</a>-->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <script>
                $("#re_user_mobile").intlTelInput({
//                    utilsScript: "<?//= $this->view->js ?>///utils.js"
                });
            </script>
        </div>
        <!--增值税 -->
        <div id="increment" class="tab-con"   style="display:none">
            <form id="iform">
                <div class="prompt-box"> <?=__('我公司依法开具发票，如您购买的商品按税法规定属于不得从增值税销项税额中抵扣的项目（例如集体福利或个人消费等），请您选择普通发票。')?><br>
                    <!--<span class="ftx-05"><a href="http://storage.jd.com/doc/%E6%8E%88%E6%9D%83%E5%A7%94%E6%89%98%E4%B9%A6.docx" target="_blank" class="ftx-05">委托书下载</a> | <a href="http://help.jd.com/user/issue/list-182-313.html" target="_blank" class="ftx-05">发票制度说明</a> | <a href="http://help.jd.com/user/issue/list-182-185.html" target="_blank" class="ftx-05">首次开具增值税发票阅读</a></span> -->
                </div>
                <div class="form" id="invoice-box-03">
                    <ul class="invoice-status">
                        <li class="fore1 curr"><?=__('1.填写公司信息')?><b></b></li>
                        <li class="fore2 "><?=__('2.填写收票人信息')?><b></b></li>
                    </ul>
                    <div class="steps">
                        <div class="step step1">
                            <input type="hidden" id="vatCanEdit" value="false">
                            <div class="item"> <span class="label" id="vat_companyName_div"><em><?=__('*')?></em><?=__('单位名称：')?></span>
                                <div class="fl">
                                    <input type="text" class="itxt itxt05 vat-step-1" maxlength="50" name="invoice_company" id="vat_companyName" value="<?php if(isset($data['invoice_company'])){ echo $data['invoice_company']; }?>" onblur="check_Invoice('vat_companyName', this.value)">
                                    <span class="message" id="vat_companyName_error"></span> </div>
                            </div>
                            <div class="item" id="vat_code_div"> <span class="label"><em><?=__('*')?></em><?=__('纳税人识别码：')?></span>
                                <div class="fl">
                                    <input type="text" class="itxt itxt05 vat-step-1" name="invoice_code" id="vat_code" value="<?php if(isset($data['invoice_code'])){ echo $data['invoice_code']; }?>" onblur="check_Invoice('vat_code', this.value)">
                                    <span class="message" id="vat_code_error"></span> </div>
                            </div>
                            <div class="item" id="vat_address_div"> <span class="label"><em><?=__('*')?></em><?=__('注册地址：')?></span>
                                <div class="fl">
                                    <input type="text" class="itxt itxt05 vat-step-1" name="invoice_reg_addr" id="vat_address" value="<?php if(isset($data['invoice_reg_addr'])){ echo $data['invoice_reg_addr']; }?>" onblur="check_Invoice('vat_address', this.value)">
                                    <span class="message" id="vat_address_error"></span> </div>
                            </div>
                            <div class="item" id="vat_phone_div"> <span class="label"><em><?=__('*')?></em><?=__('注册电话：')?></span>
                                <div class="fl">
                                    <input type="text" maxlength="11" class="itxt itxt05 vat-step-1" name="invoice_reg_phone" id="vat_phone" value="<?php if(isset($data['invoice_reg_phone'])){ echo $data['invoice_reg_phone']; }?>" onblur="check_Invoice('vat_phone', this.value)">
                                    <span class="message" id="vat_phone_error"></span> </div>
                            </div>
                            <div class="item" id="vat_bankName_div"> <span class="label"><em><?=__('*')?></em><?=__('开户银行：')?></span>
                                <div class="fl">
                                    <input type="text" class="itxt itxt05 vat-step-1" name="invoice_reg_bname" id="vat_bankName" value="<?php if(isset($data['invoice_reg_bname'])){ echo $data['invoice_reg_bname']; }?>" onblur="check_Invoice('vat_bankName', this.value)">
                                    <span class=" message" id="vat_bankName_error"></span> </div>
                            </div>
                            <div class="item" id="vat_bankAccount_div"> <span class="label"><em><?=__('*')?></em><?=__('银行账户：')?></span>
                                <div class="fl">
                                    <input type="text" class="itxt itxt05 vat-step-1" name="invoice_reg_baccount" id="vat_bankAccount" value="<?php if(isset($data['invoice_reg_baccount'])){ echo $data['invoice_reg_baccount']; }?>" onblur="check_Invoice('vat_bankAccount', this.value)">
                                    <span class="message" id="vat_bankAccount_error"></span> </div>
                            </div>
                            <div class="item"> <span class="label">&nbsp;</span>
                                <div class="fl">
                                    <div class="op-btns">
                                        <input id="vat-btn-save" type="button" class="btn-9 btn_save" onclick="nextAvt()" value="<?=__('下一步')?>">
                                        <input id="vat-btn-cancel" type="button" class="btn-9" onclick="quxiao()" value="<?=__('取消')?>">
                                    </div>
                                    <div class="ftx-03 mt10"> <?=__('温馨提示：发票的开票金额不包括代金券、积分支付部分')?><br>
                                        <!--<a href="http://help.jd.com/user/issue/list-182.html" target="_blank" class="ftx-05">发票信息相关问题&gt;&gt;</a>--> </div>
                                </div>
                            </div>
                        </div>
                        <div class="step step2 hide">
                            <!--                            <div class="item"> <span class="label">--><?//=__('发票内容：')?><!--</span>-->
                            <!--                                <div class="fl">-->
                            <!--                                    <div class="invoice-list">-->
                            <!--                                        <ul class="content_radio">-->
                            <!--                                            <li class="invoice-item invoice-item-selected" id="electro-invoice-content-2" name="electro-normalContent" value="--><?//=__('明细')?><!--" style="cursor:pointer"> <p>--><?//=__('明细')?><!--</p><b></b> </li>-->
                            <!--                                        </ul>-->
                            <!--                                    </div>-->
                            <!--                                </div>-->
                            <!--                            </div>-->
                            <input type="hidden" id="vatConsigneeInfo" value=",,0,0,0,0,">
                            <div class="item" id="name_div"> <span class="label"><em><?=__('*')?></em><?=__('收票人姓名：')?></span>
                                <div class="fl">
                                    <input type="text" class="itxt itxt05 vat-step-2" maxlength="10" id="consignee_name" name="invoiceParam.consigneeName" value="<?php if(isset($data['invoice_rec_name'])){ echo $data['invoice_rec_name']; }?>" maxlength="20" onblur="check_InvoiceConsignee('name_div')">
                                    <span class="message" id="name_div_error"></span> </div>
                            </div>
                            <div class="item" id="call_div"> <span class="label"><em><?=__('*')?></em><?=__('收票人手机：')?></span>
                                <div class="fl">
                                    <input type="text" class="itxt itxt05 vat-step-2" id="consignee_mobile" name="invoiceParam.consigneePhone" value="<?php if(isset($data['invoice_rec_phone'])){ echo $data['invoice_rec_phone']; }?>" onblur="check_InvoiceConsignee('call_phone_div')" maxlength="20" onfocus="if(value == defaultValue){value='';}">
                                    <input type="hidden" id="area_code2" name="area_code" value="<?php if(isset($data['area_code'])){ echo $data['area_code']; }?>">

                                    <span class="message" id="call_div_error"></span> </div>
                            </div>
                            <div class="item vat-step-2" id="area_div"> <span class="label"><em><?=__('*')?></em><?=__('收票人省份：')?></span>
                                <div class="fl" id="span_area" style="margin-top: 5px;">

                                    <input type="hidden" name="address_area" id="t" value="<?php if(isset($data['invoice_rec_province'])){ echo $data['invoice_rec_province']; }?>" />
                                    <input type="hidden" name="province_id" id="id_1" value="<?php if(isset($data['invoice_province_id'])){ echo $data['invoice_province_id']; }?>" />
                                    <input type="hidden" name="city_id" id="id_2" value="<?php if(isset($data['invoice_city_id'])){ echo $data['invoice_city_id']; }?>" />
                                    <input type="hidden" name="area_id" id="id_3" value="<?php if(isset($data['invoice_area_id'])){ echo $data['invoice_area_id']; }?>" />

                                    <?php if(@$data['invoice_rec_province']){ ?>
                                        <div id="d_1"><span class="dress_box"><?=@$data['invoice_rec_province'] ?></span>&nbsp;&nbsp;<a href="javascript:sd();"><?=('编辑')?></a></div>
                                    <?php } ?>

                                    <div id="d_2"  class="<?php if(@$data['invoice_rec_province']) echo 'hidden';?>">
                                        <select id="select_1" name="select_1" onChange="district(this);">
                                            <option value=""><?=__('--请选择--')?></option>
                                            <?php foreach($district['items'] as $key=>$val){ ?>
                                                <option value="<?=$val['district_id']?>|1"><?=$val['district_name']?></option>
                                            <?php } ?>
                                        </select>
                                        <select id="select_2" name="select_2" onChange="district(this);" class="hidden"><option value=""><?=__('--请选择--')?></option></select>
                                        <select id="select_3" name="select_3" onChange="district(this);" class="hidden"><option value=""><?=__('--请选择--')?></option></select>
                                    </div>

                                </div>
                                <span id="area_div_error"></span> </div>
                            <div class="item" id="address_div"> <span class="label"><em><?=('*')?></em><?=__('详细地址：')?></span>
                                <div class="fl">
                                    <input type="text" class="itxt itxt05" id="consignee_address" name="invoiceParam.consigneeAddress" value="<?php if(isset($data['invoice_goto_addr'])){ echo $data['invoice_goto_addr']; }?>" maxlength="50" onblur="check_InvoiceConsignee('address_div')">
                                </div>
                                <span class="message" id="address_div_error"></span> </div>
                            <div class="item"> <span class="label">&nbsp;</span>
                                <div class="fl">
                                    <div class="op-btns"> <a class="btn-9 btn_save" onclick="save_Invoice(this)"><?=__('保存')?></a> <a class="btn-9 ml10" onclick="quxiao()"><?=__('取消')?></a> <a href="#none" class="ftx-05 ml10 prev" onclick="prev()"><?=__('返回上一步')?></a> </div>
                                    <div class="ftx-03 mt10"> <?=__('温馨提示：发票的开票金额不包括代金券、积分支付部分')?><br>
                                        <!--<a href="http://help.jd.com/user/issue/list-182.html" target="_blank" class="ftx-05">发票信息相关问题&gt;&gt;</a>--> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <script>
                $("#consignee_mobile").intlTelInput({
//                    utilsScript: "<?//= $this->view->js ?>///utils.js"
                });
            </script>
        </div>
    </div>
</div>
<script type="text/javascript">
    var SITE_URL = "<?php Yf_Registry::get('url')?>";

    $(function() {
        var s = "<?= $data['invoice_state']?>"
        $("#click_"+s).click();
        $("#click_"+s).parent().find("li").hide();
        $("#click_"+s).show();
    });
</script>
</body>
</html>