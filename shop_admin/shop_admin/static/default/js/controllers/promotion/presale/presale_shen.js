$(function ()
{
    var t = "shen";
    if ($('#manage-form').length > 0)
    {
        $('#manage-form').validator({
            ignore: ':hidden',
            theme: 'yellow_right',
            timely: 1,
            stopOnError: true,
            valid: function (form)
            {
                parent.$.dialog.confirm('确认审核？', function ()
                    {
                        Public.ajaxPost(SITE_URL + '?ctl=Promotion_Presale&met=review&typ=json', $("#manage-form").serialize(), function (data)
                        {
                            if (data.status == 200)
                            {
                                parent.Public.tips({content: '审核成功'});
                                callback && "function" == typeof callback && callback(data.data, t, window)
                            }
                            else
                            {
                                parent.Public.tips({type: 1, content: data.msg || '审核失败'});
                            }
                        });
                    },
                    function ()
                    {
                    });
            }
        }).on("click", "a#submitBtn", function (e)
        {
            $(e.delegateTarget).trigger("validate");
        });
    }
});

var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#wechat-public-api-setting-form"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;