<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="http://at.alicdn.com/t/font_136526_rc9m7tdbk1d.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/vue.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/axios.min.js" charset="utf-8"></script>

<div class="sale-box" id="saleBox">
    <!-- <h4 class="sale-title"><?= __('标题：'); ?></h4>
    <div class="relative mb20">
        <input class="sale-tit-input" type="text" v-model="activeTitle" maxlength="4">
        <em class="sale-tit-limit"><b>{{activeTitle.length}}</b>/{{changenum}}</em>
    </div> -->
    
<div class="activity-module" id="activityA">
    <div class="relative">
        <dl>
            <dt><?= __('活动类型：'); ?></dt>
            <dd>
            <select name="" id="saleSelect"  v-model="titleSel" v-on:change="change()">
                <option disabled value="<?= __('请选择'); ?>"><?= __('请选择'); ?></option>
                <option value="<?= __('限时折扣'); ?>"><?= __('限时折扣'); ?></option>
                <option value="<?= __('团购风暴'); ?>"><?= __('团购风暴'); ?></option>
                <option value="<?= __('领券中心'); ?>"><?= __('领券中心'); ?></option>
                <option value="<?= __('平台红包'); ?>"><?= __('平台红包'); ?></option>
            </select>
            </dd>
        </dl>
        <button id="styleEdit1" class="edit-module1" v-show="btnEdit1" v-on:click="styleEdit1"><?= __('编辑'); ?></button>
    </div>
    <div class="activity-edit wp100">
        <ul class="sale-limit-items discount" v-if="type=='discount'">
            <li v-for="(item,index) in itemList" :key="index">
                <em><img :src="item.goods_image"></em>
                <span class="one-overflow">{{item.discount_name}}</span>
                <strong><?= __('￥'); ?>{{item.discount_price}}</strong>
            </li>
        </ul>
        <ul class="sale-limit-items groupbuy" v-if="type=='groupbuy'">
            <li v-for="(item,index) in itemList" :key="index">
                <em><img :src="item.groupbuy_image"></em>
                <span class="one-overflow">{{item.goods_name}}</span>
                <strong><?= __('￥'); ?>{{item.groupbuy_price}}</strong>
            </li>
        </ul>
        <ul class="sale-limit-items voucher" v-if="type=='voucher'">
            <li v-for="(item,index) in itemList" :key="index">
                <em><img :src="item.voucher_t_customimg"></em>
                <span class="one-overflow">{{item.voucher_t_title}}</span>
                <strong><?= __('￥'); ?>{{item.voucher_t_price}}</strong>
            </li>
        </ul>
        <ul class="sale-limit-items redpacket" v-if="type=='redpacket'">
            <li v-for="(item,index) in itemList" :key="index">
                <em><img :src="item.redpacket_t_img"></em>
                <span class="one-overflow">{{item.redpacket_t_title}}</span>
                <strong><?= __('￥'); ?>{{item.redpacket_t_price}}</strong>
            </li>
        </ul>
    </div>
</div>
<script>
    var vm=new Vue({
        el:"#activityA",
        data:{
            titleSel:'<?= __('请选择'); ?>',
            itemList:[],
            formdata:[],
            forumType:'',
            type:'',
            btnEdit1:false,
            newtype:''
        },
        mounted:function(){
            var api = frameElement.api;
            api.button({
                id: "confirm",
                name:"<?= __('确认'); ?>",
                focus: true
            },{
                id:"cancel",
                name:"<?= __('取消'); ?>"
            })

            var that=this;
            axios.get(BASE_URL+"/index.php?ctl=Forum&met=getForumContent&typ=json",{
                    params:{
                        id:api.data.id
                    }
            }).then(function(res){
                that.type=res.data.data.forum_content[0].type;
                that.newtype=res.data.data.forum_content[0].type;
                that.itemList=res.data.data.forum_content[0].content_info;
                that.forumType=res.data.data.forum_content[0].title;
                console.log(that.itemList)
                console.log(this.itemList)
                switch(that.type){
                    case "groupbuy":
                        that.titleSel = "<?= __('团购风暴'); ?>";
                        that.btnEdit1=true;
                        break;
                    case "discount":
                        that.titleSel = "<?= __('限时折扣'); ?>";
                        that.btnEdit1=true;
                        break;
                    case "voucher":
                        that.titleSel = "<?= __('领券中心'); ?>";
                        that.btnEdit1=true;
                        break;
                    case "redpacket":
                        that.titleSel = "<?= __('平台红包'); ?>";
                        that.btnEdit1=true;
                        break;
                }
            }).catch(function(){
                console.log("fail");
            })

        },
        methods:{
            
            styleEdit1:function(){
                var saleSelect=$("#saleSelect").val();
                var api = frameElement.api;
                var Url="",title="";
                switch(saleSelect){
                    case "<?= __('团购风暴'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=groupbuy';
                        title= "<?= __('团购风暴'); ?>";
                        this.forumType= this.forumType?this.forumType:"<?= __('团购风暴'); ?>";
                        break;
                    case "<?= __('限时折扣'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=discount';
                        title= "<?= __('限时折扣'); ?>";
                        this.forumType=this.forumType?this.forumType:"<?= __('限时折扣'); ?>";
                        break;
                    case "<?= __('领券中心'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=voucher';
                        title= "<?= __('领券中心'); ?>";
                        this.forumType=this.forumType?this.forumType:"<?= __('领券中心'); ?>";
                        break;
                    case "<?= __('平台红包'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=redpacket';
                        title= "<?= __('平台红包'); ?>";
                        this.forumType=this.forumType?this.forumType:"<?= __('平台红包'); ?>";
                        break;

                }
                
                if (api.data.forum_content[0] && this.type == api.data.forum_content[0].type) {
                    this.formdata = this.itemList;
                } 
                
                $.dialog({
                    title:title,
                    dialogClass:'dialogFrame',
                    content: Url,
                    data: {forumType:this.forumType, data:api.data, formto:1, item:this.formdata, callback: function (){ window.location.reload();}},
                    // width: $(window).width() * 0.8,
                    // height: $(window).height() * 0.9,
                    width:800,
                    height: 800,
                    max: false,
                    min: false,
                    cache: false,
                    lock: true
                });
                this.formdata =[];
            },
            // 删除内容商品
            delSale:function(idx){
                this.itemList.splice(idx,1);
            },
            change:function(){
                var api = frameElement.api;
                this.btnEdit1=true;
                switch(this.titleSel){
                    case "<?= __('团购风暴'); ?>":
                        this.type = 'groupbuy';
                        this.forumType =  "<?= __('团购风暴'); ?>";
                        break;
                    case "<?= __('限时折扣'); ?>":
                        this.type = 'discount';
                        this.forumType =  "<?= __('限时折扣'); ?>";
                        break;
                    case "<?= __('领券中心'); ?>":
                        this.type = 'voucher';
                        this.forumType =  "<?= __('领券中心'); ?>";
                        break;
                    case "<?= __('平台红包'); ?>":
                        this.type = 'redpacket';
                        this.forumType =  "<?= __('平台红包'); ?>";
                        break;
                }
                
                if (this.type != this.newtype) {
                    this.type = '';
                } 
            }
        }
    })
 
</script>
