var UzbekValidators = {
    phone: function (value) {
        return /^\+998\d{9}$/.test(value);
    },
    pinfl: function (value) {
        return /^\d{14}$/.test(value);
    },
    passport: function (series, number) {
        return /^[A-Z]{2}$/.test(series) && /^\d{7}$/.test(number);
    }
};

$(document).on('submit', 'form.secure-validation', function (e) {
    var form = $(this);
    var hasError = false;

    form.find('.v-phone').each(function () {
        if (!UzbekValidators.phone($(this).val())) {
            $(this).addClass('is-invalid');
            hasError = true;
        }
    });

    if (hasError) {
        e.preventDefault();
        alert('Iltimos, ma\'lumotlarni to\'g\'ri kiriting.');
    }
});
