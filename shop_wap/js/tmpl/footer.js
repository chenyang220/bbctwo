$(function () {
  if (getQueryString('key') != '') {
    var key = getQueryString('key');
    var username = getQueryString('username');
    addCookie('key', key);
    addCookie('username', username);
  }
  else {
    var key = getCookie('key');
  }
  configKey(key);
  var html = '<div class="nctouch-footer-wrap posr">'
      + '<div class="nav-text">';
  var navtext = '';
  if (key) {
    html += navtext = '<a href="' + WapSiteUrl + '/tmpl/member/member.html">'+__("我的商城")+'</a>'
        + '<a id="logoutbtn" href="javascript:void(0);">'+__("注销")+'</a>'
        + '<a href="' + WapSiteUrl + '/tmpl/member/member_feedback.html">'+__("反馈")+'</a>';
    
  }
  else {
    html += navtext = '<a class="logbtn"  href="javascript:void(0);">'+__("登录")+'</a>'
        + '<a id="regbtn" href="javascript:void(0);">'+__("注册")+'</a>'
        + '<a href="' + WapSiteUrl + '/tmpl/member/login.html">'+__("反馈")+'</a>';
  }
  
  if (typeof copyright == 'undefined') {
    copyright = '';
  }

  var key = getCookie('key');
  
  $('#footer .nav-text').html(navtext);
  
  $.getJSON(SiteUrl + '/index.php?ctl=Api_Wap&met=version&typ=json', function (r) {
    
    html += '<a href="javascript:void(0);" class="gotop">'+__("返回顶部")+'</a>'
        + '</div>'
        + '<div class="nav-pic">'
        + '</div>'
        + '<div class="copyright">'
        + r.data.copyright
        + '</div>'
        + '<div class="copyright">'
        + r.data.icp_number
        + '</div>'
        + '<div class="copyright">'
        + r.data.statistics_code
        + '</div>';
    $.post(ShopWapUrl + "/cache.php", {html: html}, function () {
    });
  });
  $(document).on('click', '#regbtn', function () {
    callback = WapSiteUrl + '/tmpl/member/member.html';
    login_url = UCenterApiUrl + '?ctl=Login&met=regist&typ=e';
    callback = ApiUrl + '?ctl=Login&met=check&typ=e&redirect=' + encodeURIComponent(callback);
    login_url = login_url + '&from=wap&callback=' + encodeURIComponent(callback);
    window.location.href = login_url;
  });
  
  $(document).on('click', '.logbtn', function () {
    var shop_id_wap = getCookie('SHOP_ID_WAP');
    // wap端我的商城登录用户，成功后返回商城页
    callback = WapSiteUrl + '/tmpl/member/member.html';
    // callback = WapSiteUrl;
    login_url = UCenterApiUrl + '?ctl=Login&met=index&typ=e';
    callback = ApiUrl + '?ctl=Login&met=check&typ=e&redirect=' + encodeURIComponent(callback);
    login_url = login_url + '&from=wap&shop_id_wap='+shop_id_wap+'&callback=' + encodeURIComponent(callback);
    window.location.href = login_url;
  });
  
  $(document).on('click', '#logoutbtn', function () {
    var username = getCookie('username');
    var key = getCookie('key');
    var client = 'wap';
    
    login_url = UCenterApiUrl + '?ctl=Login&met=logout&typ=e';
    var para = '';
    if (getCookie('is_app_guest')) {
      var para = '&qr=1';
      addCookie('is_app_guest', 1);
    }
    callback = WapSiteUrl + '?redirect=' + encodeURIComponent(WapSiteUrl) + para;
    login_url = login_url + '&from=wap&callback=' + encodeURIComponent(callback);
    window.location.href = login_url;
    delCookie('username');
    delCookie('user_account');
    delCookie('id');
    delCookie('key');
  });

});
function configKey(key) {

    var style = getQueryString("style");
    var mb = getQueryString("mb");
    var shop_id_wap = getCookie('SHOP_ID_WAP');
    var shop_id = getQueryString('shop_id');
    if (key) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Webconfig&met=findAuthor&typ=json&shop_id_wap="+shop_id_wap +"&mb=" + mb + "&style=" + style  + "&shop_id=" + shop_id,
            data: {k: key, u:  getCookie("id")},
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {

                    for(var i=0; i<res.data.length;i++){
                        if(menu_active(res.data[i].url)){
                            res.data[i].active = 1;
                        } else {
                            res.data[i].active = 2;
                        }

                    }
                    var   footer   = $("#footer-template").html();
                    if (footer != null) {
                        var footer_template = template.render("footer-template", res);
                        $("#footer-template-bort1").html(footer_template);
                    }
                }
            }
        });
    } else {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Index&met=findAuthor&typ=json&shop_id_wap="+shop_id_wap +"&mb=" + mb + "&style=" + style  + "&shop_id=" + shop_id,
            data: {k: key},
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    for(var i=0; i<res.data.length;i++){
                        if(menu_active(res.data[i].url)){
                            res.data[i].active = 1;
                        } else {
                            res.data[i].active = 2;
                        }
                    }
                    var   footer   = $("#footer-template").html();
                    if (footer != null) {
                        var footer_template = template.render("footer-template", res);
                        $("#footer-template-bort1").html(footer_template);
                    }
                }
            }
        });
    }

}

function menu_active($name)
{
    var test = window.location.pathname;
    var index = test .lastIndexOf("\/");
    str_test  = test .substring(index + 1, test .length);
	if($name.indexOf("?") =="-1"){
		var index_name = $name .lastIndexOf("\/");
		str_name  = $name .substring(index_name + 1, $name .length);
	}else{
		var ar=$name.split("?");
		str_name  = ar[0];
	}
    if ($name == '/index.html' && test == '/') {
        return false;
    }
    //if (test.indexOf($name) != false) {
    //    return true;
    //}
    if (str_test == str_name) {
        return false;
    }
    return true;
}