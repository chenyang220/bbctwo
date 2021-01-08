/** common.js **/
$(document).ready(function() {
//下拉效果
    function select2Temp (obj) {
      if (!obj.id) {
        return obj.text;
      }
      var style = "";
      if(obj.id == '0'){
        style="style='color:#444;'"
      }else if(obj.disabled==false){
        style="style='color:#fe3a3a'"
      }else if(obj.disabled==true){
        style="style='color:#999'"
      }       
      var v = obj.text;
      var arr = v.split(" ");
      if(arr[1]){
        arr[1] = "<i class='fr'>"+arr[1]+"</i>"
       
          var $obj = $(
            '<span '+style+'> ' + arr[0]+ arr[1] + '</span>'
          );
      
      }else{
        var $obj = $(
            '<span '+style+'> ' + arr[0]+ '</span>'
          );
      }
      
      return $obj;
    };

    //选中效果
    function select2Selection(obj){
            var v = obj.text;
            var arr = v.split(' ');
            var curstyle = "";
            console.log(arr);
          if(obj.id == '0'){
            curstyle="style='color:#444;'"
          }else if(obj.disabled==false){
            curstyle="style='color:#fe3a3a;'"
          }
          if(arr[0]){
            var $curobj=$('<i '+curstyle+'> ' + arr[0]+ '</i>');
          }
             return $curobj;
    }
    
    var order_redpacket_info = $("input[name='redpacket']").val();
    var order_is_discount = $("input[name='redpacket']").data("is_discount");
    var voucher_info = $("input[name='voucher']").val();
    if(voucher_info){
        $(".shop_voucher").select2({
            width:"resolve",
            placeholder: voucher_info,
            templateResult: select2Temp,
            templateSelection: select2Selection,
            minimumResultsForSearch: -1,
            language: {
                noResults: function (params) {
                    $(".shop_voucher").select2("close");
                }
             }
        });
    }
    if(order_redpacket_info){
        $("#redpacket").select2({
            width:"resolve",
            placeholder: order_redpacket_info,
            templateResult: select2Temp,
            templateSelection: select2Selection,
            minimumResultsForSearch: -1,
            language: {
                noResults: function (params) {
                    $("#redpacket").select2("close");
                }
             }
        });
    }
    
    if(order_is_discount == 1) $(".select2").hide();
});
FuckInternetExplorer();    

function FuckInternetExplorer() {
    var browser = navigator.appName;
    var b_version = navigator.appVersion;
    var version = b_version.split(";");
    if (version.length > 1) {
        var trim_Version = parseInt(version[1].replace(/[ ]/g, "").replace(/MSIE/g, ""));
        if (trim_Version < 9) {  
            document.write('<div class="notifyjs-bootstrap-base notifyjs-bootstrap-error">建议IE8以上版本!</div>'); 
            return false;
        }
    } 
    return true;
}


function lazyload(){ 
 
    $("img.lazy").lazyload({ 
        effect: "fadeIn" 
         
    });
    
    $('img.lazy').on('load',function(){
       $(window).trigger('scroll') 
    });
} 

function chat(ch_u){

}
var Public = Public || {};
var Business = Business || {};
Public.isIE6 = !window.XMLHttpRequest;	//ie6

window.im_appId = '';
window.im_appToken = '';

Public.getDefaultPage = function ()
{
    var win = window.self;
    var i = 20;//最多20层，防止无限嵌套
    try
    {
        do {
            if (!(/index.php\?/.test(win.location.href)))
            {
                return win;
            }
            win = win.parent;
            i--;
        } while (i > 0);
    } catch (e)
    {
        return win;
    }
    return win;
};

function getQueryString(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r!=null) return r[2]; return '';
}


/*
 通用post请求，返回json
 url:请求地址， params：传递的参数{...}， callback：请求成功回调
 */
Public.ajaxPost = function(url, params, callback, errCallback, completeCallback){
    var loading;
    var $this = $(this);
    var preventTooFast = 'ui-btn-dis';
    $.ajax({
        type: "POST",
        url: url,
        data: params,
        dataType: "json",
        beforeSend : function(){
            $this.addClass(preventTooFast);
            myTimer = setTimeout(function(){
                $this.removeClass(preventTooFast);
            },2000)
        },
        complete : function(){
            completeCallback && completeCallback();
        },
        success: function(data, status){
            callback(data);
        },
        error: function(err,ms){
            errCallback && errCallback(err);
        }
    });
};

//生成树
Public.zTree = {
    zTree: {},
    opts: {
        showRoot: true,
        defaultClass: '',
        disExpandAll: false,//showRoot为true时无效
        callback: '',
        rootTxt: '全部分类'
    },
    setting: {
        view: {
            dblClickExpand: false,
            showLine: true,
            selectedMulti: false
        },
        data: {
            simpleData: {
                enable: true,
                idKey: "id",
                pIdKey: "parent_id",
                rootPId: ""
            }
        },
        callback: {

        }
    },
    _getTemplate: function (opts)
    {
        this.id = 'tree' + parseInt(Math.random() * 10000);
        var _defaultClass = "ztree";
        if (opts)
        {
            if (opts.defaultClass)
            {
                _defaultClass += ' ' + opts.defaultClass;
            }
        }
        return '<ul id="' + this.id + '" class="' + _defaultClass + '"></ul>';
    },
    init: function ($target, opts, setting, callback)
    {
        if ($target.length === 0)
        {
            return;
        }
        var self = this;
        self.opts = $.extend(true, self.opts, opts);
        self.container = $($target);
        self.obj = $(self._getTemplate(opts));
        self.container.append(self.obj);
        setting = $.extend(true, self.setting, setting);


        var defaultPage = Public.getDefaultPage();

        if (defaultPage.SYSTEM.goodsCatInfo)
        {
            if (self.opts.showRoot)
            {
                defaultPage.SYSTEM.goodsCatInfo.shift();
            }
            self._callback(defaultPage.SYSTEM.goodsCatInfo);
        }
        else
        {
            Public.ajaxPost(opts.url || '', {}, function (data)
            {
                if (data.status === 200 && data.data)
                {
                    defaultPage.SYSTEM.goodsCatInfo = data.data.items;
                    self._callback(data.data.items);
                }
                else
                {
                    Public.tips({
                        type: 2,
                        content: "加载失败！"
                    });
                }
            });
        }

        return self;
    },
    _callback: function (data)
    {
        var self = this;
        var callback = self.opts.callback;
        if (self.opts.showRoot)
        {
            data.unshift({name: self.opts.rootTxt, id: -1});
            self.obj.addClass('showRoot');
        }
        if (!data.length)
        {
            return;
        }
        self.zTree = $.fn.zTree.init(self.obj, self.setting, data);
        self.zTree.expandAll(!self.opts.disExpandAll);
        if (callback && typeof callback === 'function')
        {
            callback(self, data);
        }
    }
};

//分类下拉框
Public.categoryTree = function ($obj, opts)
{
    if ($obj.length === 0)
    {
        return;
    }

    opts = opts ? opts : opts = {};
    var opts = $.extend(true, {
        url: SITE_URL + '?ctl=Goods_Cat&met=cat&typ=json&type_number=goods_cat&is_delete=2',
        inputWidth: '145',
        width: '',//'auto' or int
        height: '240',//'auto' or int
        trigger: true,
        defaultClass: 'ztreeCombo',
        disExpandAll: false,//展开闭合
        defaultSelectValue: 0,
        showRoot: true,
        treeSettings: {
            callback: {
                beforeClick: function (treeId, treeNode)
                {
                    var check = (treeNode && !treeNode.isParent);

                    if (!check)
                    {
                        //alert("只能选择最后一级分类...")
                    }
                    else
                    {
                        if (_Combo.obj)
                        {
                            _Combo.obj.val(treeNode.name);
                            _Combo.obj.data('id', treeNode.id);
                            _Combo.hideTree();
                        }
                    }

                    return check;
                },
                onClick: function (treeId, treeNode)
                {
                    _Combo.obj.trigger("change");
                }
            }
        }
    }, opts);
    var _Combo = {
        container: $('<span class="ui-tree-wrap" style="width:' + opts.inputWidth + 'px"></span>'),
        obj: $('<input type="text" class="input-txt" style="width:' + (opts.inputWidth - 26) + 'px" id="' + $obj.attr('id') + '" autocomplete="off" readonly value="' + ($obj.val() || $obj.text()) + '">'),
        trigger: $('<span class="trigger"></span>'),
        data: {},
        init: function ()
        {
            var _parent = $obj.parent();
            var _this = this;
            $obj.remove();
            this.obj.appendTo(this.container);
            if (opts.trigger)
            {
                this.container.append(this.trigger);
            }
            this.container.appendTo(_parent);
            opts.callback = function (publicTree, data)
            {
                _this.zTree = publicTree;
                //_this.data = data;
                if (publicTree)
                {
                    publicTree.obj.css({
                        'max-height': opts.height
                    });
                    for (var i = 0, len = data.length; i < len; i++)
                    {
                        _this.data[data[i].id] = data[i];
                    }
                    ;
                    if (opts.defaultSelectValue !== '')
                    {
                        _this.selectByValue(opts.defaultSelectValue);
                    }
                    ;
                    _this._eventInit();
                }
            };
            this.zTree = Public.zTree.init($('body'), opts, opts.treeSettings);
            return this;
        },
        showTree: function ()
        {
            if (!this.zTree)
            {
                return;
            }
            if (this.zTree)
            {
                var offset = this.obj.offset();
                var topDiff = this.obj.outerHeight();
                var w = opts.width ? opts.width : this.obj.width();
                var _o = this.zTree.obj.hide();
                _o.css({width: w, top: offset.top + topDiff, left: offset.left - 1});
            }
            var _o = this.zTree.obj.show();
            this.isShow = true;
            this.container.addClass('ui-tree-active');
        },
        hideTree: function ()
        {
            if (!this.zTree)
            {
                return;
            }
            var _o = this.zTree.obj.hide();
            this.isShow = false;
            this.container.removeClass('ui-tree-active');
        },
        _eventInit: function ()
        {
            var _this = this;
            if (opts.trigger)
            {
                _this.trigger.on('click', function (e)
                {
                    e.stopPropagation();
                    if (_this.zTree && !_this.isShow)
                    {
                        _this.showTree();
                    }
                    else
                    {
                        _this.hideTree();
                    }
                });
            }
            ;
            _this.obj.on('click', function (e)
            {
                e.stopPropagation();
                if (_this.zTree && !_this.isShow)
                {
                    _this.showTree();
                }
                else
                {
                    _this.hideTree();
                }
            });
            if (_this.zTree)
            {
                _this.zTree.obj.on('click', function (e)
                {
                    e.stopPropagation();
                });
            }
        },
        getValue: function ()
        {
            var id = this.obj.data('id') || '';
            if (!id)
            {
                var text = this.obj.val();
                if (this.data)
                {
                    for (var item in this.data)
                    {
                        if (this.data[item].name === text)
                        {
                            id = this.data[item].id;
                        }
                    }
                }
            }
            return id;
        },
        getText: function ()
        {
            if (this.obj.data('id'))
            {
                return this.obj.val();
            }
            return '';
        },
        selectByValue: function (value)
        {
            if (value !== '')
            {
                if (this.data)
                {
                    this.obj.data('id', value);
                    this.obj.val(this.data[value].name);
                    
                }
            }
            return this;
        }
    };
    var combo = _Combo.init();
    var nodeList = [], searchName;

    //搜索事件
    if (opts.searchByName && $(opts.searchByName).get(0) && opts.searchButton && $(opts.searchButton).get(0)) {
        $(opts.searchButton).click(function (){
            combo.showTree();
            searchName = $(opts.searchByName).val();
            var zTree = $.fn.zTree.getZTreeObj(combo.zTree.id);
            zTree.showNodes(zTree.transformToArray(zTree.getNodes()));
            if (searchName) {
                nodeList = zTree.getNodesByFilter(function (node) {
                    return node.name.toString().indexOf(searchName) > -1;
                }); // 查找节点集合
                findNodes(nodeList);
            }
        });
    }

    function findNodes(searchNodeList) {
        var zTree = $.fn.zTree.getZTreeObj(combo.zTree.id),
            allNodes = zTree.getNodes();
        zTree.hideNodes(allNodes); //隐藏所有节点

        if (searchNodeList) {
            var showNodesList = [], parentNodeList = [];
            for( var i=0, l=searchNodeList.length; i<l; i++) {
                parentNodeList = findParents(searchNodeList[i]);
                $.merge(showNodesList, parentNodeList);
            }

            zTree.showNodes(showNodesList);
            //获取ids
            var nodeTIds = $.map(showNodesList, function (n, i) {
                return n.tId;
            });

            //隐藏不需要的子节点
            var hideChildrenNodes = [];
            for( var i=0, l=showNodesList.length, children; i<l; i++) {
                children = findChildren(showNodesList[i]);
                if (children) {
                    for( var m=0, n=children.length; m<n; m++) {
                        if ( $.inArray(children[m].tId, nodeTIds) == -1 ) {
                            hideChildrenNodes.push(children[m]);
                        }
                    }
                }
            }
            zTree.hideNodes(hideChildrenNodes);
        }
    }

    function findParents(node) {
        return node.getPath();
    }

    function findChildren(node) {
        return node.children;
    }
    return combo;
};

(function($) {
    $.fn.yf_show_dialog = function(options) {

        var that = $(this);
        var settings = $.extend({}, {width: 480, title: '', close_callback: function() {}}, options);

        var init_dialog = function(title) {
            var _div = that;
            that.addClass("dialog_wrapper");
            that.wrapInner(function(){
                return '<div class="dialog_content">';
            });
            that.wrapInner(function(){
                return '<div class="dialog_body" style="position: relative;border-radius:3px; ">';
            });
            that.find('.dialog_body').prepend('<h3 class="dialog_head ui_title" style="cursor: move;"><span class="dialog_title"><span class="dialog_title_icon">'+settings.title+'</span></span><span class="dialog_close_button iconfont icon-cuowu"></span></h3>');
            that.append('<div style="clear:both;"></div>');

            $(".dialog_close_button").click(function(){
                settings.close_callback();
                _div.hide();
            });

            that.draggable({handle: ".dialog_head"});
        };

        if(!$(this).hasClass("dialog_wrapper")) {
            init_dialog(settings.title);
        }

        settings.left = $(window).scrollLeft() + ($(window).width() - settings.width) / 2;
        settings.top  = ($(window).height() - $(this).height()) / 2;
        $(this).attr("style","display:none; z-index: 1100;background-color:; position: fixed; width: "+settings.width+"px; left: "+settings.left+"px; top: "+settings.top+"px;");
        $(this).show();

    };
})(jQuery);

Public.tips = function(options){
    var defaults = {
        "type": 0,
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    var opts = $.extend({},defaults,options);

    // toastr.clear();

    if (1 == parseInt(opts.type))
    {
        toastr.error(opts.content, null, opts);
    }
    else if (2 == parseInt(opts.type))
    {
        toastr.warning(opts.content, null, opts);
    }
    else if (3 == parseInt(opts.type))
    {
        toastr.success(opts.content, null, opts);
    }
    else
    {
        toastr.info(opts.content, null, opts);
    }
}

Public.tips.info = function(msg)
{
    Public.tips({type: 4, content: msg});
}

Public.tips.error = function(msg)
{
    Public.tips({type: 1, content: msg});
}


Public.tips.success = function(msg)
{
    Public.tips({type: 3, content: msg});
}


Public.tips.warning = function(msg)
{
    Public.tips({type: 2, content: msg});
}


function ucenterLogin(UCENTER_URL, SITE_URL, refresh_flag)
{
    $.ajax({
        type: "get",
        url: UCENTER_URL + "?ctl=Login&met=checkStatus&typ=json",
        dataType: "jsonp",
        jsonp: "jsonp_callback",
        success: function(data){
            if (200 == data.status)
            {
                var key = $.cookie('key');
                var u = $.cookie('id');

                if (u && key && u==data.data.us)
                {
                    getUserInfoNav()
                }
                else
                {
                    //退出
                    $.cookie('id', null);
                    $.cookie('key', null);

                    //本系统登录API
                    $.ajax({
                        type: "get",
                        url: SITE_URL + "?ctl=Login&met=check&typ=json",
                        data:{ks:data.data.ks, us:data.data.us},
                        dataType: "jsonp",
                        jsonp: "jsonp_callback",
                        success: function(data){
                            console.info(data);
                            if (200 == data.status)
                            {
                                //本系统登录API
                                $.cookie('id',data.data.user_id);
                                $.cookie('key',data.data.key);

                                //ajax 调用
                                if (refresh_flag)
                                {
                                    window.location.reload();
                                }
                                else
                                {
                                    getUserInfoNav()
                                }
                                //
                            }
                        },
                        error: function(){
                            //alert('error!');
                        }
                    });
                }
            }
            else
            {
                //退出
                $.cookie('id', null);
                $.cookie('key', null);

                //ajax 调用
                if (refresh_flag)
                {
                    window.location.reload();
                }
                else
                {
                    getUserInfoNav()
                }
            }
        },
        error: function(){
            getUserInfoNav()
        }
    });
}
function getUserInfoNav()
{
    $.ajax({
        type: "GET",
        url: SITE_URL + "?ctl=Index&met=getUserLoginInfo&typ=json",
        data: {},
        dataType: "json",
        success: function(data){
            var html = '';
            $.each(data, function(commentIndex, comment){

            });

            $('#login_top').find('.header_select_province').siblings().remove();
            $('#login_top').prepend(data.data[0]);
            $('#login_tright').html(data.data[1]);

            //用户登录 - 加载聊天窗口
            if(typeof(IM_STATU)!=='undefined' && IM_STATU==1 && data.data[3])
            {
                $.ajax({
                    type: "GET",
                    url: "index.php?ctl=Im&met=im&typ=json",
                    data: {},
                    dataType: "json",
                    success: function(data){
                        console.info(data);
                        if(data.status == 200){
                            window.im_appId    = data.data.im_appId;
                            window.im_appToken = data.data.im_appToken;
                            url = 'index.php?ctl=Index&met=chat';
                            $("#chat").load(url, function(){
                            });
                        }
                    }
                });
            }
        }
    });
    $(".set").hover(function(){
        $(this).find(".sub-menu").css("display","block");
        $(this).find("i").css("transform","rotate(-180deg)");
    },function(){
        $(this).find(".sub-menu").css("display","none");
        $(this).find("i").css("transform","rotate(1deg)");
    })
}
function load_goodseval(url,div) {
    $("#" + div).load(url, function(){
    });
}
//console
if ( !window.console ) {
    window.console = {
        info: function () {},
        log: function () {}
    };
}
//加入购物车时，获取最新的购物车列表
function getCartList()
{
    var url = SITE_URL + '?ctl=Index&met=toolbar';
    $(".J-global-toolbar").load(url, function(){
    });
}
//获取购物车商品数量
function getCartNum() {
    Public.ajaxPost(SITE_URL + "?ctl=Buyer_Cart&met=getCartGoodsNum&typ=json", {},
        function(data) {
            if (data.status == 200) {
                if(data.data.cart_count > 0)
                {
                    $('.ci-count').show();
                    $('.cart_num_toolbar').show();
                    $('.ci-count, .cart_num_toolbar').text(data.data.cart_count);
                }
                else
                {
                    $('.ci-count').hide();
                    $('.cart_num_toolbar').hide();
                }
            }
        }
    )
}
//购物车下拉框
$(function() {
    $.cookie("key") && getCartNum();

    $(document).on(
        {
            "mouseenter": function() {
                if (!$.cookie("key") || $(this).hasClass("hover")) {
                    return false;
                }
                $(this).addClass("hover");
                $("#J_cart_body").load(SITE_URL + "?ctl=Index&met=getCart&typ=e");
            },
            "mouseleave": function() {
                $(this).removeClass("hover");
            }
        },
        "#J_settle_up"
    );
    $("#J_settle_up").on("click", ".J_delete",
        function() {
            var cart_id = $(this).data("cart_id");
            Public.ajaxPost(SITE_URL + "?ctl=Buyer_Cart&met=delCartByCid&typ=json", {id: cart_id},
                function(data) {
                    if (data.status == 200) {
                        Public.tips.success(data.msg);
                        getCartNum(), getCartList(); //更新购物车
                        $("#J_cart_body").load(SITE_URL + "?ctl=Index&met=getCart&typ=e");
                    } else {
                        Public.tips.warning(data.msg);
                    }
                }
            )
        }
    )
    $(document).on("click",'.not_shop',function(){
        $.dialog.alert('非商家用户不可进入批发市场！');
    })
    $(document).on("click",'.not_shop_login',function(){
        $.dialog.alert('请先登录系统！');
    })
});
/*****************************************************************************/
/** index.js **/
+$(document).ready(function(){
	var i=0;
	var index;
	var timer=null;
    //顶部导航栏鼠标移入效果
    $(".floor_head nav li").bind("mouseover",function(){
    	$(".floor_head nav li").find("a").removeClass("selected");
    	$(this).find("a").addClass("selected");
    	var aW=$(this).find("a").width();
    	var pad=parseInt($(this).find("a").css("paddingLeft"));
    	var liW=aW+pad*2+1;
    	$(this).css("width",liW);
    	
    })
    //左侧菜单栏鼠标移入效果
     $(".tleft ul li").hover(function(){
        $(this).addClass("hover_leave");
        $(this).find(".hover_content").show();
    },function(){
        $(this).removeClass("hover_leave");
        $(this).find(".hover_content").hide();
    })

 	//导航栏移入显示下拉单
 	$(".head_right dl").hover(function(){
 		$(this).addClass("navactive");
 		$(this).find("dd").show();
        $(this).prev().find("p").css("right","-2px");
 	},function(){
 		$(".head_right dl").removeClass("navactive");
 		$(".head_right dd").hide();
        $(this).prev().find("p").css("right","-1px");
 	})
    
	//按类型搜索
    $(".search-types li").click(function()
	{
       $(".search-types li").removeClass("active");
       $(this).addClass("active");
	   var type = $(this).find("a").attr('data-param');

		if(type=='shop')
	    {
		   $("#search_ctl").val('Shop_Index');
		   $("#search_met").val('index');
		}else{
		   $("#search_ctl").val('Goods_Goods');
		   $("#search_met").val('goodslist');
		}
    })

 	//遍历楼层图标背景
 	$(".m .mt .title span").each(function(i){
 		var str="url("+STATIC_URL+"/images/flad"+(i+1)+".png)";
  		$(this).css("background",str);
  	})
    //遍历商品背景色
    var arr=["#fff0f0","#fdf5f2","#f1f6ef","#f9f9f9","#f2fbff"];
    $.each($(".goodsUl li"),function(i,obj){
         if(i>=5){
            var thisindex=$(this).index();
          i=thisindex-Math.floor(thisindex/5)*5;
        }
        $(this).css("backgroundColor",arr[i])
       
    })
    //商品滚动
    function doMove(obj,attr,speed,target,callBack){
        if(obj.timer) return;
        var ww=obj.css(attr);
        var num = parseFloat(ww); 
        speed = num > target ? -Math.abs(speed) : Math.abs(speed);
        obj.timer = setInterval(function (){
            num += speed;
            if( speed > 0 && num >= target || speed < 0 && num <= target  ){
                num = target;
                clearInterval(obj.timer);
                obj.timer = null;
                var mm=num+"px";
                obj.css(attr,mm);
                (typeof callBack === "function") && callBack();

            }else{
                var mm=num+"px";
                 obj.css(attr,mm)
            }
        },30)   
    }
    var m=0;
    $(".btn1").bind("click",function(){
        var W=$(this).parent().width();
        var goodsUl=$(this).parent().find(".goodsUl");
        var ali=goodsUl.find("li");
        var rightA=$(this).parent().find(".btn2");
        m=$(this).attr("data-numb");
        if(m<=0){
            m=0;
            return;
        }
        m--;
        $(this).attr("data-numb",m);
        rightA.attr("data-num",m);
        doMove(goodsUl,"left",30, -m*W);

    })
    $(".btn2").bind("click",function(){
        var W=$(this).parent().width();
        var goodsUl=$(this).parent().find(".goodsUl");
        var ali=goodsUl.find("li");
        goodsUl.css("width",240*ali.length);
        var ulW=goodsUl.width();
        var nums=Math.ceil(ulW/W);
        var leftA=$(this).parent().find(".btn1");
        m=$(this).attr("data-num");
        if(m>=(nums-1)){
            return;
        }
        m++;
        $(this).attr("data-num",m);
        leftA.attr("data-numb",m);
        doMove(goodsUl,"left",30,-m*W);
    })

    //地点定位
    $(".header_select_province").hover(function(){
        $(this).find("dt").css("background","#fff");
        $(this).find("dd").show();
    },function(){
       $(this).find("dt").css("background","#f2f2f2");
        $(this).find("dd").hide();
    })
    $(".code_screen").click(function(){
        $(".code_cont").css("display","block");
    },function(){
        $(".code_cont").css("display","none");
    })

     $(".all_check").click(function(){
        var isChecked = $(this).prop("checked");
        $(".cart_contents input").prop("checked", isChecked);
    });
    $(".cart_contents_head input").click(function(){
        var isChecked1 = $(this).prop("checked");
        $(this).parent().parent().siblings().find("input").prop("checked", isChecked1);
    })
})

/** 省的地区选择 **/
$(document).ready(function(){
	ucenterLogin(UCENTER_URL, SITE_URL, false);
    if(typeof(is_open_city) != 'undefined' && is_open_city > 0){
        if($.cookie('sub_site_name'))
        {
             $("#area").html($.cookie('sub_site_name'));   
        }
        //获取所有的一级地址
        $.post(SITE_URL  + '?ctl=Base_District&met=subSite&pid=0&typ=json',function(data){    //请求城市分站
                $(".header_select_province dd").append("<div class='dd' id='sub_site_div_0' data-domain=''><a onclick='setsubSitecook(0)'> 全部</a></li>");
                for (i = 0; i < data.data.items.length; i++) {
                    $(".header_select_province dd").append("<div class='dd' id='sub_site_div_" + data.data.items[i]['subsite_id'] +"' data-logo='"+ data.data.items[i]['sub_site_logo']+"'  data-copyright='"+ data.data.items[i]['sub_site_copyright']+"' data-domain='"+ data.data.items[i]['sub_site_domain']+"'><a onclick='setsubSitecook( "+data.data.items[i]['subsite_id'] + " )' >" + data.data.items[i]['sub_site_name'] + "</a></li>");
                }
            }
        );

        window.setsubSitecook = function(sub_site_id)
        {
            var mster_site_host = MASTER_SITE_URL.split('/'); 
            var mster_site_host_arr = mster_site_host[2].split( "." ); 
            if(mster_site_host_arr[0] == 'www'){
                mster_site_host[2] = mster_site_host[2].replace('www.','');
            }
            var domain = $('#sub_site_div_'+sub_site_id).data('domain');
            if(typeof(domain) == 'undefined' || !domain){
                window.location.href = MASTER_SITE_URL+'?sub_site_id='+sub_site_id;
            }else{
                window.location.href = '//'+domain+"."+mster_site_host[2]+'?sub_site_id='+sub_site_id;
                
            }
        }
    }
	url = SITE_URL + '?ctl=Index&met=toolbar';
		$(".J-global-toolbar").load(url, function(){
    });
})

$(function(){
    if ($.isFunction($.fn.blueberry))
    {
        $(".blueberry").blueberry();
    }
})

/** nav.js **/
/* nav.js zhaokun 20150709 主要应用于首页右侧导航栏 */
$(document).ready(function(){
	$('.tbar-cart-item').hover(function (){ $(this).find('.p-del').show(); },function(){ $(this).find('.p-del').hide(); });
	$('.jth-item').hover(function (){ $(this).find('.add-cart-button').show(); },function(){ $(this).find('.add-cart-button').hide(); });
	$('.toolbar-tab').hover(function (){ $(this).find('.tab-text').addClass("tbar-tab-hover"); $(this).find('.footer-tab-text').addClass("tbar-tab-footer-hover"); $(this).addClass("tbar-tab-selected");},function(){ $(this).find('.tab-text').removeClass("tbar-tab-hover"); $(this).find('.footer-tab-text').removeClass("tbar-tab-footer-hover"); $(this).removeClass("tbar-tab-selected"); });
	$('.tbar-tab-online-contact').hover(function (){ $(this).find('.tab-text').addClass("tbar-tab-hover"); $(this).find('.footer-tab-text').addClass("tbar-tab-footer-hover"); $(this).addClass("tbar-tab-selected");},function(){ $(this).find('.tab-text').removeClass("tbar-tab-hover"); $(this).find('.footer-tab-text').removeClass("tbar-tab-footer-hover"); $(this).removeClass("tbar-tab-selected"); });
	$('.tbar-tab-cart').click(function (){ 
		if($('.toolbar-wrap').hasClass('toolbar-open')){
			if($(this).find('.tab-text').length > 0){
				if(! $('.tbar-tab-follow').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的关注')+"</em>";
					$('.tbar-tab-follow').append(info);
					$('.tbar-tab-follow').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-follow').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-contrast').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('对比商品')+"</em>";
					$('.tbar-tab-contrast').append(info);
					$('.tbar-tab-contrast').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-contrast').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-assets').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的资产')+"</em>";
					$('.tbar-tab-assets').append(info);
					$('.tbar-tab-assets').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-assets').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-history').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的足迹')+"</em>";
					$('.tbar-tab-history').append(info);
					$('.tbar-tab-history').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-history').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-news').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('通知')+"</em>";
					$('.tbar-tab-news').append(info);
					$('.tbar-tab-news').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-news').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-sav').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的收藏')+"</em>";
					$('.tbar-tab-sav').append(info);
					$('.tbar-tab-sav').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-sav').css({'visibility':"hidden","z-index":"-1"});
				}
				$(this).addClass('tbar-tab-click-selected'); 
				$(this).find('.tab-text').remove();
				$('.tbar-panel-cart').css({'visibility':"visible","z-index":"1"});
				
			}else{
				var info = "<em class='tab-text '>"+__('我的购物车')+"</em>";
				$('.toolbar-wrap').removeClass('toolbar-open');
				$(this).append(info);
				$(this).removeClass('tbar-tab-click-selected');
				$('.tbar-panel-cart').css({'visibility':"hidden","z-index":"-1"});
			}
			 
			
		}else{ 
			$(this).addClass('tbar-tab-click-selected'); 
			$(this).find('.tab-text').remove();
			$('.tbar-panel-cart').css({'visibility':"visible","z-index":"1"});
			$('.tbar-panel-follow').css('visibility','hidden');
			$('.tbar-panel-history').css('visibility','hidden');
			$('.tbar-panel-news').css('visibility','hidden');
			$('.tbar-panel-sav').css('visibility','hidden');
			$('.tbar-panel-contrast').css('visibility','hidden');
			$('tbar-panel-assets').css('visibility','hidden');
			$('.toolbar-wrap').addClass('toolbar-open'); 
		}
	});
	$('.tbar-tab-follow').click(function (){
		if($('.toolbar-wrap').hasClass('toolbar-open')){
			if($(this).find('.tab-text').length > 0){
				if(! $('.tbar-tab-cart').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('购物车')+"</em>";
					$('.tbar-tab-cart').append(info);
					$('.tbar-tab-cart').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-cart').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-contrast').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('对比商品')+"</em>";
					$('.tbar-tab-contrast').append(info);
					$('.tbar-tab-contrast').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-contrast').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-assets').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的资产')+"</em>";
					$('.tbar-tab-assets').append(info);
					$('.tbar-tab-assets').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-assets').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-history').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的足迹')+"</em>";
					$('.tbar-tab-history').append(info);
					$('.tbar-tab-history').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-history').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-news').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('通知')+"</em>";
					$('.tbar-tab-news').append(info);
					$('.tbar-tab-news').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-news').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-sav').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的收藏')+"</em>";
					$('.tbar-tab-sav').append(info);
					$('.tbar-tab-sav').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-sav').css({'visibility':"hidden","z-index":"-1"});
				}
				$(this).addClass('tbar-tab-click-selected'); 
				$(this).find('.tab-text').remove();
				$('.tbar-panel-follow').css({'visibility':"visible","z-index":"1"});
				
			}else{
				var info = "<em class='tab-text '>"+__('我的关注')+"</em>";
				$('.toolbar-wrap').removeClass('toolbar-open');
				$(this).append(info);
				$(this).removeClass('tbar-tab-click-selected');
				$('.tbar-panel-follow').css({'visibility':"hidden","z-index":"-1"});
			}
			 
			
		}else{ 
			$(this).addClass('tbar-tab-click-selected'); 
			$(this).find('.tab-text').remove();
			$('.tbar-panel-cart').css('visibility','hidden');
			$('.tbar-panel-follow').css({'visibility':"visible","z-index":"1"});
			$('.tbar-panel-history').css('visibility','hidden');
			$('.tbar-panel-news').css('visibility','hidden');
			$('.tbar-panel-history').css('visibility','hidden');
			$('.tbar-panel-sav').css('visibility','hidden');
			$('.tbar-panel-contrast').css('visibility','hidden');
			$('tbar-panel-assets').css('visibility','hidden');
			$('.toolbar-wrap').addClass('toolbar-open'); 
		}
	});
	$('.tbar-tab-history').click(function (){
		if($('.toolbar-wrap').hasClass('toolbar-open')){
			if($(this).find('.tab-text').length > 0){
				if(! $('.tbar-tab-follow').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的关注')+"</em>";
					$('.tbar-tab-follow').append(info);
					$('.tbar-tab-follow').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-follow').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-contrast').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('对比商品')+"</em>";
					$('.tbar-tab-contrast').append(info);
					$('.tbar-tab-contrast').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-contrast').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-assets').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的资产')+"</em>";
					$('.tbar-tab-assets').append(info);
					$('.tbar-tab-assets').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-assets').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-cart').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('购物车')+"</em>";
					$('.tbar-tab-cart').append(info);
					$('.tbar-tab-cart').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-cart').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-news').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('通知')+"</em>";
					$('.tbar-tab-news').append(info);
					$('.tbar-tab-news').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-news').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-sav').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的收藏')+"</em>";
					$('.tbar-tab-sav').append(info);
					$('.tbar-tab-sav').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-sav').css({'visibility':"hidden","z-index":"-1"});
				}
				$(this).addClass('tbar-tab-click-selected'); 
				$(this).find('.tab-text').remove();
				$('.tbar-panel-history').css({'visibility':"visible","z-index":"1"});
				
			}else{
				var info = "<em class='tab-text '>"+__('我的足迹')+"</em>";
				$('.toolbar-wrap').removeClass('toolbar-open');
				$(this).append(info);
				$(this).removeClass('tbar-tab-click-selected');
				$('.tbar-panel-history').css({'visibility':"hidden","z-index":"-1"});
			}
			
		}else{ 
			$(this).addClass('tbar-tab-click-selected'); 
			$(this).find('.tab-text').remove();
			$('.tbar-panel-cart').css('visibility','hidden');
			$('.tbar-panel-follow').css('visibility','hidden');
			$('.tbar-panel-news').css('visibility','hidden');
			$('.tbar-panel-sav').css('visibility','hidden');
			$('.tbar-panel-contrast').css('visibility','hidden');
			$('tbar-panel-assets').css('visibility','hidden');
			$('.tbar-panel-history').css({'visibility':"visible","z-index":"1"});
			$('.toolbar-wrap').addClass('toolbar-open'); 
		}
	});
	$('.tbar-tab-sav').click(function (){ 
		if($('.toolbar-wrap').hasClass('toolbar-open')){
			if($(this).find('.tab-text').length > 0){
				if(! $('.tbar-tab-follow').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的关注')+"</em>";
					$('.tbar-tab-follow').append(info);
					$('.tbar-tab-follow').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-follow').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-contrast').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('对比商品')+"</em>";
					$('.tbar-tab-contrast').append(info);
					$('.tbar-tab-contrast').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-contrast').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-cart').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('购物车')+"</em>";
					$('.tbar-tab-cart').append(info);
					$('.tbar-tab-cart').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-cart').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-assets').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的资产')+"</em>";
					$('.tbar-tab-assets').append(info);
					$('.tbar-tab-assets').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-assets').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-news').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('通知')+"</em>";
					$('.tbar-tab-news').append(info);
					$('.tbar-tab-news').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-news').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-history').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的足迹')+"</em>";
					$('.tbar-tab-history').append(info);
					$('.tbar-tab-history').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-history').css({'visibility':"hidden","z-index":"-1"});
				}
				$(this).addClass('tbar-tab-click-selected'); 
				$(this).find('.tab-text').remove();
				$('.tbar-panel-sav').css({'visibility':"visible","z-index":"1"});
				
			}else{
				var info = "<em class='tab-text '>"+__('我的收藏')+"</em>";
				$('.toolbar-wrap').removeClass('toolbar-open');
				$(this).append(info);
				$(this).removeClass('tbar-tab-click-selected');
				$('.tbar-panel-sav').css({'visibility':"hidden","z-index":"-1"});
			}
			
		}else{ 
			$(this).addClass('tbar-tab-click-selected'); 
			$(this).find('.tab-text').remove();
			$('.tbar-panel-cart').css('visibility','hidden');
			$('.tbar-panel-follow').css('visibility','hidden');
			$('.tbar-panel-history').css('visibility','hidden');
			$('.tbar-panel-news').css('visibility','hidden');
			$('.tbar-panel-contrast').css('visibility','hidden');
			$('tbar-panel-assets').css('visibility','hidden');
			$('.tbar-panel-sav').css({'visibility':"visible","z-index":"1"});
			$('.toolbar-wrap').addClass('toolbar-open'); 
		}
	});
	$('.tbar-tab-news').click(function (){ 
		if($('.toolbar-wrap').hasClass('toolbar-open')){
			if($(this).find('.tab-text').length > 0){
				if(! $('.tbar-tab-follow').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的关注')+"</em>";
					$('.tbar-tab-follow').append(info);
					$('.tbar-tab-follow').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-follow').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-contrast').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('对比商品')+"</em>";
					$('.tbar-tab-contrast').append(info);
					$('.tbar-tab-contrast').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-contrast').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-cart').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('购物车')+"</em>";
					$('.tbar-tab-cart').append(info);
					$('.tbar-tab-cart').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-cart').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-assets').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的资产')+"</em>";
					$('.tbar-tab-assets').append(info);
					$('.tbar-tab-assets').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-assets').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-history').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的足迹')+"</em>";
					$('.tbar-tab-history').append(info);
					$('.tbar-tab-history').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-history').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-sav').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的收藏')+"</em>";
					$('.tbar-tab-sav').append(info);
					$('.tbar-tab-sav').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-sav').css({'visibility':"hidden","z-index":"-1"});
				}
				$(this).addClass('tbar-tab-click-selected'); 
				$(this).find('.tab-text').remove();
				$('.tbar-panel-news').css({'visibility':"visible","z-index":"1"});
				
			}else{
				var info = "<em class='tab-text '>"+__('我的通知')+"</em>";
				$('.toolbar-wrap').removeClass('toolbar-open');
				$(this).append(info);
				$(this).removeClass('tbar-tab-click-selected');
				$('.tbar-panel-news').css({'visibility':"hidden","z-index":"-1"});
			}
			
		}else{ 
			$(this).addClass('tbar-tab-click-selected'); 
			$(this).find('.tab-text').remove();
			$('.tbar-panel-cart').css('visibility','hidden');
			$('.tbar-panel-follow').css('visibility','hidden');
			$('.tbar-panel-history').css('visibility','hidden');
			$('.tbar-panel-sav').css('visibility','hidden');
			$('.tbar-panel-contrast').css('visibility','hidden');
			$('tbar-panel-assets').css('visibility','hidden');
			$('.tbar-panel-news').css({'visibility':"visible","z-index":"1"});
			$('.toolbar-wrap').addClass('toolbar-open'); 
		}
	});
	$('.tbar-tab-assets').click(function (){ 
		if($('.toolbar-wrap').hasClass('toolbar-open')){
			if($(this).find('.tab-text').length > 0){
				if(! $('.tbar-tab-follow').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的关注')+"</em>";
					$('.tbar-tab-follow').append(info);
					$('.tbar-tab-follow').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-follow').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-cart').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('购物车')+"</em>";
					$('.tbar-tab-cart').append(info);
					$('.tbar-tab-cart').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-cart').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-contrast').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('对比商品')+"</em>";
					$('.tbar-tab-contrast').append(info);
					$('.tbar-tab-contrast').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-contrast').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-history').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的足迹')+"</em>";
					$('.tbar-tab-history').append(info);
					$('.tbar-tab-history').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-history').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-news').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('通知')+"</em>";
					$('.tbar-tab-news').append(info);
					$('.tbar-tab-news').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-news').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-sav').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的收藏')+"</em>";
					$('.tbar-tab-sav').append(info);
					$('.tbar-tab-sav').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-sav').css({'visibility':"hidden","z-index":"-1"});
				}
				$(this).addClass('tbar-tab-click-selected'); 
				$(this).find('.tab-text').remove();
				$('.tbar-panel-assets').css({'visibility':"visible","z-index":"1"});
				
			}else{
				var info = "<em class='tab-text '>"+__('我的资产')+"</em>";
				$('.toolbar-wrap').removeClass('toolbar-open');
				$(this).append(info);
				$(this).removeClass('tbar-tab-click-selected');
				$('.tbar-panel-assets').css({'visibility':"hidden","z-index":"-1"});
			}
			
		}else{ 
			$(this).addClass('tbar-tab-click-selected'); 
			$(this).find('.tab-text').remove();
			$('.tbar-panel-cart').css('visibility','hidden');
			$('.tbar-panel-follow').css('visibility','hidden');
			$('.tbar-panel-history').css('visibility','hidden');
			$('.tbar-panel-sav').css('visibility','hidden');
			$('.tbar-panel-news').css('visibility','hidden');
			$('.tbar-panel-contrast').css('visibility','hidden');
			$('.tbar-panel-assets').css({'visibility':"visible","z-index":"1"});
			$('.toolbar-wrap').addClass('toolbar-open'); 
		}
	});
	$('.tbar-tab-contrast').click(function (){ 
		if($('.toolbar-wrap').hasClass('toolbar-open')){
			if($(this).find('.tab-text').length > 0){
				if(! $('.tbar-tab-follow').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的关注')+"</em>";
					$('.tbar-tab-follow').append(info);
					$('.tbar-tab-follow').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-follow').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-cart').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('购物车')+"</em>";
					$('.tbar-tab-cart').append(info);
					$('.tbar-tab-cart').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-cart').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-assets').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的资产')+"</em>";
					$('.tbar-tab-assets').append(info);
					$('.tbar-tab-assets').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-assets').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-news').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('通知')+"</em>";
					$('.tbar-tab-news').append(info);
					$('.tbar-tab-news').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-news').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-history').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的足迹')+"</em>";
					$('.tbar-tab-history').append(info);
					$('.tbar-tab-history').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-history').css({'visibility':"hidden","z-index":"-1"});
				}
				if(! $('.tbar-tab-sav').find('.tab-text').length > 0){
					var info = "<em class='tab-text '>"+__('我的收藏')+"</em>";
					$('.tbar-tab-sav').append(info);
					$('.tbar-tab-sav').removeClass('tbar-tab-click-selected'); 
					$('.tbar-panel-sav').css({'visibility':"hidden","z-index":"-1"});
				}
				$(this).addClass('tbar-tab-click-selected'); 
				$(this).find('.tab-text').remove();
				$('.tbar-panel-contrast').css({'visibility':"visible","z-index":"1"});
				
			}else{
				var info = "<em class='tab-text '>"+__('对比商品')+"</em>";
				$('.toolbar-wrap').removeClass('toolbar-open');
				$(this).append(info);
				$(this).removeClass('tbar-tab-click-selected');
				$('.tbar-panel-contrast').css({'visibility':"hidden","z-index":"-1"});
			}
			
		}else{ 
			$(this).addClass('tbar-tab-click-selected'); 
			$(this).find('.tab-text').remove();
			$('.tbar-panel-cart').css('visibility','hidden');
			$('.tbar-panel-follow').css('visibility','hidden');
			$('.tbar-panel-history').css('visibility','hidden');
			$('.tbar-panel-news').css('visibility','hidden');
			$('.tbar-panel-sav').css('visibility','hidden');
			$('tbar-panel-assets').css('visibility','hidden');
			$('.tbar-panel-contrast').css({'visibility':"visible","z-index":"1"});
			$('.toolbar-wrap').addClass('toolbar-open'); 
		}
	});
	$(".close_p").click(function(){
		$(".toolbar-wrap").removeClass("toolbar-open");
		$(".toolbar-panel").css("visibility","hidden");
		$(".toolbar-tab").removeClass("tbar-tab-click-selected");
		$(".tbar-tab-news").removeClass("tbar-tab-click-selected");
		
	})
	function not_shop()
	{
		Public.tips.warning('非商家用户不可进入批发市场！');
	}
});

/** decoration/common.js **/
$(function () {
    //search v4 by 33 hao.c om
    var act = "store_list";
    if (act == "store_list") {
        $('#search ul.tab li span').eq(0).html('店铺');
        $('#search ul.tab li span').eq(1).html('商品');
        $('#search ul.tab li').eq(0).attr('act', 'store_list');
        $('#search_act').attr("value", "store_list");
    }
    $('#search').hover(function () {
        $('#search ul.tab li').eq(1).show();
        $('#search ul.tab li i').addClass('over').removeClass('arrow');
    }, function () {
        $('#search ul.tab li').eq(1).hide();
        $('#search ul.tab li i').addClass('arrow').removeClass('over');
    });
    $('#search ul.tab li').eq(1).click(function () {
        $(this).hide();
        if ($(this).find('span').html() == '店铺') {
            $('#keyword').attr("placeholder", "请输入您要搜索的店铺关键字");
            $('#search ul.tab li span').eq(0).html('店铺');
            $('#search ul.tab li span').eq(1).html('商品');
            $('#search_act').attr("value", 'store_list');
        } else {
            $('#keyword').attr('placeholder', '请输入您要搜索的商品关键字');
            $('#search ul.tab li span').eq(0).html('商品');
            $('#search ul.tab li span').eq(1).html('店铺');
            $('#search_act').attr("value", 'search');
        }
        $("#keyword").focus();
    });
});

function drop_confirm(msg, url) {
    if (confirm(msg)) {
        window.location = url;
    }
}

function ajax_confirm(msg, url) {
    if (confirm(msg)) {
        ajaxget(url);
    }
}

function go(url) {
    window.location = url;
}

/* 格式化金额 */
function price_format(price) {
    if (typeof(PRICE_FORMAT) == 'undefined') {
        PRICE_FORMAT = '&yen;%s';
    }
    price = number_format(price, 2);
    
    return PRICE_FORMAT.replace('%s', price);
}

function number_format(num, ext) {
    if (ext < 0) {
        return num;
    }
    num = Number(num);
    if (isNaN(num)) {
        num = 0;
    }
    var _str = num.toString();
    var _arr = _str.split('.');
    var _int = _arr[0];
    var _flt = _arr[1];
    if (_str.indexOf('.') == -1) {
        /* 找不到小数点，则添加 */
        if (ext == 0) {
            return _str;
        }
        var _tmp = '';
        for (var i = 0; i < ext; i++) {
            _tmp += '0';
        }
        _str = _str + '.' + _tmp;
    } else {
        if (_flt.length == ext) {
            return _str;
        }
        /* 找得到小数点，则截取 */
        if (_flt.length > ext) {
            _str = _str.substr(0, _str.length - (_flt.length - ext));
            if (ext == 0) {
                _str = _int;
            }
        } else {
            for (var i = 0; i < ext - _flt.length; i++) {
                _str += '0';
            }
        }
    }
    
    return _str;
}

/* 火狐下取本地全路径 */
function getFullPath(obj) {
    if (obj) {
        //ie
        if (window.navigator.userAgent.indexOf("MSIE") >= 1) {
            obj.select();
            if (window.navigator.userAgent.indexOf("MSIE") == 25) {
                obj.blur();
            }
            return document.selection.createRange().text;
        }
        //firefox
        else if (window.navigator.userAgent.indexOf("Firefox") >= 1) {
            if (obj.files) {
                //return obj.files.item(0).getAsDataURL();
                return window.URL.createObjectURL(obj.files.item(0));
            }
            return obj.value;
        }
        return obj.value;
    }
}

/* 转化JS跳转中的 ＆ */
function transform_char(str) {
    if (str.indexOf('&')) {
        str = str.replace(/&/g, "%26");
    }
    return str;
}

//图片垂直水平缩放裁切显示
(function ($) {
    $.fn.VMiddleImg = function (options) {
        var defaults = {
            "width": null,
            "height": null
        };
        var opts = $.extend({}, defaults, options);
        return $(this).each(function () {
            var $this = $(this);
            var objHeight = $this.height(); //图片高度
            var objWidth = $this.width(); //图片宽度
            var parentHeight = opts.height || $this.parent().height(); //图片父容器高度
            var parentWidth = opts.width || $this.parent().width(); //图片父容器宽度
            var ratio = objHeight / objWidth;
            if (objHeight > parentHeight && objWidth > parentWidth) {
                if (objHeight > objWidth) { //赋值宽高
                    $this.width(parentWidth);
                    $this.height(parentWidth * ratio);
                } else {
                    $this.height(parentHeight);
                    $this.width(parentHeight / ratio);
                }
                objHeight = $this.height(); //重新获取宽高
                objWidth = $this.width();
                if (objHeight > objWidth) {
                    $this.css("top", (parentHeight - objHeight) / 2);
                    //定义top属性
                } else {
                    //定义left属性
                    $this.css("left", (parentWidth - objWidth) / 2);
                }
            }
            else {
                if (objWidth > parentWidth) {
                    $this.css("left", (parentWidth - objWidth) / 2);
                }
                $this.css("top", (parentHeight - objHeight) / 2);
            }
        });
    };
})(jQuery);

function DrawImage(ImgD, FitWidth, FitHeight) {
    var image = new Image();
    image.src = ImgD.src;
    if (image.width > 0 && image.height > 0) {
        if (image.width / image.height >= FitWidth / FitHeight) {
            if (image.width > FitWidth) {
                ImgD.width = FitWidth;
                ImgD.height = (image.height * FitWidth) / image.width;
            }
            else {
                ImgD.width = image.width;
                ImgD.height = image.height;
            }
        }
        else {
            if (image.height > FitHeight) {
                ImgD.height = FitHeight;
                ImgD.width = (image.width * FitHeight) / image.height;
            }
            else {
                ImgD.width = image.width;
                ImgD.height = image.height;
            }
        }
    }
}

/**
 * 浮动DIV定时显示提示信息,如操作成功, 失败等
 * @param string tips (提示的内容)
 * @param int height 显示的信息距离浏览器顶部的高度
 * @param int time 显示的时间(按秒算), time > 0
 * @sample <a href="javascript:void(0);" onclick="showTips( '操作成功', 100, 3 );">点击</a>
 * @sample 上面代码表示点击后显示操作成功3秒钟, 距离顶部100px
 * @copyright ZhouHr 2010-08-27
 */
function showTips(tips, height, time) {
    var windowWidth = document.documentElement.clientWidth;
    var tipsDiv = '<div class="tipsClass">' + tips + '</div>';
    
    $('body').append(tipsDiv);
    $('div.tipsClass').css({
        'top': 200 + 'px',
        'left': (windowWidth / 2) - (tips.length * 13 / 2) + 'px',
        'position': 'fixed',
        'padding': '20px 50px',
        'background': '#EAF2FB',
        'font-size': 14 + 'px',
        'margin': '0 auto',
        'text-align': 'center',
        'width': 'auto',
        'color': '#333',
        'border': 'solid 1px #A8CAED',
        'opacity': '0.90',
        'z-index': '9999'
    }).show();
    setTimeout(function () {
        $('div.tipsClass').fadeOut().remove();
    }, (time * 1000));
}

function trim(str) {
    return (str + '').replace(/(\s+)$/g, '').replace(/^\s+/g, '');
}

//弹出框登录
function login_dialog() {
    CUR_DIALOG = ajax_form('login', '登录', SITEURL + '/index.php?act=login&inajax=1', 360, 1);
}

/* 显示Ajax表单 */
function ajax_form(id, title, url, width, model) {
    if (!width) width = 480;
    if (!model) model = 1;
    var d = DialogManager.create(id);
    d.setTitle(title);
    d.setContents('ajax', url);
    d.setWidth(width);
    d.show('center', model);
    return d;
}

//显示一个内容为自定义HTML内容的消息
function html_form(id, title, _html, width, model) {
    if (!width) width = 480;
    if (!model) model = 0;
    var d = DialogManager.create(id);
    d.setTitle(title);
    d.setContents(_html);
    d.setWidth(width);
    d.show('center', model);
    return d;
}

//收藏店铺js
function collect_store(fav_id, jstype, jsobj) {
    $.get('index.php?act=index&op=login', function (result) {
        if (result == '0') {
            login_dialog();
        } else {
            var url = 'index.php?act=member_favorites&op=favoritesstore';
            $.getJSON(url, {'fid': fav_id}, function (data) {
                if (data.done) {
                    showDialog(data.msg, 'succ', '', '', '', '', '', '', '', '', 2);
                    if (jstype == 'count') {
                        $('[nctype="' + jsobj + '"]').each(function () {
                            $(this).html(parseInt($(this).text()) + 1);
                        });
                    }
                    if (jstype == 'succ') {
                        $('[nctype="' + jsobj + '"]').each(function () {
                            $(this).html("收藏成功");
                        });
                    }
                    if (jstype == 'store') {
                        $('[nc_store="' + fav_id + '"]').each(function () {
                            $(this).before('<span class="goods-favorite" title="该店铺已收藏"><i class="have">&nbsp;</i></span>');
                            $(this).remove();
                        });
                    }
                }
                else {
                    showDialog(data.msg, 'notice');
                }
            });
        }
    });
}

//收藏商品js
function collect_goods(fav_id, jstype, jsobj) {
    $.get('index.php?act=index&op=login', function (result) {
        if (result == '0') {
            login_dialog();
        } else {
            var url = 'index.php?act=member_favorites&op=favoritesgoods';
            $.getJSON(url, {'fid': fav_id}, function (data) {
                if (data.done) {
                    showDialog(data.msg, 'succ', '', '', '', '', '', '', '', '', 2);
                    if (jstype == 'count') {
                        $('[nctype="' + jsobj + '"]').each(function () {
                            $(this).html(parseInt($(this).text()) + 1);
                        });
                    }
                    if (jstype == 'succ') {
                        $('[nctype="' + jsobj + '"]').each(function () {
                            $(this).html("收藏成功");
                        });
                    }
                }
                else {
                    showDialog(data.msg, 'notice');
                }
            });
        }
    });
}

//加载购物车信息
function load_cart_information() {
    $.getJSON(SITEURL + '/index.php?act=cart&op=ajax_load&callback=?', function (result) {
        var obj = $('.head-user-menu .my-cart');
        if (result) {
            var html = '';
            if (result.cart_goods_num > 0) {
                for (var i = 0; i < result['list'].length; i++) {
                    var goods = result['list'][i];
                    html += '<dl id="cart_item_' + goods['cart_id'] + '"><dt class="goods-name"><a href="' + goods['goods_url'] + '">' + goods['goods_name'] + '</a></dt>';
                    html += '<dd class="goods-thumb"><a href="' + goods['goods_url'] + '" title="' + goods['goods_name'] + '"><img src="' + goods['goods_image'] + '"></a></dd>';
                    html += '<dd class="goods-sales"></dd>';
                    html += '<dd class="goods-price"><em>&yen;' + goods['goods_price'] + '×' + goods['goods_num'] + '</dd>';
                    html += '<dd class="handle"><a href="javascript:void(0);" onClick="drop_topcart_item(' + goods['cart_id'] + ',' + goods['goods_id'] + ');">删除</a></dd>';
                    html += "</dl>";
                }
                obj.find('.incart-goods').html(html);
                obj.find('.incart-goods-box').perfectScrollbar('destroy');
                obj.find('.incart-goods-box').perfectScrollbar({suppressScrollX: true});
                html = "共<i>" + result.cart_goods_num + "</i>种商品&nbsp;&nbsp;总计金额：<em>&yen;" + result.cart_all_price + "</em>";
                obj.find('.total-price').html(html);
                if (obj.find('.addcart-goods-num').size() == 0) {
                    obj.append('<div class="addcart-goods-num">0</div>');
                }
                obj.find('.addcart-goods-num').html(result.cart_goods_num);
                $('#rtoobar_cart_count').html(result.cart_goods_num).show();
            } else {
                html = "<div class="
                no - order
                "><span>您的购物车中暂无商品，赶快选择心爱的商品吧！</span></div>";
                obj.find('.incart-goods').html(html);
                obj.find('.total-price').html('');
                $('.addcart-goods-num').remove();
                $('#rtoobar_cart_count').html('').hide();
                
            }
        }
    });
}

//头部删除购物车信息，登录前使用goods_id,登录后使用cart_id
function drop_topcart_item(cart_id, goods_id) {
    $.getJSON(SITEURL + '/index.php?act=cart&op=del&cart_id=' + cart_id + '&goods_id=' + goods_id + '&callback=?', function (result) {
        if (result.state) {
            var obj = $('.head-user-menu .my-cart');
            //删除成功
            if (result.quantity == 0) {
                html = "<div class="
                no - order
                "><span>您的购物车中暂无商品，赶快选择心爱的商品吧！</span></div>";
                obj.find('.incart-goods').html(html);
                obj.find('.total-price').html('');
                obj.find('.addcart-goods-num').remove();
                $('.cart-list').html('<li><dl><dd style="text-align: center; ">暂无商品</dd></dl></li>');
                $('div[ncType="rtoolbar_total_price"]').html("");
                $('#rtoobar_cart_count').html("").hide()
            } else {
                $('#cart_item_' + cart_id).remove();
                html = "共<i>" + result.quantity + "</i>种商品&nbsp;&nbsp;总计金额：<em>&yen;" + result.amount + "</em>";
                obj.find('.total-price').html(html);
                obj.find('.addcart-goods-num').html(result.quantity);
                obj.find('.incart-goods-box').perfectScrollbar('destroy');
                obj.find('.incart-goods-box').perfectScrollbar();
                $('div[ncType="rtoolbar_total_price"]').html("共计金额：<em class="+
                (goods - price) +
                ">&yen;" + result.amount + "</em>"
            )
                ;
                $("#rtoobar_cart_count").html(result.quantity);
                if ($("#rtoolbar_cartlist > ul").children().size() != result.quantity) {
                    $("#rtoolbar_cartlist").load("index.php?act=cart&op=ajax_load&type=html");
                    return
                }
            }
        } else {
            alert(result.msg);
        }
    });
}

//加载最近浏览的商品
function load_history_information() {
    $.getJSON(SITEURL + '/index.php?act=index&op=viewed_info', function (result) {
        var obj = $('.head-user-menu .my-mall');
        if (result['m_id'] > 0) {
            if (typeof result['consult'] !== 'undefined') obj.find('#member_consult').html(result['consult']);
            if (typeof result['consult'] !== 'undefined') obj.find('#member_voucher').html(result['voucher']);
        }
        var goods_id = 0;
        var text_append = '';
        var n = 0;
        if (typeof result['viewed_goods'] !== 'undefined') {
            for (goods_id in result['viewed_goods']) {
                var goods = result['viewed_goods'][goods_id];
                text_append += '<li class="goods-thumb"><a href="' + goods['url'] + '" title="' + goods['goods_name'] +
                    '" target="_blank"><img src="' + goods['goods_image'] + '" alt="' + goods['goods_name'] + '"></a>';
                text_append += '</li>';
                n++;
                if (n > 4) break;
            }
        }
        if (text_append == '') text_append = '<li class="no-goods">暂无商品</li>';
        ;
        obj.find('.browse-history ul').html(text_append);
    });
}

/*
 * 登录窗口
 *
 * 事件绑定调用范例
 * $("#btn_login").nc_login({
 *     nchash:'<?php echo getNchash();?>',
 *     formhash:'<?php echo Security::getTokenValue();?>',
 *     anchor:'cms_comment_flag'
 * });
 *
 * 直接调用范例
 * $.show_nc_login({
 *     nchash:'<?php echo getNchash();?>',
 *     formhash:'<?php echo Security::getTokenValue();?>',
 *     anchor:'cms_comment_flag'
 * });

 */

(function ($) {
    $.show_nc_login = function (options) {
        var settings = $.extend({}, {action: '/index.php?act=login&op=login&inajax=1', nchash: '', formhash: '', anchor: ''}, options);
        var login_dialog_html = $('<div class="quick-login"></div>');
        var ref_url = document.location.href;
        login_dialog_html.append('<form class="bg" method="post" id="ajax_login" action="' + APP_SITE_URL + settings.action + '"></form>').find('form')
            .append('<input type="hidden" value="ok" name="form_submit">')
            .append('<input type="hidden" value="' + settings.formhash + '" name="formhash">')
            .append('<input type="hidden" value="' + settings.nchash + '" name="nchash">')
            .append('<dl><dt>用户名</dt><dd><input type="text" name="user_name" autocomplete="off" class="text"></dd></dl>')
            .append('<dl><dt>密&nbsp;&nbsp;&nbsp;码</dt><dd><input type="password" autocomplete="off" name="password" class="text"></dd></dl>')
            .append('<dl><dt>验证码</dt><dd><input type="text" size="10" maxlength="4" class="text fl w60" name="captcha"><img border="0" onclick="this.src=\'' + APP_SITE_URL + '/index.php?act=seccode&amp;op=makecode&amp;nchash=' + settings.nchash + '&amp;t=\' + Math.random()" name="codeimage" title="看不清，换一张" src="' + APP_SITE_URL + '/index.php?act=seccode&amp;op=makecode&amp;nchash=' + settings.nchash + '" class="fl ml10"><span>不区分大小写</span></dd></dl>')
            .append('<ul><li>›&nbsp;如果您没有登录帐号，请先<a class="register" href="' + SHOP_SITE_URL + '/index.php?act=login&amp;op=register">注册会员</a>然后登录</li><li>›&nbsp;如果您<a class="forget" href="' + SHOP_SITE_URL + '/index.php?act=login&amp;op=forget_password">忘记密码</a>？，申请找回密码</li></ul>')
            .append('<div class="enter"><input type="submit" name="Submit" value="登&nbsp;录" class="submit"></div><input type="hidden" name="ref_url" value="' + ref_url + '">');
        
        login_dialog_html.find('input[type="submit"]').click(function () {
            ajaxpost('ajax_login', '', '', 'onerror');
        });
        html_form("form_dialog_login", "登录", login_dialog_html, 360);
    };
    $.fn.nc_login = function (options) {
        return this.each(function () {
            $(this).on('click', function () {
                $.show_nc_login(options);
                return false;
            });
        });
    };
    
})(jQuery);


/*
 * 为低版本IE添加placeholder效果
 *
 * 使用范例：
 * [html]
 * <input id="captcha" name="captcha" type="text" placeholder="验证码" value="" >
 * [javascrpt]
 * $("#captcha").nc_placeholder();
 *
 * 生效后提交表单时，placeholder的内容会被提交到服务器，提交前需要把值清空
 * 范例：
 * $('[data-placeholder="placeholder"]').val("");
 * $("#form").submit();
 *
 */
(function ($) {
    $.fn.nc_placeholder = function () {
        var isPlaceholder = 'placeholder' in document.createElement('input');
        return this.each(function () {
            if (!isPlaceholder) {
                $el = $(this);
                $el.focus(function () {
                    if ($el.attr("placeholder") === $el.val()) {
                        $el.val("");
                        $el.attr("data-placeholder", "");
                    }
                }).blur(function () {
                    if ($el.val() === "") {
                        $el.val($el.attr("placeholder"));
                        $el.attr("data-placeholder", "placeholder");
                    }
                }).blur();
            }
        });
    };
})(jQuery);

/*
 * 弹出窗口
 */
(function ($) {
    $.fn.nc_show_dialog = function (options) {
        
        var that = $(this);
        var settings = $.extend({}, {
            width: 480, title: '', close_callback: function () {
            }
        }, options);
        
        var init_dialog = function (title) {
            var _div = that;
            that.addClass("dialog_wrapper");
            that.wrapInner(function () {
                return '<div class="dialog_content">';
            });
            that.wrapInner(function () {
                return '<div class="dialog_body" style="position: relative;">';
            });
            that.find('.dialog_body').prepend('<h3 class="dialog_head" style="cursor: move;"><span class="dialog_title"><span class="dialog_title_icon">' + settings.title + '</span></span><span class="dialog_close_button iconfont icon-cuowu"></span></h3>');
            that.append('<div style="clear:both;"></div>');
            
            $(".dialog_close_button").click(function () {
                settings.close_callback();
                _div.hide();
            });
            
            that.draggable({handle: ".dialog_head"});
        };
        
        if (!$(this).hasClass("dialog_wrapper")) {
            init_dialog(settings.title);
        }
        settings.left = $(window).scrollLeft() + ($(window).width() - settings.width) / 2;
        settings.top = ($(window).height() - $(this).height()) / 2;
        $(this).attr("style", "display:none; z-index: 1000; position: fixed; width: " + settings.width + "px; left: " + settings.left + "px; top: " + settings.top + "px;");
        $(this).show();
        
    };
})(jQuery);

/**
 * Membership card
 *
 *
 * Example:
 *
 * HTML part
 * <a href="javascript" nctype="mcard" data-param="{'id':5}"></a>
 *
 * JAVASCRIPT part
 * <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/qtip/jquery.qtip.min.js"></script>
 * <link href="<?php echo RESOURCE_SITE_URL;?>/js/qtip/jquery.qtip.min.css" rel="stylesheet" type="text/css">
 * $('a[nctype="mcard"]').membershipCard();
 */
(function ($) {
    $.fn.membershipCard = function (options) {
        var defaults = {
            type: ''			// params  shop/circle/cms/micorshop
        };
        options = $.extend(defaults, options);
        return this.each(function () {
            var $this = $(this);
            var data_str = $(this).attr('data-param');
            eval('data_str = ' + data_str);
            var _uri = SITEURL + '/index.php?act=member_card&callback=?&uid=' + data_str.id + '&from=' + options.type;
            $this.qtip({
                content: {
                    text: 'Loading...',
                    ajax: {
                        url: _uri,
                        type: 'GET',
                        dataType: 'jsonp',
                        success: function (data) {
                            if (data) {
                                var _dl = $('<dl></dl>');
                                // sex
                                $('<dt class="member-id"></dt>').append('<i class="sex' + data.sex + '"></i>')
                                    .append('<a href="' + SHOP_SITE_URL + '/index.php?act=member_snshome&mid=' + data.id + '" target="_blank">' + data.name + '</a>' + (data.truename != '' ? '(' + data.truename + ')' : ''))
                                    .appendTo(_dl);
                                // avatar
                                $('<dd class="avatar"><a href="' + SHOP_SITE_URL + '/index.php?act=member_snshome&mid=' + data.id + '" target="_blank"><img src="' + data.avatar + '" /></a><dd>')
                                    .appendTo(_dl);
                                // info
                                var _info = '';
                                if (typeof connect !== 'undefined' && connect === 1 && data.follow != 2) {
                                    var class_html = 'chat_offline';
                                    var text_html = '离线';
                                    if (typeof user_list[data.id] !== 'undefined' && user_list[data.id]['online'] > 0) {
                                        class_html = 'chat_online';
                                        text_html = '在线';
                                    }
                                    _info += '<a class="chat ' + class_html + '" title="点击这里给我发消息" href="JavaScript:chat(' + data.id + ');">' + text_html + '</a>';
                                }
                                if (data.qq != '') {
                                    _info += '<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=' + data.qq + '&site=qq&menu=yes" title="QQ: ' + data.qq + '"><img border="0" src="http://wpa.qq.com/pa?p=2:' + data.qq + ':52" style=" vertical-align: middle;"/></a>';
                                }
                                if (data.ww != '') {
                                    _info += '<a target="_blank" href="http://amos.im.alisoft.com/msg.aw?v=2&amp;uid=' + data.ww + '&site=cntaobao&s=1&charset=' + _CHARSET + '" ><img border="0" src="http://amos.im.alisoft.com/online.aw?v=2&uid=' + data.ww + '&site=cntaobao&s=2&charset=' + _CHARSET + '" alt="点击这里给我发消息" style=" vertical-align: middle;"/></a>';
                                }
                                if (_info == '') {
                                    _info = '--';
                                }
                                var _ul = $('<ul></ul>').append('<li>城市：' + ((data.areainfo != null) ? data.areainfo : '--') + '</li>')
                                    .append('<li>生日：' + ((data.birthday != null) ? data.birthday : '--') + '</li>')
                                    .append('<li>联系：' + _info + '</li>').appendTo('<dd class="info"></dd>').parent().appendTo(_dl);
                                // ajax info
                                if (data.url != '') {
                                    $.getJSON(data.url + '/index.php?act=member_card&op=mcard_info&uid=' + data.id, function (d) {
                                        if (d) {
                                            eval('var msg = ' + options.type + '_function(d);');
                                            msg.appendTo(_dl);
                                        }
                                    });
                                    data.url = '';
                                }
                                
                                // bottom
                                var _bottom;
                                if (data.follow != 2) {
                                    _bottom = $('<div class="bottom"></div>');
                                    var _a;
                                    if (data.follow == 1) {
                                        $('<div class="follow-handle" nctype="follow-handle' + data.id + '" data-param="{\'mid\':' + data.id + '}"></div>')
                                            .append('<a href="javascript:void(0);" >已关注</a>')
                                            .append('<a href="javascript:void(0);" nctype="nofollow">取消关注</a>').find('a[nctype="nofollow"]').click(function () {
                                            onfollow($(this));
                                        }).end().appendTo(_bottom);
                                    } else {
                                        $('<div class="follow-handle" nctype="follow-handle' + data.id + '" data-param="{\'mid\':' + data.id + '}"></div>')
                                            .append('<a href="javascript:void(0);" nctype="follow">加关注</a>').find('a[nctype="follow"]').click(function () {
                                            follow($(this));
                                        }).end().appendTo(_bottom);
                                    }
                                    $('<div class="send-msg"> <a href="' + SHOP_SITE_URL + '/index.php?act=member_message&op=sendmsg&member_id=' + data.id + '" target="_blank"><i></i>站内信</a> </div>').appendTo(_bottom);
                                }
                                
                                var _content = $('<div class="member-card"></div>').append(_dl).append(_bottom);
                                this.set('content.text', ' ');
                                this.set('content.text', _content);
                            }
                        }
                    }
                },
                position: {
                    viewport: $(window)
                },
                hide: {
                    fixed: true,
                    delay: 300
                },
                style: 'qtip-wiki'
            });
        });
        
        function follow(o) {
            var data_str = o.parent().attr('data-param');
            eval("data_str = " + data_str);
            $.getJSON(SHOP_SITE_URL + '/index.php?act=member_snsfriend&op=addfollow&callback=?&mid=' + data_str.mid, function (data) {
                if (data) {
                    $('[nctype="follow-handle' + data_str.mid + '"]').html('<a href="javascript:void(0);" >已关注</a> <a href="javascript:void(0);" nctype="nofollow">取消关注</a>').find('a[nctype="nofollow"]').click(function () {
                        onfollow($(this));
                    });
                }
            });
        }
        
        function onfollow(o) {
            var data_str = o.parent().attr('data-param');
            eval("data_str = " + data_str);
            $.getJSON(SHOP_SITE_URL + '/index.php?act=member_snsfriend&op=delfollow&callback=?&mid=' + data_str.mid, function (data) {
                if (data) {
                    $('[nctype="follow-handle' + data_str.mid + '"]').html('<a href="javascript:void(0);" nctype="follow">加关注</a>').find('a[nctype="follow"]').click(function () {
                        follow($(this));
                    });
                }
            });
        }
        
        function shop_function(d) {
            return $('<dd class="ajax-info">买家信用：' + ((d.member_credit == 0) ? '暂无信用' : d.member_credit) + '</dd>');
        }
        
        function circle_function(d) {
            var rs = $('<dd class="ajax-info"></dd>');
            $.each(d, function (i, n) {
                rs.append('<div class="rank-div" title="' + n.circle_name + '圈等级' + n.cm_level + '，经验值' + n.cm_exp + '"><a href="' + CIRCLE_SITE_URL + '/index.php?act=group&c_id=' + n.circle_id + '" target="_blank">' + n.circle_name + '</a><i></i><em class="rank-em rank-' + n.cm_level + '">' + n.cm_level + '</em></div>');
            });
            return rs;
        }
        
        function microshop_function(d) {
            var rs = $('<dd class="ajax-info"></dd>');
            rs.append('<span class="ajax-info-microshop">随心看：' + d.goods_count + '</span>');
            rs.append('<span class="ajax-info-microshop">个人秀：' + d.personal_count + '</span>');
            return rs;
        }
    };
})(jQuery);

/*
 * 地址联动选择
 * input不为空时出现编辑按钮，点击按钮进行联动选择
 *
 * 使用范例：
 * [html]
 * <input id="region" name="region" type="hidden" value="" >
 * [javascrpt]
 * $("#region").nc_region();
 *
 * 默认从cache读取地区数据，如果需从数据库读取（如后台地区管理），则需设置定src参数
 * $("#region").nc_region({{src:'db'}});
 * 
 * 如需指定地区下拉显示层级，需指定show_deep参数，默认未限制
 * $("#region").nc_region({{show_deep:2}}); 这样最多只会显示2级联动
 * 
 * 系统分自动将已经选择的地区ID存放到ID依次为_area_1、_area_2、_area_3、_area_4、_area的input框中
 * _area存放选中的最后一级ID
 * 这些input框需要手动在模板中创建
 * 
 * 取得已选值
 * $('#region').val() ==> 河北 石家庄市 井陉矿区
 * $('#region').fetch('islast')  ==> true; 如果非最后一级，返回false
 * $('#region').fetch('area_id') ==> 1127
 * $('#region').fetch('area_ids') ==> 3 73 1127
 * $('#region').fetch('selected_deep') ==> 3 已选择分类的深度
 * $("#region").fetch('area_id_1') ==> 3
 * $("#region").fetch('area_id_2') ==> 73
 * $("#region").fetch('area_id_3') ==> 1127
 * $("#region").fetch('area_id_4') ==> ''
 */

(function ($) {
    $.fn.nc_region = function (options) {
        var $region = $(this);
        var settings = $.extend({}, {
            area_id: 0,
            region_span_class: "_region_value",
            src: "cache",
            show_deep: 0,
            btn_style_html: "",
            tip_type: ""
        }, options);
        settings.islast = false;
        settings.selected_deep = 0;
        settings.last_text = "";
        this.each(function () {
            var $inputArea = $(this);
            if ($inputArea.val() === "") {
                initArea($inputArea)
            } else {
                var $region_span = $('<span id="_area_span" class="' + settings.region_span_class + '">' + $inputArea.val() + "</span>");
                var $region_btn = $('<input type="button" class="input-btn" ' + settings.btn_style_html + ' value="编辑" />');
                $inputArea.after($region_span);
                $region_span.after($region_btn);
                $region_btn.on("click", function () {
                    $region_span.remove();
                    $region_btn.remove();
                    initArea($inputArea)
                });
                settings.islast = true
            }
            this.settings = settings;
            if ($inputArea.val() && /^\d+$/.test($inputArea.val())) {
                $.getJSON(SITEURL + "/index.php?act=index&op=json_area_show&area_id=" + $inputArea.val() + "&callback=?", function (data) {
                    $("#_area_span").html(data.text == null ? "无" : data.text)
                })
            }
        });
        
        function initArea($inputArea) {
            settings.$area = $("<select></select>");
            $inputArea.before(settings.$area);
            loadAreaArray(function () {
                loadArea(settings.$area, settings.area_id)
            })
        }
        
        function loadArea($area, area_id) {
            if ($area && nc_a[area_id].length > 0) {
                var areas = [];
                areas = nc_a[area_id];
                if (settings.tip_type && settings.last_text != "") {
                    $area.append("<option value=''>" + settings.last_text + "(*)</option>" )
                } else {
                    $area.append("<option value=' '>-请选择-</option>" )
                }
                for (i = 0; i < areas.length; i++) {
                    $area.append("<option value=''" + areas[i][0] + ">" + areas[i][1] + "</option>")
                }
                settings.islast = false
            }
            $area.on("change", function () {
                var region_value = "",
                    area_ids = [],
                    selected_deep = 1;
                $(this).nextAll("select").remove();
                $region.parent().find("select").each(function () {
                    if ($(this).find("option:selected").val() != "") {
                        region_value += $(this).find("option:selected").text() + " ";
                        area_ids.push($(this).find("option:selected").val())
                    }
                });
                settings.selected_deep = area_ids.length;
                settings.area_ids = area_ids.join(" ");
                $region.val(region_value);
                settings.area_id_1 = area_ids[0] ? area_ids[0] : "";
                settings.area_id_2 = area_ids[1] ? area_ids[1] : "";
                settings.area_id_3 = area_ids[2] ? area_ids[2] : "";
                settings.area_id_4 = area_ids[3] ? area_ids[3] : "";
                settings.last_text = $region.prevAll("select").find("option:selected").last().text();
                var area_id = settings.area_id = $(this).val();
                if ($('#_area_1').length > 0) $("#_area_1").val(settings.area_id_1);
                if ($('#_area_2').length > 0) $("#_area_2").val(settings.area_id_2);
                if ($('#_area_3').length > 0) $("#_area_3").val(settings.area_id_3);
                if ($('#_area_4').length > 0) $("#_area_4").val(settings.area_id_4);
                if ($('#_area').length > 0) $("#_area").val(settings.area_id);
                if ($('#_areas').length > 0) $("#_areas").val(settings.area_ids);
                if (settings.show_deep > 0 && $region.prevAll("select").size() == settings.show_deep) {
                    settings.islast = true;
                    if (typeof settings.last_click == 'function') {
                        settings.last_click(area_id);
                    }
                    return
                }
                if (area_id > 0) {
                    if (nc_a[area_id] && nc_a[area_id].length > 0) {
                        var $newArea = $("<select></select>");
                        $(this).after($newArea);
                        loadArea($newArea, area_id);
                        settings.islast = false
                    } else {
                        settings.islast = true;
                        if (typeof settings.last_click == 'function') {
                            settings.last_click(area_id);
                        }
                    }
                } else {
                    settings.islast = false
                }
                if ($('#islast').length > 0) $("#islast").val("");
            })
        }
        
        function loadAreaArray(callback) {
            if (typeof nc_a === "undefined") {
                $.getJSON(SITEURL + "/index.php?act=index&op=json_area&src=" + settings.src + "&callback=?", function (data) {
                    nc_a = data;
                    callback()
                })
            } else {
                callback()
            }
        }
        
        if (typeof jQuery.validator != 'undefined') {
            jQuery.validator.addMethod("checklast", function (value, element) {
                return $(element).fetch('islast');
            }, "请将地区选择完整");
        }
    };
    $.fn.fetch = function (k) {
        var p;
        this.each(function () {
            if (this.settings) {
                p = eval("this.settings." + k);
                return false
            }
        });
        return p
    }
})(jQuery);

/* 加入购物车 */
function addcart(goods_id, quantity, callbackfunc) {
    var url = 'index.php?act=cart&op=add';
    quantity = parseInt(quantity);
    $.getJSON(url, {'goods_id': goods_id, 'quantity': quantity}, function (data) {
        if (data != null) {
            if (data.state) {
                if (callbackfunc) {
                    eval(callbackfunc + "(data)");
                }
                // 头部加载购物车信息
                load_cart_information();
                $("#rtoolbar_cartlist").load(SHOP_SITE_URL + '/index.php?act=cart&op=ajax_load&type=html');
            } else {
                alert(data.msg);
            }
        }
    });
}

function setCookie(name, value, days) {
    var exp = new Date();
    exp.setTime(exp.getTime() + days * 24 * 60 * 60 * 1000);
    var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
}

function getCookie(name) {
    var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
    if (arr != null) {
        return unescape(arr[2]);
        return null;
    }
}

function delCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = getCookie(name);
    if (cval != null) {
        document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
    }
}

function addCookie(name, value, expireHours) {
    var cookieString = name + "=" + escape(value) + "; path=/";
    //判断是否设置过期时间
    if (expireHours > 0) {
        var date = new Date();
        date.setTime(date.getTime() + expireHours * 3600 * 1000);
        cookieString = cookieString + ";expires=" + date.toGMTString();
    }
    document.cookie = cookieString;
}

function getCookie(name) {
    var strcookie = document.cookie;
    var arrcookie = strcookie.split("; ");
    for (var i = 0; i < arrcookie.length; i++) {
        var arr = arrcookie[i].split("=");
        if (arr[0] == name) return unescape(arr[1]);
    }
    return null;
}

// 登陆后更新购物车
function updateCookieCart(key) {
    var cartlist = decodeURIComponent(getCookie('goods_cart'));
    if (cartlist) {
        $.ajax({
            type: 'post',
            url: SITE_URL + '?ctl=Buyer_Cart&met=addCartRow&typ=json',
            data: {cartlist: cartlist},
            dataType: 'json',
            async: false
        });
        delCookie('goods_cart');
    }
}

function loadSeccode() {
    $("#register_sms_captcha").val("");
    $.ajax({
        type: "get",
        url: "index.php?act=seccode&op=makecodekey",
        async: false,
        dataType: "json",
        success: function (e) {
            $("#register_sms_captcha").val(e.datas.register_sms_captcha)
        }
    });
    $("#sms_codeimage").attr("src", "index.php?act=seccode&op=makecode&k=" + $("#register_sms_captcha").val() + "&t=" + Math.random())
}

/** base.js **/
$(document).ready(function(){
    var nice_scroll_row = ['.sav_goods', '.cart_con', '.item_cons', '.history_goods', '.news_contents', '.other_voucher', '.contrast_goods'];

    $.each($.unique(nice_scroll_row), function(index, data)
    {
        $scroll_obj= $(data);

        if ($scroll_obj.length > 0)
        {
            $scroll_obj.niceScroll({
                cursorcolor: "#666",
                cursoropacitymax: 1,
                touchbehavior: false,
                cursorwidth: "3px",
                cursorborder: "0",
                cursorborderradius: "3px",
                autohidemode: false,
                nativeparentscrolling:true
            });
        }
    });

    $(".all_check").click(function(){
    	var isChecked = $(this).prop("checked");
    	$(".cart_contents input").prop("checked", isChecked);
    });
    $(".cart_contents_head input").click(function(){
    	var isChecked1 = $(this).prop("checked");
      	$(this).parent().parent().siblings().find("input").prop("checked", isChecked1);
    })

    //品牌页右侧排行榜效果
    $(".bFt-list li").hover(function(){
        $(this).addClass("bFlil-expand");
    },function(){
         $(this).removeClass("bFlil-expand");
    })

    //遍历banner图背景色
    var arr2=["#5bacf7","#b96fe4","#f2a8a7","#b96fe4"];
    $.each($(".banimg li"),function(i,obj){
         if(i>=4){
            var thisindexs=$(this).index();
          i=thisindexs-Math.floor(thisindexs/5)*5;
        }
        $(this).css("backgroundColor",arr2[i])
       
    })


    $('#site_search').click(function (e){
        var $siteKeyWords = $("#site_keywords");
        if ($siteKeyWords.val() == "") {
            $siteKeyWords.val($siteKeyWords.attr('placeholder'));
        }
       $("#form_search").submit();
    });

    $("#form_search").on("submit", function () {
        var $siteKeyWords = $("#site_keywords");
        if ($siteKeyWords.val() == "") {
            $siteKeyWords.val($siteKeyWords.attr('placeholder'));
        }
    });

    $(".search-types > li:eq(1)").on("click", function () {
        var $site_keywords = $("#site_keywords");
        $site_keywords.val("");
        $site_keywords.parent().find("label").text("");
    });


    //搜索商品
    $("#site_keywords")
        .focus(function (){
            $(this).parent().addClass("active");
        })
        .blur(function () {
            if (this.value == "") {
                $(this).parent().find('label').show();
                $(this).parent().removeClass("active");
            }
        })
        .keydown(function (e) {
            var keyCode, val;
            if(window.event) {// IE
                keyCode = e.keyCode
            } else if(e.which) { // Netscape/Firefox/Opera
                keyCode = e.which
            }

            val = String.fromCharCode(keyCode);
            if (this.value == '' && val != "") {
                $(this).parent().find('label').hide();
            }
        });
});

$(function () {
    if (screen.width <= 1366) {
       $(".bbuilder_code").css({"zIndex":"999"});
    }

});


