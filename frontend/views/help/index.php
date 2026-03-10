<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $groupedFaqs array */

$this->title = Yii::t('app', 'Yordam va ko\'p so\'raladigan savollar');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="help-index container my-5">
    
    <div class="text-center mb-5">
        <h1 class="display-6 fw-bold"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted"><?= Yii::t('app', 'Qabul jarayoni bo\'yicha barcha savollarga javoblar') ?></p>
        
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="faq-search" class="form-control border-start-0" placeholder="<?= Yii::t('app', 'Savolingizni kiriting...') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- AJAX Search Results Container -->
    <div id="search-results-container" class="d-none mb-5">
        <h4 class="mb-3"><i class="fas fa-search text-primary me-2"></i><?= Yii::t('app', 'Qidiruv natijalari') ?></h4>
        <div class="list-group shadow-sm" id="search-results-list">
            <!-- Results injected via JS -->
        </div>
    </div>

    <!-- Categorized FAQs -->
    <div id="faq-categories-container">
        <?php foreach ($groupedFaqs as $category => $faqs) : ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h4 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-folder-open me-2"></i> <?= Html::encode($category) ?>
                    </h4>
                </div>
                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="accordion-<?= md5($category) ?>">
                        <?php foreach ($faqs as $index => $faq) : ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-<?= $faq->id ?>">
                                    <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $faq->id ?>" aria-expanded="false" aria-controls="collapse-<?= $faq->id ?>">
                                        <?= Html::encode($faq->getQuestion()) ?>
                                    </button>
                                </h2>
                                <div id="collapse-<?= $faq->id ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $faq->id ?>" data-bs-parent="#accordion-<?= md5($category) ?>">
                                    <div class="accordion-body text-muted bg-light">
                                        <?= nl2br(Html::encode($faq->getAnswer())) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
$searchUrl = Url::to(['help/search']);
$noResultsText = Yii::t('app', 'Hech qanday natija topilmadi.');

$js = <<<JS
$(document).ready(function() {
    let searchTimeout;
    const searchInput = $('#faq-search');
    const resultsContainer = $('#search-results-container');
    const resultsList = $('#search-results-list');
    const categoriesContainer = $('#faq-categories-container');

    searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();

        if (query.length < 3) {
            resultsContainer.addClass('d-none');
            categoriesContainer.removeClass('d-none');
            return;
        }

        // Show global loader? Or just local loader. Let's do a local simple spinner
        let icon = searchInput.prev('.input-group-text').find('i');
        icon.removeClass('fa-search').addClass('fa-spinner fa-spin');

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '$searchUrl',
                data: { q: query },
                dataType: 'json',
                success: function(data) {
                    icon.removeClass('fa-spinner fa-spin').addClass('fa-search');
                    categoriesContainer.addClass('d-none');
                    resultsContainer.removeClass('d-none');
                    resultsList.empty();

                    if (data.length === 0) {
                        resultsList.append(
                            '<div class="list-group-item text-muted py-4 text-center">' + 
                            '<i class="fas fa-box-open fa-2x mb-2 text-light"></i><br>' + 
                            '$noResultsText</div>'
                        );
                    } else {
                        $.each(data, function(index, item) {
                            let excerpt = item.answer.length > 100 ? item.answer.substring(0, 100) + '...' : item.answer;
                            resultsList.append(
                                '<a href="#" class="list-group-item list-group-item-action py-3 search-result-item" data-id="' + item.id + '">' +
                                    '<div class="d-flex w-100 justify-content-between">' +
                                        '<h6 class="mb-1 text-primary">' + item.question + '</h6>' +
                                    '</div>' +
                                    '<p class="mb-1 text-muted small">' + excerpt + '</p>' +
                                '</a>'
                            );
                        });
                    }
                },
                error: function() {
                    icon.removeClass('fa-spinner fa-spin').addClass('fa-search');
                }
            });
        }, 500);
    });

    // Handle click on search result
    $(document).on('click', '.search-result-item', function(e) {
        e.preventDefault();
        // Clear search and open the actual accordion item
        searchInput.val('');
        resultsContainer.addClass('d-none');
        categoriesContainer.removeClass('d-none');
        
        let id = $(this).data('id');
        let targetCollapse = $('#collapse-' + id);
        
        if(targetCollapse.length) {
            // Scroll to it
            $('html, body').animate({
                scrollTop: targetCollapse.closest('.card').offset().top - 100
            }, 500);
            
            // Open it
            if(!targetCollapse.hasClass('show')) {
                targetCollapse.collapse('show');
            }
        }
    });
});
JS;
$this->registerJs($js);
?>
