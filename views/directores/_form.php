<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Directores */
/* @var $form yii\widgets\ActiveForm */

$isNewRecord = $model->isNewRecord;

// Generar array de años desde 2000 hasta 2050
$years = ArrayHelper::map(range(2000, 2050), function($year) { return $year; }, function($year) { return $year; });
?>

<div class="container-form-wrapper">
    <div class="directores-form">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'directores-form']]); ?>

        <div class="form-phases-container">
            <?php if ($isNewRecord): ?>
                <!-- Alertas para validación -->
                <div class="alert alert-danger validation-alert" style="display:none;" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <span class="alert-message"></span>
                </div>
            
                <!-- Indicador de puntos para las fases -->
                <div class="progress-dots-container">
                    <div class="progress-dots">
                        <div class="dot active" data-step="1"></div>
                        <div class="dot" data-step="2"></div>
                        <div class="dot" data-step="3"></div>
                        <div class="dot" data-step="4"></div>
                    </div>
                </div>

                <!-- Fase 1: Datos personales (CREATE) -->
                <div class="form-phase active" id="phase-1">
                    <div class="phase-subtitle">Información Personal</div>
                    
                    <?= $form->field($model, 'nombre_director', [
                        'options' => ['class' => 'form-group required-field'],
                        'inputOptions' => ['class' => 'form-control', 'required' => true]
                    ])->textInput(['maxlength' => true])->label('<i class="fas fa-user"></i> Nombre del Director') ?>
                    
                    <div class="form-navigation">
                        <button type="button" class="btn btn-primary next-phase" data-next="phase-2" data-validate="phase-1">Siguiente</button>
                    </div>
                </div>

                <!-- Fase 2: Datos de resolución (CREATE) -->
                <div class="form-phase" id="phase-2" style="display:none;">
                    <div class="phase-subtitle">Información de Resolución</div>
                    
                    <div class="form-group field-directores-resolucion required-field">
                        <label class="control-label" for="directores-resolucion"><i class="fas fa-file-alt"></i> Resolución</label>
                        <div class="input-group mb-3 resolution-input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">PGEB-</span>
                            </div>
                            <?= Html::activeTextInput($model, 'resolucion', ['class' => 'form-control', 'id' => 'directores-resolucion', 'required' => true]) ?>
                            <div class="input-group-append">
                                <span class="input-group-text border-right-0">/</span>
                            </div>
                            <?= Html::dropDownList('resolucion_year', null, $years, [
                                'class' => 'form-control resolution-year',
                                'id' => 'resolucion-year',
                                'required' => true
                            ]) ?>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    
                    <?= $form->field($model, 'fecha_resolucion', [
                        'options' => ['class' => 'form-group required-field'],
                    ])->widget(DatePicker::className(), [
                        'language' => 'es',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options' => ['class' => 'form-control', 'required' => true],
                    ])->label('<i class="fas fa-calendar-alt"></i> Fecha de Resolución') ?>
                    
                    <div class="form-navigation">
                        <button type="button" class="btn btn-secondary prev-phase" data-prev="phase-1">Anterior</button>
                        <button type="button" class="btn btn-primary next-phase" data-next="phase-3" data-validate="phase-2">Siguiente</button>
                    </div>
                </div>

                <!-- Fase 3: Datos de gaceta (CREATE) -->
                <div class="form-phase" id="phase-3" style="display:none;">
                    <div class="phase-subtitle">Información de Gaceta</div>
                    
                    <div class="form-group field-directores-gaceta required-field">
                        <label class="control-label" for="directores-gaceta"><i class="fas fa-newspaper"></i> Gaceta</label>
                        <div class="input-group mb-3 gaceta-input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">N°</span>
                            </div>
                            <?= Html::activeTextInput($model, 'gaceta', ['class' => 'form-control', 'id' => 'directores-gaceta', 'required' => true]) ?>
                            <div class="input-group-append">
                                <span class="input-group-text border-right-0">/</span>
                            </div>
                            <?= Html::dropDownList('gaceta_year', null, $years, [
                                'class' => 'form-control gaceta-year',
                                'id' => 'gaceta-year',
                                'required' => true
                            ]) ?>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    
                    <?= $form->field($model, 'fecha_gaceta', [
                        'options' => ['class' => 'form-group required-field'],
                    ])->widget(DatePicker::className(), [
                        'language' => 'es',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options' => ['class' => 'form-control', 'required' => true],
                    ])->label('<i class="fas fa-calendar-day"></i> Fecha de Gaceta') ?>
                    
                    <div class="form-navigation">
                        <button type="button" class="btn btn-secondary prev-phase" data-prev="phase-2">Anterior</button>
                        <button type="button" class="btn btn-primary next-phase" data-next="phase-4" data-validate="phase-3">Siguiente</button>
                    </div>
                </div>

                <!-- Fase 4: Firma y estado (CREATE) -->
                <div class="form-phase" id="phase-4" style="display:none;">
                    <div class="phase-subtitle">Firma y Estado</div>
                    
                    <div class="form-group field-directores-firma required-field">
                        <label class="control-label" for="firma-upload"><i class="fas fa-signature"></i> Firma</label>
                        <input type="file" id="firma-upload" name="firma-upload" accept="image/*" class="form-control" required>
                        <div class="help-block">Seleccione una imagen para la firma del director</div>
                    </div>
                    
                    <?= $form->field($model, 'activo')->checkbox()->label('<i class="fas fa-toggle-on"></i>') ?>
                    
                    <div class="form-navigation">
                        <button type="button" class="btn btn-secondary prev-phase" data-prev="phase-3">Anterior</button>
                        <?= Html::submitButton(Yii::t('app', 'Guardar'), ['class' => 'btn btn-success', 'id' => 'submit-button', 'data-validate' => 'phase-4']) ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Formulario para UPDATE: Solo nombre, firma y checkbox -->
                <div class="form-phase active">
                    <div class="phase-subtitle">Actualizar Director</div>
                    
                    <?= $form->field($model, 'nombre_director')->textInput(['maxlength' => true])->label('<i class="fas fa-user"></i> Nombre del Director') ?>
                    
                    <div class="form-group field-directores-firma">
                        <label class="control-label" for="firma-upload"><i class="fas fa-signature"></i> Firma</label>
                        <input type="file" id="firma-upload" name="firma-upload" accept="image/*" class="form-control">
                        <div class="help-block">Seleccione una imagen para la firma del director</div>
                    </div>
                    
                    <?php if ($model->firma_base64): ?>
                    <div class="form-group">
                        <label><i class="fas fa-image"></i> Firma actual</label>
                        <div>
                            <img src="data:image/png;base64,<?= $model->firma_base64 ?>" alt="Firma del director" style="max-width: 300px; max-height: 100px;">
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?= $form->field($model, 'activo')->checkbox()->label('<i class="fas fa-toggle-on"></i>') ?>
                    
                    <div class="form-navigation">
                        <?= Html::submitButton(Yii::t('app', 'Actualizar'), ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<style>
.container-form-wrapper {
    width: 80%;
    margin-left: 10%;
    margin-right: 10%;
    margin-top: 20px;
}

.form-phases-container {
    position: relative;
    min-height: 300px;
}

/* Estilos para los dots de progreso */
.progress-dots-container {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
    padding: 10px 0;
}

.progress-dots {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 200px;
    position: relative;
}

.progress-dots:before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #e0e0e0;
    transform: translateY(-50%);
    z-index: 1;
    transition: background-color 0.3s ease;
}

html.dark-mode .progress-dots:before,
body.dark-mode .progress-dots:before {
    background-color: #495057;
}

.dot {
    width: 24px;
    height: 24px;
    background-color: #e0e0e0;
    border-radius: 50%;
    position: relative;
    z-index: 2;
    cursor: default;
    transition: all 0.3s ease;
    display: flex;
    justify-content: center;
    align-items: center;
}

.dot:after {
    content: attr(data-step);
    font-size: 12px;
    font-weight: 600;
    color: #757575;
    transition: color 0.3s ease;
}

.dot.active, .dot.completed {
    background-color: #337ab7;
}

.dot.active:after, .dot.completed:after {
    color: white;
}

html.dark-mode .dot,
body.dark-mode .dot {
    background-color: #495057;
}

html.dark-mode .dot:after,
body.dark-mode .dot:after {
    color: #bbb;
}

html.dark-mode .dot.active, 
html.dark-mode .dot.completed,
body.dark-mode .dot.active,
body.dark-mode .dot.completed {
    background-color: #8ab4f8;
    box-shadow: 0 0 0 2px rgba(138, 180, 248, 0.4);
}

html.dark-mode .dot.active:after, 
html.dark-mode .dot.completed:after,
body.dark-mode .dot.active:after,
body.dark-mode .dot.completed:after {
    color: #000;
    font-weight: 700;
    text-shadow: 0px 0px 1px rgba(255, 255, 255, 0.5);
}

.form-phase {
    transition: all 0.3s ease-in-out;
    position: relative;
    opacity: 1;
    transform: translateX(0);
}

.form-phase.slide-out-left {
    transform: translateX(-100%);
    opacity: 0;
}

.form-phase.slide-in-right {
    transform: translateX(100%);
    opacity: 0;
}

.form-phase.active {
    display: block;
    transform: translateX(0);
    opacity: 1;
    z-index: 10;
}

.form-navigation {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
}

.phase-subtitle {
    margin-bottom: 20px;
    color: #777;
    font-size: 16px;
    text-align: center;
    transition: color 0.3s ease;
}

html.dark-mode .phase-subtitle,
body.dark-mode .phase-subtitle {
    color: #bbb;
}

/* Estilo para los íconos en las etiquetas */
label i.fas {
    margin-right: 5px;
    width: 18px;
    text-align: center;
}

/* Ajustes para el input-group de resolución */
.resolution-input-group,
.gaceta-input-group {
    display: flex;
    flex-wrap: nowrap;
}

.resolution-input-group .input-group-prepend .input-group-text,
.gaceta-input-group .input-group-prepend .input-group-text {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.resolution-input-group .form-control:not(:last-child),
.gaceta-input-group .form-control:not(:last-child) {
    border-radius: 0;
    border-left: 0;
}

.resolution-year,
.gaceta-year {
    width: 100px !important;
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
    border-left: 0 !important;
}

.resolution-input-group .input-group-append .input-group-text,
.gaceta-input-group .input-group-append .input-group-text {
    border-radius: 0;
    border-left: 0;
    border-right: 0;
}

/* Adaptación para modo oscuro de los input-group */
html.dark-mode .input-group-text,
body.dark-mode .input-group-text {
    background-color: #495057;
    color: #e9ecef;
    border-color: #6c757d;
}

html.dark-mode .form-control,
body.dark-mode .form-control {
    background-color: #343a40;
    color: #e9ecef;
    border-color: #6c757d;
}

/* Estilos para la alerta de validación */
.validation-alert {
    margin-bottom: 20px;
    padding: 10px 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.validation-alert i {
    margin-right: 5px;
}

html.dark-mode .validation-alert,
body.dark-mode .validation-alert {
    background-color: rgba(220, 53, 69, 0.2);
    color: #ff6b6b;
    border-color: rgba(220, 53, 69, 0.5);
}

/* Estilo para campos requeridos */
.required-field label:after {
    content: " *";
    color: #dc3545;
}

html.dark-mode .required-field label:after,
body.dark-mode .required-field label:after {
    color: #ff6b6b;
}

/* Estilo para campos con error */
.field-error input, 
.field-error select,
.field-error .input-group {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.field-error label {
    color: #dc3545;
}

html.dark-mode .field-error label,
body.dark-mode .field-error label {
    color: #ff6b6b;
}
</style>

<?php
$script = <<<JS
    // Convertir archivo de firma a base64
    $('#firma-upload').change(function() {
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var base64 = e.target.result.split(',')[1];
                // Eliminar input anterior si existe
                $('input[name="Directores[firma_base64]"]').remove();
                // Añadir nuevo input
                $('<input>').attr({
                    type: 'hidden',
                    name: 'Directores[firma_base64]',
                    value: base64
                }).appendTo('#directores-form');
            }
            reader.readAsDataURL(input.files[0]);
        }
    });
    
    // Funciones de validación para cada fase
    function validatePhase(phaseId) {
        // Remover clases de error previas
        $('#' + phaseId).find('.field-error').removeClass('field-error');
        
        // Ocultar alerta previa
        $('.validation-alert').hide();
        
        var isValid = true;
        var errorFields = [];
        
        // Validar cada campo requerido en la fase actual
        $('#' + phaseId).find('[required]').each(function() {
            var field = $(this);
            var fieldName = field.closest('.form-group').find('label').text().replace('*', '').trim();
            
            // Quitar icono de la etiqueta para el mensaje de error
            fieldName = fieldName.replace(/^\\S+\\s/, '');
            
            if (!field.val()) {
                field.closest('.form-group').addClass('field-error');
                isValid = false;
                errorFields.push(fieldName);
            }
        });
        
        // Si hay errores, mostrar alerta
        if (!isValid) {
            var errorMessage = 'Por favor complete los siguientes campos: ' + errorFields.join(', ');
            showValidationAlert(errorMessage);
        }
        
        return isValid;
    }
    
    // Función para mostrar alerta de validación
    function showValidationAlert(message) {
        $('.validation-alert').find('.alert-message').text(message);
        $('.validation-alert').fadeIn(300);
        
        // Scroll hacia la alerta
        $('html, body').animate({
            scrollTop: $('.validation-alert').offset().top - 100
        }, 300);
    }
    
    // Manejar la concatenación de resolución y gaceta con año al enviar el formulario
    $('#directores-form').on('beforeSubmit', function() {
        // Validar la fase actual antes de enviar
        var currentPhaseId = $('.form-phase.active').attr('id');
        if (currentPhaseId && !validatePhase(currentPhaseId)) {
            return false;
        }
        
        // Procesar resolución
        var resolucion = $('#directores-resolucion').val();
        var resYear = $('#resolucion-year').val();
        
        if (resolucion && resYear) {
            $('#directores-resolucion').val(resolucion + '/' + resYear);
        }
        
        // Procesar gaceta
        var gaceta = $('#directores-gaceta').val();
        var gacYear = $('#gaceta-year').val();
        
        if (gaceta && gacYear) {
            $('#directores-gaceta').val(gaceta + '/' + gacYear);
        }
        
        return true;
    });
    
    // Navegación entre fases con actualización de dots
    $(document).ready(function() {
        // Parsear la resolución si ya existe
        var resolucionFull = $('#directores-resolucion').val();
        if (resolucionFull && resolucionFull.includes('/')) {
            var parts = resolucionFull.split('/');
            $('#directores-resolucion').val(parts[0]);
            $('#resolucion-year').val(parts[1]);
        }
        
        // Parsear la gaceta si ya existe
        var gacetaFull = $('#directores-gaceta').val();
        if (gacetaFull && gacetaFull.includes('/')) {
            var parts = gacetaFull.split('/');
            $('#directores-gaceta').val(parts[0]);
            $('#gaceta-year').val(parts[1]);
        }
        
        // Actualizar dots basado en la fase actual
        function updateDots(currentStep) {
            $('.dot').removeClass('active completed');
            
            // Marcar dots completados y activos
            for (var i = 1; i <= 4; i++) {
                if (i < currentStep) {
                    $('.dot[data-step="' + i + '"]').addClass('completed');
                } else if (i === currentStep) {
                    $('.dot[data-step="' + i + '"]').addClass('active');
                }
            }
        }
        
        // Botón Siguiente con validación
        $('.next-phase').click(function() {
            var currentPhaseId = $(this).closest('.form-phase').attr('id');
            var nextPhaseId = $(this).data('next');
            var phaseToValidate = $(this).data('validate');
            var nextStep = parseInt(nextPhaseId.split('-')[1]);
            
            // Validar la fase actual antes de avanzar
            if (phaseToValidate && !validatePhase(phaseToValidate)) {
                return false;
            }
            
            // Ocultar fase actual
            $('#' + currentPhaseId).removeClass('active').hide();
            
            // Mostrar siguiente fase
            $('#' + nextPhaseId).addClass('active').show();
            
            // Actualizar dots
            updateDots(nextStep);
        });
        
        // Botón Anterior
        $('.prev-phase').click(function() {
            var currentPhaseId = $(this).closest('.form-phase').attr('id');
            var prevPhaseId = $(this).data('prev');
            var prevStep = parseInt(prevPhaseId.split('-')[1]);
            
            // Ocultar alerta al retroceder
            $('.validation-alert').hide();
            
            // Ocultar fase actual
            $('#' + currentPhaseId).removeClass('active').hide();
            
            // Mostrar fase anterior
            $('#' + prevPhaseId).addClass('active').show();
            
            // Actualizar dots
            updateDots(prevStep);
        });
        
        // Validar antes de enviar
        $('#submit-button').click(function(e) {
            var phaseToValidate = $(this).data('validate');
            if (phaseToValidate && !validatePhase(phaseToValidate)) {
                e.preventDefault();
                return false;
            }
        });
        
        // Inicializar dots
        updateDots(1);
    });
JS;
$this->registerJs($script);
?>
