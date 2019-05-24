<style>
    {if $ps_version != "1.6"}
        .remodal-av .dropdown-toggle:after {
            display: none;
        }
    {/if}

     .remodal-overlay {
         background: rgba({$color_r}, {$color_g}, {$color_b}, {$opacity}) !important;
     }

    .remodal-av {
        background: {$bg_color} !important;
    }

    {if $font_color}
        .remodal-av h3, .remodal-av p {
            color: {$font_color};
        }
    {/if}

     {if $selected_bg || $selected_color}
        .remodal-av .dropdown-menu > li > a:hover, .remodal-av .dropdown-menu > li > a:focus {
        {if $selected_color}
         color: {$selected_color} !important;
        {/if}
        {if $selected_bg}
         background-color: {$selected_bg} !important;
         {/if}
     }
     {/if}

    {if $header_font || $header_size || $header_size_mobile}
         .remodal-av h3 {
             {if $header_font}
             font-family: "{$header_font}" !important;
            {/if}
             {if $header_size}
             font-size: {$header_size}px;
             {/if}
         }

         {if $header_size_mobile}
            @media(max-width: 768px) {
                .remodal-av h3 {
                    font-size: {$header_size_mobile}px;
                }
            }
         {/if}
     {/if}

     {if $content_font || $content_size || $content_size_mobile}
         .remodal-av p {
             {if $content_font}
                 font-family: "{$content_font}" !important;
             {/if}
             {if $content_size}
                 font-size: {$content_size}px;
             {/if}
         }

         {if $content_size_mobile}
             @media(max-width: 768px) {
                 .remodal-av p {
                     font-size: {$content_size_mobile}px;
                 }
             }
         {/if}
     {/if}
 </style>

{if $ps_version != "1.6"}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js" type="text/javascript"></script>
{/if}

<script>
    var mode = "{$mode}";
    var inst;

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

    function avLiveValidation()
    {
        $('.av-select').on('change', function() {
            performValidation();
        });
    }

    function avClassicValidation()
    {
        $('.remodal-confirm').click(function() {
            performValidation();
        });
    }

    function performValidation()
    {
        // Current date
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();

        // Selected date
        var day = $(".av-day").find("option:selected").val();
        var month = $(".av-month").find("option:selected").val();
        var year = $(".av-year").find("option:selected").val();

        if(year < yyyy-{$age}) {
            // Allowed
            avAllow();
        } else {
            if(year == yyyy-{$age}) {
                // Check month and day
                if(month < mm) {
                    // Allowed
                    avAllow();
                } else {
                    if(month == mm) {
                        if(day <= dd) {
                            // Allowed
                            avAllow();
                        } else {
                            // Not allowed
                            avReject();
                        }
                    } else {
                        // Not allowed
                        avReject();
                    }
                }
            } else {
                // Not allowed
                avReject();
            }
        }
    }

    jQuery( document ).ready(function() {
        {if $mode == "classic"}
        avClassicValidation();
        {else}
        avLiveValidation();
        {/if}

        jQuery(".remodal-confirm").click(function () {
            if($(this).attr("data-remodal-action") == "confirm") {
                avAjax();
                inst.close();
            }
        });
    });

    {if $ps_version == "1.6"}
        $( document ).ready(function() {
            $('.remodal-av').remodal({ hashTracking: false });
            $(".selectpicker").selectpicker();
            inst = $.remodal.lookup[$('[data-remodal-id=modalav]').data('remodal')];
            inst.open();
        });
    {else}
        jQuery( document ).ready(function() {
            $('.remodal-av').remodal({ hashTracking: false });
            $(".selectpicker").selectpicker();
            inst = $.remodal.lookup[$('[data-remodal-id=modalav]').data('remodal')];
            inst.open();
        });
    {/if}
</script>

<div class="remodal remodal-av" data-remodal-id="modalav">

    {if $title}
        <h3>{$title}</h3>
    {else}
        <h3>{l s='Age verification' mod='ageverification'}</h3>
    {/if}
    <p>
        {if $text}
            {$text|nl2br}
        {else}
            {l s='If you want to use our webiste you have to be at least' mod='ageverification'}
            <strong>{$age}</strong> {l s='years old.' mod='ageverification'}
        {/if}
    </p>

    {if $type == "date"}
        <select class="selectpicker av-select av-day">
            {for $i=1; $i<=31; $i++}
                <option value="{$i}">{$i}</option>
            {/for}
        </select>
        <select class="selectpicker av-select av-month">
            <option value="1">{l s='January' mod='ageverification'}</option>
            <option value="2">{l s='February' mod='ageverification'}</option>
            <option value="3">{l s='March' mod='ageverification'}</option>
            <option value="4">{l s='April' mod='ageverification'}</option>
            <option value="5">{l s='May' mod='ageverification'}</option>
            <option value="6">{l s='June' mod='ageverification'}</option>
            <option value="7">{l s='July' mod='ageverification'}</option>
            <option value="8">{l s='August' mod='ageverification'}</option>
            <option value="9">{l s='September' mod='ageverification'}</option>
            <option value="10">{l s='October' mod='ageverification'}</option>
            <option value="11">{l s='November' mod='ageverification'}</option>
            <option value="12">{l s='December' mod='ageverification'}</option>
        </select>
        <select class="selectpicker av-select av-year">
            {for $i={'Y'|date}; $i>={{'Y'|date}-100}; $i--}
                <option value="{$i}">{$i}</option>
            {/for}
        </select>
    {/if}
    <br>

    {if $mode == "classic"}
        <p class="text-age" style="margin: 0 20px;display: none;color: red;margin-top:20px;">
            {if !$age_text}
                {l s='Your age is too low' mod='ageverification'}
            {else}
                {$age_text}
            {/if}
        </p>
    {/if}

    <button {if $type != "date"}style="margin-top: 0;"{/if}
            class="remodal-confirm dis"
            {if $type != "date"}data-remodal-action="confirm"{/if}>
        <span class="text-verified" style="display: none;margin: 0 20px;">
        {if !$button_text}
            {l s='OK' mod='ageverification'}
        {else}
            {$button_text}
        {/if}
        </span>

        <span class="text-unverified" style="margin: 0 20px;">

            {if $type == "accept"}
                {if !$button_text}
                    {l s='OK' mod='ageverification'}
                {else}
                    {$button_text}
                {/if}
            {else}
                {if $mode == "classic"}
                    {if !$button_text}
                        {l s='Confirm' mod='ageverification'}
                    {else}
                        {$button_text}
                    {/if}
                {else}
                    {if !$button_text}
                        {l s='Please select your birthdate' mod='ageverification'}
                    {else}
                        {$button_text}
                    {/if}
                {/if}

            {/if}
        </span>
        {if $mode == "classic"}
        <span class="text-age" style="margin: 0 20px;display: none;">
            {if !$button_text}
                {l s='Confirm' mod='ageverification'}
            {else}
                {$button_text}
            {/if}
        </span>
        {else}
        <span class="text-age" style="margin: 0 20px;display: none;">
             {if !$age_text}
                 {l s='Your age is too low' mod='ageverification'}
             {else}
                 {$age_text}
             {/if}
        </span>
        {/if}
    </button>

</div>
