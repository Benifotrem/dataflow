<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversaciones Telegram - Dataflow</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .conversation {
            margin-bottom: 40px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        .conversation-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        .conversation-header h2 {
            margin: 0 0 5px 0;
            font-size: 20px;
            color: #667eea;
        }
        .conversation-info {
            font-size: 14px;
            color: #666;
        }
        .messages {
            padding: 20px;
        }
        .message {
            margin-bottom: 15px;
            padding: 12px 15px;
            border-radius: 8px;
        }
        .message-user {
            background-color: #e3f2fd;
            margin-left: 40px;
        }
        .message-assistant {
            background-color: #f1f8e9;
            margin-right: 40px;
        }
        .message-role {
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .message-user .message-role {
            color: #1976d2;
        }
        .message-assistant .message-role {
            color: #689f38;
        }
        .message-content {
            color: #333;
            white-space: pre-wrap;
        }
        .message-timestamp {
            font-size: 11px;
            color: #999;
            margin-top: 5px;
        }
        .stats {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Exportación de Conversaciones</h1>
        <p>Registro completo de interacciones con el Bot de Telegram</p>
        <p>{{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="container">
        <div class="stats">
            <strong>Total de conversaciones exportadas:</strong> {{ count($exports) }}
        </div>

        @foreach($exports as $export)
        <div class="conversation">
            <div class="conversation-header">
                <h2>👤 {{ $export['user'] }}</h2>
                <div class="conversation-info">
                    <strong>Email:</strong> {{ $export['email'] }} |
                    <strong>Chat ID:</strong> {{ $export['chat_id'] }} |
                    <strong>Mensajes:</strong> {{ $export['total_messages'] }}
                </div>
            </div>

            <div class="messages">
                @foreach($export['messages'] as $message)
                <div class="message {{ $message['role'] === 'Usuario' ? 'message-user' : 'message-assistant' }}">
                    <div class="message-role">{{ $message['role'] }}</div>
                    <div class="message-content">{{ $message['message'] }}</div>
                    <div class="message-timestamp">{{ $message['timestamp'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="footer">
            <p><strong>Dataflow - Sistema de Gestión Contable</strong></p>
            <p>Este email contiene información confidencial. Si lo recibiste por error, por favor elimínalo.</p>
        </div>
    </div>
</body>
</html>
