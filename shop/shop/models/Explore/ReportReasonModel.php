<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Explore_ReportReasonModel extends Explore_ReportReason
{

    //获取所有举报原因
    public function getReportReasonAll()
    {
        $sql = "SELECT * FROM ".TABEL_PREFIX."explore_report_reason";
        $data = $this -> sql -> getAll($sql);

        return $data;
    }

}

?>