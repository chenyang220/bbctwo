<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
include TPL_PATH . '/' . 'header.php';
?>

<link rel="stylesheet" href="http://at.alicdn.com/t/font_136526_rc9m7tdbk1d.css">
<link rel="stylesheet" href="<?= $this->view->css ?>/add.css">
<script type="text/javascript" src="<?= $this->view->js_com ?>/vue.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/axios.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/qs.min.js" charset="utf-8"></script>
<div class="fast-enter-box fl" id="fistEnter">
    <h4 class="clearfix">
        <button class="fr btn-default" v-on:click="restoreDefault"><i class="iconfont icon-shuaxin"></i><em class="normal">快捷默认设置</em></button>
    </h4>
    <div class="fast-enter-overflow">
        <div class="fast-enter-items">

            <div v-for="(item,index) in fastList" :key="index" class="fast-enter-item">
                <div class="fast-enter-position">
                    <em v-on:click="moduleUp(index)"><i class="iconfont icon-up"></i></em>
                    <em v-on:click="moduleDown(index)"><i class="iconfont icon-down"></i></em>
                </div>
                <div class="fast-enter-input clearfix">
                    <div class="img-box fl"><input v-on:change="fileImage(index,$event)" class="inputfile" type="file"><img v-on:click="imgEdit" v-if="item.imgInput=='false'" :src="item.icons" alt=""><em v-if="item.imgInput=='true'"><b class="icon-must">*</b><i class="iconfont icon-btn03"></i></em><span>建议尺寸80*80</span></div>
                    <div class="fl fast-enter-cont">
                        <dl>
                            <dt><b>*</b>导航名称：</dt>
                            <dd><input type="text" v-model="item.navName" maxlength="4"></dd>
                        </dl>
                        <dl>
                            <dt><b>*</b>目&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;标：</dt>
                            <dd>
                                <select name="opts" v-model="item.select" v-on:change="checkselect(index,$event,this)">
                                    <option v-for="(opt,index) in optionList">{{opt}}</option>
                                </select>
                            </dd>
                        </dl>
                        <dl v-show="item.linkShow">
                            <dt><b>*</b>链&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;接：</dt>
                            <dd><input type="text" placeholder="http://" v-model="item.url"></dd>
                        </dl>
                    </div>

                </div>
                <button class="btn-module-del" v-on:click="delModule(index)"><i class="iconfont icon-guanbifuzhi"></i></button>
            </div>

        </div>
        <div class="clearfix pr20">
            <button class="btn-add-module fr" v-show="btnShow" v-on:click="addList"><i class="iconfont icon-btn03"></i></button>
        </div>
    </div>
</div>
<script>
    var vm = new Vue({
        el: "#fistEnter",
        data: {
            btnShow: true,
            enteranceShow: true,
            postaccount: {},
            default: {
                navName: "",
                icons: "",
                select: "",

            },
            fastList: {},
            optionList: [
                "积分商城",
                "团购中心",
                "店铺精选",
                "拼团中心",
                "平台红包",
                "代金券",
                "限时折扣",
                "收藏中心",
                "订单中心",
                "购物车",
                "资讯",
                "plus会员",
                "砍价",
                "专题",
                "自定义链接"
            ],
            imgList: [
                "/images/icons/jifen.png",
                "/images/icons/tuangou.png",
                "/images/icons/store.png",
                "/images/icons/pintuan.png",
                "/images/icons/redpackets.png",
                "/images/icons/voucher.png",
                "/images/icons/time.png",
                "/images/icons/save.png",
                "/images/icons/orders.png",
                "/images/icons/cart.png",
                "/images/icons/zixun.png",
                "/images/icons/plus.png",
                "/images/icons/bargain.png",
                "/images/icons/special.png"
            ],
           urlList: [
                "tmpl/integral.html",
                "tmpl/group_buy_index.html",
                "tmpl/store-list.html",
                "tmpl/pintuan_index.html",
                "tmpl/redpacket_plat.html",
                "tmpl/voucher_list.html",
                "tmpl/discount_list.html",
                "tmpl/member/favorites.html",
                "tmpl/member/order_list.html",
                "tmpl/cart_list.html",
                "tmpl/information_news_list.html",
                "tmpl/member/plus_index.html",
                "tmpl/bargain_list.html",
                "tmpl/special_column.html"
           ],
            defaultList: [
                {icons: "https://uploader.local.yuanfeng021.com/image.php/media/d3aabd05be45670d48e2685d1e1f5992/10297/76/image/20180917/1537155307827401.png", navName: "积分商城", select: "积分商城", url: "tmpl/integral.html", btnShow: false, imgInput: 'false'},
                {icons: "https://uploader.local.yuanfeng021.com/image.php/media/d3aabd05be45670d48e2685d1e1f5992/10297/76/image/20180917/1537155181835260.png", navName: "团购中心", select: "团购中心", url: "tmpl/group_buy_index.html", btnShow: false, imgInput: 'false'},
                {icons: "https://uploader.local.yuanfeng021.com/image.php/media/d3aabd05be45670d48e2685d1e1f5992/10297/76/image/20180917/1537155191337651.png", navName: "店铺精选", select: "店铺精选", url: "tmpl/store-list.html", btnShow: false, imgInput: 'false'},
                {icons: "https://uploader.local.yuanfeng021.com/image.php/media/d3aabd05be45670d48e2685d1e1f5992/10297/76/image/20180917/1537155205788982.png", navName: "拼团中心", select: "拼团中心", url: "tmpl/pintuan_index.html", btnShow: false, imgInput: 'false'},
                {icons: "https://uploader.local.yuanfeng021.com/image.php/media/d3aabd05be45670d48e2685d1e1f5992/10297/76/image/20180917/1537155209227035.png", navName: "平台红包", select: "平台红包", url: "tmpl/redpacket_plat.html", btnShow: false, imgInput: 'false'},
            ]
        },
        mounted: function () {
            var api = frameElement.api;
            console.log(api);
            // var api =window.parent.document.getElementsByTagName("iframe")[2].api;
            // console.log(window.parent.document.getElementsByTagName("iframe")[2]);
            console.log(api.data.item_data.mb_tpl_layout_data);
            var that = this;
            that.fastList = api.data.item_data.mb_tpl_layout_data;
            console.log(that.fastList);

            api.button({
                id: "confirm",
                name: "确认",
                focus: true,
                callback: function () {
                    that.lastAccount();
                    console.log(that.postaccount);
                    for (var i in that.postaccount) {
                        if (that.postaccount[i].icons == " " || that.postaccount[i].navName == " " || that.postaccount[i].select == " " || that.postaccount[i].url == " ") {
                            alert("请填充完整");
                            return false;
                        }
                    }
                    axios.post(BASE_URL + "/index.php?ctl=Mb_TplLayout&met=editTplLayout&typ=json", Qs.stringify({
                        item_id: api.data.item_id,
                        layout_data: that.postaccount
                    }, {indices: false})).then(function (res) {
                        console.log("success");
                        api.close();
                    }).catch(function (res) {
                        console.log("fail");
                    })
                    return false;
                }
            })
        },
        methods: {
            //增加编辑框

            addList: function () {
                this.fastList.push({
                    navName: this.default.navName,
                    select: this.default.select,
                    imgInput: 'true',
                    icons: " ",
                    url: " "
                })
                console.log(this.fastList);

                if (this.fastList.length >= 10) {
                    this.btnShow = false;
                    return false;
                }
            },
            //删除编辑框
            delModule: function (idx) {
                var that = this;
                $.dialog.confirm("确定删除吗？", function () {
                    that.fastList.splice(idx, 1);
                    if (that.fastList.length < 10) {
                        that.btnShow = true;
                    }
                });


            },
            // 重新上传图片
            imgEdit: function () {

            },
            //上移编辑框
            moduleUp: function (idx) {
                if (idx - 1 >= 0) {
                    var prev = this.fastList[idx - 1];
                    Vue.set(this.fastList, idx - 1, this.fastList[idx]);
                    Vue.set(this.fastList, idx, prev);
                }


            },
            //下移编辑框
            moduleDown: function (idx) {
                if (idx + 1 <= this.fastList.length - 1) {
                    var next = this.fastList[idx + 1];
                    Vue.set(this.fastList, idx + 1, this.fastList[idx]);
                    Vue.set(this.fastList, idx, next);
                }

            },
            lastAccount: function () {
                console.log(this.fastList);
                for (var a = 0; a < this.fastList.length; a++) {
                    this.postaccount[a] = this.fastList[a];
                }
            },
            //监听select改变，改变对应名称
            checkselect: function (idx, ele, obj) {
                if (ele.target.value == "自定义链接") {
                    var islist = this.imgList.indexOf(this.fastList[idx].icons);
                    if (islist != -1) {
                        this.fastList[idx].icons = " ";
                        this.fastList[idx].navName = " ";
                        this.fastList[idx].imgInput = 'true';
                    }

                    this.fastList[idx].linkShow = true;
                } else {
                    this.fastList[idx].icons = this.imgList[ele.target.selectedIndex];
                    this.fastList[idx].url = this.urlList[ele.target.selectedIndex];
                    this.fastList[idx].navName = ele.target.value;
                    // this.fastList[idx].linkShow=false;
                    this.fastList[idx].imgInput = 'false';
                }

            },
            //恢复默认设置
            restoreDefault: function () {
                this.fastList = this.defaultList;
                //this.fastList=JSON.parse(JSON.stringify(this.defaultList));
            },
            //上传图片
            fileImage: function (index, e) {
                var that = this;
                var file = e.target.files[0];
                var imgSize = file.size / 1024;
                if (imgSize > 200) {
                    alert('请上传大小不要超过200KB的图片')
                } else {
                    var reader = new FileReader();
                    reader.readAsDataURL(file); //读出 base64
                    reader.onloadend = function () {
                        //图片的 base64格式,可以直接当成 img的 src属性值
                        var dataURL = reader.result;
                        console.log(dataURL);
                        that.fastList[index].icons = dataURL;
                        that.fastList[index].imgInput = 'false';
                    };
                }

            }

        }

    })
</script>