<?php 
if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author   nsy
 * @date     2019-10-14
 * @mark     一些不需要登录的前端接口，可以写在这里
 */
class CommonCtl extends Controller
{
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}
	
	
	/**
	 * 
	 *获取商城系统版本号信息
	 *@nsy 2019-10-14
	 */
	public function  shopVersion(){
		$version = SHOP_VERSION;
		!$version && $version = '3.0.0';
		$typ = request_string('typ');
		$data = array(
			'version'=>$version,
			//......
		);
		if ('json' == $typ) {
			return $this->data->addBody(-140, $data);
		}
		return $data;
	}
	
	/**
	 * 
	 *生成手机端二维码图片
	 *@nsy 2019-10-16
	 */
	public function wapQrcodeImg(){
		$typ = request_string('typ');
		//文件保存地址
		$outdir = APP_PATH . '/data/upload/wap';
		$base_url =Yf_Registry::get('base_url');
		$outfile= $outdir."/qr_img.png";
		$img_url = $base_url.'/shop/data/upload/wap/qr_img.png';
		$return = array(
			'img'=>''
		);
		if (file_exists($outfile)) {
           $return['img'] = $img_url;
        }
		if(!$return['img']){
			//手机端url
			$url = Yf_Registry::get('shop_wap_url');
			include  LIB_PATH."/phpqrcode/qrlib.php"; 

			//创建目录失败
			$flag = true;
			if (!file_exists($outdir)){
				if(!mkdir($outdir, 0777, true)){
					$flag = false;
				}
			}
			if($flag){
				QRcode::png($url,$outfile);
				$return['img'] = $img_url;
			}
		}
		if ('json' == $typ) {
			return $this->data->addBody(-140, $return);
		}else{
			return $return;
		}		
	}

	/**
	 * pc二维码管理
	 */
	public function qrCodeImage(){
		$data = array();
		$data['mobile_app'] = Web_ConfigModel::value('mobile_app');
		$data['mobile_wap'] = Web_ConfigModel::value('mobile_wap');
		$data['mobile_wx_code'] = Web_ConfigModel::value('mobile_wx_code');
		if(empty($data)){
			$outdir = APP_PATH . '/data/upload/wap';
			$base_url =Yf_Registry::get('base_url');
			$outfile= $outdir."/qr_img.png";
			$img_url = $base_url.'/shop/data/upload/wap/qr_img.png';
			$data = array(
				'img'=>''
			);
			if (file_exists($outfile)) {
	           $data['img'] = $img_url;
	        }
			if(!$data['img']){
				//手机端url
				$url = Yf_Registry::get('shop_wap_url');
				include  LIB_PATH."/phpqrcode/qrlib.php"; 

				//创建目录失败
				$flag = true;
				if (!file_exists($outdir)){
					if(!mkdir($outdir, 0777, true)){
						$flag = false;
					}
				}
				if($flag){
					QRcode::png($url,$outfile);
					$data['img'] = $img_url;
				}
			}
		}
		return $this->data->addBody(-140, $data);
	}
}

?>