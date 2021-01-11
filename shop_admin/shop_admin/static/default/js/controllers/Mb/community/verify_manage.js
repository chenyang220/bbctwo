var api = frameElement.api;
var oper = api.data.oper;
var rowData = api.data.rowData || {};
var callback = api.data.callback;
initPopBtns();
initField();
initEvent();

function initField(){
    if(rowData.id){
        Public.ajaxPost( SITE_URL + '?ctl=Mb_Community&typ=json&met=getExploreList', {explore_id:rowData.id}, function(data){
            console.log(data);
            if (data.status == 200) {
                $('#user_account').html(data.data.user_account);
                $('#user_mobile').html(data.data.user_mobile);
                $('#explore_title').html(data.data.explore_title);
                $('#explore_content').html(data.data.explore_content);
                $('#explore_verify_remark').html(data.data.explore_verify_remark);
                if(data.data.explore_status==0){
                    $('#verify_type').html('<em>是</em>');
                }else if(data.data.explore_status==4){
                    $('#verify_type').html('<em>否</em>');
                }
                var list = data.data;
                var goods = data.data.goods;
                var image_str='';
                var goods_str='';
                for(var i = 0;i<list.explore_images.length;i++){
                    if (list.explore_images[i].type == '.mov' || list.explore_images[i].type == '.mp4'){
                        image_str+='<em class="img-box" style="width:150px;height:100px;float:left">';
                        image_str+='<video class="my-video" style="max-width:100%;" controls controlsList="nofullscreen nodownload noremote footbar" src="'+list.explore_images[i].images_url +'"></video>';
                        image_str+='</em>';
                    }else{
                        image_str+='<em class="img-box"><img class="img" src="'+list.explore_images[i].images_url+'"alt="" style="width:150px;height:100px;float:left"></em>';
                    }
                }
                $('#image_info').html(image_str);

                for(var i = 0;i<goods.goods.length;i++){
                    goods_str+='<div>';
                    goods_str+='<em class="img-box"><img class="img" src="'+goods.goods[i].common_image+'"alt="" style="width:60px;height:60px;"></em>';
                    goods_str+='<p>'+goods.goods[i].common_name+'</p>';
                    goods_str+='<em>'+goods.goods[i].common_price+'</em>';
                    goods_str+='</div>';   
                }
                $('#goods_info').html(goods_str);
            } else {
                parent.parent.Public.tips({type:1, content : msg + '获取数据失败！' + data.msg});
            }
        });        
    }


}

function initEvent(){
    var $number = $('#number');

    Public.limitInput($number, /^[a-zA-Z0-9\-_]*$/);
    Public.bindEnterSkip($('#manage-wrap'), postData, oper, rowData.id);
    initValidator();
    $number.focus().select();
}

function initPopBtns(){
    var operName = oper == "add" ? ["保存", "关闭"] : ["确定", "取消"];
    api.button({
        id: 'confirm',
        name: operName[0],
        focus: true,
        callback: function() {
            postData(oper, rowData.id);
            return false;
        }
    },{
        id: 'cancel',
        name: operName[1]
    });
}

function initValidator(){
    $.validator.addMethod('number', function(value){
        return /^[a-zA-Z0-9\-_]*$/.test(value);
    });

    $('#manage-form').validate({
        rules: {
            common_verify_remark: {
                required: true
            }
        },
        messages: {
            common_verify_remark: {
                required: __('审核理由不能为空')
            }
        },
        errorClass: 'valid-error'
    });
}

function postData(oper, id){

    if (Number($('input:radio[name="explore_status"]:checked').val()))
    {
        if(!$('#manage-form').validate().form()){
            $('#manage-form').find('textarea.valid-error').eq(0).focus();
            return ;
        }
    }

    var	explore_verify_remark = $.trim($('#explore_verify_remark').val());
    var msg = oper == 'add' ? '新增内容' : '内容审核';

    if(rowData.id){
        params = { explore_id: id, explore_status: $('input:radio[name="explore_status"]:checked').val(), explore_verify_remark:explore_verify_remark};
    }else{
        params = {};
    }
    Public.ajaxPost( SITE_URL + '?ctl=Mb_Community&typ=json&met=editCommonVerify', params, function(data){
        if (data.status == 200) {
            rowData.explore_status = data.data['explore_status'];
            rowData.operate = oper;
            parent.parent.Public.tips({content : msg + '成功！'});
            if(callback && typeof callback == 'function'){
                
                callback(rowData, oper, window);
            }
        } else {
            parent.parent.Public.tips({type:1, content : msg + '失败！' + data.msg});
        }
    });
}

function resetForm(data){
    $('#manage-form').validate().resetForm();
    $('#name').val('');
    $('#number').val(Public.getSuggestNum(data.locationNo)).focus().select();
}