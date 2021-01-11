<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="http://at.alicdn.com/t/font_136526_rc9m7tdbk1d.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/vue.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/axios.min.js" charset="utf-8"></script>
<!-- <?= __('引入样式'); ?> -->
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
<!-- <?= __('引入组件库'); ?> -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>

    <div class="sale-box" id="saleBox">
        <h4 class="sale-title"><?= __('标题：'); ?></h4>
        <div class="relative mb20">
            <input class="sale-tit-input" type="text" v-model="activeTitle" maxlength="4">
            <em class="sale-tit-limit"><b>{{activeTitle.length}}</b>/{{changenum}}</em>
        </div>
       <!-- <?= __('团购'); ?> -->
        <div  v-if="forumType=='groupbuy'">
            <h4 class="sale-title" v-if="contentList.length>0"><?= __('内容：'); ?></h4>
            <div class="items-box">
                <ul class="sale-limit-items">
                    <li v-for="(item,index) in contentList" :key="index" :id="item.goods_id">
                        <em><img :src="item.goods_image" :alt="item.goods_name"></em>
                        <span>{{item.goods_name}}</span>
                        <strong>{{item.goods_price}}</strong>
                        <button class="del-contentGood" v-on:click="delSale(index)"><?= __('删除'); ?></button>
                    </li>
                </ul>
            </div>
            <div class="mod-search cf">
                    <div>
                        <ul class="ul-inline clearfix">
                            <li class="fl mr10">
                                <input class="ui-input ui-input-ph matchCon" type="text" v-model="searchInput1" placeholder="<?= __('团购名称'); ?>...">
                            </li>
                            <li class="fl mr10">
                                <input type="text" id="redpacket_t_title" name="redpacket_t_title" class="ui-input ui-input-ph matchCon" v-model="searchInput2"  placeholder="<?= __('商品名称'); ?>">
                            
                            </li>
                            <li class="fl mr10">
                                <input type="text" id="redpacket_t_title" name="redpacket_t_title" class="ui-input ui-input-ph matchCon" v-model="searchInput3"  placeholder="<?= __('店铺名称'); ?>">
                            
                            </li>
                            <li class="fl"> <a class="ui-btn" v-on:click="btntgSearch" id="search"><?= __('查询'); ?><i class="iconfont icon-btn02"></i></a></li>
                        </ul>
                    </div>
            </div>
            <div class="ui-state-default ui-jqgrid-hdiv ui-corner-top">
                <table class="sale-table sale1" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th class="activity"><?= __('团购名称'); ?></th>
                                <th class="store"><?= __('商品名称'); ?></th>
                                <th class="img"><?= __('团购图片'); ?></th>
                                <th class="operate"><?= __('操作'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr v-for="(item,index) in tuangouList" :key="index">
                                    <td>{{item.groupbuy_name}}</td>
                                    <td>{{item.goods_name}}</td>
                                    <td><img :src="item.groupbuy_image"></td>
                                    <td><button class="btn-addgoods" v-on:click="addtgGoods(index)"><?= __('添加'); ?></button></td>
                                </tr>
                        </tbody>
                </table>
            </div>
        </div>
        <!-- <?= __('限时折扣'); ?> -->
        <div v-if="forumType=='discount'">
            <h4 class="sale-title"><?= __('内容：'); ?></h4>
            <div class="items-box">
                <ul class="sale-limit-items">
                    <li v-for="(item,index) in contentList" :key="index" :id="item.goods_id">
                        <em><img :src="item.goods_image" :alt="item.goods_name"></em>
                        <span>{{item.goods_name}}</span>
                        <strong>{{item.goods_price}}</strong>
                        <button v-on:click="delSale(index)"><?= __('删除'); ?></button>
                    </li>
                </ul>
            </div>
            <div class="mod-search cf">
                    <div>
                        <ul class="ul-inline clearfix">
                            <li class="fl mr10">
                                <input class="ui-input ui-input-ph matchCon" type="text" v-model="searchInput1" placeholder="<?= __('活动名称'); ?>...">
                            </li>
                            <li class="fl mr10">
                                <input type="text" id="redpacket_t_title" name="redpacket_t_title" class="ui-input ui-input-ph matchCon" v-model="searchInput2"  placeholder="<?= __('店铺名称'); ?>">
                            
                            </li>
                            <li class="fl"> <a class="ui-btn" v-on:click="btnSearch" id="search"><?= __('查询'); ?><i class="iconfont icon-btn02"></i></a></li>
                        </ul>
                    </div>
            </div>
            <table class="sale-table sale2" cellspacing="0" cellpadding="0" >
                    <thead>
                        <tr>
                            <th class="activity"><?= __('活动名称'); ?></th>
                            <th class="store"><?= __('店铺名称'); ?></th>
                            <th class="img"><?= __('商品图片'); ?></th>
                            <th class="pri"><?= __('商品价格'); ?></th>
                            <th class="sale"><?= __('折扣价格'); ?></th>
                            <th class="goodsprop"><?= __('商品规格'); ?></th>
                            <th class="operate"><?= __('操作'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr v-for="(item,index) in zhekouList" :key="index">
                                <td>{{item.activity}}</td>
                                <td>{{item.store}}</td>
                                <td><img :src="item.img"></td>
                                <td>{{item.pri}}</td>
                                <td>{{item.sale}}</td>
                                <td><span class="td-props one-overflow" :title="item.goodsprop">{{item.goodsprop}}</span></td>
                                <td><button class="btn-addgoods" v-on:click="addGoods(index)"><?= __('添加'); ?></button></td>
                            </tr>
                    </tbody>
            </table>
            <template>
                <div class="block">
                  <el-pagination
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                    :current-page="currentPage"
                    :page-sizes="[100, 200, 500]"
                    :page-size="100"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="totalnum">
                  </el-pagination>
                </div>
            </template>
     
        </div>
       

        <!-- <?= __('平台红包'); ?> -->
        <div v-if="forumType=='redpacket'">
            <h4 class="sale-title" v-if="contentList.length>0"><?= __('内容：'); ?></h4>
            <ul class="sale-redpacket-items clearfix">
                <li v-for="(item,index) in contentList" :key="index" :id="item.goods_id">
                    <em class="redpacket-img-box"><img :src="item.redpacket_t_img" :alt="item.goods_name"></em>
                    <div>
                        
                        <em><?= __('￥'); ?><strong>{{item.redpacket_t_price}}</strong></em><em class="sale-fit"><?= __('满'); ?>{{item.redpacket_t_orderlimit}}<?= __('可用'); ?></em>
                        <span>{{item.redpacket_t_title}}</span>
                        <b><?= __('限'); ?>{{item.redpacket_t_end_date_day}}<?= __('使用'); ?></b>
                        <p>{{item.redpacket_t_used}}<?= __('张已被领取'); ?></p>
                    </div>
                    
                    <button class="del-contentGood" v-on:click="delSale(index)"><?= __('删除'); ?></button>
                </li>
            </ul>
            <div class="mod-search cf">
                    <div>
                        <ul class="ul-inline clearfix">
                            <li class="fl mr10">
                                <input class="ui-input ui-input-ph matchCon" type="text" v-model="searchInput1" placeholder="<?= __('活动名称'); ?>...">
                            </li>
                            <li class="fl mr10">
                                <input type="text" id="redpacket_t_title" name="redpacket_t_title" class="ui-input ui-input-ph matchCon" v-model="searchInput2"  placeholder="<?= __('店铺名称'); ?>">
                            
                            </li>
                            <li class="fl"> <a class="ui-btn" v-on:click="btnSearch" id="search"><?= __('查询'); ?><i class="iconfont icon-btn02"></i></a></li>
                        </ul>
                    </div>
            </div>
            <table class="sale-table sale3" cellspacing="0" cellpadding="0" >
                    <thead>
                        <tr>
                            <th class="activity"><?= __('红包名称'); ?></th>
                            <th class="img"><?= __('红包图片'); ?></th>
                            <th class="pri"><?= __('面额'); ?></th>
                            <th class="sale"><?= __('消费限额'); ?></th>
                            <th class="operate"><?= __('操作'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr v-for="(item,index) in hongbaoList" :key="index">
                                <td>{{item.redpacket_t_title}}</td>
                                <td><img :src="item.redpacket_t_img"></td>
                                <td>{{item.redpacket_t_price}}</td>
                                <td>{{item.redpacket_t_orderlimit}}</td>
                                <td><button class="btn-addgoods" v-on:click="addhbGoods(index)"><?= __('添加'); ?></button></td>
                            </tr>
                    </tbody>
            </table>
            <template>
                <div class="block">
                  <el-pagination
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                    :current-page="currentPage"
                    :page-sizes="[100, 200, 500]"
                    :page-size="100"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="totalnum">
                  </el-pagination>
                </div>
            </template>
        </div> 
         
    </div>
    <script>
       
        var vm=new Vue({
            el:"#saleBox",
            data:{
                changenum:"4",
                // <?= __('活动名称：'); ?>
                activeTitle:"",
                // <?= __('活动类型'); ?>
                forumType:'',

                searchInput1:"",
                searchInput2:"",
                searchInput3:"",
                // <?= __('分页'); ?>
                totalnum:400,
                currentPage:1,
                // <?= __('内容'); ?>
                contentList:[],
                //团购'); ?>
                tuangouList:[],
                // <?= __('限时折扣'); ?>
                zhekouList:[],
                // <?= __('平台红包'); ?>
                hongbaoList:[],
                // <?= __('代金券'); ?>
                voucherList:[],
                // <?= __('人气推荐'); ?>
                hotList:[]
           },
            mounted:function(){
                //获取活动内容'); ?>
                // var api =window.parent.document.getElementsByTagName("iframe")[3].api;
                // console.log(window.parent.document.getElementsByTagName("iframe")[3]);
                var api = frameElement.api;
                console.log(api);
                var that=this;
                axios.get(BASE_URL+"/index.php?ctl=Forum&met=getForumContent&typ=json&id="+api.data).then(function(res){
               //axios.get(BASE_URL+"/index.php?ctl=Forum&met=getForumContent&typ=json&id=1").then(function(res){
                    console.log(res);
                    that.contentList=res.data.data.content;
                    that.activeTitle=res.data.data.forum_name;
                    that.forumType=res.data.data.forum_type;
                    //获取对应商品列表'); ?>

                     switch(that.forumType){
                         case 'groupbuy':
                            axios.get(BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getGroupBuyGoodsList&typ=json&groupbuy_state=2").then(function(res){
                                that.tuangouList=res.data.data.items;
                            })
                            break;
                        case 'discount':
                            axios.get(BASE_URL+"/index.php?ctl=Promotion_DiscountCtl&met=getDiscountGoodsList&typ=json&discount_state=1").then(function(res){
                                that.zhekouList=res.data.data.items;
                            })
                            break;
                        case 'redpacket':
                            axios.get(BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=getRedPacketTempList&typ=json&redpacket_t_state=1").then(function(res){
                                that.hongbaoList=res.data.data.items;
                            })
                            break;
                        case 'voucher':
                            axios.get(BASE_URL+"/index.php?crl=Promotion_Voucher&met=getVoucherTempList&typ=json").then(function(res){
                                that.voucherList=res.data.data.items;
                            })
                            break;
                        case 'recommend':
                            axios.get(BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getGroupBuyGoodsList&typ=json&groupbuy_state=2").then(function(res){
                                that.hotList=res.data.data.items;
                            })
                            break;
                    }
                })
               
                
                
            },
            methods:{
                // <?= __('添加团购内容商品'); ?>
                addtgGoods:function(idx){
                    if(this.contentList.length<15){
                        this.contentList.push({
                            goods_image:this.tuangouList[idx].groupbuy_image,
                            goods_name:this.tuangouList[idx].groupbuy_name,
                            goods_price:this.tuangouList[idx].goods_price
                        })
                        
                    }else{
                        alert("<?= __('最多添加'); ?>15<?= __('个商品'); ?>")
                    }
                    return false;
                },
                // <?= __('添加红包内容商品'); ?>
                addhbGoods:function(idx){
                    if(this.contentList.length<15){
                        this.contentList.push({
                            redpacket_t_img:this.hongbaoList[idx].redpacket_t_img,
                            redpacket_t_price:this.hongbaoList[idx].redpacket_t_price,
                            redpacket_t_orderlimit:this.hongbaoList[idx].redpacket_t_orderlimit,
                            redpacket_t_title:this.hongbaoList[idx].redpacket_t_title,
                            redpacket_t_end_date_day:this.hongbaoList[idx].redpacket_t_end_date_day,
                            redpacket_t_used:this.hongbaoList[idx].redpacket_t_used
                        })
                        
                    }else{
                        alert("<?= __('最多添加'); ?>15<?= __('个商品'); ?>")
                    }
                    return false;
                },
                // <?= __('删除内容商品'); ?>
                delSale:function(idx){
                    this.contentList.splice(idx,1);
                },
                // <?= __('搜索'); ?>
                btntgSearch:function(){
                    var that=this;
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getGroupBuyGoodsList&typ=json&groupbuy_state=2",{
                        params:{
                            groupbuy_name:that.searchInput1,
                            goods_name:that.searchInput2,
                            shop_name:that.searchInput3
                        }
                    }).then(function(res){
                        that.tuangouList=res.data.data.items;
                    })
                    
                },
                // <?= __('组件'); ?>
                handleSizeChange(val) {
                    console.log(`<?= __('每页'); ?> ${val} <?= __('条'); ?>`);
                },
                handleCurrentChange(val) {
                    console.log(`<?= __('当前页'); ?>: ${val}`);
                }
                
            }
        })
    </script>
