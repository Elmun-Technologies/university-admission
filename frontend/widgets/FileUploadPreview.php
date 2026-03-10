<?php

namespace frontend\widgets;

use yii\bootstrap5\InputWidget;
use yii\helpers\Html;

/**
 * FileUploadPreview injects an Image Thumbnail instantly using JS FileReader mapped to a Native FileInput
 */
class FileUploadPreview extends InputWidget
{
    public $existingImageUrl = null;

    public function run()
    {
        $inputId = Html::getInputId($this->model, $this->attribute);
        $previewId = $inputId . '-preview';

        $html = '<div class="file-upload-wrapper text-center border rounded p-3 bg-light position-relative">';

        // Preview Container
        $html .= '<div id="' . $previewId . '" class="mb-3 d-flex justify-content-center align-items-center overflow-hidden bg-white shadow-sm mx-auto" style="width: 150px; height: 150px; border-radius: 10px; border: 2px dashed #ccc;">';
        if ($this->existingImageUrl) {
            $html .= '<img src="' . Html::encode($this->existingImageUrl) . '" style="width:100%; height:100%; object-fit:cover;">';
        } else {
            $html .= '<i class="bi bi-cloud-arrow-up text-muted display-4"></i>';
        }
        $html .= '</div>';

        // Actual Input
        $html .= Html::activeFileInput($this->model, $this->attribute, [
            'id' => $inputId,
            'class' => 'form-control',
            'accept' => 'image/jpeg, image/png'
        ]);

        $html .= '</div>';

        $js = <<<JS
        document.getElementById('{$inputId}').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    document.getElementById('{$previewId}').style.border = 'none';
                    document.getElementById('{$previewId}').innerHTML = '<img src="'+evt.target.result+'" style="width:100%; height:100%; object-fit:cover;">';
                }
                reader.readAsDataURL(file);
            }
        });
        JS;

        $this->getView()->registerJs($js);

        return $html;
    }
}
