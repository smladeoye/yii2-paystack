<script>
    var options = <?php echo json_encode($options)?>;
    var idOptions = <?php echo json_encode($idOptions)?>;
    var optionsClone = <?php echo json_encode($options)?>;

    function paystack_inline()
    {
        eval("var f = "+ options.callback) ;

        var defaultCallback = function(response){
            parent.window.location = options.callbackUrl + "?trx_ref=" + response.trxref;
        };

        var callback = options.callback;
        var callbackUrl = options.callbackUrl;

        if ((callback == '' || callback == undefined) && (callbackUrl != '' || callbackUrl != undefined))
        {
            options.callback = defaultCallback;
        }
        else
        {
            options.callback = f;
        }

        setIdOptionsValue();
        var handler = PaystackPop.setup(options);
        handler.openIframe();
    };

    function setIdOptionsValue()
    {
        for (x in idOptions)
        {
            var optionVal = idOptions[x]

            if (optionVal == 'amount')
                options[optionVal] = parseInt($(optionsClone[optionVal]).val()) * 100;
            else
                options[optionVal] = $(optionsClone[optionVal]).val();
        }
    }
</script>