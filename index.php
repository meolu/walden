<?php
/* *****************************************************************
 * @Author: wushuiyong@huamanshu.com
 * @Created Time : 一  8/31 12:39:22 2015
 *
 * @File Name: index.php
 * @Description:
 * *****************************************************************/

// 设置UTF8，支持中文
setlocale(LC_ALL, 'en_US.UTF8');
// 设置时区
ini_set('date.timezone','Asia/Shanghai');
// error report off
ini_set('display_errors', 0);
// 根目录
define('WEB_ROOT', __DIR__);

$config = include('Config.php');
require_once 'Bootstrap.php';

$bootstrap = new Bootstrap();
// 设置错误捕获
set_error_handler(array($bootstrap, 'errorHandler'));
// 设置异常捕获
set_exception_handler(array($bootstrap, 'exceptionHandler'));

$bootstrap->setConfig($config)->run();
