<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
include TPL_PATH . '/' . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/vue.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/axios.min.js" charset="utf-8"></script>
<div class="activity-module" id="activityA">
    <div class="relative">
        <dl>
            <dt>活动类型：</dt>
            <dd>
                <select name="activity" id="activity" v-model="titleSel" v-on:change="change()">
                    <option disabled value="请选择">请选择</option>
                    <option value="限时折扣">限时折扣</option>
                    <option value="拼团活动">拼团活动</option>
                    <option value="团购风暴">团购风暴</option>
                    <option value="预售活动">预售活动</option>
                </select>
            </dd>
        </dl>

        <button class="edit-module1" v-show="btnEdit1" v-on:click="edit()"><?= __('编辑'); ?></button>

        <input type="hidden" value="">

    </div>
     <div class="activity-edit">
            <ul class="sale-limit-items groupbuy" v-if="type=='groupbuy'">
                <li v-for="item in itemList" :key="item.groupbuy_name">
                    <em><img :src="item.groupbuy_image"></em>
                    <span class="one-overflow">{{item.groupbuy_name}}</span>
                    <strong>￥{{item.groupbuy_price}}</strong>
                </li>
            </ul>
            <ul class="sale-limit-items discount" v-if="type=='discount'">
                <li v-for="item in itemList" :key="item.goods_name">
                    <em><img :src="item.goods_image"></em>
                    <span class="one-overflow">{{item.goods_name}}</span>
                    <strong>￥{{item.discount_price}}</strong>
                </li>
            </ul>
            <ul class="sale-limit-items pintuan" v-if="type=='pintuan'">
                <li v-for="item in itemList" :key="item.goods_name">
                    <em><img :src="item.goods_image"></em>
                    <span class="one-overflow">{{item.goods_name}}</span>
                    <strong>￥{{item.price}}</strong>
                </li>
            </ul>
            <ul class="sale-limit-items presale" v-if="type=='presale'">
                <li v-for="item in itemList" :key="item.goods_name">
                    <em><img :src="item.goods_image"></em>
                    <span class="one-overflow">{{item.goods_name}}</span>
                    <strong>￥{{item.presale_price}}</strong>
                </li>
            </ul>
        
    </div>
</div>



<script>
    var vm=new Vue({
        el:"#activityA",
        data:{
            itemList:[],
            type:"",
            titleSel:"<?= __('请选择'); ?>",
            btnEdit1:false,
            newList:[],
            originType:""
        },
        mounted:function(){
            var that=this;
            console.log(frameElement.api);
          
            axios.get(BASE_URL+"?ctl=Mb_TplLayout&met=tplLayoutList&typ=json",{
                params:{
                    mb_tpl_layout_id:frameElement.api.data.item_data.mb_tpl_layout_id
                }
            }).then(function(res){
                that.type=res.data.data.items[0].mb_tpl_layout_data[0].type;
                that.originType=res.data.data.items[0].mb_tpl_layout_data[0].type;
                that.itemList=res.data.data.items[0].mb_tpl_layout_data[0].content_info;
                console.log(res.data.data.items[0].mb_tpl_layout_data[0].type);
                if(res.data.data.items[0].mb_tpl_layout_data[0].type){
                    that.btnEdit1=true;
                    switch(res.data.data.items[0].mb_tpl_layout_data[0].type){
                        case "groupbuy":
                        that.titleSel="团购风暴";
                        break;
                        case "discount":
                        that.titleSel="限时折扣";
                        break;
                        case "pintuan":
                        that.titleSel="拼团活动";
                        break;
                        case "presale":
                        that.titleSel="预售活动";
                        break;
                    }
                }
                
                
             }).catch(function(){
                console.log("fail");
            })
            var api = frameElement.api;
             
                var that=this;
                api.button({
                    id: "confirm",
                    name:"<?= __('确认'); ?>",
                    focus: true,
                    callback: function () {
                        var callback = frameElement.api.data.callback;
                        callback();
                        api.close();
                        
                        return false;
                    }
                },{
                    id: "cancel",
                    name:"<?= __('取消'); ?>",
                })
        },
        methods:{
            edit:function(){
                var forumType = $("#activity").find('option:selected').html();
                var forumNum = $("#activity").find('option:selected').val();
                var Url="";
                var discountUrl=BASE_URL+"/index.php?ctl=Promotion_Discount&met=getDiscountGoods&typ=e";
                var pinTuanUrl=BASE_URL+"/index.php?ctl=Promotion_PinTuan&met=gePinTuantGoods&typ=e";
                var groupBuyUrl=BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getGroupBuyGoods&typ=e";
                var presaleUrl=BASE_URL+"/index.php?ctl=Promotion_Presale&met=presale&typ=e";
                switch(forumNum){
                    case "<?= __('限时折扣'); ?>":
                    Url="url:"+discountUrl;
                    break;
                    case "<?= __('拼团活动'); ?>":
                    Url="url:"+pinTuanUrl;
                    break;
                    case "<?= __('团购风暴'); ?>":
                    Url="url:"+groupBuyUrl;
                    break;
                    case "<?= __('预售活动'); ?>":
                    Url="url:"+presaleUrl;
                    break;
                }
                var api = frameElement.api;
                $.dialog({
                    title:forumType,
                    dialogClass:'dialogFrame',
                    content:Url,
                    zIndex:9999,
                    data:{
                        iframe_id:api.data.item_id,
                        fromto:"1",
                        module:api.data.module,
                        forumType:forumType,
                        item_id:api.data.item_data.mb_tpl_layout_id,
                        data:api.data.item_data.mb_tpl_layout_data,
                        callback: function (){ window.location.reload(); }
                    },
                    width:800,
                    height: 500,
                    max: false,
                    min: false,
                    cache: false,
                });
            },
            change:function(){
                this.btnEdit1=true;
                var value = $("#activity").find('option:selected').val();
                switch (value) {
                    case "<?= __('限时折扣'); ?>":
                        this.type = "discount";
                        break;
                    case "<?= __('拼团活动'); ?>":
                        this.type = "pintuan";
                        break;
                    case "<?= __('团购风暴'); ?>":
                        this.type = "groupbuy";
                        break;
                    case "<?= __('团购风暴'); ?>":
                        this.type = "presale";
                        break;
                }
                if (this.type != this.originType) {
                    this.type = '';
                }
            }
        }
       
    })
    


</script>
