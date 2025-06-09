<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Directores;
use app\models\Procuradores;
use app\models\HistoricConstancias;
use app\models\TfaForm;
use DateTime;
use Luecano\NumeroALetras\NumeroALetras;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Label\Label;
use chillerlan\QRCode\QRCode as QRGenerator;
use chillerlan\QRCode\QROptions;
use app\models\PasswordResetRequestForm;
use app\models\Users;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'perfil'],
                'rules' => [
                    [
                        'actions' => ['logout', 'perfil'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = false;
        
        // Forzamos la lectura de los mensajes flash para asegurar que están disponibles en la vista
        $flashMessages = [
            'success' => Yii::$app->session->getFlash('success', null, true),
            'warning' => Yii::$app->session->getFlash('warning', null, true),
            'error' => Yii::$app->session->getFlash('error', null, true),
        ];
        
        // Si tenemos flash messages, volvemos a establecerlos para mostrarlos
        foreach ($flashMessages as $key => $message) {
            if ($message !== null) {
                Yii::$app->session->setFlash($key, $message);
            }
        }
        
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $model->getUser();
            
            // Si el usuario tiene TFA activado
            if ($user && $user->tfa_on == 1) {
                // Generar código TFA
                $tfaCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->tfa_code = $tfaCode;
                $user->tfa_vence = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                
                if ($user->save()) {
                    try {
                        // Enviar código por correo usando la plantilla
                        Yii::$app->mailer->compose('verification-code', ['code' => $tfaCode])
                            ->setTo($user->email)
                            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                            ->setSubject('Código de Verificación TFA')
                            ->send();
                            
                        // Guardar datos en sesión para TFA
                        Yii::$app->session->set('tfa_user_id', $user->user_id);
                        Yii::$app->session->set('tfa_rememberMe', $model->rememberMe);
                        
                        if (Yii::$app->request->isAjax) {
                            return $this->asJson([
                                'success' => true,
                                'redirect' => Yii::$app->urlManager->createUrl(['site/tfa'])
                            ]);
                        }
                        return $this->redirect(['site/tfa']);
                    } catch (\Exception $e) {
                        Yii::error('Error al enviar código TFA: ' . $e->getMessage());
                        Yii::$app->session->setFlash('error', 'Error al enviar el código de verificación. Por favor intente nuevamente.');
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Error al generar el código de verificación.');
                }
            } else {
                // Si no tiene TFA, hacer login normal
                if ($model->login()) {
                    // Actualizar ultima_vez del usuario con timezone de Caracas
                    $user = Yii::$app->user->identity;
                    $date = new \DateTime('now', new \DateTimeZone('America/Caracas'));
                    $user->ultima_vez = $date->format('Y-m-d H:i:s');
                    $user->save(false);
                    
                    if (Yii::$app->request->isAjax) {
                        return $this->asJson([
                            'success' => true,
                            'redirect' => Yii::$app->urlManager->createUrl(['site/index'])
                        ]);
                    }
                    return $this->goBack();
                }
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('login', [
                'model' => $model,
            ]);
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * TFA verification action.
     *
     * @return Response|string
     */
    public function actionTfa()
    {
        $this->layout = false;
        
        // Verificar si hay un usuario pendiente de verificación TFA
        if (!Yii::$app->session->has('tfa_user_id')) {
            Yii::$app->session->setFlash('error', 'No hay una verificación TFA pendiente.');
            return $this->redirect(['site/login']);
        }
        
        $model = new TfaForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->verify()) {
            // Actualizar ultima_vez del usuario con timezone de Caracas
            $user = Yii::$app->user->identity;
            $date = new \DateTime('now', new \DateTimeZone('America/Caracas'));
            $user->ultima_vez = $date->format('Y-m-d H:i:s');
            $user->save(false);
            
            // Limpiar datos de sesión TFA
            Yii::$app->session->remove('tfa_user_id');
            Yii::$app->session->remove('tfa_rememberMe');
            
            return $this->goHome();
        }
        
        return $this->render('tfa', [
            'model' => $model
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Displays perfil page.
     *
     * @return string|Response
     */
    public function actionPerfil()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        
        $user = \app\models\Users::findOne([
            'user_id' => Yii::$app->user->id,
            'is_deleted' => 0
        ]);
        
        if ($user === null) {
            Yii::$app->session->setFlash('error', 'Usuario no encontrado.');
            return $this->goHome();
        }
        
        // Manejo para actualizar tfa_on
        if (Yii::$app->request->isPost && isset($_POST['tfa_toggle'])) {
            try {
                // Registrar el estado actual para depuración
                Yii::info('Estado actual de tfa_on: ' . $user->tfa_on);
                Yii::info('POST recibido: ' . json_encode($_POST));
                
                // Actualizar el estado de tfa_on
                $user->tfa_on = isset($_POST['tfa_enabled']) ? 1 : 0;
                
                // Registrar el nuevo estado para depuración
                Yii::info('Nuevo estado de tfa_on: ' . $user->tfa_on);
                
                // Intentar guardar sin validación
                if (!$user->save(false)) {
                    Yii::error('Error al guardar configuración TFA: ' . json_encode($user->errors));
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return [
                        'success' => false,
                        'message' => 'Error al actualizar la configuración de autenticación de dos factores.'
                    ];
                }
                
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'La configuración de autenticación de dos factores ha sido actualizada correctamente.'
                ];
                
            } catch (\Exception $e) {
                Yii::error('Excepción al actualizar TFA: ' . $e->getMessage());
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Error al actualizar la configuración de autenticación de dos factores.'
                ];
            }
        }
        
        // Manejo para eliminar cuenta
        if (Yii::$app->request->isPost && isset($_POST['delete_account'])) {
            // Log detallado para depuración
            Yii::warning('POST recibido para eliminar cuenta: ' . json_encode($_POST), 'perfil');
            
            try {
                $user->is_deleted = 1;
                if (!$user->save()) {
                    throw new \Exception('Error al guardar: ' . json_encode($user->errors));
                }
                
                // Cerrar sesión
                Yii::$app->user->logout();
                
                // Flash message y redirección
                Yii::$app->session->setFlash('success', 'Su cuenta ha sido eliminada correctamente.');
                Yii::warning('Usuario eliminado correctamente: ' . $user->user_id, 'perfil');
                return $this->redirect(['site/login']);
                
            } catch (\Exception $e) {
                Yii::error('Error al eliminar cuenta: ' . $e->getMessage(), 'perfil');
                Yii::$app->session->setFlash('error', 'No se pudo eliminar la cuenta: ' . $e->getMessage());
                return $this->refresh();
            }
        }
        
        return $this->render('perfil', [
            'user' => $user,
        ]);
    }

    public function actionRecibos()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }   
        
        return $this->redirect(['detalles-nomina/index']);
    }
    

    /**
     * Displays signup page.
     *
     * @return string
     */
    public function actionSignup()
    {
        $this->layout = false;
        
        $model = new \app\models\SignupForm();

        // NO procesamos el formulario desde aquí ya que ahora se maneja vía AJAX
        // Solo renderizamos la vista
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Verify CI via AJAX.
     *
     * @return \yii\web\Response
     */
    public function actionVerifyCi()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        // Leer los datos del cuerpo de la solicitud JSON
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $ci = $data['ci'] ?? null;
        
        if (!$ci) {
            return [
                'success' => false,
                'message' => 'No se proporcionó un número de documento.'
            ];
        }
        
        $empleado = \app\models\Empleados::findOne(['ci' => $ci]);
        if (!$empleado) {
            return [
                'success' => false,
                'message' => 'El número de documento proporcionado no está registrado en nuestro sistema.'
            ];
        }
        
        // Verificar si ya existe un usuario con este empleado_id
        $user = \app\models\Users::findOne(['empleado_id' => $empleado->empleado_id]);
        if ($user) {
            // Verificar si el usuario está marcado como eliminado
            if ($user->is_deleted == 1) {
                return [
                    'success' => true,
                    'message' => 'Documento verificado correctamente. Se restaurará la cuenta eliminada.',
                    'nombre' => $empleado->nombre,
                    'is_deleted' => true
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Ya existe una cuenta activa asociada a este documento de identidad.'
                ];
            }
        }
        
        return [
            'success' => true,
            'message' => 'Documento verificado correctamente.',
            'nombre' => $empleado->nombre,
            'is_deleted' => false
        ];
    }

    /**
     * Send verification code via AJAX.
     *
     * @return \yii\web\Response
     */
    public function actionSendVerification()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        // Leer los datos del cuerpo de la solicitud JSON
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $email = $data['email'] ?? null;
        
        if (!$email) {
            return [
                'success' => false,
                'message' => 'No se proporcionó un correo electrónico.'
            ];
        }
        
        // Verificar si el correo ya está registrado
        $user = \app\models\Users::findOne(['email' => $email]);
        if ($user) {
            return [
                'success' => false,
                'message' => 'Este correo ya está registrado en nuestro sistema.'
            ];
        }
        
        // Generar y enviar código de verificación
        $model = new \app\models\SignupForm();
        $model->email = $email;
        
        if ($model->sendVerificationCode()) {
            return [
                'success' => true,
                'message' => 'Código de verificación enviado correctamente.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al enviar el código de verificación. Inténtelo de nuevo.'
            ];
        }
    }

    /**
     * Register user and generate verification code after step 2.
     *
     * @return \yii\web\Response
     */
    public function actionRegisterUser()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        // Leer los datos del cuerpo de la solicitud JSON
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        
        if (!isset($data['ci']) || !isset($data['email']) || !isset($data['password'])) {
            return [
                'success' => false,
                'message' => 'Datos incompletos para el registro'
            ];
        }
        
        // Verificar que el CI exista en la tabla de empleados
        $empleado = \app\models\Empleados::findOne(['ci' => $data['ci']]);
        if (!$empleado) {
            return [
                'success' => false,
                'message' => 'El número de documento no está registrado'
            ];
        }
        
        // Verificar si ya existe un usuario con este empleado_id
        $userExistente = \app\models\Users::findOne(['empleado_id' => $empleado->empleado_id]);
        
        // Verificar si el correo ya está en uso por otra cuenta activa
        $emailExistente = \app\models\Users::findOne(['email' => $data['email'], 'is_deleted' => 0]);
        if ($emailExistente && (!$userExistente || $emailExistente->user_id != $userExistente->user_id)) {
            return [
                'success' => false,
                'message' => 'Este correo electrónico ya está registrado por otra cuenta'
            ];
        }
        
        // Si existe un usuario con este empleado_id pero no está marcado como eliminado, es un error
        if ($userExistente && $userExistente->is_deleted != 1) {
            return [
                'success' => false,
                'message' => 'Ya existe una cuenta activa asociada a este documento de identidad'
            ];
        }
        
        // Generar código de verificación aleatorio de 6 dígitos
        $code = sprintf("%06d", mt_rand(1, 999999));
        
        if ($userExistente && $userExistente->is_deleted == 1) {
            // Restaurar usuario previamente eliminado
            $userExistente->email = $data['email'];
            $userExistente->password_hash = Yii::$app->security->generatePasswordHash($data['password']);
            $userExistente->state = 0; // inactivo hasta verificación
            $userExistente->is_deleted = 0; // ya no está eliminado
            $userExistente->auth_key = Yii::$app->security->generateRandomString();
            $userExistente->verification_code = $code;
            $userExistente->is_verified = 0; // requiere verificación nuevamente
            
            // Guardar el usuario restaurado
            if (!$userExistente->save()) {
                return [
                    'success' => false,
                    'message' => 'Error al restaurar el usuario: ' . json_encode($userExistente->errors)
                ];
            }
            
            $user = $userExistente;
        } else {
            // Registrar un nuevo usuario
            $user = new \app\models\Users();
            $user->empleado_id = $empleado->empleado_id;
            $user->email = $data['email'];
            $user->password_hash = Yii::$app->security->generatePasswordHash($data['password']);
            $user->state = 0; // inactivo
            $user->rol_id = 3; // Rol por defecto (ajustar según corresponda)
            $user->auth_key = Yii::$app->security->generateRandomString();
            $user->is_verified = 0; // No verificado
            $user->tfa_on = 0; // Por defecto desactivado
            $user->tfa_code = '000000'; // Código inicial inactivo
            $user->tfa_vence = date('Y-m-d H:i:s'); // Fecha actual (ya vencido)
            $user->verification_code = $code;
            $user->is_deleted = 0;
            
            // Guardar el usuario
            if (!$user->save()) {
                return [
                    'success' => false,
                    'message' => 'Error al registrar el usuario: ' . json_encode($user->errors)
                ];
            }
        }
        
        // Enviar correo con el código de verificación usando la plantilla
        $sent = Yii::$app->mailer->compose('verification-code', ['code' => $code])
            ->setTo($user->email)
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
            ->setSubject('Código de Verificación - ' . Yii::$app->name)
            ->send();
            
        if (!$sent) {
            // Si no se pudo enviar el correo, aún así continuamos pero informamos
            return [
                'success' => true,
                'warning' => true,
                'message' => 'Usuario registrado pero hubo un problema al enviar el correo de verificación'
            ];
        }
        
        // Guardar datos en sesión para la verificación final
        Yii::$app->session->set('verification_user_id', $user->user_id);
        Yii::$app->session->set('verification_email', $user->email);
        
        $message = $userExistente && $userExistente->is_deleted == 1 ? 
            'Su cuenta ha sido restaurada. Se ha enviado un código de verificación a su correo.' : 
            'Usuario registrado correctamente. Se ha enviado un código de verificación a su correo.';
        
        return [
            'success' => true,
            'message' => $message
        ];
    }
    
    /**
     * Verify user code after registration.
     *
     * @return \yii\web\Response
     */
    public function actionVerifyUser()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        // Leer los datos del cuerpo de la solicitud JSON
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        
        if (!isset($data['code'])) {
            return [
                'success' => false,
                'message' => 'Código de verificación no proporcionado'
            ];
        }
        
        // Obtener el ID del usuario de la sesión
        $userId = Yii::$app->session->get('verification_user_id');
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'No se encontró información de registro. Por favor, inicie el proceso nuevamente.'
            ];
        }
        
        // Buscar el usuario
        $user = \app\models\Users::findOne($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }
        
        // Verificar el código
        if ($user->verification_code !== $data['code']) {
            return [
                'success' => false,
                'message' => 'El código de verificación es incorrecto'
            ];
        }
        
        // Actualizar el estado de verificación
        $user->is_verified = 1;
        $user->state = 1; // Activar el usuario
        
        if (!$user->save(false)) { // false para saltarse la validación
            return [
                'success' => false,
                'message' => 'Error al actualizar el estado de verificación'
            ];
        }
        
        // Establecer mensaje flash para la página de login con keep=true para asegurar que persista
        Yii::$app->session->setFlash('success', 'Gracias por registrarse. Su cuenta ha sido verificada correctamente. Ahora puede iniciar sesión.', true);
        
        // También establecer un mensaje en una variable de sesión normal para asegurar que persista
        Yii::$app->session->set('registration_success', true);
        
        // Limpiar todas las variables de sesión relacionadas con el registro
        Yii::$app->session->remove('verification_user_id');
        Yii::$app->session->remove('verification_email');
        Yii::$app->session->remove('verification_code');
        
        // Forzar que la sesión se guarde para asegurar que el mensaje flash permanece después de la redirección
        Yii::$app->session->close();
        
        return [
            'success' => true,
            'message' => 'Verificación completada correctamente',
            'redirect' => \yii\helpers\Url::to(['site/login'], true)
        ];
    }

    /**
     * Genera una constancia de trabajo
     */
    public function actionGenerarConstancia()
    {
        try {
            // Obtener el CI del usuario actual
            $user = Yii::$app->user->identity;
            if (!$user || !$user->empleado) {
                throw new \Exception('No se encontró información del empleado.');
            }
            $ci = $user->empleado->ci;
            
            $currentDate = date("Y-m-d");
            $currentMonth = date("m");
            $currentYear = date("Y");
            
            // Obtener la nómina seleccionada
            $nominaId = Yii::$app->request->get('nomina_id');
            if (!$nominaId) {
                throw new \Exception('No se ha seleccionado una nómina.');
            }

            // Registrar en historic_constancias
            $userId = Yii::$app->user->id;
            $historic = new \app\models\HistoricConstancias();
            $historic->user_id = $userId;
            $historic->mes = (int)$currentMonth;
            $historic->anio = (int)$currentYear;
            $historic->created_at = date('Y-m-d H:i:s');
            $historic->save(false);
            
            // Obtener el ID del registro histórico creado
            $historicId = $historic->id;

            // Consulta base reutilizable
            $baseQuery = (new \yii\db\Query())
                ->select([
                    'empleados.ci',
                    'empleados.nombre',
                    'empleados.fecha_ingreso',
                    'detalles_nomina.cargo',
                    'detalles_nomina.tipo_cargo',
                    'detalles_nomina.cesta_tickets',
                    'detalles_nomina.montopagar',
                    'nominas.nomina',
                    'detalles_nomina.mes',
                    'detalles_nomina.anio',
                    'detalles_nomina.periodo',
                    'detalles_nomina.total_a',
                    'bono_vac'
                ])
                ->from('empleados')
                ->innerJoin('detalles_nomina', 'empleados.empleado_id = detalles_nomina.empleado_id')
                ->innerJoin('nominas', 'nominas.nomina_id = detalles_nomina.nomina_id')
                ->where(['empleados.ci' => $ci, 'detalles_nomina.periodo' => 2, 'detalles_nomina.nomina_id' => $nominaId]);

            // Obtener los datos de la nómina seleccionada
            $data = $baseQuery->one();

            if (!$data) {
                throw new \Exception('No se encontraron datos para la nómina seleccionada.');
            }

            // CONDICIÓN: Verificar si es ALTO_NIVEL para usar proceso diferente
            if ($data['nomina'] === 'ALTO_NIVEL') {
                return $this->generarConstanciaAltoNivel($data, $historicId, $currentDate);
            }

            // Obtener datos procesados una sola vez
            $empleadoData = $this->getEmpleado_Valores($data);
            $financialData = $this->getFinnancialData($data);
            $directorData = $this->getDirectorData($data);

            if (!$directorData) {
                Yii::$app->session->setFlash('error', '¡No se ha podido generar la constancia debido a que no hay director activo!');
                return $this->redirect(['index']);
            }

            // Formateo de montos
            $formatter = new NumeroALetras();
            $formatter->apocope = true;

            // Generar contenido del PDF
            $htmlContent = $this->prepareHtmlContent([
                'empleadoData' => $empleadoData,
                'financialData' => $financialData,
                'directorData' => $directorData,
                'data' => $data,
                'formatter' => $formatter,
                'currentDate' => $currentDate,
                'historicId' => $historicId
            ]);

            // Limpiar cualquier salida previa
            Yii::$app->response->clear();
            
            // Configurar mPDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'dejavusans',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'tempDir' => Yii::getAlias('@runtime/mpdf')
            ]);
            
            $mpdf->WriteHTML($htmlContent);
            
            $fileName = 'CONSTANCIA_' . date("dmY") . '_' . $data['ci'] . '.pdf';

            // Establecer los headers correctos para la descarga
            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->headers->set('Content-Type', 'application/pdf');
            Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            Yii::$app->response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            Yii::$app->response->headers->set('Pragma', 'no-cache');
            Yii::$app->response->headers->set('Expires', '0');
            Yii::$app->response->headers->set('Content-Transfer-Encoding', 'binary');

            // Establecer la cookie antes de enviar el archivo
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => 'fileDownload',
                'value' => 'true',
                'expire' => time() + 2,
            ]));

            // Enviar el PDF directamente
            return $mpdf->Output('', 'S');
        } catch (\Exception $e) {
            Yii::error('Error al generar PDF: ' . $e->getMessage());
            throw new \yii\web\HttpException(500, 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    /**
     * Genera constancia para personal de ALTO_NIVEL
     */
    private function generarConstanciaAltoNivel($data, $historicId, $currentDate) 
    {
        try {
            // Obtener datos procesados una sola vez
            $empleadoData = $this->getEmpleado_Valores($data);
            $financialData = $this->getFinnancialData($data);
            $procuradorData = $this->getProcuradorData($data);

            if (!$procuradorData) {
                Yii::$app->session->setFlash('error', '¡No se ha podido generar la constancia debido a que no hay procurador activo!');
                return $this->redirect(['index']);
            }

            // Formateo de montos
            $formatter = new NumeroALetras();
            $formatter->apocope = true;

            // Generar contenido del PDF usando plantilla para alto nivel
            $htmlContent = $this->prepareHtmlContentAltoNivel([
                'empleadoData' => $empleadoData,
                'financialData' => $financialData,
                'procuradorData' => $procuradorData,
                'data' => $data,
                'formatter' => $formatter,
                'currentDate' => $currentDate,
                'historicId' => $historicId
            ]);

            // Configurar y generar PDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'dejavusans',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'tempDir' => Yii::getAlias('@runtime/mpdf')
            ]);
            
            $mpdf->WriteHTML($htmlContent);
            
            $fileName = 'CONSTANCIA_' . date("dmY") . '_' . $data['ci'] . '.pdf';

            // Establecer headers para descarga
            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->headers->set('Content-Type', 'application/pdf');
            Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            Yii::$app->response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            Yii::$app->response->headers->set('Pragma', 'no-cache');
            Yii::$app->response->headers->set('Expires', '0');
            Yii::$app->response->headers->set('Content-Transfer-Encoding', 'binary');

            // Establecer cookie
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => 'fileDownload',
                'value' => 'true',
                'expire' => time() + 2,
            ]));

            return $mpdf->Output('', 'S');
        } catch (\Exception $e) {
            Yii::error('Error al generar PDF: ' . $e->getMessage());
            throw new \yii\web\HttpException(500, 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    /**
     * Prepara el contenido HTML para constancias de alto nivel
     */
    private function prepareHtmlContentAltoNivel($params)
    {
        [$nombreEmpleado, $cedulaFormateada, $fechaActualLetras, $fechaActualNumeros] = $params['empleadoData'];
        [$salarioIntegral, $siLetras, $ctMonto, $ctLetras] = $params['financialData'];
        [$nombreProcurador, $resolucion, $fechares, $gaceta, $fechagac, $firmaObtenida, $firmaBase64] = $params['procuradorData'];

        // Formatear montos
        $salarioIntegralFormateado = number_format($salarioIntegral, 2, ',', '.');
        $ctMontoFormateado = number_format($ctMonto, 2, ',', '.');

        // Añadir "SIN CÉNTIMOS" si es necesario
        $siLetras .= (floor($salarioIntegral) == $salarioIntegral) ? " SIN CÉNTIMOS" : "";
        $ctLetras .= (floor($ctMonto) == $ctMonto) ? " SIN CÉNTIMOS" : "";

        // Obtener fecha actual en formato dd/mm/yyyy
        $fechaConstancia = date('d/m/Y');
        
        // Obtener el ID del registro histórico y otros datos para el QR
        $historicId = $params['historicId'] ?? 0;
        $mes = $params['data']['mes'] ?? date('m');
        $anio = $params['data']['anio'] ?? date('Y');
        $created_at = date('Y-m-d H:i:s');

        // Obtener el empleado_id del usuario logueado
        $empleadoId = Yii::$app->user->identity->empleado_id;

        // Generar el código QR
        $qrCode = $this->generateQrCode($historicId, $empleadoId, $mes, $anio, $created_at);

        $replacements = [
            '[NOMBRE PROCURADOR]' => $nombreProcurador,
            '[RESOLUCION]' => $resolucion,
            '[FECHA RESOLUCION]' => $fechares,
            '[GACETA]' => $gaceta,
            '[FECHA GACETA]' => $fechagac,
            '[NOMBRE EMPLEADO]' => $nombreEmpleado,
            '[ci]' => $cedulaFormateada,
            '[NOMINA]' => $this->getNomina($params['data']),
            '[SALARIO INTEGRAL EN LETRAS]' => $siLetras,
            '[SALARIO INTEGRAL]' => $salarioIntegralFormateado,
            '[CT LETRAS]' => $ctLetras,
            '[CT MONTO]' => $ctMontoFormateado,
            '[FECHA ACTUAL]' => $fechaActualLetras,
            '[FECHA INGRESO]' => $this->formatFechaIngreso($params['data']),
            '[FECHA ACTUAL NUMEROS]' => $fechaActualNumeros,
            '[Base64Firma]' => '<img src="' . $firmaBase64 . '" alt="Firma" style="max-width: 200px; height: auto;">',
            '[DATECONSTANCIA]' => $fechaConstancia,
            '[VALCODE]' => '<img src="' . $qrCode . '" alt="Código QR" style="width: 100px; height: 100px;">'
        ];

        $htmlContent = file_get_contents(Yii::getAlias('@app/templates/CONSTANCIA_ALTONIVEL.html'));
        return str_replace(array_keys($replacements), array_values($replacements), $htmlContent);
    }

    /**
     * Genera el código QR con la información de la constancia
     */
    private function generateQrCode($historicId, $empleadoId, $mes, $anio, $created_at)
    {
        // Crear el texto que contendrá el QR - Optimizamos las claves para reducir el tamaño
        $qrData = json_encode([
            'h' => $historicId,         // h = historic_id
            'e' => $empleadoId,         // e = empleado_id
            'm' => $mes,                // m = mes
            'a' => $anio,              // a = anio
            'c' => $created_at         // c = created_at
        ], JSON_UNESCAPED_SLASHES);

        // Configurar las opciones del QR
        $options = new QROptions([
            'version'      => QRGenerator::VERSION_AUTO, // Permitimos que se ajuste automáticamente
            'outputType'   => QRGenerator::OUTPUT_MARKUP_SVG,
            'eccLevel'     => QRGenerator::ECC_L,       // Cambiamos a nivel L para permitir más datos
            'scale'        => 8,
            'addQuietzone' => true,
            'quietzoneSize'=> 2,
            'cssClass'     => 'qr-code',
            'svgOpacity'   => 1,
            'svgDefs'      => '
                <style>
                    .qr-code { 
                        shape-rendering: crispEdges;
                        background-color: white;
                    }
                </style>
            ',
        ]);

        // Crear y retornar el QR
        $qrcode = new QRGenerator($options);
        return $qrcode->render($qrData);
    }

    private function prepareHtmlContent($params)
    {
        [$nombreEmpleado, $cedulaFormateada, $fechaActualLetras, $fechaActualNumeros] = $params['empleadoData'];
        [$salarioIntegral, $siLetras, $ctMonto, $ctLetras] = $params['financialData'];
        [$nombreDirector, $resolucion, $fechares, $gaceta, $fechagac, $firmaObtenida, $firmaBase64] = $params['directorData'];

        // Formatear montos
        $salarioIntegralFormateado = number_format($salarioIntegral, 2, ',', '.');
        $ctMontoFormateado = number_format($ctMonto, 2, ',', '.');

        // Añadir "SIN CÉNTIMOS" si es necesario
        $siLetras .= (floor($salarioIntegral) == $salarioIntegral) ? " SIN CÉNTIMOS" : "";
        $ctLetras .= (floor($ctMonto) == $ctMonto) ? " SIN CÉNTIMOS" : "";

        // Obtener fecha actual en formato dd/mm/yyyy
        $fechaConstancia = date('d/m/Y');
        
        // Obtener el ID del registro histórico y otros datos para el QR
        $historicId = $params['historicId'] ?? 0;
        $mes = $params['data']['mes'] ?? date('m');
        $anio = $params['data']['anio'] ?? date('Y');
        $created_at = date('Y-m-d H:i:s');

        // Obtener el empleado_id del usuario logueado
        $empleadoId = Yii::$app->user->identity->empleado_id;

        // Generar el código QR
        $qrCode = $this->generateQrCode($historicId, $empleadoId, $mes, $anio, $created_at);

        $replacements = [
            '[NOMBRE DIRECTOR]' => $nombreDirector,
            '[RESOLUCION]' => $resolucion,
            '[FECHA RESOLUCION]' => $fechares,
            '[GACETA]' => $gaceta,
            '[FECHA GACETA]' => $fechagac,
            '[NOMBRE EMPLEADO]' => $nombreEmpleado,
            '[ci]' => $cedulaFormateada,
            '[NOMINA]' => $this->getNomina($params['data']),
            '[SALARIO INTEGRAL EN LETRAS]' => $siLetras,
            '[SALARIO INTEGRAL]' => $salarioIntegralFormateado,
            '[CT LETRAS]' => $ctLetras,
            '[CT MONTO]' => $ctMontoFormateado,
            '[FECHA ACTUAL]' => $fechaActualLetras,
            '[FECHA INGRESO]' => $this->formatFechaIngreso($params['data']),
            '[FECHA ACTUAL NUMEROS]' => $fechaActualNumeros,
            '[Base64Firma]' => '<img src="' . $firmaBase64 . '" alt="Firma" style="max-width: 200px; height: auto;">',
            '[DATECONSTANCIA]' => $fechaConstancia,
            '[VALCODE]' => '<img src="' . $qrCode . '" alt="Código QR" style="width: 100px; height: 100px;">',
            '[IDCODE]' => $historicId
        ];

        $htmlContent = file_get_contents(Yii::getAlias('@app/templates/CONSTANCIA_V2.html'));
        return str_replace(array_keys($replacements), array_values($replacements), $htmlContent);
    }

    private function generatePdf($htmlContent, $ci)
    {
        try {
            // Limpiar cualquier salida previa
            Yii::$app->response->clear();
            
            // Configurar mPDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'dejavusans',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'tempDir' => Yii::getAlias('@runtime/mpdf')
            ]);
            
            $mpdf->WriteHTML($htmlContent);
            
            $fileName = 'CONSTANCIA_' . date("dmY") . '_' . $ci . '.pdf';

            // Establecer los headers correctos para la descarga
            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            Yii::$app->response->headers->set('Content-Type', 'application/pdf');
            Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            Yii::$app->response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            Yii::$app->response->headers->set('Pragma', 'no-cache');
            Yii::$app->response->headers->set('Expires', '0');
            Yii::$app->response->headers->set('Content-Transfer-Encoding', 'binary');

            // Establecer la cookie antes de enviar el archivo
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => 'fileDownload',
                'value' => 'true',
                'expire' => time() + 2,
            ]));

            // Enviar el PDF directamente
            return $mpdf->Output('', 'S');
        } catch (\Exception $e) {
            Yii::error('Error al generar PDF: ' . $e->getMessage());
            throw new \yii\web\HttpException(500, 'Error al generar el PDF');
        }
    }

    function getDirectorData($data) {
        $director = Directores::find()->where(['activo' => true])->one();
        if (!$director) {
            return null;
        }
        $nombreDirector = $director->nombre_director;
        $resolucion = $director->resolucion;
        $fechares = $director->fecha_resolucion ? date("d/m/Y", strtotime($director->fecha_resolucion)) : 'Fecha no disponible';
        $gaceta = $director->gaceta;
        $fechagac = $director->fecha_gaceta ? date("d/m/Y", strtotime($director->fecha_gaceta)) : 'Fecha no disponible';
        $firmaObtenida = $director->firma_base64;
        $firmaBase64 = "data:image/png;base64," . $firmaObtenida;
        return [$nombreDirector, $resolucion, $fechares, $gaceta, $fechagac, $firmaObtenida, $firmaBase64];
    }

    function getNomina($data) {
        // Mapeo de valores de nómina
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

        $nomina = $mapaNominas[$data['nomina']] ?? $data['nomina'];
        return $nomina;
    }

    function formatFechaIngreso($data) {
        // Mapeo directo de meses en español
        $meses = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre'
        ];
        
        $fecha = new DateTime($data['fecha_ingreso']);
        $dia = $fecha->format('d');
        $mes = $meses[(int)$fecha->format('m')];
        $anio = $fecha->format('Y');
        
        return mb_strtolower("{$dia} de {$mes} de {$anio}");
    }

    function getFinnancialData($data) {
        // Formateo de montos a texto
        $formatter = new NumeroALetras();
        $formatter->apocope = true;

        // Fórmula Salario Integral = Salario Básico + Alicuota de Utilidades + Bono vacacional
        $ctMonto = $data['cesta_tickets'];
        $totalAsignaciones = $data['total_a'];
        $vac_bono = $data['bono_vac'];
        $salarioIntegral = ($totalAsignaciones + $vac_bono) * 2;

        // Convertir salario integral a letras
        $siLetras = $formatter->toMoney($salarioIntegral, 2, ($salarioIntegral > 1000000) ? 'DE BOLÍVARES' : 'BOLÍVARES', 'CÉNTIMOS');
        $montoDecimal = $salarioIntegral - floor($salarioIntegral);
        if ($montoDecimal == 0) {
            $siLetras = str_replace('CÉNTIMOS', 'SIN CÉNTIMOS', $siLetras);
        }

        // Formatear el monto de cesta de tickets
        $ctLetras = $formatter->toMoney($ctMonto, 2, ($ctMonto > 1000000) ? 'DE BOLÍVARES' : 'BOLÍVARES', 'CÉNTIMOS');
        $ctDecimal = $ctMonto - floor($ctMonto);
        if ($ctDecimal == 0) {
            $ctLetras = str_replace('CÉNTIMOS', 'SIN CÉNTIMOS', $ctLetras);
        }

        return [$salarioIntegral, $siLetras, $ctMonto, $ctLetras];
    }

    function getEmpleado_Valores($data) {
        // Obtener valores
        $nombreEmpleado = $data['nombre'];
        $cedulaEmpleado = $data['ci'];
        // Formatear la cédula con separadores de miles
        $cedulaEmpleadoFormateada = number_format($cedulaEmpleado, 0, '', '.');
        $cedulaFormateada = $cedulaEmpleadoFormateada;
        $fechaActualLetras = $this->fechaEnLetras(date("d/m/Y"));
        $fechaActualNumeros = date("d/m/Y");

        return [$nombreEmpleado, $cedulaFormateada, $fechaActualLetras, $fechaActualNumeros];
    }

    function fechaEnLetras($fecha) {
        $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        $fechaConvertida = DateTime::createFromFormat('d/m/Y', $fecha);
        if (!$fechaConvertida) {
            return "fecha inválida";
        }
        $date = $fechaConvertida;
        $diaNum = $date->format('d');
        $diaTexto = $formatter->format($diaNum);
        
        // Mapeo directo de meses en español
        $meses = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre'
        ];
        
        $mesNum = (int)$date->format('m');
        $mesTexto = $meses[$mesNum];
        
        $anioNum = $date->format('Y');
        $anioTexto = $formatter->format($anioNum);
        $anioFormateado = number_format($anioNum, 0, '', '.');
        $resultado = "{$diaTexto} ({$diaNum}) días del mes de {$mesTexto} del año {$anioFormateado}";
        return mb_strtolower($resultado);
    }

    /**
     * Obtiene los datos del procurador activo
     */
    function getProcuradorData($data) {
        $procurador = Procuradores::find()->where(['activo' => true])->one();
        if (!$procurador) {
            return null;
        }
        $nombreProcurador = $procurador->nombre;
        $resolucion = $procurador->resolucion;
        $fechares = $procurador->fecha_resolucion ? date("d/m/Y", strtotime($procurador->fecha_resolucion)) : 'Fecha no disponible';
        $gaceta = $procurador->gaceta;
        $fechagac = $procurador->fecha_gaceta ? date("d/m/Y", strtotime($procurador->fecha_gaceta)) : 'Fecha no disponible';
        $firmaObtenida = $procurador->firma_base64;
        $firmaBase64 = "" . $firmaObtenida;
        return [$nombreProcurador, $resolucion, $fechares, $gaceta, $fechagac, $firmaObtenida, $firmaBase64];
    }

    /**
     * Verifica si hay directores y procuradores activos en el sistema
     * @return \yii\web\Response
     */
    public function actionVerificarActivos()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        // Verificar directores activos
        $hayDirectoresActivos = \app\models\Directores::find()
            ->where(['activo' => 1])
            ->exists();
            
        // Verificar procuradores activos
        $hayProcuradoresActivos = \app\models\Procuradores::find()
            ->where(['activo' => 1])
            ->exists();
            
        return [
            'hayDirectoresActivos' => $hayDirectoresActivos,
            'hayProcuradoresActivos' => $hayProcuradoresActivos,
        ];
    }

    /**
     * Solicitud de recuperación de contraseña
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            $data = Yii::$app->request->post();
            $model->email = $data['email'] ?? '';
            
            if ($model->validate(['email'])) {
                if ($model->sendEmail()) {
                    return [
                        'success' => true,
                        'message' => 'Se ha enviado un código de verificación a su correo electrónico.'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'No se pudo enviar el código de verificación. Por favor intente nuevamente.'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => $model->getFirstError('email')
                ];
            }
        }
        
        return $this->renderPartial('request-password-reset', [
            'model' => $model
        ]);
    }

    /**
     * Verifica el código de recuperación de contraseña
     *
     * @return mixed
     */
    public function actionVerifyResetCode()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model = new PasswordResetRequestForm();
        $model->email = Yii::$app->request->post('email');
        $model->verification_code = Yii::$app->request->post('code');
        
        if ($model->verifyCode()) {
            $user = Users::findOne([
                'email' => $model->email,
                'verification_code' => $model->verification_code,
                'is_deleted' => 0
            ]);
            
            return [
                'success' => true,
                'nombre' => $user->empleado->nombre,
                'email' => $user->email
            ];
        }
        
        return [
            'success' => false,
            'message' => 'El código de verificación no es válido'
        ];
    }

    /**
     * Cambia la contraseña del usuario
     *
     * @return mixed
     */
    public function actionResetPassword()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model = new PasswordResetRequestForm();
        $model->email = Yii::$app->request->post('email');
        $model->verification_code = Yii::$app->request->post('code');
        $model->password = Yii::$app->request->post('password');
        $model->password_confirm = Yii::$app->request->post('password');
        
        if ($model->validate(['password', 'password_confirm'])) {
            if ($model->resetPassword()) {
                Yii::$app->session->setFlash('success', 'Su contraseña ha sido cambiada correctamente. Puede proceder a iniciar sesión.');
                return [
                    'success' => true
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Hubo un error al cambiar la contraseña'
        ];
    }

    /**
     * Acción para eliminar la foto de perfil del usuario
     */
    public function actionEliminarFotoPerfil()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            
            try {
                $user->foto_perfil = null;
                if ($user->save()) {
                    return [
                        'success' => true,
                        'message' => 'Foto de perfil eliminada correctamente'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Error al eliminar la foto de perfil'
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Usuario no autenticado'
        ];
    }

    /**
     * Acción para subir la foto de perfil del usuario
     */
    public function actionSubirFotoPerfil()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            
            try {
                // Verificar si se recibió la imagen
                if (!isset($_POST['imagen'])) {
                    throw new \Exception('No se recibió ninguna imagen');
                }

                // Obtener la imagen base64
                $imagen_base64 = $_POST['imagen'];
                
                // Verificar que la imagen sea válida
                if (!preg_match('/^data:image\/(jpeg|png|gif);base64,/', $imagen_base64)) {
                    throw new \Exception('Formato de imagen no válido');
                }

                // Decodificar la imagen base64
                $imagen_data = base64_decode(preg_replace('/^data:image\/(jpeg|png|gif);base64,/', '', $imagen_base64));
                
                if (!$imagen_data) {
                    throw new \Exception('La imagen no es válida');
                }

                // Crear directorio si no existe
                $upload_dir = Yii::getAlias('@webroot/upload/profiles/');
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Generar nombre único para la imagen
                $timestamp = date('YmdHis');
                $filename = $user->user_id . '_' . $timestamp . '.jpg';
                $filepath = $upload_dir . $filename;

                // Guardar la imagen
                if (file_put_contents($filepath, $imagen_data)) {
                    // Actualizar la ruta en la base de datos
                    $user->foto_perfil = '/upload/profiles/' . $filename;
                    
                    if ($user->save()) {
                        return [
                            'success' => true,
                            'message' => 'Foto de perfil actualizada correctamente',
                            'foto_perfil' => $user->foto_perfil
                        ];
                    } else {
                        // Si falla el guardado, eliminar la imagen
                        unlink($filepath);
                        throw new \Exception('Error al guardar la información en la base de datos');
                    }
                } else {
                    throw new \Exception('Error al guardar la imagen');
                }
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Usuario no autenticado'
        ];
    }
}
