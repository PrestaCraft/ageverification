function avAjax()
{
    $.ajax({
        url: baseDir,
        type: 'post',
        data: {
            ajax       : true,
            module     : 'ageverification',
            fc         : 'module',
            controller : 'av',
            token      : token
        },
        success: function (response) {
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(xhr.responseText);
            alert(thrownError);
        }
    });
}
