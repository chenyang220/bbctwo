<?php
/**
 * 生成Db模型
 * 
 * 此方法，自动生成基本的Model程序
 * 
 * @category   Framework
 * @package    __init__
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2016远丰仁商
 * @version    1.0
 * @todo       
 */

if (is_file('../shop/configs/config.ini.php'))
{
    require_once '../shop/configs/config.ini.php';
}
else
{
    die('请先运行index.php,生成应用程序框架结构！');
}


define("MOD_PATH_TMP", './model'); //控制器后缀

echo '<pre>';

if (!defined('CONTROLLER_CLASS_NAME'))
{
    define("CONTROLLER_CLASS_NAME", 'Main_'); //控制器class前缀
}

define("CONTROLLER_ENDFIX", 'Ctl'); //控制器后缀
define("MODEL_ENDFIX", 'Model'); //模型后缀

if (!defined('MODEL_CLASS_NAME'))
{
    define("MODEL_CLASS_NAME", ''); //模型class前缀
}

define("ACT", 'DEL_');       //是生成还是删除临时文件
define("PRE", false);        //表是否有前缀
define("COMMENT", true);     //sql语句不需要有字段注释
define("COLUMN_NAME_LEN", 4 * 8); //字段长度格式
define("COLUMN_COMMENT_LEN", 4 * 4); //字段注释长度格式

define("BR", "\n"); //字段注释长度格式

$db_id = 'shop';
$Db  = Yf_Db::get($db_id);
$Dbh = $Db->getDbHandle();

define("DATABASE", $Dbh->cfg[$db_id]['database']);

$table_sql = 'SHOW  TABLES   ;';
$table_rows = $Db->getAll($table_sql);

$table_name = '';

if ('cli' != SAPI)
{
    $table_name = $_REQUEST['t'];
}
else
{
    $opt = getopt('t:');

    if (isset($opt['t']))
    {
        $table_name = trim($opt['t']);
    }

    while(!$table_name)
    {
        fwrite(STDOUT,'请输入表名：');
        $table_name = trim(fgets(STDIN));
    }
}

$arr_name = array();

foreach($table_rows as $key => $value)
{
    $arr_name[] = $value['Tables_in_'.DATABASE];
}

if (!$table_name)
{
    die("请传入表名！");
}

$table_rows = explode(",", $table_name);

foreach($table_rows as $key => $value)
{
    if (!in_array($value,$arr_name))
    {
        die($value . " 表不存在");
    }
}

//    var_dump($table_rows);die("kk");
if (!empty($table_rows))
{
    echo "\n";
    echo '生成代码开始';
    echo "\n\n";

    foreach ($table_rows as $key=>$table_row)
    {
        if (true || 'cli' != SAPI)
        {
            $table_name = $table_row;
            $field_name = get_field_name($table_name);
            $columns_name_str = get_columns_name_str($table_name);

            $class_name = get_class_name($table_name);
            $file_name  = get_file_name($class_name);

            echo 'table_name = '.$table_name."\n";
           
            create_data($db_id, $table_name, $field_name, $class_name, $file_name);

            if (true || in_array($db_id, array("data", "data_ext")))
            {
                create_model($table_name, $field_name, $class_name, $file_name);
                create_controller($table_name, $field_name, $class_name, $file_name, $columns_name_str);
                //create_view($table_name, $field_name, $class_name, $file_name);
            }

            echo "\n\n";
        }
        else
        {
            die('暂时禁止使用！');
            generate_model($table_row['Tables_in_' . DATABASE]);
            generate_controller($table_row['Tables_in_' . DATABASE]);
        }
    }

    echo "\n";
    echo '生成代码结束';
    echo "\n";    
}

function template_replace($template, $arr_replace)
{
	if (empty($arr_replace))
	{
		return $template;
	}

	$search  = array();
	$replace = array();

	foreach ($arr_replace as $key => $value)
	{
		$search[]  = '{{' . $key . '}}';
		$replace[] = $value;
	}

	return str_replace($search, $replace, $template);
}


function get_field_name($table_name)
{
    global $Db;
    $sql = "show columns from `".$table_name."` ";
    $arr_row = $Db->getRow($sql);
    $field_name = $arr_row['Field'];
    return $field_name;
}


function get_columns_name_str($table_name)
{
    $table_columns_sql = 'SELECT * FROM information_schema.columns WHERE TABLE_NAME="' . $table_name . '" AND TABLE_SCHEMA="' . DATABASE . '"';

    global $Db;

    global $table_columns_rows;
    $table_columns_rows = $Db->getAll($table_columns_sql);


    global $columns_id_name;
    $columns_id_name = get_id_columns_name($table_name);

    $column_pre = "        ";
    $column_str = '';
    $dot        = ';';
    $columns_len= count($table_columns_rows) ;

    $update_str = '';
    foreach ($table_columns_rows as $key=>$column_row)
    {
        if (is_numeric($column_row['COLUMN_NAME']))
        {
            $column_row['COLUMN_NAME'] = '`' . $column_row['COLUMN_NAME'] . '`';
        }

        if ($columns_len == $key+1)
        {
            $dot = ';';
        }

        if (COMMENT)
        {
            $column_row['COLUMN_COMMENT'] = str_replace("\r", "", $column_row['COLUMN_COMMENT']);
            $column_row['COLUMN_COMMENT'] = str_replace("\n", " ", $column_row['COLUMN_COMMENT']);
            $insert_column_comment    = '// ' . str_pad($column_row['COLUMN_COMMENT'], COLUMN_COMMENT_LEN + strlen($column_row['COLUMN_COMMENT']) - mb_strwidth($column_row['COLUMN_COMMENT'], 'UTF-8')) . '';
        }

        $insert_column_name        = '' . str_pad('$data[\'' . $column_row['COLUMN_NAME'] . '\']' . '', COLUMN_NAME_LEN-1);

        $update_column_name        = $insert_column_name . ' = ' . str_pad('request_string(\'' . $column_row['COLUMN_NAME'] . '\')', COLUMN_NAME_LEN) . '';


        if (COMMENT)
        {
            if (true || $columns_id_name != $column_row['COLUMN_NAME'])
            {
                $update_str        .= $column_pre . $update_column_name . $dot . " " . $insert_column_comment . "\n";
            }
        }
        else
        {
            if (true || $columns_id_name != $column_row['COLUMN_NAME'])
            {
                $update_str        .= $column_pre . $update_column_name . $dot  . "\n";
            }
        }
    }

    $dot        = ';';

    return $update_str;

    //return $table_columns_rows;
}


function get_class_name($table_name)
{
    $table_name = str_replace(TABEL_PREFIX, '', $table_name);

    $arr_table_name = explode("_",$table_name);
    $class_name = '';
    $i = 0;
    foreach($arr_table_name as $key => $value)
    {
        $class_name .= ucfirst($value);
        if(count($arr_table_name) > 1)
        {
            if ( $i == 0  )
            {
                $class_name .= "_";
            }
            $i++;
        }
    }
    return $class_name;
}
function get_file_name($class_name)
{
    $arr_class_name = explode("_",$class_name);
    if ( count($arr_class_name) > 1 )
    {
        $file_name = $arr_class_name[1];
    }
    else
    {
        $file_name = $class_name;
    }
    return $file_name;
}

function create_model($table_name, $field_name, $class_name, $file_name)
{
    $table_name = str_replace(TABEL_PREFIX, '', $table_name);

    $template_name = CODE_TEMPLATE_PATH.'/model.php';
    $arr_content = file($template_name);//echo $str;
    $content = implode("",$arr_content);
    $arr_replace = array();
    $arr_replace['class_name'] = $class_name;
    $arr_replace['table_name'] = $table_name;
    $arr_replace['field_name'] = $field_name;
    $arr_replace['file_name'] = $file_name;
    $arr_table_name = explode("_", $table_name);
    $dir_path = MOD_PATH;
    if(count($arr_table_name) > 1)
    {
        $dir_path .= "/".ucfirst($arr_table_name[0]);
        array_shift($arr_table_name);
    }
    if (!is_dir($dir_path))
    {
        mkdir($dir_path,0777);
    }
    $file = $dir_path . "/" . $file_name . "Model.php";//die("f=".$file);

    if(file_exists($file))
    {
        echo 'model 文件已经存在<br>';
    }
    else
    {
        $file_content = template_replace($content,$arr_replace);
        file_put_contents($file, $file_content);
    }    
}

function create_data($db_id, $table_name, $field_name, $class_name, $file_name)
{
    $table_name = str_replace(TABEL_PREFIX, '', $table_name);

    $tmp_name = 'data';
    
    if ($db_id == 'base')
    {
        $tmp_name = 'base';
    }
    
    $template_name = CODE_TEMPLATE_PATH . '/'. $tmp_name . '.php';
    $arr_content   = file($template_name);//echo $str;
    $content       = implode("", $arr_content);
    $arr_replace   = array();

    $arr_replace['db_id'] = $db_id;
    $arr_replace['class_name'] = $class_name;
    $arr_replace['table_name'] = $table_name;
    $arr_replace['field_name'] = $field_name;
    $arr_replace['file_name']  = $file_name;
    $arr_table_name = explode("_",$table_name);
    $arr_replace['table_prefix'] = $arr_table_name[0];
    $dir_path = MOD_PATH;

    if(count($arr_table_name) > 1)
    {
        $dir_path .= "/" . ucfirst($arr_table_name[0]);
        array_shift($arr_table_name);
    }

    if (!is_dir($dir_path))
    {
        mkdir($dir_path,0777);
    }

    $file = $dir_path . "/" . $file_name . ".php";//die("f=".$file);

    if(file_exists($file))
    {
        echo 'data 文件已经存在<br>';
    }
    else
    {
        $file_content = template_replace($content, $arr_replace);
        file_put_contents($file, $file_content);
    }    
}

function create_controller($table_name, $field_name, $class_name, $file_name, $columns_name_str)
{
    $table_name = str_replace(TABEL_PREFIX, '', $table_name);

    $template_name = CODE_TEMPLATE_PATH . '/controller.php';
    $arr_content = file($template_name);//echo $str;
    $content     = implode("", $arr_content);
    $arr_replace = array();

    $class_name_lc = $class_name;

    if (function_exists('lcfirst'))
    {
        $arr_replace['class_name_lc'] = lcfirst($class_name);
    }
    else
    {
        $class_name_lc[0] = strtolower($class_name_lc[0]);
        $arr_replace['class_name_lc'] =  $class_name_lc;
    }

    $arr_replace['class_name_lc'] = str_replace("_", "", $arr_replace['class_name_lc']);
    

    $arr_replace['class_name'] = $class_name;
    $arr_replace['table_name'] = $table_name;
    $arr_replace['field_name'] = $field_name;
    $arr_replace['file_name']  = $file_name;
    $arr_replace['columns_name_str']  = $columns_name_str;

    $arr_table_name = explode("_", $table_name);
    $dir_path = CTL_PATH;

    if(count($arr_table_name) > 1)
    {
        $dir_path .= "/" . ucfirst($arr_table_name[0]);
        array_shift($arr_table_name);
    }

    if (!is_dir($dir_path))
    {
        mkdir($dir_path,0777);
    }

    $file = $dir_path . "/" . $file_name . "Ctl.php";

    if(file_exists($file))
    {
        echo 'controller 文件已经存在<br>';
    }
    else
    {
        $file_content = template_replace($content, $arr_replace);
        file_put_contents($file, $file_content);
    }    
}
    
    

function create_view($table_name, $field_name, $class_name, $file_name)
{
    //
    $file_name =  $file_name . 'Ctl';
    $table_name = str_replace(TABEL_PREFIX, '', $table_name);

    $template_name = CODE_TEMPLATE_PATH . '/view.php';
    $arr_content = file($template_name);//echo $str;
    $content     = implode("", $arr_content);
    $arr_replace = array();

    $class_name_lc = $class_name;

    if (function_exists('lcfirst'))
    {
        $arr_replace['class_name_lc'] = lcfirst($class_name);
    }
    else
    {
        $class_name_lc[0] = strtolower($class_name_lc[0]);
        $arr_replace['class_name_lc'] =  $class_name_lc;
    }

    $arr_replace['class_name_lc'] = str_replace("_", "", $arr_replace['class_name_lc']);
    

    $arr_replace['class_name'] = $class_name;
    $arr_replace['table_name'] = $table_name;
    $arr_replace['field_name'] = $field_name;
    $arr_replace['file_name']  = $file_name;
    $arr_table_name = explode("_", $table_name);
    $dir_path = TPL_PATH;

    if(count($arr_table_name) > 1)
    {
        $dir_path .= "/" . ucfirst($arr_table_name[0]);
        array_shift($arr_table_name);
    }

    if (!is_dir($dir_path))
    {
        mkdir($dir_path,0777);
    }

    $dir_path = $dir_path . "/" . $file_name . "/";

    if (!is_dir($dir_path))
    {
        mkdir($dir_path,0777);
    }

    $met = 'index';

    $file = $dir_path . "/" . $met . '.php';
    $file_manage = $dir_path . '/manage.php';


    if(file_exists($file))
    {
        echo 'view 文件已经存在<br>';
    }
    else
    {
        $file_content = template_replace($content, $arr_replace);
        file_put_contents($file, $file_content);
        file_put_contents($file_manage, $file_content);
    }    
}
    
        
function generate_model($table_name)
{
    $table_columns_sql = 'SELECT * FROM information_schema.columns WHERE TABLE_NAME="' . $table_name . '" AND TABLE_SCHEMA="' . DATABASE . '"';
    
    global $Db;

    global $table_columns_rows;
    $table_columns_rows = $Db->getAll($table_columns_sql);
    
    if (!empty($table_columns_rows))
    {
        global $columns_id_name;
        $columns_id_name = get_id_columns_name($table_name);

        $column_pre = "                    ";
        $column_str = '';
        $dot        = ',';
        $columns_len= count($table_columns_rows) ;


        $insert_str        = '
        $query = \'
                INSERT INTO ' . $table_name . '
                (' . "\n";

        $select_str        = '
        $query = \'
                SELECT
                    SQL_CALC_FOUND_ROWS' . "\n";

        $select_num_str   = '
        $query = \'
                SELECT
                    FOUND_ROWS() num\';' . "\n";

        $update_str        = '
        $query = \'
                UPDATE
                    ' . $table_name . '
                SET' . "\n";

        $delete_str        = '
        $query = \'
                    DELETE FROM
                        ' . $table_name . '' . "\n";

        foreach ($table_columns_rows as $key=>$column_row)
        {
            if (is_numeric($column_row['COLUMN_NAME']))
            {
                $column_row['COLUMN_NAME'] = '`' . $column_row['COLUMN_NAME'] . '`';
            }
            if ($columns_len == $key+1)
            {
                $dot = ' ';
            }

            if (COMMENT)
            {
                $column_row['COLUMN_COMMENT'] = str_replace("\r", "", $column_row['COLUMN_COMMENT']);
                $column_row['COLUMN_COMMENT'] = str_replace("\n", " ", $column_row['COLUMN_COMMENT']);
                $insert_column_comment    = '/* ' . str_pad($column_row['COLUMN_COMMENT'], COLUMN_COMMENT_LEN + strlen($column_row['COLUMN_COMMENT']) - mb_strwidth($column_row['COLUMN_COMMENT'], 'UTF-8')) . '*/';
            }

            $insert_column_name        = '' . str_pad($column_row['COLUMN_NAME'] . '', COLUMN_NAME_LEN-1);
            //$update_column_name        = $insert_column_name . ' = "\' . mres(' . str_pad('$' . $column_row['COLUMN_NAME'], COLUMN_NAME_LEN) . ') . \'"';

            //$update_column_name        = $insert_column_name . ' = "\' . mres(' . str_pad('$a[\'' . $column_row['COLUMN_NAME'] . '\']', COLUMN_NAME_LEN) . ') . \'"';
            $update_column_name        = $insert_column_name . ' = "\' . ' . str_pad('$a[\'' . $column_row['COLUMN_NAME'] . '\']', COLUMN_NAME_LEN) . ' . \'"';


            if (COMMENT)
            {
                $insert_str        .= $column_pre . $insert_column_name . $dot . " " . $insert_column_comment . "\n";
                $select_str        .= $column_pre . $insert_column_name . $dot . " " . $insert_column_comment . "\n";

                if ($columns_id_name != $column_row['COLUMN_NAME'])
                {
                    $update_str        .= $column_pre . $update_column_name . $dot . " " . $insert_column_comment . "\n";
                }
            }
            else
            {
                $insert_str        .= $column_pre . $insert_column_name . $dot  . "\n";
                $select_str        .= $column_pre . $insert_column_name . $dot  . "\n";


                if ($columns_id_name != $column_row['COLUMN_NAME'])
                {
                    $update_str        .= $column_pre . $update_column_name . $dot  . "\n";
                }
            }
        }

        $insert_str        .= '                )
                VALUES
                (' . "\n";

        $select_str        .= '                FROM
                    ' . $table_name . '' . "\n";

        $dot        = ',';
        foreach ($table_columns_rows as $key=>$column_row)
        {
            if ($columns_len == $key+1)
            {
                $dot = ' ';
            }

            //$insert_column_name        = '"\' . mres(' . str_pad('$a[\'' . $column_row['COLUMN_NAME'] . '\']', COLUMN_NAME_LEN) . ') . \'"';
            $insert_column_name        = '"\' . ' . str_pad('$a[\'' . $column_row['COLUMN_NAME'] . '\']', COLUMN_NAME_LEN) . ' . \'"';

            $insert_str        .= $column_pre . $insert_column_name . $dot .  "\n";
        }


        $insert_str        .= '                    )\';';
        $select_str        .= '                    \';';
        $update_str        .= '                    \';';
        $delete_str        .= '                    \';';

        //echo $update_str;
        //echo $delete_str;
        //echo $select_str;
        //echo $insert_str;

        $model_class_name = get_model_class_name($table_name);
        $function_suffix_name = get_function_suffix_name($table_name); //函数名称

    $insert_method = '
    /**
     * 插入
     *
     * @param array $a 信息
     * @return bool $rs 是否成功
     * @access protected
     */
    protected function _insert' . $function_suffix_name . '(&$a)
    {
        addslashes_array($a);' . "\n" . $insert_str . '

        $rs = $this->sql->exec($query);

        return $rs;
    }' . "\n";

    $table_prefix_row = explode('_', $model_class_name);

    if (true || 'Base' == $table_prefix_row[0])
    {
    $select_method = '
    /**
     * 取得
     *
     * @param bool $cache_del 删除缓存标记
     * @return array $rows 信息
     * @access protected
     */
    protected function _select' . $function_suffix_name . '()
    {' . $select_str . '

        $query  .= $this->sql->getWhere();

        $rs = $this->sql->getAll($query);
        $rows = array();

        if($rs)
        {
            foreach ($rs as $k=>$v)
            {
                $rows[$v[\'' . $columns_id_name . '\']] = $v;
            }
        }

        return $rows;
    }' . "\n";

    }
    else
    {
    $select_method = '
    /**
     * 取得
     *
     * @return array $rows 信息
     * @access protected
     */
    protected function _select' . $function_suffix_name . '()
    {' . $select_str . '

        $query  .= $this->sql->getWhere();

        $rows = $this->sql->getAll($query);

        return $rows;
    }' . "\n";

    }

    $select_num_method = '
    /**
     * 取得行数
     *
     * @return array $rows 信息
     * @access protected
     */
    protected function _selectFoundRows()
    {' . $select_num_str . '

        $row = $this->sql->getRow($query);

        return $row[\'num\'];
    }' . "\n";

    $update_method = '
    /**
     * 更新
     *
     * @param array $a 更新的数据
     * @return bool $update_flag 是否成功
     * @access protected
     */
    protected function _update' . $function_suffix_name . '(&$a)
    {
        addslashes_array($a);' . "\n" . $update_str . '

        $query  .= $this->sql->getWhere();
        $update_flag  = $this->sql->exec($query);

        return $update_flag;
    }' . "\n";

    $delete_method = '
    /**
     * 删除操作
     *
     * @return bool $del_flag 是否成功
     * @access protected
     */
    protected function _delete' . $function_suffix_name . '()
    {' . $delete_str . '

        $query  .= $this->sql->getWhere();
        $del_flag = $this->sql->exec($query);

        return $del_flag;
    }' . "\n";

    //echo $insert_method;
    //echo $select_method;
    //echo $update_method;
    //echo $delete_method;

    $ins_method = '
    /**
     * 插入
     *
     * @param array $a 信息
     * @return bool $rs 是否成功
     * @access public
     */
    public function add' . $function_suffix_name . '(&$a)
    {
        $rs = $this->_insert' . $function_suffix_name . '($a);

        return $rs;
    }' . "\n";
    if ('Base' == $table_prefix_row[0])
    {
    $sel_method = '
    /**
     * 取得
     *
     * @param int $' . $columns_id_name . ' ' . $columns_id_name . '
     * @return array $rows 信息
     * @access public
     */
    public function get' . $function_suffix_name . '($' . $columns_id_name . '=null, $arr_key=null)
    {
       $rows = array();
 
        if (is_array($' . $columns_id_name . '))
        {
            if (CHE)
            {
                $need_cache_id_name = array();

                $rows = $this->getCacheRow($' . $columns_id_name . ', $need_cache_id_name);
                $rows_db = array();

                if (!empty($need_cache_id_name))
                {
                    $this->sql->setWhere(\'' . $columns_id_name . '\', $need_cache_id_name, \'IN\');
                    $rows_db = $this->_select' . $function_suffix_name . '();
                }

                $this->setCacheRow($rows_db);

                $rows = $rows = $rows + $rows_db;
            }
            else
            {
                $this->sql->setWhere(\'' . $columns_id_name . '\', $' . $columns_id_name . ', \'IN\');
                $rows = $this->_select' . $function_suffix_name . '();
            }
        }
        else
        {
            if (CHE)
            {
                $rows = $this->getCache($' . $columns_id_name . ');
            }

            if (false !== $rows)
            {
                if ($' . $columns_id_name . ')
                {
                    $this->sql->setWhere(\'' . $columns_id_name . '\', $' . $columns_id_name . ');
                }

                $rows = $this->_select' . $function_suffix_name . '();

                if (CHE && $rows)
                {
                    $this->setCache($rows);
                }
            }

        }
        
        if ($arr_key && !empty($rows))
        {
            $rows = array_reset($arr_key, $rows);
        }

        return $rows;
    }' . "\n";
    }
    else
    {    $sel_method = '
    /**
     * 取得
     *
     * @param int $' . $columns_id_name . ' ' . $columns_id_name . '
     * @return array $rows 信息
     * @access public
     */
    public function get' . $function_suffix_name . '($' . $columns_id_name . '=null, $arr_key=null)
    {
       $rows = array();
 
        if (is_array($' . $columns_id_name . '))
        {
            if (CHE)
            {
                $need_cache_id_name = array();

                $rows = $this->getCacheRow($' . $columns_id_name . ', $need_cache_id_name);
                $rows_db = array();

                if (!empty($need_cache_id_name))
                {
                    $this->sql->setWhere(\'' . $columns_id_name . '\', $need_cache_id_name, \'IN\');
                    $rows_db = $this->_select' . $function_suffix_name . '();
                }

                $this->setCacheRow($rows_db);

                $rows = $rows = $rows + $rows_db;
            }
            else
            {
                $this->sql->setWhere(\'' . $columns_id_name . '\', $' . $columns_id_name . ', \'IN\');
                $rows = $this->_select' . $function_suffix_name . '();
            }
        }
        else
        {
            if (CHE)
            {
                $rows = $this->getCache($' . $columns_id_name . ');
            }

            if (false !== $rows)
            {
                if ($' . $columns_id_name . ')
                {
                    $this->sql->setWhere(\'' . $columns_id_name . '\', $' . $columns_id_name . ');
                }
                else
                {
                    throw new Exception(_(\'need input ' . $columns_id_name . '\'));
                }

                $rows = $this->_select' . $function_suffix_name . '();

                if (CHE && $rows)
                {
                    $this->setCache($rows);
                }
            }

        }
        
        if ($arr_key && !empty($rows))
        {
            $rows = array_reset($arr_key, $rows);
        }

        return $rows;
    }' . "\n";
    }

    $sel_num_method = '
    /**
     * 取得影响的行数
     *
     * @return int $num 行数
     * @access public
     */
    public function getFoundRows()
    {
        $num = $this->_selectFoundRows();

        return $num;
    }' . "\n";

    $upd_method = '
    /**
     * 更新
     *
     * @param array $a
     * @return bool $update_flag 是否成功
     * @access public
     */
    public function edit' . $function_suffix_name . '($' . $columns_id_name . '=null, &$a)
    {
        $update_flag =false;

        if ($' . $columns_id_name . ')
        {
            $this->sql->setWhere(\'' . $columns_id_name . '\', $' . $columns_id_name . ');
            $update_flag  = $this->_update' . $function_suffix_name . '($a);

            if (CHE && $update_flag)
            {
                $this->removeCache($' . $columns_id_name . ');
            }
        }
        else
        {

        }

        return $update_flag;
    }' . "\n";

    $del_method = '
    /**
     * 删除操作
     *
     * @param int $' . $columns_id_name . ' ' . $columns_id_name . '
     * @return bool $del_flag 是否成功
     * @access public
     */
    public function remove' . $function_suffix_name . '($' . $columns_id_name . '=null)
    {
        $del_flag = false;

        if ($' . $columns_id_name . ')
        {
            $this->sql->setWhere(\'' . $columns_id_name . '\', $' . $columns_id_name . ');
            $del_flag = $this->_delete' . $function_suffix_name . '();

            if (CHE && $del_flag)
            {
                $this->removeCache($' . $columns_id_name . ');
            }
        }

        return $del_flag;
    }' . "\n";

    //$ins_method = '';
    //$sel_method = '';
    //$upd_method = '';
    //$del_method = '';
    }

    $year    = date('Y');

    global $package;
    global $db_id;
    if ('Base' == $table_prefix_row[0])
    {
        $zero_model_name = 'Yf_ModelSingleton';
        $dbId = '\'' . $db_id . '\'';
    }
    else
    {
        $zero_model_name = 'Yf_Model';
        $dbId = '\'' . $db_id . '\'';
    }


/**
 * @name  名字
 * @abstract  申明变量/类/方法
 * @access  指明这个变量、类、函数/方法的存取权限
 * @author  函数作者的名字和邮箱地址
 * @category  组织packages
 * @copyright  指明版权信息
 * @const  指明常量
 * @deprecate  指明不推荐或者是废弃的信息
 * @example  示例
 * @exclude  指明当前的注释将不进行分析，不出现在文挡中
 * @final  指明这是一个最终的类、方法、属性，禁止派生、修改。
 * @global  指明在此函数中引用的全局变量
 * @include  指明包含的文件的信息
 * @link  定义在线连接
 * @module  定义归属的模块信息
 * @modulegroup  定义归属的模块组
 * @package  定义归属的包的信息
 * @param  定义函数或者方法的参数信息
 * @return  定义函数或者方法的返回信息
 * @see  定义需要参考的函数、变量，并加入相应的超级连接。
 * @since  指明该api函数或者方法是从哪个版本开始引入的
 * @static  指明变量、类、函数是静态的。
 * @throws  指明此函数可能抛出的错误异常,极其发生的情况
 * @todo  指明应该改进或没有实现的地方
 * @var  定义说明变量/属性。
 * @version  定义版本信息
 */
    $php_start = '<?php if (!defined(\'ROOT_PATH\')) exit(\'No Permission\');
/**
 * 
 * 
 * 
 * @category   Game
 * @package    ' . $package . '
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) ' . $year . '远丰仁商
 * @version    1.0
 * @todo       
 */
class ' . $model_class_name . ' extends ' . $zero_model_name .'
{
    public $_cacheKeyPrefix  = \'c|' . $table_name . '|\';
    public $_cacheName       = \'' . strtolower($table_prefix_row[0]). '\';
    public $_tableName       = \'' . $table_name . '\';
    public $_tablePrimaryKey = \'' . $columns_id_name . '\';

    /**
     * Constructor
     * 
     * @param string $user  User Object
     * @var   string $db_id 指定需要连接的数据库Id
     * @return void
     */
    public function __construct(&$db_id=' . $dbId . ', &$user=null)
    {
        parent::__construct($db_id, $user);
    }' . "\n";

    $php_end = '}'  . "\n" . '?>';

	echo $model_class_name;
	echo "\n";

    $file_path = str_replace('_', '/', $model_class_name) . '.php';

    if ('Base' == $table_prefix_row[0])
    {
        $file_contents = $php_start . $select_method . $sel_method . $php_end;
    }
    else
    {
        $file_contents = $php_start . $insert_method . $select_method . $select_num_method . $update_method . $delete_method . $ins_method . $sel_method . $sel_num_method . $upd_method . $del_method . $php_end;
    }

    if (is_file(MOD_PATH_TMP . '/' . $file_path))
    {
        echo('file has exists,file_path='.$file_path);

        if ('DEL' == ACT)
        {
            clean_cache(MOD_PATH_TMP . '/temp', true);
            unlink(MOD_PATH_TMP . '/' . $file_path . '.tmp');
        }
        else
        {
            //make_dir_path(dirname(MOD_PATH_TMP . '/temp/' . $file_path));
            make_dir_path(dirname(MOD_PATH_TMP . '/' . $file_path));
            //file_put_contents(MOD_PATH_TMP . '/temp/' . $file_path, $file_contents);
            //file_put_contents(MOD_PATH_TMP . '/' . $file_path . '.tmp', $file_contents);
        }
    }
    else
    {
        make_dir_path(dirname(MOD_PATH_TMP . '/' . $file_path));
        file_put_contents(MOD_PATH_TMP . '/' . $file_path, $file_contents);
    }

    echo '生成:' . $file_path . ' 完成';
    echo "\n";

	//继承class ext。
	$model_class_name_ext = $model_class_name . MODEL_ENDFIX;

    $php_start = '<?php if (!defined(\'ROOT_PATH\')) exit(\'No Permission\');
/**
 * 
 * 
 * 
 * @category   Game
 * @package    ' . $package . '
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) ' . $year . '远丰仁商
 * @version    1.0
 * @todo       
 */
class ' . $model_class_name_ext . ' extends ' . $model_class_name .'
{
    /**
     * Constructor
     * 
     * @param string $user  User Object
     * @var   string $db_id 指定需要连接的数据库Id
     * @return void
     */
    public function __construct(&$db_id=' . $dbId . ', &$user=null)
    {
        parent::__construct($db_id, $user);
    }' . "\n";

    $php_end = '}'  . "\n" . '?>';

	echo $model_class_name_ext;
	echo "\n";

    $file_path = str_replace('_', '/', $model_class_name_ext) . '.php';
    $file_contents = $php_start . $php_end;

    if (is_file(MOD_PATH_TMP . '/' . $file_path))
    {
        if ('DEL' == ACT)
        {
            clean_cache(MOD_PATH_TMP . '/temp', true);
            unlink(MOD_PATH_TMP . '/' . $file_path . '.tmp');
        }
    }
    else
    {
        make_dir_path(dirname(MOD_PATH_TMP . '/' . $file_path));
        file_put_contents(MOD_PATH_TMP . '/' . $file_path, $file_contents);
    }
}

function get_model_class_name($table_name)
{
    global $package;
    $table_name_row = explode('_', $table_name);

    if (PRE)
    {
        array_shift($table_name_row);
    }

    $model_class_name = MODEL_CLASS_NAME;
    $class_depth = count($table_name_row);

    foreach ($table_name_row as $key=>$value)
    {
        if (0 == $key)
        {
            if ('' == $value)
            {
                $package = '';
                return $table_name;
            }

            $package = ucfirst($value);

            if (1 == $class_depth)
            {
                $model_class_name .= ucfirst($value); 
            }
            else
            {
                $model_class_name .= ucfirst($value) . '_'; 
            }
        }
        else
        {
            $model_class_name .= ucfirst($value); 
        }
    }

    return $model_class_name;
}

function get_controller_class_name($table_name)
{
    global $package;
    $table_name_row = explode('_', $table_name);

    if (PRE)
    {
        array_shift($table_name_row);
    }

    $controller_class_name = CONTROLLER_CLASS_NAME;
    $class_depth = count($table_name_row);

    foreach ($table_name_row as $key=>$value)
    {
        if (0 == $key)
        {
            if ('' == $value)
            {
                $package = '';
                return $table_name;
            }

            $package = ucfirst($value);

            if (1 == $class_depth)
            {
                $controller_class_name .= ucfirst($value); 
            }
            else
            {
                $controller_class_name .= ucfirst($value) . '_'; 
            }
        }
        else
        {
            $controller_class_name .= ucfirst($value); 
        }
    }

    //foreach ($table_name_row as $key=>$value)
    //{
        //$controller_class_name .= ucfirst($value); 
    //}

    return $controller_class_name;
}

function get_function_suffix_name($table_name)
{
    $table_name_row = explode('_', $table_name);

    if (PRE)
    {
        array_shift($table_name_row);
    }

    $function_suffix_name = '';

    foreach ($table_name_row as $key=>$value)
    {
        if (0 == $key)
        {
            if ('' == $value)
            {
                return $table_name;
            }
        }
        else
        {
            $function_suffix_name .= ucfirst($value); 
        }
    }

    return $function_suffix_name;
}


function get_id_columns_name($table_name)
{
    //真正的数据, 需要从表中取得.
    global $Db;
    $columns = $Db->getRow('SHOW  COLUMNS FROM ' . $table_name . '');

    $columns_name = $columns['Field'];


    return $columns_name;
}


function generate_controller($table_name)
{
    global $table_columns_rows;
    global $columns_id_name;


    if (!empty($table_columns_rows))
    {
        $model_class_name = get_model_class_name($table_name) . MODEL_ENDFIX;
        $controller_class_name = get_controller_class_name($table_name);
        $function_suffix_name = get_function_suffix_name($table_name); //函数名称

    $ins_method = '
    /**
     * 插入
     *
     * @param array $a 信息
     * @return bool $rs 是否成功
     * @access public
     */
    public function add()
    {
        $a = $_REQUEST;
        $' . $model_class_name . ' = new ' . $model_class_name . '();

        $rs = $' . $model_class_name . '->add' . $function_suffix_name . '($a);

        return $rs;
    }' . "\n";

    $sel_method = '
    /**
     * 取得
     *
     * @param int $id id
     * @return array $rows 信息
     * @access public
     */
    public function get()
    {
        $' . $model_class_name . ' = new ' . $model_class_name . '();
        $rows = $' . $model_class_name . '->get' . $function_suffix_name . '();

        if (\'e\' == $this->typ)
        {
            print_r($rows);
        }
        else
        {
            $this->data->addBody(100, $rows);
        }

    }' . "\n";


    $upd_method = '
    /**
     * 更新
     *
     * @param array $a
     * @return bool $update_flag 是否成功， false：执行错误， num：影响的行数，如果更新数据一样或者没有where条件的数据，返回应该为0
     * @access public
     */
    public function edit()
    {
        $' . $columns_id_name . ' = request_string(\'' . $columns_id_name . '\');
        $a = $_REQUEST;
        $' . $model_class_name . ' = new ' . $model_class_name . '();
        $update_flag  = $' . $model_class_name . '->edit' . $function_suffix_name . '($' . $columns_id_name . ', $a);

        $this->data->addBody(100, array($update_flag));

        return $update_flag;
    }' . "\n";

    $del_method = '
    /**
     * 删除操作
     *
     * @param int $' . $columns_id_name . ' ' . $columns_id_name . '
     * @return bool $del_flag 是否成功
     * @access public
     */
    public function remove()
    {
        $' . $columns_id_name . ' = request_string(\'' . $columns_id_name . '\');
        $' . $model_class_name . ' = new ' . $model_class_name . '();

        $del_flag = $' . $model_class_name . '->remove' . $function_suffix_name . '($' . $columns_id_name . ');

        $this->data->addBody(100, array($del_flag));

        return $del_flag;
    }' . "\n";

    //$ins_method = '';
    //$sel_method = '';
    //$upd_method = '';
    //$del_method = '';
    }

    $year    = date('Y');

    global $package;


    $table_prefix_row = explode('_', $model_class_name);

    $php_start = '<?php if (!defined(\'ROOT_PATH\')) exit(\'No Permission\');
/**
 * 控制器demo，有程序自从生成，作为部分正是项目复制代码使用。
 * 
 * 
 * @category   Game
 * @package    ' . $package . '
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) ' . $year . '远丰仁商
 * @version    1.0
 * @todo       
 */
class ' . $controller_class_name . CONTROLLER_ENDFIX . ' extends Yf_AppController
{
    /**
     * Constructor
     * 
     * @param string $user  User Object
     * @var   string $db_id 指定需要连接的数据库Id
     * @return void
     */
    public function init()
    {
        $this->model = new ' . $model_class_name . '();
    }' . BR;

    $php_end = '}'  . "\n" . '?>';

    echo $controller_class_name . CONTROLLER_ENDFIX;
    echo "\n";

    $file_path = str_replace('_', '/', $controller_class_name . CONTROLLER_ENDFIX) . '.php';

    $file_contents = $php_start  . $ins_method . $sel_method . $upd_method . $del_method . $php_end;


    if ('Base' != $table_prefix_row[0])
    {

        if (is_file(CTL_PATH . '/' . $file_path))
        {
			/*
            if ('DEL' == ACT)
            {
                clean_cache(CTL_PATH . '/temp', true);
                unlink(CTL_PATH . '/' . $file_path . '.tmp');
            }
            else
            {
                make_dir_path(dirname(CTL_PATH . '/temp/' . $file_path));
                make_dir_path(dirname(CTL_PATH . '/' . $file_path));
                file_put_contents(CTL_PATH . '/temp/' . $file_path, $file_contents);
                file_put_contents(CTL_PATH . '/' . $file_path . '.tmp', $file_contents);
            }
			*/
        }
        else
        {
            make_dir_path(dirname(CTL_PATH . '/' . $file_path));
            file_put_contents(CTL_PATH . '/' . $file_path, $file_contents);
        }

        echo '生成:' . $file_path . ' 完成';
        echo "\n";
        
        /*
        $view_path = str_replace('_', '/', $controller_class_name . CONTROLLER_ENDFIX);

        if (!is_file(TPL_PATH . '/' . $view_path))
        {
            make_dir_path(TPL_PATH . '/' . $view_path);

            if (!is_file(TPL_PATH . '/' . $view_path . '/' . 'add.php'))
            {
                file_put_contents(TPL_PATH . '/' . $view_path . '/' . 'add.php', '');
                file_put_contents(TPL_PATH . '/' . $view_path . '/' . 'get.php', '');
                file_put_contents(TPL_PATH . '/' . $view_path . '/' . 'remove.php', '');
                file_put_contents(TPL_PATH . '/' . $view_path . '/' . 'edit.php', '');
            }

        }

        echo '生成:' . $file_path . ' 完成';
        echo "\n";
        */
    }
}
?>