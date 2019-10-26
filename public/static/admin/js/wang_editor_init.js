;(function (win) {
    win.wangEditorInit=function () {
        layui.use(['layer','jquery'],function () {
            $ = layui.jquery;

            //富文本其他数据
            let data=$("#editor").data('data');
            //获取容器
            let containerEl=$(`#${data.container_id}`);
            //初始化
            let E = window.wangEditor;
            let editor = new E('#editor');
            //自定义菜单配置
            let menus=[
                'head',  // 标题
                'bold',  // 粗体
                'fontSize',  // 字号
                'fontName',  // 字体
                'italic',  // 斜体
                'underline',  // 下划线
                'strikeThrough',  // 删除线
                'foreColor',  // 文字颜色
                'backColor',  // 背景颜色
                'link',  // 插入链接
                'list',  // 列表
                'justify',  // 对齐方式
                'quote',  // 引用
                'emoticon',  // 表情
                'image',  // 插入图片
                'table',  // 表格
                'video',  // 插入视频
                'code',  // 插入代码
                'undo',  // 撤销
                'redo'  // 重复
            ];
            if(isMobile()){
                menus=[
                    'bold',  // 粗体
                    'fontSize',  // 字号
                    'foreColor',  // 文字颜色
                    'link',  // 插入链接
                    'justify',  // 对齐方式
                    'table',  // 表格
                    'code',  // 插入代码
                    'undo',  // 撤销
                    'redo'  // 重复
                ];
            }
            editor.customConfig.menus = menus;
            //配置颜色（字体颜色、背景色）
            editor.customConfig.colors = ['#000000', '#eeece0', '#1c487f', '#4d80bf', '#c24f4a', '#8baa4a', '#7b5ba1', '#46acc8', '#f9963b', '#ffffff'];
            //配置服务器端地址
            if(data.file_upload_url){
                //自定义上传图片事件，files 是 input 中选中的文件列表，insert 是获取图片 url 后，插入到编辑器的方法
                editor.customConfig.customUploadImg = function (files, insert) {
                    layer.load();
                    for(let i=0;i<files.length;i++){
                        // 实例化一个对象
                        let formdata = new FormData();
                        formdata.append("file" , files[i]);//获取文件
                        $.ajax({
                            url: data.file_upload_url,
                            type: 'POST',
                            data: formdata,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (res) {
                                console.log(res);
                                if(res.code!=='00000'){
                                    errorAlert(res.msg);
                                }else{
                                    // 上传代码返回结果之后，将图片插入到编辑器中
                                    insert(res.data.src);
                                }
                            },
                            error: function () {
                                errorAlert('上传失败');
                            }
                        });
                        if(i===files.length-1){
                            layer.closeAll('loading');
                        }
                    }
                };
            }
            //监控变化，同步更新到textarea
            if(containerEl.length){
                editor.customConfig.onchange = function (html) {
                    containerEl.html(html);
                };
            }
            editor.create();
            // 初始化 textarea 的值
            if(containerEl.length){
                containerEl.html(editor.txt.html());
            }
        });
    };
})(window);
