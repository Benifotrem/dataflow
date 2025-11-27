<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio Fiscal</title>
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
            background: {{ $isUrgent ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)' }};
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header-icon {
            font-size: 48px;
            margin-bottom: 10px;
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
        .event-box {
            background: {{ $isUrgent ? '#fee2e2' : '#f3f4f6' }};
            border-left: 4px solid {{ $isUrgent ? '#ef4444' : '#6366f1' }};
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .event-box h2 {
            margin: 0 0 10px 0;
            color: {{ $isUrgent ? '#991b1b' : '#1e40af' }};
            font-size: 20px;
        }
        .event-box p {
            margin: 5px 0;
            color: {{ $isUrgent ? '#7f1d1d' : '#1f2937' }};
        }
        .countdown-box {
            background: {{ $isUrgent ? '#fef2f2' : '#ede9fe' }};
            border: 2px solid {{ $isUrgent ? '#fca5a5' : '#a78bfa' }};
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }
        .countdown-number {
            font-size: 48px;
            font-weight: bold;
            color: {{ $isUrgent ? '#dc2626' : '#6366f1' }};
            line-height: 1;
        }
        .countdown-text {
            font-size: 14px;
            color: {{ $isUrgent ? '#991b1b' : '#4f46e5' }};
            margin-top: 5px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin: 20px 0;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            padding: 10px 15px 10px 0;
            font-weight: 600;
            color: #6b7280;
            width: 40%;
        }
        .info-value {
            display: table-cell;
            padding: 10px 0;
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
        .checklist {
            background: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .checklist h3 {
            margin: 0 0 15px 0;
            color: #1f2937;
            font-size: 16px;
        }
        .checklist-item {
            padding: 8px 0;
            color: #4b5563;
            font-size: 14px;
        }
        .checklist-item:before {
            content: "☐ ";
            color: #6366f1;
            font-weight: bold;
            margin-right: 8px;
        }
        .cta-button {
            display: inline-block;
            background: {{ $isUrgent ? '#ef4444' : '#6366f1' }};
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .cta-button:hover {
            background: {{ $isUrgent ? '#dc2626' : '#4f46e5' }};
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-icon">{{ $isUrgent ? '🚨' : '📅' }}</div>
            <h1>{{ $isUrgent ? 'Recordatorio Urgente' : 'Recordatorio Fiscal' }}</h1>
            <p>{{ $eventType }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Estimado/a <strong>{{ $tenantName }}</strong>,
            </div>

            <p>Te recordamos que se acerca una fecha importante en tu calendario fiscal:</p>

            <div class="event-box">
                <h2>{{ $eventTitle }}</h2>
                <p>{{ $eventDescription }}</p>
            </div>

            <div class="countdown-box">
                <div class="countdown-number">{{ $daysUntil }}</div>
                <div class="countdown-text">{{ $daysUntil === 1 ? 'Día Restante' : 'Días Restantes' }}</div>
            </div>

            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">📅 Fecha límite:</div>
                    <div class="info-value"><strong>{{ $eventDate }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">🏷️ Tipo de evento:</div>
                    <div class="info-value">{{ $eventType }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">🌍 País:</div>
                    <div class="info-value">{{ $countryCode }}</div>
                </div>
            </div>

            @if($isUrgent)
            <div class="alert-box">
                <strong>⚠️ ¡Acción requerida pronto!</strong>
                <p>Quedan menos de 3 días para el vencimiento. Asegúrate de tener toda la documentación preparada.</p>
            </div>
            @endif

            <div class="checklist">
                <h3>✓ Checklist de Preparación:</h3>
                <div class="checklist-item">Revisar todos los documentos del período en Dataflow</div>
                <div class="checklist-item">Verificar que todas las facturas estén procesadas</div>
                <div class="checklist-item">Exportar el reporte de liquidación si aplica</div>
                <div class="checklist-item">Consultar con tu contador si tienes dudas</div>
                <div class="checklist-item">Preparar el pago o transferencia correspondiente</div>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="https://dataflow.guaraniappstore.com/documents" class="cta-button">Ver Documentos en Dataflow</a>
            </div>

            <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
                <strong>Nota:</strong> Este recordatorio se genera automáticamente según tu calendario fiscal.
                Puedes gestionar tus eventos fiscales desde el panel de Dataflow.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Dataflow</strong> - Sistema de Gestión Documental y Contable</p>
            <p>Este es un email automático de recordatorio fiscal.</p>
            <p style="margin-top: 15px;">
                <a href="https://dataflow.guaraniappstore.com">Ir a Dataflow</a> •
                <a href="https://dataflow.guaraniappstore.com/fiscal-events">Ver Calendario Fiscal</a>
            </p>
            <p style="font-size: 11px; color: #9ca3af; margin-top: 15px;">
                © {{ date('Y') }} Dataflow. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
