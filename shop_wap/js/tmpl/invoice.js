var key = getCookie('key');

Zepto(function() {
    //地址选择
    Zepto.animationLeft({
        valve : '#list-address-valve',
        wrapper : '#list-address-wrapper',
        scroll : '#list-address-scroll'
    });

    // 发票
    Zepto.animationLeft({
        valve : '#invoice-valve',
        wrapper : '#invoice-wrapper',
        openCallback:function(){
            Zepto("#js-module-order").hide();
            Zepto(".nctouch-cart-bottom").hide();
        }
    });
    Zepto(document).on("click","#js-btn-back",function(){
        Zepto("#js-module-order").show();
        Zepto(".nctouch-cart-bottom").show();
    })

    //增值税发票中的地区选择
    Zepto('#invoice-list').on('click', '#invoice_area_info', function(){
        Zepto.areaSelected({
            success: function (a)
            {
                Zepto("#invoice_area_info").val(a.area_info).attr({"data-areaid1": a.area_id_1, "data-areaid2": a.area_id_2, "data-areaid3": a.area_id_3, "data-areaid": a.area_id, "data-areaid2": a.area_id_2 == 0 ? a.area_id_1 : a.area_id_2})
            }
        });
    });
    
    
    // 发票选择
    Zepto('#invoice-noneed').click(function(){
        Zepto(this).addClass('sel');
        Zepto("#invoice_type input").attr("disabled",true);
        Zepto('#invoice_type .label').addClass("ucli").removeClass('checked');
        Zepto('#invoice_type .label').eq(0).removeClass('ucli').addClass('checked');
        Zepto('#invoice-need').removeClass('sel');
        Zepto('#invoice_add,#invoice-list').hide();
    });
    Zepto('#invoice-need').click(function(){
        Zepto(this).addClass('sel');
        Zepto("#invoice_type input").removeAttr("disabled");
        Zepto('#invoice_type .label').removeClass("ucli");
        Zepto('#invoice-noneed').removeClass('sel');
        Zepto('#invoice_add,#invoice-list').show();
        var html = '<option value="明细">明细</option><option value="办公用品">办公用品</option><option value="电脑配件">电脑配件</option><option value="耗材">耗材</option>';
        Zepto('#inc_content').append(html);
        //获取发票列表
        Zepto.ajax({
            type:'post',
            url:ApiUrl+'/index.php?ctl=Buyer_Cart&met=piao&typ=json',
            data:{k:key, u:getCookie('id')},
            dataType:'json',
            success:function(result){
                checkLogin(result.login);
                var html = template.render('invoice-list-script', result.data);
                Zepto('#invoice-list').html(html)
                jQuery("#inv_ele_phone").intlTelInput({
                    utilsScript: "../../js/utils.js"
                });
            }
        });
    })

    // 发票类型选择
    Zepto('body').on('click', '#invoice_type em.label', function () {
        if (Zepto("#invoice-need").hasClass('sel')) {
            Zepto('#invoice_type em.label').removeClass('checked');
            Zepto(this).addClass('checked');

            //增值税发票
            if (Zepto(this).find("input[name='inv_title_select']").val() == 'increment') {
                Zepto('#invoice-list>#addtax').show();
                Zepto('#invoice-list>#electron').hide();
                Zepto('#invoice-list>#normal').hide();
                jQuery("#inv_ele_phone").intlTelInput({
                    utilsScript: "../../js/utils.js"
                });
                
            } //电子发票
            else if(Zepto(this).find("input[name='inv_title_select']").val() == 'electronics') {
                // $('#invoice-list').find(".checked").removeClass("checked");
                var personal = Zepto('#invoice-list>#electron').find("input[name='inv_ele_title_type']").val();
                console.log(personal);
                // $('#invoice-list>#electron').find(".personal_lable").addClass("checked");
                Zepto('#invoice-list>#electron').show();
                Zepto('#invoice-list>#normal').hide();
                Zepto('#invoice-list>#addtax').hide();
                jQuery("#inv_tax_recphone").intlTelInput({
                    utilsScript: "../../js/utils.js"
                });
            }//普通发票
            else
            {
                // $('#invoice-list').find(".checked").removeClass("checked");
                var personal = Zepto('#invoice-list>#normal').find("input[name='inv_ele_title_type']").val();
                console.log(personal);
                // $('#invoice-list>#normal').find(".personal_lable").addClass("checked");
                Zepto('#invoice-list>#normal').show();
                Zepto('#invoice-list>#electron').hide();
                Zepto('#invoice-list>#addtax').hide();


            }
        }
    });
    var invoice_title = true;
    var tax_num = true;

    // 发票添加
    Zepto('#invoice-sure').click(function(){
        //选择需要发表按钮

        if (Zepto('#invoice-need').hasClass('sel')) {
            //判断选择的发票类型
            var invoice_type = $('#invoice_type').find(".checked").find("input[name='inv_title_select']").attr('id');

            if(invoice_type == 'norm'){

                invoice_title = inv_ele_title('normal');
                tax_num = company_tax_num('normal');
            }
            if(invoice_type == 'electronics'){
                invoice_title = inv_ele_title('electron');
                tax_num = company_tax_num('electron');
                var email = Zepto('#invoice-list').find("input[name='inv_ele_email']").val();
                var reg_email = /^\w{3,}@\w+(\.\w+)+$/;
                if(email)
                {
                    if (!reg_email.test(email)) {
                        Zepto.sDialog({skin: "red", content: '请输入正确的邮箱！', okBtn: false, cancelBtn: false});
                        return false;
                    }
                }
                else
                {
                    Zepto.sDialog({skin: "red", content: '请输入邮箱！', okBtn: false, cancelBtn: false});
                    return false;
                }
            }

            if(!invoice_title){
                Zepto.sDialog({skin: "red", content: '请输入发票抬头！', okBtn: false, cancelBtn: false});
                return false;
            }

            var inv_ele_title_type = Zepto('#invoice-list').find(".checked").find("input[name='inv_ele_title_type']").val();
            if(inv_ele_title_type == 'company')
            {
                if(!tax_num){
                    Zepto.sDialog({skin: "red", content: '请输入企业税号！', okBtn: false, cancelBtn: false});
                    return false;
                }
            }

            
            
            var back_flag = add_invoice(invoice_type);
        } else {
            Zepto('#invContent').html('不需要发票');
            back_flag = true;
        }
        if(back_flag){
            Zepto('#invoice-wrapper').find('.header-l > a').click();
        }
    });
});

function inv_ele_title(type){
    var inv_type = Zepto('#'+type).find('.checked').find('input').val();
    if(inv_type == 'company')
    {
        var inv_title = Zepto('#'+type).find(".company_inv").attr('data-status');
        if(inv_title){
            // var title = Zepto('#'+type).find(".company_inv").next().next().val();
            var title = Zepto('#'+type).find(".company_tit").val();
            console.log(Zepto('#'+type).find(".company_tit").attr("id"));
            console.log("test");
            if(title.length>0) {

                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
    else
    {
        return true;
    }

}

function company_tax_num(type){
    var inv_title = Zepto('#'+type).find(".company_inv").attr('data-status');
    if(inv_title ){

        var num = Zepto('#'+type).find("input[name='company_tax_num']").val();

        if(num.length>0) {

            return true;
        }else{
            return false;
        }
    }else{
        return true;
    }




}





function CompanyTaxNumShow(type,ele)
{

    invoice_title = true;
    tax_num = true;

    if(type == 1){
        //显示
        Zepto('#'+ele).find('.js-company-tax-num').removeClass('hide');
    }else{
        //隐藏
        Zepto('#'+ele).find('.js-company-tax-num').addClass('hide');
    }
}

function add_invoice_ajax(data){



    var result = "";
    Zepto.ajax({
        type:'post',
        url: ApiUrl+"?ctl=Buyer_Invoice&met=addInvoice&typ=json",
        data:data,
        dataType: "json",
        async:false,
        success:function(a){
            result = a;
        }
    });
    return result;
}


function add_invoice(invoice_type){
    if(invoice_type == 'norm'){
        var obj = Zepto("#normal");
        var invoice_state = '1';
        var title = obj.find('.checked').find("input[name='inv_ele_title_type']").val() == 'company'  ? obj.find('.checked').find("input[name='inv_ele_title']").val() : '个人';
        var cont  = obj.find("#inc_normal_content").val();
         var invContent = '普通发票'+' '+obj.find('.checked').find("input[name='inv_ele_title']").val()+' '+ cont;
        var invoice_code = obj.find("input[name='company_tax_num']").val();
    }
    if(invoice_type == 'electronics'){
        var obj = Zepto("#electron");
        var invoice_state = '2';
        var email = obj.find("input[name='inv_ele_email']").val();
        var phone = obj.find("input[name='inv_ele_phone']").val();
        var invoice_area_code = obj.find("input[name='invoice_area_code']").val();
        var cont  = obj.find("#inc_content").val();
        var title = obj.find('.checked').find("input[name='inv_ele_title_type']").val() == 'company'  ?obj.find('.checked').find("input[name='inv_ele_title']").val() : '个人';
        var invContent = '电子发票'+' '+obj.find('.checked').find("input[name='inv_ele_title']").val()+' '+ cont;
        var invoice_code = obj.find("input[name='company_tax_num']").val();
    }
    if(invoice_type == 'increment'){
         //将增值税发票保存到数库中
        var title = Zepto("#addtax").find("input[name='inv_tax_title']").val();
        var company = Zepto("#addtax").find("input[name='inv_tax_title']").val();
        var code    = Zepto("#addtax").find("input[name='inv_tax_code']").val();
        var addr = Zepto("#addtax").find("input[name='inv_tax_address']").val();
        var phone = Zepto("#addtax").find("input[name='inv_tax_phone']").val();
        var bname = Zepto("#addtax").find("input[name='inv_tax_bank']").val();
        var bcount = Zepto("#addtax").find("input[name='inv_tax_bankaccount']").val();
        var cname = Zepto("#addtax").find("input[name='inv_tax_recname']").val();
        var cphone = Zepto("#addtax").find("input[name='inv_tax_recphone']").val();
        var invoice_area_code1 = Zepto("#addtax").find("input[name='invoice_area_code1']").val();
        var province = Zepto("#addtax").find("input[name='invoice_tax_rec_province']").val();
        var caddr = Zepto("#addtax").find("input[name='inv_tax_rec_addr']").val();
        var province_id = Zepto("#addtax").find("input[name='invoice_tax_rec_province']").attr('data-areaid1');
        var city_id = Zepto("#addtax").find("input[name='invoice_tax_rec_province']").attr('data-areaid2');
        var area_id = Zepto("#addtax").find("input[name='invoice_tax_rec_province']").attr('data-areaid3');
        var cont = Zepto("#addtax").find("#inc_tax_content").val();
        var invContent = '增值税发票'+' '+title+' '+ cont;
        var data = {
            invoice_state:'3',
            invoice_title:title,
            invoice_company:company,
            invoice_code:code,
            invoice_reg_addr:addr,
            invoice_reg_phone:phone,
            invoice_reg_bname:bname,
            invoice_reg_baccount:bcount,
            invoice_rec_name:cname,
            invoice_rec_phone:cphone,
            invoice_area_code:invoice_area_code1,
            invoice_rec_province:province,
            invoice_province_id:province_id,
            invoice_city_id:city_id,
            invoice_area_id:area_id,
            invoice_goto_addr:caddr,
            k:key, u:getCookie('id')
        };
        var result = add_invoice_ajax(data);
    }else{        
        var data = {
            invoice_state:invoice_state,
            invoice_title:title,
            invoice_code:invoice_code,
            invoice_rec_phone:phone,
            invoice_area_code:invoice_area_code,
            invoice_rec_email:email,
            k:key, 
            u:getCookie('id')
        };
        var result = add_invoice_ajax(data);
    }
    
    if(result.status == 200)
    {
        Zepto('#invContent').html(invContent);
        Zepto("#order_invoice_title").val(title);
        Zepto("#order_invoice_content").val(cont);
        Zepto("#order_invoice_id").val(result.data.invoice_id);
        return true;
    }
    else
    {
        if(typeof(result.msg)){
            var msg = result.msg;
        }else{
            var msg = '操作失败';
        }

        Zepto.sDialog({
            content: msg,
            okBtn:false,
            cancelBtnText:'返回',
            cancelFn: function() { }
        });
        return false;
    }
    return ;
}















