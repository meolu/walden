<?php
/* *****************************************************************
 * @Author: wushuiyong@huamanshu.com
 * @Created Time : 一  8/31 12:39:22 2015
 *
 * @File Name: Bootstrap.php
 * @Description:
 * *****************************************************************/
require_once 'Command.php';
require_once 'Parsedown.php';
require_once 'Document.php';

class Bootstrap {

    /**
     * 项目名字
     * 为了作者的付出，请保留
     */
    const DOC_NAME = 'Walden';
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
    /**
     * 推送git接口
     */
    const PUSH_GIT_URL = '/git/push';

    private $_config;

    /**
     * 图片后缀
     */
    public $imageExtensions = array(
        'jpg', 'jpeg', 'gif', 'png',
    );

    /**
     * 附件后缀
     */
    public $attachedExtensions = array(
        'zip', 'tar.gz', 'tgz', 'rar',

    );

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
        $file  = static::route2file($route);
        // 先创建markdown作为文档仓库，可以保存至git
        if (!file_exists(static::MARKDOWN_ROOT)) {
            $this->actionInit();
        }
        // html渲染
        elseif ($file && static::isHtmlFile($file)) {
            $this->actionReadHtml($file);
        }
        // 编辑md文件(md文件不存在则先创建) /docx/usage/start.md
        elseif ($file && static::isMarkDownFile($file)) {
            $this->actionEditMarkdown($file, $route);
        }
        // 目录已存在，列出文件。/docx/usage
        elseif ($file && file_exists($file) && is_dir($file)) {
            $this->actionListDir($file, isset($_GET['recourse']));
        }
        // 上传附件
        elseif ($route === static::getSafeFile(self::UPLOAD_URL)) {
            $this->actionUploadAttached();
        }
        // 推送git
        elseif ($route === static::getSafeFile(self::PUSH_GIT_URL)) {
            $this->actionPushGit();
        } else {
            throw new Exception("无此目录：{$file}，请确认：）");
        }
    }

    /**
     * 判断是否为AJAX (XMLHttpRequest)请求
     * @return bool
     */
    public static function getIsAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
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
        $ret = array(
            'code' => (int)$code,
            'msg'  => $msg,
            'data' => $data
        );
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
        $pos = strpos($file, '?');
        if ($pos !== false) {
            $file = substr($file, 0, $pos);
        }
        $file = ltrim($file, '/');
        $file = ltrim($file, '.');
        while ($file != $origin) {
            $origin = $file;
            $file = static::getSafeFile($file);
        }
        return $file;
    }

    public static function route2file($route) {
        return sprintf('%s/%s', trim(static::MARKDOWN_ROOT, '/'), trim($route, '/'));
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

    /**
     * markdownhtml文件路径转化为markdown文件路径
     *
     * @example /docx/read.html => /docx/readme.md
     * @param $file
     * @return bool|string
     */
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

    public static function getProjectByRoute($route) {
        $pattern = sprintf('#(%s/[^/]+)#', static::MARKDOWN_ROOT);
        preg_match($pattern, static::getSafeFile($route), $match);
        return $match ? current($match) : static::MARKDOWN_ROOT;
    }

    /**
     * html渲染
     *
     * @param $file
     * @throws Exception
     */
    public function actionReadHtml($file) {
        $mdFile = static::html2MdFile($file);
        if (!$mdFile || !file_exists($mdFile) || !is_file($mdFile)) {
            throw new Exception("无此文档：{$file}，请确认：）");
        }
        // 目录索引
        $index = Document::listDirectory(static::getProjectByRoute($mdFile), Document::MODE_READ);
        // 标题
        $title = Document::trimFileExtension(basename($file));
        // 文档预览
        $content = file_get_contents($mdFile);
        $parser = new Parsedown();
        $dom =  $parser->text($content);
        $this->render(static::VIEW_DETAIL, array(
            'index'   => $index,
            'editUrl' => str_replace('//', '/', Document::file2Url($mdFile, Document::MODE_WRITE)),
            'title'   => $title,
            'content' => $dom,
        ));
    }


    /**
     * 编辑md文件 /docx/usage/start.md
     *
     * @param $file
     * @param $route
     * @throws Exception
     */
    public function actionEditMarkdown($file, $route) {
        $title = Document::trimFileExtension(basename($file));
        if (!is_file($file)) {
            $cmd[]   = sprintf("mkdir -p %s", dirname($file));
            $cmd[]   = sprintf("echo '# %s%s-------------' > %s", $title, PHP_EOL, $file);
            $command = join(" && ", $cmd);
            $cmd = new Command();
            $ret = $cmd->execute($command);
        }
        $content = file_get_contents($file);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $content = $_POST['content'];
            $ret = file_put_contents($file, $content);
            $htmlFile = static::md2HtmlFile($route);
            $url = sprintf("/%s?action=%s", $htmlFile, static::PUSH_GIT_URL);
            $this->redirect($url);
        }
        $time = time();
        $this->render(static::VIEW_EDITOR,array(
            'timestamp' => $time,
            'token'     => md5($this->_config['validationKey'] . $time),
            'returnUrl' => Document::file2Url($file),
            'title'     => $title,
            'content'   => $content,
        ));
    }

    /**
     * 目录已存在，列出文件。但我不想写了。。。/docx/usage
     *
     * @param $route
     * @throws Exception
     */
    public function actionListDir($route, $recourse = false) {
        $title = Document::trimFileExtension(basename($route));
        // 当前目录索引
        $currentIndex = Document::listDirectory($route, Document::MODE_READ, $recourse);
        // 如果是ajax请求，则以json返回
        if (static::getIsAjax()) {
            $this->renderJson(array_values($currentIndex));
        }
        $mdFile = sprintf('%s/README.md', $route);
        if (file_exists($mdFile) && is_file($mdFile)) {
            return $this->actionReadHtml(static::md2HtmlFile($mdFile));
        }
        // 顶层目录索引
        $TopIndex = Document::listDirectory(static::getProjectByRoute($route), Document::MODE_READ);
        $this->render(static::VIEW_DETAIL,array(
            'index'   => $TopIndex,
            'currentIndex' => $currentIndex,
            'title'     => $title,
            'content' => '',
        ));
    }

    /**
     * 上传附件
     */
    public function actionUploadAttached() {
        // Define a destination
        $verifyToken = md5($this->_config['validationKey'] . $_POST['timestamp']);
        if (empty($_FILES)) die('请上传文件');
        if ($_POST['token'] != $verifyToken) die('别闹了：）');

        // Validate the file extensions
        $fileTypes = array_merge($this->imageExtensions, $this->attachedExtensions);
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        if (!in_array(strtolower($fileParts['extension']), $fileTypes)) die('上传附件失败，附件格式只支持：' . join(', ', $fileTypes));

        $tempFile   = $_FILES['Filedata']['tmp_name'];
        $newFile    = sprintf("%s-%d.%s", date("YmdHis", time()), rand(10, 99), $fileParts['extension']);
        $targetFile = rtrim(self::UPLOAD_ROOT, '/') . '/' . $newFile;
        $ret = move_uploaded_file($tempFile, $targetFile);

        $md = in_array(strtolower($fileParts['extension']), $this->imageExtensions)
            ? sprintf("![%s](/%s)", $_FILES['Filedata']['name'], trim($targetFile, '/'))
            : sprintf("[%s](/%s)", $_FILES['Filedata']['name'], trim($targetFile, '/'));
        echo $ret ? $md : '上传附件失败';

    }

    /**
     * 初始化markdown
     */
    public function actionInit() {
        if (empty($this->_config['git'])) {
            throw new Exception('请先在Config.php中配置文档保存到的git ssh地址：)');
        }
        if (strpos($this->_config['git'], '.git') === false) {
            throw new Exception('请确保Config.php中配置的git为ssh格式地址：)');
        }
        $git = new Command();
        $ret = $git->initGit($this->_config['git']);
        if ($ret) {
            $this->redirect('/');
        } else {
            $git->cleanInitDir();
            throw new Exception('初始化git文档目录失败，请确认php进程用户'
                . getenv("USER") . '的ssh-key已加入git的ssh-key列表。');
        }
    }

    /**
     * 推送git
     */
    public function actionPushGit() {
        if (empty($this->_config['git'])) {
            throw new Exception('请先在Config.php中配置文档保存到的git ssh地址：)');
        }
        try {
            $git = new Command();
            $ret = $git->gitPush();
            echo $ret ? '推送成功：）' : '推送失败：（';
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * 用户错误捕获
     *
     * @param $errNo
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param $errContext
     */
    public function errorHandler($errNo , $errStr, $errFile, $errLine, $errContext) {
        throw new Exception($errStr);
    }

    /**
     * 异常捕获处理
     *
     * @param Exception $e
     * @throws Exception
     */
    public function exceptionHandler(Exception $e) {
        $msg = sprintf('<div class="alert alert-danger">%s</div>', $e->getMessage());
        $this->render(static::VIEW_DETAIL,array(
            'title'   => '哎哟，不好了：(',
            'content' => $msg,
        ));
    }

}
