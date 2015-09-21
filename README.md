# Walden 最适合东半球同学使用的文档框架

或许是极人性化的一个文档管理框架，让你一下子就喜欢上写文档分享，[官网主页](http://www.huamanshu.com/walden.html)了解更多。

## 演示
![walden](https://raw.github.com/meolu/Walden/master/static/screenshots/walden.gif)

体验[demo](http://walden.huamanshu.com/)。

## 特点

* Markdown语法
* 修改后实时展现，无编译
* 多模板支持
* 图片、附件上传，自动生成url
* 多项目
* 任意定义目录嵌套、定义文档，目录与文档均可中文（甚至推荐中文）
* 文档、图片、附件同步保存至git，这下你安心了吧

## 一、安装

零安装、零配置，无数据库，不需要composer，开箱即用。

* 依赖git，php，nginx环境
* 检出Walden到Walden_WORKSPACE，注意该目录可写markdown目录
* 配置nginx指向Walden_WORKSPACE

## 二、快速开始

### A 初始化
```
Config.php
<?php
return [
    // 保存文档和附件的git ssh地址，可以是在github，好吧，不想公开，可以bitbucket
    'git' => 'git@github.com:meolu/Walden-markdown-demo.git',
];
```
php进程的用户的id_rsa.pub已添加到git的ssh-key。这样才可以推送markdown下的文件。然后初始化markdown目录：`http://your-domain/`

### B 创建项目

创建方式：http://your-domain/project-name/readme.md
项目可以是中文，或许有更强的阅读性，如：http://your-domain/滑雪修炼手册/介绍.md。

### C 新建文档

新建方式：http://your-domain/project-name/dir/dir/doc.md
由创建项目的规则知道，url跟认知中的目录是一样的，请随意发挥吧

### D 修改文档

修改方式：http://your-domain/project-name/dir/dir/doc.md
其实，你不需要记录目录，文档的右上角有`编辑`链接直达

## 三、自定义

### 自定义模板

前端同学可以自己定义模板，在templates下新建一个模板目录，包含预览模板：`markdown-detail-view.php`，编辑模板：`markdown-editor-view.php`，然后修改`Config.php`的`template`为你的模板项目。

最后，当然希望你可以给此项目提个merge_request，目前只有一个bootstrap的默认模板：(


## to do list

* 文档搜索
* 文档删除，重命名
* 新UI

## 四、可能会遇到的问题


### nginx简单配置

```
server {
    listen       80;
    server_name  Walden.dev;
    root /the/dir/of/Walden;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
```

###上传文件提示413 Request Entity Too Large

```
修改nginx.conf的http模块：client_max_body_size 10m;
```


### 有问题加群
** QQ：135114826 **

