<?php if (!defined('ROOT_PATH')) exit('No Permission');?>
<script type="text/javascript">
    $(document).ready(function () {
        $('ul#setting-base,.tab-base').on('click', function () {
            console.log('点击li元素了~');
            if (!$.cookie('id')) {
                // 刷新主页面
                // window.location.href = BASE_URL;
                parent.location.reload();
            }
        });
    });
</script>
</body>
</html>