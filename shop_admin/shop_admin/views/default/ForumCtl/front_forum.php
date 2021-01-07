<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
    <link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?=$this->view->css?>/add.css">
    <link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
    <link rel="stylesheet" href="http://at.alicdn.com/t/font_136526_rc9m7tdbk1d.css">
    <script type="text/javascript" src="<?=$this->view->js_com?>/vue.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js_com?>/axios.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js_com?>/qs.min.js" charset="utf-8"></script>
    </head>
    <body class="<?=$skin?>">
    <div class="wrapper page" id="indexModule" >
        <div class="fixed-bar">
            <div class="item-title">
                <div class="subject">
                    <h3><?= __('首页版块'); ?></h3>
                    <h5><?= __('商城首页版块及编辑'); ?></h5>
                </div>
                <ul class="tab-base nc-row">
                    <?php
                    $data_theme = $this->getUrl('Config', 'siteTheme', 'json', null, array('config_type'=>array('site')));

                    $theme_id = $data_theme['theme_id']['config_value'];

                    foreach ($data_theme['theme_row'] as $k => $theme_row)
                    {
                        if ($theme_id == $theme_row['name'])
                        {
                            $config = $theme_row['config'];
                            break;
                        }
                    }
                    ?>
                    <?php if (isset($config['index_tpl']) && $config['index_tpl']):?>
                        <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Floor_Adpage&met=adpage"><span><?= __('首页模板'); ?></span></a></li>
                    <?php endif;?>
                    <?php if (isset($config['index_slider']) && $config['index_slider']):?>
                        <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Config&met=index_slider&config_type%5B%5D=index_slider"><span><?= __('首页幻灯片'); ?></span></a></li>
                    <?php endif;?>
                    <?php if (isset($config['index_slider_img']) && $config['index_slider_img']):?>
                        <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Config&met=index_liandong&config_type%5B%5D=index_liandong"><span><?= __('首页联动小图'); ?></span></a></li>
                    <?php endif;?>
                    <li><a class="current"><span><?= __('首页版块'); ?></span></a></li>
                </ul>
            </div>
        </div>



        <!--操作说明 -->
        <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
            <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
                <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
                <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
            <ul>
                <li><?= __('首页最多展示排序最前的四个版块'); ?></li>
            </ul>
        </div>
        <div class="mod-toolbar-top cf">
            <div class="left" style="float: left;">
            </div>
            <div class="fr">
                <a href="#" class="ui-btn ui-btn-sp mrb"  v-on:click="moduleAdd()" id="btn-add"><?= __('新增'); ?><i class="iconfont icon-btn03"></i></a>
                <a class="ui-btn" id="btn-refresh"><?= __('刷新'); ?><i class="iconfont icon-btn01"></i></a>
            </div>
        </div>

        <!-- <div class="grid-wrap">
            <table id="grid">
            </table>
            <div id="page"></div>
        </div> -->
        <div class="ui-state-default ui-jqgrid-hdiv ui-corner-top" >
            <table cellspacing="0" cellpadding="0" class="sale-table">
                <thead>
                <tr>
                    <th class="activity"><?= __('首页顺序'); ?></th>
                    <th class="store"><?= __('版块名称'); ?></th>
                    <th class="img"><?= __('启用'); ?></th>
                    <th class="operate"><?= __('操作'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item,index) in indexList" :data-order="item.forum_order" :data-id="item.forum_id" :data-style="item.forum_style">
                    <td><em class="btn-turn" v-on:click="moduleUp(index)"><i class="iconfont icon-up"></i></em><em class="btn-turn" v-on:click="moduleDown(index)"><i class="iconfont icon-down"></i></em></td>
                    <td><span class="module-name one-overflow">{{item.forum_name}}</span></td>
                    <td>
                        <div class="btn-checkbox">
                            <input type="checkbox" :checked="item.forum_state==1"  v-on:click="inputChecked(index,$event)">
                            <label></label>
                        </div>
                    </td>
                    <td><button class="btn-addgoods" v-on:click="moduleEdit(item,item.forum_style)"><?= __('编辑'); ?></button><button class="btn-addgoods" v-on:click="styleEdit(item)"><?= __('修改'); ?></button><button class="btn-addgoods" v-on:click="delEdit(item.forum_id,index)"><?= __('删除'); ?></button></td>
                </tr>

                </tbody>
            </table>
        </div>




    </div>
    <script>
        // var axiosAjax = axios.create({
        //     baseURL:BASE_URL,
        //     transformRequest: [function (data) { // <?= __('转换数据'); ?>
        //         data = Qs.stringify(data); // <?= __('通过'); ?>Qs.stringify<?= __('转换为表单查询参数'); ?>
        //         return data;
        //     }],
        //     headers:{
        //         'Content-Type':'application/x-www-form-urlencoded'
        //     }
        // });
        var vm=new Vue({
            el:"#indexModule",
            data:{
                tit:"",
                indexList:[]

            },
            mounted:function(){
                var that=this;
                axios.get(BASE_URL+"/index.php?ctl=Forum&met=front&typ=json").then(function(res){
                    that.indexList=res.data.data.items;
                })
            },
            methods:{
                //上移

                moduleUp:function(idx){
                    var that=this;
                    // '在改变数组顺序之前，根据idx获取当前点击的id和order
                    var clickId=that.indexList[idx].forum_id;
                    var clickOrder=this.indexList[idx].forum_order;
                    if(idx-1>=0){
                        var prevOrder=this.indexList[idx-1].forum_order;
                        var previd = this.indexList[idx-1].forum_id;
                        that.indexList[idx-1].forum_order= this.indexList[idx].forum_order;
                        that.indexList[idx].forum_order=prevOrder;
                        //console.log(that.indexList[idx-1],that.indexList[idx]);
                        var prev=this.indexList[idx-1];
                        Vue.set(this.indexList,idx-1,this.indexList[idx]);
                        Vue.set(this.indexList,idx,prev);
                        axios.post(BASE_URL+"/index.php?ctl=Forum&met=setForumOrder&typ=json",Qs.stringify({
                            order_forum1:{id:clickId,order:prevOrder},
                            order_forum2:{id:previd,order:clickOrder}
                        },{ indices: false })).then(function(res){
                            console.log("success");
                        }).catch(function(res){
                            console.log("fail");
                        })
                    }


                },
                //下移

                moduleDown:function(idx){
                    var that=this;
                    if(idx+1<=this.indexList.length-1){
                        // <?= __('在改变数组顺序之前，根据'); ?>idx<?= __('获取当前点击的'); ?>id<?= __('和'); ?>order
                        var clickId=this.indexList[idx].forum_id;
                        var clickOrder=this.indexList[idx].forum_order;
                        // <?= __('本地改变顺序'); ?>order
                        var nextOrder=this.indexList[idx+1].forum_order;
                        var nextid = this.indexList[idx+1].forum_id;
                        that.indexList[idx+1].forum_order= this.indexList[idx].forum_order;
                        that.indexList[idx].forum_order=nextOrder;

                        var next=this.indexList[idx+1];
                        Vue.set(this.indexList,idx+1,this.indexList[idx]);
                        Vue.set(this.indexList,idx,next);
                        axios.post(BASE_URL+"/index.php?ctl=Forum&met=setForumOrder&typ=json",Qs.stringify({
                            order_forum1:{id:clickId,order:nextOrder},
                            order_forum2:{id:nextid,order:clickOrder}
                        },{ indices: false })).then(function(res){
                            console.log("success");
                        }).catch(function(res){
                            console.log("fail");
                        })
                    }


                },
                // 切换启用
                inputChecked:function(idx,ev){
                    var that=this;
                    var check=ev.target.checked;
                    if(check==true){
                        that.indexList[idx].forum_state=1;
                    }else{
                        that.indexList[idx].forum_state=2;
                    }
                    axios.post(BASE_URL+'/index.php?ctl=Forum&met=editForum&typ=json',Qs.stringify({
                        "id":that.indexList[idx].forum_id,
                        "forum_state":that.indexList[idx].forum_state
                    }),{
                        headers:{
                            "content-type": "application/x-www-form-urlencoded"
                        }
                    })
                        .then(function (response) {
                            console.log("<?= __('操作成功'); ?>");
                        })
                        .catch(function (error) {
                            console.log("<?= __('操作失败'); ?>");
                        });

                },
                //模块编辑
                moduleEdit:function(ids,style){
                    var that=this;
                    var Url="";
                    switch(style){
                        case "1":
                            Url='url:' + SITE_URL + '?ctl=Front&met=style1';
                            that.tit="<?= __('活动版块A'); ?>"
                            break;
                        case "2":
                            Url='url:' + SITE_URL + '?ctl=Front&met=style2';
                            that.tit="<?= __('活动版块B'); ?>"
                            break;
                    }
                    $.dialog({
                        title:that.tit,
                        content:Url,
                        data: ids,
                        width:800,
                        height: 500,
                        max: false,
                        min: false,
                        cache: false,
                        lock: true
                    });
                },

                //修改样式

                styleEdit:function(item){
                    $.dialog({
                        title:"<?= __('修改样式'); ?>",
                        content:'url:' + SITE_URL + '?ctl=Front&met=manage',
                        data: {item,callback: function (){ window.location.reload(); }},
                        width:800,
                        height: 500,
                        max: false,
                        min: false,
                        cache: false,
                        lock: true
                    });
                },
                // 删除模块
                delEdit:function(id,index){
                    var that=this;
                    $.dialog.confirm("确定删除吗？", function() {
                        that.indexList.splice(index,1);
                        axios.post(BASE_URL+"/index.php?ctl=Forum&met=delFrontForum&typ=json",Qs.stringify({
                            id:id
                        })).then(function(){
                            console.log("<?= __('删除成功'); ?>");
                        }).catch(function(){
                            console.log("<?= __('删除失败'); ?>");
                        })
                      
                    });
                },
                //模块新增

                moduleAdd:function(){
                    $.dialog({
                        title:'<?= __('新增'); ?>',
                        dialogClass:'dialogFrame',
                        content: 'url:' + SITE_URL + '?ctl=Front&met=manage',
                        data: { callback: function (){ window.location.reload(); } },
                        width:800,
                        height:400,
                        max: false,
                        min: false,
                        cache: false,
                        lock: true
                    });
                }
            }
        })
    </script>
    <script type="text/javascript" src="<?=$this->view->js?>/controllers/forum/forum_list.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>