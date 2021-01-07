$(function () {
    var k = getCookie("key");
    var u = getCookie("id");
    var add_goods_flag = true;//判断添加、编辑商品
    var action = '';//增删改
    var edit_explore_status = 0;

    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }

    //心得标题字数限制
    $("input[name='explore_title']").keyup(function () {
        var lengths = $(this).val().length;
        //当输入框的字数限制正好为30时，光标消失
        if (lengths == 30) {
            $(" input[ name='explore_title' ] ").blur();
        }
        if (lengths > 30) {
            $(this).val($(this).val().substring(0, 29));
        }
        if (lengths <= 0) {
            lengths = 0;
        }
        var last_length = Number(30 - lengths);
        if (last_length <= 5) {
            $("#num").addClass('active');
        } else {
            $("#num").removeClass('active');
        }
        $("#num").html(last_length);//剩余字数
    });

    //心得编辑
    var explore_id = getQueryString('explore_id');
    if (explore_id) {
        var data = {
            k: k,
            u: u,
            explore_id: explore_id,
        };
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getExploreByExploreId&typ=json",
            data: data,
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    var data = res.data;
                    if (data.images.length > 0) {
                        //图片
                        $(".heart-img-input").addClass('hide');
                        $(".heart-upimg-swiper").removeClass('hide');
                        var r = template.render("images-list-tmpl", data);
                        $(".swiper-add-image").before(r);
                    }
                    if (data.images.length >= 9) {
                        $(".swiper-add-image").addClass('hide');
                    }

                    //标签

                    //判断是否有标签
                    if (data.lables.length > 0) {
                        $("#add_lable_list").removeClass('hide');
                        $("#add_lable").addClass('hide');
                        var str = template.render("lable-tmpl", data);
                        $("#add_lable_list").find("div").html(str);
                        var html = template.render("choose-lable-tmpl", data);
                        $("#lable_list").html(html);
                    } else {
                        $("#add_lable_list").addClass('hide');
                        $("#add_lable").removeClass('hide');
                    }

                    //标题
                    $("input[name='explore_title']").val(data.explore_title);
                    var last_length = Number(30 - data.explore_title.length);
                    if (last_length <= 5) {
                        $("#num").addClass('active');
                    } else {
                        $("#num").removeClass('active');
                    }
                    $("#num").html(last_length);//剩余字数
                    //内容
                    $("textarea[name='explore_content']").val(data.explore_content);

                    //判断当前文章为草稿或心得(2为草稿)
                    edit_explore_status = data.explore_status;
                }
            }
        });
    }

    var swiper = new Swiper('.heart-upimg-swiper', {
        slidesPerView: "auto",
        freeMode: true,
        observer: true,//修改swiper自己或子元素时，自动初始化swiper
        observeParents: true,//修改swiper的父元素时，自动初始化swiper
    });


    $.animationUp({
        valve: '.js-add-information',          // 动作触发，为空直接触发
        wrapper: '#social-add-information',    // 动作块
        scroll: ''  // 滚动块，为空不触发滚动
    });
    //添加商品信息button
    $(".js-add-information").click(function () {
        add_goods_flag = true;
        $("#add_or_edit").html('添加').data('image_goods_id', '');
        //判断商品是否达到上限 最大20件商品
        var goods_common_id = [];
        $(".heart-relative-infor-items>li").each(function () {
            goods_common_id.push($(this).data('goods_common_id'));
        });
        var goods_length = goods_common_id.length;
        if (goods_length >= 20) {
            $('.limit-text-tips').removeClass('hide');
            $('.info-add-module').addClass('hide');
        } else {
            $('.limit-text-tips').addClass('hide');
            $('.info-add-module').removeClass('hide');
        }
        cancel_assignment();
    })

    //input（品牌、商品搜索）清空
    $(document).on('click', '.search-input-icon', function () {
        $(this).prev().val('');
        $(this).removeClass('active');
    });

    //input清空（品牌、商品）
    $(document).on('click', '.input-quxiao-icon', function () {
        if ($(this).hasClass('brand')) {
            $("#choose_brand_search").parent().prev().addClass('hide').data('brand_id', '');
        }
        $(this).parent().prev().val('');
        $(this).removeClass('active');

    });

    //添加、选择标签标签
    $("#add_lable,#add_lable_list").click(function () {
        $("#explore").addClass('hide');
        $("#explore_lable").removeClass('hide');
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getHotLable&typ=json",
            data: {k: k, u: u,},
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    var r = template.render("lable-list-tmpl", res.data);
                    $(".recommend-tags-items").html(r)
                }
            }
        })
    });

    //搜索标签
    $("#lable_name_input").keyup(function () {
        var search = $(this).val();
        if (search.length > 0) {
            $(this).parent().next().addClass('active');
        } else {
            $(this).parent().next().addClass('hide');
        }
    });
    $("#lable_name_input").keypress(function (e) {
        var lable_content = $(this).val();
        var data = {
            k: k,
            u: u,
            lable_content: lable_content,
        };
        if (e.keyCode == 13) {
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getExploreLable&typ=json",
                data: data,
                dataType: "json",
                success: function (res) {
                    if (res.status == 200) {
                        $(".label-title").html('搜索结果');
                        var r = template.render("lable-list-tmpl", res.data);
                        $(".recommend-tags-items").html(r)
                    }
                }
            })
        }
    });
    $(".lable-input-icon").click(function () {
        $(this).prev().val('');
        $(this).removeClass('active');
    });

    //删除标签
    $(document).on('click', '.icon-close', function () {
        $(this).parent().parent().remove();
    });

    //选择标签
    $(document).on('click', '.recommend-tags-items li', function () {
        var lable_name = $(this).find('span').html();
        var lable_id = $(this).find('span').data('lable_id');

        //判断是否已经存在该标签(已存在的不重新添加)
        if ($("#lable_list").find('.lable' + lable_id).length == 0 && lable_id) {
            var html = "<li class='swiper-slide lable" + lable_id + "'><input type='hidden' value='" + lable_id + "' name='lable_id' ><span><i class='iconfont icon-jing'></i><em>" + lable_name + "</em><i class='iconfont icon-close'></i></span></li>"
            $("#lable_list").append(html);
        }

    });
    var swiper = new Swiper('.heart-need-tags', {
        slidesPerView: "auto",
        freeMode: true,
        observer: true,//修改swiper自己或子元素时，自动初始化swiper
        observeParents: true,//修改swiper的父元素时，自动初始化swiper

    });

    //创建标签
    $(document).on('click', '.tag-build', function () {
        var lable_content = $(this).prev().find('span').html();
        var data = {
            k: k,
            u: u,
            lable_content: lable_content,
        };
        var _this = $(this);
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=addExploreLable&typ=json",
            data: data,
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    var html = "<li class='swiper-slide lable" + res.data.id + "'><input type='hidden' value='" + res.data.id + "' name='lable_id' ><span><i class='iconfont icon-jing'></i><em>" + lable_content + "</em><i class='iconfont icon-close'></i></span></li>";
                    $("#lable_list").append(html);
                    // _this.prev().find('span').attr('data-lable_id', res.data.id);
                    // _this.prev().addClass('choose-lable');
                    _this.remove();
                }
            }
        })
    });

    //添加标签页返回按钮
    $("#explore_lable").on('click', '.back', function () {
        $("#explore").removeClass('hide');
        $("#explore_lable").addClass('hide');
    })

    //标签添加完成
    $("#lable_complete").click(function () {
        var html = '';
        var lable_ids = [];
        $(".heart-need-tags input").each(function () {
            lable_ids.push($(this).val());
            html += "<span><i class='iconfont icon-jing'></i><em>";
            html += $(this).next().find('em').html();
            html += "</em></span>";
        });
        if (lable_ids.length > 0) {
            $("#add_lable_list").find('div').html(html);
            $("#add_lable_list").removeClass('hide');
            $("#add_lable").addClass('hide');
        } else {
            $("#add_lable_list").addClass('hide');
            $("#add_lable").removeClass('hide');
        }
        $("#explore").removeClass('hide');
        $("#explore_lable").addClass('hide');
    });

    //图片上传
    var i = 0;
    $("input[name='upfile']").ajaxUploadImage({

        url: ApiUrl + "/index.php?ctl=Upload&action=upload",
        // url: ApiUrl + "/index.php?ctl=Upload&action=uploadImage",
        data: {key: k},
        start: function (e) {
            //     alert(123123);
            //     return;
            $("#edit_image").removeClass('hide');
            $("#explore").addClass('hide');
            $('.heart-img-input').addClass('hide');
            $(".heart-upimg-swiper").removeClass('hide');
            $(".social-edit-img").html('');
            $("#image_next").addClass('hide');
            $(".loading-box").removeClass('hide');
        },
        success: function (e, res) {

            $(".loading-box").addClass('hide');
            if (res.state == 'SUCCESS') {
                i = $(".heart-upimg-items>li").length;
                $("#image_next").removeClass('hide');
                //图片写入yf_explore_images表
                addExploreImages(res.url, i, res.type, res.poster_image);
            } else {
                $('.heart-img-input').removeClass('hide');
                $(".heart-upimg-swiper").addClass('hide');
                $("#edit_image").addClass('hide');
                $("#explore").removeClass('hide');
                $.sDialog({skin: "red", content: "文件过大！", okBtn: false, cancelBtn: false});
                return false
            }

            if ($('.heart-upimg-items').find('li').length == 9) {
                $(".swiper-add-image").addClass('hide');
            }
        }
    });

    // $("input[name='upfile']").click(function () {
    //     i = $(".heart-upimg-items>li").length;
    //     var a = "https://shop.local.yuanfeng021.com/image.php/shop/data/upload/media/plantform/d3aabd05be45670d48e2685d1e1f5992/image/20181205/1544000752143088.jpeg";
    //     addExploreImages(a, i);
    // });

    //删除图片以及对应商品
    $(document).on('click', '.icon-forbid', function (event) {
        var _this = $(this);
        var images_id = _this.prev().data('images_id');
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=delExploreImages&typ=json",
            data: {images_id: images_id, k: k, u: u},
            dataType: "json",
            success: function (data) {
                if (data.status == 200) {
                    var length = $(".heart-upimg-items li").length;
                    _this.parent().remove();
                    if (length <= 2) {
                        $(".heart-upimg-swiper").addClass('hide');
                        $(".heart-img-input").removeClass('hide');
                    }
                    i--;
                    window.location.reload();
                }
            }
        })
    });

    //图片对应详细商品信息
    $(document).on('click', '.edit-images-goods', function () {
        var is_img_flag = $(this).hasClass('swiper-add-image');
        var images_id = $(this).find('img').data('images_id');
        var image_url = $(this).find('img').attr('src');
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getGoodsByImagesId&typ=json",
            data: {images_id: images_id, k: k, u: u},
            dataType: "json",
            success: function (data) {
                console.log(data);
                if (data.status == 200) {
                    $("#edit_image").removeClass('hide');
                    $("#explore").addClass('hide');
                    $.ajax({
                        type: "post",
                        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getImagesId&typ=json",
                        data: {images_id: images_id, k: k, u: u},
                        dataType: "json",
                        success: function (rel) {
                            console.log(rel);
                            if (rel.type == ".mp4") {
                                var img_str = "<video controls autoplay src='" + image_url + "'' alt='video' data-images_id='" + images_id + "'>";
                                $("#edit_image").find('.social-edit-img').html(img_str).data('images_id', images_id);
                            } else {
                                var img_str = "<img src='" + image_url + "'' alt='img' data-images_id='" + images_id + "'>";
                                $("#edit_image").find('.social-edit-img').html(img_str).data('images_id', images_id);
                            }
                        }
                    });
                    var html = template.render("goods-list-tmpl", data);
                    $(".heart-relative-infor-items").html(html);
                }
            }
        })

    });


    function addExploreImages(images_url, i, type, poster_image) {

        console.log(type);
        // console.log(i);
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=addExploreImages&typ=json",
            data: {images_url: images_url, type: type, poster_image: poster_image, k: k, u: u},
            dataType: "json",
            success: function (data) {
                if (data.status == 200) {
                    $("#edit_image").removeClass('hide');
                    $("#explore").addClass('hide');
                    var str =
                        "  <div class='social-edit-video' style='position: relative;width: 98%;background: #000;'>\n" +
                        "   <video controls='' autoplay='' style='max-width:100%;' src='" + images_url + "'  alt='video' data-images_id='" + data.data.id + "'></video>\n" +
                        "    <i class='iconfont icon-forbid active'></i>\n" +
                        "   </div>\n";
                    $(".swiper-add-image").before(str);
                    $(".swiper-add-image").hide();
                    //图片与视频
                    if (type == ".mp4") {
                        var img_str = "<video controls autoplay style='max-width:100%;' src='" + images_url + "'' alt='video' data-images_id='" + data.data.id + "' >";
                    } else {
                        var img_str = "<img src='" + images_url + "'' alt='img' data-images_id='" + data.data.id + "'>";
                    }

                    $("#edit_image").find('.social-edit-img').html(img_str).data('images_id', data.data.id);
                    $(".heart-relative-infor-items").html('');
                    var image_num = Number(i);
                    $("#image_num").html(image_num);
                }
            }
        })
    }

    //编辑图片返回-button
    $("#edit_image").on('click', '.back', function () {
        $("#edit_image").addClass('hide');
        $("#explore").removeClass('hide');
    });
    //编辑图片-继续button
    $("#image_next").click(function () {
        $(".heart-img-input").addClass('hide');
        $(".heart-upimg-swiper").removeClass('hide');
        $("#edit_image").addClass('hide');
        $("#explore").removeClass('hide');
    })

    //编辑图片- 选择品牌
    $("#choose_brand_input").click(function () {
        $("#edit_image").addClass('hide');
        $("#choose_brand").removeClass('hide');
        $("#choose_brand_search").attr('placeholder', '搜索任意品牌').focus();
        $("#choose_brand_search").parent().prev().data('search_name', 'brand').data('brand_id', '').html('').addClass('hide');
        $("#choose_brand_search").parent().next().addClass('hide');
        $("#search-ul-items").removeClass('heart-order-goods-items').addClass('heart-search-brand-items').html('');
        $(".load-completion").addClass("hide");
    });

    //品牌、商品取消、完成
    $(".heart-search-btn").click(function () {
        $("#choose_brand").addClass('hide');
        $("#edit_image").removeClass('hide');
        $(".heart-search-btn").removeClass('active').html('取消');
    })

    //商品、品牌搜索
    $("#choose_brand_search").keyup(function () {
        var search = $(this).val();
        if (search.length > 0) {
            $(this).next().removeClass('hide');
            $(this).next().addClass('active');
        } else {
            $(this).next().addClass('hide');
            $(this).next().removeClass('active');
        }
    });
    //品牌搜索
    $("#choose_brand_search").keypress(function (e) {
        var search_name = $(this).parent().prev().attr('data-search_name');//判断品牌、商品、从订单搜索商品
        var search_words = $(this).val();
        var brand_id = $(this).parent().prev().data('brand_id');
        var data = {
            k: k,
            u: u,
            search_words: search_words,
            brand_id: brand_id,
        };

        if (e.keyCode == 13) {
            switch (search_name) {
                case 'brand':
                    chooseBrand(data);//品牌搜索
                    break;
                case 'goods':
                    chooseGoods(data);//商品搜索
                    break;
                case 'fromOrder':
                    chooseGoodsFromOrder(data);//从订单搜索商品
                    break;
                default:
                    chooseBrand(data);//品牌搜索
                    break;
            }
        }
    });

    //根据品牌id选择商品
    $(document).on('click', '.choose_brand_goods', function () {
        $(".heart-search-btn").addClass('active').html('完成');
        var brand_id = $(this).data('brand_id');
        var brand_name = $(this).data('brand_name');
        var data = {
            k: k,
            u: u,
            brand_id: brand_id,
            brand_name: brand_name,
        };
        //添加商品信息赋值
        $("#choose_brand_input").val(brand_name).next().find('i').addClass('active');
        $("#choose_brand_search").val('').attr('placeholder', '搜索该品牌下任意商品').focus();
        $("#choose_brand_search").parent().prev().removeClass('hide').data('brand_id', brand_id).data('search_name', 'goods').html(brand_name);
        $("#choose_brand_search").parent().next().addClass('hide');
        chooseGoods(data);
    });
    //商品搜索
    $(document).on('click', '#choose_goods_input', function () {
        var brand_id = $("#choose_brand_input").data('brand_id');
        var brand_name = $("#choose_brand_input").val();
        $("#choose_brand").removeClass('hide');
        $("#edit_image").addClass('hide');
        if (brand_name) {
            $("#choose_brand_search").attr('placeholder', '搜索该品牌下任意商品').focus();
            $("#choose_brand_search").parent().prev().html(brand_name).removeClass('hide');
        } else {
            $("#choose_brand_search").attr('placeholder', '搜索任意商品').focus();
        }
        $("#choose_brand_search").parent().prev().attr('data-search_name', 'goods');
        $("#search-ul-items").html('');
        $(".load-completion").addClass("hide");
    });

    //品牌搜索
    function chooseBrand(data) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=chooseBrand&typ=json",
            data: data,
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    if (res.data.items.length > 0) {
                        var r = template.render("search-brand-list-tmpl", res.data);
                        $("#search-ul-items").html(r);
                        $(".load-completion").removeClass("hide");
                        $(".social-nodata").addClass("hide");
                    } else {
                        $("#search-ul-items").html('');
                        $(".load-completion").addClass("hide");
                        $(".social-nodata").removeClass("hide");
                    }
                }
            }
        })
    }

    //商品搜索
    function chooseGoods(data) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=chooseGoodsByBrand&typ=json",
            data: data,
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    if (res.data.items.length > 0) {
                        var r = template.render("search-goods-list-tmpl", res.data);
                        $("#search-ul-items").removeClass('heart-search-brand-items').addClass('heart-search-brand-items').html(r);
                        $(".load-completion").removeClass("hide");
                        $(".social-nodata").addClass("hide");
                    } else {
                        $(".load-completion").addClass('hide');
                        $("#search-ul-items").removeClass('heart-search-brand-items').addClass('heart-search-brand-items').html('');
                        $(".social-nodata").removeClass("hide");
                    }

                }
            }
        })
    }

    //从订单中搜索商品
    function chooseGoodsFromOrder(data) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getGoodsFromOrder&typ=json",
            data: data,
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    if (res.data.length > 0) {
                        var r = template.render("search-order-goods-list-tmpl", res);
                        $("#search-ul-items").html(r).addClass('heart-order-goods-items').removeClass('heart-search-brand-items');
                        $(".load-completion").removeClass("hide");
                        $(".social-nodata").addClass("hide");
                    } else {
                        $(".load-completion").addClass("hide");
                        $("#search-ul-items").html('').removeClass('heart-order-goods-items').addClass('heart-search-brand-items');
                        $(".social-nodata").removeClass("hide");
                    }
                }
            }
        })
    }

    //选中商品
    $(document).on('click', '.choose_goods_common', function () {
        $(".heart-search-btn").removeClass('active').html('取消');

        var common_id = $(this).data('common_id');
        var common_name = $(this).data('common_name');
        var brand_id = $("#choose_brand_search").parent().prev().data('brand_id');
        var brand_name = $("#choose_brand_search").parent().prev().html();
        //添加商品信息赋值
        assignment(common_id, common_name, brand_id, brand_name);
        information_up();

        $("#edit_image").removeClass('hide');
        $("#choose_brand").addClass('hide');
        $("#choose_brand_search").val('').attr('placeholder', '搜索任意品牌');
        $("#choose_brand_search").parent().prev().addClass('hide').attr('data-brand_id', brand_id);
    });

    //编辑已经选择的商品
    $(document).on('click', '.edit-goods-common', function () {
        add_goods_flag = false;//编辑
        $("#add_or_edit").html('编辑');
        var common_id = $(this).data('common_id');
        var common_name = $(this).prev().html();
        var brand_id = $(this).data('brand_id');
        var brand_name = $(this).data('brand_name');
        var image_goods_id = $(this).parent().data('image_goods_id');
        $("#add_or_edit").data('image_goods_id', image_goods_id);
        //控制编辑对象
        $(".heart-relative-infor-items").find('li').removeClass('edit_goods');
        $(this).parent().addClass('edit_goods');
        //添加商品信息赋值
        assignment(common_id, common_name, brand_id, brand_name);
        information_up();
    })

    //商品信息-确认button
    $(".btn-push-sure").click(function () {
        var brand_name = $("#choose_brand_input").val();
        var brand_id = $("#choose_brand_input").data('brand_id');
        var common_name = $("#choose_goods_input").val();
        var common_id = $("#choose_goods_input").data('common_id');
        var images_id = $("#edit_image").find('.social-edit-img').data('images_id');
        var image_goods_id = $("#add_or_edit").data('image_goods_id');
        var data = {
            k: k,
            u: u,
            id: image_goods_id,
            images_id: images_id,
        };
        if (brand_name) {
            data.brand_id = brand_id;
            data.brand_name = brand_name;
        }
        if (common_name) {
            data.common_name = common_name;
            data.goods_common_id = common_id;
        }
        if (!add_goods_flag) {
            $("#add_or_edit").html('添加');
            action = 'edit';
            //品牌、商品同时清空，则删除对应商品
            if (brand_name == '' && common_name == '') {
                action = 'del';
            }
        } else {
            $("#add_or_edit").html('编辑');
            action = 'add';
        }
        sureButton(action, data)
        information_down();
    })

    //图片id、商品存入yf_explore_images_goods
    function sureButton(action, data) {
        var ajax_url = ApiUrl;
        switch (action) {
            case 'add'://添加
                ajax_url += "/index.php?ctl=Explore_Explore&met=addImagesGoods&typ=json";
                break;
            case 'edit'://编辑
                ajax_url += "/index.php?ctl=Explore_Explore&met=editImagesGoods&typ=json";
                break;
            case 'del'://删除
                ajax_url += "/index.php?ctl=Explore_Explore&met=delImagesGoods&typ=json";
                break;
            default:
                break;
        }
        $.ajax({
            type: "post",
            url: ajax_url,
            data: data,
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    //添加编辑品牌、商品
                    if (data.brand_name && data.common_name) {
                        var str1 = "<i class='iconfont icon-goods'></i>\n" +
                            "<span class='one-overflow'>" + data.common_name + "</span>\n" +
                            "<em class='fr edit-goods-common' data-common_id='" + data.goods_common_id + "' data-brand_id='" + data.brand_id + "' data-brand_name='" + data.brand_name + "'>编辑</em>";
                        var str2 = "<li class='clearfix' data-image_goods_id='" + res.data.id + "'>\n" +
                            "   <i class='iconfont icon-goods'></i>\n" +
                            "   <span class='one-overflow'>" + data.common_name + "</span>\n" +
                            "   <em class='fr edit-goods-common' data-common_id='" + data.goods_common_id + "' data-brand_id='" + data.brand_id + "' data-brand_name='" + data.brand_name + "'>编辑</em>\n" +
                            "</li>";
                    } else if (data.brand_name && !data.common_name) {
                        var str1 = "<i class='iconfont icon-goods'></i>\n" +
                            "<span class='one-overflow'>" + data.brand_name + "</span>\n" +
                            "<em class='fr edit-goods-common' data-common_id='' data-brand_id='" + data.brand_id + "' data-brand_name='" + data.brand_name + "'>编辑</em>";
                        var str2 = "<li class='clearfix' data-image_goods_id=''>\n" +
                            "   <i class='iconfont icon-goods'></i>\n" +
                            "   <span class='one-overflow'>" + data.brand_name + "</span>\n" +
                            "   <em class='fr edit-goods-common' data-common_id='' data-brand_id='" + data.brand_id + "' data-brand_name='" + data.brand_name + "'>编辑</em>\n" +
                            "</li>";
                    } else if (!data.brand_name && data.common_name) {
                        var str1 = "<i class='iconfont icon-goods'></i>\n" +
                            "<span class='one-overflow'>" + data.common_name + "</span>\n" +
                            "<em class='fr edit-goods-common' data-common_id='" + data.goods_common_id + "' data-brand_id='' data-brand_name=''>编辑</em>";
                        var str2 = "<li class='clearfix' data-image_goods_id='" + res.data.id + "'>\n" +
                            "   <i class='iconfont icon-goods'></i>\n" +
                            "   <span class='one-overflow'>" + data.common_name + "</span>\n" +
                            "   <em class='fr edit-goods-common' data-common_id='" + data.goods_common_id + "' data-brand_id='' data-brand_name=''>编辑</em>\n" +
                            "</li>";
                    } else {
                        var str1 = "";
                        var str2 = "";
                    }

                    if (action == 'edit') {
                        $(".edit_goods").find('em').data('brand_id', data.brand_id);
                        $(".edit_goods").find('em').data('brand_name', data.brand_name);
                        $(".edit_goods").find('em').data('common_id', data.common_id);
                        if (data.common_name == '') {
                            $(".edit_goods").find('span').html(data.brand_name);
                        }
                        //品牌、商品编辑
                        $(".edit_goods").html(str1);
                    } else if (action == 'add') {
                        $(".heart-relative-infor-items").append(str2);
                    } else if (action == 'del') {
                        //品牌、商品同时清空，则删除对应商品
                        if (data.brand_name == '' && data.common_name == '') {
                            $(".edit_goods").remove();
                        }
                    }
                    action = '';
                    $(".heart-relative-infor-items").find('li').removeClass('edit_goods');

                    $.sDialog({
                        skin: "green",
                        content: '添加成功',
                        okBtn: false,
                        cancelBtn: false
                    });
                }
            }
        })
    }

    //商品信息赋值
    function assignment(common_id, common_name, brand_id, brand_name) {
        if (brand_name && brand_id) {
            $("#choose_brand_input").data('brand_id', brand_id).val(brand_name);
            $("#choose_brand_input").next().find('i').addClass('active');
        }
        if (common_name && common_id) {
            $("#choose_goods_input").data('common_id', common_id).val(common_name);
            $("#choose_goods_input").next().addClass('active');
            $("#choose_goods_input").next().find('i').addClass('active');
        }
    }

    //取消商品信息赋值
    function cancel_assignment() {
        $("#choose_brand_input").data('brand_id', '').val('');
        $("#choose_brand_input").next().find('i').removeClass('active');
        $("#choose_goods_input").data('common_id', '').val('');
        $("#choose_goods_input").next().removeClass('active');
        $("#choose_goods_input").next().find('i').removeClass('active');
    }

    //商品信息-取消button
    $(".btn-push-cancel").click(function () {
        information_down();
    })

    //从订单搜索商品
    $("#get_order_goods").click(function () {
        information_down();
        $("#choose_brand").removeClass('hide');
        $("#edit_image").addClass('hide');
        $("#choose_brand_search").parent().prev().addClass('hide').data('brand_id', '').html('');
        $("#choose_brand_search").attr('placeholder', '搜索订单中的商品');
        $("#choose_brand_search").parent().prev().data('search_name', 'fromOrder');
        var data = {
            k: k,
            u: u,
        };
        chooseGoodsFromOrder(data);
    });

    //发布心得
    $("#addExplore").click(function () {
        var images_id = [];
        $(".heart-upimg-items>div").each(function () {
            var images_flag = $(this).hasClass('swiper-add-image');
            if (!images_flag) {
                console.log($(this).find('video').data('images_id'));
                images_id.push($(this).find('video').data('images_id'));
            }
        });
        var lable_ids = [];
        $(".heart-need-tags input").each(function () {
            lable_ids.push($(this).val());
        });
        var explore_title = $("input[name='explore_title']").val();
        var explore_content = $("textarea[name='explore_content']").val();
        if (explore_id) {
            //编辑
            var data = {
                k: k,
                u: u,
                explore_id: explore_id,
                images_id: images_id,
                lable_ids: lable_ids,
                explore_title: explore_title,
                explore_content: explore_content,
                explore_status: 0,//状态-上架
                edit_explore_status: edit_explore_status //判断当前文章是否为草稿发布 2为草稿发布
            };
            var url = ApiUrl + "/index.php?ctl=Explore_Explore&met=editExplore&typ=json";
        } else {
            //添加
            var data = {
                k: k,
                u: u,
                images_id: images_id,
                lable_ids: lable_ids,
                explore_title: explore_title,
                explore_content: explore_content,
            };
            var url = ApiUrl + "/index.php?ctl=Explore_Explore&met=addExplore&typ=json";
        }
        //照片、标题、心得内容必填
        if (images_id.length <= 0) {
            $.sDialog({skin: "red", content: "请上传图片", okBtn: false, cancelBtn: false});
            return false;
        }
        if (explore_title == '') {
            $.sDialog({skin: "red", content: "请输入心得标题", okBtn: false, cancelBtn: false});
            return false;
        }
        if (explore_content == '') {
            $.sDialog({skin: "red", content: "请输入心得内容", okBtn: false, cancelBtn: false});
            return false;
        }
        var action = 'add';
        addOrEditExplore(data, url, action);
    });

    //返回是否保存草稿
    $(".js-cancel-push").click(function () {
        var images_id = [];
        $(".heart-upimg-items>li").each(function () {
            var images_flag = $(this).hasClass('swiper-add-image');
            if (!images_flag) {
                images_id.push($(this).find('img').data('images_id'));
            }
        });
        var lable_ids = [];
        $(".heart-need-tags input").each(function () {
            lable_ids.push($(this).val());
        });
        var explore_title = $("input[name='explore_title']").val();
        var explore_content = $("textarea[name='explore_content']").val();
        var data = {
            k: k,
            u: u,
            images_id: images_id,
            lable_ids: lable_ids,
            explore_title: explore_title,
            explore_content: explore_content,
            explore_status: 5,//保存为草稿
        };
        if (!explore_id) {
            var action = 'draft_add';
            var url = ApiUrl + "/index.php?ctl=Explore_Explore&met=addExplore&typ=json";
        } else {
            var action = 'draft_edit';
            data.explore_id = explore_id;
            var url = ApiUrl + "/index.php?ctl=Explore_Explore&met=editExplore&typ=json";
        }
        if (images_id.length > 0 || lable_ids > 0 || explore_title != '' || explore_content != '') {
            $.sDialog({
                skin: "red",
                content: "是否保存为草稿？",
                okBtn: true,
                cancelBtn: true,
                "okBtnText": "是",
                "cancelBtnText": "不保存",
                "okFn": function () {
                    addOrEditExplore(data, url, action);
                },
                "cancelFn": function () {
                    //返回上一层
                    window.history.go(-1);
                }
            });
        } else {
            //返回上一层
            window.history.go(-1);
        }
    });

    //添加或编辑社交心得
    function addOrEditExplore(data, url, action) {
        $.ajax({
            type: "post",
            url: url,
            data: data,
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    if (action == 'draft_edit' || action == 'draft_add') {
                        $.sDialog({skin: "red", content: "保存成功！", okBtn: false, cancelBtn: false});
                        setTimeout(function () {
                            window.location.href = WapSiteUrl + "/tmpl/explore_center.html?from=draft_edit&user_id=" + u;
                        }, 3000);
                    } else {
                        $.sDialog({skin: "red", content: "发布成功！", okBtn: false, cancelBtn: false});
                        setTimeout(function () {
                            window.location.href = WapSiteUrl + "/tmpl/explore_list.html";
                        }, 3000);
                    }
                }
            }
        })
    }

    //添加商品信息弹层
    function information_up() {
        $("#social-add-information").addClass('up');
        $("#social-add-information").removeClass('down');
    }

    function information_down() {
        $("#social-add-information").removeClass('up');
        $("#social-add-information").addClass('down');
    }
});

