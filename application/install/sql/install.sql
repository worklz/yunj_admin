

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ss_config_group
-- ----------------------------
DROP TABLE IF EXISTS `ss_config_group`;
CREATE TABLE `ss_config_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '配置组名称',
  `code` varchar(255) NOT NULL COMMENT '配置组代码',
  `sort` tinyint(2) unsigned NOT NULL DEFAULT '99' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0禁用，1正常默认',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`(191)) USING HASH,
  UNIQUE KEY `code` (`code`(191)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='配置组-表';

-- ----------------------------
-- Records of ss_config_group
-- ----------------------------
INSERT INTO `ss_config_group` VALUES ('1', '基础配置', 'base', '1', '1567783672', '1567824868', '1');
INSERT INTO `ss_config_group` VALUES ('2', '其他配置', 'other', '99', '1567824860', '1569379538', '1');
INSERT INTO `ss_config_group` VALUES ('3', '上传配置', 'upload', '20', '1569379423', '1569379534', '1');

-- ----------------------------
-- Table structure for ss_config_item
-- ----------------------------
DROP TABLE IF EXISTS `ss_config_item`;
CREATE TABLE `ss_config_item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '配置项名称',
  `code` varchar(255) NOT NULL COMMENT '配置项代码',
  `value` varchar(255) DEFAULT NULL COMMENT '配置项值',
  `tips` varchar(500) DEFAULT NULL COMMENT '配置项提示',
  `options` varchar(500) DEFAULT NULL COMMENT '配置供选项，适用于有多个选项的类型的配置',
  `sort` tinyint(2) unsigned NOT NULL DEFAULT '99',
  `group_id` int(11) unsigned NOT NULL COMMENT '配置组id',
  `type_id` int(11) unsigned NOT NULL COMMENT '配置类型id',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0禁用，1正常默认',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`(191)) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE,
  KEY `type_id` (`type_id`) USING BTREE,
  KEY `name` (`name`(191)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COMMENT='系统配置项-表';

-- ----------------------------
-- Records of ss_config_item
-- ----------------------------
INSERT INTO `ss_config_item` VALUES ('1', '站点名称', 'site_name', '云静Admin', '将显示在浏览器窗口标题等位置', '', '1', '1', '1', '1568000718', '1570171221', '1');
INSERT INTO `ss_config_item` VALUES ('2', 'text测试', 'test_text', 'text测试', 'text测试提示', '', '1', '2', '1', '1568009088', '1569389624', '1');
INSERT INTO `ss_config_item` VALUES ('3', 'textarea测试', 'test_textarea', 'textarea测试', 'textarea测试提示', '', '2', '2', '2', '1568010137', '1569389624', '1');
INSERT INTO `ss_config_item` VALUES ('4', 'radio测试', 'test_radio', 'r2', 'radio测试提示', '[{\"key\":\"r1\",\"value\":\"\\u9009\\u98791\"},{\"key\":\"r2\",\"value\":\"\\u9009\\u98792\"},{\"key\":\"r3\",\"value\":\"\\u9009\\u98793\"}]', '3', '2', '3', '1568081059', '1569389624', '1');
INSERT INTO `ss_config_item` VALUES ('5', 'switch测试', 'test_switch', '1', 'switch测试提示', '[{\"key\":\"1\",\"value\":\"\\u5f00\"},{\"key\":\"2\",\"value\":\"\\u5173\"}]', '4', '2', '7', '1568081123', '1569389624', '1');
INSERT INTO `ss_config_item` VALUES ('6', 'checkbox测试', 'test_checkbox', 'c1,c2,c3', 'checkbox测试提示', '[{\"key\":\"c1\",\"value\":\"\\u9009\\u98791\"},{\"key\":\"c2\",\"value\":\"\\u9009\\u98792\"},{\"key\":\"c3\",\"value\":\"\\u9009\\u98793\"}]', '5', '2', '4', '1568081173', '1569389624', '1');
INSERT INTO `ss_config_item` VALUES ('7', 'select测试', 'test_select', 's2', 'select测试提示', '[{\"key\":\"s1\",\"value\":\"\\u9009\\u98791\"},{\"key\":\"s2\",\"value\":\"\\u9009\\u98792\"},{\"key\":\"s3\",\"value\":\"\\u9009\\u98793\"}]', '6', '2', '5', '1568081230', '1569389624', '1');
INSERT INTO `ss_config_item` VALUES ('8', 'image测试', 'test_image', '/favicon.ico', 'image测试提示', '', '7', '2', '6', '1568081272', '1569389624', '1');
INSERT INTO `ss_config_item` VALUES ('9', 'array测试', 'test_array', 'Index/index,Error/index,User/info', 'test_array测试提示语', '', '99', '2', '8', '1568276218', '1569389624', '1');
INSERT INTO `ss_config_item` VALUES ('10', '站点地址', 'site_domain', '', '', '', '2', '1', '1', '1568276911', '1570171221', '1');
INSERT INTO `ss_config_item` VALUES ('11', '通用控制器/方法', 'no_check_controller_action', 'Index/index,Error/index,User/info', '', '', '50', '1', '8', '1568277428', '1570171221', '1');
INSERT INTO `ss_config_item` VALUES ('12', '导出文件数据量限制', 'export_file_data_number_limit', '800', '每个文件数据条数限制，单位/条', '', '99', '1', '1', '1568855575', '1570171221', '1');
INSERT INTO `ss_config_item` VALUES ('13', '站点图标', 'site_favicon', '/favicon.ico', '用于浏览器的地址栏展示，.ico格式', '', '3', '1', '6', '1569378332', '1570171221', '1');
INSERT INTO `ss_config_item` VALUES ('14', '站点LOGO', 'site_logo', '/favicon.ico', '站点的LOGO图片', '', '40', '1', '6', '1569378398', '1570171221', '1');
INSERT INTO `ss_config_item` VALUES ('15', '图片大小限制', 'image_size', '2048', '单位：KB，0表示不限制上传大小', '', '99', '3', '1', '1569379745', '1569379745', '1');
INSERT INTO `ss_config_item` VALUES ('16', '图片格式', 'image_ext', 'jpg,png,gif,jpeg,ico', '多个格式请用英文逗号（,）隔开', '', '99', '3', '1', '1569379876', '1569379876', '1');
INSERT INTO `ss_config_item` VALUES ('17', '操作超时时间', 'action_timeout', '1800', '单位：秒。设置时间不可过小，请谨慎操作', '', '45', '1', '1', '1569490682', '1570171221', '1');

-- ----------------------------
-- Table structure for ss_config_type
-- ----------------------------
DROP TABLE IF EXISTS `ss_config_type`;
CREATE TABLE `ss_config_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '配置类型名称',
  `type` varchar(255) NOT NULL COMMENT '配置类型',
  `sort` tinyint(2) unsigned NOT NULL DEFAULT '99',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0禁用，1正常默认',
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`(191)) USING BTREE,
  KEY `name` (`name`(191)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='配置类型-表';

-- ----------------------------
-- Records of ss_config_type
-- ----------------------------
INSERT INTO `ss_config_type` VALUES ('1', '单行文本框', 'text', '1', '1567784782', '1567784811', '1');
INSERT INTO `ss_config_type` VALUES ('2', '多行文本框', 'textarea', '2', '1567784803', '1567784830', '1');
INSERT INTO `ss_config_type` VALUES ('3', '单选按钮', 'radio', '3', '1567949683', '1567949789', '1');
INSERT INTO `ss_config_type` VALUES ('4', '多选框', 'checkbox', '5', '1567949706', '1567950291', '1');
INSERT INTO `ss_config_type` VALUES ('5', '下拉选框', 'select', '6', '1567949732', '1567950295', '1');
INSERT INTO `ss_config_type` VALUES ('6', '图片', 'image', '7', '1567949781', '1567950298', '1');
INSERT INTO `ss_config_type` VALUES ('7', '开关', 'switch', '4', '1567950277', '1567950284', '1');
INSERT INTO `ss_config_type` VALUES ('8', '数组', 'array', '8', '1568276132', '1568276137', '1');

-- ----------------------------
-- Table structure for ss_log
-- ----------------------------
DROP TABLE IF EXISTS `ss_log`;
CREATE TABLE `ss_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `ip` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL COMMENT '描述',
  `request_content` text NOT NULL COMMENT '请求内容：url、type、param',
  `create_time` int(10) unsigned NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE,
  KEY `description` (`description`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='日志记录-表';

-- ----------------------------
-- Records of ss_log
-- ----------------------------

-- ----------------------------
-- Table structure for ss_menu
-- ----------------------------
DROP TABLE IF EXISTS `ss_menu`;
CREATE TABLE `ss_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级菜单id，0顶级菜单默认',
  `name` varchar(50) CHARACTER SET utf8mb4 NOT NULL COMMENT '菜单名称',
  `code` varchar(50) CHARACTER SET utf8mb4 NOT NULL COMMENT '控制器/方法，名',
  `icon` varchar(50) CHARACTER SET utf8mb4 NOT NULL COMMENT '菜单图标',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '等级，0顶级默认',
  `sort` tinyint(4) NOT NULL DEFAULT '99' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0不显示，1默认显示，是否显示在菜单栏',
  `create_time` int(10) NOT NULL,
  `update_time` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0失效，1默认正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COMMENT='菜单-表';

-- ----------------------------
-- Records of ss_menu
-- ----------------------------
INSERT INTO `ss_menu` VALUES ('1', '0', '系统管理', 'System/index', 'layui-icon layui-icon-template-1', '0', '2', '1', '1566142117', '1566728572', '1');
INSERT INTO `ss_menu` VALUES ('2', '1', '菜单管理', 'Menu/index', 'layui-icon layui-icon-app', '1', '1', '1', '1566195505', '1566553618', '1');
INSERT INTO `ss_menu` VALUES ('3', '2', '添加', 'Menu/add', 'layui-icon layui-icon-app', '2', '1', '0', '1566205056', '1566553629', '1');
INSERT INTO `ss_menu` VALUES ('4', '2', '编辑', 'Menu/edit', 'layui-icon layui-icon-app', '2', '1', '0', '1566205614', '1566553636', '1');
INSERT INTO `ss_menu` VALUES ('5', '2', '状态编辑', 'Menu/status', 'layui-icon layui-icon-app', '2', '5', '0', '1566381533', '1566957315', '1');
INSERT INTO `ss_menu` VALUES ('6', '2', '是否显示', 'Menu/show', 'layui-icon layui-icon-app', '2', '4', '0', '1566381552', '1566814082', '1');
INSERT INTO `ss_menu` VALUES ('7', '2', '批量失效', 'Menu/delBatch', 'layui-icon layui-icon-app', '2', '3', '0', '1566381576', '1566729833', '1');
INSERT INTO `ss_menu` VALUES ('8', '0', '我的桌面', 'Index/welcome', 'layui-icon layui-icon-home', '0', '1', '0', '1566524570', '1566957506', '1');
INSERT INTO `ss_menu` VALUES ('9', '1', '角色管理', 'Role/index', 'layui-icon layui-icon-user', '1', '2', '1', '1566529838', '1566553682', '1');
INSERT INTO `ss_menu` VALUES ('10', '1', '用户管理', 'User/index', 'layui-icon layui-icon-username', '1', '3', '1', '1566529866', '1566553701', '1');
INSERT INTO `ss_menu` VALUES ('11', '9', '添加', 'Role/add', 'layui-icon layui-icon-app', '2', '99', '0', '1566633429', '1566633429', '1');
INSERT INTO `ss_menu` VALUES ('12', '9', '编辑', 'Role/edit', 'layui-icon layui-icon-app', '2', '99', '0', '1566633449', '1566633449', '1');
INSERT INTO `ss_menu` VALUES ('13', '2', '排序', 'Menu/sort', 'layui-icon layui-icon-app', '2', '2', '0', '1566633534', '1566729809', '1');
INSERT INTO `ss_menu` VALUES ('14', '9', '搜索', 'Role/search', 'layui-icon layui-icon-app', '2', '99', '0', '1566633601', '1566633601', '1');
INSERT INTO `ss_menu` VALUES ('15', '9', '状态编辑', 'Role/status', 'layui-icon layui-icon-app', '2', '99', '0', '1566635126', '1566635126', '1');
INSERT INTO `ss_menu` VALUES ('16', '9', '排序', 'Role/sort', 'layui-icon layui-icon-app', '2', '2', '0', '1566635154', '1567565570', '1');
INSERT INTO `ss_menu` VALUES ('17', '10', '添加', 'User/add', 'layui-icon layui-icon-app', '2', '99', '0', '1566635325', '1566635325', '1');
INSERT INTO `ss_menu` VALUES ('18', '10', '编辑', 'User/edit', 'layui-icon layui-icon-app', '2', '99', '0', '1566635341', '1566635341', '1');
INSERT INTO `ss_menu` VALUES ('19', '10', '搜索', 'User/search', 'layui-icon layui-icon-app', '2', '99', '0', '1566635367', '1566635367', '1');
INSERT INTO `ss_menu` VALUES ('20', '10', '状态编辑', 'User/status', 'layui-icon layui-icon-app', '2', '99', '0', '1566635472', '1566635472', '1');
INSERT INTO `ss_menu` VALUES ('21', '10', '批量禁用', 'User/delBatch', 'layui-icon layui-icon-app', '2', '99', '0', '1566635513', '1566635513', '1');
INSERT INTO `ss_menu` VALUES ('22', '9', '批量失效', 'Role/delBatch', 'layui-icon layui-icon-app', '2', '1', '0', '1566635744', '1566958670', '1');
INSERT INTO `ss_menu` VALUES ('23', '1', '系统日志', 'Log/index', 'layui-icon layui-icon-form', '1', '40', '1', '1566894664', '1569476106', '1');
INSERT INTO `ss_menu` VALUES ('24', '2', '搜索', 'Menu/search', 'layui-icon layui-icon-app', '2', '6', '0', '1566997446', '1567565212', '1');
INSERT INTO `ss_menu` VALUES ('25', '23', '搜索', 'Log/search', 'layui-icon layui-icon-app', '2', '99', '0', '1566997532', '1567474077', '1');
INSERT INTO `ss_menu` VALUES ('26', '1', '系统配置', 'Config/index', 'layui-icon layui-icon-util', '1', '50', '1', '1567678664', '1569476111', '1');
INSERT INTO `ss_menu` VALUES ('27', '26', '搜索', 'Config/search', 'layui-icon layui-icon-app', '2', '1', '0', '1568282505', '1568282553', '1');
INSERT INTO `ss_menu` VALUES ('28', '26', '配置类型管理', 'ConfigType/index', 'layui-icon layui-icon-app', '2', '30', '0', '1568282541', '1569476364', '1');
INSERT INTO `ss_menu` VALUES ('29', '26', '配置组管理', 'ConfigGroup/index', 'layui-icon layui-icon-app', '2', '40', '0', '1569476287', '1569476368', '1');
INSERT INTO `ss_menu` VALUES ('30', '26', '编辑', 'Config/edit', 'layui-icon layui-icon-app', '2', '20', '0', '1569476348', '1569476359', '1');
INSERT INTO `ss_menu` VALUES ('31', '28', '搜索', 'ConfigType/search', 'layui-icon layui-icon-app', '3', '99', '0', '1569476832', '1569476832', '1');
INSERT INTO `ss_menu` VALUES ('32', '28', '添加', 'ConfigType/add', 'layui-icon layui-icon-app', '3', '99', '0', '1569476843', '1569476843', '1');
INSERT INTO `ss_menu` VALUES ('33', '28', '编辑', 'ConfigType/edit', 'layui-icon layui-icon-app', '3', '99', '0', '1569476861', '1569476861', '1');
INSERT INTO `ss_menu` VALUES ('34', '28', '排序', 'ConfigType/sort', 'layui-icon layui-icon-app', '3', '99', '0', '1569476886', '1569476886', '1');
INSERT INTO `ss_menu` VALUES ('35', '28', '状态编辑', 'ConfigType/status', 'layui-icon layui-icon-app', '3', '99', '0', '1569476904', '1569476904', '1');
INSERT INTO `ss_menu` VALUES ('36', '28', '批量禁用', 'ConfigType/delBatch', 'layui-icon layui-icon-app', '3', '99', '0', '1569476940', '1569476940', '1');
INSERT INTO `ss_menu` VALUES ('37', '29', '搜索', 'ConfigGroup/search', 'layui-icon layui-icon-app', '3', '10', '0', '1569477021', '1569477171', '1');
INSERT INTO `ss_menu` VALUES ('38', '29', '添加', 'ConfigGroup/add', 'layui-icon layui-icon-app', '3', '20', '0', '1569477031', '1569477180', '1');
INSERT INTO `ss_menu` VALUES ('39', '29', '编辑', 'ConfigGroup/edit', 'layui-icon layui-icon-app', '3', '30', '0', '1569477050', '1569477187', '1');
INSERT INTO `ss_menu` VALUES ('40', '29', '排序', 'ConfigGroup/sort', 'layui-icon layui-icon-app', '3', '40', '0', '1569477069', '1569477194', '1');
INSERT INTO `ss_menu` VALUES ('41', '29', '状态编辑', 'ConfigGroup/status', 'layui-icon layui-icon-app', '3', '50', '0', '1569477093', '1569477215', '1');
INSERT INTO `ss_menu` VALUES ('42', '29', '批量禁用', 'ConfigGroup/delBatch', 'layui-icon layui-icon-app', '3', '60', '0', '1569477117', '1569477220', '1');
INSERT INTO `ss_menu` VALUES ('43', '29', '配置项列表', 'ConfigItem/index', 'layui-icon layui-icon-app', '3', '70', '0', '1569477156', '1569477226', '1');
INSERT INTO `ss_menu` VALUES ('44', '43', '搜索', 'ConfigItem/search', 'layui-icon layui-icon-app', '4', '99', '0', '1569477269', '1569477269', '1');
INSERT INTO `ss_menu` VALUES ('45', '43', '添加', 'ConfigItem/add', 'layui-icon layui-icon-app', '4', '99', '0', '1569477280', '1569477280', '1');
INSERT INTO `ss_menu` VALUES ('46', '43', '编辑', 'ConfigItem/edit', 'layui-icon layui-icon-app', '4', '99', '0', '1569477293', '1569477293', '1');
INSERT INTO `ss_menu` VALUES ('47', '43', '排序', 'ConfigItem/sort', 'layui-icon layui-icon-app', '4', '99', '0', '1569477311', '1569477311', '1');
INSERT INTO `ss_menu` VALUES ('48', '43', '状态编辑', 'ConfigItem/status', 'layui-icon layui-icon-app', '4', '99', '0', '1569477329', '1569477329', '1');
INSERT INTO `ss_menu` VALUES ('49', '43', '批量禁用', 'ConfigItem/delBatch', 'layui-icon layui-icon-app', '4', '100', '0', '1569477352', '1569477384', '1');

-- ----------------------------
-- Table structure for ss_role
-- ----------------------------
DROP TABLE IF EXISTS `ss_role`;
CREATE TABLE `ss_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT '角色名称',
  `alias` varchar(60) NOT NULL COMMENT '别名',
  `description` varchar(255) DEFAULT NULL COMMENT '简介',
  `menu_ids` varchar(800) DEFAULT NULL COMMENT '用,隔开的菜单id',
  `sort` tinyint(2) unsigned NOT NULL DEFAULT '99',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0失效，1正常默认',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='角色-表';

-- ----------------------------
-- Records of ss_role
-- ----------------------------
INSERT INTO `ss_role` VALUES ('1', '管理员', 'admin', '拥有大部分后台权限', '8,1,2,4,3,13,7,6,5,24,9,22,16,15,14,12,11,10,21,20,19,18,17,23,25,26,27,30,28,36,35,34,33,32,31,29,37,38,39,40,41,42,43,44,45,46,47,48,49', '1', '1566381411', '1569477488', '1');

-- ----------------------------
-- Table structure for ss_user
-- ----------------------------
DROP TABLE IF EXISTS `ss_user`;
CREATE TABLE `ss_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '账户（只能是英文数字下划线）',
  `nickname` varchar(50) NOT NULL COMMENT '昵称，用于对外显示',
  `login_pwd` char(32) NOT NULL COMMENT '登录密码password+login_salt',
  `login_salt` char(6) NOT NULL COMMENT '登录加密字符串',
  `email` varchar(60) NOT NULL COMMENT '邮箱',
  `mobile` varchar(20) NOT NULL COMMENT '手机',
  `role_id` int(11) unsigned NOT NULL COMMENT '角色id',
  `last_login_ip` varchar(128) NOT NULL COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL COMMENT '最后登录时间',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0禁用，1正常默认',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE,
  KEY `nickname` (`nickname`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户-表';

-- ----------------------------
-- Records of ss_user
-- ----------------------------
