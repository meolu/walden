<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 一  8/31 12:39:22 2015
 *
 * @File Name: index.php
 * @Description:
 * *****************************************************************/

require_once 'Bootstrap.php';

function dd($var) {
    var_dump($var);die;
}
function d($var) {
    var_dump($var);
}

$config = include('Config.php');
$bootstrap = new Bootstrap();
$bootstrap->setConfig($config)->run();
