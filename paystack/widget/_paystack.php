<script>
    var options = <?php echo json_encode($options)?>;

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

        console.log(options.callback);
        var handler = PaystackPop.setup(options);
        handler.openIframe();
    };
</script>