$(function ()
{
    var e;
    $("#header").on("click", ".header-inp", function ()
    {
        location.href = WapSiteUrl + "/tmpl/search.html"
    });

    $.getJSON(ApiUrl + "/index.php?ctl=Goods_Cat&met=cat1&typ=json",{cat_parent_id:"0"},function (t)
    {
        var a = template.render("category-list", t);
            console.log(t.data);
        $(".category-lists").html(a);
    });
});
