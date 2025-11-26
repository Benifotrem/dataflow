<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Mensual Dataflow</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.95;
        }
        .content {
            padding: 30px 25px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #1f2937;
        }
        .alert-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-box strong {
            color: #92400e;
            display: block;
            margin-bottom: 5px;
        }
        .alert-box p {
            margin: 5px 0;
            color: #78350f;
            font-size: 14px;
        }
        .info-box {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 5px 0;
            color: #1e40af;
            font-size: 14px;
        }
        .instructions {
            margin: 25px 0;
        }
        .instructions h2 {
            color: #1f2937;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .step {
            margin: 15px 0;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 6px;
            border-left: 3px solid #6366f1;
        }
        .step-number {
            display: inline-block;
            width: 28px;
            height: 28px;
            background: #6366f1;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 28px;
            font-weight: bold;
            margin-right: 10px;
            font-size: 14px;
        }
        .step-content {
            display: inline-block;
            vertical-align: top;
            width: calc(100% - 50px);
        }
        .step strong {
            color: #4b5563;
            font-size: 15px;
        }
        .step p {
            margin: 5px 0 0 0;
            color: #6b7280;
            font-size: 14px;
        }
        .screenshot {
            margin: 10px 0;
            text-align: center;
            background-color: #f3f4f6;
            padding: 10px;
            border-radius: 4px;
        }
        .screenshot img {
            max-width: 100%;
            border-radius: 4px;
            border: 1px solid #d1d5db;
        }
        .screenshot-caption {
            font-size: 12px;
            color: #6b7280;
            font-style: italic;
            margin-top: 5px;
        }
        .tip-box {
            background-color: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .tip-box strong {
            color: #065f46;
            display: block;
            margin-bottom: 5px;
        }
        .tip-box p {
            margin: 5px 0;
            color: #047857;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: #6366f1;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            transition: background 0.3s;
        }
        .cta-button:hover {
            background: #4f46e5;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #6b7280;
        }
        .footer a {
            color: #6366f1;
            text-decoration: none;
        }
        .attachment-icon {
            font-size: 40px;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📦 Backup Mensual de Dataflow</h1>
            <p>{{ $monthName }} {{ $year }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Estimado/a <strong>{{ $tenantName }}</strong>,
            </div>

            <p>Te enviamos tu backup mensual de Dataflow correspondiente a <strong>{{ $monthName }} {{ $year }}</strong>.</p>

            <div class="attachment-icon">
                📎
            </div>

            <div class="info-box">
                <p><strong>📊 ARCHIVO ADJUNTO:</strong> Encontrarás un archivo Excel con todos tus documentos procesados durante este período.</p>
            </div>

            <div class="alert-box">
                <strong>⚠️ IMPORTANTE: Conserva este backup</strong>
                <p>En Dataflow solo conservamos tus datos durante <strong>2 meses</strong> por políticas de almacenamiento y protección de datos.</p>
                <p>Es fundamental que guardes este backup en tu Gmail o Google Drive para conservar tu historial completo contable.</p>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h2>📥 Instrucciones para Guardar tu Backup</h2>

                <div class="step">
                    <span class="step-number">1</span>
                    <div class="step-content">
                        <strong>Descarga el archivo Excel adjunto</strong>
                        <p>Haz clic en el archivo adjunto de este correo para descargarlo a tu computadora.</p>
                    </div>
                </div>

                <div class="screenshot">
                    <img src="{{ asset('images/email/backup-step1.png') }}" alt="Paso 1: Descargar archivo adjunto" style="max-width: 100%;">
                    <p class="screenshot-caption">Haz clic en el archivo adjunto para descargarlo</p>
                </div>

                <div class="step">
                    <span class="step-number">2</span>
                    <div class="step-content">
                        <strong>Accede a Google Drive</strong>
                        <p>Ve a <a href="https://drive.google.com" style="color: #6366f1;">drive.google.com</a> e inicia sesión con tu cuenta de Gmail.</p>
                    </div>
                </div>

                <div class="screenshot">
                    <img src="{{ asset('images/email/backup-step2.png') }}" alt="Paso 2: Google Drive" style="max-width: 100%;">
                    <p class="screenshot-caption">Accede a tu Google Drive</p>
                </div>

                <div class="step">
                    <span class="step-number">3</span>
                    <div class="step-content">
                        <strong>Crea una carpeta para tus backups</strong>
                        <p>Si no la tienes, crea una carpeta llamada "Backups Dataflow" haciendo clic derecho → "Nueva carpeta".</p>
                    </div>
                </div>

                <div class="screenshot">
                    <img src="{{ asset('images/email/backup-step3.png') }}" alt="Paso 3: Crear carpeta" style="max-width: 100%;">
                    <p class="screenshot-caption">Crea una carpeta "Backups Dataflow"</p>
                </div>

                <div class="step">
                    <span class="step-number">4</span>
                    <div class="step-content">
                        <strong>Sube el archivo Excel a la carpeta</strong>
                        <p>Arrastra el archivo descargado a la carpeta "Backups Dataflow" o haz clic en "Nuevo" → "Subir archivo".</p>
                    </div>
                </div>

                <div class="screenshot">
                    <img src="{{ asset('images/email/backup-step4.png') }}" alt="Paso 4: Subir archivo" style="max-width: 100%;">
                    <p class="screenshot-caption">Arrastra o sube el archivo a tu carpeta</p>
                </div>

                <div class="step">
                    <span class="step-number">5</span>
                    <div class="step-content">
                        <strong>(Opcional) Etiqueta este correo en Gmail</strong>
                        <p>En Gmail, puedes crear una etiqueta "Backup Dataflow" para encontrar estos correos fácilmente en el futuro.</p>
                    </div>
                </div>

                <div class="screenshot">
                    <img src="{{ asset('images/email/backup-step5.png') }}" alt="Paso 5: Etiquetar correo" style="max-width: 100%;">
                    <p class="screenshot-caption">Etiqueta el correo para encontrarlo fácilmente</p>
                </div>
            </div>

            <div class="tip-box">
                <strong>💡 TIP PROFESIONAL:</strong>
                <p>También puedes <strong>archivar este email en Gmail</strong> sin eliminarlo. Así siempre podrás volver a descargar el archivo Excel cuando lo necesites sin tener que buscarlo en Google Drive.</p>
                <p>Para archivar: Selecciona el email y haz clic en el icono de archivo (📥) en la barra superior de Gmail.</p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="https://dataflow.guaraniappstore.com" class="cta-button">Acceder a Dataflow</a>
            </div>

            <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
                ¿Necesitas ayuda o tienes alguna pregunta? No dudes en contactarnos respondiendo a este correo.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Dataflow</strong> - Sistema de Gestión Documental y Contable</p>
            <p>Este es un email automático enviado el día 20 de cada mes.</p>
            <p style="margin-top: 15px;">
                <a href="https://dataflow.guaraniappstore.com">Ir a Dataflow</a> •
                <a href="https://dataflow.guaraniappstore.com/faq">Centro de Ayuda</a>
            </p>
            <p style="font-size: 11px; color: #9ca3af; margin-top: 15px;">
                © {{ date('Y') }} Dataflow. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
