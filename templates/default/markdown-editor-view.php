<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" type="text/css" href="/static/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/static/editor.css">
    <style>
    </style>
</head>
<body>

<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 一  8/31 15:50:02 2015
 *
 * @File Name: markdown-editor-view.php
 * @Description:
 * *****************************************************************/
?>

<div id="editor">
    <form action="" method="POST">
        <input name="token" type="hidden" value="<?= $token ?>">
        <input name="timestamp" type="hidden" value="<?= $timestamp ?>">
        <textarea  id="textarea" name="content"><?= $content ?></textarea>
        <div id="queue"></div>
        <input id="file_upload" class="upload" name="file_upload" type="file" multiple="true">
        <input type="submit" class="btn btn-default save" value="保存">
        <a href="<?= $returnUrl ?>" class="btn btn-default return" value="">返回</a>
    </form>
</div>

<script src="/static/jquery-1.8.2.min.js"></script>
<script src="/static/bootstrap/js/bootstrap.js"></script>
<script src="/static/jquery.uploadify.js" type="text/javascript"></script>
<script>
    var sprintf = function() {
        var arg = arguments,
            str = arg[0] || '',
            i, n;
        for (i = 1, n = arg.length; i < n; i++) {
            str = str.replace(/%s/, arg[i]);
        }
        return str;
    }
    function initPicUpload() {
        $('#file_upload').uploadify({
            'formData'     : {
                'timestamp' : '<?= $timestamp; ?>',
                'token'     : '<?= $token ?>'
            },
            'swf'      : '/static/uploadify.swf',
            'uploader' : '<?= Bootstrap::UPLOAD_URL ?>',
            'onSelect': function(file) {
                console.log(file.name+"---"+file.id);
            },// 选择文件时触发的方法

            'onUploadError' : function(file, errorCode, errorMsg, errorString) {
                console.log('onUploadError')
                console.log('The file ' + file.name + ' could not be uploaded: ' + errorString);
            },//上传出错后的方法

            'onUploadSuccess' : function(file, url, response) {
                console.log(file.name + response + ':' + url);
                var append = sprintf("![%s](%s)", file.name, url);
                var editor = $("#textarea");
                editor.val(editor.val() + "\n" + append);
            }
        });
    }
    $(function() {
        initPicUpload();
    });
</script>
</body>
</html>
