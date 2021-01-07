<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="http://at.alicdn.com/t/font_136526_rc9m7tdbk1d.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/vue.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/axios.min.js" charset="utf-8"></script>
<style>
    [v-cloak]{ display: none; }
</style>
<div class="activity-module activity-module-style2" id="saleBox">
    <div class="relative clearfix">
        <div class="style2 style2-module1">
            <dl>
                <dt><?= __('活动类型：'); ?></dt>
                <dd>
                    <select name="" v-model="titSel1" id="saleSelect1" v-on:change="change1()">
                        <option disabled value="<?= __('请选择'); ?>"><?= __('请选择'); ?></option>
                        <option value="<?= __('限时折扣'); ?>"><?= __('限时折扣'); ?></option>
                        <option value="<?= __('店铺代金券'); ?>"><?= __('店铺代金券'); ?></option>
                        <option value="<?= __('平台红包'); ?>"><?= __('平台红包'); ?></option>
                        <option value="<?= __('预售活动'); ?>"><?= __('预售活动'); ?></option>
                    </select>
                </dd>
            </dl>
            <button class="edit-module1" v-show="btnEdit1" id="styleEdit1" v-on:click="styleEdit1" data-fromto="1">编辑</button>

            <div class="activity-edit activity-edit1 wp100">

                <ul class="sale-limit-items discount" v-if="type1=='discount'">
                    <li v-cloak v-for="item in itemList1">
                        <em><img :src="item.goods_image"></em>
                        <span class="one-overflow">{{item.goods_name}}</span>
                        <strong>￥{{item.discount_price}}</strong>
                    </li>
                </ul>

                <ul class="sale-limit-items presale" v-if="type1=='presale'">
                    <li v-cloak v-for="item in itemList1">
                        <em><img :src="item.goods_image"></em>
                        <span class="one-overflow">{{item.goods_name}}</span>
                        <strong>￥{{item.presale_price}}</strong>
                    </li>
                </ul>

                <div class="voucher" v-if="type1=='voucher'">
                    <img class="wp100" v-for="item in itemList1" :src="item">
                </div>
                <div class="redpacket" v-if="type1=='redpacket'">
                    <img class="wp100" v-for="item in itemList1" :src="item">
                </div>

            </div>
        </div>

        <div class="style2 style2-module2">
            <dl>
                <dt><?= __('活动类型：'); ?></dt>
                <dd>
                    <select name="" v-model="titSel2" id="saleSelect2" v-on:change="change2()">
                        <option disabled value="<?= __('请选择'); ?>"><?= __('请选择'); ?></option>
                        <option value="<?= __('限时折扣'); ?>"><?= __('限时折扣'); ?></option>
                        <option value="<?= __('店铺代金券'); ?>"><?= __('店铺代金券'); ?></option>
                        <option value="<?= __('平台红包'); ?>"><?= __('平台红包'); ?></option>
                        <option value="<?= __('预售活动'); ?>"><?= __('预售活动'); ?></option>
                    </select>
                </dd>
            </dl>
            <button  class="edit-module1" id="styleEdit2" v-show="btnEdit2" v-on:click="styleEdit2" data-fromto="2"><?= __('编辑'); ?></button>
            <div class="activity-edit activity-edit2 wp100">

                <ul class="sale-limit-items discount" v-if="type2=='discount'">
                    <li v-cloak v-for="item in itemList2">
                        <em><img :src="item.goods_image"></em>
                        <span class="one-overflow">{{item.goods_name}}</span>
                        <strong>￥{{item.discount_price}}</strong>
                    </li>
                </ul>

                <ul class="sale-limit-items presale" v-if="type2=='presale'">
                    <li v-cloak v-for="item in itemList2">
                        <em><img :src="item.goods_image"></em>
                        <span class="one-overflow">{{item.goods_name}}</span>
                        <strong>￥{{item.presale_price}}</strong>
                    </li>
                </ul>

                <div class="voucher" v-if="type2=='voucher'">
                    <img class="wp100" v-for="item in itemList2" :src="item">
                </div>
                <div class="redpacket" v-if="type2=='redpacket'">
                    <img class="wp100" v-for="item in itemList2" :src="item">
                </div>
            </div>


        </div>
    </div>

</div>
<script>

    var vm=new Vue({
        el:"#saleBox",
        data:{
            itemList1:[],
            itemList2:[],
            titSel1:"请选择",
            titSel2:"请选择",
            type1:"",
            type2:"",
            btnEdit1:false,
            btnEdit2:false,
            fromto:"",
        },
        mounted:function(){
            axios.get(SITE_URL + "?ctl=Wx_TplLayout&met=tplLayoutList&typ=json",{
                params:{
                    wx_tpl_layout_id:frameElement.api.data.item_data.wx_tpl_layout_id
                }

            }).then(function(res){
                console.log(res);
                console.log(99999);
                var that=this;
                var info1=res.data.data.items[0].wx_tpl_layout_data[0];
                var info2=res.data.data.items[0].wx_tpl_layout_data[1];
                console.log(info1,info2);
                if(info1.content_info.length){
                    vm.btnEdit1=true;
                    if(info1.type=="voucher" ||info1.type=="redpacket"){
                        vm.itemList1[0]=info1.content_info;
                    }else{
                        vm.itemList1=info1.content_info;
                    }
                    vm.type1=info1.type;
                    switch(info1.type){
                        case "discount":
                            vm.titSel1="限时折扣";
                            break;
                        case "voucher":
                            vm.titSel1="店铺代金券"
                            break;
                        case "redpacket":
                            vm.titSel1="平台红包"
                            break;
                        case "presale":
                            vm.titSel1="预售活动"
                            break;
                    }

                }
                if(info2.content_info.length){
                    vm.btnEdit2=true;
                    if(info2.type=="voucher" ||info2.type=="redpacket"){
                        vm.itemList2[0]=info2.content_info;
                    }else{
                        vm.itemList2=info2.content_info;
                    }
                    vm.type2=info2.type;
                    switch(info2.type){

                        case "discount":
                            vm.titSel2="限时折扣";
                            break;

                        case "voucher":
                            vm.titSel2="店铺代金券"
                            break;
                        case "redpacket":
                            vm.titSel2="平台红包"
                            break;
                        case "presale":
                            vm.titSel2="预售活动"
                            break;
                    }
                }




            }).catch(function(){
                console.log("fail");
            })
            var api = frameElement.api;
            api.button({
                id: "confirm",
                name:"<?= __('确认'); ?>",
                focus: true,
                callback: function () {
                    var callback = frameElement.api.data.callback;
                    callback();
                    api.close();
                    return false;
                    api.data.callback();
                }
            },{
                id: "cancel",
                name:"<?= __('取消'); ?>",
            })



        },
        methods:{
            getEdit(sel,fromto,forumType){
                var saleSelect=sel.val();
                var api = frameElement.api;
                var that=this;
                var Url="",title="";
                var discountUrl=BASE_URL+"/index.php?ctl=Promotion_Discount&met=getDiscountGoods&typ=e";

                var voucherUrl=BASE_URL+"/index.php?ctl=Promotion_Voucher&met=voucher&typ=e";
                var redpacketUrl=BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=redpacket&typ=e";
                var presaleUrl=BASE_URL+"/index.php?ctl=Promotion_Presale&met=presale&typ=e";
                switch(saleSelect){
                    case "<?= __('限时折扣'); ?>":
                        Url="url:"+discountUrl;
                        break;

                    case "<?= __('店铺代金券'); ?>":
                        Url="url:"+voucherUrl;
                        break;
                    case "<?= __('平台红包'); ?>":
                        Url="url:"+redpacketUrl;
                        break;
                    case "<?= __('预售活动'); ?>":
                        Url="url:"+presaleUrl;
                        break;
                }

                $.dialog({
                    title:saleSelect,
                    dialogClass:'dialogFrame',
                    content:Url,
                    data:{
                        iframe_id:api.data.item_id,
                        fromto:fromto,
                        wxapp:1,
                        forumType:forumType,
                        module:api.data.module,
                        data:api.data.item_data.wx_tpl_layout_data,
                        callback: function (){ window.location.reload(); }
                    },
                    zIndex:9999,
                    width:800,
                    height: 500,
                    max: false,
                    min: false,
                    cache: false,
                    lock: true
                });
            },
            styleEdit1:function(){

                var saleSelect=$("#saleSelect1").val();
                var api = frameElement.api;
                var that=this;
                that.fromto="1";
                var Url="",title="";
                var discountUrl=BASE_URL+"/index.php?ctl=Promotion_Discount&met=getDiscountGoods&typ=e";
                var voucherUrl=BASE_URL+"/index.php?ctl=Promotion_Voucher&met=voucher&typ=e";
                var redpacketUrl=BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=redpacket&typ=e";
                var presaleUrl=BASE_URL+"/index.php?ctl=Promotion_Presale&met=presale&typ=e";
                switch(saleSelect){
                    case "<?= __('限时折扣'); ?>":
                        Url="url:"+discountUrl;
                        that.forumType1=that.forumType1?that.forumType1:"<?= __('限时折扣'); ?>"
                        break;
                    case "<?= __('店铺代金券'); ?>":
                        Url="url:"+voucherUrl;
                        that.forumType1=that.forumType1?that.forumType1:"<?= __('店铺代金券'); ?>"
                        break;
                    case "<?= __('平台红包'); ?>":
                        Url="url:"+redpacketUrl;
                        that.forumType1=that.forumType1?that.forumType1:"<?= __('平台红包'); ?>"
                        break;
                    case "<?= __('预售活动'); ?>":
                        Url="url:"+presaleUrl;
                        that.forumType1=that.forumType1?that.forumType1:"<?= __('预售活动'); ?>"
                        break;
                }

                $.dialog({
                    title:saleSelect,
                    dialogClass:'dialogFrame',
                    content:Url,
                    data:{
                        iframe_id:api.data.item_id,
                        fromto:that.fromto,
                        wxapp:1,
                        forumType:that.forumType1,
                        module:api.data.module,
                        data:api.data.item_data.wx_tpl_layout_data,
                        callback: function (){ window.location.reload(); }
                    },
                    width:800,
                    height: 500,
                    max: false,
                    min: false,
                    cache: false,
                    lock: true
                });


            },
            styleEdit2:function(){

                var saleSelect=$("#saleSelect2").val();
                var api = frameElement.api;
                var that=this;
                that.fromto="2";
                var Url="",title="";
                var discountUrl=BASE_URL+"/index.php?ctl=Promotion_Discount&met=getDiscountGoods&typ=e";
                var voucherUrl=BASE_URL+"/index.php?ctl=Promotion_Voucher&met=voucher&typ=e";
                var redpacketUrl=BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=redpacket&typ=e";
                var presaleUrl=BASE_URL+"/index.php?ctl=Promotion_Presale&met=presale&typ=e";
                switch(saleSelect){
                    case "<?= __('限时折扣'); ?>":
                        Url="url:"+discountUrl;
                        that.forumType2=that.forumType2?that.forumType2:"<?= __('限时折扣'); ?>"
                        break;

                    case "<?= __('店铺代金券'); ?>":
                        Url="url:"+voucherUrl;
                        that.forumType2=that.forumType2?that.forumType2:"<?= __('店铺代金券'); ?>"
                        break;
                    case "<?= __('平台红包'); ?>":
                        Url="url:"+redpacketUrl;
                        that.forumType2=that.forumType2?that.forumType2:"<?= __('平台红包'); ?>"
                        break;
                    case "<?= __('预售活动'); ?>":
                        Url="url:"+presaleUrl;
                        that.forumType2=that.forumType2?that.forumType2:"<?= __('预售活动'); ?>"
                        break;
                }
                $.dialog({
                    title:saleSelect,
                    dialogClass:'dialogFrame',
                    content:Url,
                    data:{
                        iframe_id:api.data.item_id,
                        fromto:that.fromto,
                        wxapp:1,
                        forumType:that.forumType2,
                        module:api.data.module,
                        data:api.data.item_data.wx_tpl_layout_data,
                        callback: function (){ window.location.reload(); }
                    },
                    width:800,
                    height: 500,
                    max: false,
                    min: false,
                    cache: false,
                    lock: true
                });

            },
            change1:function(){
                this.btnEdit1=true;
                var value1 = $("#saleSelect1").find('option:selected').val();
                switch (value1) {
                    case "<?= __('限时折扣'); ?>":
                        this.type1 = "discount";
                        break;

                    case "<?= __('店铺代金券'); ?>":
                        this.type1 = "voucher";
                        break;
                    case "<?= __('平台红包'); ?>":
                        this.type1 = "redpacket";
                        break;
                    case "<?= __('预售活动'); ?>":
                        this.type1 = "presale";
                        break;
                }
                var api = frameElement.api;
                var actType = api.data.item_data.wx_tpl_layout_data[0];
                if (actType && this.type1 != actType.type) {
                    this.type1 = '';
                }
            },
            change2:function(){
                this.btnEdit2=true;
                var value2 = $("#saleSelect2").find('option:selected').val();
                switch (value2) {
                    case "<?= __('限时折扣'); ?>":
                        this.type2 = "discount";
                        break;

                    case "<?= __('店铺代金券'); ?>":
                        this.type2 = "voucher";
                        break;
                    case "<?= __('平台红包'); ?>":
                        this.type2 = "redpacket";
                        break;
                    case "<?= __('预售活动'); ?>":
                        this.type2 = "presale";
                        break;
                }
                var api = frameElement.api;
                var actType = api.data.item_data.wx_tpl_layout_data[1];
                if (actType && this.type2 != actType.type) {
                    this.type2 = '';
                }
            },


        }
    })
</script>
