(function ($) {
    'use strict';

    function renderError($container, message) {
        $container.removeClass('d-none alert-success').addClass('alert-danger').text(message);
    }

    function renderSuccess($container, message) {
        $container.removeClass('d-none alert-danger').addClass('alert-success').text(message);
    }

    $(document).on('submit', '#shorten-form', function (event) {
        event.preventDefault();

        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var $result = $('#shortener-result');
        var $error = $('#shortener-error');
        var $output = $('#shortener-output');
        var $shortLink = $('#short-link');
        var $qrCode = $('#qr-code-image');
        var urlValue = $.trim($('#url-input').val());
        var endpointUrl = $form.attr('action');

        $result.addClass('d-none').removeClass('alert-success alert-danger').empty();
        $error.addClass('d-none').removeClass('alert-success alert-danger').empty();
        $output.addClass('d-none');

        $button.prop('disabled', true);

        $.ajax({
            url: endpointUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                url: urlValue,
                _csrf: yii.getCsrfToken()
            }
        })
            .done(function (response) {
                if (!response || !response.success) {
                    renderError($error, response && response.message ? response.message : 'Ошибка при обработке запроса.');
                    return;
                }

                renderSuccess($result, response.message || 'Готово');
                $shortLink.attr('href', response.shortUrl).text(response.shortUrl);
                $qrCode.attr('src', response.qrCodeDataUri);
                $output.removeClass('d-none');
            })
            .fail(function () {
                renderError($error, 'Сервер временно недоступен. Повторите попытку позже.');
            })
            .always(function () {
                $button.prop('disabled', false);
            });
    });
})(jQuery);
