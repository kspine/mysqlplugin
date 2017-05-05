<?php

ini_set('memory_limit','1024M');

date_default_timezone_set('PRC');

//修改下面的常量来指定数据库信息
define('MYHOST', '192.168.1.204');//mysql主机名称
define('MYPORT', '3306');//mysql端口
define('MYDB', 'empery');//mysql数据库名称
define('MYUSER', 'root');//mysql用户名称
define('MYPWD', 'root');//mysql密码

define('MGHOST', '127.0.0.1');//mongodb主机名称
define('MGPORT', '27017');//mongodb端口
define('MGDB', 'empery');//mongodb数据库名称
define('MGUSER', '');//mongodb用户名
define('MGPWD', '');//mongodb密码

define('MYSQLTOMONGO', 1);//mysql到mongodb
define('MONGOTOMYSQL', 2);//mongodb到mysql[*未实现]

define('MAP_WIDTH', 600);//地图宽
define('MAP_HEIGHT', 640);//地图高

define("APP_PATH",  realpath(dirname(__FILE__) . '/../'));

require_once APP_PATH . "/application/vendor/autoload.php";

runCmd();//执行命令
/**
 * 命令行调用说明
 * 1.导出mysql数据到mongodb
 * 支持参数：
 *   --clear, -c 导出前先清除mongodb里面的旧数据
 *   --table=table1,table2,table3...  此参数表示导出指定的表，如果未指定，则导出mysql中的全部数据
 * 示例：
 *   php etldb.php export
 *   php etldb.php export --clear --table=Center_MapCellAttribute,Center_MapEvent
 * 2.校验mysql导出的到mongodb的完整性
 * 支持参数：
 *   --table=table1,table2,table3...  此参数表示导出指定的表，如果未指定，则导出mysql中的全部数据
 * 示例：
 *   php etldb.php check --table=Center_MapCellAttribute,Center_MapEvent
 */


//执行命令
function runCmd() {

    if (php_sapi_name() !== 'cli') {
        echo '<h1>强烈建议转换程序在命令行模式下运行！！！</h1>';
    }

    $pargv = $_SERVER['argv'];
    if (!isset($pargv[1])) {
        echo "error: empty command, please check it. \n";
        cmdHelp();
        exit;
    }

    $cmd = 'cmd' . ucfirst($pargv[1]);
    if (!function_exists($cmd)) {
        echo 'error: this command undefined! ' . $cmd, "\n";
        cmdHelp();
        exit;
    }

    $etl = EtlDb::create(array(//mysql配置信息
        'hostname' => MYHOST,
        'port' => MYPORT,
        'dbname'   => MYDB,
        'user'     => MYUSER,
        'password' => MYPWD
    ), array(//mongodb配置信息
        'hostname' => MGHOST,
        'port' => MGPORT,
        'dbname'   => MGDB,
        'user' => MGUSER,
        'password' => MGPWD
    ));

    $etl->setRule('Center_Account', array(//设置转换规则
        'activated' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_AuctionTransaction', array(//设置转换规则
        'committed' => array('dt_bool' => array(1)),//数据转换为boolean类型
        'cancelled' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_BattleRecord', array(//设置转换规则
        'deleted' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_BattleRecordCrate', array(//设置转换规则
        'deleted' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_CastleBattalion', array(//设置转换规则
        'enabled' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_Mail', array(//设置转换规则
        'system' => array('dt_bool' => array(1)),//数据转换为boolean类型
        'read' => array('dt_bool' => array(1)),//数据转换为boolean类型
        'attachments_fetched' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_MapCell', array(//设置转换规则
        'acceleration_card_applied' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_MapObject', array(//设置转换规则
        'garrisoned' => array('dt_bool' => array(1)),//数据转换为boolean类型
        'coord_hint' => array('*coords_set' => array('%s,%s', 'x', 'y')),//设置转换规则
    ))->setRule('Center_MapWorldActivity', array(//设置转换规则
        'finish' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_PaymentTransaction', array(//设置转换规则
        'committed' => array('dt_bool' => array(1)),//数据转换为boolean类型
        'cancelled' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_Task', array(//设置转换规则
        'rewarded' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_TaxRecord', array(//设置转换规则
        'deleted' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('League_Info', array(//设置转换规则
        '_id' => array('*pk_set' => array('', 'league_uuid')),//设置转换后的主键
    ))->setRule('Center_LegionTask', array(//设置转换规则
        'rewarded' => array('dt_bool' => array(1)),//设置转换后的主键
        'deleted' => array('dt_bool' => array(1)),//设置转换后的主键
    ))->setRule('Center_MapEvent', array(//设置转换规则
        '_id' => array('*coords_set2' => array('%s,%s,%s,%s', 'x', 'y')),//设置转换后的主键
    ))->setRule('Center_MapWorldActivityAccumulate', array(//设置转换规则
        'rewarded' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_MapWorldActivity', array(//设置转换规则
        'finish' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_MapWorldActivityRank', array(//设置转换规则
        'rewarded' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_CastleTechEra', array(//设置转换规则
        'unlocked' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_FriendPrivateMsgRecent', array(//设置转换规则
        'sender' => array('dt_bool' => array(1)),//数据转换为boolean类型
        'read' => array('dt_bool' => array(1)),//数据转换为boolean类型
        'deleted' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_FriendRecord', array(//设置转换规则
        'deleted' => array('dt_bool' => array(1)),//数据转换为boolean类型
    ))->setRule('Center_MapActivityRank', array(//设置转换规则))
        'rewarded' => array('dt_bool' => array(1)),
    ));
    $etl->newIndex('Center_MapObject', array(//地图表设置表的索引
        array('map_object_type_id' => 1),
        array('owner_uuid' => 1),
        array('parent_object_uuid' => 1),
        array('coord_hint' => 1),
    ))->newIndex('Center_Account', array(//帐号表设置表的索引
        //array(array('login_name' => 1), array('unique' => true)),
    ))->newIndex('Center_SpyReport',array(
        array('account_uuid' => 1),
        array('spy_account_uuid' => 1),
    ));

    array_shift($pargv);
    array_shift($pargv);

    call_user_func_array($cmd, array($etl, $pargv));
}


/**
 * 导出mysql数据到mongodb
 * @param type $obj
 * @param type $pargv Description
 */
function cmdExport($obj, $pargv) {

    foreach ($pargv as $value) {
        if ($value == '--clear' || $value == '-c') {//导出前清表
            $obj->setOptions('ClearData', true);
        } elseif (stripos($value, '--table=') === 0) {//仅导出指定的表
            $tables = substr($value, 8);
            $obj->setProcessTable(explode(',', $tables));
        } elseif ($value == '--nobatch') {//取消批量插入
            $obj->setOptions('NoBatch', true);
        } elseif ($value == '--onlystruct' || $value == '-s') {//仅仅导出表结构
            $obj->setOptions('OnlyStruct', true);
        } elseif ($value == '--ignore_error') {//出现错误后继续插入处理
            $obj->setOptions('IgnoreError', true);
        } elseif ($value == '-h' || $value == '--help' || $value == '?'){
            cmdHelp();
        } elseif ($value == '--delinvalid'){
            $obj->clearIdleData();
        }
    }

    //启动转换
    $obj->export();//开始导出
}

/**
 * 验证导出到mongodb的数据是否正确
 * @param type $obj
 * @param type $pargv Description
 */
function cmdCheck($obj, $pargv) {

    foreach ($pargv as $value) {
        if (stripos($value, '--table=') === 0) {//仅导出指定的表
            $tables = substr($value, 8);
            $obj->setProcessTable(explode(',', $tables));
        } elseif ($value == '-h' || $value == '--help' || $value == '?'){//帮助
            cmdHelp();
        }
    }

    $obj->checkData($pargv);
}

/**
 * 帮助说明
 */
function cmdHelp() {

    echo <<<EOF
  命令行调用说明
  1.导出mysql数据到mongodb
  支持参数：
    --clear, -c 导出前先清除mongodb里面的旧数据
    --table=table1,table2,table3...  此参数表示导出指定的表，如果未指定，则导出mysql中的全部数据
    --nobatch  取消批量导出，使用此项，将逐条的写入
    --ignore_error  忽略错误，使用此项，如果导出出现错误时，会继续的导出后面的数据，否则会中断当前的数据导出
    --onlystruct, -s  仅仅导出mysql的表名和索引
	--delinvalid  导出前删除无效的数据
  示例： 
    php etldb.php export
    php etldb.php export --clear --table=Center_MapCellAttribute,Center_MapEvent
  2.校验mysql导出的到mongodb的完整性
  支持参数：
    --table=table1,table2,table3...  此参数表示导出指定的表，如果未指定，则导出mysql中的全部数据
  示例：
    php etldb.php check
    php etldb.php check --table=Center_MapCellAttribute,Center_MapEvent
EOF;
    echo "\n\n";
    exit;
}


/**
 * EtlMongoDb类
 */
class EtlMongoDb {

    private static $dbh_instance;

    public static function init($scheme = null, $config = array(), $reconnect = false)
    {
        if($scheme == null){
            $scheme = MGDB;
        }
        if (empty($config)) {
            return false;
        }
        if ($reconnect == true || !isset(self::$dbh_instance[$scheme])) {
            self::mongodb_client($scheme, $config);
        }
        return self::$dbh_instance[$scheme];
    }

    public static function mongodb_client($scheme, $config = null)
    {
        $db = new MongoDB\Client($config['dsn']);
        self::$dbh_instance[$scheme] = $db;
        return true;
    }
}

/**
 * 数据转换类
 */
class EtlDb {

    private $mysql = array(
        'hostname' => 'localhost',
        'port' => '3306',
        'dbname'   => '',
        'user'     => 'root',
        'password' => '',
    );

    private $mongodb = array(
        'hostname' => 'localhost',
        'port' => '27017',
        'dbname'   => '',
        'user'     => 'root',
        'password' => '',
    );

    private $_rule = array(//转换规则
    );
    private $_index = array(//表索引
    );

    private $db_mysql = null;
    private $db_mongo = null;

    private $_options = array(//转换时的操作选项
        'ClearData' => false,//转换前是否清除数据
        'Tables'    => array(),//要处理的表名，后面只针对这些表处理，导出或校验
        'NoBatch'   => false,//取消批量处理
        'IgnoreError' => false,//忽略错误
        'OnlyStruct' => false, //仅仅导出表结构
    );

    private $_logPath = './logs';//日志目录

    /**
     *
     * @param type $mysql  mysql的数据库配置信息
     * @param type $mongodb  monogodb数据配置信息
     * @throws Exception
     */
    public function __construct($mysql, $mongodb) {

        if (isset($mysql['hostname']) && isset($mysql['port']) && isset($mysql['dbname']) && isset($mysql['user']) && isset($mysql['password'])) {
            $this->mysql = $mysql;
        }
        if (isset($mongodb['hostname']) && isset($mongodb['port']) && isset($mongodb['dbname'])) {
            $this->mongodb = $mongodb;
        }

        if (!class_exists('MongoDB\Client')) {
            echo 'MongoDB\Client not found';
            exit;
        }

        if (!class_exists('mysqli')) {
            echo "mysqli not found";
            exit;
        }

        $this->db_mysql = new mysqli($this->mysql['hostname'], $this->mysql['user'], $this->mysql['password'], $this->mysql['dbname'], $this->mysql['port']);
        if ($this->db_mysql->connect_errno) {
            echo "mysql连接失败!" . $this->db_mysql->connect_error;
            exit;
        }

        $this->db_mysql->query("SET NAMES 'utf8'");

        try {

            $conn = EtlMongoDb::init(MGDB,
                array("dsn"=>"mongodb://" . MGHOST . ":" . MGPORT,));

            $scheme = MGDB;
            $db = $conn->$scheme;

            $this->db_mongo = $db;
        } catch (Exception $e) {
            echo "mongodb连接失败！" . $e->getMessage();
            exit;
        }
    }

    /**
     * 创建数据转换类实例
     * @param type $mysql
     * @param type $mongodb
     * @return \class
     */
    public static function create($mysql, $mongodb) {

        $class = __CLASS__;

        return new $class($mysql, $mongodb);
    }

    /**
     * 设置转换规则
     * @param string $table 转换的表
     * @param array $rule  规则列表， 格式为：
     * array(
     *   '字段名1' => array(
     *       '规则名' => array(转换关系,....)
     *   ),
     *   '字段名2' => array(
     *       '规则名' => 转换关系,....
     *   ),
     * )
     * 规则名有：
     * dt_bool(数据类型转换) = array(代表true的值)  将数据转换为boolean类型
     *
     * @return \EtlDb
     */
    public function setRule($table, $rule) {

        $this->_rule[$table] = $rule;

        return $this;
    }

    /**
     * 创建索引
     * @param type $table
     * @param type $index 索引 ，参看MongoCollection::createIndex的相关参数
     * @return \EtlDb
     */
    public function newIndex($table, $index) {

        $this->_index[$table] = $index;

        return $this;
    }

    /**
     * 设置处理参数
     * @param type $key
     * @param type $value
     */
    public function setOptions($key, $value) {

        $this->_options[$key] = $value;
        return $this;
    }

    /**
     * 设置要处理的表，后面将只处理这些表
     * @param type $tables
     */
    public function setProcessTable($tables) {

        if (is_array($tables) && count($tables) > 0) {
            $this->_options['Tables'] = array_merge($this->_options['Tables'], $tables);
        } else if (is_string($tables) && !empty($tables)) {
            $this->_options['Tables'][] = $tables;
        }

        return $this;
    }

    /**
     * 开始转换数据
     * @param int $type  1 mysql to mongodb,  2 mongodb to mysql，目前仅支持 1将mysql导出到mongo
     */
    public function export($type = MYSQLTOMONGO) {

        $func = '_trans';
        if ($type == MYSQLTOMONGO) {//将
            $func .= 'MysqlToMongo';
        } else {
            $func .= 'MongoToMysql';
        }

        if (method_exists($this, $func)) {
            $this->$func();
        } else {
            echo 'function undefined : ' . $func;
        }
    }

    /**
     * 以单记录插入方式从mysql数据导出到mongodb
     */
    private function _transMysqlToMongo() {

        $tableCount = count($this->_options['Tables']);
        $isAll = $tableCount == 0;//是否导出mysql的全部表到mongodb

        $result = $this->db_mysql->query("SHOW TABLES");
        $this->slog(sprintf("mysql total (%d) tables, (%d) will be export to mongodb", $result->num_rows, ($tableCount > 0 ? $tableCount : $result->num_rows)));
        $stepPos = 1;
        while($row = $result->fetch_array()) {

            if ($isAll || in_array($row[0], $this->_options['Tables'])) {
                $this->_saveToMongo($row[0], $stepPos);
                $stepPos++;
            }
        }
        $this->slog(sprintf("[all table(%d) has done.]\n\n", ($tableCount > 0 ? $tableCount : $result->num_rows)));
    }

    /**
     * 读取mysql数据并直接保存到mongodb
     * @param type $tableName
     * @param type $stepPos
     * @return boolean
     */
    private function _saveToMongo($tableName, $stepPos) {

        $result = $this->db_mysql->query('SELECT * FROM ' . $tableName);
        $write = array();
        $fieldType = $this->_getDataType($result);//取得数据类型

        $pk = $this->_getTablePk($tableName);//取得表主键列表

        $batchPos = 0; $totalRealNum = 0;

        $mongoTable = $this->db_mongo->$tableName;


        $this->slog(sprintf("process start %d table: %s (%d rows)......", $stepPos, $tableName, $result->num_rows));

        $mongoTable->drop();//清除全部记录
        $this->slog('......clear table data ok.');

        $this->_tableIndex($mongoTable, $tableName);//处理索引

        if ($this->_options['OnlyStruct']) {//只导出表名
            $this->slog('......only table struct....ok.');
            return true;
        }

        $this->slog('......exporting......');
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {

            $_id = $this->_formatMongoPk($pk, $row); // 计算Mongo中的主键

            $row = $this->_convertDataType($fieldType, $row); //强制转换PHP中记录的数据类型
            if (!empty($_id)) {//有主键，则按自定义规则重新生成
                $row['_id'] = $_id;
            }

            $row = $this->_checkValue($tableName, $row);
            try {
                if ($this->_options['NoBatch']) {//单条插入
                    $mongoTable->insertOne($row);
                } else {//批量插入
                    $write[] = $row;
                    if ($batchPos >= 10000) {//以万记录为单位批量插入
                        $mongoTable->insertMany($write);
                        $write = array();
                        $batchPos = 0;
                    }
                }
            } catch (MongoCursorException $ex) {
                echo "\n\n", date('m/d H:i:s') . " Warning: insert error! at: " . (empty($write) ? json_encode($row) : json_encode($write));
                echo "\n", $ex->getMessage();
                if (!$this->_options['IgnoreError']) {
                    exit;
                }
            }

            $batchPos++; $totalRealNum++;
        }

        if (count($write) > 0) {//批量写入时，如果存在未写完数据，则全部写入
            $mongoTable->insertMany($write);
        }

        $this->slog(sprintf(" done. written rows nums : %d", $totalRealNum), false);

        return true;
    }

    /**
     * 处理表的索引
     * @param type $mongoTable
     * @param type $tableName
     * @return boolean
     */
    private function _tableIndex($mongoTable, $tableName) {

        if (!isset($this->_index[$tableName])) {
            return false;
        }

        foreach ($this->_index[$tableName] as $value) {

            if (isset($value[0]) && is_array($value[0])) {//带有参数的索引数组，例如唯一索引
                call_user_func_array(array($mongoTable, 'createIndex'), $value);
            } else {
                $mongoTable->createIndex($value);//普通索引
            }
        }
    }

    /**
     * 取得表的所有主键，并以数组返回
     * @param type $tableName
     * @return array  第一个元素为主键列表名数组，第二个元素为坐标轴名数组
     */
    private function _getTablePk($tableName) {

        $pk = array();
        $coods = array();

        $result = $this->db_mysql->query('desc ' . $tableName);
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            if ($row['Key'] == 'PRI') {

                $fieldName = strtolower($row['Field']);
                $fieldType = strtolower($row['Type']);
                if (($fieldName == 'x' || strpos($fieldName, '_x') !==false ) && $fieldType == 'bigint(20)') {//主键为X坐标
                    $coods['x'] = $fieldName;
                } elseif (($fieldName == 'y'|| strpos($fieldName, '_y') !== false) && $fieldType == 'bigint(20)') {//主键为Y坐标
                    $coods['y'] = $fieldName;
                } else {
                    $pk[] = $row['Field'];
                }
            }
        }

        return array($pk, $coods);
    }

    /**
     * 格式化
     * @param type $pk
     * @param type $row
     * @return string
     */
    private function _formatMongoPk($pk, $row) {

        $return = '';
        $pkeys = $pk[0];
        $cools = $pk[1];
        if (count($cools) > 0) {
            $mapcood = EtlRule::rule_coords_set($row, '%s,%s', $cools['x'], $cools['y']);
            $o1 = explode(',', $mapcood);
            $return = sprintf('%s,%s,%s', $mapcood, ($row[$cools['x']] - intval($o1[0])), ($row[$cools['y']] - intval($o1[1])));
        }

        if (count($pkeys) > 0) {

            $pvalue = array();
            foreach ($pkeys as $value) {
                if (isset($row[$value])) {
                    $pvalue[] = $row[$value];
                }
            }

            if (count($pvalue) > 0) {
                $return .= (empty($return) ? '' : ',') . implode(',', $pvalue);
            }
        }


        return $return;
    }

    /**
     * 强制转换数据类型
     * @param type $fieldType
     * @param type $row
     * @return type
     */
    private function _convertDataType($fieldType, $row) {

        foreach ($row as $fieldName => $value) {

            if ($fieldType[$fieldName] == 'int') {
                $row[$fieldName] = intval($value);
            } elseif ($fieldType[$fieldName] == 'float') {
                $row[$fieldName] = (float)$value;
            }
        }

        return $row;
    }

    /**
     * 取得转换的数据类型，仅支持数值和字符串
     * @param type $result
     * @return string
     */
    private function _getDataType($result) {

        $return = array();
        if(is_object($result)){
            $fieldInfo = $result->fetch_fields();
            foreach ($fieldInfo as $info) {

                if (in_array($info->type, array(1,2,3,8,9))) {//int类型
                    $return[$info->name] = 'int';
                } else if (in_array($info->type, array(4,5,246))) {//float类型
                    $return[$info->name] = 'float';
                } else {
                    $return[$info->name] = 'string';
                }
            }
        }
        return $return;
    }

    /**
     * 过滤表的字段值
     * @param type $tableName
     * @param type $row
     * @return type
     */
    private function _checkValue($tableName, $row) {

        if (!isset($this->_rule[$tableName])) {//无规则原样返回
            return $row;
        }

        $rules = $this->_rule[$tableName];
        foreach ($rules as $fieldName => $rule) {
            foreach ($rule as $ruleName => $value) {

                $pvalue = NULL;//回调方法的第一个参数值
                $params = array();//回调方法的所有参数
                if (substr($ruleName, 0, 1) == '*') {//如果指定规则名第一个字符为*表示将整个row值传为参数
                    $pvalue = $row;
                    $ruleName = ltrim($ruleName, '*');//去掉*字符
                } else {
                    $pvalue = $row[$fieldName];
                }

                $func = 'rule_' . $ruleName;//过滤的方法名称，在类EtlRule定义

                if (is_array($value)) {//过滤的方法参数，第一个永远为字段的内容值，其后才是定义的规则参数
                    array_unshift($value, $pvalue);
                    $params = $value;
                } else {
                    $params = array($pvalue, $value);
                }

                $ret = call_user_func_array(array('EtlRule', $func), $params);//调用过滤方法
                if ($ret !== NULL) {//返回空表示不处理
                    $row[$fieldName] = $ret;
                }
            }
        }

        return $row;
    }


    /**
     * 将mysql记录导出为json文件，然后将此json文件导入到mongodb
     */
    public function exportToFile() {
    }

    /**
     * 记录转换日志
     * @param string $message
     */
    private function slog($msg, $isNewLine = true) {

        $message = '';
        if ($isNewLine) {
            $message = "\n" . date("m-d H:i:s") . '  -> ';
        }

        $message .=  $msg;
        echo $message;

        //file_put_contents('./logs', $message, FILE_APPEND);//暂时直接输出到终端，不写文件
    }

    /**
     * 写校验结果到日志文件
     * @param type $tableName
     * @param type $cp_result
     */
    private function slogFile($tableName, $cp_result) {

        $totalFile = sprintf($this->_logPath . '/total_%s.log', date('Ymd'));
        $detailFile = sprintf($this->_logPath . '/detail_%s_%s.log', $tableName, date('Ymd'));

        $totalInfo = $cp_result[0];
        $detailInfo = $cp_result[1];


        //总数统计
        $totalMysql = str_pad($totalInfo['mysql'], 90);
        $totalMongo = $totalInfo['mongo'];
        $totalLine = $totalMysql . str_pad($totalInfo['msg'], 30) . $totalMongo . "\n";
        file_put_contents($totalFile, $totalLine, FILE_APPEND);


        //详情统计
        if (count($detailInfo) > 0) {//数据有不同，则写入详情文件
            $detailLine = sprintf("%s %s(different: %d rows)  Mysql Table Result   <============msg=============>   Mongodb Table Result\n"
                , date('m-d H:i '), $tableName, count($detailInfo));
            $darr = array();
            foreach ($detailInfo as $row) {
                $darr[] = sprintf("%s     <============(%s)=============>     %s\n", $row['mysql'] , $row['msg'], $row['mongo']);
            }

            $detailLine .= implode('', array_unique($darr)) . "\n\n\n";
            file_put_contents($detailFile, $detailLine, FILE_APPEND);
        }

    }

    /**
     * 初始化文件
     * @param type $tableName
     */
    private function slogInitFile() {

        $totalFile = sprintf($this->_logPath . '/total_%s.log', date('Ymd'));

        if (!is_dir($this->_logPath)) {//创建日志目录
            mkdir($this->_logPath);
        }

        $totalMysql = str_pad('Mysql Table', 90);
        $totalMongo = 'Mongodb Table';
        $totalLine = $totalMysql . str_pad("status", 30) . $totalMongo . "\n";
        file_put_contents($totalFile, $totalLine);

    }

    /**
     * 验证从mysql导出到mongodb的数据是否正确
     * @param array $tables  要检测的表名数据列表，如果为空，表示检测整个数据库全部表
     */
    public function checkData() {

        //概览：mysql表名(记录数，字段数)  |  mongodb表名(记录数，字段数，多余字段名[_id除外]，数据差异数)
        //详情（仅显示差异）：  mysql表名  |  mongodb表名
        //                    记录数据  |数据未导入或被修改|  相似数据

        //步骤：
        //1. 从数据库mysql获取全部表
        //2. 逐条从mysql表中获取比对数据
        //3. 根据比对数据表中的主键从被比对的mongodb数据表查询记录，如果没有主键，则将所有的字段值作为查询凭证
        //4. 比对表总数
        //5. 比对记录总数
        //6. 比对字段总数
        //7. 比对字段值

        $this->slogInitFile();


        $tableCount = count($this->_options['Tables']);
        $isAll = $tableCount == 0;//是否导出mysql的全部表到mongodb

        $result = $this->db_mysql->query("SHOW TABLES");
        $this->slog(sprintf("mysql total (%d) tables, (%d) mongodb table will be verify. ", $result->num_rows, ($tableCount > 0 ? $tableCount : $result->num_rows)));

        $stepPos = 1;

        $cp_result = NULL;
        while($row = $result->fetch_array()) {

            if ($isAll || in_array($row[0], $this->_options['Tables'])) {
                $cp_result = $this->_checkData($row[0], $stepPos);
                $this->slogFile($row[0], $cp_result);
                $stepPos++;
            }
        }
        $this->slog(sprintf("[all table(%d) has checked.]\n\n", ($tableCount > 0 ? $tableCount : $result->num_rows)));
    }

    /**
     * 校验操作
     * @param type $tableName
     * @param type $stepPos
     * @return type
     */
    private function _checkData($tableName, $stepPos) {

        $tableRemark = array(
            'mysql' => '',
            'mongo' => ''
        );  //表的概览
        $tableDetail = array(
        );  //表的校验详情


        $result = $this->db_mysql->query('SELECT * FROM ' . $tableName);

        $pk = $this->_getTablePk($tableName);//取得表主键列表
        $fieldType = $this->_getDataType($result);//取得数据类型
        $qfields = $this->_checkData_WhereField($tableName);//取得表查询字段

        $mongoTable = $this->db_mongo->$tableName;

        $tableRemark['mysql'] = sprintf('%s(%d rows, %d fields, %s)', $tableName, $result->num_rows, $qfields[2], ($qfields[1] ? 'PK: ' . implode(',', $qfields[0]) : '!!!NO PK'));


        $this->slog(sprintf("process start %d table: %s (%d rows)......", $stepPos, $tableName, $result->num_rows));
        $st_mongoFields = (array)$mongoTable->findOne();//取得第一行数据用于判断字段数和差异字段
        $st_mongoFieldNum = count($st_mongoFields) - 1;//不统计_id字段
        $st_fd = $this->_checkData_AllField($result);


        $st_mongoFieldCp = $this->_checkData_CompareField($st_fd, $st_mongoFields);//比较mysql和mongo表的字段是否相等
        $st_mongoCount = $mongoTable->count();

        if ($st_mongoCount == 0) {//mongodb表没有数据，取消比较
            $tableRemark['msg'] = sprintf("<%s>(mongo collection is empty)", $st_mongoCount == $result->num_rows ? '==' : '!=');
            $tableRemark['mongo'] = $tableName . '(no data)';
            $this->slog('......fail: mongo collection ' . $tableName . ' no data.');
            return array(
                $tableRemark,
                $tableDetail
            );
        }

        if ($st_mongoCount != $result->num_rows) {//mysql和mongodb表的记录数不一致，取消比较
            $tableRemark['msg'] = '<!=>(rows count different)';
            $tableRemark['mongo'] = $tableName . '( ' . $st_mongoCount . ' rows )';
            $this->slog(sprintf('......fail: mongo collection %s rows not equals(mysql:%s, mongo:%s)', $tableName, $result->num_rows, $st_mongoCount));
            return array(
                $tableRemark,
                $tableDetail
            );
        }

        $logId = 0;
        $this->slog(".......checking......");
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {

            $this->db_mysql->ping();

            $lineComp = array();
            $wf = array();//条件字段

            $_id = $this->_formatMongoPk($pk, $row);
            $rowdata = $this->_convertDataType($fieldType, $row);//强制转换PHP中记录的数据类型
            if (!empty($_id)) {//按_id键高效率查找
                $rowdata['_id'] = $_id;
                $wf[] = '_id';
            } else {
                $wf = $qfields[0];//按普通字段查找,效率低
            }

            $rowdata = $this->_checkValue($tableName, $rowdata);

            $where = $this->_checkData_Where($wf, $rowdata);//取得查询的条件
            $count = $mongoTable->count($where);//统计表的记录数

            if ($count == 0) {
                $lineComp['mysql'] = json_encode($where);
                $lineComp['msg'] = 'not found data row in mongodb.';
                $lineComp['mongo'] = '';
            } elseif ($count > 1) {
                $lineComp['mysql'] = json_encode($where);
                $lineComp['msg'] = 'mongodb found data row too many.';
                $lineComp['mongo'] = $count . ' rows';
            } else {
                $mongo_row = $mongoTable->findOne($where);
                unset($rowdata['_id']);//如果有_id键，则清除
                $lineComp = $this->_checkData_Compare($rowdata, $mongo_row);//执行比较
                if (empty($lineComp['msg'])) {//没有错误消息表示校验正确，则不用记录比较结果
                    continue;
                } else {
                    $lineComp['mongo'] .= '(query=' . (json_encode($where)) . ')';
                }
            }

            $logId++;
            $tableDetail[] = $lineComp;
        }

        $tableRemark['mongo'] = sprintf('%s(%d rows, %d fields, %s, %d different)',
            $tableName, $st_mongoCount, $st_mongoFieldNum, implode('|', $st_mongoFieldCp), count($tableDetail));
        if (count($tableDetail) > 0 || count($st_mongoFieldCp) > 0) {//是否有差异
            $tableRemark['msg'] = sprintf("<!=>(data value different)");//有差异
        } else {
            $tableRemark['msg'] = '<==>';//正常
        }

        $this->slog(sprintf(" checked. status: %s, different num: %d", $tableRemark['msg'], count($tableDetail)), false);

        return array($tableRemark, $tableDetail);
    }

    /**
     * 检测字段
     * @param type $mysql_row
     * @param type $mongo_row
     * @return string
     */
    private function _checkData_CompareField($mysql_row, $mongo_row) {

        $return = array();

        unset($mongo_row['_id']);

        $mysql_keys = array_keys($mysql_row);
        $mongo_keys = array_keys($mongo_row);

        $sub = array_diff($mysql_keys, $mongo_keys);
        $plus = array_diff($mongo_keys, $mysql_keys);

        foreach ($sub as $value) {
            $return[] = '-' . $value;
        }
        foreach ($plus as $value) {
            $return[] = '+' . $value;
        }


        return $return;
    }

    /**
     * 取得表全部字段名称
     * @param type $result
     * @return boolean
     */
    private function _checkData_AllField($result) {

        $return = array();
        if(is_object($result)){
            $fieldInfo = $result->fetch_fields();
            foreach ($fieldInfo as $info) {
                $return[$info->name] = true;
            }
        }

        return $return;
    }

    /**
     * 取得用于校验数据查询的字段
     * @param type $tableName
     * @return array  第一个元素为查询的字段名列表， 第二个元素为boolean表示是否为pk查询,pk时为true， 第三个元素为表的真实字段数
     */
    private function _checkData_WhereField($tableName) {

        $pk = array();//主键列表
        $all = array();//所有字段列表，当没有主键时

        $result = $this->db_mysql->query('desc ' . $tableName);
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            if ($row['Key'] == 'PRI') {
                $pk[] = $row['Field'];
            }

            $all[] = $row['Field'];
        }

        if (count($pk) > 0) {
            return array($pk, true, count($all));
        } else {
            return array($all, false, count($all));
        }
    }

    /**
     * 生成mongo查询的条件
     * @param type $boolFields
     * @param type $fields
     * @param type $row
     * @return type
     */
    private function _checkData_Where($fields, $row) {

        $return = array();

        foreach ($fields as $value) {

            if (!isset($row[$value])) {
                echo 'verify fail! data field exception: ' . json_encode($row) . ' ------ ' . json_encode($fields);
                exit;
            }

            $return[$value] = $row[$value];
        }

        return $return;
    }


    /**
     * 执行行比较
     * @param type $mysql_row
     * @param type $mongo_row
     * @return type
     */
    private function _checkData_Compare($mysql_row, $mongo_row) {

        $mysql = array();
        $mongo = array();

        if (count($mysql_row) != (count($mongo_row) - 1)) {//不计算_id字段
            return array(
                'mysql' => implode(',', array_keys($mysql_row)),
                'mongo' => implode(',', array_keys($mongo_row)),
                'msg'   => 'field num not equals.',
            );
        }

        foreach ($mysql_row as $f => $value) {

            if ($value instanceof MongoInt64) {
                $value = (int)$value->__toString();
            }

            $cvalue = $value;
            if (is_bool($mongo_row[$f])) {//布尔值比较
                $cvalue = $value == 1;
            }
            if ($value != $mongo_row[$f]) {
                $mysql[] = $f . '=' . (is_bool($value) ? ($value ? 'true' : 'false') : $value);
                $mongo[] = $f . '=' . (is_bool($mongo_row[$f]) ? ($mongo_row[$f] ? 'true' : 'false') : $mongo_row[$f]);
            }
        }

        $msg = '';
        if (!empty($mysql) && !empty($mongo)) {
            $msg = 'value not equal';
        }

        return array(
            'mysql' => implode(",", $mysql),
            'mongo' => implode(',', $mongo),
            'msg' => $msg);
    }

    /**
    clear all table invalid data
     */
    public function clearIdleData() {

        $this->db_mysql->query("DELETE a FROM Center_MapObject as a LEFT JOIN Center_Account as b ON a.owner_uuid = b.account_uuid WHERE b.account_uuid IS NULL AND a.owner_uuid <> '00000000-0000-0000-0000-000000000000'");
        $this->db_mysql->query("DELETE a FROM Center_CastleOfflineUpgradeBuildingBase as a LEFT JOIN Center_Account as b ON a.account_uuid = b.account_uuid WHERE b.account_uuid IS NULL AND a.account_uuid <> '00000000-0000-0000-0000-000000000000'");

    }

}

/**
 * 转换规则
 */
class EtlRule {

    /**
     * 转换规则： 将数据转换为boolean类型
     * @param type $value  字段值
     * @param type $true   为真时的值
     * @return type
     */
    public static function rule_dt_bool($value, $true) {

        if ($value instanceof MongoInt64) {
            $value = (int)$value->__toString();
        }


        return $value == $true;
    }

    /**
     * 将mysql日期转换为mongo日期对象
     * @param type $value
     * @return \MongoDate
     */
    public static function rule_dt_datetime($value) {

        $time = strtotime($value);
        if ($time === false) {
            return NULL;
        }

        return new MongoDate($time);
    }

    /**
     * 按指定的格式生成新的PK键
     * @param array $row 当前数据行数组
     * @param string $format 格式化字符串，支持printf的格式化语法
     * @return int
     */
    public static function rule_pk_set() {

        $args = func_get_args();
        if(count($args) < 3) {
            return NULL;
        }

        $row = array_shift($args);
        $format = array_shift($args);

        $pvalue = array();
        foreach ($args as $field) {
            if (isset($row[$field])) {
                $pvalue[] = $row[$field];
            }
        }

        if (empty($format)) {
            if (count($pvalue) == 0) {
                return NULL;
            }

            return implode('', $pvalue);
        } else {
            array_unshift($pvalue, $format);
            return call_user_func_array('sprintf', $pvalue);//格式化并返回新PK值
        }
    }

    /**
     * 生成坐标轴的键值
     * @param array $row 当前数据行数组
     * @param string $format 格式化字符串，支持printf的格式化语法
     * @return int
     */
    public static function rule_coords_set() {

        $args = func_get_args();
        if(count($args) != 4) {
            return NULL;
        }

        list($row, $format, $x, $y) = $args;
        if ($row[$x] instanceof MongoInt64) {
            $row[$x] = $row[$x]->__toString();
        }
        if ($row[$y] instanceof MongoInt64) {
            $row[$y] = $row[$y]->__toString();
        }


        $map_y = floor((float)$row[$y] / 640 + 1e-6) * 640;//范围y坐标
        $map_x = floor((float)$row[$x] / 600 + 1e-6) * 600;//范围x坐标

        return sprintf($format, $map_x, $map_y);//格式化并返回新字段值
    }

    /**
     * 生成坐标轴的键值2
     * @param array $row 当前数据行数组
     * @param string $format 格式化字符串，支持printf的格式化语法
     * @return int
     */
    public static function rule_coords_set2() {

        $args = func_get_args();
        if(count($args) != 4) {
            return NULL;
        }

        list($row, $format, $x, $y) = $args;
        if ($row[$x] instanceof MongoInt64) {
            $row[$x] = $row[$x]->__toString();
        }
        if ($row[$y] instanceof MongoInt64) {
            $row[$y] = $row[$y]->__toString();
        }


        $map_y = floor((float)$row[$y] / 32 + 1e-6) * 32;  //范围y坐标
        $map_x = floor((float)$row[$x] / 30 + 1e-6) * 30;  //范围x坐标

        return sprintf($format, $map_x, $map_y, ($row[$x] - $map_x), ($row[$y] - $map_y));//格式化并返回新字段值
    }
}
