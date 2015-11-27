<?php
/* *****************************************************************
 * @Author: wushuiyong@huamanshu.com
 * @Created Time : 日  8/30 23:30:24 2015
 *
 * @File Name: Document.php
 * @Description:
 * *****************************************************************/

require_once 'Bootstrap.php';

class Document extends DirectoryIterator {

    /**
     * 文件类型：目录
     */
    const TYPE_DIR  = 'folder';

    /**
     * 文件类型：文件
     */
    const TYPE_FILE = 'item';

    const MODE_READ = 'r';

    const MODE_WRITE = 'w';

    public static $MARKDOWN_ROOT;

    public static $EXCLUDES = array('.git', 'upload', 'README.md',);

    public function __construct($path) {
        parent::__construct($path);
    }

    /**
     * 列出当前目录下的目录和文件
     *
     * @param string $dir
     * @param string $mode
     * @param bool $recourse
     * @return array
     */
    public static function listDirectory($dir = 'markdown', $mode = self::MODE_READ, $recourse = false) {
        $list = array();
        if (is_file($dir)) {
            return array($dir => static::TYPE_FILE);
        }

        if (!file_exists($dir)) {
            return $list;
        }

        $self = new static($dir);
        foreach ($self as $fileInfo) {
            if ($fileInfo->isDot() || in_array($fileInfo->__toString(), static::$EXCLUDES)) continue;
            $file  = sprintf('%s/%s', rtrim($dir, '/'), $fileInfo->__toString());
            $url   = static::file2Url($file, $mode);
            $title = basename($url);
            $item = array(
                'type' => $fileInfo->isFile() ? self::TYPE_FILE : self::TYPE_DIR,
                'name' => $fileInfo->isFile() ? static::trimFileExtension($title) : $title,
                'link' => $url,
            );
            if ($recourse && $fileInfo->isDir()) {
                $item['children'] = static::listDirectory(Bootstrap::route2file($url), self::MODE_READ, $recourse);
            }
            $list[] = $item;
        }
        sort($list);

        return $list;
    }

    /**
     * 获取markdown下的所有项目projects
     *
     * @return array
     */
    public static function getProjects() {
        $projects = static::listDirectory(Bootstrap::MARKDOWN_ROOT);
        foreach ($projects as $key => &$project) {
            if ($project['type'] == Document::TYPE_FILE) {
                unset($projects[$key]);
            }
        }
        return $projects;
    }

    /**
     * 一个md文件映射成可访问的url
     *
     * @param $file
     * @param string $mode
     * @return bool|string
     */
    public static function file2Url($file, $mode = self::MODE_READ) {
        $file = Bootstrap::getSafeFile($file);
        if (strpos($file, Bootstrap::MARKDOWN_ROOT) === 0) {
            $file = substr($file, strlen(Bootstrap::MARKDOWN_ROOT));
        }
        return $mode == self::MODE_READ && Bootstrap::isMarkDownFile($file) ? Bootstrap::md2HtmlFile($file) : $file;
    }

    /**
     * 去掉.md, .html后缀
     *
     * @param $file
     * @return string
     */
    public static function trimFileExtension($file) {
        $len = strlen($file);
        if (substr($file, $len - strlen(Bootstrap::TYPE_HTML)) == Bootstrap::TYPE_HTML) {
            $file = substr($file, 0, $len - strlen(Bootstrap::TYPE_HTML));
        }
        if (substr($file, $len - strlen(Bootstrap::TYPE_MD)) == Bootstrap::TYPE_MD) {
            $file = substr($file, 0, $len - strlen(Bootstrap::TYPE_MD));
        }

        return $file;
    }

}

