<?php
/**
 * 裁剪图片调用这里
 */
class ImgCtl extends Controller
{
	public function index()
	{
		$data['url'] = Img::url(request_string('url'));

		if ('json' == $this->typ)
		{
			$this->data->addBody(-140, $data);
		}
	}


}