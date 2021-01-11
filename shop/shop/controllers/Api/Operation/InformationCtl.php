<?php
/**
 * Created by PhpStorm.
 * User: tech0
 * Date: 2018/8/3
 * Time: 11:21
 */
if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author
 */
class Api_Operation_Information extends Api_Controller
{

    public $Information    = null;
    public $InformationModel = null;

    /**
     * 初始化方法，构造函数
     *
     * @access public
     */
    public function init()
    {
        $this->Information    = new Information();
        $this->InformationModel = new InformationModel();
    }

    /**
     * 获取资讯列表
     */
    public function getInformationList()
    {

        $page                    = request_int('page', 1);
        $rows                    = request_int('rows', 10);
        $issue_user_type = request_string('issue_user_type');
        $information_title            = request_string('information_title');

        $cond_row = array();
        $sort     = array();

        if ($issue_user_type)
        {
            $cond_row['issue_user_type'] = $issue_user_type;
        }
        if ($information_title)
        {
            $cond_row['user_account:LIKE'] = '%'.$information_title.'%';
        }

        $data = array();
        $data = $this->InformationModel->getInformationList($cond_row, $sort, $page, $rows);


        $this->data->addBody(-140, $data);
    }
}