<?php
/**
 * 更新任务
 */
date_default_timezone_set('PRC');


//修改下面的常量来指定数据库信息
define('MYHOST', '192.168.1.206');//mysql主机名称
define('MYPORT', '3306');//mysql端口
define('MYDB', 'empery');//mysql数据库名称
define('MYUSER', 'root');//mysql用户名称
define('MYPWD', '');//mysql密码

boot();

function boot() {
    
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
    
    array_shift($pargv);
    array_shift($pargv);

    call_user_func_array($cmd, array($pargv));
}

//更新新任务
function cmdNewtask($pargv) {
    
    if (!class_exists('mysqli')) {
         echo "mysqli not found";
         exit;
    }
    
    foreach ($pargv as $value) {
        if ($value == '--all') {//导出前清表
            process_newtask();
        } elseif (stripos($value, '--user=') === 0) {//仅导出指定的表
            $tables = substr($value, 7);
            process_newtask(explode(',', $tables));
        }
    }
}

//处理任务
function process_newtask($user = NULL) {
    
    $db_mysql = new mysqli(MYHOST, MYUSER, MYPWD, MYDB, MYPORT);
    if ($db_mysql->connect_errno) {
        echo "mysql连接失败!" . $db_mysql->connect_error;
        exit;
    }
    
    
    if (is_array($user)) {
        
        $user_where = "'" . implode("','", $user) . "'";
        $result = $db_mysql->query("SELECT account_uuid,login_name FROM `empery`.`Center_Account` WHERE login_name IN ({$user_where})");
    } else {
        $result = $db_mysql->query("SELECT account_uuid,login_name FROM `empery`.`Center_Account`");
    }
    
    echo sprintf("\n[%s] total %s account will be update.", date("Y/m/d H:i:s"), $result->num_rows);
    
    $incpos = 1;
    while($row = $result->fetch_array()) {
        
        $db_mysql->query("DELETE FROM Center_Task WHERE account_uuid = '{$row[0]}'");
        $db_mysql->query("REPLACE INTO Center_AccountAttribute (account_uuid, account_attribute_id, VALUE) VALUES ('{$row[0]}', 181, '1')");
        $db_mysql->query("REPLACE INTO `empery`.`Center_Task` (`account_uuid`, `task_id`, `category`, `created_time`, `expiry_time`, `progress`, `rewarded`) VALUES ('{$row[0]}', 0, 0, '2016-12-13 06:58:43', '0000-00-00 00:00:00', '', 0)");
        $db_mysql->query("REPLACE INTO `empery`.`Center_Task` (`account_uuid`, `task_id`, `category`, `created_time`, `expiry_time`, `progress`, `rewarded`) VALUES ('{$row[0]}', 3010003, 1, '2016-12-13 06:58:43', '9999-00-00 00:00:00', '', 0)");
        
        
        echo sprintf("\n[%s] for %d user %s has updated.", date("Y/m/d H:i:s"), $incpos, $row[1]);
        $incpos++;
    }
    
    echo sprintf("\n\n[%s] all account has updated.\n", date("Y/m/d H:i:s"));
}



/**
 * 帮助说明
 */
function cmdHelp() {

    echo <<<EOF
  命令行调用说明
  1.更新任务
  支持参数：
    --all 更新所有用户
    --user=name1,name2,name3...  用户名称，多个间用逗号分隔

  示例：
    php updatetask.php newtask --all
    php updatetask.php newtask --user=name1,name2,name3

EOF;
    echo "\n\n";
    exit;
}



