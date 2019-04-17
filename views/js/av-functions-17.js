function avAjax()
{
    $.ajax({
        url: prestashop["base_url"],
        type: 'post',
        data: {
            ajax       : true,
            module     : 'ageverification',
            fc         : 'module',
            controller : 'av',
            token      : prestashop["token"]
        },
        success: function (response) {
        },
        error: function (xhr, ajaxOptions, thrownError) {
        }
    });
}
