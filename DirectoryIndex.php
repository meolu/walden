<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 日  8/30 23:30:24 2015
 *
 * @File Name: Directory.php
 * @Description:
 * *****************************************************************/

require_once 'Bootstrap.php';

class DirectoryIndex extends DirectoryIterator {

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

    public static $EXCLUDES = ['.git', 'upload',];

    public function __construct($path) {
        parent::__construct($path);
    }

    public static function listDirectory($dir = 'docx', $mode = self::MODE_READ, $recourse = false) {
        $list = [];
        if (is_file($dir)) return [$dir => TYPE_FILE];

        $self = new static($dir);
        foreach ($self as $fileInfo) {
            if ($fileInfo->isDot() || in_array($fileInfo->__toString(), static::$EXCLUDES)) continue;
            $file  = sprintf('%s/%s', rtrim($dir, '/'), $fileInfo->__toString());
            $url   = static::file2Url($file, $mode);
            $title = basename($url);
            $item = [
                'type' => $fileInfo->isFile() ? self::TYPE_FILE : self::TYPE_DIR,
                'name' => $fileInfo->isFile() ? static::trimFileExtension($title) : $title,
                'link' => $url,
            ];
            if ($recourse && $fileInfo->isDir()) {
                $item['children'] = static::listDirectory(Bootstrap::route2file($url), self::MODE_READ, $recourse);
            }
            $list[] = $item;


        }
        return $list;
    }

    public static function getProjects() {
        $projects = static::listDirectory(Bootstrap::MARKDOWN_ROOT);
        foreach ($projects as $key => &$project) {
            if ($project['type'] == DirectoryIndex::TYPE_FILE) {
                unset($projects[$key]);
            }
        }
        return $projects;
    }


    public static function file2Url($file, $mode = self::MODE_READ) {
        $file = Bootstrap::getSafeFile($file);
        if (strpos($file, Bootstrap::MARKDOWN_ROOT) === 0) {
            $file = substr($file, strlen(Bootstrap::MARKDOWN_ROOT));
        }
        return $mode == self::MODE_READ && Bootstrap::isMarkDownFile($file) ? Bootstrap::md2HtmlFile($file) : $file;
    }


    public static function trimFileExtension($file) {
        return trim(trim($file, Bootstrap::TYPE_HTML), Bootstrap::TYPE_MD);
    }

}

