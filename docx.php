<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 日  8/30 22:54:11 2015
 *
 * @File Name: docx.php
 * @Description:
 * *****************************************************************/
include "Parsedown.php";

$file = 'Changelog.md';
$content = file_get_contents($file);

$Parsedown = new Parsedown();
$out = $Parsedown->text($content);

$ret = file_put_contents('new.html', $out);
var_dump($ret);
