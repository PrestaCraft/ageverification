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
