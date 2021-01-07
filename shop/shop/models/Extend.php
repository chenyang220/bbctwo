<?php

/**
 * Meta 管理者类
 *
 * 负责初始化并存放所有的Meta类。
 *
 * @category   Framework
 * @package    Meta
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2010远丰仁商
 * @version    1.0
 * @todo
 */
final class Extend
{
	/**
	 * 存储已经实例后的Meta对象
	 *
	 * @staticvar Zero_Meta
	 */
	public static $ext = array();

	/**
	 * 获得配置的Meta实例
	 *
	 * @param string $name 缓存配置key
	 * @return Yf_Extend_Interface
	 */
	static public function create($name = 'User_ExtendModel')
	{
		if (!isset(self::$ext[$name]))
		{
			self::$ext[$name] = new $name();
		}

		return self::$ext[$name];
	}

	private function __construct()
	{
	}
}

?>