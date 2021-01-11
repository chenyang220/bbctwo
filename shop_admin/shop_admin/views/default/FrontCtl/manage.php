<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="http://at.alicdn.com/t/font_136526_rc9m7tdbk1d.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/vue.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/axios.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/qs.min.js" charset="utf-8"></script>
<div class="sale-box" id="saleBox">
    <h4 class="sale-title"><b class="icon-require">*</b><?= __('版块名称：'); ?></h4>
    <div class="relative mb20">
        <input class="sale-tit-input" type="text" v-model="activeTitle"><em class="iblock ml10">该版块名称不在前台版块显示，仅供版块列表标识</em>
    </div>
    <h4 class="sale-title"><b class="icon-require">*</b><?= __('版块样式：'); ?></h4>
    <div class="clearfix">
        <div class="act-style act-style1 fl" :class="active ? 'active' : '' " v-on:click="active1" :forum-style="1">
            <a href="javascript:;" title="<?= __('活动版块将独占显示在商城首页'); ?>"><em></em></a>
        </div>
        <div class="act-style act-style2 fl" :class="active ? '' : 'active' " v-on:click="active2" :forum-style="2">
            <a href="javascript:;" title="<?= __('两个活动版块组合显示在商城首页'); ?>">
                <em></em>
                <em></em>
            </a>
        </div>
        <span class="style-tips"><?= __('样式一：活动版块将独占显示在商城首页'); ?></span>
    </div>
</div>

<script>

    var vm=new Vue({
        el:"#saleBox",
        data:{
            activeTitle:"",
            active:true,
            style:"1"
        },
        mounted:function(){
            var api = frameElement.api;
            var item = api.data.item;
            console.log(api);
            if (item) {
                this.activeTitle = item.forum_name;
                if (item.forum_style == 2) {
                    this.active=false;
                    this.style = item.forum_style;
                    $('.clearfix span').html('<?= __('样式二：两个活动版块组合显示在商城首页'); ?>');
                }
            }

            var that=this;
            api.button({
                id: "confirm",
                name:"<?= __('确认'); ?>",
                focus: true,
                callback: function () {
                    if (!that.activeTitle) {
                        alert("请输入名称！");
                        return false;
                    }

                    if (item) {
                        
                        axios.post(BASE_URL+"/index.php?ctl=Forum&met=editForum&typ=json",Qs.stringify({
                            id:item.id,
                            forum_name:that.activeTitle,
                            forum_style:that.style
                        })).then(function(res){
                            console.log("success");
                            var callback = frameElement.api.data.callback;
                            callback();
                            api.close();
                        }).catch(function(){
                            console.log("fail");
                        })
                    } else {
                        axios.post(BASE_URL+"/index.php?ctl=Forum&met=addFrontForum&typ=json",Qs.stringify({
                            forum_name:that.activeTitle,
                            forum_style:that.style,
                        },{ indices: false })).then(function(res){
                            console.log("success");
                            var callback = frameElement.api.data.callback;
                            callback();
                            api.close();
                            
                        }).catch(function(res){
                            console.log("fail");
                        })
                    }

                    return false;
                }
            },{
                id:"cancel",
                name:"<?= __('取消'); ?>"
            })
        },
        methods:{
            active1:function(){
                this.active=true;
                this.style="1";
                $('.clearfix span').html('<?= __('样式一：活动版块将独占显示在商城首页'); ?>');
            },
            active2:function(){
                this.active=false;
                this.style="2";
                $('.clearfix span').html('<?= __('样式二：两个活动版块组合显示在商城首页'); ?>');
            }

        }
    })
</script>
