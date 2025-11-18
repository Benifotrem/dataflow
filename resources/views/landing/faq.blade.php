@extends('layouts.landing')

@section('content')

<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6 max-w-4xl">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Preguntas Frecuentes
            </h1>
            <p class="text-xl text-gray-600">
                Encuentra respuestas a las preguntas más comunes sobre Contaplus
            </p>
        </div>

        <div class="space-y-6">
            {{-- Funcionalidad --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Cómo funciona el OCR inteligente?</h3>
                <p class="text-gray-700">
                    Nuestro sistema utiliza inteligencia artificial avanzada (OpenAI) para extraer automáticamente todos los datos relevantes de tus facturas y recibos: importes, IVA, fechas, emisor, receptor, etc. Simplemente subes el documento en PDF, imagen o Excel y en segundos está procesado.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Qué formatos de archivo puedo subir?</h3>
                <p class="text-gray-700">
                    Aceptamos PDF, Excel (XLS, XLSX), CSV, y imágenes (JPG, PNG). Para extractos bancarios también puedes usar estos formatos.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Cómo funciona la conciliación bancaria?</h3>
                <p class="text-gray-700">
                    Importas manualmente tu extracto bancario, y Contaplus compara automáticamente las transacciones del extracto con las facturas y recibos que has subido. Las coincidencias se marcan automáticamente como conciliadas.
                </p>
            </div>

            {{-- Seguridad --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Por qué no se conectan directamente con mi banco?</h3>
                <p class="text-gray-700">
                    Por política de seguridad, NO nos conectamos directamente con APIs bancarias. Creemos que es más seguro que tú controles qué información compartes. Los extractos bancarios solo se retienen por 60 días desde fin de mes y luego se eliminan automáticamente.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Qué pasa con mis extractos bancarios después de 60 días?</h3>
                <p class="text-gray-700">
                    Los extractos bancarios se eliminan física y lógicamente de nuestros servidores tras 60 días desde el fin del mes en curso. Esta es una medida de seguridad para proteger tus datos sensibles. Las transacciones extraídas se conservan, pero no el documento original.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Mis datos están seguros?</h3>
                <p class="text-gray-700">
                    Absolutamente. Usamos encriptación de grado bancario, aislamiento total entre tenants (arquitectura multi-tenant), y cumplimos con GDPR y normativas de protección de datos de cada jurisdicción.
                </p>
            </div>

            {{-- Límites y Monetización --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Qué pasa si excedo los 500 documentos mensuales?</h3>
                <p class="text-gray-700">
                    Al alcanzar el límite de 500 documentos, recibirás una notificación con la opción de comprar un addon de 500 documentos adicionales por $9.99. Puedes comprar tantos addons como necesites. Los addons son válidos solo para el mes en curso.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Los 500 documentos se acumulan si no los uso?</h3>
                <p class="text-gray-700">
                    No, el límite de 500 documentos se renueva cada mes y no se acumula. Cada mes comienzas con 500 documentos disponibles.
                </p>
            </div>

            {{-- Multi-jurisdicción --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Qué países soportan?</h3>
                <p class="text-gray-700">
                    Actualmente soportamos España y todos los países de Hispanoamérica (México, Argentina, Colombia, Chile, Perú, Venezuela, Ecuador, Guatemala, Cuba, Bolivia, República Dominicana, Honduras, Paraguay, El Salvador, Nicaragua, Costa Rica, Panamá, Puerto Rico, Uruguay, y más). Cada país tiene su configuración fiscal específica.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Cómo gestionan el IVA de diferentes países?</h3>
                <p class="text-gray-700">
                    Cada entidad fiscal se configura con el país correspondiente, y el sistema aplica automáticamente las reglas fiscales de ese país: IVA/VAT, retenciones, tipos impositivos, etc. Por ejemplo, en España usamos 21% de IVA, en México 16%, en Argentina 21%, etc.
                </p>
            </div>

            {{-- CSV e iCalendar --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Cómo funciona la importación/exportación CSV?</h3>
                <p class="text-gray-700">
                    Ofrecemos un mapeador visual de columnas que te permite importar datos desde cualquier CSV (Excel, Google Sheets, Apple Numbers). Seleccionas qué columna corresponde a cada campo (fecha, importe, descripción, etc.) y guardas el mapeo para futuras importaciones.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Cómo sincronizo los plazos fiscales con mi calendario?</h3>
                <p class="text-gray-700">
                    Cada entidad fiscal genera automáticamente una URL de feed iCalendar (.ics) que puedes suscribir en Google Calendar, Apple Calendar, Outlook o cualquier aplicación compatible. Los plazos se sincronizan automáticamente y recibes recordatorios.
                </p>
            </div>

            {{-- Colaboración B2B --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Cómo funciona la colaboración para despachos (Plan Avanzado)?</h3>
                <p class="text-gray-700">
                    Con el Plan Avanzado, puedes gestionar múltiples clientes (entidades ilimitadas). Cada cliente puede tener dos roles: Propietario (quien sube documentos) y Asesor (contador que valida y clasifica). Ambos pueden trabajar en tiempo real con cambios síncronos.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Cuál es la diferencia entre Plan Básico y Avanzado?</h3>
                <p class="text-gray-700">
                    El Plan Básico ($19.99/mes) es para una sola entidad fiscal (ideal para PyMEs y autónomos). El Plan Avanzado ($49.99/mes) permite gestionar clientes ilimitados con colaboración en tiempo real (ideal para despachos y contadores que gestionan múltiples empresas).
                </p>
            </div>

            {{-- Otros --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Ofrecen período de prueba?</h3>
                <p class="text-gray-700">
                    Sí, ofrecemos 14 días de prueba gratuita sin necesidad de tarjeta de crédito. Puedes cancelar en cualquier momento durante la prueba sin cargo alguno.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Puedo cancelar mi suscripción en cualquier momento?</h3>
                <p class="text-gray-700">
                    Sí, puedes cancelar en cualquier momento desde tu panel de control. No hay contratos ni penalizaciones. Si cancelas, tendrás acceso hasta el final del período de facturación.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Ofrecen soporte en español?</h3>
                <p class="text-gray-700">
                    Sí, todo nuestro soporte está disponible en español (España y Latinoamérica). El Plan Básico incluye soporte por email, y el Plan Avanzado incluye soporte prioritario.
                </p>
            </div>
        </div>

        <div class="mt-12 text-center bg-purple-50 rounded-lg p-8">
            <h3 class="text-2xl font-bold mb-4">¿No encuentras tu respuesta?</h3>
            <p class="text-gray-700 mb-6">Nuestro equipo está listo para ayudarte</p>
            <a href="mailto:soporte@contaplus.com" class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-bold transition">
                Contactar Soporte
            </a>
        </div>
    </div>
</section>

@endsection
