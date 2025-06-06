<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\DetallesNomina;
use yii\bootstrap5\Modal;

/** @var yii\web\View $this */
/** @var app\models\Nominas $model */
/** @var yii\widgets\ActiveForm $form */

// Registrar SweetAlert2
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);

$detalleNomina = new DetallesNomina();

$meses = [
    1 => 'ENERO',
    2 => 'FEBRERO',
    3 => 'MARZO',
    4 => 'ABRIL',
    5 => 'MAYO',
    6 => 'JUNIO',
    7 => 'JULIO',
    8 => 'AGOSTO',
    9 => 'SEPTIEMBRE',
    10 => 'OCTUBRE',
    11 => 'NOVIEMBRE',
    12 => 'DICIEMBRE'
];

$años = array_combine(range(2025, 2050), range(2025, 2050));

$this->registerCss("
    .nominas-form {
        width: 100%;
        padding: 20px;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .custom-file-upload {
        border: 2px dashed var(--bs-border-color);
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        background: var(--bs-body-bg);
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 20px;
        width: 100%;
    }
    .custom-file-upload:hover {
        border-color: var(--bs-primary);
        background: var(--bs-primary-bg-subtle);
    }
    .custom-file-upload i {
        font-size: 2em;
        color: var(--bs-primary);
        margin-bottom: 10px;
    }
    .file-upload-info {
        margin-top: 10px;
        font-size: 0.9em;
        color: var(--bs-body-color);
    }
    .selected-file {
        margin-top: 10px;
        font-weight: bold;
        color: var(--bs-success);
    }
    body.dark-mode .custom-file-upload,
    html.dark-mode .custom-file-upload {
        background: var(--bs-dark);
        border-color: var(--bs-border-color);
    }
    body.dark-mode .custom-file-upload:hover,
    html.dark-mode .custom-file-upload:hover {
        background: var(--bs-dark-bg-subtle);
        border-color: var(--bs-primary);
    }
    body.dark-mode .file-upload-info,
    html.dark-mode .file-upload-info {
        color: var(--bs-body-color);
    }
    .form-control, .form-select {
        width: 100%;
    }
    .file-upload-container {
        position: relative;
        margin-bottom: 1rem;
    }
    .file-upload-input {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
        z-index: 2;
    }
    .file-upload-label {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    .file-upload-label:hover {
        border-color: #0d6efd;
        background-color: #e9ecef;
    }
    .file-upload-icon {
        font-size: 2rem;
        color: #0d6efd;
        margin-right: 1rem;
    }
    .file-upload-text {
        font-size: 1.1rem;
        color: #495057;
    }
    .file-info {
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: #6c757d;
    }
    .selected-file {
        margin-top: 0.5rem;
        padding: 0.5rem;
        background-color: #e9ecef;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        color: #198754;
    }
    body.dark-mode .file-upload-label {
        background-color: #212529;
        border-color: #495057;
    }
    body.dark-mode .file-upload-label:hover {
        background-color: #343a40;
        border-color: #0d6efd;
    }
    body.dark-mode .file-upload-text {
        color: #e9ecef;
    }
    body.dark-mode .file-info {
        color: #adb5bd;
    }
    body.dark-mode .selected-file {
        background-color: #343a40;
        color: #75b798;
    }
    .modal-confirmation {
        display: none;
    }
    .confirmation-checkbox {
        margin: 1rem 0;
    }
    .confirmation-checkbox label {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        cursor: pointer;
    }
    .confirmation-checkbox input[type='checkbox'] {
        margin-top: 0.25rem;
    }
    .file-details {
        margin-bottom: 1rem;
    }
    .file-details p {
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
    }
    .file-details strong {
        color: #495057;
    }
    body.dark-mode .file-details strong {
        color: #e9ecef;
    }
    .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        background-color: #f8f9fa;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .modal-title {
        font-weight: 600;
        color: #212529;
    }
    .modal-body {
        padding: 1.5rem;
    }
    .file-details {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #0d6efd;
    }
    .file-details p {
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        border-radius: 0.25rem;
        transition: background-color 0.3s ease;
    }
    .file-details p:hover {
        background-color: rgba(13, 109, 253, 0.1);
    }
    .file-details strong {
        color: #495057;
        font-weight: 600;
    }
    .file-details span {
        color: #6c757d;
        font-weight: 500;
    }
    .confirmation-checkbox {
        margin: 1.5rem 0;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        border-left: 4px solid #198754;
    }
    .confirmation-checkbox label {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
        color: #495057;
        font-weight: 500;
    }
    .confirmation-checkbox input[type='checkbox'] {
        margin-top: 0.25rem;
        width: 1.25rem;
        height: 1.25rem;
    }
    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        padding: 1rem 1.5rem;
    }
    .modal-footer .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        border-radius: 0.375rem;
    }
    .modal-footer .btn-success {
        background-color: #198754;
        border-color: #198754;
    }
    .modal-footer .btn-success:hover {
        background-color: #157347;
        border-color: #146c43;
    }
    .modal-footer .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .modal-footer .btn-secondary:hover {
        background-color: #5c636a;
        border-color: #565e64;
    }
    body.dark-mode .modal-content {
        background-color: #212529;
        border-color: #495057;
    }
    body.dark-mode .modal-header {
        background-color: #343a40;
        border-bottom-color: #495057;
    }
    body.dark-mode .modal-title {
        color: #e9ecef;
    }
    body.dark-mode .file-details,
    body.dark-mode .confirmation-checkbox {
        background-color: #343a40;
    }
    body.dark-mode .file-details p:hover {
        background-color: rgba(13, 109, 253, 0.2);
    }
    body.dark-mode .file-details strong {
        color: #e9ecef;
    }
    body.dark-mode .file-details span {
        color: #adb5bd;
    }
    body.dark-mode .confirmation-checkbox label {
        color: #e9ecef;
    }
    body.dark-mode .modal-footer {
        border-top-color: #495057;
    }
");
?>

<div class="nominas-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class' => 'w-100']]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($detalleNomina, 'periodo')->dropDownList([
                1 => 'PRIMERA QUINCENA',
                2 => 'SEGUNDA QUINCENA'
            ])->label('Periodo:') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($detalleNomina, 'mes')->dropDownList($meses, ['prompt' => 'Seleccione un mes...'])->label('Mes:') ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($detalleNomina, 'anio')->dropDownList($años, ['prompt' => 'Seleccione un año...'])->label('Año:') ?>
        </div>
    </div>

    <div class="form-group">
        <div class="file-upload-container">
            <label class="file-upload-label">
                <i class="fas fa-file-excel file-upload-icon"></i>
                <span class="file-upload-text">Arrastra tu archivo Excel aquí o haz clic para seleccionar</span>
                <input type="file" class="file-upload-input" name="excelFile" id="excelFile" accept=".xls,.xlsm">
            </label>
            <div class="file-info">Formatos aceptados: .xls, .xlsm</div>
            <div class="selected-file" id="selectedFileName"></div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::button(Yii::t('app', 'Guardar'), ['class' => 'btn btn-success w-100', 'id' => 'btnShowModal']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
Modal::begin([
    'id' => 'confirmationModal',
    'title' => '<i class="fas fa-file-excel me-2"></i>Confirmar Carga de Nómina',
    'size' => 'modal-lg',
    'options' => [
        'tabindex' => false,
        'class' => 'fade',
    ],
    'headerOptions' => [
        'class' => 'border-bottom-0',
    ],
    'footer' => '
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Cancelar
        </button>
        <button type="button" class="btn btn-success" id="btnConfirmSave" disabled>
            <i class="fas fa-check me-2"></i>Guardar
        </button>
    ',
]);
?>

<div class="file-details">
    <p><strong>Mes:</strong> <span id="modalMes"></span></p>
    <p><strong>Año:</strong> <span id="modalAnio"></span></p>
    <p><strong>Periodo:</strong> <span id="modalPeriodo"></span></p>
    <p><strong>Nombre del archivo:</strong> <span id="modalFileName"></span></p>
    <p><strong>Tipo de archivo:</strong> <span id="modalFileType"></span></p>
    <p><strong>Peso del archivo:</strong> <span id="modalFileSize"></span></p>
</div>
<div class="confirmation-checkbox">
    <label>
        <input type="checkbox" id="confirmationCheckbox">
        Entiendo que los datos suministrados aquí son fidedignos y corresponden al correspondiente período de quincena.
    </label>
</div>

<?php Modal::end(); ?>

<?php
$this->registerJs("
    document.getElementById('excelFile').addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : '';
        var selectedFile = document.getElementById('selectedFileName');
        if (fileName) {
            selectedFile.innerHTML = '<i class=\"fas fa-check-circle me-2\"></i>Archivo seleccionado: ' + fileName;
            selectedFile.style.display = 'block';
        } else {
            selectedFile.style.display = 'none';
        }
    });

    // Prevenir el comportamiento predeterminado de arrastrar y soltar
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        document.querySelector('.file-upload-label').addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Función para mostrar alertas con SweetAlert2
    function showAlert(icon, title, text) {
        return Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Aceptar',
            customClass: {
                popup: 'animated fadeInDown'
            }
        });
    }

    // Manejo del modal de confirmación
    $('#btnShowModal').on('click', function() {
        var file = document.getElementById('excelFile').files[0];
        if (!file) {
            showAlert('warning', '¡Atención!', 'Por favor, seleccione un archivo primero.');
            return;
        }

        var periodo = $('select[name=\"DetallesNomina[periodo]\"]').val();
        var mes = $('select[name=\"DetallesNomina[mes]\"]').val();
        var anio = $('select[name=\"DetallesNomina[anio]\"]').val();

        if (!periodo || !mes || !anio) {
            showAlert('warning', '¡Atención!', 'Por favor, complete todos los campos del formulario.');
            return;
        }

        // Actualizar información en el modal
        $('#modalMes').text($('select[name=\"DetallesNomina[mes]\"] option:checked').text());
        $('#modalAnio').text(anio);
        $('#modalPeriodo').text(periodo === '1' ? 'PRIMERA QUINCENA' : 'SEGUNDA QUINCENA');
        $('#modalFileName').text(file.name);
        $('#modalFileType').text(file.type || 'application/vnd.ms-excel');
        $('#modalFileSize').text(formatFileSize(file.size));

        // Mostrar el modal usando Bootstrap 5
        var modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        modal.show();
    });

    // Manejar el checkbox de confirmación
    $('#confirmationCheckbox').on('change', function() {
        $('#btnConfirmSave').prop('disabled', !this.checked);
    });

    // Manejar el botón de guardar en el modal
    $('#btnConfirmSave').on('click', function() {
        Swal.fire({
            title: '¿Está seguro?',
            text: 'Se procederá a procesar la nómina con los datos proporcionados.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, procesar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'animated fadeInDown'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $('form').submit();
            }
        });
    });

    // Función para formatear el tamaño del archivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
");
?>
