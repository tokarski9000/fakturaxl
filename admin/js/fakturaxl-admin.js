(function ( $ ) {
    const queryString = window.location.search;
    const urlParams   = new URLSearchParams(queryString);
    const postID      = urlParams.get('post')
    $(window).ready(
        function () {
            $('#wystaw-fakture').click(
                function () {
                    $('#wystaw-fakture').after('<p class="wystawiam-msg">wystawiam...</p>');
                    var data = {
                        'action': 'wystaw_fakture',
                        'postID': postID,
                        security: ajax_object.nonce
                    }
                    $.post(
                        ajax_object.ajax_url,
                        data,
                        function (response) {
                            const data = JSON.parse(response)
                            console.log(data);
                            const res  = JSON.stringify(data);
                            $('.wystawiam-msg').remove();
                            $('#wystaw-fakture').after(` < pre > ${res} < / pre > `);
                        }
                    );
                }
            )
        }
    )
})(jQuery);
