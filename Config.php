<?php
/* *****************************************************************
 * @Author: wushuiyong@huamanshu.com
 * @Created Time : 一  8/31 14:52:40 2015
 *
 * @File Name: config.php
 * @Description:
 * *****************************************************************/
return array(
    // 站点validationKey
    'validationKey' => 'PdXWDAfV5-gPJJWRar5sEN71DN0JcDRV',
    // 模板名字
    'template' => 'default',

    // 项目留空保存文档和附件的git地址，可以是在github，好吧，不想公开，可以bitbucket。
    // 1.php进程的用户的id_rsa.pub已添加到git的ssh-key。这样才可以推送markdown下的文件。
    'git' => 'git@bitbucket.org:wu_shuiyong/walden-markdown.git',

    // 2.好吧，如果实在不想加key，可以直接明文用户名密码认证的http(s)地址也可以。
    // 'git' => 'https://username:password@github.com/meolu/Walden-markdown-demo.git',
);
