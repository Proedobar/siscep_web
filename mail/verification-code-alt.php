<?php
/* @var $code string */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <!-- Encabezado con logo y nombre de la institución -->
        <div style="text-align: center; margin-bottom: 30px;">
            <table style="width: 100%; margin-bottom: 20px;">
                <tr>
                    <td style="width: 80px; vertical-align: middle; text-align: center;">
                        <img src="<?= Yii::$app->params['baseUrl'] ?>/logo.png" alt="Logo" style="max-width: 50px; height: auto;">
                    </td>
                    <td style="vertical-align: middle; text-align: left; padding-left: 15px;">
                        <div style="font-size: 18px; font-weight: bold; color: #2c3e50;">PROCURADURIA GENERAL DEL ESTADO BARINAS</div>
                        <div style="font-size: 14px; color: #777;">Sistema de Constancias y Emisión de Pagos - SISCEP</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tarjeta principal -->
        <div style="background: #ffffff; border-radius: 10px; padding: 30px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <h2 style="color: #2c3e50; font-size: 24px; margin-bottom: 20px; text-align: center;">Código de Verificación</h2>
            
            <p style="color: #666; line-height: 1.6; margin-bottom: 25px; text-align: center; font-size: 16px;">
                Hemos recibido una solicitud de verificación para tu cuenta. Por favor, usa el siguiente código:
            </p>
            
            <!-- Código de verificación con estilo -->
            <div style="margin: 30px 0; text-align: center;">
                <div style="display: inline-block; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 15px 40px; letter-spacing: 5px;">
                    <span style="font-size: 32px; font-weight: bold; color: #3498db;"><?= $code ?></span>
                </div>
            </div>
            
            <p style="color: #666; line-height: 1.6; text-align: center; font-size: 14px;">
                Este código expirará en 10 minutos. Si no has solicitado este código, por favor ignora este mensaje.
            </p>
            
            <div style="margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
                <p style="color: #999; font-size: 12px;">
                    &copy; <?= date('Y') ?> PROCURADURIA GENERAL DEL ESTADO BARINAS - Todos los derechos reservados
                </p>
            </div>
        </div>
    </div>
</body>
</html> 