jQuery(document).ready(function($) {
    console.log('WP Multilang Guttenberg');

    let $div = $('[wpm-guttenberg-copy]');
    id = $('#post_ID').val();
    current = $div.find('[name=wpm-g-current]').val();
    $select = $div.find('select');
    nonce = $div.find('[name=wpm_guttenberg_nonce]').val();

    $div.find('button').click(function(event) {

        console.log(ajaxurl);

        event.preventDefault();
        console.log('Copy to lang');

        if(current == null)
            return;
        if(id == null)
            return;
        if($select.val() == null)
            return;
        if(nonce == null)
            return;

        console.log($select.val());
        console.log(current);
        console.log(id);

        if(confirm('Realy want copy content?')) {
            let formData = new FormData();
            formData.append('action', 'wpm_guttenber_copy');
            formData.append('id', id);
            formData.append('from', current);
            formData.append('to', $select.val());
            formData.append('_ajax_nonce', nonce);

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                async: true,
                success: function (data) {
                    console.log(data);
                    if(data.result == 'ok') {

                    }
                },
                error: function(param1, param2, param3) {
                    console.log(param1);
                    console.log(param2);
                    console.log(param3);
                }
            });
        }
    })
});