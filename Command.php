<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 一  8/31 15:35:35 2015
 *
 * @File Name: Command.php
 * @Description:
 * *****************************************************************/
class Command {

    public static function log($msg) {
        file_put_contents('/Users/wushuiyong/workspace/git/docx/app.log', var_export($msg, true) . PHP_EOL, 8);
    }

    public static function execute($command) {
        self::log('---------------------------------');
        self::log('---- Executing: $ ' . $command);

        $return = 1;
        $log = [];
        exec($command . ' 2>&1', $log, $return);
        $log = implode(PHP_EOL, $log);

        self::log($log);
        self::log('---------------------------------');

        return !$return;
    }

    public static function gitPush() {

    }


}
