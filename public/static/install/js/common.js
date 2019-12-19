layui.use(['jquery','layer','element', 'form'],function(){
    let $ = layui.jquery;
    let layer = layui.layer;
    let form = layui.form;

    //成功提示
    window.successAlert = function (msg) {
        layer.msg(msg, {icon: 1,time: 2000});
    };

    //错误提示
    window.errorAlert = function (msg,callback) {
        layer.open({
            title: '提示',
            icon: 2,
            content: msg,
            yes: callback||function(index, layero){
                //如果设定了yes回调，需进行手工关闭
                layer.close(index);
            },
        });
    };

    //警示提示
    window.warnAlert = function (msg) {
        layer.msg(msg, {icon: 0,time: 2000});
    };

    //处理中，遮罩层防二次点击
    window.loadingStart = function (msg) {
        msg = msg || '处理中...';
        return layer.msg(msg, {icon: 16, shade: [0.3, '#393D49'], time: 60 * 60 * 1000});
    };

    //结束处理中
    window.loadingEnd = function (index) {
        parent.layer.close(index);
    };

    //Ajax请求
    window.request = function (args) {
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
                requestCallback(args,res)
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
    window.requestCallback = function (args, res) {
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

    //进入系统配置
    $(".enter-system-config").click(function(){
        let data=$(this).data('data');
        if(data.install_check_pass){
            location.href=data.url;
        }else {
            errorAlert('环境检测未通过，不能进行安装操作！');
        }
    });

    //表单验证
    form.verify({
        alpha_: function(value, item){
            if(!value){
                return '不能留空！';
            }
            if(!/^[0-9a-zA-Z_]{1,}$/.test(value)){
                return '格式错误！';
            }
        },
        required_: function(value, item){
            if(!/^[a-z0-9]{1,20}[_]{1}$/.test(value)){
                return "末尾字符必须为 '_'";
            }
        },
        length4: function(value, item){
            if(value.length<4){
                return '数据长度错误！';
            }
        },
        length6: function(value, item){
            if(value.length<6){
                return '数据长度错误！';
            }
        }
    });

    //测试redis连接
    form.on('submit(redisInstall)', function(data){
        layer.confirm(`确认现在连接吗？`, {icon: 0, title: '提示'}, function () {
            let formEl=data.form;
            let formData=$(formEl).data('data');
            if(!formData.environment_pass){
                errorAlert('环境检测未通过，不能进行测试连接操作！');
                return false;
            }
            request({
                type:'post',
                url:formData.redis_install_url,
                data:data.field,
                loading_txt:'测试连接中...',
                action_txt:'测试连接',
                successCallback:function (res) {
                    successAlert(res.msg);
                },
                errorCallback:function (res) {
                    errorAlert(res.msg,function (index, layero) {
                        layer.close(index);
                    });
                }
            });
        });
        return false;
    });
    
    //测试数据库连接
    form.on('submit(dbInstall)', function(data){
        layer.confirm(`确认现在连接吗？`, {icon: 0, title: '提示'}, function () {
            let formEl=data.form;
            let formData=$(formEl).data('data');
            if(!formData.environment_pass){
                errorAlert('环境检测未通过，不能进行测试连接操作！');
                return false;
            }
            if(!formData.redis_pass){
                errorAlert('Redis测试连接未通过，不能进行测试连接操作！');
                return false;
            }
            request({
                type:'post',
                url:formData.db_install_url,
                data:data.field,
                loading_txt:'测试连接中...',
                action_txt:'测试连接',
                successCallback:function (res) {
                    successAlert(res.msg);
                },
                errorCallback:function (res) {
                    errorAlert(res.msg,function (index, layero) {
                        layer.close(index);
                    });
                }
            });
        });
        return false;
    });
    
    //立即安装
    form.on('submit(nowInstall)', function(data){
        layer.confirm(`确认要立即安装吗？`, {icon: 0, title: '提示'}, function () {
            let formEl=data.form;
            let formData=$(formEl).data('data');
            if(!formData.environment_pass){
                errorAlert('环境检测未通过，不能执行安装操作！');
                return false;
            }
            if(!formData.redis_pass){
                errorAlert('Redis测试连接未通过，不能执行安装操作！');
                return false;
            }
            if(!formData.db_pass){
                errorAlert('数据库测试连接未通过，不能执行安装操作！');
                return false;
            }
            request({
                type:'post',
                url:formData.now_install_url,
                data:data.field,
                loading_txt:'安装中...',
                action_txt:'安装',
                successCallback:function (res) {
                    successAlert(res.msg);
                    window.location.href=res.data.url;
                },
                errorCallback:function (res) {
                    errorAlert(res.msg,function (index, layero) {
                        layer.close(index);
                    });
                }
            });
        });
        return false;
    });

});