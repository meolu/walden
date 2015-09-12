<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title><?= $title ?> - 编辑 - <?= Bootstrap::DOC_NAME ?></title>
    <link rel="stylesheet" type="text/css" href="/static/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/static/editor.css">
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
        <button type="submit" class="btn btn-default save" aria-label="Left Align" value="保存">
            <span class="glyphicon glyphicon-align-left glyphicon-floppy-saved" aria-hidden="true"></span>
            保存
        </button>
        <a href="<?= $returnUrl ?>" class="btn btn-default return" value="">
            <i class="glyphicon  glyphicon-arrow-left"></i>返回
        </a>
    </form>
</div>

<script src="/static/jquery-1.8.2.min.js"></script>
<script src="/static/bootstrap/js/bootstrap.js"></script>
<script src="/static/jquery.uploadify.js" type="text/javascript"></script>
<script>

    function initPicUpload() {
        $('#file_upload').uploadify({
            'formData'     : {
                'timestamp' : '<?= $timestamp; ?>',
                'token'     : '<?= $token ?>'
            },
            'buttonText' : '插入图片 / 附件',
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
                var editor = $("#textarea");
                editor.val(editor.val() + "\n" + url);
            }
        });
    }
    $(function() {
        initPicUpload();
    });
</script>
</body>
</html>
