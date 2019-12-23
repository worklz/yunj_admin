云静Admin V1.0.2 —— 通用后台管理框架
===============

基于TP5.1+ layui开发的通用后台管理框架，默认有安装模块和后台模块。后台集成了后台常用功能模块，以方便开发者快速构建自己的应用。

+ 菜单管理
+ 角色管理
+ 用户管理
+ 系统日志
+ 系统配置

#### 一、富文本编辑器的引入

1. kindeditor--推荐使用
    > 在视图文件中书写如下：
    ```
    <textarea id="kindeditor_container">请输入内容...</textarea>
    <script src="/static/libs/kindeditor/kindeditor-all-min.js" charset="utf-8"></script>
    <script src="/static/libs/kindeditor/lang/zh-CN.js" charset="utf-8"></script>
    <script type="text/javascript" src="__ADMIN__/js/kindeditor_init.js?v={$Think.config.system.version}"></script>
    <script type="text/javascript">
        kindEditorInit();
    </script>
    ```

2. wangEditor
    > 在视图文件中书写如下：
    ```
    <textarea id="wang_editor" style="display: none;"></textarea>
    <div id="editor" data-data='{"container_id":"wang_editor","file_upload_url":"{:url(\"fileUpload\")}"}'>
        <p>请输入内容...</p>
    </div>
    <script src="/static/libs/wangEditor-3.1.1/release/wangEditor.js" charset="utf-8"></script>
    <script type="text/javascript" src="__ADMIN__/js/wang_editor_init.js?v={$Think.config.system.version}"></script>
    <script type="text/javascript">
        wangEditorInit();
    </script>
    ```

体验地址：[http://admin.yunj.net.cn/admin ](http://admin.yunj.net.cn/admin)

体验账户：admin 123456

#### 更新历史

> 1.0.24

    * 安装时判断是否存在redis扩展，增加redis信息填写步骤
    * 缓存驱动方式由file改为redis
    * session由文件存储改为redis存储（减轻文件I/O）
    * cookie保存用户登录状态信息（防止分布式部署，用户状态丢失）