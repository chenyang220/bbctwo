/**
 * Created by Administrator on 2016/5/23.
 */
function initField()
{
	$('#redpacket_t_start_date').datetimepicker({lang:'ch'}).prop('readonly', 'readnoly');
	$('#redpacket_t_end_date').datetimepicker({lang:'ch'}).prop('readonly', 'readnoly');
    var currDate = new Date();
    var date = currDate.getDate();
    date = date;
    currDate.setDate(date);
    $('#redpacket_t_start_date').datetimepicker({
        controlType: 'select',
        minDate:currDate,
    });

    $('#redpacket_t_end_date').datetimepicker({
        controlType: 'select',
        onShow:function( ct ){
            this.setOptions({
                minDate:($('#redpacket_t_start_date').val() && (new Date(Date.parse($('#redpacket_t_start_date').val().replace(/-/g, "/")))) > (new Date()))?(new Date(Date.parse($('#redpacket_t_start_date').val().replace(/-/g, "/")))):(new Date())
            })
        }
    });
}

function initPopBtns()
{
    var t = "add" == oper ? ["提交", "关闭"] : ["确定", "取消"];
    postData(oper, rowData.redpacket_t_id);
    api.button({
        id: "confirm", name: t[0], focus: !0, callback: function ()
        {
            return cancleGridEdit(),$("#manage-form").trigger("validate"), !1;
        }
    }, {id: "cancel", name: t[1]})
}
function postData(t, e)
{
    $_form.validator({
        ignore: "hidden",
        theme: "yellow_bottom",
        timely: 1,
        stopOnError: true,
        debug:true,
        rules: {
            greaterThanPrice: function(element) {
                // 返回true，则验证必填
                return (parseInt(element.value) > parseInt($("#redpacket_t_price").val()))?true:false;
            },

            checkTime:function (element) {
                var redpacket_t_start_date          = $("#redpacket_t_start_date").val();
                var redpacket_t_end_date            = $("#redpacket_t_end_date").val();
                var datetime1 = Date.parse(redpacket_t_start_date.replace(/-/g, "/"));
                var datetime2 = Date.parse(redpacket_t_end_date.replace(/-/g, "/"));
                var DateTime = new Date();
                var now_time = DateTime.getTime();
                if(datetime2 < now_time)
                {
                    return false;
                }
                if(redpacket_t_start_date!=""&&redpacket_t_end_date!=""&&datetime1 >=datetime2)
                {
                    return false;
                }
            },
            changeImg:function(element)
            {
                if(!element.value){
                    return '请上传图片';
                }
            }
        },
        messages: {
            required: "",
            greaterThanPrice:"使用限额必须大于面额",
            checkTime:"开始时间不能比结束时间大"
        },
        fields: {
            redpacket_t_title: "required;length[~30]",               //红包名称
            redpacket_t_type:"required;range[1~2];integer[+];",     //红包类型
            redpacket_t_start_date: "required",                      //开始时间
            redpacket_t_end_date: "required;checkTime",                        //结束时间
            redpacket_t_price: "required;integer[+];",               //红包面额
            redpacket_t_total: "required;integer[+0];",              //可发放总数
            // redpacket_t_eachlimit: "required;integer[+0];",         //每人限领
            redpacket_t_user_grade_limit: "required;integer[+0];",  //用户等级限制
            redpacket_t_orderlimit: "required;integer[+];greaterThanPrice",         //消费限额
            // redpacket_t_desc: "required;length[~200];",              //红包描述
            redpacket_t_img: "required;changeImg;"//红包图片
        },
        display: function (a)
        {
            return $(a).closest(".row-item").find("label").text().replace("*","");
        },
        valid: function (form)
        {
            var redpacket_t_title               = $.trim($("#redpacket_t_title").val()),
                redpacket_t_type                = $("input[name='redpacket_t_type']:checked").val(),
                redpacket_t_start_date          = $.trim($("#redpacket_t_start_date").val()),
                redpacket_t_end_date            = $.trim($("#redpacket_t_end_date").val()),
                redpacket_t_price               = $.trim($("#redpacket_t_price").val()),
                redpacket_t_orderlimit          = $.trim($("#redpacket_t_orderlimit").val()),
                redpacket_t_total               = $.trim($("#redpacket_t_total").val()),
                redpacket_t_eachlimit           = $.trim($("#redpacket_t_eachlimit").val()),
                redpacket_t_user_grade_limit    = $.trim($("#redpacket_t_user_grade_limit").val()),
                redpacket_t_img                 = $.trim($("#redpacket_t_img").val()),
                redpacket_t_desc                = $.trim($("#redpacket_t_desc").val()),
                n = "add" == t ? "新增红包模板" : "编辑红包模板";

            params = {
                redpacket_t_title  : redpacket_t_title,
                redpacket_t_type   : redpacket_t_type,
                redpacket_t_start_date : redpacket_t_start_date,
                redpacket_t_end_date : redpacket_t_end_date,
                redpacket_t_price:redpacket_t_price,
                redpacket_t_total:redpacket_t_total,
                redpacket_t_eachlimit:redpacket_t_eachlimit,
                redpacket_t_user_grade_limit:redpacket_t_user_grade_limit,
                redpacket_t_orderlimit:redpacket_t_orderlimit,
                redpacket_t_img:redpacket_t_img,
                redpacket_t_desc:redpacket_t_desc
            };
            Public.ajaxPost( SITE_URL + "?ctl=Promotion_RedPacket&typ=json&met=addRedPacketTemp", params, function (e)
            {
                if (200 == e.status)
                {
                    parent.parent.Public.tips({content: n + "成功！"});
                    callback && "function" == typeof callback && callback(e.data, t, window)
                }
                else
                {
                    parent.parent.Public.tips({type: 1, content: n + "失败！" + e.msg})
                }
            })
        },
    });
}
function cancleGridEdit()
{
    null !== curRow && null !== curCol && ($grid.jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null)
}
function resetForm(t)
{
    $("#manage-form").validate().resetForm();
    $("#redpacket_t_title").val("");
    $("#redpacket_t_start_date").val("");
    $("#range_end").val("");
}
var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#manage-form"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
initPopBtns();
initField();
