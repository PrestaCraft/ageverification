function avAllow()
{
    $(".remodal-av .remodal-confirm").removeClass("dis-age dis");
    $(".text-unverified").hide();
    $(".text-age").hide();
    $(".text-verified").fadeIn();
    $(".remodal-confirm").attr("data-remodal-action", "confirm");
}

function avReject()
{
    if (mode != "classic")
        $(".remodal-av .remodal-confirm").removeClass("dis");

    if (mode != "classic")
        $(".remodal-av .remodal-confirm").addClass("dis-age");

    $(".text-verified").hide();
    $(".text-unverified").hide();
    $(".text-age").fadeIn();
    $(".remodal-confirm").attr("data-remodal-action", "");
}

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

$( document ).ready(function() {
    $(".selectpicker").selectpicker();
    var inst = $.remodal.lookup[$('[data-remodal-id=modalav]').data('remodal')];
    inst.open();
});