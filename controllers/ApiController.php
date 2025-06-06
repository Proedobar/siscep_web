<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\Cors;
use app\models\Users;
use app\models\DetallesNomina;
use app\models\Nominas;
use app\models\Directores;
use app\models\Procuradores;
use app\models\HistoricConstancias;
use app\models\Empleados;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;
use yii\helpers\ArrayHelper;
use DateTime;
use yii\web\Response;
use app\models\Roles;

class ApiController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Habilitar CORS para permitir peticiones desde otros dominios
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];
        
        return $behaviors;
    }

    // Acción de prueba
    public function actionIndex()
    {
        return [
            'status' => 'success',
            'message' => 'API funcionando correctamente',
            'version' => '1.0'
        ];
    }

    public function actionLogin()
    {
        // Obtener los datos dependiendo del método de la petición
        $email = Yii::$app->request->get('email', Yii::$app->request->post('email'));
        $password = Yii::$app->request->get('password', Yii::$app->request->post('password'));

        if (!$email || !$password) {
            throw new BadRequestHttpException('Email y contraseña son requeridos');
        }

        // Buscar el usuario por email
        $user = Users::find()
            ->where(['email' => $email])
            ->with(['empleado']) // Cargar la relación con empleado
            ->one();

        if (!$user) {
            throw new UnauthorizedHttpException('Credenciales inválidas');
        }

        // Verificar la contraseña (asumiendo que está hasheada)
        if (!Yii::$app->security->validatePassword($password, $user->password_hash)) {
            throw new UnauthorizedHttpException('Credenciales inválidas');
        }

        // Obtener el último detalle de nómina del empleado
        $detalleNomina = DetallesNomina::find()
            ->where(['empleado_id' => $user->empleado_id])
            ->orderBy(['anio' => SORT_DESC, 'mes' => SORT_DESC, 'periodo' => SORT_DESC])
            ->one();

        // Obtener todas las nóminas del empleado
        $nominasEmpleado = DetallesNomina::find()
            ->select(['nomina_id'])
            ->distinct()
            ->where(['empleado_id' => $user->empleado_id])
            ->with(['nomina'])
            ->asArray()
            ->all();

        // Crear array de nóminas con estructura ID => Nombre
        $nominas = [];
        foreach ($nominasEmpleado as $detalle) {
            $nomina = Nominas::findOne($detalle['nomina_id']);
            if ($nomina) {
                $nominas[(string)$nomina->nomina_id] = [
                    'id' => $nomina->nomina_id,
                    'nombre' => $nomina->nomina
                ];
            }
        }

        // Preparar la respuesta
        $response = [
            'status' => 'success',
            'data' => [
                'user_id' => $user->user_id,
                'nombre' => $user->empleado->nombre,
                'cedula' => $user->empleado->ci,
                'fecha_ingreso' => $user->empleado->fecha_ingreso,
                'nominas' => $nominas
            ]
        ];

        // Añadir información del cargo si existe
        if ($detalleNomina) {
            $response['data']['cargo'] = $detalleNomina->cargo;
            $response['data']['tipo_cargo'] = $detalleNomina->tipo_cargo;
        } else {
            $response['data']['cargo'] = null;
            $response['data']['tipo_cargo'] = null;
        }

        return $response;
    }

    public function actionGetConstancia($user_id)
    {
        // Obtener el usuario y verificar que existe
        $user = Users::findOne($user_id);
        if (!$user) {
            throw new BadRequestHttpException('Usuario no encontrado');
        }

        // Obtener el empleado
        $empleado = Empleados::findOne($user->empleado_id);
        if (!$empleado) {
            throw new BadRequestHttpException('Empleado no encontrado');
        }

        // Obtener el último detalle de nómina del empleado
        $detalleNomina = DetallesNomina::find()
            ->where(['empleado_id' => $empleado->empleado_id])
            ->orderBy(['anio' => SORT_DESC, 'mes' => SORT_DESC, 'periodo' => SORT_DESC])
            ->with(['nomina'])
            ->one();

        if (!$detalleNomina || !$detalleNomina->nomina) {
            throw new BadRequestHttpException('No se encontró información de nómina para el empleado');
        }

        try {
            // Guardar el estado actual
            $oldIdentity = Yii::$app->user->identity;
            
            // Configurar la nueva identidad
            Yii::$app->user->enableSession = false;
            Yii::$app->user->login($user, 0);
            
            // Crear una instancia del SiteController
            $siteController = new \app\controllers\SiteController('site', Yii::$app);
            
            // Configurar los parámetros necesarios en la solicitud
            $_GET['nomina_id'] = $detalleNomina->nomina_id;
            
            // Llamar al actionGenerarConstancia
            $pdfContent = $siteController->actionGenerarConstancia();
            
            // Restaurar el estado original
            if ($oldIdentity) {
                Yii::$app->user->login($oldIdentity, 0);
            } else {
                Yii::$app->user->logout(false);
            }

            // Configurar la respuesta
            Yii::$app->response->format = Response::FORMAT_RAW;
            Yii::$app->response->headers->set('Content-Type', 'application/pdf');
            Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="constancia.pdf"');

            return $pdfContent;

        } catch (\Exception $e) {
            // Restaurar el estado original en caso de error
            if (isset($oldIdentity)) {
                if ($oldIdentity) {
                    Yii::$app->user->login($oldIdentity, 0);
                } else {
                    Yii::$app->user->logout(false);
                }
            }
            
            throw new BadRequestHttpException('Error al generar la constancia: ' . $e->getMessage());
        }
    }

    public function actionGetRecibo($detail_id)
    {
        try {
            // Crear una instancia del DetallesNominaController
            $controller = new \app\controllers\DetallesNominaController('detalles-nomina', Yii::$app);
            
            // Obtener el usuario actual
            $user = Users::findOne(Yii::$app->request->get('user_id'));
            if (!$user) {
                throw new BadRequestHttpException('Usuario no encontrado');
            }

            // Guardar el estado actual
            $oldIdentity = Yii::$app->user->identity;
            
            // Configurar la nueva identidad
            Yii::$app->user->enableSession = false;
            Yii::$app->user->login($user, 0);

            // Llamar al actionDescargarRecibo
            $pdfContent = $controller->actionDescargarRecibo($detail_id);
            
            // Restaurar el estado original
            if ($oldIdentity) {
                Yii::$app->user->login($oldIdentity, 0);
            } else {
                Yii::$app->user->logout(false);
            }

            // Configurar la respuesta
            Yii::$app->response->format = Response::FORMAT_RAW;
            Yii::$app->response->headers->set('Content-Type', 'application/pdf');
            Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="recibo.pdf"');

            return $pdfContent;

        } catch (\Exception $e) {
            // Restaurar el estado original en caso de error
            if (isset($oldIdentity)) {
                if ($oldIdentity) {
                    Yii::$app->user->login($oldIdentity, 0);
                } else {
                    Yii::$app->user->logout(false);
                }
            }
            
            throw new BadRequestHttpException('Error al generar el recibo: ' . $e->getMessage());
        }
    }

    public function actionGetReciboAltoNivel($detail_id)
    {
        // Obtener detalle de nómina con relaciones
        $detalleNomina = DetallesNomina::find()
            ->with(['empleado', 'nomina'])
            ->where(['detail_id' => $detail_id])
            ->one();

        if (!$detalleNomina) {
            throw new BadRequestHttpException('Detalles de nómina no encontrados.');
        }

        // Validar entidades relacionadas
        $empleado = $detalleNomina->empleado;
        if (!$empleado) {
            throw new BadRequestHttpException('Empleado no encontrado.');
        }

        // Obtener procurador activo en lugar de director
        $procurador = Procuradores::find()->where(['activo' => true])->one();
        if (!$procurador) {
            throw new BadRequestHttpException('No hay procurador activo para generar el recibo.');
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

        // Procesar periodo
        $periodoData = $this->procesarPeriodo($detalleNomina);

        // Preparar la respuesta
        $response = [
            'status' => 'success',
            'data' => [
                'empleado' => [
                    'nombre' => $empleado->nombre,
                    'ci' => $empleado->ci,
                    'fecha_ingreso' => $formatter->asDate($empleado->fecha_ingreso, 'php:d/m/Y'),
                    'cargo' => $detalleNomina->cargo,
                ],
                'nomina' => [
                    'tipo' => $nominaMap[$detalleNomina->nomina->nomina ?? ''] ?? '',
                    'mes' => $meses[$detalleNomina->mes] ?? '',
                    'anio' => $detalleNomina->anio,
                    'periodo' => $periodoData,
                ],
                'montos' => [
                    'sueldo_quincenal' => $formatter->asDecimal($detalleNomina->sueldo_quinc, 2),
                    'prima_hijos' => $formatter->asDecimal($detalleNomina->prima_hijos, 2),
                    'prima_profesional' => $formatter->asDecimal($detalleNomina->prima_prof, 2),
                    'prima_antiguedad' => $formatter->asDecimal($detalleNomina->prima_antig, 2),
                    'ivss' => $formatter->asDecimal($detalleNomina->ivss, 2),
                    'tesoreria_ss' => $formatter->asDecimal($detalleNomina->tesoreria_ss, 2),
                    'pie' => $formatter->asDecimal($detalleNomina->pie, 2),
                    'faov' => $formatter->asDecimal($detalleNomina->faov, 2),
                    'caja_ahorro' => $formatter->asDecimal($detalleNomina->caja_ahorro, 2),
                    'aporte_suep' => $formatter->asDecimal($detalleNomina->aporte_suep, 2),
                    'total_asignaciones' => $formatter->asDecimal($detalleNomina->total_a, 2),
                    'total_deducciones' => $formatter->asDecimal($detalleNomina->total_d, 2),
                    'monto_neto' => $formatter->asDecimal($detalleNomina->montopagar, 2),
                    'cesta_tickets' => $formatter->asDecimal($detalleNomina->cesta_tickets, 2),
                    'bono_vacacional' => $formatter->asDecimal($detalleNomina->bono_vac, 2),
                ],
                'firmante' => [
                    'nombre' => $procurador->nombre,
                    'resolucion' => $procurador->resolucion,
                    'fecha_resolucion' => $formatter->asDate($procurador->fecha_resolucion, 'php:d/m/Y') ?: 'Fecha no disponible',
                    'gaceta' => $procurador->gaceta,
                    'fecha_gaceta' => $formatter->asDate($procurador->fecha_gaceta, 'php:d/m/Y') ?: 'Fecha no disponible',
                    'firma' => $procurador->firma_base64,
                ]
            ]
        ];

        return $response;
    }

    private function fechaEnLetras($fecha)
    {
        // Primero, convertir la fecha al formato correcto
        if (strpos($fecha, '-') !== false) {
            // Si la fecha viene en formato YYYY-MM-DD
            $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
            if (!$fechaObj) {
                return "fecha inválida";
            }
            $fecha = $fechaObj->format('d/m/Y');
        }
        
        $fechaConvertida = DateTime::createFromFormat('d/m/Y', $fecha);
        if (!$fechaConvertida) {
            return "fecha inválida";
        }

        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        
        $diaNum = $fechaConvertida->format('d');
        $mesNum = (int)$fechaConvertida->format('m');
        $anio = $fechaConvertida->format('Y');
        
        return "{$diaNum} de {$meses[$mesNum]} de {$anio}";
    }

    private function mapearNomina($nominaOriginal)
    {
        $mapaNominas = [
            'ALTO_NIVEL' => 'ALTO NIVEL',
            'EMPLEADO_FIJO' => 'EMPLEADO FIJO',
            'OBRERO_FIJO' => 'OBRERO',
            'OBREROS_CONTRATADOS' => 'OBRERO',
            'CONTRATADOS' => 'CONTRATADO',
            'PENSIONADOS' => 'PENSIONADO',
            'JUBILADOS' => 'JUBILADO',
            'ENCARGADOS' => 'ALTO NIVEL (E)'
        ];

        return $mapaNominas[$nominaOriginal] ?? $nominaOriginal;
    }

    private function montoALetras($monto)
    {
        $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        $entero = floor($monto);
        $decimal = round(($monto - $entero) * 100);
        
        $resultado = $formatter->format($entero) . ' BOLÍVARES';
        if ($decimal > 0) {
            $resultado .= ' CON ' . $formatter->format($decimal) . ' CÉNTIMOS';
        } else {
            $resultado .= ' SIN CÉNTIMOS';
        }
        
        return mb_strtoupper($resultado);
    }

    private function procesarPeriodo($detalleNomina)
    {
        // Implementa la lógica para procesar el periodo de la nómina
        // Esto puede variar dependiendo de cómo se maneje el periodo en tu base de datos
        // Aquí se asume que el periodo es un número que representa el tipo de periodo
        // y se mapea a una cadena legible
        $periodo = $detalleNomina->periodo;
        $periodoMap = [
            1 => 'PRIMERO',
            2 => 'SEGUNDO',
            3 => 'TERCERO',
            4 => 'CUARTO',
            5 => 'QUINTO',
            6 => 'SEXTO',
            7 => 'SEPTIMO',
            8 => 'OCTAVO',
            9 => 'NOVENO',
            10 => 'DECIMO',
            11 => 'DECIMO PRIMERO',
            12 => 'DECIMO SEGUNDO'
        ];
        return $periodoMap[$periodo] ?? $periodo;
    }

    public function actionGetUsers()
    {
        // Obtener todos los usuarios con sus relaciones
        $users = Users::find()
            ->with(['empleado', 'rol'])
            ->where(['is_deleted' => 0])
            ->all();

        $response = [
            'status' => 'success',
            'data' => array_map(function ($user) {
                return [
                    'user_id' => $user->user_id,
                    'empleado' => [
                        'id' => $user->empleado->empleado_id,
                        'nombre' => $user->empleado->nombre
                    ],
                    'foto_perfil' => $user->foto_perfil ?? 'default',
                    'email' => $user->email,
                    'state' => $user->state,
                    'ultima_vez' => $user->ultima_vez,
                    'rol' => [
                        'id' => $user->rol->id,
                        'descripcion' => $user->rol->descripcion
                    ]
                ];
            }, $users)
        ];

        return $response;
    }

    public function actionGetDirectores()
    {
        // Obtener todos los directores
        $directores = Directores::find()->all();

        $formatter = Yii::$app->formatter;
        
        $response = [
            'status' => 'success',
            'data' => array_map(function ($director) use ($formatter) {
                return [
                    'id' => $director->id,
                    'nombre_director' => $director->nombre_director,
                    'resolucion' => $director->resolucion,
                    'fecha_resolucion' => $formatter->asDate($director->fecha_resolucion, 'php:d/m/Y'),
                    'gaceta' => $director->gaceta,
                    'fecha_gaceta' => $formatter->asDate($director->fecha_gaceta, 'php:d/m/Y'),
                    'activo' => (bool)$director->activo,
                    'firma_base64' => $director->firma_base64 ?? null
                ];
            }, $directores)
        ];

        return $response;
    }

    

    public function actionGetProcuradores()
    {
        // Obtener todos los procuradores
        $procuradores = Procuradores::find()->all();

        $formatter = Yii::$app->formatter;
        
        $response = [
            'status' => 'success',
            'data' => array_map(function ($procurador) use ($formatter) {
                return [
                    'id' => $procurador->id,
                    'nombre' => $procurador->nombre,
                    'resolucion' => $procurador->resolucion,
                    'fecha_resolucion' => $formatter->asDate($procurador->fecha_resolucion, 'php:d/m/Y'),
                    'gaceta' => $procurador->gaceta,
                    'fecha_gaceta' => $formatter->asDate($procurador->fecha_gaceta, 'php:d/m/Y'),
                    'activo' => (bool)$procurador->activo,
                    'firma_base64' => $procurador->firma_base64
                ];
            }, $procuradores)
        ];

        return $response;
    }

    public function actionVerificarConstancia($id)
    {
        // Buscar la constancia por ID
        $constancia = HistoricConstancias::findOne($id);

        if ($constancia) {
            // Obtener el usuario y su empleado relacionado
            $user = Users::find()
                ->with(['empleado'])
                ->where(['user_id' => $constancia->user_id])
                ->one();

            // Preparar la respuesta exitosa
            $response = [
                'status' => 'success',
                'message' => 'La constancia existe',
                'data' => [
                    'constancia_id' => $constancia->id,
                    'empleado' => [
                        'nombre' => $user ? $user->empleado->nombre : 'No disponible',
                        'user_id' => $constancia->user_id
                    ],
                    'fecha_creacion' => $constancia->created_at
                ]
            ];
        } else {
            // Preparar la respuesta de error
            $response = [
                'status' => 'error',
                'message' => 'La constancia no existe'
            ];
        }

        return $response;
    }

    public function actionGetFirmante($user_id)
    {
        // 1. Obtener el empleado_id desde Users
        $user = Users::findOne($user_id);
        if (!$user) {
            return [
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ];
        }

        // 2. Verificar el empleado
        $empleado = Empleados::findOne($user->empleado_id);
        if (!$empleado) {
            return [
                'status' => 'error',
                'message' => 'Empleado no encontrado'
            ];
        }

        // 3 y 4. Obtener el último detalle de nómina y su tipo
        $detalleNomina = DetallesNomina::find()
            ->where(['empleado_id' => $empleado->empleado_id])
            ->orderBy(['anio' => SORT_DESC, 'mes' => SORT_DESC, 'periodo' => SORT_DESC])
            ->with(['nomina'])
            ->one();

        if (!$detalleNomina || !$detalleNomina->nomina) {
            return [
                'status' => 'error',
                'message' => 'No se encontró información de nómina'
            ];
        }

        // 5. Verificar si es ALTO_NIVEL
        if ($detalleNomina->nomina->nomina === Nominas::NOMINA_ALTO_NIVEL) {
            // 5.1 Buscar procurador activo
            $firmante = Procuradores::find()
                ->where(['activo' => true])
                ->one();

            if (!$firmante) {
                return [
                    'status' => 'error',
                    'message' => 'No hay procurador activo configurado'
                ];
            }

            return [
                'status' => 'success',
                'data' => [
                    'nombre' => $firmante->nombre,
                    'resolucion' => $firmante->resolucion,
                    'fecha_resolucion' => $firmante->fecha_resolucion,
                    'gaceta' => $firmante->gaceta,
                    'fecha_gaceta' => $firmante->fecha_gaceta,
                    'activo' => (bool)$firmante->activo,
                    'isprocurador' => true
                ]
            ];
        } else {
            // 6. Para cualquier otra nómina, buscar director activo
            $firmante = Directores::find()
                ->where(['activo' => true])
                ->one();

            if (!$firmante) {
                return [
                    'status' => 'error',
                    'message' => 'No hay director activo configurado'
                ];
            }

            return [
                'status' => 'success',
                'data' => [
                    'nombre' => $firmante->nombre_director,
                    'resolucion' => $firmante->resolucion,
                    'fecha_resolucion' => $firmante->fecha_resolucion,
                    'gaceta' => $firmante->gaceta,
                    'fecha_gaceta' => $firmante->fecha_gaceta,
                    'activo' => (bool)$firmante->activo,
                    'isprocurador' => false
                ]
            ];
        }
    }

    public function actionSearchDetalles()
    {
        // Obtener parámetros
        $user_id = Yii::$app->request->get('user_id');
        $mes = Yii::$app->request->get('mes');
        $anio = Yii::$app->request->get('anio');
        $nomina_id = Yii::$app->request->get('nomina_id');

        // Validar parámetros obligatorios
        if (!$user_id || !$mes || !$anio) {
            throw new BadRequestHttpException('Los parámetros user_id, mes y anio son obligatorios');
        }

        // Obtener el empleado_id desde Users
        $user = Users::findOne($user_id);
        if (!$user) {
            throw new BadRequestHttpException('Usuario no encontrado');
        }

        // Construir la consulta base
        $query = DetallesNomina::find()
            ->alias('dn')
            ->select([
                'dn.detail_id',
                'n.nomina AS nombre_nomina',
                'CASE 
                    WHEN dn.periodo = 1 THEN "PRIMERA QUINCENA"
                    WHEN dn.periodo = 2 THEN "SEGUNDA QUINCENA"
                    ELSE CONCAT("QUINCENA ", dn.periodo)
                END AS periodo_nombre',
                'e.nombre AS nombre_empleado',
                'dn.montopagar',
                'dn.cesta_tickets'
            ])
            ->innerJoin('nominas n', 'n.nomina_id = dn.nomina_id')
            ->innerJoin('empleados e', 'e.empleado_id = dn.empleado_id')
            ->where([
                'dn.empleado_id' => $user->empleado_id,
                'dn.mes' => $mes,
                'dn.anio' => $anio
            ]);

        // Agregar filtro de nómina si se proporciona
        if ($nomina_id) {
            $query->andWhere(['dn.nomina_id' => $nomina_id]);
        }

        // Ordenar por periodo
        $query->orderBy(['dn.periodo' => SORT_ASC]);

        // Ejecutar la consulta
        $detalles = $query->asArray()->all();

        if (empty($detalles)) {
            return [
                'status' => 'success',
                'message' => 'No se encontraron detalles de nómina para los criterios especificados',
                'data' => []
            ];
        }

        return [
            'status' => 'success',
            'data' => $detalles
        ];
    }

    public function actionGetUserNominas($user_id)
    {
        // Validar que el usuario existe
        $user = Users::findOne($user_id);
        if (!$user) {
            throw new BadRequestHttpException('Usuario no encontrado');
        }

        // Mapeo de nombres de nóminas
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

        // Obtener las nóminas del empleado
        $nominas = DetallesNomina::find()
            ->select(['n.nomina_id', 'n.nomina'])
            ->distinct()
            ->alias('dn')
            ->innerJoin('nominas n', 'n.nomina_id = dn.nomina_id')
            ->where(['dn.empleado_id' => $user->empleado_id])
            ->asArray()
            ->all();

        // Procesar y formatear las nóminas
        $nominasFormateadas = [];
        foreach ($nominas as $nomina) {
            $nombreFormateado = isset($nominaMap[$nomina['nomina']]) 
                ? $nominaMap[$nomina['nomina']] 
                : $nomina['nomina'];
            
            $nominasFormateadas[] = [
                'id' => $nomina['nomina_id'],
                'nombre' => $nombreFormateado
            ];
        }

        // Ordenar por nombre
        usort($nominasFormateadas, function($a, $b) {
            return strcmp($a['nombre'], $b['nombre']);
        });

        return [
            'status' => 'success',
            'data' => $nominasFormateadas
        ];
    }

    public function actionGetRoleForUser($user_id)
    {
        // Buscar el usuario
        $user = Users::findOne($user_id);
        if (!$user) {
            return [
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ];
        }

        // Obtener el rol del usuario
        $rol = Roles::findOne($user->rol_id);
        if (!$rol) {
            return [
                'status' => 'error',
                'message' => 'Rol no encontrado'
            ];
        }

        return [
            'status' => 'success',
            'data' => [
                $rol->id => $rol->descripcion
            ]
        ];
    }

    public function actionRegisterUser()
    {
        // Leer los datos del cuerpo de la solicitud JSON
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        
        if (!isset($data['ci']) || !isset($data['email']) || !isset($data['password'])) {
            return [
                'status' => 'error',
                'message' => 'Datos incompletos para el registro'
            ];
        }
        
        // Verificar que el CI exista en la tabla de empleados
        $empleado = Empleados::findOne(['ci' => $data['ci']]);
        if (!$empleado) {
            return [
                'status' => 'error',
                'message' => 'El número de documento no está registrado'
            ];
        }
        
        // Verificar si ya existe un usuario con este empleado_id
        $userExistente = Users::findOne(['empleado_id' => $empleado->empleado_id]);
        
        // Verificar si el correo ya está en uso por otra cuenta activa
        $emailExistente = Users::findOne(['email' => $data['email'], 'is_deleted' => 0]);
        if ($emailExistente && (!$userExistente || $emailExistente->user_id != $userExistente->user_id)) {
            return [
                'status' => 'error',
                'message' => 'Este correo electrónico ya está registrado por otra cuenta'
            ];
        }
        
        // Si existe un usuario con este empleado_id pero no está marcado como eliminado, es un error
        if ($userExistente && $userExistente->is_deleted != 1) {
            return [
                'status' => 'error',
                'message' => 'Ya existe una cuenta activa asociada a este documento de identidad'
            ];
        }
        
        // Generar código de verificación aleatorio de 6 dígitos
        $code = sprintf("%06d", mt_rand(1, 999999));
        
        if ($userExistente && $userExistente->is_deleted == 1) {
            // Restaurar usuario previamente eliminado
            $userExistente->email = $data['email'];
            $userExistente->password_hash = Yii::$app->security->generatePasswordHash($data['password']);
            $userExistente->state = 0;
            $userExistente->is_deleted = 0;
            $userExistente->auth_key = Yii::$app->security->generateRandomString();
            $userExistente->verification_code = $code;
            $userExistente->is_verified = 0;
            
            if (!$userExistente->save()) {
                return [
                    'status' => 'error',
                    'message' => 'Error al restaurar el usuario: ' . json_encode($userExistente->errors)
                ];
            }
            
            $user = $userExistente;
        } else {
            // Registrar un nuevo usuario
            $user = new Users();
            $user->empleado_id = $empleado->empleado_id;
            $user->email = $data['email'];
            $user->password_hash = Yii::$app->security->generatePasswordHash($data['password']);
            $user->state = 0;
            $user->rol_id = 3;
            $user->auth_key = Yii::$app->security->generateRandomString();
            $user->is_verified = 0;
            $user->tfa_on = 0;
            $user->tfa_code = '000000';
            $user->tfa_vence = date('Y-m-d H:i:s');
            $user->verification_code = $code;
            $user->is_deleted = 0;
            
            if (!$user->save()) {
                return [
                    'status' => 'error',
                    'message' => 'Error al registrar el usuario: ' . json_encode($user->errors)
                ];
            }
        }
        
        // Enviar correo con el código de verificación
        $sent = Yii::$app->mailer->compose()
            ->setTo($user->email)
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
            ->setSubject('Código de Verificación - ' . Yii::$app->name)
            ->setTextBody('Su código de verificación es: ' . $code)
            ->setHtmlBody('<p>Su código de verificación es: <strong>' . $code . '</strong></p>')
            ->send();
            
        if (!$sent) {
            return [
                'status' => 'success',
                'warning' => true,
                'message' => 'Usuario registrado pero hubo un problema al enviar el correo de verificación',
                'data' => [
                    'user_id' => $user->user_id,
                    'email' => $user->email
                ]
            ];
        }
        
        $message = $userExistente && $userExistente->is_deleted == 1 ? 
            'Su cuenta ha sido restaurada. Se ha enviado un código de verificación a su correo.' : 
            'Usuario registrado correctamente. Se ha enviado un código de verificación a su correo.';
        
        return [
            'status' => 'success',
            'message' => $message,
            'data' => [
                'user_id' => $user->user_id,
                'email' => $user->email
            ]
        ];
    }

    public function actionVerifyUser()
    {
        // Leer los datos del cuerpo de la solicitud JSON
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        
        if (!isset($data['code']) || !isset($data['user_id'])) {
            return [
                'status' => 'error',
                'message' => 'Código de verificación y user_id son requeridos'
            ];
        }
        
        // Buscar el usuario
        $user = Users::findOne($data['user_id']);
        if (!$user) {
            return [
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ];
        }
        
        // Verificar el código
        if ($user->verification_code !== $data['code']) {
            return [
                'status' => 'error',
                'message' => 'El código de verificación es incorrecto'
            ];
        }
        
        // Actualizar el estado de verificación
        $user->is_verified = 1;
        $user->state = 1; // Activar el usuario
        
        if (!$user->save(false)) {
            return [
                'status' => 'error',
                'message' => 'Error al actualizar el estado de verificación'
            ];
        }
        
        return [
            'status' => 'success',
            'message' => 'Verificación completada correctamente',
            'data' => [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'is_verified' => true
            ]
        ];
    }
} 