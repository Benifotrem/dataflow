@extends('layouts.landing')

@section('content')
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6 max-w-4xl">
        <h1 class="text-4xl font-bold mb-8">Política de Privacidad y Tratamiento de Datos</h1>
        <div class="bg-white rounded-lg shadow-md p-8 prose prose-lg max-w-none">
            <p class="text-gray-600 mb-6"><strong>Última actualización:</strong> Noviembre 2025</p>
            
            <h2 class="text-2xl font-bold mt-8 mb-4">1. Introducción</h2>
            <p>En Dataflow, nos tomamos muy en serio la protección de tus datos personales. Esta política explica qué información recopilamos, cómo la usamos, cómo la protegemos y cuáles son tus derechos.</p>
            <p>Cumplimos con GDPR (Reglamento General de Protección de Datos) y las normativas de protección de datos de cada jurisdicción en la que operamos.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">2. Responsable del Tratamiento</h2>
            <p><strong>Dataflow S.L.</strong><br>
            Email: privacy@dataflow.com<br>
            Dirección: [Dirección]</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">3. Datos que Recopilamos</h2>
            
            <h3 class="text-xl font-bold mt-6 mb-3">3.1 Datos de Registro</h3>
            <ul class="list-disc pl-6 mb-4">
                <li>Nombre y apellidos</li>
                <li>Correo electrónico</li>
                <li>Empresa u organización</li>
                <li>País y moneda</li>
                <li>Contraseña (encriptada)</li>
            </ul>

            <h3 class="text-xl font-bold mt-6 mb-3">3.2 Datos Contables y Fiscales</h3>
            <ul class="list-disc pl-6 mb-4">
                <li>Documentos contables (facturas, recibos, extractos)</li>
                <li>Transacciones contables</li>
                <li>Información fiscal (NIF, CIF, RFC, etc.)</li>
                <li>Configuración fiscal por jurisdicción</li>
            </ul>

            <h3 class="text-xl font-bold mt-6 mb-3">3.3 Datos Técnicos</h3>
            <ul class="list-disc pl-6 mb-4">
                <li>Dirección IP</li>
                <li>Tipo de navegador</li>
                <li>Páginas visitadas</li>
                <li>Fecha y hora de acceso</li>
            </ul>

            <h2 class="text-2xl font-bold mt-8 mb-4">4. Cómo Usamos tus Datos</h2>
            <ul class="list-disc pl-6 mb-4">
                <li><strong>Proveer el servicio:</strong> Procesar documentos, clasificar transacciones, generar reportes</li>
                <li><strong>Facturación:</strong> Gestionar suscripciones y pagos</li>
                <li><strong>Soporte:</strong> Responder a tus consultas y resolver problemas</li>
                <li><strong>Mejoras:</strong> Analizar el uso de la plataforma para mejorar funcionalidades</li>
                <li><strong>Comunicaciones:</strong> Enviar notificaciones importantes sobre el servicio</li>
            </ul>

            <h2 class="text-2xl font-bold mt-8 mb-4">5. Servicios de Terceros</h2>
            <p>Utilizamos los siguientes servicios de terceros:</p>
            <ul class="list-disc pl-6 mb-4">
                <li><strong>OpenAI / OpenRoute:</strong> Para procesamiento OCR e inteligencia artificial</li>
                <li><strong>Brevo:</strong> Para envío de correos transaccionales</li>
                <li><strong>Procesador de pagos:</strong> Para gestionar pagos de forma segura</li>
            </ul>
            <p>Estos proveedores están obligados contractualmente a proteger tus datos y solo pueden usarlos para los fines específicos que les hemos autorizado.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">6. Política de Retención de Datos Bancarios</h2>
            <p class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <strong>⚠️ IMPORTANTE:</strong> Los extractos bancarios que subas se almacenan por un máximo de 60 días desde el fin del mes en curso. Tras este período, los archivos se eliminan física y lógicamente de forma automática e irreversible.
            </p>
            <p>Esta medida de seguridad protege tus datos bancarios sensibles. Las transacciones extraídas de los extractos se conservan, pero no el documento original.</p>
            <p>Recibirás notificaciones claras sobre esta política al subir extractos bancarios.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">7. Seguridad de los Datos</h2>
            <p>Implementamos medidas de seguridad técnicas y organizativas para proteger tus datos:</p>
            <ul class="list-disc pl-6 mb-4">
                <li><strong>Encriptación:</strong> SSL/TLS para transmisión, encriptación en reposo para datos sensibles</li>
                <li><strong>Arquitectura Multi-Tenant:</strong> Aislamiento total entre organizaciones</li>
                <li><strong>Control de acceso:</strong> Autenticación de dos factores disponible</li>
                <li><strong>Auditoría:</strong> Registro de acciones sensibles</li>
                <li><strong>Backups:</strong> Copias de seguridad diarias encriptadas</li>
            </ul>

            <h2 class="text-2xl font-bold mt-8 mb-4">8. Política de No Conexión Bancaria</h2>
            <p>Por seguridad, NO nos conectamos directamente con APIs bancarias. Solo permitimos carga manual de extractos. Esto significa que nunca almacenamos tus credenciales bancarias ni tenemos acceso directo a tus cuentas.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">9. Tus Derechos (GDPR y normativas locales)</h2>
            <p>Tienes derecho a:</p>
            <ul class="list-disc pl-6 mb-4">
                <li><strong>Acceso:</strong> Solicitar una copia de tus datos personales</li>
                <li><strong>Rectificación:</strong> Corregir datos inexactos</li>
                <li><strong>Supresión:</strong> Eliminar tus datos ("derecho al olvido")</li>
                <li><strong>Portabilidad:</strong> Exportar tus datos en formato estructurado</li>
                <li><strong>Oposición:</strong> Oponerte a ciertos procesamientos</li>
                <li><strong>Limitación:</strong> Limitar el procesamiento en determinadas circunstancias</li>
            </ul>
            <p>Para ejercer estos derechos, contacta a: <a href="mailto:privacy@dataflow.com" class="text-purple-600">privacy@dataflow.com</a></p>

            <h2 class="text-2xl font-bold mt-8 mb-4">10. Transferencias Internacionales</h2>
            <p>Tus datos pueden ser transferidos y procesados en servidores fuera de tu país de residencia. En todos los casos, implementamos salvaguardas adecuadas para proteger tus datos, incluyendo cláusulas contractuales estándar aprobadas por la UE.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">11. Cookies y Tecnologías Similares</h2>
            <p>Utilizamos cookies esenciales para el funcionamiento de la plataforma y cookies analíticas para mejorar nuestro servicio. Puedes configurar tu navegador para rechazar cookies, pero esto puede afectar la funcionalidad del sitio.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">12. Cambios a esta Política</h2>
            <p>Podemos actualizar esta política ocasionalmente. Te notificaremos de cambios significativos por correo electrónico o mediante aviso en la plataforma.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">13. Contacto y Reclamaciones</h2>
            <p>Para preguntas sobre esta política o para ejercer tus derechos:</p>
            <p><strong>Email:</strong> <a href="mailto:privacy@dataflow.com" class="text-purple-600">privacy@dataflow.com</a></p>
            <p class="mt-4">También puedes presentar una reclamación ante la autoridad de protección de datos de tu país:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>España: Agencia Española de Protección de Datos (AEPD)</li>
                <li>México: Instituto Nacional de Transparencia (INAI)</li>
                <li>Argentina: Agencia de Acceso a la Información Pública (AAIP)</li>
                <li>[Otras jurisdicciones según corresponda]</li>
            </ul>
        </div>
    </div>
</section>
@endsection
