<!DOCTYPE html>
<?php
$route = Bootstrap::route2file(urldecode($_SERVER['REQUEST_URI']));
$baseUrl = preg_replace('#markdown#', '', Bootstrap::getProjectByRoute($route));
?>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?php echo $title ?> - <?php echo Bootstrap::DOC_NAME ?></title>

    <link rel="stylesheet" type="text/css" href="/static/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/static/bootstrap/css/font-awesome.min.css" />
    <link rel="stylesheet" href="/static/bootstrap/css/ace.min.css" />
    <link rel="stylesheet" type="text/css" href="/static/bootstrap/css/base.css" />
    <link rel="stylesheet" href="/static/bootstrap/css/github.css">

    <!--[if lt IE 9]>
    <script src="/static/bootstrap/js/html5shiv.js"></script>
    <script src="/static/bootstrap/js/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<nav class="navbar navbar-inverse navbar-static-top top-navbar header-color-black" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/"><?php echo Bootstrap::DOC_NAME ?></a>
        </div>
        <div class="collapses navbar-collapses">
            <ul class="nav navbar-nav">
                <?php foreach (Document::getProjects() as $project) { ?>
                    <li><a href="<?php echo $project['link'] ?>"><?php echo $project['name'] ?></a></li>
                <?php } ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if (isset($editUrl)) { ?><li><a href="<?php echo $editUrl?>">编辑</a></li><?php } ?>
            </ul>
        </div>
    </div>
</nav>
<header class="jumbotron subhead">
    <div class="container">
        <h1><small>Demo</small></h1>
    </div>
</header>

<div class="container">
    <div class="row">
        <div class="col-sm-3">
            <div class="widget-box">
                <div class="widget-header header-color-green2 header-color-sblue">
                    <h4 class="lighter smaller">目录</h4>
                </div>

                <div class="widget-body">
                    <div class="widget-main padding-8">
                        <div id="tree" class="tree"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-9 content">
            <!--文档中文内容 start-->
            <?php echo $content ?>
            <!--文档中文内容 end-->
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container">
        <p class="pull-right">
            <a href="#">Back to top</a>
        </p>
        <ul class="footer-links">
            <li><a href="http://www.huamanshu.com/walden.html" target="_blank">walden主页</a></li>
            <li><a href="https://github.com/meolu/walden" target="_blank">github项目</a></li>
            <li><a href="https://github.com/meolu/walden/issues?state=open" target="_blank">提交bug</a></li>
        </ul>
    </div>
</footer>
<!-- basic scripts -->

<!--[if !IE]> -->

<script type="text/javascript">
    window.jQuery || document.write("<script src='/static/bootstrap/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
</script>

<!-- <![endif]-->

<!--[if IE]>
<script type="text/javascript">
    window.jQuery || document.write("<script src='/static/bootstrap/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

<script type="text/javascript">
    if("ontouchend" in document) document.write("<script src='/static/bootstrap/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>
<script src="/static/bootstrap/js/bootstrap.min.js"></script>
<!--<script src="/static/bootstrap/js/typeahead-bs2.min.js"></script>-->

<!-- page specific plugin scripts -->

<script src="/static/bootstrap/js/fuelux/fuelux.tree.min.js"></script>

<!-- ace scripts -->

<script src="/static/bootstrap/js/ace-elements.min.js"></script>
<script src="/static/bootstrap/js/ace.min.js"></script>
<script src="/static/bootstrap/js/highlight.pack.js"></script>

<!-- inline scripts related to this page -->

<script type="text/javascript">
    jQuery(function($){
        var format = function (o) {
            var list = [];
            $.each(o, function(k, v) {
                var item = v;
                if (item.type == 'folder') {
                    item.additionalParameters = {'children': format(item.children)};
                } else {
                    item.name = '<i class="icon-file-text"></i><a href="' + item.link + '">' + item.name + '</a>'
                }
               list.push(item)
            })
            return list;
        }


        $.get('<?php echo $baseUrl ?>?recourse=1', function(o) {
            var treeData = format(o.data);
            var DataSourceTree = function(options) {
                this._data 	= options.data;
                this._delay = options.delay;
            }

            DataSourceTree.prototype.data = function(options, callback) {
                var self = this;
                var $data = null;

                if(!("name" in options) && !("type" in options)){
                    $data = this._data;//the root tree
                    callback({ data: $data });
                    return;
                }
                else if("type" in options && options.type == "folder") {
                    if("additionalParameters" in options && "children" in options.additionalParameters)
                        $data = options.additionalParameters.children;
                    else $data = {}//no data
                }

                if($data != null)//this setTimeout is only for mimicking some random delay
                    setTimeout(function(){callback({ data: $data });} , parseInt(Math.random() * 500) + 200);

            };
            var treeDataSource = new DataSourceTree({data: treeData});
            $('#tree').ace_tree({
                dataSource: treeDataSource ,
                loadingHTML:'<div class="tree-loading"><i class="icon-refresh icon-spin blue"></i></div>',
                'open-icon' : 'icon-folder-open',
                'close-icon' : 'icon-folder-close',
                'selectable' : false,
                'selected-icon' : null,
                'unselected-icon' : null
            });
        })

        <?php if (isset($_GET['action']) && urldecode($_GET['action']) == Bootstrap::PUSH_GIT_URL) { ?>
        // 是否为编辑后的第一次文档预览，需要推送到git
        $.get('<?php echo Bootstrap::PUSH_GIT_URL ?>', function (o) {
            console.log(o);
        })
        <?php } ?>
    });

    // 统计
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?5980089b1455e9e015256741d0ab0b2e";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();

    // 代码高亮
    hljs.initHighlightingOnLoad();
</script>
</body>
</html>
