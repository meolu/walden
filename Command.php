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
//        file_put_contents('/Users/wushuiyong/workspace/git/walden/app.log', var_export($msg, true) . PHP_EOL, 8);
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

    public static function gitPush($markdown) {
        // 存在git目录，直接push
        if (!file_exists($markdown)) return false;
        $cmd[] = sprintf('cd %s ', $markdown);
        $cmd[] = sprintf('/usr/bin/env git add .');
        $cmd[] = sprintf('/usr/bin/env git commit -m"%s"', date("Y-m-d H:i:s", time()));
        $cmd[] = sprintf('/usr/bin/env git push origin master');
        $command = join(' && ', $cmd);
        $log = '';
        return static::execute($command, $log);
    }

    public function initGit($gitRepo, $webroot, $markdown) {
        $gitDir = sprintf("%s/%s", rtrim($webroot, '/'), $markdown);
        if (file_exists($gitDir) && file_exists(rtrim($gitDir, '/') . '/.git')) return true;

        $cmd[] = sprintf('cd %s ', $webroot);
        $cmd[] = sprintf('/usr/bin/env git clone %s %s', $gitRepo, $markdown);
        $command = join(' && ', $cmd);
        $log = '';
        return static::execute($command, $log);
    }


}
