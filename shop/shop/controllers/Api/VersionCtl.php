<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}
//版本号

 
class Api_VersionCtl extends Yf_AppController
{

	public function index()
	{

        $ver = include ROOT_PATH."/pack/version.php";
        echo $ver['version'];
    }
    public function skin()
    {
        $data = Web_ConfigModel::value('theme_page_color_back');
        $this->data->addBody(-140, (array)$data);
    }

}

?>