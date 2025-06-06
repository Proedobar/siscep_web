<?php

namespace app\controllers;

use app\models\DetallesNomina;
use app\models\DetallesNominaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\web\Response;
use Mpdf\Mpdf;
use app\models\Directores;
use app\models\Procuradores;

/**
 * DetallesNominaController implements the CRUD actions for DetallesNomina model.
 */
class DetallesNominaController extends Controller
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
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all DetallesNomina models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DetallesNominaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DetallesNomina model.
     * @param int $detail_id Detail ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($detail_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($detail_id),
        ]);
    }

    /**
     * Creates a new DetallesNomina model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new DetallesNomina();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'detail_id' => $model->detail_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DetallesNomina model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $detail_id Detail ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($detail_id)
    {
        $model = $this->findModel($detail_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'detail_id' => $model->detail_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DetallesNomina model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $detail_id Detail ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($detail_id)
    {
        $this->findModel($detail_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DetallesNomina model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $detail_id Detail ID
     * @return DetallesNomina the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($detail_id)
    {
        if (($model = DetallesNomina::findOne(['detail_id' => $detail_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Descarga el recibo de nómina en formato PDF
     * @param int $detail_id ID del detalle de nómina
     * @return mixed
     */
    public function actionDescargarRecibo($detail_id) {
        // Obtener detalle de nómina con relaciones
        $detalleNomina = DetallesNomina::find()
            ->with(['empleado', 'nomina'])
            ->where(['detail_id' => $detail_id])
            ->one();
    
        if (!$detalleNomina) {
            Yii::$app->session->setFlash('error', 'Detalles de nómina no encontrados.');
            return $this->redirect(['index']);
        }
    
        // Verificar si la nómina es de ALTO_NIVEL
        if ($detalleNomina->nomina && $detalleNomina->nomina->nomina === 'ALTO_NIVEL') {
            return $this->actionDescargarReciboAltoNivel($detail_id);
        }
    
        // Validar entidades relacionadas
        $empleado = $detalleNomina->empleado;
        if (!$empleado) {
            Yii::$app->session->setFlash('error', 'Empleado no encontrado.');
            return $this->redirect(['index']);
        }
    
        $director = Directores::find()->where(['activo' => true])->one();
        if (!$director) {
            Yii::$app->session->setFlash('error', 'No hay director activo para generar el recibo.');
            return $this->redirect(['index']);
        }

    
        // Configurar formateadores
        $formatter = Yii::$app->formatter;
        $meses = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
            5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
            9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];
    
        // Procesar datos comunes
        $nominaMap = [
            'ALTO_NIVEL' => 'ALTO NIVEL',
            'EMPLEADO_FIJO' => 'EMPLEADO FIJO',
            'OBRERO_FIJO' => 'OBRERO',
            'OBREROS_CONTRATADOS' => 'OBRERO',
            'CONTRATADOS' => 'CONTRATADO',
            'PENSIONADOS' => 'PENSIONADO',
            'JUBILADOS' => 'JUBILADO',
            'ENCARGADOS' => 'ALTO NIVEL (E)'
        ];
    
        $replacements = [
            '[EMPLEADO]' => $empleado->nombre,
            '[CI]' => $empleado->ci,
            '[FECHA_INGRESO]' => $formatter->asDate($empleado->fecha_ingreso, 'php:d/m/Y'),
            '[CARGO]' => $detalleNomina->cargo,
            '[NOMINA]' => $nominaMap[$detalleNomina->nomina->nomina ?? ''] ?? '',
            '[MES]' => $meses[$detalleNomina->mes] ?? '',
            '[AÑO]' => $detalleNomina->anio,
            '[Base64Firma]' => $director->firma_base64 ? '<img src="data:image/png;base64,'.$director->firma_base64.'" alt="Firma">' : '',
            '[NOMBRE DIRECTOR]' => $director->nombre_director,
            '[RESOLUCION]' => $director->resolucion,
            '[FECHA RESOLUCION]' => $formatter->asDate($director->fecha_resolucion, 'php:d/m/Y') ?: 'Fecha no disponible',
        ];
    
        // Procesar montos comunes (sin agregar 'VES' al final)
        $montos = [
            '[MONTO SUELDO]', '[MONTO PHIJOS]', '[MONTO PPROF]', '[MONTO PANTIG]',
            '[MONTO SSO]', '[MONTO TSS]', '[MONTO PIE]', '[MONTO FAOV]', 
            '[MONTO CAJA]', '[MONTO SUEP]', '[TOTAL_AS]', '[TOTAL_DED]', 
            '[MONTO_PAGAR]', '[MONTO CT]', '[MONTO VAC]', '[MONTOPAGAR]'
        ];
    
        $montoValues = [
            $detalleNomina->sueldo_quinc, $detalleNomina->prima_hijos,
            $detalleNomina->prima_prof, $detalleNomina->prima_antig,
            $detalleNomina->ivss, $detalleNomina->tesoreria_ss,
            $detalleNomina->pie, $detalleNomina->faov,
            $detalleNomina->caja_ahorro, $detalleNomina->aporte_suep,
            $detalleNomina->total_a, $detalleNomina->total_d,
            $detalleNomina->montopagar, $detalleNomina->cesta_tickets,
            $detalleNomina->bono_vac, $detalleNomina->montopagar
        ];
    
        foreach ($montos as $key => $tag) {
            $replacements[$tag] = $formatter->asDecimal($montoValues[$key] ?? 0, 2);
        }
    
        // Determinar periodo
        $periodoData = $this->procesarPeriodo($detalleNomina);
        $replacements = array_merge($replacements, $periodoData);
    
        // Cargar y procesar plantilla
        $htmlContent = file_get_contents(Yii::getAlias('@app/templates/RECIBO.html'));
        $htmlContent = str_replace(array_keys($replacements), array_values($replacements), $htmlContent);
    
        // Generar PDF
        try {
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($htmlContent);
            
            // Determinar si es primera o segunda quincena
            $periodoStr = ($detalleNomina->periodo == 1) ? '1RA' : '2DA';
            $fileName = 'RECIBO' . $periodoStr . '_' . date("dmY") . '_' . $empleado->ci . '.pdf';

            // Establecer el tipo MIME correcto
            Yii::$app->response->headers->set('Content-Type', 'application/pdf');
            Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

            // Establecer la cookie antes de enviar el archivo
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => 'fileDownload',
                'value' => 'true',
                'expire' => time() + 2, // La cookie expirará en 2 segundos
            ]));

            return $mpdf->Output($fileName, 'D');

        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Error generando el PDF: ' . $e->getMessage());
            return $this->redirect(['index']);
        }
    }
    
    private function procesarPeriodo($detalle) {
        $fechaInicio = $fechaFin = '';
        $primerDia = new \DateTime("{$detalle->anio}-{$detalle->mes}-01");

        $fechaRecibo = date('d/m/Y');
    
        if ($detalle->periodo == 1) {
            $fechaInicio = $primerDia->format('d/m/Y');
            $fechaFin = $primerDia->modify('+14 days')->format('d/m/Y');
            return [
                '[PERIODO_LTR]' => 'PRIMERA',
                '[FECHA INICIO QUINCENA]' => $fechaInicio,
                '[FECHA FIN QUINCENA]' => $fechaFin,
                '[CT TEXTO]' => '&nbsp;',
                '[BV TEXTO]' => '&nbsp;',
                '[DATECONSTANCIA]' => $fechaRecibo,
                // Se dejan en blanco las etiquetas de montos correspondientes
                '[MONTO CT]' => '',
                '[MONTO VAC]' => '',
            ];
        }
    
        if ($detalle->periodo == 2) {
            $fechaInicio = $primerDia->modify('+15 days')->format('d/m/Y');
            $fechaFin = $primerDia->modify('last day of this month')->format('d/m/Y');
    
            $fechaRecibo = date('d/m/Y');

            return [
                '[PERIODO_LTR]' => 'SEGUNDA',
                '[FECHA INICIO QUINCENA]' => $fechaInicio,
                '[FECHA FIN QUINCENA]' => $fechaFin,
                '[CT TEXTO]' => 'CESTA TICKETS',
                '[BV TEXTO]' => 'BONO VACACIONAL',
                '[DATECONSTANCIA]' => $fechaRecibo,
                '[TOTAL_AS]' => Yii::$app->formatter->asDecimal($detalle->total_a + $detalle->cesta_tickets, 2),
                '[MONTOPAGAR]' => Yii::$app->formatter->asDecimal($detalle->montopagar + $detalle->cesta_tickets, 2)
            ];
        }
    
        return [];
    }

    /**
     * Acción para obtener los meses disponibles por año (para AJAX)
     *
     * @return string JSON de meses con su conteo de registros
     */
    public function actionMesesPorAnio()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        // Obtener el año del formato DetallesSearch[anio]
        $anio = \Yii::$app->request->get('DetallesSearch')['anio'] ?? null;
        
        if (!$anio) {
            return [];
        }
        
        // Obtener el empleado_id del usuario logueado
        $ci = \Yii::$app->session->get('ci');
        $empleado = \app\models\Employees::find()->where(['ci' => $ci])->one();
        $empleado_id = $empleado ? $empleado->empleado_id : null;
        
        $mesesConConteo = \app\models\DetallesNomina::getMesesConConteo($anio, $empleado_id);
        
        // Si no hay registros para el año seleccionado, devolver un array vacío
        // El JavaScript en el cliente manejará este caso
        if (empty($mesesConConteo)) {
            // Opcionalmente, podríamos devolver todos los meses con conteo 0
            $meses = [
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre',
            ];
            
            $resultado = [];
            foreach ($meses as $key => $nombre) {
                $resultado[$key] = [
                    'nombre' => $nombre,
                    'conteo' => 0
                ];
            }
            
            return $resultado;
        }
        
        return $mesesConConteo;
    }

    /**
     * Descarga el recibo de nómina específico para ALTO_NIVEL
     * @param int $detail_id ID del detalle de nómina
     * @return mixed
     */
    public function actionDescargarReciboAltoNivel($detail_id) {
        // Obtener detalle de nómina con relaciones
        $detalleNomina = DetallesNomina::find()
            ->with(['empleado', 'nomina'])
            ->where(['detail_id' => $detail_id])
            ->one();

        if (!$detalleNomina) {
            Yii::$app->session->setFlash('error', 'Detalles de nómina no encontrados.');
            return $this->redirect(['index']);
        }

        // Validar entidades relacionadas
        $empleado = $detalleNomina->empleado;
        if (!$empleado) {
            Yii::$app->session->setFlash('error', 'Empleado no encontrado.');
            return $this->redirect(['index']);
        }

        // Obtener procurador activo en lugar de director
        $procurador = Procuradores::find()->where(['activo' => true])->one();
        if (!$procurador) {
            Yii::$app->session->setFlash('error', 'No hay procurador activo para generar el recibo.');
            return $this->redirect(['index']);
        }

        // Configurar formateadores
        $formatter = Yii::$app->formatter;
        $meses = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
            5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
            9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];

        // Procesar datos comunes
        $nominaMap = [
            'ALTO_NIVEL' => 'ALTO NIVEL',
            'EMPLEADO_FIJO' => 'EMPLEADO FIJO',
            'OBRERO_FIJO' => 'OBRERO',
            'OBREROS_CONTRATADOS' => 'OBRERO',
            'CONTRATADOS' => 'CONTRATADO',
            'PENSIONADOS' => 'PENSIONADO',
            'JUBILADOS' => 'JUBILADO',
            'ENCARGADOS' => 'ALTO NIVEL (E)'
        ];

        $replacements = [
            '[EMPLEADO]' => $empleado->nombre,
            '[CI]' => $empleado->ci,
            '[FECHA_INGRESO]' => $formatter->asDate($empleado->fecha_ingreso, 'php:d/m/Y'),
            '[CARGO]' => $detalleNomina->cargo,
            '[NOMINA]' => $nominaMap[$detalleNomina->nomina->nomina ?? ''] ?? '',
            '[MES]' => $meses[$detalleNomina->mes] ?? '',
            '[AÑO]' => $detalleNomina->anio,
            '[Base64Firma]' => $procurador->firma_base64 ? '<img src="'.$procurador->firma_base64.'" alt="Firma">' : '',
            '[NOMBRE PROCURADOR]' => $procurador->nombre,
            '[RESOLUCION]' => $procurador->resolucion,
            '[FECHA RESOLUCION]' => $formatter->asDate($procurador->fecha_resolucion, 'php:d/m/Y') ?: 'Fecha no disponible',
            '[GACETA]' => $procurador->gaceta,
            '[FECHA GACETA]' => $formatter->asDate($procurador->fecha_gaceta, 'php:d/m/Y') ?: 'Fecha no disponible',
        ];

        // Procesar montos comunes (sin agregar 'VES' al final)
        $montos = [
            '[MONTO SUELDO]', '[MONTO PHIJOS]', '[MONTO PPROF]', '[MONTO PANTIG]',
            '[MONTO SSO]', '[MONTO TSS]', '[MONTO PIE]', '[MONTO FAOV]', 
            '[MONTO CAJA]', '[MONTO SUEP]', '[TOTAL_AS]', '[TOTAL_DED]', 
            '[MONTO_PAGAR]', '[MONTO CT]', '[MONTO VAC]', '[MONTOPAGAR]'
        ];

        $montoValues = [
            $detalleNomina->sueldo_quinc, $detalleNomina->prima_hijos,
            $detalleNomina->prima_prof, $detalleNomina->prima_antig,
            $detalleNomina->ivss, $detalleNomina->tesoreria_ss,
            $detalleNomina->pie, $detalleNomina->faov,
            $detalleNomina->caja_ahorro, $detalleNomina->aporte_suep,
            $detalleNomina->total_a, $detalleNomina->total_d,
            $detalleNomina->montopagar, $detalleNomina->cesta_tickets,
            $detalleNomina->bono_vac, $detalleNomina->montopagar
        ];

        foreach ($montos as $key => $tag) {
            $replacements[$tag] = $formatter->asDecimal($montoValues[$key] ?? 0, 2);
        }

        // Determinar periodo
        $periodoData = $this->procesarPeriodo($detalleNomina);
        $replacements = array_merge($replacements, $periodoData);

        // Cargar y procesar plantilla específica para ALTO_NIVEL
        $htmlContent = file_get_contents(Yii::getAlias('@app/templates/RECIBO_ALTONIVEL.html'));
        $htmlContent = str_replace(array_keys($replacements), array_values($replacements), $htmlContent);

        // Generar PDF
        try {
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($htmlContent);
            
            // Determinar si es primera o segunda quincena
            $periodoStr = ($detalleNomina->periodo == 1) ? '1RA' : '2DA';
            $fileName = 'RECIBO' . $periodoStr . '_' . date("dmY") . '_' . $empleado->ci . '.pdf';

            // Establecer el tipo MIME correcto
            Yii::$app->response->headers->set('Content-Type', 'application/pdf');
            Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

            // Establecer la cookie antes de enviar el archivo
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => 'fileDownload',
                'value' => 'true',
                'expire' => time() + 2, // La cookie expirará en 2 segundos
            ]));

            return $mpdf->Output($fileName, 'D');

        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Error generando el PDF: ' . $e->getMessage());
            return $this->redirect(['index']);
        }
    }

    /**
     * Obtiene los datos del recibo para el modal de vista previa
     * @return \yii\web\Response
     */
    public function actionGetReciboData()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->get('id');
        
        if (!$id) {
            return [
                'error' => true,
                'message' => 'ID de recibo no proporcionado'
            ];
        }
        
        $model = DetallesNomina::find()
            ->with(['empleado', 'nomina'])
            ->where(['detail_id' => $id])
            ->one();
            
        if (!$model) {
            return [
                'error' => true,
                'message' => 'No se encontró el recibo solicitado'
            ];
        }
        
        return [
            'empleado' => [
                'nombre' => $model->empleado->nombre,
                'ci' => $model->empleado->ci,
            ],
            'cargo' => $model->cargo,
            'nomina' => [
                'nomina' => $model->nomina->nomina,
            ],
            'periodo' => $model->periodo,
            'mes' => $model->mes,
            'anio' => $model->anio,
            'sueldo_quinc' => floatval($model->sueldo_quinc),
            'prima_hijos' => floatval($model->prima_hijos),
            'prima_prof' => floatval($model->prima_prof),
            'cesta_tickets' => floatval($model->cesta_tickets),
            'ivss' => floatval($model->ivss),
            'pie' => floatval($model->pie),
            'faov' => floatval($model->faov),
            'montopagar' => floatval($model->montopagar),
        ];
    }
}
