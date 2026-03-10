/**
 * University Admission System - Global UI Logic
 */

(function($) {
    "use strict";

    // 1. AJAX Loading Indicator
    $(document).on('ajaxStart', function() {
        $('#global-loader').css('display', 'flex');
    }).on('ajaxStop', function() {
        $('#global-loader').hide();
    });

    // 2. Form Submission Visuals
    $(document).on('submit', 'form', function() {
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        
        if ($btn.length && !$btn.hasClass('no-loader')) {
            $btn.prop('disabled', true);
            var originalText = $btn.html();
            $btn.data('original-text', originalText);
            $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + 
                     (Yii.t ? Yii.t('app', 'Yuklanmoqda...') : 'Yuklanmoqda...'));
        }
    });

    // 3. Handle Network Errors Globally
    $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
        if (jqXHR.status === 0) {
            alert("Internet aloqasi mavjud emas. Iltimos, qayta urinib ko'ring.");
        }
    });

})(jQuery);
