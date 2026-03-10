<?php

namespace frontend\widgets;

use yii\bootstrap5\InputWidget;
use yii\helpers\Html;

/**
 * Renders an advanced wrapper enforcing UZ Phone format +998 via Client-side masking simply
 */
class PhoneInput extends InputWidget
{
    public function run()
    {
        $options = $this->options;
        $options['class'] = isset($options['class']) ? $options['class'] . ' form-control phone-mask' : 'form-control phone-mask';
        $options['placeholder'] = '+998 (__) ___-__-__';
        $options['maxlength'] = 19; // Including formatting

        $input = $this->hasModel()
            ? Html::activeTextInput($this->model, $this->attribute, $options)
            : Html::textInput($this->name, $this->value, $options);

        // Native Vanilla Mask logic enforcing digits after +998
        $js = <<<JS
        document.querySelectorAll('.phone-mask').forEach(function(el) {
            el.addEventListener('input', function(e) {
                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
                
                // Force prefix safely without breaking backspace organically
                if(!x[1]) {
                    e.target.value = '+998 ';
                    return;
                }
                
                let prefix = '+998';
                if(x[1] === '998') {
                    // Extract rest formatting
                    let res = prefix + (x[2] ? ' ('+x[2] : '') + (x[3] ? ') '+x[3] : '') + (x[4] ? '-'+x[4] : '') + (x[5] ? '-'+x[5] : '');
                    e.target.value = res;
                } else if(e.target.value.startsWith('+998')) {
                    // It already has it but user is typing
                    let clean = e.target.value.substring(4).replace(/\D/g, '');
                    let matches = clean.match(/(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
                    if(matches) {
                       let res = prefix + (matches[1] ? ' ('+matches[1] : '') + (matches[2] ? ') '+matches[2] : '') + (matches[3] ? '-'+matches[3] : '') + (matches[4] ? '-'+matches[4] : '');
                       e.target.value = res;
                    }
                } else {
                    e.target.value = '+998 ';
                }
            });
        });
        JS;

        $this->getView()->registerJs($js);

        return $input;
    }
}
