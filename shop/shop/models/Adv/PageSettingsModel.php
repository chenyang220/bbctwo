<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Adv_PageSettingsModel extends Adv_PageSettings
{
	////#008800 生鲜绿  #000033 //工业蓝 #db2c2c //标准红
	public static $page_color_bg = array(
		"red" => "红色",
		"skyblue" => "天蓝",
		"green" => "绿色",
		"gray" => "褐色",
		"blue" => "蓝色",
		"paleblue" => "古蓝",
        "orange"  => "橘色",
        "sgreen"  => "生鲜绿",
        "gbule"  => "工业蓝",
        "bred"  => "标准红"
	);
	public static $page_color_floor = array(
		"red" => "红色",
        "grey"  => "经典",
		"skyblue" => "天蓝",
		"green" => "绿色",
		"gray" => "褐色",
		"blue" => "蓝色",
		"paleblue" => "古蓝",
        "orange"  => "橘色"
	);
	public static $page_statu = array(
		"0" => "不显示",
		"1" => "显示"
	);


	public function listPageSettingsWhere($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
  
		$page_list = $this->listByWhere($cond_row, $order_row);
		foreach ($page_list['items'] as $key => $value)
		{
			$page_list['items'][$key]['page_colorcha']  = __(self::$page_color_floor[$value["page_color"]]);
			$page_list['items'][$key]['page_statuscha'] = __(self::$page_statu[$value["page_status"]]);
		}
		return $page_list;
	}

	//查询全部模板，以及颜色
	public function layoutColor()
	{
		$data['color_bg']      = self::$page_color_bg;
		$data['color_floor']      = self::$page_color_floor;
		$AdvPagelayoutModel = new Adv_PageLayoutModel();
		$data['layout']     = $AdvPagelayoutModel->getByWhere();

		return $data;
	}


	/**
	 * 获取广告页里面的模块内容
	 * @param string 模块id 广告页id
	 * @param $structure 模板内容
	 * @return array
	 */
	public static function getAdpositionlist($page_id, $layout_id, $structure)
	{

		$AdvWidgetBaseModel = new Adv_WidgetBaseModel();
		$AdvWidgetItemModel = new Adv_WidgetItemModel();


		foreach ($structure as $skeys => $structure_block)
		{
			if (is_array($structure_block))
			{
				foreach ($structure_block as $cskeys => $structure_block_child)
				{
					if (empty($structure_block_child["child"]))
					{

						$cond_row = array(
							"page_id" => $page_id,
							"layout_id" => $layout_id,
							"widget_name" => $cskeys
						);
						$selt_con = $AdvWidgetBaseModel->getByWhere($cond_row);

						foreach ($selt_con as $widget_id => $val)
						{
							$array    = array("widget_id" => $widget_id);
							$item_con = $AdvWidgetItemModel->getByWhere($array);

                            if (!empty($item_con)){
								if($item_con['item_goods_id'])
								{
									$Goods_CommonModel = new Goods_CommonModel();
									$goods = $Goods_CommonModel->getOne($item_con['item_goods_id']);
									$item_con['common_id'] = $goods['common_id'];
									$item_con['common_image'] = $goods['common_image'];
									$item_con['common_name'] = $goods['common_name'];
									$item_con['common_price'] = $goods['common_price'];
								}

                                $structure["layout_structure"][$cskeys]["html"] = $item_con;
                            }

						}


					}
					else
					{
						foreach ($structure_block_child["child"] as $childkey => $childhtml)
						{

							$cond_row = array(
								"page_id" => $page_id,
								"layout_id" => $layout_id,
								"widget_name" => $childkey
							);
							$selt_con = $AdvWidgetBaseModel->getByWhere($cond_row);
							foreach ($selt_con as $widget_id => $val)
							{
								$array                                                              = array("widget_id" => $widget_id);
								$item_con                                                           = $AdvWidgetItemModel->getByWhere($array);
								if($val['widget_cat'] == 'goods')
								{
									$k = key($item_con);
									$Goods_CommonModel = new Goods_CommonModel();
									$goods = $Goods_CommonModel->getOne($item_con[$k]['item_goods_id']);
									$item_con[$k]['common_id'] = $goods['common_id'];
									$item_con[$k]['common_image'] = $goods['common_image'];
									$item_con[$k]['common_name'] = $goods['common_name'];
									$item_con[$k]['common_price'] = $goods['common_price'];
								}
								$structure["layout_structure"][$cskeys]["child"][$childkey]["html"] = $item_con;

							}

						}
					}


				}
			}
		}

		return $structure;

	}

	/* 设置页面显示表的html内容
* @param $id 广告页的id
*
*           */

	function set_page_settings_html($id, $type = "index")
	{
		$re       = $this->getOne($id);
		$layoutid = $re['layout_id'];

		$LayoutModel = new Adv_PageLayoutModel();
		$structure       = $LayoutModel->getOne($layoutid);
		$re['structure'] = $this->getAdpositionlist($id, $layoutid, $structure);

		$AdvWidgetNavModel = new Adv_WidgetNavModel();
		$cond_row         = array(
				"page_id" =>$id,
		);
		$nav = $AdvWidgetNavModel->getByWhere($cond_row);

		$str = "<div class='m frame" . $re['layout_id'] . " " . $re['page_color'] . " '>";
		$str .= ($type == 'wap') ? "" : "<div class='mt fn-clear'><h3 class='index-module-tit'><span></span><em>". $re['page_name']."</em><span></span></h3>";
		if(!empty($nav)) {
			foreach ($nav as $keys => $vals) {
				$str .= '<a href="' .$vals["widget_nav_url"] .'" target="_blank">'. $vals["widget_nav_name"].'</a>';
				// $str .= "<a  target='_blank' href='" .$vals['widget_nav_url'] ."'>$vals[widget_nav_name]</a>";
			}
		}
		$str.="</div>";
		$str .= "<div class='mc fn-clear'>";

		foreach ($re['structure']['layout_structure'] as $keys => $vals)
		{
			$css = $type == 'index' ? "style='width:" . $vals['style']['width'] . "px;height:" . $vals['style']['height'] . "px'" : "";
			$str .= "<div class='block $keys' $css>";

			if (!empty($vals['child']))
			{

				foreach ($vals['child'] as $k => $v)
				{
					$css = $type == 'index' ? "style='width:" . $v['style']['width'] . "px;height:" . $v['style']['height'] . "px'" : "";
					$str .= "<div class='$k' $css>";
					if ($v['type'] == "ag")
					{

						if (!empty($v['html']))
						{
							foreach ($v['html'] as $ke => $va)
							{
								$str .= '<a href="' . $va["item_url"] . '" target="_blank">';
								//$str .= "<a target='_blank' href='" . $va['item_url'] . "'>";
								$str .= "<img width='" . $v['style']['width'] . "' height='" . $v['style']['height'] . "' title ='" . $va['item_name'] . "' alt ='" . $va['item_name'] . "' src='" . $va['item_img_url'] . "'>";
								$str .= "</a>";
							}
						}
					}
					elseif ($v['type'] == "ad")
					{
						if (!empty($v['html']))
						{
							$str .= "<div class='blueberry'>";
							$str .= "<ul class='slides'>";
							foreach ($v['html'] as $ke => $va)
							{

								$str .= "<li>";
								$str .= '<a href="' . $va["item_url"] . '" target="_blank">';
								//$str .= "<a target='_blank' href='" . $va['item_url'] . "'>";
								$str .= "<img width='" . $v['style']['width'] . "' height='" . $v['style']['height'] . "' title ='" . $va['item_name'] . "' alt ='" . $va['item_name'] ."'  src='" . $va['item_img_url'] . "'>";
								$str .= "</a>";
								$str .= "</li>";
							}
							$str .= "</ul>";
							$str .= "</div>";
						}

					}
					elseif ($v['type'] == "goods")
					{
						//查找商品信息
						$Goods_CommonModel = new Goods_CommonModel();
						if (!empty($v['html']))
						{
							foreach ($v['html'] as $ke => $va)
							{
								$goods = $Goods_CommonModel->getOne($va['item_goods_id']);
								$str .= "<div class='blueberry'>";
								$str .= "<div class='goods'>";

								$str .= '<a href="'.Yf_Registry::get("url").'index.php?ctl=Goods_Goods&met=goods&type=goods&cid='.$va["item_goods_id"].'" target="_blank">';
								$str .= "<img style='height: 292px; width: 292px;' class='goods_img' src='".$goods['common_image']."'></a></div>";
								$str .= '<p><a href="index.php?ctl=Goods_Goods&met=goods&type=goods&cid='.$va["item_goods_id"] .'" target="_blank">'.$goods["common_name"].'</a></p>';
								// $str .= "<p><a target='_blank' href='index.php?ctl=Goods_Goods&met=goods&type=goods&cid=".$va['item_goods_id'] ."'>".$goods['common_name']."</a></p>";
								$str .= '<span><a href="index.php?ctl=Goods_Goods&met=goods&type=goods&cid='.$va["item_goods_id"] .'" target="_blank"><b class="rmb">'.Web_ConfigModel::value("monetary_unit").'</b>'.$goods["common_price"].'</a></span>';
								// $str .= "<span><a target='_blank' href='index.php?ctl=Goods_Goods&met=goods&type=goods&cid=".$va['item_goods_id'] ."'><b class='rmb'>".Web_ConfigModel::value('monetary_unit')."</b>".$goods['common_price']."</a></span>";
								$str .= "</div>";
							}
						}
					}
					else
					{
						$str .= "<ul class='fn-clear'>";
						if (!empty($v['html']))
						{
							foreach ($v['html'] as $ke => $va)
							{
								$str .= "<li>";
								$str .= '<a href="' . $va["item_url"] . '" target="_blank">'.$va["item_name"].'</a>';
								// $str .= "<a target='_blank' href='" . $va['item_url'] . "'>$va[item_name]</a>";
								$str .= "</li>";
							}
						}
						$str .= "</ul>";
					}
					$str .= "</div>";
				}

			}
			else
			{
				if ($vals['type'] == "ad")
				{
					if (!empty($vals['html']))
					{
						$str .= "<div class='blueberry'>";
						$str .= "<ul class='slides'>";
						foreach ($vals['html'] as $ke => $va)
						{
							// $click_url = Yf_Registry::get('base_url') . '/advertisement.php?ctl=Message_Adhtml&met=click&item_id='.$va['item_id'].'&url=http://' . urlencode($va['item_url']);
							$str .= "<li>";
							$str .= '<a href="' . $va["item_url"] . '" target="_blank">';
							// $str .= "<a target='_blank' href='" . $va['item_url'] . "'>";
							$str .= "<img width='" . $vals['style']['width'] . "' height='" . $vals['style']['height'] . "' title ='" . $va['item_name'] . "' alt ='" . $va['item_name'] ."'  src='" . $va['item_img_url'] . "'>";
							$str .= "</a>";
							$str .= "</li>";
						}
						$str .= "</ul>";
						$str .= "</div>";
					}
				}
				else
				{
					if (!empty($vals['html']))
					{
						foreach ($vals['html'] as $ke => $va)
						{
							// $click_url = Yf_Registry::get('base_url') . '/advertisement.php?ctl=Message_Adhtml&met=click&item_id='.$va['item_id'].'&url=http://' . urlencode($va['item_url']);
							$str .= '<a href="' . $va["item_url"] . '" target="_blank">';
							// $str .= "<a target='_blank' href='" . $va['item_url'] . "'>";
							$str .= "<img width='" . $vals['style']['width'] . "' height='" . $vals['style']['height'] . "' title ='" . $va['item_name'] . "' alt ='" . $va['item_name'] ."'  src='" . $va['item_img_url'] . "'>";
							$str .= "</a>";
						}
					}
				}

			}
			$str .= "</div>";
		}

		$str .= "</div></div>";

		$data["page_html"] = $str;

		$editpage          = $this->editPageSettings($id, $data);


	}


}

?>