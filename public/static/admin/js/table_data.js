layui.use(['layer', 'jquery', 'form', 'table'], function () {
    $ = layui.jquery;
    let layer = layui.layer;
    let form = layui.form;
    let table = layui.table;

    /**
     * 屏幕宽度小等于768初始化
     */
    window.screenWidthElt768Init=function () {
        //搜索表单
        if($("#search_form").length){
            //追加展开/收起元素
            let el='<button type="button" class="layui-btn layui-btn-sm layui-btn-primary search-open-retract" data-current="retract">展开 <i class="layui-icon">&#xe61a;</i></button>';
            if($("#search_form .show-xs-block:last .search-open-retract").length<=0){
                $("#search_form .show-xs-block:last").append(el);
                //展开收起绑定事件
                $(".search-open-retract").bind("click",function(){
                    let current=$(this).data('current');
                    let _current='open';
                    let _html='收起 <i class="layui-icon">&#xe619;</i>';
                    if(current==='open'){
                        _current='retract';
                        _html='展开 <i class="layui-icon">&#xe61a;</i>';
                    }
                    $("#search_form .show-xs-block:not(:first,:last)").slideToggle("fast");
                    $(this).data('current',_current);
                    $(this).html(_html);
                });
            }else{
                //每次重载完毕之后都是收起的
                $("#search_form .show-xs-block:not(:first,:last)").slideUp("fast");
                $(".search-open-retract").data('current','retract');
                $(".search-open-retract").html('展开 <i class="layui-icon">&#xe61a;</i>');
            }
        }
    };

    /**
     * 表格初始化
     */
    window.tableInit=function () {
        //屏幕宽度小于768执行
        if(window.screen.width<=768){
            screenWidthElt768Init();
        }
        //排序
        $(".table-sort").bind("blur",function(){
            let data=$(this).data('data');
            let sort=$(this).val();
            //判断是否误操作
            if (data.oldSort == sort) return;
            let requestData = {id: data.id, sort: sort};
            request({
                url: data.url,
                data: requestData,
                successCallback: function (res) {
                    successAlert(res.msg);
                    refreshTable(true);
                },
                errorCallback: function (res) {
                    errorAlert(res.msg,function (index, layero) {
                        $(this).val(data.oldSort);
                        layer.close(index);
                    });
                }
            });
        });
        //图片预览
        $(".table-img").click(function () {
            previewImg($(this));
        });
    };

    //表格渲染
    if (typeof table_args !== "undefined") {
        var table_data = table.render(Object.assign({},{
            elem: '#table_data',
            url: window.location.pathname.replace('index.html','search.html'),
            parseData: function (res) {
                return {
                    "code": res.code === '00000' ? 0 : 1,
                    "msg": res.msg,
                    "count": res.data.count,
                    "data": res.data.items
                };
            },
            page: true,
            limit: 20,
            loading: true,
            text: {none: '暂无相关数据'},
            toolbar: '#toolbar',
            defaultToolbar: ['filter','print'],
            cols: [],
            done: function() {
                tableInit();
                table.resize(this.id);
            }
        },table_args));
    }

    /**
     * 刷新表格
     * @param currentPage [是否刷新当前分页]
     */
    window.refreshTable=function (currentPage) {
        currentPage=currentPage||false;
        let page=currentPage?$(".layui-laypage-em").next().html():1;
        let where = formFields();
        table_data.reload({where: where,page:page});
    };

    //表格重载搜索功能
    form.on('submit(submit)', function (data) {
        refreshTable();
        //阻止表单跳转
        return false;
    });

    //监听表格左上操作栏事件
    table.on('toolbar(table_data)', function (obj) {
        let el=$(".layui-btn-container").find(`.layui-btn[lay-event=${obj.event}]`);
        let data=el.data("data");
        switch (obj.event) {
            case 'add':
                //弹出层类型，0（信息框，默认）1（页面层）2（iframe层）3（加载层）4（tips层）
                openPopup(data);
                break;
            case 'del_batch':
                layer.confirm(`确认要${data.title}吗？`, {icon: 0, title: '提示'}, function () {
                    let checkStatus = table.checkStatus(obj.config.id);
                    let checkeds = checkStatus.data;
                    if (!checkeds.length) {
                        warnAlert('请选择数据');
                        return;
                    }
                    let ids = '';
                    checkeds.forEach((item) => {
                        ids += `,${item.id}`;
                    });
                    ids = ids.substr(1);
                    let url = data.url;
                    let requestData = {ids: ids, title: data.title};
                    request({
                        url: url,
                        data: requestData,
                        successCallback: function (res) {
                            successAlert(res.msg);
                            refreshTable(true);
                        }
                    });
                });
                break;
            case 'export':
                let export_data=obj.config.export_data;
                layer.confirm(`确认要导出${export_data.title}吗？`, {icon: 0, title: '提示'}, function (index) {
                    let url=export_data.url;
                    let where = Object.assign({},{
                        title:export_data.title
                    },formFields());
                    layer.close(index);
                    location.href = formatToUrlParams(url,where);
                });
                break;
        }
    });

    //监听表格内操作列事件
    table.on('tool(table_data)', function (obj) {
        let event = obj.event;
        let tr = obj.tr;
        let data=$(tr).find(".layui-btn-group").find(`.layui-btn[lay-event=${event}]`).data("data");
        switch (event) {
            case 'add':
                openPopup(data);
                break;
            case 'edit':
                openPopup(data);
                break;
        }
    });

    //监听状态选择开关
    form.on('switch(status)', function (data) {
        let obj = data.elem;
        let _data=$(obj).data("data");
        let url = _data.url;
        let requestData={id:_data.id,status:data.elem.checked?1:0};
        request({
            url: url,
            data: requestData,
            errorCallback: function (res) {
                res.msg = res.msg || '更改状态失败';
                errorAlert(res.msg,function (index, layero) {
                    $(obj).attr('checked', !data.elem.checked);
                    form.render();
                    layer.close(index);
                });
            }
        });
    });
});