<?php

use yii\helpers\Html;
use portalium\menu\Module;
use portalium\theme\widgets\Panel;
use portalium\menu\models\MenuItem;
use portalium\theme\widgets\ActiveForm;
use portalium\theme\widgets\Tabs;

/* @var $this yii\web\View */
/* @var $model portalium\menu\models\MenuItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-item-form">
    <?php
    $isNewRecord = (isset($model->isNewRecord) && $model->isNewRecord == 1) ? 1 : 0;
    ?>
    <?php $form = ActiveForm::begin(
        [
            'id' => 'menu-item-form',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'wrapper' => 'col-sm-9',
                ],
                'labelOptions' => ['style' => 'margin-top: 10px;'],
            ],
        ]
    ); ?>

    <?php Panel::begin([
        'title' => (isset($model->isNewRecord) && $model->isNewRecord == 1) ? Module::t('Create Menu Item') : Module::t('Update Menu Item'),
        'actions' => [
            'header' => [],
            'footer' => [
                Html::button(Module::t('Save'), ['class' => 'btn btn-success create-menu-item', 'id' => 'create-menu-item']),
                Html::tag('div', '', ['class' => 'spinner-border text-primary', 'role' => 'status', 'style' => 'display:none; margin-left: 3px; margin-bottom: -10px;', 'id' => 'spinner'])
            ]
        ]
    ]) ?>
    <?php
        Tabs::begin([
            'items' => [
                [
                    'label' => Module::t('General'),
                    'content' => $this->render('_form-general', ['model' => $model, 'form' => $form, 'id_menu' => $id_menu]),
                    'active' => true,
                    'options' => ['style' => 'margin-top: 10px;']
                ],
                [
                    'label' => Module::t('Style'),
                    'content' => $this->render('_form-style', ['model' => $model, 'form' => $form, 'menuModel' => $menuModel]),
                    'options' => ['style' => 'margin-top: 10px;']

                ],
            ]
        ]);
    ?>
    <?php Tabs::end() ?>
    
    <?php Panel::end() ?>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
    /**
     * UI Visibility Manager:
     * Controls the display of form sections (Module, URL, Route) based on the selected menu type.
     */
    function toggleDivs() {
        var typeVal = $('#type').val();
        var routeTypeVal = $('#routeType-list').val();

        $('#module-list-div, #routeType-list-div, #route-list-div, #url-input-div, #model-list-div').hide();

        if (typeVal == " . MenuItem::TYPE['module'] . ") {
            $('#module-list-div').show();
            if ($('#module-list').val()) $('#routeType-list-div').show();
        } else if (typeVal == " . MenuItem::TYPE['route'] . " || typeVal == " . MenuItem::TYPE['url'] . ") {
            $('#url-input-div').show();
        }

        if (routeTypeVal == 'model') {
            $('#model-list-div, #route-list-div').show();
        } else if (routeTypeVal == 'widget' || routeTypeVal == 'action' || routeTypeVal == 'route') {
            $('#route-list-div').show();
        }
    }

    /**
     * Change Listeners:
     * Trigger visibility updates on manual user interactions.
     */
    $(document).on('change', '#type, #module-list, #routeType-list, #route-list', function() {
        toggleDivs();
    });

    /**
     * Async Initial Load:
     * Populates dependent dropdowns sequentially using async/await to ensure data integrity
     * without blocking the main UI thread during page load.
     */
    async function loadFormData() {
        var moduleName = '" . $model->module . "';
        var routeType = '" . $model->routeType . "';
        var route = '" . str_replace('\\', '\\\\', $model->route ? $model->route : '') . "';
        var modelName = '" . $model->model . "';

        if (!moduleName) {
            toggleDivs();
            $('#drop-menu-form').show();
            $('#spinner-div-form').hide();
            $('.edit-item, .clone-item, .delete-item, .create-item, .move-item, .dd-handle-button').attr('disabled', false);
            return;
        }

        disabledButton();
        toggleDivs();

        try {
            // Fetch dependent dropdown data sequentially using async/await to prevent race conditions.
            let types = await $.post('/menu/item/route-type', { moduleName: moduleName });
            fillSelect('#routeType-list', types, routeType);

            if (routeType) {
                let routes = await $.post('/menu/item/route', { moduleName: moduleName, type: routeType });
                fillSelect('#route-list', routes, route);
            }

            if (routeType === 'model' && route) {
                let models = await $.post('/menu/item/model', { moduleName: moduleName, type: routeType, route: route });
                fillSelect('#model-list', models, modelName);
            }
        } catch (err) {
            console.error('Initial load failed during async data fetching:', err);
        } finally {
            // Re-enable UI components and synchronize final visibility state.
            $('#drop-menu-form').show();
            $('#spinner-div-form').hide();
            $('.edit-item, .clone-item, .delete-item, .create-item, .move-item, .dd-handle-button').attr('disabled', false);
            toggleDivs();
        }
    }

    /**
     * Select Population Helper:
     * Utility function to clear, populate, and optionally select a value in a dropdown.
     */
    function fillSelect(selector, data, selected) {
        let \$el = $(selector);
        \$el.empty().append(new Option('" . Module::t('Select...') . "', ''));
        if (data && data.length > 0) {
            data.forEach(item => \$el.append(new Option(item.name, item.id)));
            \$el.prop('disabled', false);
        }
        if (selected) {
            \$el.val(selected);
        }
    }

    // Initialize the form data as soon as the view is rendered.
    loadFormData();

    /**
     * Form Submission Handler:
     * Manages AJAX submission and chains Pjax reloads to maintain nestable tree consistency.
     */
    $(document).off('click', '#create-menu-item').on('click', '#create-menu-item', function (e) {
        disabledButton();
        e.preventDefault();
        var form = $('#menu-item-form');
        var data = form.serialize() + '&output=' + $('#nestable-output').val();
        
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: data,
            beforeSend: function () { $('#spinner').show(); },
            success: function (response) {
                // Refresh both nestable Pjax containers and trigger expand-all for a consistent user experience.
                $.pjax.reload({ container: '#nestable-pjax' }).done(function () {
                    $.pjax.reload({ container: '#nestable2-pjax' }).done(function () {
                        $('#expand-all').trigger('click');
                        $('#spinner').hide();
                    });
                });
            }
        });
    });

    /**
     * UI Locker:
     * Disables interactive elements and shows spinners to prevent race conditions during AJAX operations.
     */
    function disabledButton() {
        $('#drop-menu-form').hide();
        $('#spinner-div-form').show();
        $('.edit-item, .clone-item, .delete-item, .create-item, .move-item, .dd-handle-button').attr('disabled', true);
    }
");
?>