$(function(){var e=getCookie("key");if(!e){window.location.href=WapSiteUrl+"/tmpl/member/login.html";return}$.getJSON(ApiUrl+"/index.php?act=member_index&op=my_asset",{k:e,u:getCookie('id')},function(e){checkLogin(e.login);$("#predepoit").html(e.data.predepoit+" 元");$("#rcb").html(e.data.available_rc_balance+" 元");$("#voucher").html(e.data.voucher+" 张");$("#redpacket").html(e.data.redpacket+" 个");$("#point").html(e.data.point+" 分")})});