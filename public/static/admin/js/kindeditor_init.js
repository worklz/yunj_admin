;(function (win) {
    win.kindEditorInit = function () {
        layui.use(['layer', 'jquery'], function () {
            $ = layui.jquery;

            //获取容器
            let editorEl = $("#kindeditor_container");
            //获取外部数据
            let data = editorEl.data('data');
            //配置编辑器的工具栏
            let items = [
                'source', '|', 'undo', 'redo', '|', 'preview', 'code', '|',
                'justifyleft', 'justifycenter', 'justifyright', 'justifyfull',
                'insertorderedlist', 'insertunorderedlist', 'formatblock', 'fontname', 'fontsize', '|',
                'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', '|',
                'image', 'insertfile', 'table', 'hr', 'link'
            ];
            //最小高度
            let minHeight=400;
            if (isMobile()) {
                items = [
                    'undo', 'redo', '|', 'preview', 'code', '|',
                    'insertorderedlist', 'insertunorderedlist', 'formatblock', '|',
                    'image', 'insertfile', 'table', 'hr', 'link'
                ];
                minHeight=300;
            }
            //创建编辑器
            KindEditor.ready(function (K) {
                window.editor = K.create('#kindeditor_container', {
                    items: items,
                    resizeType: 1,
                    pasteType: 1,
                    width: '100%',
                    minHeight: minHeight,
                    syncType: '',
                    afterChange: function () {
                        let that = this;
                        editorEl.html(that.html());
                    },
                    allowFileManager: true
                });
            });
        });
    };
})(window);
