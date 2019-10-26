;(function (win) {
    win.fileUpload=function () {
        layui.use(['layer','jquery','upload'],function (){
            let $ = layui.jquery;
            let layer = layui.layer;
            let upload = layui.upload;

            upload.render({
                elem: '.file-upload',
                url:'',
                accept:'file',
                size: 0,
                before: function(obj){
                    layer.load();
                },
                done: function(res, index, upload){
                    layer.closeAll('loading');
                    if(res.code==='00000'){
                        successAlert(res.msg);
                        let el=this.item;
                        let data=res.data;
                        switch (data.type){
                            case 'image':
                                $(el).siblings(".upload-img-txt").val(data.src);
                                $(el).siblings(".upload-img-container").find("img").attr('src',data.src);
                                break;
                        }
                    }else {
                        errorAlert(res.msg);
                    }
                },
                error: function(index, upload){
                    layer.closeAll('loading');
                }
            });
        });

        //预览图片
        $(".upload-img-container img").click(function () {
            previewImg($(this));
        });

        //删除
        $(".upload-img-container .layui-icon-close-fill").click(function () {
            let el=$(this);
            layer.confirm(`确认删除？`, {icon: 0, title: '提示'}, function (index) {
                let imgEl=el.siblings('img');
                let imgTxtEl=el.parent('.upload-img-container').siblings('.upload-img-txt');
                imgEl.attr('src','');
                imgTxtEl.val('');
                layer.close(index);
            });
        });
    };

})(window);
