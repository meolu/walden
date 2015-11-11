<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 三 10/ 7 19:43:39 2015
 *
 * @File Name: Command.php
 * @Description:
 * *****************************************************************/
class Command {

    private $_log;

    public static function log($msg) {
        // file_put_contents('/tmp/cmd', var_export($msg, true) . PHP_EOL, 8);
    }

    public function getExeLog() {
        return $this->_log;
    }

    public function execute($command) {
        self::log('---------------------------------');
        self::log('---- Executing: $ ' . $command);

        $return = 1;
        $log = '';
        exec($command . ' 2>&1', $log, $return);
        $this->_log = implode(PHP_EOL, $log) ?: array();
        self::log($this->_log);
        self::log('---------------------------------');

        return !$return;
    }

    /**
     * 推送更新
     *
     * @return bool
     */
    public function gitPush() {
        $markdownDir = sprintf("%s/markdown", WEB_ROOT);
        // 存在git目录，直接push
        if (!file_exists($markdownDir) || !file_exists($markdownDir . '/.git')) return false;

        if (!file_exists($markdownDir) || !file_exists($markdownDir . '/.git')) {
            throw new \Exception('初始化git目录失败:' . $this->getExeLog());
        }

        $cmd[] = sprintf('cd %s ', $markdownDir);
        $cmd[] = sprintf('/usr/bin/env git add .');
        $cmd[] = sprintf('/usr/bin/env git commit -m"%s"', date("Y-m-d H:i:s", time()));
        $cmd[] = sprintf('/usr/bin/env git push origin master');
        $command = join(' && ', $cmd);
        return $this->execute($command);
    }

    /**
     * 初始化git项目
     *
     * @param $gitRepo
     * @return bool
     */
    public function initGit($gitRepo) {
        $markdownDir = sprintf("%s/markdown", WEB_ROOT);
        if (file_exists($markdownDir) && file_exists(rtrim($markdownDir, '/') . '/.git')) return true;

        if (file_exists($markdownDir)) {
            $cmd[] = sprintf('cd %s', $markdownDir);
            $cmd[] = sprintf('/usr/bin/env git init');
            $cmd[] = sprintf('mkdir -p %s/upload', $markdownDir);
            $cmd[] = sprintf('/usr/bin/env git remote add origin %s', $gitRepo);
        } else {
            $cmd[] = sprintf('mkdir %s', $markdownDir);
            $cmd[] = sprintf('cd %s', $markdownDir);
            $cmd[] = sprintf('/usr/bin/env git clone %s .', $gitRepo);
            $cmd[] = sprintf('mkdir -p %s/upload', $markdownDir);
        }

        $command = join(' && ', $cmd);
        return $this->execute($command);
    }

    /**
     * 清除初始化时的目录
     *
     * @return bool
     */
    public function cleanInitDir() {
        $markdownDir = sprintf("%s/markdown", WEB_ROOT);
        if (!file_exists($markdownDir)) return true;

        $command = sprintf('rm -rf %s', $markdownDir);
        return $this->execute($command);
    }



}
