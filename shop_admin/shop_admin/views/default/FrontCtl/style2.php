<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="http://at.alicdn.com/t/font_136526_rc9m7tdbk1d.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/vue.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/axios.min.js" charset="utf-8"></script>

<div class="sale-box activity-module activity-module-style2" id="saleBox">
    <!-- <h4 class="sale-title"><?= __('标题：'); ?></h4>
    <div class="relative mb20">
        <input class="sale-tit-input" type="text" v-model="activeTitle" maxlength="4">
        <em class="sale-tit-limit"><b>{{activeTitle.length}}</b>/{{changenum}}</em>
    </div> -->
    
    <div class="pc-style2 style2-module1">
        <dl>
            <dt>活动类型：</dt>
            <dd>
                <select name="" id="saleSelect1"  v-model="titleSel1" v-on:change="change1()">
                    <option disabled value="<?= __('请选择'); ?>"><?= __('请选择'); ?></option>
                    <option value="<?= __('限时折扣'); ?>"><?= __('限时折扣'); ?></option>
                    <option value="<?= __('团购风暴'); ?>"><?= __('团购风暴'); ?></option>
                    <option value="<?= __('领券中心'); ?>"><?= __('领券中心'); ?></option>
                    <option value="<?= __('平台红包'); ?>"><?= __('平台红包'); ?></option>
                </select>
            </dd>
        </dl>
        <em id="styleEdit1" class="edit-module1"  v-show="btnEdit1" v-on:click="styleEdit1"><?= __('编辑'); ?></em>
        <div class="activity-edit wp100">
            <ul class="sale-limit-items discount" v-if="type1=='discount'">
                <li v-for="(item,index) in itemList" :key="index">
                    <em><img :src="item.goods_image"></em>
                    <span class="one-overflow">{{item.goods_name}}</span>
                    <strong><?= __('￥'); ?>{{item.discount_price}}</strong>
                </li>
            </ul>
            <ul class="sale-limit-items groupbuy" v-if="type1=='groupbuy'">
                <li v-for="(item,index) in itemList" :key="index">
                    <em><img :src="item.groupbuy_image"></em>
                    <span class="one-overflow">{{item.goods_name}}</span>
                    <strong><?= __('￥'); ?>{{item.groupbuy_price}}</strong>
                </li>
            </ul>
            <ul class="sale-limit-items voucher" v-if="type1=='voucher'">
                <li v-for="(item,index) in itemList" :key="index">
                    <em><img :src="item.voucher_t_customimg"></em>
                    <span class="one-overflow">{{item.voucher_t_title}}</span>
                    <strong><?= __('￥'); ?>{{item.voucher_t_price}}</strong>
                </li>
            </ul>
            <ul class="sale-limit-items redpacket" v-if="type1=='redpacket'">
                <li v-for="(item,index) in itemList" :key="index">
                    <em><img :src="item.redpacket_t_img"></em>
                    <span class="one-overflow">{{item.redpacket_t_title}}</span>
                    <strong><?= __('￥'); ?>{{item.redpacket_t_price}}</strong>
                </li>
            </ul>
        </div>
    </div>
    <div class="pc-style2 style2-module2">
        <dl>
            <dt>活动类型：</dt>
            <dd>
                <select name="" id="saleSelect2"  v-model="titleSel2" v-on:change="change2()">
                    <option disabled value="<?= __('请选择'); ?>"><?= __('请选择'); ?></option>
                    <option value="<?= __('限时折扣'); ?>"><?= __('限时折扣'); ?></option>
                    <option value="<?= __('团购风暴'); ?>"><?= __('团购风暴'); ?></option>
                    <option value="<?= __('领券中心'); ?>"><?= __('领券中心'); ?></option>
                    <option value="<?= __('平台红包'); ?>"><?= __('平台红包'); ?></option>
                </select>
            </dd>
        </dl>
        
        <em id="styleEdit1" class="edit-module1"  v-show="btnEdit2" v-on:click="styleEdit2"><?= __('编辑'); ?></em>
        <div class="activity-edit wp100">
            <ul class="sale-limit-items discount" v-if="type2=='discount'">
                <li v-for="(item,index) in infoList" :key="index">
                    <em><img :src="item.goods_image"></em>
                    <span>{{item.goods_name}}</span>
                    <strong><?= __('￥'); ?>{{item.discount_price}}</strong>
                </li>
            </ul>
            <ul class="sale-limit-items groupbuy" v-if="type2=='groupbuy'">
                <li v-for="(item,index) in infoList" :key="index">
                    <em><img :src="item.groupbuy_image"></em>
                    <span class="one-overflow">{{item.goods_name}}</span>
                    <strong><?= __('￥'); ?>{{item.groupbuy_price}}</strong>
                </li>
            </ul>
            <ul class="sale-limit-items voucher" v-if="type2=='voucher'">
                <li v-for="(item,index) in infoList" :key="index">
                    <em><img :src="item.voucher_t_customimg"></em>
                    <span class="one-overflow">{{item.voucher_t_title}}</span>
                    <strong><?= __('￥'); ?>{{item.voucher_t_price}}</strong>
                </li>
            </ul>
            <ul class="sale-limit-items redpacket" v-if="type2=='redpacket'">
                <li v-for="(item,index) in infoList" :key="index">
                    <em><img :src="item.redpacket_t_img"></em>
                    <span class="one-overflow">{{item.redpacket_t_title}}</span>
                    <strong><?= __('￥'); ?>{{item.redpacket_t_price}}</strong>
                </li>
            </ul>
        </div>
    </div>
    

</div>
<script>

    var vm=new Vue({
        el:"#saleBox",
        data:{
            formto:0,
            formdata:[],
            titleSel1:'<?= __('请选择'); ?>',
            titleSel2:'<?= __('请选择'); ?>',
            itemList:[],
            infoList:[],
            type1:'',
            type2:'',
            forumType1:'',
            forumType2:'',
            btnEdit1:false,
            btnEdit2:false,
            newtype1:'',
            newtype2:'',
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
                if (res.data.data.forum_content[0]) {
                    console.log(res.data.data.forum_content[0])
                    that.type1=res.data.data.forum_content[0].type;
                    that.newtype1=res.data.data.forum_content[0].type;
                    that.itemList=res.data.data.forum_content[0].content_info;
                    that.forumType1=res.data.data.forum_content[0].title;
                    switch(that.type1){
                        case "groupbuy":
                            that.titleSel1 = "<?= __('团购风暴'); ?>";
                            that.btnEdit1=true;
                            break;
                        case "discount":
                            that.titleSel1 = "<?= __('限时折扣'); ?>";
                            that.btnEdit1=true;
                            break;
                        case "voucher":
                            that.titleSel1 = "<?= __('领券中心'); ?>";
                            that.btnEdit1=true;
                            break;
                        case "redpacket":
                            that.titleSel1 = "<?= __('平台红包'); ?>";
                            that.btnEdit1=true;
                            break;
                    }
                } 
                if(res.data.data.forum_content[1]){
                    that.type2=res.data.data.forum_content[1].type;
                    that.newtype2=res.data.data.forum_content[1].type;
                    that.infoList=res.data.data.forum_content[1].content_info;
                    that.forumType2=res.data.data.forum_content[1].title;
                    console.log(this.infoList)
                    console.log(that.infoList)
                    console.log(845)
                    switch(that.type2){
                        case "groupbuy":
                            that.titleSel2 = "<?= __('团购风暴'); ?>";
                            that.btnEdit2=true;
                            break;
                        case "discount":
                            that.titleSel2 = "<?= __('限时折扣'); ?>";
                            that.btnEdit2=true;
                            break;
                        case "voucher":
                            that.titleSel2 = "<?= __('领券中心'); ?>";
                            that.btnEdit2=true;
                            break;
                        case "redpacket":
                            that.titleSel2 = "<?= __('平台红包'); ?>";
                            that.btnEdit2=true;
                            break;
                    }
                }
            }).catch(function(){
                console.log("fail");
            })

        },
        methods:{
            styleEdit1:function(){
                var api = frameElement.api;
                this.formto = 1;
                if (api) {
                    if (api.data.forum_content[0] && this.type1 == api.data.forum_content[0].type) {
                        this.formdata = this.itemList;
                    } 
                }

                var saleSelect=$("#saleSelect1").val();
                var Url="",title="";
                switch(saleSelect){
                    
                    case "<?= __('限时折扣'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=discount';
                        title="<?= __('限时折扣'); ?>"
                        this.forumType1=this.forumType1?this.forumType1:"<?= __('限时折扣'); ?>"
                        break;
                    case "<?= __('团购风暴'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=groupbuy';
                        title="<?= __('团购风暴'); ?>"
                        this.forumType1=this.forumType1?this.forumType1:"<?= __('团购风暴'); ?>"
                        break;
                    case "<?= __('领券中心'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=voucher';
                        title="<?= __('领券中心'); ?>"
                        this.forumType1=this.forumType1?this.forumType1:"<?= __('领券中心'); ?>"
                        break;
                    case "<?= __('平台红包'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=redpacket';
                        title="<?= __('平台红包'); ?>"
                        this.forumType1=this.forumType1?this.forumType1:"<?= __('平台红包'); ?>"
                        break;

                }
                
                $.dialog({
                    title:title,
                    dialogClass:'dialogFrame',
                    content: Url,
                    data: {forumType:this.forumType1, data:api.data, formto:this.formto, item:this.formdata, callback: function (){ window.location.reload();}},
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
            styleEdit2:function(){
                var api = frameElement.api;
                var that = this;
                this.formto = 2;
                if (api.data.forum_content[1] && this.type2 == api.data.forum_content[1].type) {
                    that.formdata = this.infoList;
                } 
                
                var saleSelect=$("#saleSelect2").val();
                var Url="",title="";
                switch(saleSelect){
                    
                    case "<?= __('限时折扣'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=discount';
                        title="<?= __('限时折扣'); ?>"
                        this.forumType2=this.forumType2?this.forumType2:"<?= __('限时折扣'); ?>"
                        break;
                    case "<?= __('团购风暴'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=groupbuy';
                        title="<?= __('团购风暴'); ?>"
                        this.forumType2=this.forumType2?this.forumType2:"<?= __('团购风暴'); ?>"
                        break;
                    case "<?= __('领券中心'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=voucher';
                        title="<?= __('领券中心'); ?>"
                        this.forumType2=this.forumType2?this.forumType2:"<?= __('领券中心'); ?>"
                        break;
                    case "<?= __('平台红包'); ?>":
                        Url='url:' + SITE_URL + '?ctl=Front&met=redpacket';
                        title="<?= __('平台红包'); ?>"
                        this.forumType2=this.forumType2?this.forumType2:"<?= __('平台红包'); ?>"
                        break;

                }
                $.dialog({
                    title:title,
                    dialogClass:'dialogFrame',
                    content: Url,
                    data: {forumType:this.forumType2, data:api.data, formto:this.formto, item:this.formdata, callback: function (){ window.location.reload();}},
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
            change1:function(){
                var api = frameElement.api;
                this.btnEdit1=true;
                switch(this.titleSel1){
                    case "<?= __('团购风暴'); ?>":
                        this.type1 = 'groupbuy';
                        this.forumType1 =  "<?= __('团购风暴'); ?>";
                        break;
                    case "<?= __('限时折扣'); ?>":
                        this.type1 = 'discount';
                        this.forumType1 =  "<?= __('限时折扣'); ?>";
                        break;
                    case "<?= __('领券中心'); ?>":
                        this.type1 = 'voucher';
                        this.forumType1 =  "<?= __('领券中心'); ?>";
                        break;
                    case "<?= __('平台红包'); ?>":
                        this.type1 = 'redpacket';
                        this.forumType1 =  "<?= __('平台红包'); ?>";
                        break;
                }
                if (this.type1 != this.newtype1) {
                    this.type1 = '';
                } 
            },
            change2:function(){
                var api = frameElement.api;
                this.btnEdit2=true;
                switch(this.titleSel2){
                    case "<?= __('团购风暴'); ?>":
                        this.type2 = 'groupbuy';
                        this.forumType2 =  "<?= __('团购风暴'); ?>";
                        break;
                    case "<?= __('限时折扣'); ?>":
                        this.type2 = 'discount';
                        this.forumType2 =  "<?= __('限时折扣'); ?>";
                        break;
                    case "<?= __('领券中心'); ?>":
                        this.type2 = 'voucher';
                        this.forumType2 =  "<?= __('领券中心'); ?>";
                        break;
                    case "<?= __('平台红包'); ?>":
                        this.type2 = 'redpacket';
                        this.forumType2 =  "<?= __('平台红包'); ?>";
                        break;
                }
                if (this.type2 != this.newtype2) {
                    this.type2 = '';
                } 
            },


        }
    })
</script>
