var api = frameElement.api;
var oper = api.data.oper;
var rowData = api.data.rowData || {};
var callback = api.data.callback;
initPopBtns();
initField();
initEvent();
console.log(rowData);
function initField() {
    if (rowData.id) {
        console.log(rowData);
        $('#title').val(rowData.title);
        $('#subtitle').val(rowData.subtitle);
        //$("textarea[name=article_desc]").val(rowData.article_desc);
        //$("#article_desc").append(rowData.article_desc);
        ue.ready(function () {
            ue.setContent(rowData.content);
        });
        
    }
}

function initEvent() {
    
    $("#type").data("defItem", ["id", rowData.type]);
    group = $("#type").combo({
        data: SITE_URL + "?ctl=Information_NewsClass&met=newsClassGroup&typ=json",
        value: "id",
        text: "newsclass_name",
        width: 130,
        ajaxOptions: {
            formatData: function (e) {
                console.log(e);
                return e.data;
            }
        },
        defaultSelected: rowData.type ? $("#type").data("defItem"):void 0
    }).getCombo();
}

function initPopBtns() {
    var operName = oper == "add" ? ["保存", "关闭"]:["确定", "取消"];
    api.button({
        id: 'confirm',
        name: operName[0],
        focus: true,
        callback: function () {
            postData(oper, rowData.id);
            return false;
        }
    }, {
        id: 'cancel',
        name: operName[1]
    });
}


function postData(oper, id) {
    
    
    var title = $.trim($('#title').val());
    var subtitle = $.trim($('#subtitle').val());
 
    var article_desc = $("textarea[name=content]").val();

    var newsclass_id = group.getValue();
    
    console.log(newsclass_id);
    
    if (!title) {
       return parent.Public.tips({type: 1,content:'请填写资讯标题名称！'});
    }
    if (!newsclass_id) {
       return parent.Public.tips({type: 1,content:'请选着资讯标签！'});
    }
    if (!article_desc) {
       return parent.Public.tips({type: 1,content:'请填写资讯内容！'});
    }
    var news_id = rowData.id;
    var msg = oper == 'add' ? '新增资讯文章':'编辑资讯文章';

    var params = {title: title, subtitle: subtitle, content: article_desc, newsclass_id: newsclass_id};
    Public.ajaxPost(SITE_URL + '?ctl=Information_News&typ=json&met=' + (oper == 'add' ? 'addInformationNews':'editInformationNews&news_id=' + news_id) + '&newsclass_id=' + newsclass_id+'&author_type=3', params, function (data) {
        if (data.status == 200) {
            rowData = data.data;
            rowData.operate = oper;
            parent.parent.Public.tips({content: msg + '成功！'});
            if (callback && typeof callback == 'function') {
                callback(rowData, oper, window);
            }
        } else {
            parent.parent.Public.tips({type: 1, content: msg + '失败！' + data.msg});
        }
    });
}

function resetForm(data) {
    $('#manage-form').validate().resetForm();
    $('#name').val('');
    $('#number').val(Public.getSuggestNum(data.locationNo)).focus().select();
}