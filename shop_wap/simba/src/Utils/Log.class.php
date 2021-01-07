<?php

/*
 * 文件日志
 *  
 * @author      Brant <381114687@qq.com>
 * @link       
 */

namespace simba\oauth\Utils;

class Log {

    // 日期格式
    public static $format = '[ c ]';
    public static $log_file_size = '1048576'; //1M
    private $destination;
    
    public function __construct($destination) {
        $this->destination = $destination;
    }

    public function write($message, $extra = '') {
        if (!is_string($message)){
            $message = json_encode($message);
        }
        $now = date(self::$format);
        $destination = $this->destination;
        if (empty($destination)) {
            if (!empty(LOG_PATH)) {
                $log_path = LOG_PATH ? LOG_PATH : "";
                @mkdir($log_path, 0777, true);
            } else {
                $log_path = '';
            }
            $destination = $log_path . date('y_m_d') . '.log';
        }

        //检测知识文件大小，超过配置大小则备份知识文件重新生成
        if (is_file($destination) && self::$log_file_size <= filesize($destination)) {
            rename($destination, dirname($destination) . '/' . date('Y-m-d-H-i-s') . '-' . basename($destination));
        }
    }

}
