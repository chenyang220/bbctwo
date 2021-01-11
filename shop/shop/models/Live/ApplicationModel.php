<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 *
 *
 * @category   Framework
 * @package    __init__
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2010远丰仁商
 * @version    1.0
 * @todo
 */
class Live_ApplicationModel extends Live_Application
{
    const CHECK = 1;//通过
    const PASS = 2;//通过
    const NO_PASS = 3;//未通过
    const STOP = 4;//关闭


}

?>