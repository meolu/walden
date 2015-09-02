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
require_once 'DirectoryIndex.php';

class Bootstrap {

    /**
     * markdown 文档根目录
     */
    const MARKDOWN_ROOT = 'markdown';

    /**
     * 模板templates目录
     */
    const TEMPLATE_ROOT = 'templates';

    /**
     * 附件上传目录
     */
    const UPLOAD_ROOT   = 'markdown/upload';

    /**
     * 预览文档目录时，展示列表模板
     */
    const VIEW_LIST = 'markdown-list-view';

    /**
     * 预览文档模板
     */
    const VIEW_DETAIL = 'markdown-detail-view';

    /**
     * 编辑文档模板
     */
    const VIEW_EDITOR = 'markdown-editor-view';

    /**
     * html文档类型
     */
    const TYPE_HTML = '.html';
    
    /**
     * markdown文档类型
     */
    const TYPE_MD = '.md';

    /**
     * 上传附件接口
     */
    const UPLOAD_URL = '/attachment';

    private $_config;

    public function __construct() {

    }

    /**
     * 配置设置 config.php
     * 
     * @param $config array
     */
    public function setConfig($config) {
        $this->_config = $config;
        return $this;
    }

    /**
     *
     * @throws Exception
     */
    public function run() {
        $route = static::getSafeFile(urldecode($_SERVER['REQUEST_URI']));
        $file  = static::MARKDOWN_ROOT . '/' . $route;
        // html渲染
        if ($file && static::isHtmlFile($file)) {
            $this->actionReadHtml($file);
        }
        // 编辑md文件(md文件不存在则先创建) /docx/usage/start.md
        elseif ($file && static::isMarkDownFile($file)) {
            $this->actionEditMarkdown($file, $route);
        }
        // 目录已存在，列出文件。/docx/usage
        elseif ($file && file_exists($file) && is_dir($file)) {
            $this->actionListDir($file);
        }
        // 上传附件
        elseif ($route === static::getSafeFile(self::UPLOAD_URL)) {
            $this->actionUploadAttached();
        }

    }

    /**
     * 页面重定向
     *
     * @param $url
     */
    public function redirect($url, $status = 302) {
        header("Location: $url", TRUE, $status);
    }

    /**
     * 页面渲染
     *
     * @param $view
     * @param $params
     * @throws Exception
     */
    public function render($view, $params) {
        $tpl = sprintf("%s/%s/%s.php", static::TEMPLATE_ROOT, $this->_config['template'], $view);
        if (!file_exists($tpl)) throw new Exception('找不到模板:' . $tpl);
        ob_start();
        extract($params);
        include($tpl);
        ob_flush();
    }

    /**
     * 输出json
     *
     * @param $data
     * @param int $code
     * @param string $msg
     */
    public function renderJson($data, $code = 0, $msg = '') {
        header('content-type:application/json; charset=utf8');
        $ret = [
            'code' => (int) $code,
            'msg' => $msg,
            'data' => $data
        ];
        echo json_encode($ret, 0);
        die;
    }

    /**
     * 文档路径过滤
     *
     * @param $file
     * @return string
     */
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

    /**
     * markdown文件路径转化为html文件路径 
     * 
     * @example /docx/readme.md => /docx/read.html
     * @param $file
     * @return bool|string
     */
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



    public function exceptionHandle() {

    }

    /**
     * html渲染
     * @param $file
     * @throws Exception
     */
    public function actionReadHtml($file) {
        $mdFile = static::html2MdFile($file);
        if ($mdFile && file_exists($mdFile) && is_file($mdFile)) {
            // 目录索引
            $index = DirectoryIndex::listDirectory(static::MARKDOWN_ROOT . '/docx', DirectoryIndex::MODE_READ);
            // 文档预览
            $content = file_get_contents($mdFile);
            $parser = new Parsedown();
            $dom =  $parser->text($content);
            $this->render(static::VIEW_DETAIL, [
                'index'   => $index,
                'editUrl' => DirectoryIndex::file2Url($mdFile, DirectoryIndex::MODE_WRITE),
                'content' => $dom,
            ]);
        }
    }

    /**
     * 编辑md文件 /docx/usage/start.md
     * @param $file
     * @param $route
     * @throws Exception
     */
    public function actionEditMarkdown($file, $route) {
        $title = DirectoryIndex::trimFileExtension(basename($file));
        if (!is_file($file)) {
            $cmd[]   = sprintf("mkdir -p %s", dirname($file));
            $cmd[]   = sprintf("echo '# %s%s' > %s", $title, PHP_EOL, $file);
            $command = join(" && ", $cmd);
            $ret = Command::execute($command);
        }
        $content = file_get_contents($file);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $content = $_POST['content'];
            $ret = file_put_contents($file, $content);
            $htmlFile = static::md2HtmlFile($route);
            $this->redirect('/' . $htmlFile);
        }
        $time = time();
        $this->render(static::VIEW_EDITOR, [
            'timestamp' => $time,
            'token'     => md5($this->_config['validationKey'] . $time),
            'returnUrl' => DirectoryIndex::file2Url($file),
            'content'   => $content,
        ]);
    }

    /**
     * 目录已存在，列出文件。/docx/usage
     * @param $route
     * @throws Exception
     */
    public function actionListDir($route) {
        // 当前目录索引
        $currentIndex = DirectoryIndex::listDirectory($route, DirectoryIndex::MODE_READ);
        // 顶层目录索引
        $TopIndex = DirectoryIndex::listDirectory(static::MARKDOWN_ROOT . '/docx', DirectoryIndex::MODE_READ);

        $this->render(static::VIEW_LIST, [
            'index'   => $TopIndex,
            'currentIndex' => $currentIndex,
        ]);
    }

    /**
     * 上传附件
     */
    public function actionUploadAttached() {
        // Define a destination
        $verifyToken = md5($this->_config['validationKey'] . $_POST['timestamp']);
        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
            // Validate the file extensions
            $fileTypes = [
                'jpg', 'jpeg', 'gif', 'png', // 图片
                'zip', 'tar.gz', 'tgz', 'rar' // 附件
            ];
            $fileParts = pathinfo($_FILES['Filedata']['name']);

            if (in_array(strtolower($fileParts['extension']), $fileTypes)) {
                $tempFile   = $_FILES['Filedata']['tmp_name'];
                $newFile    = sprintf("%s-%d.%s", date("YmdHis", time()), rand(10, 99), $fileParts['extension']);
                $targetFile = rtrim(self::UPLOAD_ROOT, '/') . '/' . $newFile;
                $previewUrl = DirectoryIndex::file2Url($targetFile);
                $ret = move_uploaded_file($tempFile, $targetFile);
                echo $ret ? $previewUrl : '上传附件失败';
            } else {
                echo '附件格式只支持：' . join(', ', $fileTypes);
            }
        }
    }
}
