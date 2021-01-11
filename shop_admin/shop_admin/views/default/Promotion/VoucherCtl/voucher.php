<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="http://at.alicdn.com/t/font_136526_rc9m7tdbk1d.css">

 <!-- <?= __('限时折扣'); ?> -->
 
 <div class="sale-box" id="voucher">
        <h4 class="sale-title"><?= __('标题：'); ?></h4>
        <div class="relative mb20">
            <input class="sale-tit-input" type="text" v-model="defaultTitle" maxlength="4">
            <em class="sale-tit-limit"><b>{{defaultTitle.length}}</b>/{{changenum}}</em>
        </div>
        <div>
            <h4 class="sale-title"><?= __('内容：'); ?></h4>
            <div class="items-box">
                <div class="up-img-box"><img :src="upImg"></div>
             </div>
        </div>
        <p class="up-tips"><?= __('上传代金券活动入口图片，推荐图片尺寸'); ?>300x150<?= __('像素'); ?></p>
        <div class="up-img"><input type="file" v-on:change="fileImage($event)"><span><?= __('图片上传'); ?></span></div>
</div>
<script type="text/javascript" src="<?=$this->view->js_com?>/vue.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/axios.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/qs.min.js" charset="utf-8"></script>
    <script>
        var vm=new Vue({
            el:"#voucher",
            data:{
                defaultTitle:"<?= __('领券中心'); ?>",
                changenum:"4",
                upImg:"",
                changeList:[],
                postaccount:{}
           },
            mounted:function(){
                var api = frameElement.api;
                var that=this;
                axios.get(BASE_URL + "/index.php?ctl=Promotion_Voucher&met=getVoucherStatus&typ=json", {
                    params: {}
                }).then(function (res) {
                    if (res.data.status == 200) {
                        $('#tips').css('display', 'none');
                        $('#voucher').css('display', 'block');
                        console.log(111);
                    } else {
                        $('#tips').css('display', 'block');
                        $('#voucher').css('display', 'none');
                        console.log(222);
                    }
                }).then(function () {
                    console.log("suceess");
                }).catch(function () {
                    console.log("fail");
                })
                api.button({
                    id: "confirm",
                    name:"<?= __('确认'); ?>",
                    focus: true,
                    callback: function () {
                        that.saleAccount();
                        if(api.data.wxapp == 1){
                            var url = BASE_URL+"/index.php?ctl=Wx_TplLayout&met=editTplABLayout&typ=json";
                        }else{
                            var url = BASE_URL+"/index.php?ctl=Mb_TplLayout&met=editTplABLayout&typ=json";
                        }
                        axios.post(url,Qs.stringify({
                            module:api.data.module,
                            fromto:api.data.fromto,
                            item_id:api.data.iframe_id,
                            layout_data:that.postaccount
                        },{ indices: false })).then(function(res){
                            console.log("success");
                            var callback = frameElement.api.data.callback;
                            callback();
                            api.close();

                        }).catch(function(res){
                            conosle.log("fail");
                        })
                        return false;
                    }
                },{
                    id: "cancel",
                    name:"<?= __('取消'); ?>",
                })
            },
            methods:{
                saleAccount:function(){
                     this.changeList.push({
                        title:this.defaultTitle,
                        type:"voucher",
                        content:this.upImg
                    })
                    for(var a=0; a<this.changeList.length; a++) {
                        this.postaccount[a] = this.changeList[a];
                    }
                },
                fileImage:function(e){
                        var that=this;
                        var file = e.target.files[0];
                        var imgSize=file.size/1024;
                        if(imgSize>200){
                            alert('请上传大小不要超过200KB的图片')
                        }else{
                            var reader = new FileReader();
                            reader.readAsDataURL(file); // 读出base64
                            reader.onloadend = function () {
                                // '图片的格式可以直接当成的属性值
                                var dataURL = reader.result;
                                console.log(reader);
                                that.upImg=dataURL;
                            };
                        }

                    }

            }
        })
    </script>