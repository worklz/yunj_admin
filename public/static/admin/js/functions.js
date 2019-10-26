;(function (win, $) {
    "use strict";
    /**
     * 时间戳格式化
     * @param timestamp [10位时间戳]
     */
    win.formatDate = function (timestamp) {
        timestamp=timestamp<=9999999999?parseInt(timestamp*1000):timestamp;
        let d=new Date(timestamp);

        let year=d.getFullYear();  //取得4位数的年份
        let month=d.getMonth()+1;  //取得日期中的月份，其中0表示1月，11表示12月
        let date=d.getDate();      //返回日期月份中的天数（1到31）
        let hour=d.getHours();     //返回日期中的小时数（0到23）
        let minute=d.getMinutes(); //返回日期中的分钟数（0到59）
        let second=d.getSeconds(); //返回日期中的秒数（0到59）
        return year+"-"+month+"-"+date+" "+hour+":"+minute+":"+second;
    };

})(window, jQuery);