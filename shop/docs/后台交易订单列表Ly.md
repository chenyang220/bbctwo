# 交易订单列表 API

key | value
------|------------
负责人 | 李宇
Email  | rd06@yuanfeng021.com

### 商品订单 | 虚拟订单
#### 请求URL
yf_shop_admin/index.php?ctl=Trade_Order&met=getOrderList&typ=json

#### 请求参数
键值 | 类型 | 描述
------|------|--------------
无

#### 返回数据
```json
{
    "cmd_id": -140,
    "status": 200,
    "msg": "success",
    "data": {
          `order_id` varchar(50) NOT NULL COMMENT '订单号',
          `shop_id` int(10) NOT NULL COMMENT '卖家店铺id',
          `shop_name` varchar(50) NOT NULL COMMENT '卖家店铺名称',
          `buyer_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '买家id',
          `buyer_user_name` varchar(50) NOT NULL COMMENT '买家姓名',
          `seller_user_id` int(10) unsigned NOT NULL COMMENT '卖家id',
          `seller_user_name` varchar(50) NOT NULL,
          `order_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '订单日期',
          `order_create_time` datetime NOT NULL COMMENT '订单生成时间',
          `order_receiver_name` varchar(50) NOT NULL COMMENT '收货人的姓名',
          `order_receiver_address` varchar(255) NOT NULL COMMENT '收货人的详细地址',
          `order_receiver_contact` varchar(50) NOT NULL COMMENT '收货人的联系方式',
          `order_receiver_date` datetime NOT NULL COMMENT '收货时间（最晚收货时间）',
          `payment_id` varchar(50) NOT NULL COMMENT '支付方式id',
          `payment_name` varchar(50) NOT NULL COMMENT '支付方式名称',
          `payment_time` datetime NOT NULL COMMENT '支付(付款)时间',
          `payment_number` varchar(20) NOT NULL COMMENT '支付单号',
          `payment_other_number` varchar(20) NOT NULL COMMENT '第三方支付平台交易号',
          `order_seller_name` varchar(50) NOT NULL COMMENT '发货人的姓名',
          `order_seller_address` varchar(255) NOT NULL COMMENT '发货人的地址',
          `order_seller_contact` varchar(50) NOT NULL COMMENT '发货人的联系方式',
          `order_shipping_time` datetime NOT NULL COMMENT '配送时间',
          `order_shipping_express_id` smallint(3) NOT NULL DEFAULT '0' COMMENT '配送公司ID',
          `order_shipping_code` varchar(50) NOT NULL COMMENT '物流单号',
          `order_shipping_message` varchar(255) NOT NULL COMMENT '卖家备注',
          `order_finished_time` datetime NOT NULL COMMENT '订单完成时间',
          `order_invoice` varchar(100) NOT NULL COMMENT '发票信息',
          `order_goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品总价格',
          `order_payment_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付金额',
          `order_discount_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '折扣价格',
          `order_point_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '买家使用积分',
          `order_shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费价格',
          `order_buyer_evaluation_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '买家评价状态 0-未评价 1-已评价',
          `order_buyer_evaluation_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '评价时间',
          `order_seller_evaluation_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '卖家评价状态 0为评价，1已评价',
          `order_seller_evaluation_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '评价时间',
          `order_message` varchar(255) NOT NULL DEFAULT '' COMMENT '订单留言',
          `order_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态',
          `order_points_add` int(10) NOT NULL DEFAULT '0' COMMENT '订单赠送积分',
          `voucher_id` int(10) NOT NULL COMMENT '代金券id',
          `voucher_price` int(10) NOT NULL COMMENT '代金券面额',
          `voucher_code` varchar(32) NOT NULL COMMENT '代金券编码',
          `order_refund_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '退款状态:0是无退款,1是退款中,2是退款完成',
          `order_return_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '退货状态:0是无退货,1是退货中,2是退货完成',
          `order_refund_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
          `order_return_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退货数量',
          `order_from` enum('1','2') NOT NULL DEFAULT '1' COMMENT '手机端',
          `order_commission_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '交易佣金',
          `order_commission_return_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '交易佣金退款',
          `order_is_virtual` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟订单',
          `order_virtual_code` varchar(100) NOT NULL DEFAULT '' COMMENT '虚拟商品兑换码',
          `order_virtual_use` tinyint(1) NOT NULL DEFAULT '0' COMMENT '虚拟商品是否使用 0-未使用 1-已使用',
          `order_shop_hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '卖家删除',
          `order_buyer_hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '买家删除',
          `order_cancel_identity` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单取消者身份   1-买家 2-卖家 3-系统',
          `order_cancel_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '订单取消原因',
          `order_cancel_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '订单取消时间',
          `order_shop_benefit` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺优惠',
           goods_list: {
                buyer_user_id: 0
                common_id: 0
                evaluation_count: 0
                goods_class_id: 100013
                goods_id: 338
                goods_image: "http://demo.bbc-builder.com/uploadfile/goods/91/2015/02/04/1423013735.jpg"
                goods_name: "中粮屯河 番茄酱198g*24罐(复合调味酱) 天然番茄 官方正品包邮 "
                goods_price: 110
                goods_refund_status: 0
                goods_refund_status_con: "无退货"
                id: 4
                order_goods_adjust_fee: 0
                order_goods_amount: 110
                order_goods_benefit: ""
                order_goods_commission: 0
                order_goods_discount_fee: 0
                order_goods_evaluation_status: 0
                order_goods_id:4
                order_goods_num:1
                order_goods_payment_amount:0
                order_goods_point_fee:0
                order_goods_returnnum:0
                order_goods_status:2
                order_goods_time:"0000-00-00 00:00:00"
                order_id:1429597895
                order_spec_info:null
                shop_id:7
                spec_id:0
          }
    }
}
```


### 相册列表
#### 请求URL
yf_shop_admin/index.php?ctl=Goods_Album&met=getAlbumList&typ=json

#### 请求参数
键值| 类型 | 描述
------|------|--------------

#### 返回数据
```json
{
    "cmd_id": -140,
    "status": 200,
    "msg": "success",
    "data": {
          `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片编号',
          `image_name` varchar(50) NOT NULL COMMENT '图片名称',
          `image_origin_name` varchar(50) NOT NULL COMMENT '图片原始名称',
          `image_width` int(10) unsigned NOT NULL COMMENT '图片宽度',
          `image_height` int(10) unsigned NOT NULL COMMENT '图片高度',
          `image_size` int(10) unsigned NOT NULL COMMENT '图片大小',
          `shop_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
          `upload_time` int(10) unsigned NOT NULL COMMENT '上传时间',
           is_default : false 时候为默认相册
    }
}
```


### 相册列表
#### 请求URL
yf_shop_admin/index.php?ctl=Goods_Album&met=getImageList

#### 请求参数
键值| 类型 | 描述
------|------|--------------

#### 返回数据
```json
{
    "cmd_id": -140,
    "status": 200,
    "msg": "success",
    "data": {
        `upload_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品图片Id',
        `album_id` bigint(20) NOT NULL,
        `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
        `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺id',
        `upload_url_prefix` varchar(255) NOT NULL DEFAULT '',
        `upload_path` varchar(255) NOT NULL DEFAULT '',
        `upload_url` varchar(255) NOT NULL COMMENT '附件的url   upload_url = upload_url_prefix  + upload_path',
        `upload_thumbs` text NOT NULL COMMENT 'JSON存储其它尺寸',
        `upload_original` varchar(255) NOT NULL DEFAULT '' COMMENT '原附件',
        `upload_source` varchar(255) NOT NULL DEFAULT '' COMMENT '源头-网站抓取',
        `upload_displayorder` tinyint(4) NOT NULL DEFAULT '50' COMMENT '排序',
        `upload_type` enum('video','other','image') NOT NULL DEFAULT 'image' COMMENT 'image|video|',
        `upload_image_spec` int(10) NOT NULL DEFAULT '0' COMMENT '规格',
        `upload_size` int(10) NOT NULL COMMENT '原文件大小',
        `upload_mime_type` varchar(100) NOT NULL DEFAULT '' COMMENT '上传的附件类型',
        `upload_metadata` text NOT NULL,
        `upload_name` text NOT NULL COMMENT '附件标题',
        `upload_time` int(10) NOT NULL COMMENT '附件日期',
    }
}
```