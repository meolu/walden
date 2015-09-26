<?php
/* *****************************************************************
 * @Author: wushuiyong@huamanshu.com
 * @Created Time : 一  8/31 15:35:35 2015
 *
 * @File Name: Command.php
 * @Description:
 * *****************************************************************/
class Command {

    private $_log;

    public static function log($msg) {
        file_put_contents('/tmp/cmd.log', var_export($msg, true) . PHP_EOL, 8);
    }

    public function getExeLog() {
        return $this->_log;
    }

    public function execute($command) {
        self::log('---------------------------------');
        self::log('---- Executing: $ ' . $command);

        $return = 1;
        $log = [];
        exec($command . ' 2>&1', $log, $return);
        $this->_log = implode(PHP_EOL, $log);
        self::log($this->_log);
        self::log('---------------------------------');

        return !$return;
    }

    public function gitPush($markdown) {
        // 存在git目录，直接push
        if (!file_exists($markdown)) return false;
        $cmd[] = sprintf('cd %s ', $markdown);
        $cmd[] = sprintf('/usr/bin/env git add .');
        $cmd[] = sprintf('/usr/bin/env git commit -m"%s"', date("Y-m-d H:i:s", time()));
        $cmd[] = sprintf('/usr/bin/env git push origin master');
        $command = join(' && ', $cmd);
        return $this->execute($command);
    }

    public function initGit($gitRepo, $webroot, $markdown) {
        $gitDir = sprintf("%s/%s", rtrim($webroot, '/'), $markdown);
        if (file_exists($gitDir) && file_exists(rtrim($gitDir, '/') . '/.git')) return true;

        $cmd[] = sprintf('cd %s ', $webroot);
        $cmd[] = sprintf('/usr/bin/env git clone %s %s', $gitRepo, $markdown);
        $cmd[] = sprintf('mkdir -p %s/upload', $markdown);
        $command = join(' && ', $cmd);
        return $this->execute($command);
    }


}
