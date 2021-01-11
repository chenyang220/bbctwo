<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Distribution_NewBuyer_UploadWapCtl extends Yf_AppController
{
	/**
     * Constructor
     *
     * @param  string $ctl 控制器目录
     * @param  string $met 控制器方法
     * @param  string $typ 返回数据类型
     * @access public
     */
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
    }

	//店铺LOGO编辑
	public function imgUpload(){
		$image_type = array(
				"image/jpeg",
				"image/pjpeg",
				"image/png",
				"image/gif"
			);
		$image = $_FILES['picture'];
		$type = $image['type'];
		$file_error = $image['error'];
		$file_name = $image['name'];
		$file_tmp_name = $image['tmp_name'];
		$dir_path = APP_DIR_NAME .'/data/upload/media/wap/';
		
		$data=array();
		if(isset($image) && $file_error == 0){
			if(in_array($type,$image_type)){
				$thefile = $dir_path.uniqid('wap_').strrchr($file_name, ".");
				if (is_dir($dir_path) || mkdir($dir_path, 0755, true)) {

			    }else{
			    	echo "文件夹没有写入权限";
			    	exit();
			    }
				if(move_uploaded_file($file_tmp_name,$thefile)){
					$data['file_path'] = 
					    Yf_Registry::get('shop_api_url') . '/'.$thefile;
				}else{
					$msg = __('图片上传失败');
					$status = 250;
				}
			}else{
				return $this->data->addBody(-140, array(), '图片格式有误', '250');
			}
		}else{
			 switch($file_error){
	            case 1:
	            $msg = '上传文件超过了PHP配置文件中upload_max_filesize选项的值';
	                break;
	            case 2:
	                $msg = '超过了表单max_file_size限制的大小';
	                break;
	            case 3:
	                $msg = '文件部分被上传';
	                break;
	            case 4:
	                $msg = '没有选择上传文件';
	                break;
	            case 6:
	                $msg = '没有找到临时文件';
	                break;
	            case 7:
	            case 8:
	                $msg = '系统错误';
	                break;
	        }
	        $status = 250;
		}
		return $this->data->addBody(-140, $data, $msg, $status);
	}
}