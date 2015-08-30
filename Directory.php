<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 日  8/30 23:30:24 2015
 *
 * @File Name: Directory.php
 * @Description:
 * *****************************************************************/

class Directory {

    public static function listDirectory($dir = 'docx') {

        // 如果是文件
        if (is_file($dir)) return $dir;

        // 递归目录
        $dirHandler = opendir($dir);
        if (!$dirHandler) return false;

        while (($file = readdir($dirHandler)) {
            echo $file, PHP_EOL;
        }

    }

}
