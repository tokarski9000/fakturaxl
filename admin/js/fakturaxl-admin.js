(function ( $ ) {
    $(window).ready(
        function () {
            $('.faktura-xl-issue-invoice').click(
                function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const _this  = $(this);
                    const postID = _this.attr('post-id');
                    _this.after('<div class="issuing-msg lds-dual-ring"></div>');
                    var data = {
                        'action': 'issue_invoice',
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
                            $('.issuing-msg').remove();
                            $('.order_data_column .faktura-xl-issue-invoice').after(` < pre > ${res} < / pre > `);
                            _this.html('✓');
                        }
                    );
                }
            )
            $('.column-Faktura').click(
                function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            )
        }
    )
})(jQuery);
