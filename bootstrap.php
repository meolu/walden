<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 一  8/31 12:39:22 2015
 *
 * @File Name: bootstrap.php
 * @Description:
 * *****************************************************************/
require_once 'Command.php';
require_once 'Parsedown.php';

class Bootstrap {

    
    /**
     * html文档类型
     */
    const TYPE_HTML = '.html';
    
    /**
     * markdown文档类型
     */
    const TYPE_MD = '.md';

    public static function getSafeFile($file) {
        $origin = $file;
        $file = ltrim($file, '/');
        $file = ltrim($file, '.');
        while ($file != $origin) {
            $origin = $file;
            $file = static::getSafeFile($file);
        }
        return $file;
    }

    public static function md2HtmlFile($file) {
        if (!static::isMarkDownFile($file)) return false;
        return substr($file, 0, strlen($file) - strlen(static::TYPE_MD)) . static::TYPE_HTML;
    }

    public static function html2MdFile($file) {
        if (!static::isHtmlFile($file)) return false;
        return substr($file, 0, strlen($file) - strlen(static::TYPE_HTML)) . static::TYPE_MD;
    }

    /**
     * 判断是否为html文件
     *
     * @author wushuiyong
     * @param file string
     * @return bool
     */
    public static function isHtmlFile($file) {
        return substr($file, 0 - strlen(static::TYPE_HTML)) === static::TYPE_HTML;
    }

    /**
     * 判断是否为markdown文件
     *
     * @author wushuiyong
     * @param file string
     * @return bool
     */
    public static function isMarkDownFile($file) {
        return substr($file, 0 - strlen(static::TYPE_MD)) === static::TYPE_MD;
    }

    public function redirect($url) {
        header("Location: $url", TRUE, 302);
    }

    public function render($view, $params) {
        $tpl = sprintf("%s.php", $view);
        if (!file_exists($tpl)) throw new Exception('找不到模板:' . $view);
        ob_start();
        extract($params);
        include($tpl);
        ob_flush();
    }

}

// /docx/usage/start.md

function dd($var) {
    var_dump($var);die;
}function d($var) {
    var_dump($var);
}

function route($route) {
    $bootstrap = new Bootstrap();
    $config = include('config.php');
    $route = Bootstrap::getSafeFile($route);
    $file = rtrim($config['markdown_root'], '/') . '/' . $route;
    // html渲染
    if ($file && Bootstrap::isHtmlFile($file)) {
        $mdFile = Bootstrap::html2MdFile($file);
        if ($mdFile && file_exists($mdFile) && is_file($mdFile)) {
            $Parsedown = new Parsedown();
            $content = file_get_contents($mdFile);
            $dom =  $Parsedown->text($content);
            $bootstrap->render('templates/bootstrap', [
                'content' => $dom,
            ]);
        }

    }
    // 编辑md文件 /docx/usage/start.md
    elseif ($file && file_exists($file) && is_file($file) && Bootstrap::isMarkDownFile($file)) {
        $content = file_get_contents($file); 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $content = $_POST['content'];
            $ret = file_put_contents($file, $content);
            $htmlFile = Bootstrap::md2HtmlFile($route);
            $bootstrap->redirect('/' . $htmlFile);
        }
        $bootstrap->render('markdown-editor-view', [
            'content' => $content,
        ]);

    }
    // md文件不存在则先创建。/docx/usage/start.md
    elseif ($file && !file_exists($file) && Bootstrap::isMarkDownFile($file)) {
        $dir = dirname($file);
        $cmd = sprintf("mkdir -p %s", $dir);
        $ret = Command::execute($cmd);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $content = $_POST['content'];
            $ret = file_put_contents($file, $content);
            $bootstrap->redirect('/s.html');
        }
        $bootstrap->render('markdown-editor-view', [
            'content' => 'file-content',
        ]);
    }
    // 目录已存在，列出文件。/docx/usage
    elseif ($file && file_exists($file) && is_dir($file)) {
        $dir = DirectoryIndex::listDirectory($file);
    }

}

$route = $_SERVER['REQUEST_URI'];
route($route);
#$file = '../../../abc.md.d';
#$ret = Bootstrap::isMarkDownFile($file);
#var_dump($ret);



