;(function (win,$){
    "use strict";
    /**
     * 成功提示
     * @param msg
     */
    win.successAlert = function (msg) {
        layer.msg(msg, {icon: 1,time: 2000});
    };

    /**
     * 错误提示
     * @param msg
     * @param callback
     */
    win.errorAlert = function (msg,callback) {
        layer.open({
            title: '在线调试',
            icon: 2,
            content: msg,
            yes: callback||function(index, layero){
                //如果设定了yes回调，需进行手工关闭
                layer.close(index);
            },
        });
    };

    /**
     * 警示提示
     * @param msg
     */
    win.warnAlert = function (msg) {
        layer.msg(msg, {icon: 0,time: 2000});
    };
    
    /**
     * 打开弹出层
     * @param args
     */
    win.openPopup = function (args) {
        let area='auto';
        let w=win.screen.width;
        if(w<=375){
            area=['80%','80%'];
        }else if(w>375&&w<=768){
            area=['70%','70%'];
        }else if(w>768&&w<=1366){
            area=['60%','60%'];
        }else {
            area=['60%','60%'];
        }
        args = Object.assign({}, {
            type: 2,
            title: '',
            shadeClose: true,
            shade: 0.5,
            maxmin: true,
            area: area,
            content: '',
            success:function () {
                //给表格增加属性表示是否有数据变化，有则重载表格
                if($("#table_data").length>0){
                    $("#table_data").attr("data-change","0");
                }
            },
            end:function () {
                if($("#table_data").length>0){
                    let dateChange=$("#table_data").attr("data-change");
                    if(dateChange==="1") refreshTable(true);
                }
            }
        }, args);
        let index = layer.open(args);
        return index;
    };

    /**
     * 是否移动端
     */
    win.isMobile = function () {
        return navigator.userAgent.toLowerCase().match(/(ipod|iphone|android|coolpad|mmp|smartphone|midp|wap|xoom|symbian|j2me|blackberry|wince)/i) != null;
    };

    /**
     * 处理中，遮罩层防二次点击
     * @param msg
     */
    win.loadingStart = function (msg) {
        msg = msg || '处理中...';
        return layer.msg(msg, {icon: 16, shade: [0.3, '#393D49'], time: 60 * 60 * 1000});
    };

    /**
     * 结束处理中
     */
    win.loadingEnd = function (index) {
        parent.layer.close(index);
    };

    /**
     * 封装ajax异步请求
     * @param args [action_txt 操作文字，用于弹窗提示]
     * @param args [loading_txt 处理弹窗文字]
     * @param args [loading_index 处理弹窗索引]
     * @param args [successCallback 成功回调]
     * @param args [errorCallback 失败回调]
     */
    win.request = function (args) {
        args = Object.assign({}, {
            type: 'post',
            url: null,
            async: true,
            data: null,
            dataType: 'json',
            loading_txt: '处理中...',
            loading_index: null,
            action_txt: null,
            successCallback: function (res) {
                successAlert(res.msg);
            },
            errorCallback: function (res) {
                errorAlert(res.msg);
            }
        }, args);
        //处理中...
        args.loading_index = loadingStart(args.loading_txt);
        //正式请求
        $.ajax({
            type: args.type,
            url: args.url,
            async: args.async,
            data: args.data,
            dataType: args.dataType,
            success: function (res) {
                requestCallback(args, res)
            },
            error: function (XMLHttpRequest) {
                requestCallback(args);
            }
        });

    };

    /**
     * 请求回调
     * @param args [请求参数]
     * @param res [请求结果]
     */
    win.requestCallback = function (args, res) {
        args = args || {};
        res = res || {};
        if (!$.isPlainObject(args) || !$.isPlainObject(res)) return true;
        //关闭处理中...
        loadingEnd(args.loading_index);
        //判断是否有结果
        if (res.code === undefined) {
            errorAlert(`${args.action_txt}失败`);
            return true;
        }
        //判断结果是否成功
        if (res.code === '00000') {
            args.successCallback && args.successCallback(res);
        } else {
            args.errorCallback && args.errorCallback(res);
        }
        return true;
    };

    /**
     * 获取表单字段，形式：{name: value}
     */
    win.formFields = function (formID) {
        let json = {};
        formID = formID || 'search_form';
        let form = $("#" + formID);
        if(form.length<=0) return json;
        form.serializeArray().forEach(function(val, index, arr){
            json[val['name']]=val['value'];
        });
        return json;
    };

    /**
     *  将json对象|数组格式化为url参数
     * @param url
     * @param data [json|array]
     */
    win.formatToUrlParams=function (url,data) {
        if(url.indexOf('?')===-1) url+='?';

        let paramStr='';
        if(typeof data==='object'){
            Object.keys(data).forEach(function (key) {
                paramStr+=`&${key}=${data[key]}`;
            });
        }

        url=url+(paramStr.substr(0, 1) === "&"?paramStr.substr(1):paramStr);

        return url;
    };

    /**
     * 页面加载完后执行
     */
    win.onload=function (){

        //监听刷新事件
        $('#refresh').click(function () {
            if($("#table_data").length>0){
                //刷新表格当前页
                refreshTable(true);
            }else {
                location.reload();
            }
        });

    };

    /**
     * 图片预览
     * @param imgEl [图片对象]
     */
    win.previewImg=function (imgEl) {
        //获取图片src
        let imgSrc =imgEl.attr("src");
        //设置弹出层大小
        let w=win.screen.width;
        let areaW='auto';
        if(w<=375){
            areaW=0.9*w;
        }else if(w>375&&w<=768){
            areaW=0.7*w;
        }else {
            areaW=480;
        }
        openPopup({
            type:1,
            title:false,
            skin:'layui-layer-nobg',
            shadeClose:true,
            maxmin:false,
            area:areaW+'px',
            content:'<img src="'+imgSrc+'" style="width:100%">',
            scrollbar:false
        });
    };

})(window,jQuery);

layui.use(['jquery','form'], function(){
    $ = layui.jquery;
    let form = layui.form;

    //监听表单添加、编辑的提交
    form.on('submit(submit_btn)', function (data) {
        layer.confirm('确认要提交吗？', {icon: 0, title: '提示'}, function () {
            let form = data.form;
            let url = $(form).data("url");
            let method = $(form).data("method");
            let requestData = data.field;
            request({
                type: method,
                url: url,
                data: requestData,
                successCallback:function (res) {
                    if($("#table_data",parent.document).length>0){
                        $("#table_data",parent.document).attr("data-change","1");
                    }
                    successAlert(res.msg);
                }
            });
        });
    });
});