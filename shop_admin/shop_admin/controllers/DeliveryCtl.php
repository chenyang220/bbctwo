<?php
if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author    tech40@yuanfeng021.com
 * 统计数据初始化
 *
 */
class DeliveryCtl extends AdminController
{

    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

    }


}

?>
