$(function () {
    var api = frameElement.api, callback = api.data.callback, set = api.data.data;
    if (set == 1) {
        $(".max-image").show();
        $(".min-image").hide();
    } else {
        $(".max-image").hide();
        $(".min-image").show();
    }

    //删除已上传的图片
    $(".del-img").click(function () {
        $(this).next().val('');
        $(this).prev().prop('src', '../shop_admin/static/common/images/image.png');
    })

    $(".submit-btn").click(function () {
        var image_infos = [];
        if(set == 1){
            $(".max-image").find('dd').each(function () {
                var image_info = {}
                var img_path = $(this).find('.img-path').val();
                var img_url = $(this).find('.img-url').val();
                if (img_path || img_url) {
                    image_info.img_path = img_path;
                    image_info.img_url = img_url;
                    image_infos.push(image_info);
                }
            });
        }else{
            $(".min-image").find('dd').each(function () {
                var image_info = {}
                var img_path = $(this).find('.img-path').val();
                var img_url = $(this).find('.img-url').val();
                if (img_path || img_url) {
                    image_info.img_path = img_path;
                    image_info.img_url = img_url;
                    image_infos.push(image_info);
                }
            });
        }
        typeof callback == 'function' && callback(set,image_infos);
        api.close();
    })
});