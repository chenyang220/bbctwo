<?php
/**
 * @category   Framework
 * @package    __init__
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2018远丰仁商
 * @version    2.0
 * @todo
 */
class OrderSeq
{
    //订单序列号表
    const YF_ORDER_GOODS_TABLE = 'OrderSeq';

    //数据库
    public $db;
    public $dbId = 'shop';

    public function __construct()
    {
        $this->db = new Yf_Model($this->dbId);
    }

    public function get_order_no($pre_str='Order_'){
        $time_now = time();
        $seq = $this->get_sequence($pre_str.date('Ymd', $time_now));
        $new_seq = sprintf('%04s', $seq);
        // 当天订单编号
        $order_no = $new_seq;
        return $order_no;
    }

    public function get_sequence($ids_key){
        $strSQL = <<<ENDSQL
        select _next("$ids_key") as $ids_key;
ENDSQL;
        $res = $this->db->sql->getAll($strSQL);
        //$res=$this->query($strSQL)->fetchAll();
        return $res[0][$ids_key];
    }

    //sql语句
    /*
     *
     * CREATE TABLE `yf_order_seq` (
  `prefix` varchar(64) NOT NULL DEFAULT '' COMMENT 'key',
  `n_current_value` bigint(20) unsigned NOT NULL DEFAULT '1' COMMENT 'value',
  `n_increment` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'increment',
  PRIMARY KEY (`prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='订单序列号表';
     */


//    public function createOrderSeq($prefix){
//
//    }

//    /**
//     * 获取现在商品版本号
//     * 是goods_id，要找到对应的common_i！切记~
//     * @param $goods_id
//     * @return int
//     * @throws
//     */
//    public function getGoodsVersion($goods_id)
//    {
//        $goods_table = TABEL_PREFIX.static::GOODS_TABLE;
//
//        $goodsModel = new Goods_BaseModel;
//        $goods = $goodsModel->getOne($goods_id);
//
//        $shop_id = $goods['shop_id'];
//        $cat_id = $goods['cat_id'];
//        $common_id = $goods['common_id'];
//
//        $select_sql = <<<EOF
//SELECT MAX(`version`) FROM `$goods_table` WHERE `goods_id`="$common_id"
//EOF;
//        $res = $this->db->sql->getAll($select_sql);
//
//        $version = current(current($res));
//        //当前商品未设置分佣比例则按平台默认分佣比例来结算，并写入商品分销表
//        if ($version === null) {
//            $version = 0;
//
//            $values = $this->getLatestGoodsValues($cat_id, $common_id);
//            $this->addGoods($version, $shop_id, $common_id, $values);
//        }
//
//        return $version;
//    }
//
//
//
//    /**
//     * 更新商品
//     * 目前分佣为三级，每次修改都增加三条记录，版本号加一
//     * `shop_id` INT (10) NOT NULL COMMENT '店铺id（冗余）',
//     * `goods_id` INT (10) NOT NULL COMMENT '商品id' 此goods_id指的是common_id,
//     * `version` INT (10) NOT NULL COMMENT '版本号',
//     * `level` TINYINT (1) NOT NULL COMMENT '分销级别',
//     * `value` DECIMAL (4, 2) unsigned NOT NULL COMMENT '分销比例',
//     *
//     * @param $data =
//     * array(cat_id, shop_id, goods_id, values=> array(5,3,1))
//     * @return boolean
//     */
//    public function updateGoods($data)
//    {
//        $this->checkGoodsConfig($data);
//
//        $cat_id = $data['cat_id'];
//        $values = $data['values'];
//        $this->checkMin($values);
//
//        $goods_table = TABEL_PREFIX.static::GOODS_TABLE;
//        $shop_id = $data['shop_id'];
//        $common_id = $data['goods_id'];
//
//        $select_sql = <<<EOF
//SELECT
//	`level`,
//	`value`,
//	`version`
//FROM
//	$goods_table
//WHERE
//	`goods_id` = $common_id
//GROUP BY
//	`level`,
//	`version`,
//	`value`
//HAVING
//	max(version) = (
//		SELECT
//			max(version)
//		FROM
//			$goods_table
//		WHERE
//			`goods_id` = $common_id
//	);
//EOF;
//        $rows = $this->db->sql->getAll($select_sql);
//        if (empty($rows)) {
//            $version = 0;
//            $this->addGoods($version, $shop_id, $common_id, $values);
//        } else {
//            $old_values = array_map(function ($row) {
//                return $row['value'];
//            }, $rows);
//
//            //有数据则比较，是否发生修改，修改就插入三条数据，版本号加一（即使只修改一条数据）
//            $diff = array_udiff_assoc($old_values, $values, function ($a, $b) {
//                if ($a == $b) {
//                    return 0;
//                }
//                return $a > $b ? 1 : -1;
//            });
//
//            if (empty($diff)) { //无修改，直接返回
//                return true;
//            } else {
//                $row = current($rows);
//                $version = $row['version'] + 1;
//                $this->addGoods($version, $shop_id, $common_id, $values);
//            }
//        }
//    }


}

