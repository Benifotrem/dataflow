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

        {{-- GUÍA COMPLETA - DESTACADA --}}
        <div class="mb-12 bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl p-8 text-white shadow-2xl">
            <h2 class="text-3xl font-bold mb-4 flex items-center">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Guía Completa: Flujo de Trabajo Ideal
            </h2>
            <p class="text-purple-100 mb-6">
                Paso a paso de cómo usar Dataflow en tu estudio de contabilidad o empresa
            </p>
            <a href="#guia-completa" class="inline-block bg-white text-purple-600 px-6 py-3 rounded-lg font-bold hover:bg-purple-50 transition">
                Ver Guía Paso a Paso
            </a>
        </div>

        <div class="space-y-6">

            {{-- GUÍA COMPLETA --}}
            <div id="guia-completa" class="bg-white rounded-lg shadow-lg p-8 border-l-4 border-purple-600">
                <h2 class="text-3xl font-bold mb-6 text-purple-600">📘 Guía Completa de Uso</h2>

                <div class="prose prose-lg max-w-none">
                    <p class="text-gray-700 text-lg mb-8">
                        Sigue esta guía paso a paso para aprovechar al máximo Dataflow. Hemos diseñado el flujo para que sea <strong>simple, rápido e intuitivo</strong> tanto para contadores profesionales como para emprendedores.
                    </p>

                    {{-- PASO 1 --}}
                    <div class="mb-10 bg-purple-50 rounded-lg p-6 border-l-4 border-purple-500">
                        <h3 class="text-2xl font-bold text-purple-700 mb-4 flex items-center">
                            <span class="bg-purple-600 text-white w-10 h-10 rounded-full flex items-center justify-center mr-3 text-xl">1</span>
                            Configuración Inicial (5 minutos)
                        </h3>

                        <div class="space-y-4 text-gray-700">
                            <div class="bg-white rounded p-4 shadow-sm">
                                <h4 class="font-bold text-purple-600 mb-2">1.1. Crear tu cuenta</h4>
                                <ul class="list-disc list-inside space-y-1 ml-4">
                                    <li>Regístrate en <a href="{{ route('register') }}" class="text-purple-600 underline">dataflow.guaraniappstore.com/register</a></li>
                                    <li>Confirma tu email</li>
                                    <li>14 días de prueba gratuita, sin tarjeta de crédito</li>
                                </ul>
                            </div>

                            <div class="bg-white rounded p-4 shadow-sm">
                                <h4 class="font-bold text-purple-600 mb-2">1.2. Crear tu primera Entidad Fiscal</h4>
                                <ul class="list-disc list-inside space-y-1 ml-4">
                                    <li>Ve a <strong>Dashboard → Entidades Fiscales → Crear Nueva</strong></li>
                                    <li>Ingresa los datos:
                                        <ul class="list-circle list-inside ml-6 mt-2 space-y-1">
                                            <li><strong>Nombre:</strong> El nombre de tu empresa/cliente</li>
                                            <li><strong>RUC/RFC/NIT:</strong> Tu identificación fiscal</li>
                                            <li><strong>País:</strong> Selecciona Paraguay (o tu país)</li>
                                            <li><strong>Moneda:</strong> PYG (Guaraníes) o tu moneda local</li>
                                        </ul>
                                    </li>
                                    <li>Guarda y ¡listo! Tu entidad está creada</li>
                                </ul>
                            </div>

                            <div class="bg-white rounded p-4 shadow-sm">
                                <h4 class="font-bold text-purple-600 mb-2">1.3. Vincular Bot de Telegram (Recomendado)</h4>
                                <p class="mb-2">Para procesar facturas desde tu celular:</p>
                                <ol class="list-decimal list-inside space-y-2 ml-4">
                                    <li>Ve a <strong>Configuración → Bot de Telegram</strong></li>
                                    <li>Busca <code class="bg-gray-100 px-2 py-1 rounded">@dataflow_guaraniappstore_bot</code> en Telegram</li>
                                    <li>Envía <code class="bg-gray-100 px-2 py-1 rounded">/start</code> al bot</li>
                                    <li>El bot te dará un código de 9 dígitos (ej: <code class="bg-gray-100 px-2 py-1 rounded">123456789</code>)</li>
                                    <li>Ingresa ese código en <strong>Configuración → Vincular Telegram</strong></li>
                                    <li>¡Listo! Ahora puedes enviar facturas desde tu celular</li>
                                </ol>
                                <div class="mt-3 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                                    <p class="text-sm"><strong>💡 Tip:</strong> Con Telegram puedes tomar una foto de la factura y enviarla directamente. El OCR procesará automáticamente los datos.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PASO 2 --}}
                    <div class="mb-10 bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500">
                        <h3 class="text-2xl font-bold text-blue-700 mb-4 flex items-center">
                            <span class="bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center mr-3 text-xl">2</span>
                            Procesar Facturas
                        </h3>

                        <p class="text-gray-700 mb-4">Tienes <strong>4 formas</strong> de procesar facturas. Elige la que prefieras según el tipo de factura:</p>

                        <div class="space-y-4">
                            {{-- Opción A: Telegram --}}
                            <div class="bg-white rounded p-4 shadow-sm">
                                <h4 class="font-bold text-blue-600 mb-2">📱 Opción A: Vía Telegram (Más Rápida)</h4>
                                <ol class="list-decimal list-inside space-y-2 ml-4 text-gray-700">
                                    <li>Abre el bot de Telegram <code class="bg-gray-100 px-2 py-1 rounded">@dataflow_guaraniappstore_bot</code></li>
                                    <li>Toma una <strong>foto clara</strong> de la factura o envía el <strong>PDF</strong></li>
                                    <li>El bot procesa automáticamente:
                                        <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                            <li>Extrae datos con OCR (RUC, número, fecha, montos)</li>
                                            <li>Detecta IVA 10%, IVA 5%, Exentas automáticamente</li>
                                            <li>Valida con la SET (DNIT) en Paraguay</li>
                                        </ul>
                                    </li>
                                    <li>En <strong>10-15 segundos</strong> recibes confirmación:
                                        <div class="mt-2 p-3 bg-green-50 rounded border border-green-200">
                                            <p class="text-sm font-mono">✅ <strong>¡Factura procesada y validada con la SET!</strong></p>
                                            <p class="text-sm font-mono">📄 Nº: 001-001-0012345</p>
                                            <p class="text-sm font-mono">🏢 RUC: 80012345-6</p>
                                            <p class="text-sm font-mono">💰 Base 10%: ₲ 81.819 | IVA 10%: ₲ 8.181</p>
                                            <p class="text-sm font-mono">💰 <strong>TOTAL: ₲ 90.000</strong></p>
                                        </div>
                                    </li>
                                </ol>
                                <div class="mt-3 p-3 bg-green-50 border-l-4 border-green-500 rounded">
                                    <p class="text-sm"><strong>✨ Ventaja:</strong> Ideal para facturas en papel. Tomas foto con el celular y ¡listo!</p>
                                </div>
                            </div>

                            {{-- Opción B: Web Upload --}}
                            <div class="bg-white rounded p-4 shadow-sm">
                                <h4 class="font-bold text-blue-600 mb-2">💻 Opción B: Subida Web</h4>
                                <ol class="list-decimal list-inside space-y-2 ml-4 text-gray-700">
                                    <li>Ve a <strong>Dashboard → Documentos → Subir Documento</strong></li>
                                    <li>Arrastra el archivo (PDF, JPG, PNG) o haz clic para seleccionar</li>
                                    <li>Selecciona la <strong>Entidad Fiscal</strong> correspondiente</li>
                                    <li>Haz clic en <strong>Procesar con OCR</strong></li>
                                    <li>Espera 10-15 segundos mientras se procesa</li>
                                </ol>
                                <div class="mt-3 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                                    <p class="text-sm"><strong>💡 Tip:</strong> Puedes subir múltiples archivos a la vez. Ideal para procesar lotes de facturas.</p>
                                </div>
                            </div>

                            {{-- Opción C: Factura Electrónica (API SET) --}}
                            <div class="bg-white rounded p-4 shadow-sm border-2 border-green-300">
                                <h4 class="font-bold text-green-600 mb-2">🔌 Opción C: Factura Electrónica (API SET/Ekuatia) ⭐</h4>
                                <ol class="list-decimal list-inside space-y-2 ml-4 text-gray-700">
                                    <li>Ve a <strong>Dashboard → Documentos → Consultar Factura Electrónica</strong></li>
                                    <li>Ingresa el <strong>CDC</strong> (Código de Control) de 44 dígitos o escanea el QR de la factura</li>
                                    <li>El sistema consulta automáticamente a la API pública de <strong>ekuatia.set.gov.py</strong></li>
                                    <li>Los datos se importan <strong>directamente desde la SET</strong>:
                                        <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                            <li>RUC y Razón Social del Emisor</li>
                                            <li>Número de Factura (Timbrado electrónico)</li>
                                            <li>Fecha de emisión</li>
                                            <li>Montos desglosados: Base 10%, IVA 10%, Base 5%, IVA 5%, Exentas</li>
                                            <li>Estado de la factura (Aprobada, Anulada, etc.)</li>
                                        </ul>
                                    </li>
                                    <li>La factura se registra automáticamente <strong>sin necesidad de OCR</strong></li>
                                </ol>
                                <div class="mt-3 p-3 bg-green-50 border-l-4 border-green-600 rounded">
                                    <p class="text-sm"><strong>✨ Ventajas:</strong></p>
                                    <ul class="list-disc list-inside text-sm ml-4 space-y-1">
                                        <li><strong>100% de precisión</strong>: Datos oficiales de la SET, sin errores de OCR</li>
                                        <li><strong>Cero trabajo manual</strong>: No necesitas contrastar con Marangatu</li>
                                        <li><strong>Validación instantánea</strong>: Verificas que la factura existe y está aprobada</li>
                                        <li><strong>Ideal para facturas recibidas</strong>: Tus proveedores te pasan el CDC o QR</li>
                                    </ul>
                                </div>
                                <div class="mt-3 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                                    <p class="text-sm"><strong>💡 Tip:</strong> Para facturas en papel usa Telegram/Web (OCR). Para facturas electrónicas, usa esta opción que consulta directamente la API de la SET. ¡Es mucho más rápido y preciso!</p>
                                </div>
                            </div>

                            {{-- Opción D: Email (Futuro) --}}
                            <div class="bg-white rounded p-4 shadow-sm opacity-75">
                                <h4 class="font-bold text-gray-600 mb-2">📧 Opción D: Por Email (Próximamente)</h4>
                                <p class="text-gray-600 text-sm">Envía facturas a <code class="bg-gray-100 px-2 py-1 rounded">facturas@dataflow.com</code> y se procesarán automáticamente.</p>
                            </div>
                        </div>

                        {{-- MINI APP DE TELEGRAM --}}
                        <div class="mt-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
                            <h4 class="font-bold text-2xl mb-3 flex items-center">
                                <span class="text-3xl mr-3">📱</span>
                                ¡NUEVO! Mini App de Telegram
                            </h4>
                            <p class="mb-4 text-blue-50">Ahora puedes gestionar todo desde una aplicación dentro de Telegram, sin salir del chat:</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                    <div class="font-bold mb-2">📊 Dashboard Móvil</div>
                                    <ul class="text-sm space-y-1 text-blue-50">
                                        <li>• Métricas en tiempo real</li>
                                        <li>• Gráficos interactivos</li>
                                        <li>• Alertas de vencimientos</li>
                                    </ul>
                                </div>

                                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                    <div class="font-bold mb-2">📷 Escaneo de QR</div>
                                    <ul class="text-sm space-y-1 text-blue-50">
                                        <li>• Consulta facturas electrónicas</li>
                                        <li>• Importación desde API SET</li>
                                        <li>• 100% precisión oficial</li>
                                    </ul>
                                </div>

                                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                    <div class="font-bold mb-2">✏️ Edición Rápida</div>
                                    <ul class="text-sm space-y-1 text-blue-50">
                                        <li>• Corrige datos desde el móvil</li>
                                        <li>• Sin abrir dashboard web</li>
                                        <li>• Recálculo automático de IVAs</li>
                                    </ul>
                                </div>

                                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                    <div class="font-bold mb-2">📊 Exportación Excel</div>
                                    <ul class="text-sm space-y-1 text-blue-50">
                                        <li>• Genera Excel desde Telegram</li>
                                        <li>• Comparte directamente</li>
                                        <li>• Formato RG-90 listo</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="bg-yellow-400 bg-opacity-30 rounded-lg p-4 mb-4">
                                <p class="font-bold mb-2">🎯 Cómo Acceder:</p>
                                <ol class="text-sm space-y-2 text-blue-50">
                                    <li><strong>1.</strong> Abre <code class="bg-black bg-opacity-30 px-2 py-1 rounded">@dataflow_guaraniappstore_bot</code> en Telegram</li>
                                    <li><strong>2.</strong> Envía el comando <code class="bg-black bg-opacity-30 px-2 py-1 rounded">/app</code></li>
                                    <li><strong>3.</strong> Presiona el botón <strong>"🚀 Abrir Dataflow"</strong></li>
                                    <li><strong>4.</strong> ¡Listo! Toda la funcionalidad en tu móvil</li>
                                </ol>
                            </div>

                            <div class="bg-green-400 bg-opacity-20 rounded-lg p-3">
                                <p class="text-sm"><strong>💡 Ventaja:</strong> Trabajas desde donde estés, sin abrir laptop. Ideal para contadores en movimiento que necesitan consultar o corregir datos rápidamente.</p>
                            </div>
                        </div>
                    </div>

                    {{-- PASO 3 --}}
                    <div class="mb-10 bg-yellow-50 rounded-lg p-6 border-l-4 border-yellow-500">
                        <h3 class="text-2xl font-bold text-yellow-700 mb-4 flex items-center">
                            <span class="bg-yellow-600 text-white w-10 h-10 rounded-full flex items-center justify-center mr-3 text-xl">3</span>
                            Revisar y Corregir (si es necesario)
                        </h3>

                        <div class="space-y-4 text-gray-700">
                            <p>El OCR tiene <strong>95-98% de precisión</strong>, pero siempre debes revisar los datos extraídos:</p>

                            <div class="bg-white rounded p-4 shadow-sm">
                                <h4 class="font-bold text-yellow-600 mb-2">3.1. Ver todas las facturas</h4>
                                <ul class="list-disc list-inside space-y-1 ml-4">
                                    <li>Ve a <strong>Dashboard → Documentos</strong></li>
                                    <li>Verás una lista con todas tus facturas procesadas</li>
                                    <li>Estado:
                                        <ul class="list-circle list-inside ml-6 mt-2">
                                            <li><span class="text-green-600">✅ Validado</span>: Factura validada con la SET</li>
                                            <li><span class="text-yellow-600">⚠️ Revisar</span>: Requiere revisión manual</li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>

                            <div class="bg-white rounded p-4 shadow-sm">
                                <h4 class="font-bold text-yellow-600 mb-2">3.2. Corregir datos (si es necesario)</h4>
                                <ol class="list-decimal list-inside space-y-2 ml-4">
                                    <li>Haz clic en cualquier factura para ver detalles</li>
                                    <li>Verás:
                                        <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                            <li>Vista previa del documento original</li>
                                            <li>Datos extraídos por el OCR</li>
                                            <li>Estado de validación con la SET</li>
                                        </ul>
                                    </li>
                                    <li>Si hay errores, edita directamente los campos</li>
                                    <li>Guarda y la factura queda lista</li>
                                </ol>
                            </div>

                            <div class="bg-white rounded p-4 shadow-sm border-2 border-purple-300">
                                <h4 class="font-bold text-purple-600 mb-2">🔢 3.3. Validación Matemática Automática</h4>
                                <p class="mb-3">El sistema valida automáticamente que los importes sean coherentes según las reglas fiscales de Paraguay:</p>

                                <div class="bg-blue-50 p-3 rounded mb-3">
                                    <p class="text-sm font-bold text-blue-800">💡 Importante: En Paraguay, el precio final SIEMPRE incluye el IVA</p>
                                    <p class="text-sm text-blue-700 mt-1">El cálculo es inverso (del total hacia la base sin IVA)</p>
                                </div>

                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li><strong>IVA 10%:</strong> El subtotal incluye IVA → Base = Subtotal ÷ 1.10 → IVA = Subtotal - Base
                                        <div class="text-sm text-gray-600 ml-6 mt-1">
                                            <strong>Ejemplo:</strong> Compras por ₲110,000 (incluye IVA 10%)<br>
                                            • Base sin IVA: ₲110,000 ÷ 1.10 = ₲100,000<br>
                                            • IVA 10%: ₲110,000 - ₲100,000 = ₲10,000
                                        </div>
                                    </li>
                                    <li><strong>IVA 5%:</strong> El subtotal incluye IVA → Base = Subtotal ÷ 1.05 → IVA = Subtotal - Base
                                        <div class="text-sm text-gray-600 ml-6 mt-1">
                                            <strong>Ejemplo:</strong> Compras por ₲105,000 (incluye IVA 5%)<br>
                                            • Base sin IVA: ₲105,000 ÷ 1.05 = ₲100,000<br>
                                            • IVA 5%: ₲105,000 - ₲100,000 = ₲5,000
                                        </div>
                                    </li>
                                    <li><strong>Total:</strong> Verifica que Total = Subtotal 10% + Subtotal 5% + Exentas
                                        <div class="text-sm text-gray-600 ml-6 mt-1">
                                            <strong>Ejemplo:</strong> Factura mixta<br>
                                            • Subtotal gravado 10%: ₲110,000 (ya incluye IVA)<br>
                                            • Subtotal gravado 5%: ₲105,000 (ya incluye IVA)<br>
                                            • Exentas: ₲50,000<br>
                                            • <strong>Total: ₲265,000</strong>
                                        </div>
                                    </li>
                                </ul>
                                <div class="mt-3 p-3 bg-purple-50 rounded">
                                    <p class="text-sm"><strong>✨ Auto-corrección:</strong> Si detecta errores menores (redondeo ±10 guaraníes), el sistema intenta corregirlos automáticamente usando el cálculo inverso. Si no puede, marca la factura como "⚠️ Revisar" con el detalle del error matemático.</p>
                                </div>
                            </div>

                            <div class="mt-3 p-3 bg-yellow-100 border-l-4 border-yellow-600 rounded">
                                <p class="text-sm"><strong>⚠️ Importante:</strong> Las facturas marcadas como "Revisar" pueden tener:</p>
                                <ul class="list-disc list-inside text-sm ml-4 mt-2 space-y-1">
                                    <li>Datos incompletos (imagen borrosa, timbrado ilegible)</li>
                                    <li>Errores matemáticos en los importes (IVA no coincide con la base)</li>
                                    <li>Total incoherente con la suma de componentes</li>
                                </ul>
                                <p class="text-sm mt-2"><strong>Revísalas antes de exportar para asegurar la precisión contable.</strong></p>
                            </div>
                        </div>
                    </div>

                    {{-- PASO 4 --}}
                    <div class="mb-10 bg-green-50 rounded-lg p-6 border-l-4 border-green-500">
                        <h3 class="text-2xl font-bold text-green-700 mb-4 flex items-center">
                            <span class="bg-green-600 text-white w-10 h-10 rounded-full flex items-center justify-center mr-3 text-xl">4</span>
                            Exportar para Declaraciones de IVA
                        </h3>

                        <div class="space-y-4 text-gray-700">
                            <p>Llegó el momento de exportar para presentar ante la SET (DNIT) en Paraguay:</p>

                            <div class="bg-white rounded p-4 shadow-sm">
                                <h4 class="font-bold text-green-600 mb-2">4.1. Exportar Liquidación de IVA</h4>
                                <ol class="list-decimal list-inside space-y-2 ml-4">
                                    <li>Ve a <strong>Dashboard → Liquidación de IVA</strong></li>
                                    <li>Selecciona:
                                        <ul class="list-disc list-inside ml-6 mt-2">
                                            <li><strong>Entidad Fiscal:</strong> Tu empresa/cliente</li>
                                            <li><strong>Período:</strong> Mes actual, rango de fechas, etc.</li>
                                        </ul>
                                    </li>
                                    <li>Haz clic en <strong>Exportar a Excel</strong></li>
                                    <li>Se descarga un archivo <code class="bg-gray-100 px-2 py-1 rounded">.xlsx</code> con esta estructura:</li>
                                </ol>

                                <div class="mt-4 overflow-x-auto">
                                    <table class="min-w-full border border-gray-300 text-sm">
                                        <thead class="bg-purple-600 text-white">
                                            <tr>
                                                <th class="border px-2 py-1">Fecha</th>
                                                <th class="border px-2 py-1">Tipo</th>
                                                <th class="border px-2 py-1">Nº Factura</th>
                                                <th class="border px-2 py-1">RUC</th>
                                                <th class="border px-2 py-1">Razón Social</th>
                                                <th class="border px-2 py-1 bg-purple-700">Base 10%</th>
                                                <th class="border px-2 py-1 bg-purple-700">IVA 10%</th>
                                                <th class="border px-2 py-1 bg-blue-700">Base 5%</th>
                                                <th class="border px-2 py-1 bg-blue-700">IVA 5%</th>
                                                <th class="border px-2 py-1 bg-gray-700">Exentas</th>
                                                <th class="border px-2 py-1">Total IVA</th>
                                                <th class="border px-2 py-1">Monto Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white">
                                            <tr class="text-xs">
                                                <td class="border px-2 py-1">01/12/2025</td>
                                                <td class="border px-2 py-1">FACTURA</td>
                                                <td class="border px-2 py-1">001-001-123</td>
                                                <td class="border px-2 py-1">80012345-6</td>
                                                <td class="border px-2 py-1">Proveedor SA</td>
                                                <td class="border px-2 py-1 font-mono">81.819</td>
                                                <td class="border px-2 py-1 font-mono">8.181</td>
                                                <td class="border px-2 py-1 font-mono">-</td>
                                                <td class="border px-2 py-1 font-mono">-</td>
                                                <td class="border px-2 py-1 font-mono">-</td>
                                                <td class="border px-2 py-1 font-mono">8.181</td>
                                                <td class="border px-2 py-1 font-mono">90.000</td>
                                            </tr>
                                            <tr class="text-xs">
                                                <td class="border px-2 py-1">05/12/2025</td>
                                                <td class="border px-2 py-1">FACTURA</td>
                                                <td class="border px-2 py-1">002-001-456</td>
                                                <td class="border px-2 py-1">80098765-4</td>
                                                <td class="border px-2 py-1">Otro Proveedor</td>
                                                <td class="border px-2 py-1 font-mono">50.000</td>
                                                <td class="border px-2 py-1 font-mono">5.000</td>
                                                <td class="border px-2 py-1 font-mono">20.000</td>
                                                <td class="border px-2 py-1 font-mono">1.000</td>
                                                <td class="border px-2 py-1 font-mono">10.000</td>
                                                <td class="border px-2 py-1 font-mono">6.000</td>
                                                <td class="border px-2 py-1 font-mono">86.000</td>
                                            </tr>
                                            <tr class="bg-green-600 text-white font-bold text-xs">
                                                <td class="border px-2 py-1" colspan="5">TOTAL GENERAL</td>
                                                <td class="border px-2 py-1 font-mono">131.819</td>
                                                <td class="border px-2 py-1 font-mono">13.181</td>
                                                <td class="border px-2 py-1 font-mono">20.000</td>
                                                <td class="border px-2 py-1 font-mono">1.000</td>
                                                <td class="border px-2 py-1 font-mono">10.000</td>
                                                <td class="border px-2 py-1 font-mono">14.181</td>
                                                <td class="border px-2 py-1 font-mono">176.000</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="bg-white rounded p-4 shadow-sm">
                                <h4 class="font-bold text-green-600 mb-2">4.2. Cumplimiento con normativa RG-90</h4>
                                <p class="mb-2">El Excel generado cumple con los requisitos de la DNIT (SET Paraguay):</p>
                                <ul class="list-disc list-inside space-y-1 ml-4">
                                    <li>✅ Desglose por tipo de IVA (10%, 5%, Exentas)</li>
                                    <li>✅ Una factura con múltiples IVAs se muestra correctamente</li>
                                    <li>✅ Formato paraguayo (sin decimales, punto como separador de miles)</li>
                                    <li>✅ Totales calculados automáticamente</li>
                                    <li>✅ Listo para cargar en el sistema de la SET</li>
                                </ul>
                            </div>

                            <div class="mt-3 p-3 bg-green-100 border-l-4 border-green-600 rounded">
                                <p class="text-sm"><strong>🎯 Ahorra tiempo:</strong> Lo que antes tomaba <strong>2-3 horas</strong> de trabajo manual, ahora se hace en <strong>2 minutos</strong>.</p>
                            </div>
                        </div>
                    </div>

                    {{-- PASO 5 --}}
                    <div class="mb-10 bg-indigo-50 rounded-lg p-6 border-l-4 border-indigo-500">
                        <h3 class="text-2xl font-bold text-indigo-700 mb-4 flex items-center">
                            <span class="bg-indigo-600 text-white w-10 h-10 rounded-full flex items-center justify-center mr-3 text-xl">5</span>
                            Casos Especiales
                        </h3>

                        <div class="space-y-4 text-gray-700">
                            {{-- Facturas con múltiples IVAs --}}
                            <div class="bg-white rounded p-4 shadow-sm">
                                <h4 class="font-bold text-indigo-600 mb-2">📊 Facturas con múltiples tipos de IVA</h4>
                                <p class="mb-2">Ejemplo: Una factura que tiene productos al 10%, 5% y exentos en la misma compra:</p>
                                <div class="bg-gray-50 p-3 rounded border border-gray-200 font-mono text-sm">
                                    <p>Producto A (IVA 10%): ₲ 90.000</p>
                                    <p>Producto B (IVA 5%): ₲ 21.000</p>
                                    <p>Producto C (Exento): ₲ 10.000</p>
                                    <p class="mt-2 font-bold">Total Factura: ₲ 121.000</p>
                                </div>
                                <p class="mt-3"><strong>El sistema detecta automáticamente:</strong></p>
                                <ul class="list-disc list-inside ml-4 space-y-1 mt-2">
                                    <li>Base 10%: ₲ 81.819 | IVA 10%: ₲ 8.181</li>
                                    <li>Base 5%: ₲ 20.000 | IVA 5%: ₲ 1.000</li>
                                    <li>Exentas: ₲ 10.000</li>
                                </ul>
                                <p class="mt-3 text-sm text-green-600 font-bold">✅ Una sola línea en el Excel con todos los desgloses correctos</p>
                            </div>

                            {{-- Facturas electrónicas --}}
                            <div class="bg-white rounded p-4 shadow-sm border-2 border-green-300">
                                <h4 class="font-bold text-green-600 mb-2">🔐 Facturas Electrónicas (e-Kuatia) - Método Recomendado ⭐</h4>
                                <p class="mb-3">Para facturas electrónicas, <strong>usa la Opción C (Consulta directa por CDC)</strong> en lugar de OCR:</p>

                                <div class="bg-green-50 p-3 rounded mb-3">
                                    <p class="font-bold text-green-700 mb-2">✅ Ventajas de consultar por CDC:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1 text-sm">
                                        <li><strong>100% precisión</strong>: Datos oficiales de ekuatia.set.gov.py</li>
                                        <li><strong>Sin OCR</strong>: No hay errores de lectura</li>
                                        <li><strong>Sin contrastar con Marangatu</strong>: Los datos ya vienen validados</li>
                                        <li><strong>Más rápido</strong>: Solo ingresas el CDC o escaneas el QR</li>
                                        <li><strong>Estado en tiempo real</strong>: Sabes si fue aprobada o anulada</li>
                                    </ul>
                                </div>

                                <p class="mb-2"><strong>Cómo funciona:</strong></p>
                                <ol class="list-decimal list-inside ml-4 space-y-1 text-sm">
                                    <li>Pide a tu proveedor el <strong>CDC</strong> (44 dígitos) o el <strong>código QR</strong></li>
                                    <li>Ingresa el CDC en <strong>Dashboard → Consultar Factura Electrónica</strong></li>
                                    <li>El sistema consulta la API pública de la SET</li>
                                    <li>Todos los datos se importan automáticamente con validación oficial</li>
                                </ol>

                                <div class="mt-3 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                                    <p class="text-sm"><strong>💡 Tip:</strong> Si también procesaste la factura con OCR (Telegram/Web), el sistema puede detectar automáticamente el CDC del QR y validar con la SET. Pero lo más eficiente es usar directamente la Opción C.</p>
                                </div>
                            </div>

                            {{-- Facturas rechazadas --}}
                            <div class="bg-white rounded p-4 shadow-sm">
                                <h4 class="font-bold text-indigo-600 mb-2">⚠️ Facturas que requieren revisión</h4>
                                <p>Algunas facturas pueden requerir revisión manual por:</p>
                                <ul class="list-disc list-inside ml-4 space-y-1 mt-2">
                                    <li><strong>Imagen borrosa:</strong> El OCR no puede leer bien los datos</li>
                                    <li><strong>Timbrado inválido:</strong> El timbrado no existe en la SET</li>
                                    <li><strong>RUC inválido:</strong> El RUC no está registrado</li>
                                    <li><strong>Datos incompletos:</strong> Falta información crítica</li>
                                </ul>
                                <p class="mt-3"><strong>Qué hacer:</strong></p>
                                <ol class="list-decimal list-inside ml-4 space-y-1 mt-2">
                                    <li>Ve a la factura marcada como "Revisar"</li>
                                    <li>Corrige manualmente los datos incorrectos</li>
                                    <li>Guarda y la factura queda validada</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    {{-- CONSEJOS FINALES --}}
                    <div class="bg-gray-800 text-white rounded-lg p-6">
                        <h3 class="text-2xl font-bold mb-4 flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            Consejos para Aprovechar al Máximo Dataflow
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 mr-2 flex-shrink-0 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Procesa diariamente:</strong> No esperes a fin de mes. Procesa facturas conforme las recibes para mantener tu contabilidad al día.</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 mr-2 flex-shrink-0 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Usa Telegram:</strong> Es la forma más rápida. Toma foto con tu celular y listo.</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 mr-2 flex-shrink-0 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Revisa antes de exportar:</strong> Siempre verifica que los datos estén correctos antes de generar el Excel para la SET.</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 mr-2 flex-shrink-0 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Aprovecha la validación automática:</strong> Las facturas validadas con la SET (✅) no necesitan revisión adicional.</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 mr-2 flex-shrink-0 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Plan Avanzado para despachos:</strong> Si gestionas múltiples clientes, el Plan Avanzado te permite crear entidades ilimitadas.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

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
