<script type="text/javascript" src="../../js/zepto.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
<?php
  if ($_GET['qr']) {
        setcookie('is_app_guest', 1, time() + 86400 * 366);
        $_COOKIE['is_app_guest'] = 1;
    }
//$footer_menu = [
//	'首页'=>'/index.html',
//	'分类'=>'/tmpl/product_first_categroy.html',
////	'资讯'=>'/tmpl/information_news_list.html',
//    '发现' => '/tmpl/explore_list.html',
////    '发布' => '/tmpl/explore.html',
//    '购物车'=>'/tmpl/cart_list.html',
//	'我的'=>'/tmpl/member/member.html',
//];
?>
    <div class="footer bort1 " id="footer-template-bort1">

    </div>

<script id="footer-template" type="text/template">
     <ul class="clearfix 4444444">
        <% for(var i = 0;i<data.length;i++){ %>
         <% if (data[i].active == 1) {%>
         <li>
         <% } else { %>
         <li class="active">
             <% } %>
             <a href="<%=data[i].url%>"

                    <% if (data[i].onclick) {%>
                        onclick="<%=data[i].onclick%>"
                    <% } %>
					<% if (data[i].classs) {%>
                        class="<%=data[i].classs%>"
                    <% } %>
                >
				 <% if (data[i].active == 1) {%>
                 <i class="iconfont <%=data[i].type%>" 
				<% } %>
				<% if (data[i].active != 1) {%>
				 <i class="iconfont <%=data[i].type_active%>" 
				<% } %>
                    ></i>
                 <h3><%=data[i].name%></h3>
             </a>
             <% if(data[i].type == 'footer-find' && data[i].sum > 0) {%>
             <b class="active"></b>
             <% } %>
         </li>
        <% } %>
     </ul>
 </script>

