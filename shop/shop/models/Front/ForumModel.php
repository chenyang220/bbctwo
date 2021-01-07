<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Front_ForumModel extends Front_Forum
{
	const OPEN = 1;                //待付款     等待买家付款	     下单
	const CLOSE = 2;                   //待配货     等待卖家配货	     付款
	/**
	 * 读取分页列表
	 *
	 * @param  int $goods_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{

		return $this->listByWhere($cond_row, $order_row, $page, $rows);
	}


	/**
	 * 读取首页版块及内容
	 *
	 * @param  int $goods_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getForumContent($id)
	{
		$data = $this->getOne($id);
		$content = $data['forum_content'];
		foreach($content as $ckey => $cval)
		{
			$content_info = array();
			switch ($cval['type'])
			{
				case 'groupbuy':
					//获取团购商品信息(groupbuy_id),不返回已经过期失效的团购商品信息,不返回非正常状态的商品
					$GroupBuy_BaseModel = new GroupBuy_BaseModel();
					$content_info  = $GroupBuy_BaseModel->getForumGroupbuy($cval['content']);
					break;
				case 'discount':
					//获取限时折扣商品信息(discount_goods_id),不返回已经过期失效的限时折扣商品,不返回非正常状态的商品
					$Discount_GoodsModel = new Discount_GoodsModel();
					$content  = $Discount_GoodsModel->getForumDiscount($cval['content']);
					$content_info  = array_values($content);
					break;
				case 'redpacket':
					//获取红包信息(redpacket_t_id),不返回已经过期失效的红包
					$RedPacket_TempModel = new RedPacket_TempModel();
					$content_info = $RedPacket_TempModel->getForumRedpacket($cval['content']);
					break;
				case 'voucher':
					//获取代金券信息(voucher_t_id),不返回已经失效过期的代金券信息
					$Voucher_TempModel = new Voucher_TempModel();
					$content_info  = $Voucher_TempModel->getForumVoucher($cval['content']);
					break;
				default:
					;
			}
			$data['forum_content'][$ckey]['content_info'] = $content_info;
		}
		return $data;
	}


	/**
	 * 修改首页版块内容
	 *
	 * @param  int $goods_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function editForumInfo($id,$data)
	{
		$update_data = array();
		$flag = true;
		$msg = 'success';

		//版块状态 1-开启 2-关闭
		if($data['forum_state']) {
			$update_data['forum_state'] = $data['forum_state'];
		}
		//版块名称
		if($data['forum_name']) {
			$update_data['forum_name'] = $data['forum_name'];
		}
		//版块样式 1-长方形 2-正方形
		if($data['forum_style']) {
			$update_data['forum_style'] = $data['forum_style'];
		}
		//版块内容
		if($data['forum_content'] && $data['edit_content']) {
			$forum = $this->getOne($id);

			if($data['forum_style']) {
				$style = $data['forum_style'];
			} else {
				$style = $forum['forum_style'];
			}

			//验证版块内容是否是正确的
			$check = $this->checkForumContent($style,$data['forum_content']);

			if($check['flag']) {
				$forum = $this->getOne($id);
				$forum_content = $forum['forum_content'];

				//修改[0]数组
				if($data['edit_content'] == 1) {
					$forum_content[0] = $data['forum_content'][0];
				}
				//修改[1]数组
				if($data['edit_content'] == 2) {
					$forum_content[1] = $data['forum_content'][1];
				}

				$update_data['forum_content'] = $forum_content;

				$flag = true;
			} else {
				$flag = false;
				$msg = $check['msg'];
			}
		}

		if($flag)
		{
			$flag = $this->editforum($id,$update_data);
		}

		$res = array();
		$res['flag'] = $flag;
		$res['msg'] = $msg;

		return $res;
	}

	/**
	 * 按照排序读取开启的首页版块及内容
	 *
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getOpenForumContent()
	{
		$data = $this->getByWhere(array('forum_state'=>Front_ForumModel::OPEN),array('forum_order'=>'asc'));
		$res = array();
		if($data)
		{
			foreach($data as $key => $val)
			{
				$res[] = $this->getForumContent($val['id']);
			}
		}

		//获取到版块数据之后查看是否需要补充数据
		foreach($res as $key => $val)
		{
			foreach($val['forum_content'] as $vk => $vv)
			{
				$add_content = array();
				if( $val['forum_style'] == 1  && count($vv['content_info']) <15)
				{
					//添加商品
					$num = 15-count($vv['content_info'])*1;
					$add_content = $this->addOpenForumContent($vv['type'],$vv['content'],$num);

				}
				if( $val['forum_style'] == 2  && count($vv['content_info']) <9)
				{
					//添加商品
					$num = 9-count($vv['content_info'])*1;
					$add_content = $this->addOpenForumContent($vv['type'],$vv['content'],$num);
				}
				if(!is_array($add_content))
				{
					$add_content = array();
				}
				$res[$key]['forum_content'][$vk]['content_info'] = array_merge($res[$key]['forum_content'][$vk]['content_info'],$add_content);
			}
		}

		return $res;
	}

	//添加首页版块
	public function addFrontForum($add_data)
	{
	    //查找当前数据表中order的最大值
        $sql = 'select max(forum_order) max  from '. TABEL_PREFIX .'front_forum';
        $data = $this->sql->getAll($sql);
        $max = 0;
        if($data[0]['max']) {
            $max = $data[0]['max'];
        }

        $add_data['forum_order'] = $max*1+1;

		//需要根据选用的版块确认商品数量
		$status = 200;
		$msg = __('添加成功');
		$flag = true;
		foreach($add_data['forum_content'] as $fckey => $fcval)
		{
			switch ($fcval['type'])
			{
				case 'groupbuy':
					$count = count($fcval['content']);
					if($add_data['forum_style'] == 1)
					{
						if($count > 15)
						{
							$status = 250;
							$msg = __('最多添加15款商品');
							$flag = false;
						}
					}
					if($add_data['forum_style'] == 2)
					{
						if($count > 9)
						{
							$status = 250;
							$msg = __('最多添加9款商品');
							$flag = false;
						}
					}
					break;
				case 'discount':
					$count = count($fcval['content']);
					if($add_data['forum_style'] == 1)
					{
						if($count > 15)
						{
							$status = 250;
							$msg = __('最多添加15款商品');
							$flag = false;
						}
					}
					if($add_data['forum_style'] == 2)
					{
						if($count > 9)
						{
							$status = 250;
							$msg = __('最多添加9款商品');
							$flag = false;
						}
					}
					break;
				case 'redpacket':
					$count = count($fcval['content']);
					if($add_data['forum_style'] == 1)
					{
						if($count > 15)
						{
							$status = 250;
							$msg = __('最多添加15种红包');
							$flag = false;
						}
					}
					if($add_data['forum_style'] == 2)
					{
						if($count > 9)
						{
							$status = 250;
							$msg = __('最多添加9种红包');
							$flag = false;
						}
					}
					break;
				case 'voucher':
					$count = count($fcval['content']);
					if($add_data['forum_style'] == 1)
					{
						if($count > 15)
						{
							$status = 250;
							$msg = __('最多添加15款代金券');
							$flag = false;
						}
					}
					if($add_data['forum_style'] == 2)
					{
						if($count > 9)
						{
							$status = 250;
							$msg = __('最多添加9款代金券');
							$flag = false;
						}
					}
					break;
				default:
					;
			}
		}

		if($flag)
		{
			$flag = $this->addforum($add_data);
		}

		return array('flag'=>$flag,'status'=>$status,'msg'=>$msg);

	}

	//补充版块活动数据
	public function addOpenForumContent($type,$id,$num)
	{
		$content_info = array();
		switch ($type)
		{
			case 'groupbuy':
				//从有效团购中获取有效的信息，获取团购商品信息(groupbuy_id)
				$GroupBuy_BaseModel = new GroupBuy_BaseModel();
				$content_info  = $GroupBuy_BaseModel->getOpenForumGroupbuy($id,$num);
				break;
			case 'discount':
				//获取限时折扣商品信息(discount_goods_id)
				$Discount_GoodsModel = new Discount_GoodsModel();
				$content_info  = $Discount_GoodsModel->getOpenForumDiscount($id,$num);
				break;
			case 'redpacket':
				//获取红包信息(redpacket_t_id)
				$RedPacket_TempModel = new RedPacket_TempModel();
				$content_info = $RedPacket_TempModel->getOpenForumRedpacket($id,$num);
				break;
			case 'voucher':
				//获取代金券信息(voucher_t_id)
				$Voucher_TempModel = new Voucher_TempModel();
				$content_info  = $Voucher_TempModel->getOpenForumVoucher($id,$num);
				break;
            case 'pintuan':
                //获取拼团信息(pintuan_id)
                $PinTuan_Base = new PinTuan_Base();
                $content_info  = $PinTuan_Base->getOpenPinTuan($id,$num);
			default:
				break;
		}

		return $content_info;
	}

	public function checkForumContent($style,$data)
	{
		$status = 200;
		$msg = __('success');
		$flag = true;

		$data = current($data);
		switch ($data['type'])
		{
			case 'groupbuy':
				$count = count($data['content']);
				if($style == 1)
				{
					if($count > 15)
					{
						$status = 250;
						$msg = __('最多添加15款商品');
						$flag = false;
					}
				}
				if($style == 2)
				{
					if($count > 9)
					{
						$status = 250;
						$msg = __('最多添加9款商品');
						$flag = false;
					}
				}
				break;
			case 'discount':
				$count = count($data['content']);
				if($style == 1)
				{
					if($count > 15)
					{
						$status = 250;
						$msg = __('最多添加15款商品');
						$flag = false;
					}
				}
				if($style == 2)
				{
					if($count > 9)
					{
						$status = 250;
						$msg = __('最多添加9款商品');
						$flag = false;
					}
				}
				break;
			case 'redpacket':
				$count = count($data['content']);
				if($style == 1)
				{
					if($count > 15)
					{
						$status = 250;
						$msg = __('最多添加15种红包');
						$flag = false;
					}
				}
				if($style == 2)
				{
					if($count > 9)
					{
						$status = 250;
						$msg = __('最多添加9种红包');
						$flag = false;
					}
				}
				break;
			case 'voucher':
				$count = count($data['content']);
				if($style == 1)
				{
					if($count > 15)
					{
						$status = 250;
						$msg = __('最多添加15款代金券');
						$flag = false;
					}
				}
				if($style == 2)
				{
					if($count > 9)
					{
						$status = 250;
						$msg = __('最多添加9款代金券');
						$flag = false;
					}
				}
				break;
			default:
				;
		}

		return array('flag'=>$flag,'status'=>$status,'msg'=>$msg);

	}

	public function getForumHtml($data)
	{
		$str = '';
		foreach($data as $key => $val) {
				if($val['forum_style'] == 1)
				{
					switch ($val['forum_content'][0]['type'])
					{
						case 'groupbuy':
							$html = $this->addGroupBuyHtml($val['forum_style'],$val['forum_content'][0]);
							$str .= $html;
							
							break;
						case 'discount':
							$html = $this->addDiscountHtml($val['forum_style'],$val['forum_content'][0]);
							$str .= $html;
							break;
						case 'redpacket':
							$html = $this->addRedpacketHtml($val['forum_style'],$val['forum_content'][0]);
							$str .= $html;
							break;
						case 'voucher':
							$html = $this->addVoucherHtml($val['forum_style'],$val['forum_content'][0]);
							$str .= $html;
							break;
						default:
							;
					}

				} elseif ($val['forum_style'] == 2) {
					$str .= '<div class="clearfix">';
					foreach($val['forum_content'] as $k => $v)
					{
						if($k == 0)
						{
							$str .= '<div class="fl">';
						}elseif($k == 1)
						{
							$str .= '<div class="fr">';
						}
						switch ($v['type'])
						{
							case 'groupbuy':
								$html = $this->addGroupBuyHtml($val['forum_style'],$v);
								$str .= $html;
								break;
							case 'discount':
								$html = $this->addDiscountHtml($val['forum_style'],$v);
								$str .= $html;
								break;
							case 'redpacket':
								$html = $this->addRedpacketHtml($val['forum_style'],$v);
								$str .= $html;
								break;
							case 'voucher':
								$html = $this->addVoucherHtml($val['forum_style'],$v);
								$str .= $html;
								break;
							default:
								;
						}
						$str .= '</div>';
					}
					$str .= '</div>';

				}
		}

		return $str;
	}

	public function addGroupBuyHtml($style,$data)
	{
		$html = '';
		if($style == 1) {
			$html = '<div><div class="activity-a group-purchase-a"><h3><span></span><i class="iconfont icon-pintu"></i><em class="index-tit">'. $data['title'] . '</em><span></span><a href="index.php?ctl=GroupBuy&met=index" target="_blank">更多<i class="iconfont icon-btnrightarrow"></i></a></h3><div class="swiper-container"><div class="swiper-wrapper">';
			foreach($data['content_info'] as $key => $val)
			{
				$html .='<div class="swiper-slide"><a href="'. Yf_Registry::get('url') .'?ctl=Goods_Goods&met=goods&type=goods&gid='.$val['goods_id'].'" target="_blank"><img src="' . cdn_image_url($val['groupbuy_image'], 150, 150) . '"><div class="pri"><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'. $val['groupbuy_price'] .'</div><h4 class="one-overflow">' . $val['groupbuy_name'] . '</h4></a>
                                            <div class="time">
                                                <i class="iconfont icon-shijian mr4"></i>
                                                <div class="time-item iblock fnTimeCountDown" data-end="'.$val['groupbuy_endtime'].'">
                                                    <p id="minute_show" class="day">00</p>天
                                                    <p id="minute_show" class="hour">00</p>小时
                                                    <p id="second_show" class="mini">00</p>分
                                                    <p id="second_show" class="sec">00</p>秒
                                                </div>
                                            </div><a class="btn-tuan" href="'. Yf_Registry::get('url') .'?ctl=Goods_Goods&met=goods&type=goods&gid='.$val['goods_id'].'" target="_blank">立即去团</a></div>';
			}

			$html .= '</div><div class="swiper-button-next"></div><div class="swiper-button-prev"></div></div></div><div>';
		} elseif($style == 2) {
			$html = '<div class="activity-b group-purchase-b"><h3>'. $data['title'] . ' <a href="index.php?ctl=GroupBuy&met=index">更多 <i class="iconfont icon-icon_gengduo" target="_blank"></i></a></h3><div class=""><div class="swiper-container swiper-container-groupbuy"><ul class="swiper-wrapper">';

			foreach($data['content_info'] as $key => $val)
			{
				$html .='<li class="clearfix swiper-slide"><em class="img-box fl"><a href="'. Yf_Registry::get('url') .'?ctl=Goods_Goods&met=goods&type=goods&gid='.$val['goods_id'].'"
				target="_blank"><img src="" data-src="' . cdn_image_url($val['groupbuy_image'],156,156) . '" /></a></em><div class="fl"><div class="table"><div class="table-cell"><h4 class="one-overflow"><a href="'. Yf_Registry::get('url') .'?ctl=Goods_Goods&met=goods&type=goods&gid='.$val['goods_id'].'" target="_blank">' . $val['groupbuy_name'] . '</a></h4><div class="tl"><span class="price"><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'. $val['groupbuy_price'] .'</span>
                                            <div class="time">
                                                <i class="iconfont icon-shijian mr4"></i>
                                                <div class="time-item iblock fnTimeCountDown" data-end="'.$val['groupbuy_endtime'].'">
                                                    <p id="minute_show" class="day">00</p>天
                                                    <p id="minute_show" class="hour">00</p>小时
                                                    <p id="second_show" class="mini">00</p>分
                                                    <p id="second_show" class="sec">00</p>秒
                                                </div>
                                            </div></div><div class="tr"><a class="btn-tuan" href="'. Yf_Registry::get('url') .'?ctl=Goods_Goods&met=goods&type=goods&gid='.$val['goods_id'].'" target="_blank">立即去团</a></div></div></div></div></li>';
			}

			$html .= '</ul><div class="pagination swiper-pagination-groupbuy"></div></div></div></div>';

		}

		return $html;
	}

	public function addDiscountHtml($style,$data)
	{  
		$html = '';
        foreach($data['content_info'] as $key => $val)
        {
            $goods_rows[] = $val['goods_id'];
        }
        $Goods_BaseModel = new Goods_BaseModel();
        foreach($goods_rows as $kk=>$vv)
        {
            $result[] = $Goods_BaseModel->getOne($vv);
        }
        foreach($data['content_info'] as $key => $val)
        {
            if($val['goods_id']==$result[$key]['goods_id'])
            {
                $data['content_info'][$key]['goods_stock'] =$result[$key]['goods_stock'];
            }
        }
		if($style == 1) {
			$html = '<div><div class="activity-a time-limit-a">
			<h3>
				<span></span>
				<i class="iconfont icon-julishijian"></i><em class="index-tit">
				'. $data['title'].'</em>
				<span></span>
				<a href="index.php?ctl=Goods_Goods&met=getDiscountGoodsList&typ=e" target="_blank">更多<i class="iconfont icon-btnrightarrow"></i></a>
			</h3>
			<div class="clearfix">
				<div class="fl logo">
					<p>限时折扣</p>
					<p>FLASH DEALS</p>
					<i class="iconfont icon-lightningbshandian"></i>
				</div>

				<div class="swiper-container fr">
					<div class="swiper-wrapper">';

            foreach($data['content_info'] as $key => $val)
            {
                if($val['goods_stock']<=0)
                {
                    $html .='<div class="swiper-slide">
							<a href="'.Yf_Registry::get('url').'?ctl=Goods_Goods&met=goods&type=goods&gid='.$val['goods_id'].'"
							target="_blank">
								<em class="img-box">
									<img src="" data-src="'.cdn_image_url($val['goods_image'],200,200).'">
								</em>
								<h4 class="one-overflow">'.$val['goods_name'].'</h4>
								<div class="discount-a-pri"><span><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'.$val['discount_price'].'</span><span><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'.$val['goods_price'].'</span></div>
								<div class="time">
								    <i class="iconfont icon-shijian mr4"></i>
                                    <div class="time-item iblock fnTimeCountDown discount-a-time" data-end="'.$val['goods_end_time'].'">
                                        <p id="minute_show" class="day">00</p>天
                                        <p id="minute_show" class="hour">00</p>小时
                                        <p id="second_show" class="mini">00</p>分
                                        <p id="second_show" class="sec">00</p>秒
                                    </div>
								</div>
							</a>
							<div style="position:absolute;left:0px;top:0px;background:#666666;width:100%;height:100%;filter:alpha(opacity=60); opacity:0.6; z-Index:999;"></div>
							<div id="modal" style=" position:absolute;width:100%;height:200px;top:67%;opacity:0.6;cursor:pointer;z-Index:9999;font-size: 35px;"><i style="font-family: 宋体;font-weight: 900;background: #ffffff;display: block; color: red;width: 60%;margin: auto;margin-top: 1rem;-webkit-transform: rotate(25deg);-moz-transform: rotate(25deg);filter: progid:DXImageTransform.Microsoft.BasicImage(Rotation=0.45);">已售罄</i></div>
						</div>';
                }else
                {
                    $html .='<div class="swiper-slide">
							<a href="'.Yf_Registry::get('url').'?ctl=Goods_Goods&met=goods&type=goods&gid='.$val['goods_id'].'"
							target="_blank">
								<em class="img-box">
									<img src="'.cdn_image_url($val['goods_image'],200,200).'" data-src="'.cdn_image_url($val['goods_image'],200,200).'">
								</em>
								<h4 class="one-overflow">'.$val['goods_name'].'</h4>
								<div class="discount-a-pri"><span><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'.$val['discount_price'].'</span><span><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'.$val['goods_price'].'</span></div>
								<div class="time">
								    <i class="iconfont icon-shijian mr4"></i>
                                    <div class="time-item iblock fnTimeCountDown discount-a-time" data-end="'.$val['goods_end_time'].'">
                                        <p id="minute_show" class="day">00</p>天
                                        <p id="minute_show" class="hour">00</p>小时
                                        <p id="second_show" class="mini">00</p>分
                                        <p id="second_show" class="sec">00</p>秒
                                    </div>
								</div>
							</a>
						</div>';
                }


            }

			$html .= '
					</div>
					<div class="swiper-button-next"></div>
					<div class="swiper-button-prev"></div>
				</div>
			</div>
		</div></div>';
		} elseif($style == 2) {
			$html = '<div class="activity-b group-purchase-b time-limit-b">
			<h3>'. $data['title'].'<a href="index.php?ctl=Goods_Goods&met=getDiscountGoodsList&typ=e" target="_blank">更多 <i class="iconfont icon-icon_gengduo"></i></a></h3>
			<!-- Swiper -->
			<div class="swiper-container swiper-container-discount">
				<ul class="swiper-wrapper">';

			foreach($data['content_info'] as $key => $val)
			{
				$html .='<li class="clearfix swiper-slide">
							<a href="'.Yf_Registry::get('url').'?ctl=Goods_Goods&met=goods&type=goods&gid='.$val['goods_id'].'" target="_blank">
								<em class="img-box fl">
									<img class="" src="'.cdn_image_url($val['goods_image'],156,156).'" data-src="'.cdn_image_url($val['goods_image'],156,156).'" />
								</em>
								<div class="fl">
									<div class="table">
										<div class="table-cell">
											<h4 class="one-overflow">'.$val['goods_name'].'</h4>
											<div class="clearfix">
												<span class="sale-price"><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'.$val['discount_price'].'</span>
												<span class="origin-price"><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'.$val['goods_price'].'</span>
											</div>
											<div class="time">
											    <i class="iconfont icon-shijian mr4"></i>
                                                <div class="time-item iblock fnTimeCountDown" data-end="'.$val['goods_end_time'].'">
                                                    <p id="minute_show" class="day">00</p>天
                                                    <p id="minute_show" class="hour">00</p>小时
                                                    <p id="second_show" class="mini">00</p>分
                                                    <p id="second_show" class="sec">00</p>秒
                                                </div>
                                            </div>
										</div>
									</div>
								</div>
							</a>
						 </li>';
			}
			$html .= '</ul>
					
				<!-- Add Pagination -->
				<div class="pagination swiper-pagination-discount"></div>
			</div>
		</div>';

		}

		return $html;
	}

	public function addRedpacketHtml($style,$data)
	{
	 
		$html = '';
		if($style == 1) {
			$html = '<div><div class="activity-a platform-a">
			<h3>
				<span></span>
				<i class="iconfont icon-hongbao1"></i><em class="index-tit">
				'. $data['title'] . '</em>
				<span></span>
				<a href="'.Yf_Registry::get('url').'?ctl=RedPacket&met=redPacket" target="_blank">更多<i class="iconfont icon-btnrightarrow"></i></a>
			</h3>
			<div class="module-redpacket">
				<div class="swiper-container">
				<div class="swiper-wrapper">';
			foreach($data['content_info'] as $key => $val)
			{
				$html .='<div class="swiper-slide">
						<span class="fl">
							<img src="" data-src="' . cdn_image_url($val['redpacket_t_img'],100,100) . '" alt="" />
						</span>
						<div class="fl platform-details">
							<p><span><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'.$val['redpacket_t_price'].'</span> 满'.$val['redpacket_t_orderlimit'].'可用</p>
							<span class="one-overflow">'.$val['redpacket_t_title'].'</span>
							<span>限'.date('Y-m-d',strtotime($val['redpacket_t_end_date'])).'前使用</span>
							<div><span class="yq">已抢'.$val['redpacket_t_usedper'].'</span><em style="width:'.$val['redpacket_t_usedper'].';"></em></div>
						</div>
						<a onclick="ReceiveRedPack('.$val['redpacket_t_id'].')">立即领取</a>
					</div>';
			}

			$html .= '
					</div>		
			</div>
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
		</div>
			
		</div>
			
		</div>';
		} elseif($style == 2) {
			$html = '<div class="activity-b platform-b">
			<h3>'. $data['title'] . '<a href="'.Yf_Registry::get('url').'?ctl=RedPacket&met=redPacket" target="_blank">更多 <i class="iconfont icon-icon_gengduo"></i></a></h3>
			<!-- Swiper -->
			<div class="module-redpacket-b">
				<div class="swiper-container swiper-container-redpacket">
						<ul class="swiper-wrapper">';

			foreach($data['content_info'] as $key => $val)
			{
				$html .='<li class="clearfix relative swiper-slide">
								<span class="fl img-box">
									<img src="" data-src="' . cdn_image_url($val['redpacket_t_img'],100,100) . '" alt="" />
								</span>
								<div class="fl platform-details">
									<p><span><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'.$val['redpacket_t_price'].'</span> 满'.$val['redpacket_t_orderlimit'].'可用</p>
									<span class="one-overflow">'.$val['redpacket_t_title'].'</span>
									<span>限'.date('Y-m-d',strtotime($val['redpacket_t_end_date'])).'前使用</span>
									<div><em style="width: '.$val['redpacket_t_usedper'].';"></em><span class="yq">已抢'.$val['redpacket_t_usedper'].'</span></div>
								</div>
								<a onclick="ReceiveRedPack('.$val['redpacket_t_id'].')">立即领取</a>
							</li>';
			}
			$html .= '</ul>
				<!-- Add Pagination -->
				<div class="pagination swiper-pagination-redpacket"></div>
			</div>
		</div></div>';

		}

		return $html;
	}

	public function addVoucherHtml($style,$data)
	{
		$html = '';
		if($style == 1) {
			$html = '<div><div class="activity-a coupon-a">
			<h3>
				<span></span>
				<i class="iconfont icon-qian1"></i><em class="index-tit">
				'. $data['title'] . '</em>
				<span></span>
				<a href="'.Yf_Registry::get('url').'?ctl=Voucher&met=vList" target="_blank">更多<i class="iconfont icon-btnrightarrow"></i></a>
			</h3>
			<div class="module-voucher">
				<div class="swiper-container">
				<div class="swiper-wrapper">';
			foreach($data['content_info'] as $key => $val)
			{
				$html .='<div class="swiper-slide">
						<span class="fl">
									<img src="" data-src="'.cdn_image_url($val['voucher_t_customimg'],100,100).'" alt="" />
								</span>
						<div class="fl coupon-details">
							<p><span><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'.$val['voucher_t_price'].'</span> 需要'.$val['voucher_t_points'].'积分</p>
							<span class="price">满'.$val['voucher_t_limit'].'元可用</span>
							<span class="store-name one-overflow">'.$val['shop_name'].'</span>
							<div>
								<em style="width: '.$val['voucher_t_usedper'].';"></em>
								<span class="yq">已抢'.$val['voucher_t_usedper'].'</span>
							</div>
						</div>

                        <a op_type="exchangevoucherbtn" data-id="'.$val['voucher_t_id'].'" >立即领取</a>

					</div>';
			}

			$html .= '</div>
				
			</div><div class="swiper-button-next"></div>
				<div class="swiper-button-prev"></div>
		</div></div></div>';
		} elseif($style == 2) {
			$html = '<div class="activity-b coupon-b">
			<h3>'. $data['title'] . '<a href="'.Yf_Registry::get('url').'?ctl=Voucher&met=vList" target="_blank">更多 <i class="iconfont icon-icon_gengduo"></i></a></h3>
			<!-- Swiper -->
			<div class="swiper-container swiper-container-voucher">
				<ul class="swiper-wrapper">';

			foreach($data['content_info'] as $key => $val)
			{
				$html .='<li class="clearfix relative swiper-slide">
								<span class="fl img-box">
									<img src="" data-src="'.cdn_image_url($val['voucher_t_customimg'],120,120).'" alt="" />
								</span>
								<div class="fl coupon-details">
									<p><span><b class="rmb">'.Web_ConfigModel::value('monetary_unit').'</b>'.$val['voucher_t_price'].'</span> 需要'.$val['voucher_t_points'].'积分</p>
									<span class="price">满'.$val['voucher_t_limit'].'元可用</span>
									<span class="one-overflow">'.$val['shop_name'].'</span>
									<div><em style="width: '.$val['voucher_t_usedper'].';"></em><span class="yq">已抢'.$val['voucher_t_usedper'].'</span></div>
								</div>
								<a op_type="exchangevoucherbtn" data-id="'.$val['voucher_t_id'].'">立即领取</a>
							</li>';
			}
			$html .= '</ul>
				<!-- Add Pagination -->
				<div class="pagination swiper-pagination-voucher"></div>
			</div>
		</div>';

		}

		return $html;
	}

}

?>