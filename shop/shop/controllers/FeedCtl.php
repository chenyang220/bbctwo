<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class FeedCtl extends Yf_AppController
{
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);

		$this->feedGroupModel    = new Feed_GroupModel();
		$this->feedBaseModel     = new Feed_BaseModel();
	}
	//海报下载
	 public function uploadFile(){
        $image = request_string('image');
        $imageName = date("ymd",time())."_".rand(1111,9999).'.png';
        if (strstr($image,",")){
            $image = explode(',',$image);
            $image = $image[1];
        }
        $dir_path = APP_PATH.'/data/upload/haibao/'.date("Ymd",time()).'/';
        // var_dump($dir_path);die;
        if (!file_exists($dir_path)){ //判断目录是否存在 不存在就创建
            mkdir($dir_path, 0777, true);
        }
        $imageSrc= $dir_path.'/'.$imageName; //图片名字
        $r = file_put_contents($imageSrc, base64_decode($image));//返回的是字节数
        $data = array();
        if ($r) {
            $url = Yf_Registry::get('base_url') . '/' . APP_DIR_NAME . '/data/upload/haibao/'.date("Ymd",time()).'/'.$imageName;
            $data['imgUrl'] = $url;
            $status = 200;
        }else{
            $status = 250;
        }
        $this->data->addBody(-140, $data,'生成图片',$status);

    }
	//反馈插入
	public function addFeed()
	{
		//问题插入
		if (request_string('feed_desc'))
		{
			$feedback = array(
				"feed_group_id" => 0,
				"feed_desc" => request_string('feed_desc'),
				"feed_url" => request_string('feed_url'),
				"user_id" => request_int('u')
			);
			$rs = $this->feedBaseModel->addBase($feedback);
			$url = 'index.php?ctl=Index&met=feedback';
			//location_to($url);
		}

		if ('json' == $this->typ)
		{
			$this->data->addBody(-140, array());
		}
		else
		{
			include $this->view->getView();
		}
	}
}

?>