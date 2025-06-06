<?php

namespace app\controllers;

use Yii;
use app\models\Nominas;
use app\models\NominasSearch;
use app\models\DetallesNomina;
use app\models\Empleados;
use app\models\HistoricDetalles;
use app\models\HistoricDownloads;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * NominasController implements the CRUD actions for Nominas model.
 */
class NominasController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'revert' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Nominas models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new \app\models\HistoricDetallesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Nominas model.
     * @param int $nomina_id Nomina ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($nomina_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($nomina_id),
        ]);
    }

    /**
     * Creates a new Nominas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Nominas();
        $detalleNomina = new DetallesNomina();

        if ($this->request->isPost) {
            $detalleNomina->load($this->request->post());
            $excelFile = UploadedFile::getInstanceByName('excelFile');
            
            if ($excelFile) {
                try {
                    // Verificar integridad del archivo mediante el hash SHA-256
                    //$originalFileName = $excelFile->name;
                    //$result = $this->validateFileIntegrity($excelFile->tempName, $originalFileName);
                    
                    //if ($result !== true) {
                    //    Yii::$app->session->setFlash('error', $result);
                    //    return $this->render('create', [
                    //        'model' => $model,
                    //        'detalleNomina' => $detalleNomina
                    //    ]);
                    //}
                    
                    $transaction = Yii::$app->db->beginTransaction();
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFile->tempName);
                    $selectedPeriod = $detalleNomina->periodo;
                    $startSheetIndex = $selectedPeriod == '1' ? 0 : 1;
                    $mes = $detalleNomina->mes;
                    $anio = $detalleNomina->anio;
                    
                    // Verificar si hay detalles de nómina de hace 3 meses
                    $mes_anterior = $mes - 3;
                    $anio_anterior = $anio;
                    
                    if ($mes_anterior <= 0) {
                        $mes_anterior += 12;
                        $anio_anterior -= 1;
                    }
                    
                    $detalles_anteriores = DetallesNomina::find()
                        ->where(['mes' => $mes_anterior, 'anio' => $anio_anterior])
                        ->exists();
                    
                    if ($detalles_anteriores) {
                        DetallesNomina::deleteAll(['mes' => $mes_anterior, 'anio' => $anio_anterior]);
                        Yii::info("Se han eliminado los detalles de nómina del mes $mes_anterior/$anio_anterior");
                    }

                    $nominaTypes = [
                        'ALTO_NIVEL', 'EMPLEADO_FIJO', 'OBRERO_FIJO', 
                        'CONTRATADOS', 'PENSIONADOS', 'JUBILADOS', 'OBREROS_CONTRATADOS', 'ENCARGADOS'
                    ];
                    $sheetCount = $spreadsheet->getSheetCount();

                    for ($sheetIndex = $startSheetIndex; $sheetIndex < $sheetCount; $sheetIndex += 2) {
                        $sheet = $spreadsheet->getSheet($sheetIndex);
                        
                        if ($sheet->getTitle() === 'MENU') {
                            continue;
                        }
                        
                        $pairIndex = (int)($sheetIndex / 2);
                        
                        if (!isset($nominaTypes[$pairIndex])) {
                            throw new \Exception("Tipo de nómina no definido para el índice $pairIndex");
                        }
                        $nomina = $nominaTypes[$pairIndex];

                        $model = $this->getOrCreatePayroll($nomina);

                        for ($row = 11; $row <= 1000; $row++) {
                            $ci = $this->getCellValue($sheet, 'C', $row);
                            $nombre = $this->getCellValue($sheet, 'B', $row);

                            if (empty($ci) || empty($nombre)) break;

                            $empleado = $this->processEmployee($ci, $nombre, $sheet, $row);
                            $this->processPayrollDetails($model, $empleado, $sheet, $row, $mes, $anio, $selectedPeriod);
                        }
                    }

                    $transaction->commit();

                    $historicDetalle = new HistoricDetalles([
                        'user_id' => Yii::$app->user->id,
                        'mes' => $mes,
                        'anio' => $anio,
                        'periodo' => $selectedPeriod,
                        'estado' => 1,
                    ]);
                    $historicDetalle->save(false);

                    Yii::$app->session->setFlash('success', 'La nómina ha sido procesada correctamente.');
                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    if (isset($transaction)) {
                        $transaction->rollBack();
                    }

                    $historicDetalle = new HistoricDetalles([
                        'user_id' => Yii::$app->user->id,
                        'mes' => $mes ?? null,
                        'anio' => $anio ?? null,
                        'periodo' => $selectedPeriod ?? null,
                        'estado' => 0,
                    ]);
                    $historicDetalle->save(false);

                    Yii::error("Error procesando nómina: {$e->getMessage()}\n{$e->getTraceAsString()}");
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            } else {
                Yii::$app->session->setFlash('error', 'Debe seleccionar un archivo.');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'detalleNomina' => $detalleNomina
        ]);
    }

    /**
     * Valida la integridad del archivo mediante el hash SHA-256
     */
    private function validateFileIntegrity($tempFilePath, $originalFileName)
    {
        try {
            if (!preg_match('/^(\d+)-SISCEP_FORMATO_/', $originalFileName, $matches)) {
                return "El formato del nombre del archivo no es válido. No se puede procesar el archivo.";
            }
            
            $downloadId = $matches[1];
            $historicDownload = HistoricDownloads::findOne($downloadId);
            
            if (!$historicDownload) {
                return "El archivo no es válido. No se puede procesar el archivo.";
            }
            
            $expectedHash = $historicDownload->checksum;
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFilePath);
            $fileHash = $spreadsheet->getProperties()->getCustomPropertyValue('SISCEP_HASH');
            
            $menuSheet = $spreadsheet->getSheetByName('MENU');
            $menuHash = null;
            if ($menuSheet) {
                $menuHash = $menuSheet->getCell('E12')->getValue();
            }
            
            if (!$fileHash && !$menuHash) {
                return "La integridad del archivo no es válida. No se puede procesar el archivo.";
            }
            
            if (($fileHash && $fileHash !== $expectedHash) || ($menuHash && $menuHash !== $expectedHash)) {
                return "La integridad del archivo no es válida. No se puede procesar el archivo.";
            }
            
            Yii::info("Verificación de integridad exitosa para el archivo con ID: $downloadId");
            return true;
            
        } catch (\Exception $e) {
            Yii::error("Error en la validación de integridad: " . $e->getMessage());
            return "Error en la validación de integridad del archivo: " . $e->getMessage();
        }
    }

    /**
     * Obtiene o crea una nómina por tipo
     */
    private function getOrCreatePayroll($nomina)
    {
        $model = Nominas::findOne(['nomina' => $nomina]);
        
        if (!$model) {
            $model = new Nominas(['nomina' => $nomina]);
            $model->save(false);
        }
        
        return $model;
    }

    /**
     * Procesa los datos de un empleado
     */
    private function processEmployee($ci, $nombre, $sheet, $row)
    {
        $ci = $this->cleanCi($ci);
        $fecha_ingreso = $this->parseFecha($sheet->getCell('D' . $row)->getValue());

        $empleado = Empleados::findOne(['ci' => $ci]);
        
        if ($empleado) {
            $empleado->nombre = $nombre;
            $empleado->fecha_ingreso = $fecha_ingreso;
        } else {
            $empleado = new Empleados([
                'ci' => $ci,
                'nombre' => $nombre,
                'fecha_ingreso' => $fecha_ingreso,
            ]);
        }
        
        $empleado->save(false);
        return $empleado;
    }

    /**
     * Procesa los detalles de nómina para un empleado
     */
    private function processPayrollDetails($model, $empleado, $sheet, $row, $mes, $anio, $periodo)
    {
        $columns = [
            'cargo' => 'E',
            'tipo_cargo' => 'F',
            'sueldo_quinc' => 'H',
            'prima_hijos' => 'I',
            'prima_prof' => 'J',
            'prima_antig' => 'K',
            'total_a' => 'L',
            'ivss' => 'M',
            'pie' => 'N',
            'faov' => 'O',
            'tesoreria_ss' => 'P',
            'caja_ahorro' => 'Q',
            'aporte_suep' => 'R',
            'total_d' => 'S',
            'cesta_tickets' => 'T',
            'bono_vac' => 'U',
            'montopagar' => 'V'
        ];

        $attributes = [];
        foreach ($columns as $attr => $col) {
            $value = $this->getCellValue($sheet, $col, $row);
            $attributes[$attr] = in_array($attr, ['sueldo_quinc', 'prima_hijos', 'prima_prof', 
                'prima_antig', 'ivss', 'pie', 'faov', 'tesoreria_ss', 'caja_ahorro', 
                'aporte_suep', 'cesta_tickets', 'bono_vac', 'total_a', 'total_d', 'montopagar'])
                ? $this->formatDecimal($value)
                : $value;
        }

        $detalleNomina = new DetallesNomina(array_merge($attributes, [
            'nomina_id' => $model->nomina_id,
            'empleado_id' => $empleado->empleado_id,
            'mes' => $mes,
            'anio' => $anio,
            'periodo' => $periodo
        ]));

        $detalleNomina->save(false);
    }

    /**
     * Limpia el número de CI eliminando caracteres no deseados
     */
    private function cleanCi($ci)
    {
        return str_replace([',', '.', ' '], '', $ci);
    }

    /**
     * Convierte un valor de fecha de Excel a formato Y-m-d
     */
    private function parseFecha($value)
    {
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        return date('Y-m-d', strtotime($value));
    }

    /**
     * Formatea un valor decimal
     */
    private function formatDecimal($value)
    {
        $value = str_replace([' ', ','], ['', '.'], trim($value));
        
        if (substr_count($value, '.') > 1) {
            $value = preg_replace('/\.(?=.*\.)/', '', $value);
        }
        
        return is_numeric($value) ? (float)$value : 0.0;
    }

    /**
     * Obtiene el valor formateado de una celda
     */
    private function getCellValue($sheet, $column, $row)
    {
        return trim($sheet->getCell($column . $row)->getFormattedValue());
    }

    /**
     * Updates an existing Nominas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $nomina_id Nomina ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($nomina_id)
    {
        $model = $this->findModel($nomina_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'nomina_id' => $model->nomina_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Nominas model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $nomina_id Nomina ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($nomina_id)
    {
        $this->findModel($nomina_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Nominas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $nomina_id Nomina ID
     * @return Nominas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($nomina_id)
    {
        if (($model = Nominas::findOne(['nomina_id' => $nomina_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Genera y devuelve el archivo de formato de nómina con un hash de integridad
     * 
     * @return \yii\web\Response Respuesta con el archivo para descargar
     */
    public function actionDownload()
    {
        $originalFile = \Yii::getAlias('@webroot/uploads/FORMATO_NOMINA.xlsm');
    
        if (!file_exists($originalFile)) {
            \Yii::$app->session->setFlash('error', 'El archivo que intenta descargar no se encuentra disponible.');
            return $this->redirect(['index']);
        }
    
        // Array con los meses en español
        $meses = [
            '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL',
            '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO',
            '09' => 'SEPTIEMBRE', '10' => 'OCTUBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE'
        ];
    
        // Obtener el mes actual en formato numérico
        $mesActual = date('m');
        $nombreMes = $meses[$mesActual];
        
        try {
            // Cargar el archivo Excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($originalFile);
            
            // Generar un hash SHA-256 del contenido original
            $hashOriginal = hash_file('sha256', $originalFile);
            
            // Guardar el hash como una propiedad del documento
            $spreadsheet->getProperties()
                ->setCustomProperty('SISCEP_HASH', $hashOriginal);
                
            // Escribir el hash en la hoja "MENU" en la celda E12
            try {
                $worksheet = $spreadsheet->getSheetByName('MENU');
                if ($worksheet) {
                    $worksheet->setCellValue('E12', $hashOriginal);
                } else {
                    \Yii::warning('No se encontró la hoja "MENU" en el archivo Excel');
                }
            } catch (\Exception $e) {
                \Yii::error('Error al escribir el hash en la hoja MENU: ' . $e->getMessage());
            }
                
            // Crear un registro en historic_downloads
            $historicDownload = new \app\models\HistoricDownloads();
            $historicDownload->file_generado = "SISCEP_FORMATO_{$nombreMes}.xlsm";
            $historicDownload->checksum = $hashOriginal;
            $historicDownload->user_id = \Yii::$app->user->id;
            $historicDownload->fecha_hora = date('Y-m-d H:i:s');
            
            if (!$historicDownload->save()) {
                \Yii::error('Error al guardar el histórico de descargas: ' . json_encode($historicDownload->errors));
                \Yii::$app->session->setFlash('error', 'Error al registrar la descarga.');
                return $this->redirect(['index']);
            }
            
            // Obtener el ID generado
            $downloadId = $historicDownload->id;
            
            // Nombre de salida con el formato requerido
            $outputFileName = "{$downloadId}-SISCEP_FORMATO_{$nombreMes}.xlsm";
            
            // Crear un archivo temporal para la salida
            $tempFile = \Yii::getAlias('@runtime/' . $outputFileName);
            
            // Guardar el archivo con el hash incrustado
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($tempFile);
            
            // Devolver el archivo como una descarga
            return \Yii::$app->response->sendFile($tempFile, $outputFileName, ['inline' => false]);
            
        } catch (\Exception $e) {
            \Yii::error('Error al procesar el archivo Excel: ' . $e->getMessage());
            \Yii::$app->session->setFlash('error', 'Error al procesar el archivo: ' . $e->getMessage());
            return $this->redirect(['index']);
        }
    }

    /**
     * Revierte una operación de nómina eliminando los registros correspondientes.
     * @param int $id ID del registro histórico
     * @return mixed
     * @throws NotFoundHttpException si el registro histórico no existe
     */
    public function actionRevert($id)
    {
        $historico = \app\models\HistoricDetalles::findOne($id);
        
        if ($historico === null) {
            throw new \yii\web\NotFoundHttpException('El registro histórico no fue encontrado.');
        }

        // Iniciar transacción
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Eliminar los registros de nómina correspondientes
            $deleted = \app\models\DetallesNomina::deleteAll([
                'mes' => $historico->mes,
                'anio' => $historico->anio,
                'periodo' => $historico->periodo
            ]);

            // Actualizar el estado del histórico a revertido (2)
            $historico->estado = 2;
            if (!$historico->save()) {
                throw new \Exception('No se pudo actualizar el estado del histórico.');
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'La operación ha sido revertida exitosamente.');

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Error al revertir la operación: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }
}
