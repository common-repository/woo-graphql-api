(function($){
    $(document).ready(() => {
        $('input[type="text"]').on('keyup change paste', () => {
            const formObj = {
                username: $('input[type="text"]').val(),
                password: $('input[type="password"]').val()
            }
            $('input[type="hidden"]').val(btoa(JSON.stringify(formObj)));
        });

        $('input[type="password"]').on('keyup change paste', () => {
            const formObj = {
                username: $('input[type="text"]').val(),
                password: $('input[type="password"]').val()
            }
            $('input[type="hidden"]').val(btoa(JSON.stringify(formObj)));
        });
    });
})(jQuery);