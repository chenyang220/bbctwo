<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Mb_TplLayoutCtl extends AdminController
{
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
    }

    public function getEditPage()
    {
        $item_type = request_string('item_type');

        switch ($item_type)
        {
            case 'adv_list':
                $this->view->setMet('advEdit');
                break;
            case 'home1':
                $this->view->setMet('home1Edit');
                break;
            case 'home2':
                $this->view->setMet('home2Edit');
                break;
            case 'home3':
                $this->view->setMet('home3Edit');
                break;
            case 'home4':
                $this->view->setMet('home4Edit');
                break;
            case 'goods':
                $this->view->setMet('goodsEdit');
                break;
            case 'enterance':
                $this->view->setMet('enteranceEdit');
                break;
            case 'activityA':
                $this->view->setMet('activityaEdit');
                break;
            case 'activityB':
                $this->view->setMet('activitybEdit');
                break;
            case 'goodsB':
                $this->view->setMet('goodsbEdit');
                break;
            case 'advA':
                $this->view->setMet('advAEdit');
                break;
            case 'advB':
                $this->view->setMet('advBEdit');
                break;
            case 'goodsC':
                $this->view->setMet('goodscEdit');
                break;
            case 'class':
            $this->view->setMet('classEdit');
            break;
            case 'home5':
            $this->view->setMet('home5Edit');
            break;
             case 'newGoods':
            $this->view->setMet('newGoodsEdit');
            break;
        }
        include $this->view->getView();
    }

    public function editImage()
    {
        include $this->view->getView();
    }

    public function wx ()
    {
        include $this->view->getView();
    }

    public function app ()
    {
        include $this->view->getView();
    }
}

?>