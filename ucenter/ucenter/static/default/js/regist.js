$(function () {
    $(".clear-icon").click(function () {
        if($(this).next("input").val()){
            $(this).next("input").val('');
        }else{
            $(this).next('.intl-tel-input').find('input').val('');
        }

    });
    $(".eye-icon").click(function () {
        if ($(this).prev("input").attr('type') == 'password') {
            $(this).prev("input").attr('type', 'text');
            $(this).addClass('active');
        } else {
            $(this).prev("input").attr('type', 'password');
            $(this).removeClass('active');
        }
    });
});