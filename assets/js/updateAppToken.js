/*
  Call Shopz.io API and Update users' apps
  If the website owner created a demo app using our builder, Then we get his app token(random generated token on our server)
  and update 'wp_options'. So the mobile application can communicate with the website. 
*/
(function($){
    $(document).ready(() => {
        const APP_ID = 'EpxJ14t7s9aSJIturx1klEIz3H17wk7h';
        const SHOPZ_API = 'https://shopz-parse.dokku.shopz.io/parse/functions/getUserApps';
        // handle update apps button
        $('#shopz_update').click(() => {
            $('#shopz_update').addClass('button-disabled');
            // convert data to token
            const token = btoa(JSON.stringify({
                username: $('#shopz_username').val(),
                password: $('#shopz_password').val()
            }));

            // send request
            const request = $.ajax({
                method: 'POST',
                url: SHOPZ_API,
                data: {
                    token
                },
                beforeSend: (req) => {
                    req.setRequestHeader('X-Parse-Application-Id', APP_ID)
                }
            });

            // update app list
            request.then((data) => {
                const { apps } = data.result;
                $('#wc_gql_internal_key').html('');
                apps.forEach(app => {
                    $('#wc_gql_internal_key').append(`<option value=${app.internalKey}>${app.name}</option>`).trigger('change');
                });
            })
            .fail((xhr) => {
                console.log(xhr);
                const message = xhr.responseJSON.error.message || 'Failed to get data';
                alert(message);
            })
            .always(() => {
                $('#shopz_update').removeClass('button-disabled');
            });
        });

        // handle app list change
        $('#wc_gql_internal_key').on('change', () => {
            const data = {
                username: $('#shopz_username').val(),
                appName: $('#wc_gql_internal_key option:selected').text()
            }

            $('#wc_gql_shopz_app_data').val(JSON.stringify(data));
        });

        // handle show button
        $('#show_button').on('click', e => {
            $('.form-table tr.hidden').removeClass('hidden');
            $('#show_button').parent().addClass('hidden');
            e.preventDefault();
        });
    });
})(jQuery);