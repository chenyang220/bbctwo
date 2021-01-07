// 此js是从common.js中单独出来的一部分，只用作于全局anmaition动画效果（若引入了common.js，此js可不引入，只作为前端静态时使用）
(function($) {
    $.extend($, {
        /**
         * 从右到左动态显示隐藏内容
         *
         */
        animationLeft: function(options) {
            var defaults = {
                    valve : '.animation-left',          // 动作触发
                    wrapper : '.nctouch-full-mask',    // 动作块
                    scroll : '',     // 滚动块，为空不触发滚动
                    openCallback : "" //显示内容触发事件
            }
            var options = $.extend({}, defaults, options);
            function _init() {
                $(document).on('click', options.valve, function(){
                    options.openCallback && options.openCallback();
                    $(options.wrapper).removeClass('hide').removeClass('right').addClass('left');

                    if (options.scroll != '') {
                        if (typeof(myScrollAnimationLeft) == 'undefined') {
                            if (typeof(IScroll) == 'undefined') {
                                $.ajax({
                                    url: WapSiteUrl+'/js/iscroll.js',
                                    dataType: "script",
                                    async: false
                                });
                            }
                            myScrollAnimationLeft = new IScroll(options.scroll, { mouseWheel: true, click: true });
                        } else {
                            myScrollAnimationLeft.refresh();
                        }
                    }
                });
                $(options.wrapper).on('click', '.header-l > a', function(){
                    $(options.wrapper).addClass('right').removeClass('left');
                });

                $(document).on("click", "#ldg_lockmask", function() {
                    $(options.wrapper).addClass('right').removeClass('left');
                });
            }
            return this.each(function() {
                _init();
            })();
        },

        /**
         * 从下到上动态显示隐藏内容
         *
         */
        animationUp: function(options) {
            var defaults = {
                    valve : '.animation-up',                    // 动作触发，为空直接触发
                    wrapper : '.nctouch-bottom-mask',           // 动作块
                    scroll : '.nctouch-bottom-mask-rolling',    // 滚动块，为空不触发滚动
                    start : function(){},       // 开始动作触发事件
                    close : function(){}        // 关闭动作触发事件
            }
            var options = $.extend({}, defaults, options);
            function _animationUpRun() {
                // options.start.call('start');
                $(options.wrapper).removeClass('down').addClass('up');

                if (options.scroll != '') {
                    if (typeof(myScrollAnimationUp) == 'undefined') {
                        if (typeof(IScroll) == 'undefined') {
                            $.ajax({
                                url: WapSiteUrl+'/js/iscroll.js',
                                dataType: "script",
                                async: false
                              });
                        }
                        myScrollAnimationUp = new IScroll(options.scroll, { mouseWheel: true, click: true });
                    } else {
                        myScrollAnimationUp.refresh();
                    }
                }
            }
            return this.each(function() {
                var trigger_element; //触发元素
                if (options.valve != '') {
                    // $(options.valve).on('click', function(){
                    $(document).on('click',options.valve, function(){
                        trigger_element = this;
                        options.start.call(this);
                        _animationUpRun();
                    });
                } else {
                    _animationUpRun();
                }
                $(options.wrapper).on('click', '.nctouch-bottom-mask-bg,.nctouch-bottom-mask-close,.JS_close', function(){
                    $(options.wrapper).addClass('down').removeClass('up');
                    options.close.call(this, trigger_element);
                });
            })();
        }
    });
})(Zepto);

