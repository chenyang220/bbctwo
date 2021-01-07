/**
 * Created by rd04 on 2016/5/29.
 */

// $(function() {
/*
 * data is Object
 * thumbnailWidth
 * thumbnailHeight
 * imageContainer (Selector image)
 * uploadButton (Selector div)
 * */

var requestOnce = true;
var agent = navigator.userAgent.toLowerCase();
if ( agent.indexOf("msie") > -1 && (version = agent.match(/msie [\d]/), ( version == "msie 8" || version == "msie 9" )) ) {
    window.isIE8 = true;
}

UploadVideo = function (uploadArguments) {
    var _this = this;
    this.arguments = uploadArguments;

    //获取全局下载配置
    function getUploadConfig()
    {
        if (requestOnce) { //同一个页面只请求一次，不是frame加载，设为全局变量意义不大
            requestOnce = false;
        } else {
            return false;
        }
        var ajaxObj = {
            type: "get",
            url: SITE_URL + "?ctl=Upload&action=config",
            dataType: "jsonp",
            jsonp: "callback"
        };

        $.ajax(ajaxObj)
            .done(function(uploadConfig) {
                window.uploadConfig = uploadConfig;
            })
            .fail(function() {
                Public.tips.error('获取下载配置失败');
            });
    }

    getUploadConfig();

    _this.interval = setInterval(function(){
        if (window.uploadConfig) {
            _this.init();
        }
    }, 1000);
}

UploadVideo.prototype = {
    uploadConfig: {},
    init: function () {
        clearInterval(this.interval);
        this.initUploader();
    },
    initUploader: function () {
        var ratio = window.devicePixelRatio || 1,
            thumbnailWidth = this.arguments.thumbnailWidth ? this.arguments.thumbnailWidth * ratio : 113 * ratio,
            thumbnailHeight = this.arguments.thumbnailHeight ? this.arguments.thumbnailHeight * ratio : 113 * ratio,
            uploadButton = this.arguments.uploadButton ? this.arguments.uploadButton : '#uploader',
            $image = this.arguments.imageContainer ? $(this.arguments.imageContainer) : $('#uploadImage'),
            $input = this.arguments.inputHidden ? $(this.arguments.inputHidden) : $('#uploadIpunt'),
            callback = this.arguments.callback ? this.arguments.callback : 0;

        //如果没有图片添加默认图片
        if ( $image.attr('src') == '' ) {
           // $image.attr('src', BASE_URL + '/shop/static/common/images/image.png');
        }
        uploader = WebUploader.create({

            auto: true,

            pick: uploadButton,

            accept: {
                title: 'Video',
                extensions: window.uploadConfig.videoAllowFiles.join(',').replace(/\./g, ''),
                mimeTypes: 'video/*'
            },

            swf: BASE_URL + '/shop/static/common/js/Uploader.swf',

            server: SITE_URL + "?ctl=Upload&action=" + window.uploadConfig.videoActionName,

            fileVal: window.uploadConfig.videoFieldName,

            duplicate: true,

            fileSingleSizeLimit:  window.uploadConfig.videoMaxSize
            // fileNumLimit: 1
        });

        uploader.on('error',function(handler){
            if(handler == 'F_EXCEED_SIZE'){
                Public.tips( {content: '视频超出30M,请上传合适的视频！', type: 1} );
            }
        });
        // 当有文件添加进来时执行，负责view的创建
        // 当有文件添加进来的时候
        // 只能上传一张图片
        uploader.on('fileQueued', function (file) {
        });
        // 文件上传过程中创建进度条实时显示。
        uploader.on('uploadProgress', function (file, percentage) {
             $(".up-progress-percent").css('width', percentage * 100 + '%');

        });
        uploader.on('uploadSuccess', function (file, response) {

            try
            {
                if (response.state == 'SUCCESS')
                {
                    $input.attr('value', response.url);
                    $image.attr('src', response.url);
                    if ( callback && typeof callback == 'function' ) {
                        callback(response);
                    }
                }
                else
                {
                    Public.tips( {content: response.state, type: 1} );
                }
            } catch (e)
            {
                Public.tips( {content: '服务器返回出错', type: 1} );
            }

        });

        uploader.on( 'uploadError', function( file ) {
            Public.tips({content: '上传失败', type: 1});
        });
    }
}

function bytes_to_size(bytes) {
    if (bytes === 0) return '0 B';
    var k = 1000, // or 1024
        sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
        i = Math.floor(Math.log(bytes) / Math.log(k));
    return (bytes / Math.pow(k, i)).toPrecision(3);
}
// });