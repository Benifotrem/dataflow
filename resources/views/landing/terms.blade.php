@extends('layouts.landing')

@section('content')
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6 max-w-4xl">
        <h1 class="text-4xl font-bold mb-8">Términos y Condiciones</h1>
        <div class="bg-white rounded-lg shadow-md p-8 prose prose-lg max-w-none">
            <p class="text-gray-600 mb-6"><strong>Última actualización:</strong> Noviembre 2025</p>
            
            <h2 class="text-2xl font-bold mt-8 mb-4">1. Aceptación de los Términos</h2>
            <p>Al acceder y utilizar Dataflow, aceptas estar sujeto a estos Términos y Condiciones. Si no estás de acuerdo, no utilices la plataforma.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">2. Descripción del Servicio</h2>
            <p>Dataflow es una plataforma SaaS de automatización contable que utiliza inteligencia artificial para:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>Procesar documentos contables (facturas, recibos, extractos) mediante OCR</li>
                <li>Clasificar automáticamente transacciones</li>
                <li>Realizar conciliación bancaria</li>
                <li>Gestionar plazos fiscales</li>
                <li>Facilitar colaboración entre propietarios y asesores (Plan Avanzado)</li>
            </ul>

            <h2 class="text-2xl font-bold mt-8 mb-4">3. Planes y Suscripciones</h2>
            <h3 class="text-xl font-bold mt-6 mb-3">3.1 Planes Disponibles</h3>
            <ul class="list-disc pl-6 mb-4">
                <li><strong>Plan Básico (B2C):</strong> $19.99/mes - 1 entidad fiscal, 500 documentos IA/mes</li>
                <li><strong>Plan Avanzado (B2B):</strong> $49.99/mes - Clientes ilimitados, 500 documentos IA/mes</li>
            </ul>

            <h3 class="text-xl font-bold mt-6 mb-3">3.2 Límite de Documentos</h3>
            <p>Cada plan incluye 500 documentos procesados por IA al mes. Al superar este límite, se te notificará y podrás adquirir addons de 500 documentos adicionales por $9.99 cada uno. Los addons son válidos solo para el mes en curso y no se acumulan.</p>

            <h3 class="text-xl font-bold mt-6 mb-3">3.3 Facturación y Pagos</h3>
            <ul class="list-disc pl-6 mb-4">
                <li>Las suscripciones se facturan mensualmente por adelantado</li>
                <li>Los addons se facturan al momento de la compra</li>
                <li>Aceptamos tarjetas de crédito y débito principales</li>
                <li>Todos los precios están en dólares estadounidenses (USD)</li>
            </ul>

            <h3 class="text-xl font-bold mt-6 mb-3">3.4 Cancelación</h3>
            <p>Puedes cancelar tu suscripción en cualquier momento. No hay contratos ni penalizaciones. Al cancelar, tendrás acceso hasta el final del período de facturación actual.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">4. Política de Retención de Datos</h2>
            <p><strong>IMPORTANTE:</strong> Por razones de seguridad, los extractos bancarios se almacenan por un máximo de 60 días desde el fin del mes en curso. Tras este período, los archivos se eliminan física y lógicamente de forma automática e irreversible.</p>
            <p>Las transacciones extraídas de los extractos se conservan, pero no el documento original.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">5. Política de API Bancaria</h2>
            <p>Dataflow NO se conecta directamente con APIs bancarias. Solo permitimos carga manual de extractos en formatos PDF, Excel, CSV o imagen. Esta es una medida de seguridad para proteger tus credenciales bancarias.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">6. Responsabilidades del Usuario</h2>
            <ul class="list-disc pl-6 mb-4">
                <li>Proporcionar información precisa y actualizada</li>
                <li>Mantener la confidencialidad de tus credenciales de acceso</li>
                <li>Validar los datos extraídos por la IA antes de generar declaraciones</li>
                <li>Cumplir con las normativas fiscales de tu jurisdicción</li>
                <li>No utilizar el servicio para actividades ilegales</li>
            </ul>

            <h2 class="text-2xl font-bold mt-8 mb-4">7. Limitación de Responsabilidad</h2>
            <p>Dataflow es una herramienta de automatización y asistencia. No somos un despacho contable ni ofrecemos asesoría fiscal. El usuario es responsable de:</p>
            <ul class="list-disc pl-6 mb-4">
                <li>Validar la exactitud de los datos procesados</li>
                <li>Cumplir con las obligaciones fiscales de su jurisdicción</li>
                <li>Consultar con un profesional contable cuando sea necesario</li>
            </ul>

            <h2 class="text-2xl font-bold mt-8 mb-4">8. Modificaciones del Servicio</h2>
            <p>Nos reservamos el derecho de modificar, suspender o descontinuar el servicio (o cualquier parte del mismo) con previo aviso de 30 días.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">9. Ley Aplicable</h2>
            <p>Estos términos se rigen por las leyes de España. Para usuarios de Hispanoamérica, se aplicarán también las normativas de protección al consumidor de cada jurisdicción.</p>

            <h2 class="text-2xl font-bold mt-8 mb-4">10. Contacto</h2>
            <p>Para preguntas sobre estos términos, contacta a: <a href="mailto:legal@contaplus.com" class="text-purple-600">legal@contaplus.com</a></p>
        </div>
    </div>
</section>
@endsection
