<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 日  8/30 23:30:24 2015
 *
 * @File Name: Directory.php
 * @Description:
 * *****************************************************************/

class DirectoryIndex extends DirectoryIterator {

    /**
     * 文件类型：目录
     */
    const TYPE_DIR  = 'd';

    /**
     * 文件类型：文件
     */
    const TYPE_FILE = 'f';

    public function __construct($path) {
        parent::__construct($path);
    }

    public static function listDirectory($dir = 'docx') {
        $list = [];
        if (is_file($dir)) return [$dir => TYPE_FILE];

        $self = new static($dir);
        foreach ($self as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            $list[$fileInfo->__toString()] = $fileInfo->isFile() 
                ? self::TYPE_FILE
                : self::TYPE_DIR;
        }
        return $list;
    }

}

#$list = DirectoryIndex::listDirectory('./docx'); var_dump($list);
