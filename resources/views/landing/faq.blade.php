@extends('layouts.landing')

@section('content')

<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6 max-w-4xl">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Preguntas Frecuentes
            </h1>
            <p class="text-xl text-gray-600">
                Encuentra respuestas a las preguntas más comunes sobre Dataflow
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
                    Importas manualmente tu extracto bancario, y Dataflow compara automáticamente las transacciones del extracto con las facturas y recibos que has subido. Las coincidencias se marcan automáticamente como conciliadas.
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
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Cómo gestionan el IVA y los impuestos de diferentes países?</h3>
                <p class="text-gray-700 mb-3">
                    Dataflow reconoce y procesa automáticamente los distintos tipos de impuestos según el país configurado en tu entidad fiscal. Cada país tiene sus propias denominaciones y tipos:
                </p>
                <div class="text-gray-700 space-y-2 text-sm">
                    <p><strong>España y Portugal:</strong> IVA con tipos General (21%/23%), Reducido (10%/13%), Superreducido (4%/6%), además de IRPF y Recargo de equivalencia.</p>
                    <p><strong>América Latina:</strong> IVA con distintas tasas según país (10% en Paraguay, 16% en México, 19% en Colombia y Chile, 21% en Argentina, 22% en Uruguay), más retenciones y percepciones específicas de cada jurisdicción.</p>
                    <p><strong>Brasil:</strong> Sistema complejo con ICMS (estadual), IPI, PIS, COFINS, ISS (municipal).</p>
                    <p class="mt-3 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                        <strong>⚙️ Importante:</strong> Los tipos de IVA son <strong>configurables</strong> desde tu dashboard. Puedes establecer los porcentajes que aplican en tu país, y si cambian en el futuro (por reformas fiscales), los puedes actualizar en cualquier momento sin afectar tus registros históricos.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-2 text-purple-600">¿Cómo configuro los tipos de IVA para mi país?</h3>
                <p class="text-gray-700 mb-3">
                    Durante la configuración inicial de tu entidad fiscal, podrás definir los tipos de IVA que aplican en tu país. Por ejemplo:
                </p>
                <ul class="text-gray-700 text-sm list-disc list-inside space-y-1 mb-3">
                    <li>IVA General: 21% (España), 16% (México), 19% (Colombia)</li>
                    <li>IVA Reducido: 10% (España), 5% (Paraguay)</li>
                    <li>IVA Exento: 0%</li>
                    <li>Retenciones (IRPF, Ganancias, etc.)</li>
                </ul>
                <p class="text-gray-700">
                    El sistema OCR detectará automáticamente estos tipos en tus facturas y los clasificará correctamente. Si los tipos de IVA cambian en tu país por reforma fiscal, simplemente actualizas la configuración y el sistema seguirá procesando correctamente las nuevas facturas, mientras mantiene el histórico con los tipos anteriores.
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

        {{-- GUÍA COMPLETA DE USO --}}
        <div class="mt-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    📚 Guía Completa de Uso
                </h2>
                <p class="text-xl text-gray-600">
                    Todo lo que necesitas saber sobre Dataflow explicado paso a paso
                </p>
            </div>

            {{-- Introducción: Qué es Dataflow --}}
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-xl p-8 text-white mb-8">
                <h3 class="text-2xl font-bold mb-4">🎯 ¿Qué es Dataflow?</h3>
                <p class="text-lg mb-4">
                    <strong>Dataflow</strong> es tu asistente contable inteligente que <strong>automatiza todas las tareas repetitivas</strong> que consumen tu tiempo como contador o empresario.
                </p>
                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <div class="bg-white/10 rounded-lg p-4">
                        <h4 class="font-bold mb-2">❌ Sin Dataflow:</h4>
                        <ul class="space-y-1 text-sm">
                            <li>• Pasar horas clasificando facturas</li>
                            <li>• Introducir datos manualmente</li>
                            <li>• Recordar fechas de vencimientos</li>
                            <li>• Buscar documentos entre papeles</li>
                            <li>• Calcular IVA manualmente</li>
                        </ul>
                    </div>
                    <div class="bg-white/10 rounded-lg p-4">
                        <h4 class="font-bold mb-2">✅ Con Dataflow:</h4>
                        <ul class="space-y-1 text-sm">
                            <li>• Envía foto por Telegram</li>
                            <li>• IA extrae datos automáticamente</li>
                            <li>• Recordatorios automáticos</li>
                            <li>• Todo organizado y respaldado</li>
                            <li>• Reportes con un clic</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Telegram: La Magia --}}
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <h3 class="text-2xl font-bold text-purple-600 mb-4">📱 LA MAGIA DE TELEGRAM: Tu Oficina Contable de Bolsillo</h3>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-6">
                    <h4 class="font-bold text-lg mb-2">🤔 ¿Por qué Telegram y no una app?</h4>
                    <p class="text-gray-700 mb-3">
                        <strong>Porque ya usas Telegram todos los días.</strong> No necesitas instalar otra app, recordar otra contraseña, ni aprender otra interfaz.
                    </p>
                    <p class="text-gray-700">
                        <strong>Simplemente:</strong> Abres Telegram → Envías foto de la factura → Listo. Dataflow hace el resto.
                    </p>
                </div>

                <div class="space-y-6">
                    <div>
                        <h4 class="font-bold text-lg mb-3">📸 Cómo Enviar Facturas por Telegram (Paso a Paso)</h4>

                        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-6 mb-4">
                            <h5 class="font-bold mb-3">Primera Vez - Conexión (Solo una vez):</h5>
                            <ol class="space-y-3 text-gray-700">
                                <li><strong>Paso 1:</strong> Abre Telegram → Busca <code class="bg-purple-200 px-2 py-1 rounded">@DataflowBot</code></li>
                                <li><strong>Paso 2:</strong> Ve a <a href="/settings" class="text-purple-600 underline">Configuración de Dataflow</a> → Copia tu código</li>
                                <li><strong>Paso 3:</strong> Pega el código en el chat de Telegram</li>
                                <li><strong>Paso 4:</strong> ✅ ¡Listo! Ya estás conectado</li>
                            </ol>
                        </div>

                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="border-2 border-purple-200 rounded-lg p-4">
                                <h6 class="font-bold text-purple-600 mb-2">📷 Opción 1: Foto</h6>
                                <p class="text-sm text-gray-700">
                                    Estás en el super → Abres Telegram → Foto de la factura → Enviar → ¡10 segundos y listo!
                                </p>
                            </div>
                            <div class="border-2 border-purple-200 rounded-lg p-4">
                                <h6 class="font-bold text-purple-600 mb-2">📄 Opción 2: PDF</h6>
                                <p class="text-sm text-gray-700">
                                    Recibes factura por email → Reenvías PDF a @DataflowBot → Procesada automáticamente
                                </p>
                            </div>
                            <div class="border-2 border-purple-200 rounded-lg p-4">
                                <h6 class="font-bold text-purple-600 mb-2">📚 Opción 3: Múltiples</h6>
                                <p class="text-sm text-gray-700">
                                    Montón de facturas → Fotos una por una → Enviar todas → Dataflow procesa todas
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg">
                        <h4 class="font-bold text-lg mb-3">🤖 Qué Hace el Bot Automáticamente</h4>
                        <div class="space-y-3 text-gray-700">
                            <div>
                                <strong>1. Validación (2 seg):</strong> ¿Es factura válida? ¿Se ve bien?
                            </div>
                            <div>
                                <strong>2. Extracción con IA (8 seg):</strong> Lee TODO: número, fecha, emisor, IVA, totales...
                            </div>
                            <div>
                                <strong>3. Notificación Completa:</strong> Recibes mensaje con todos los datos extraídos
                            </div>
                            <div>
                                <strong>4. Almacenamiento:</strong> Guardado en la nube, organizado, listo para reportes
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-lg mb-3">💬 Comandos Disponibles</h4>
                        <div class="grid md:grid-cols-2 gap-3">
                            <div class="bg-gray-50 p-3 rounded">
                                <code class="text-purple-600 font-bold">/start</code> - Comenzar a usar el bot
                            </div>
                            <div class="bg-gray-50 p-3 rounded">
                                <code class="text-purple-600 font-bold">/help</code> - Ver ayuda y comandos
                            </div>
                            <div class="bg-gray-50 p-3 rounded">
                                <code class="text-purple-600 font-bold">/status</code> - Ver estado de cuenta
                            </div>
                            <div class="bg-gray-50 p-3 rounded">
                                <code class="text-purple-600 font-bold">/recent</code> - Ver últimas 5 facturas
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ahorro de Tiempo --}}
            <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-8 mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">⏱️ ¿Cuánto Tiempo Ahorras?</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-yellow-200">
                            <tr>
                                <th class="p-3">Tarea</th>
                                <th class="p-3">Sin Dataflow</th>
                                <th class="p-3">Con Dataflow</th>
                                <th class="p-3 font-bold">Ahorro</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr class="border-b">
                                <td class="p-3">Procesar 50 facturas/mes</td>
                                <td class="p-3">3 horas</td>
                                <td class="p-3">5 minutos</td>
                                <td class="p-3 font-bold text-green-600">2h 55min</td>
                            </tr>
                            <tr class="border-b">
                                <td class="p-3">Liquidación IVA</td>
                                <td class="p-3">2 horas</td>
                                <td class="p-3">10 minutos</td>
                                <td class="p-3 font-bold text-green-600">1h 50min</td>
                            </tr>
                            <tr class="border-b">
                                <td class="p-3">Recordar vencimientos</td>
                                <td class="p-3">30 min/semana</td>
                                <td class="p-3">Automático</td>
                                <td class="p-3 font-bold text-green-600">2h/mes</td>
                            </tr>
                            <tr class="bg-green-100 font-bold">
                                <td class="p-3">TOTAL AL MES</td>
                                <td class="p-3">~15 horas</td>
                                <td class="p-3">1 hora</td>
                                <td class="p-3 text-green-700 text-xl">14 HORAS</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Calendario Fiscal --}}
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <h3 class="text-2xl font-bold text-purple-600 mb-4">🗓️ CALENDARIO FISCAL: Nunca Más Olvides un Vencimiento</h3>

                <p class="text-gray-700 mb-6">
                    Tu asistente personal que te recuerda <strong>ANTES</strong> de cada vencimiento fiscal. Nunca más pagues multas por retraso.
                </p>

                <div class="grid md:grid-cols-3 gap-6 mb-6">
                    <div class="border-2 border-blue-200 rounded-lg p-4">
                        <h4 class="font-bold text-blue-600 mb-2">🇵🇾 Paraguay</h4>
                        <ul class="text-sm space-y-1 text-gray-700">
                            <li>✅ IVA mensual (día 25)</li>
                            <li>✅ IPS (día 10)</li>
                            <li>✅ IRE (3 cuotas/año)</li>
                        </ul>
                    </div>
                    <div class="border-2 border-blue-200 rounded-lg p-4">
                        <h4 class="font-bold text-blue-600 mb-2">🇪🇸 España</h4>
                        <ul class="text-sm space-y-1 text-gray-700">
                            <li>✅ Modelo 303 (IVA trimestral)</li>
                            <li>✅ Modelo 390 (resumen anual)</li>
                            <li>✅ Modelo 130 (IRPF)</li>
                            <li>✅ Declaración Renta</li>
                        </ul>
                    </div>
                    <div class="border-2 border-blue-200 rounded-lg p-4">
                        <h4 class="font-bold text-blue-600 mb-2">🇦🇷 Argentina</h4>
                        <ul class="text-sm space-y-1 text-gray-700">
                            <li>✅ IVA mensual (día 20)</li>
                            <li>✅ Ganancias (5 anticipos)</li>
                            <li>✅ AFIP Social (día 10)</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-purple-50 rounded-lg p-6">
                    <h4 class="font-bold mb-3">📧 Notificaciones Automáticas</h4>
                    <p class="text-gray-700 mb-3">
                        <strong>7 días antes:</strong> Email recordatorio normal con checklist de preparación
                    </p>
                    <p class="text-gray-700">
                        <strong>3 días antes:</strong> Email URGENTE (fondo rojo) para que no olvides
                    </p>
                </div>

                <div class="mt-6 text-center">
                    <a href="/fiscal-events" class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-bold transition">
                        Ver Mi Calendario Fiscal →
                    </a>
                </div>
            </div>

            {{-- Liquidación IVA --}}
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <h3 class="text-2xl font-bold text-purple-600 mb-4">💰 LIQUIDACIÓN DE IVA: Reportes Profesionales en 1 Clic</h3>

                <p class="text-gray-700 mb-6">
                    Genera reportes completos de IVA listos para tu contador o para declarar.
                </p>

                <div class="space-y-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="bg-purple-100 rounded-full p-2 mt-1">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold">Todas tus facturas en un Excel</h4>
                            <p class="text-gray-600 text-sm">Organizadas por fecha, con todos los datos fiscales</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="bg-purple-100 rounded-full p-2 mt-1">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold">Totales automáticos por tipo de IVA</h4>
                            <p class="text-gray-600 text-sm">Separado por 21%, 10%, 4%, o los tipos de tu país</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="bg-purple-100 rounded-full p-2 mt-1">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold">Filtros por período y entidad</h4>
                            <p class="text-gray-600 text-sm">Mes actual, trimestre, o rango personalizado</p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="/vat-liquidation" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold transition">
                        Generar Liquidación IVA →
                    </a>
                </div>
            </div>

            {{-- Backup Mensual --}}
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <h3 class="text-2xl font-bold text-purple-600 mb-4">💾 BACKUP MENSUAL: Tus Datos Siempre Seguros</h3>

                <div class="bg-orange-50 border-l-4 border-orange-500 p-6 rounded-lg mb-6">
                    <p class="text-gray-700 mb-2">
                        <strong>⚠️ Importante:</strong> Dataflow solo guarda documentos <strong>2 meses</strong> por políticas de almacenamiento.
                    </p>
                    <p class="text-gray-700">
                        Por eso, <strong>cada día 20</strong> recibes por email un Excel con TODOS los documentos del mes anterior.
                    </p>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📧</span>
                        <span>Email automático cada día 20 del mes</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📊</span>
                        <span>Excel con 20 columnas de información completa</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📝</span>
                        <span>Instrucciones para guardarlo en Google Drive</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🔒</span>
                        <span>Tu historial completo siempre accesible</span>
                    </div>
                </div>
            </div>

            {{-- Flujo Completo --}}
            <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">🎓 FLUJO COMPLETO: De la Factura a la Declaración</h3>

                <div class="space-y-4">
                    <div class="bg-white rounded-lg p-6 shadow">
                        <h4 class="font-bold text-lg mb-2">📱 Día a Día (30 segundos)</h4>
                        <p class="text-gray-700">
                            Compras en el super → Foto de la factura por Telegram → Bot confirma en 10 seg → ¡Listo!
                        </p>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow">
                        <h4 class="font-bold text-lg mb-2">📧 Día 18 - Recordatorio (5 min)</h4>
                        <p class="text-gray-700">
                            Email: "Vencimiento IVA en 7 días" → Entras a Dataflow → Liquidación IVA → Exportar → Enviar a contador
                        </p>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow">
                        <h4 class="font-bold text-lg mb-2">💾 Día 20 - Backup (2 min)</h4>
                        <p class="text-gray-700">
                            Email con Excel del mes → Descargar → Subir a Google Drive → Archivar email
                        </p>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow">
                        <h4 class="font-bold text-lg mb-2">✅ Día 24 - Declaración</h4>
                        <p class="text-gray-700">
                            Tu contador declara en 15 min (vs 2 horas sin Dataflow) → ¡A tiempo! → Sin multas
                        </p>
                    </div>
                </div>
            </div>

            {{-- CTA Final --}}
            <div class="mt-12 text-center bg-purple-600 text-white rounded-lg p-8">
                <h3 class="text-2xl font-bold mb-4">¿Listo para ahorrar 14 horas al mes?</h3>
                <p class="text-lg mb-6">
                    Únete a cientos de contadores y empresarios que ya confían en Dataflow
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/register" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
                        Comenzar Gratis →
                    </a>
                    <a href="/pricing" class="border-2 border-white text-white px-8 py-3 rounded-lg font-bold hover:bg-purple-700 transition">
                        Ver Planes y Precios
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center bg-purple-50 rounded-lg p-8">
            <h3 class="text-2xl font-bold mb-4">¿No encuentras tu respuesta?</h3>
            <p class="text-gray-700 mb-6">Nuestro equipo está listo para ayudarte</p>
            <a href="mailto:soporte@dataflow.com" class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-bold transition">
                Contactar Soporte
            </a>
        </div>
    </div>
</section>

@endsection
