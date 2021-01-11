<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>开放平台PHP SDK && Demo - Powered by simba.pro </title>
    </head>
    <body>
        <p>本DEMO演示了SIMBA开放平台PHP SDK的授权及接口调用方法，开发者可以在此基础上进行灵活多样的应用开发。</p>
        <p>src目录为开放平台使用的SDK文件，实际应用中，开发者可以将其迁移至所使用框架的类库目录中使用。</p>
        <hr />
        <p>一、授权</p>
        <p>用户在使用接口之前，需要先进行授权操作。</p>
        <p><a href="oauth.php" target="_blank">点击进入授权页面</a></p>
        <p>二、快速授权</p>
        <p>快速授权是simba内置应用的授权方式。应用需要成为simba内置应用，才能获取到授权参数。相关用法，可以参考/quick_oauth.php例子代码</p>
        <p>三、接口调用【例子】</p>
        <p>
            <ul>
                <Li>用户API
                    <ul>
                        <li><a href='demo.php?m=user&c=getUserInfo' target="_blank">获取当前用户信息</a></li>
                        <li><a href='demo.php?m=user&c=editUserInfo' target = '_blank'>修改个人信息</a></li>
                        <li><a href='demo.php?m=user&c=getUserPublic' target = '_blank'>获取用户公开信息</a></li>
                        <li><a href='demo.php?m=user&c=getUserPublicBatch' target = '_blank'>批量获取用户公开信息</a></li>
                    </ul>
                </li>
                <Li>群聊API
                    <ul>
                        <li><a href='demo.php?m=group&c=member_remove' target = '_blank'>移除群成员</a></li>
                        <li><a href='demo.php?m=group&c=groupMemberInvite' target = '_blank'>邀请成员加入群聊</a></li>
                        <li><a href='demo.php?m=group&c=getGroupInfo' target = '_blank'>获取群聊信息</a></li>
                        <li><a href='demo.php?m=group&c=getUserGroups' target = '_blank'>获取群成员列表</a></li>
                    </ul>
                </li>
                <Li>组织API
                    <ul>
                        <li><a href='demo.php?m=department&c=enterprise_department_info' target = '_blank'>获取部门信息</a></li>
                        <li><a href='demo.php?m=department&c=enterprise_department_member_delete' target = '_blank'>移除部门成员</a></li>
                        <li><a href='demo.php?m=department&c=enterprise_info' target = '_blank'>获取组织信息</a></li>
                        <li><a href='demo.php?m=department&c=enterprise_department_member_join' target = '_blank'>成员加入部门</a></li>
                        <li><a href='demo.php?m=department&c=enterprise_department_member_list' target = '_blank'>获取部门成员列表</a></li>
                        <li><a href='demo.php?m=department&c=enterprise_department_delete' target = '_blank'>删除部门</a></li>
                        <li><a href='demo.php?m=department&c=enterprise_buddy_page' target = '_blank'>获取组织成员列表</a></li>
                        <li><a href='demo.php?m=department&c=enterprise_buddy_leave' target = '_blank'>离职用户</a></li>
                    </ul>                        
                </li>
                <Li>消息API
                    <ul>
                        <li><a href='demo.php?m=notice&c=notice_send' target = '_blank'>业务消息通知</a></li>
                    </ul>
                </li>
            </ul>
        </p>
        <hr />
        <p>开发文档手册，请点击 <a href="http://47.94.37.132:9042/html/?tdsourcetag=s_pctim_aiomsg" target="_blank">开发手册</a> 。</p>
        <p>开放平台调测工具，请点击 <a href="http://192.168.3.228:9012/devtool.html" target="_blank">调测工具</a> 。</p>
    </body>
</html>