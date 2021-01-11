<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Web_ConfigModel extends Web_Config
{

	private static $_instance;

	/**
	 * 读取分页列表
	 *
	 * @param  int $config_key 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getConfigList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		return $this->getByWhere($cond_row, $order_row, $page, $rows);
	}

	/*
	 * 获取config
	 */
	public static function value($key, $default = false)
	{
		if (!@(self::$_instance instanceof self))
		{
			self::$_instance = new self();
		}

		return self::$_instance->getConfigValue($key, $default);
	}

	/*
	 * 获取config
	 */
	public function getConfigValue($key, $default = false)
	{
		if (Yf_Registry::isRegistered($key))
		{
			return Yf_Registry::get($key);
		}
		else
		{
			$config_row = array();

			$config_rows = $this->getConfig($key);

			if ($config_rows)
			{
				$config_row = array_pop($config_rows);

				if ('json' == $config_row['config_datatype'])
				{
					$config_row['config_value'] = decode_json($config_row['config_value']);
				}

				Yf_Registry::set($key, $config_row['config_value']);
				$val = $config_row['config_value'];
			}
			else
			{
				$val = $default;
			}

			return $val;
		}
	}

	/**
     * 查询会员权益
     */
	public function getPlusConfigStr($key = 'plus'){
        $sql = " select config_key,config_value from ".$this->_tableName." where config_type='{$key}'";
        return $this->sql->getAll($sql);
    }

    /**
     *
     * 获取公众号token
     */
    public  function getWxPublicAccessToken(){
        $appid              = self::value('wechat_public_appid');
        $secret             = self::value('wechat_public_secret');
        if(!$appid || !$secret){
            return array();
        }
        //wxpublic_access_token
        $wxpublic_access_token = self::value('wxpublic_access_token');
        $arr =array();
        if($wxpublic_access_token){
            $arr = explode("|",$wxpublic_access_token);
        }
        $token =array();
        if(!$arr[0] || time()-$arr[1]>0){//token不存在或者已过期
            $data  = getAccToken($appid,$secret);
            if(!$data)return array();
            $config_rows = $this->getByWhere(array('config_type' => 'wxpublic_access','config_key'=> 'wxpublic_access_token'));
            if(!$config_rows){
                $add_row = array();
                $add_row['config_key'] = 'wxpublic_access_token';
                $add_row['config_value'] = $data['access_token']."|".((time()+$data['expires_in'])-300);
                $add_row['config_type'] = 'wxpublic_access';
                $add_row['config_enable'] = 1;
                $add_row['config_datatype'] = 'string';
                $this->addConfig($add_row);
            }else{
                $edit_row = array();
                $edit_row['config_value'] = $data['access_token']."|".((time()+$data['expires_in'])-300);
                $this->editConfig('wxpublic_access_token', $edit_row);
            }
            $token['token'] = $data['access_token'];
        }else{
            $token['token'] =  $arr[0];
        }
        return $token;

    }
}

?>