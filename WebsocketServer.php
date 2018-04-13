<?php
/**
 * Created by PhpStorm.
 * User: ShaunXu
 * Date: 2018/4/12
 * Time: 11:33
 */


error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__FILE__));
define('CONF_PATH', ROOT_PATH . DS . 'conf');
define('APP_PATH', ROOT_PATH . DS . 'application');
define('LIB_PATH', APP_PATH . DS . 'library');

require LIB_PATH . DS . 'WebsocketServer.php';

$serverObj = WebsocketServer::getInstance();
$serverObj->setServerConfigIni(CONF_PATH . DS . 'server.ini');
$serverObj->setAppConfigIni(CONF_PATH . DS . 'application.ini');
$serverObj->start();