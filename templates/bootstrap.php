<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="utf-8" />
    <title>doxmate Documentation</title>
    <meta name="keywords" content="documentation,dox" />
    <meta name="description" content="Generate your documentation." />
    <script src="/assets/prettify.js"></script>
    <script src="/assets/jquery-1.8.2.min.js"></script>
    <script src="/assets/bootstrap/js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="/assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/assets/base.css" />
</head>
<body>
<nav class="navbar navbar-inverse navbar-static-top top-navbar" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="./index.html">doxmate</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li>
                    <a href="./api.html">API Docs</a>
                </li>

                <li>
                    <a href="./Changelog.html">Changelog</a>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div>
</nav>
<header class="jumbotron subhead">
    <div class="container">
        <h1>doxmate <small>Version: 0.3.0 By @Jackson Tian</small></h1>
        <p class="lead">
            Generate your documentation.
        </p>
    </div>
</header>

<div class="container content">
    <div class="row">
        <div class="col-md-3">
            <ul class="nav nav-list bs-docs-sidenav affix">

                <li class="level_1">
                    <a href="#index_" title="">
                        <i class="icon-chevron-right"></i>
                    </a>
                </li>
                <!--文档的目录-->
            </ul>

        </div>
        <div class="col-md-9">
            <!--文档中文内容-->
            <?= $content ?>
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container">
        <p class="pull-right">
            <a href="#">Back to top</a>
        </p>
        <p>此文档通过doxmate生成。主题借鉴Bootstrap API文档风格，注解基于<a href="https://github.com/visionmedia/dox">Dox</a>。欢迎关注doxmate作者<a href="http://weibo.com/shyvo" target="_blank">@朴灵</a></p>
        <ul class="footer-links">
            <li><a href="https://github.com/visionmedia/dox">Dox主页</a></li>
            <li><a href="http://html5ify.com/doxmate">Doxmate主页</a></li>
            <li><a href="https://github.com/JacksonTian/doxmate">Doxmate源码</a></li>
            <li><a href="https://github.com/JacksonTian/doxmate/issues?state=open">提交bug</a></li>
        </ul>
    </div>
</footer>
<script>
    $(function() {
        $('pre').addClass('prettyprint');
        $('td pre').removeClass('prettyprint');
        prettyPrint();
        var $window = $(window);
        var sidenav = $('.bs-docs-sidenav');
        if (sidenav.height() < window.innerHeight) {
            sidenav.affix({
                offset: {
                    top: function () {
                        return $window.width() <= 980 ? 290 : 210
                    },
                    bottom: 200
                }
            });
        } else {
            sidenav.removeClass('affix');
        }
        $(".content").find('h1, h2, h3, h4, h5, h6').each(function () {
            var node = $(this);
            // 总是设置id
            node.attr("id", node.data('id') || "index_" + node.text());
        });

        $('.bs-docs-sidenav .accordion-marker').on('click', function(event) {
            var current = $(event.currentTarget);
            current.find('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
        });
    });
</script>
</body>
</html>
