# 📊 Guía para Contadores - Sistema Aranduka de Validación Fiscal

## 👋 Introducción

Esta guía está diseñada para que **contadores y profesionales fiscales** puedan probar y validar el sistema Aranduka con sus clientes reales.

**¿Qué hace Aranduka?**
- 📸 Extrae datos de facturas usando OCR con inteligencia artificial
- ✅ Valida RUC, Timbrado y datos fiscales con DNIT Paraguay
- 🤖 Funciona por Telegram (muy fácil de usar)
- ⚡ Respuesta en 15-30 segundos
- 🇵🇾 100% compatible con normativa paraguaya (RG-90)

---

## 🎯 Objetivo de esta Prueba

Queremos que **valides** si el sistema:
1. Extrae correctamente los datos fiscales de las facturas
2. Valida correctamente con DNIT/SET
3. Es útil para tu trabajo diario como contador
4. Tus clientes lo encontrarían fácil de usar

---

## 📋 Preparación (5 minutos)

### Paso 1: Instalar Telegram

Si aún no tienes Telegram:
- 📱 **Android**: [Google Play Store](https://play.google.com/store/apps/details?id=org.telegram.messenger)
- 🍎 **iOS**: [App Store](https://apps.apple.com/app/telegram-messenger/id686449807)
- 💻 **Desktop**: [telegram.org/desktop](https://desktop.telegram.org/)

### Paso 2: Buscar el Bot

1. Abre Telegram
2. En el buscador, escribe: `@aranduka_fiscal_bot`
3. Clic en el bot
4. Clic en **"Start"** o **"Iniciar"**

Recibirás un mensaje de bienvenida explicando cómo funciona.

### Paso 3: Preparar Facturas de Prueba

Necesitarás:
- ✅ **3-5 facturas reales** de clientes (formato RG-90)
- ✅ Pueden ser fotos con el celular o PDFs
- ✅ Preferiblemente de diferentes empresas
- ✅ Facturas con **timbrado vigente**

**Tipos de facturas a probar**:
- Factura de venta (más común)
- Factura de compra
- Factura con IVA incluido
- Factura con IVA exento
- Factura con productos gravados al 5% y 10%

---

## 🧪 Prueba 1: Extracción de Datos (OCR)

### Qué vamos a validar:
- ¿Extrae correctamente el RUC del emisor?
- ¿Identifica el timbrado?
- ¿Lee correctamente el número de factura?
- ¿Captura la fecha correctamente?
- ¿Calcula bien los montos (subtotal, IVA, total)?

### Cómo probar:

1. **Toma una foto** de la primera factura con tu celular
   - Asegúrate de que se vea clara
   - Buena iluminación
   - Todos los datos visibles

2. **Envía la foto al bot** de Telegram

3. **Espera la respuesta** (15-30 segundos)

4. **Compara los datos extraídos** con la factura original:

```
📋 CHECKLIST DE VALIDACIÓN:

□ RUC del emisor: ¿Correcto?
□ Razón Social: ¿Correcta?
□ Timbrado: ¿Correcto?
□ N° de Factura: ¿Correcto?
□ Fecha de emisión: ¿Correcta?
□ Subtotal (5%): ¿Correcto?
□ Subtotal (10%): ¿Correcto?
□ IVA (5%): ¿Correcto?
□ IVA (10%): ¿Correcto?
□ Monto Total: ¿Correcto?
□ RUC del receptor: ¿Correcto? (si aplica)
```

5. **Anota los resultados**:
   - ✅ = Dato correcto
   - ⚠️ = Dato parcialmente correcto
   - ❌ = Dato incorrecto o no detectado

### Ejemplo de respuesta esperada:

```
✅ Factura procesada exitosamente

📄 DATOS EXTRAÍDOS:
━━━━━━━━━━━━━━━━━━━━━━━
RUC Emisor: 80012345-6
Razón Social: DISTRIBUIDORA GUARANI SA
Dirección: Av. Artigas 1234, Asunción

Timbrado: 12345678
Factura N°: 001-001-0001234
Fecha: 10/12/2025

RUC Receptor: 9028805-0
Razón Social Receptor: COMERCIAL EJEMPLO SRL

💰 MONTOS:
━━━━━━━━━━━━━━━━━━━━━━━
Gravado 10%: ₲ 1.000.000
IVA 10%: ₲ 100.000
Gravado 5%: ₲ 500.000
IVA 5%: ₲ 25.000
Exentas: ₲ 0
Total: ₲ 1.625.000

🔍 VALIDACIÓN FISCAL:
━━━━━━━━━━━━━━━━━━━━━━━
✅ RUC Emisor: VÁLIDO (ACTIVO)
✅ Timbrado: VIGENTE
   Inicio: 01/06/2025
   Fin: 31/12/2025
✅ Estado General: APROBADO

⏱️ Procesado en 18 segundos
```

---

## 🧪 Prueba 2: Validación Fiscal (DNIT)

### Qué vamos a validar:
- ¿Verifica correctamente la validez del RUC?
- ¿Detecta si un timbrado está vencido?
- ¿Identifica RUCs inactivos o cancelados?

### Cómo probar:

#### Caso 1: Factura con datos válidos
- Envía una factura reciente (últimos 3 meses)
- **Resultado esperado**: ✅ Todos los datos validados correctamente

#### Caso 2: Factura con timbrado vencido (si tienes)
- Envía una factura antigua (>1 año)
- **Resultado esperado**: ⚠️ Alerta de timbrado vencido

#### Caso 3: Factura con error intencional
- Envía una factura y luego **verifica manualmente** en [DNIT](https://www.dnit.gov.py)
- **Resultado esperado**: Sistema debe coincidir con DNIT

---

## 🧪 Prueba 3: Diferentes Tipos de Facturas

### Probar con:

1. **Factura simple** (pocos ítems, un solo tipo de IVA)
   - ¿Procesa correctamente?

2. **Factura compleja** (muchos ítems, IVA mixto 5% y 10%)
   - ¿Separa correctamente los montos?

3. **Factura con letra pequeña** o calidad regular
   - ¿Puede extraer los datos igual?

4. **Factura escaneada** (PDF)
   - ¿Funciona con PDFs además de fotos?

5. **Factura con logo grande** o diseño personalizado
   - ¿Se confunde o extrae bien los datos?

---

## 📝 Formulario de Evaluación

Por favor completa esta evaluación después de las pruebas:

### A) Precisión de Extracción de Datos (OCR)

**Facturas probadas**: _____ (cantidad)

**Precisión general**:
- □ 90-100% (Excelente)
- □ 70-89% (Bueno)
- □ 50-69% (Regular)
- □ <50% (Necesita mejora)

**Campos con mayor precisión**:
- □ RUC emisor
- □ Razón social
- □ Timbrado
- □ Número de factura
- □ Fecha
- □ Montos

**Campos con menor precisión**:
- □ RUC emisor
- □ Razón social
- □ Timbrado
- □ Número de factura
- □ Fecha
- □ Montos

### B) Validación Fiscal (DNIT/SET)

**¿La validación de RUC fue correcta?**
- □ Siempre
- □ La mayoría de veces
- □ A veces
- □ Nunca

**¿La validación de Timbrado fue correcta?**
- □ Siempre
- □ La mayoría de veces
- □ A veces
- □ Nunca

### C) Facilidad de Uso

**¿Qué tan fácil fue usar el bot?**
- □ Muy fácil (cualquiera puede usarlo)
- □ Fácil (necesita explicación breve)
- □ Complicado (necesita capacitación)
- □ Muy complicado

**¿Cuánto tiempo tomó procesar cada factura?**
- □ <15 segundos
- □ 15-30 segundos
- □ 30-60 segundos
- □ >60 segundos

### D) Utilidad Profesional

**¿Usarías este sistema en tu trabajo diario?**
- □ Definitivamente sí
- □ Probablemente sí
- □ Tal vez
- □ No

**¿En qué casos específicos lo usarías?**
- □ Revisión de facturas de compras de clientes
- □ Auditoría de documentos fiscales
- □ Validación rápida de proveedores
- □ Control de timbrados
- □ Verificación de RUCs
- □ Otro: _______________

**¿Cuánto tiempo te ahorraría por factura?**
- □ 1-2 minutos
- □ 3-5 minutos
- □ 5-10 minutos
- □ >10 minutos

### E) Mejoras Sugeridas

**¿Qué funcionalidad adicional sería útil?**
```
[Espacio para comentarios]
```

**¿Qué mejorarías del sistema actual?**
```
[Espacio para comentarios]
```

**¿Encontraste algún error o problema?**
```
[Espacio para comentarios]
```

---

## 🐛 Problemas Comunes y Soluciones

### Problema: "No pude extraer todos los datos"

**Causa**: Imagen borrosa o mal iluminada

**Solución**:
- Tomar foto con mejor luz
- Asegurarse de que el texto sea legible
- Intentar escanear en lugar de fotografiar

### Problema: "RUC no válido" (pero sí es válido)

**Causa**: Base de datos local puede estar desactualizada

**Solución**:
- Verificar manualmente en [DNIT.gov.py](https://www.dnit.gov.py)
- Reportar el caso para actualización

### Problema: "Timbrado vencido" (pero está vigente)

**Causa**: Datos del timbrado pueden estar desactualizados

**Solución**:
- Verificar vigencia en [Marangatu](https://marangatu.set.gov.py)
- Reportar el caso

### Problema: Bot no responde

**Solución**:
1. Verificar conexión a internet
2. Reintentar en 1 minuto
3. Si persiste, contactar soporte

---

## 📊 Casos de Uso Reales

### Caso 1: Auditoría Mensual de Compras

**Situación**: Cliente tiene 50 facturas de compras del mes

**Proceso tradicional**:
- 2-3 minutos por factura
- Total: 100-150 minutos (1.5-2.5 horas)

**Con Aranduka**:
- 30 segundos por factura (enviar foto + verificar)
- Total: 25 minutos
- **Ahorro: 75-125 minutos por mes**

### Caso 2: Verificación de Proveedor Nuevo

**Situación**: Cliente quiere trabajar con proveedor desconocido

**Proceso tradicional**:
- Buscar RUC en DNIT
- Verificar timbrado en Marangatu
- Validar datos
- Total: 5-10 minutos

**Con Aranduka**:
- Enviar foto de factura de muestra
- Total: 30 segundos
- **Ahorro: 4.5-9.5 minutos por verificación**

### Caso 3: Control de Timbrados Vencidos

**Situación**: Revisar facturas archivadas para reporte

**Proceso tradicional**:
- Verificar cada timbrado manualmente
- 1-2 minutos por factura
- Total para 30 facturas: 30-60 minutos

**Con Aranduka**:
- Enviar fotos secuencialmente
- Total: 15 minutos
- **Ahorro: 15-45 minutos**

---

## 💡 Consejos para Mejores Resultados

### ✅ HACER:

- Usar buena iluminación
- Foto directamente desde arriba (90°)
- Incluir toda la factura en el encuadre
- Usar cámara trasera del celular (mejor calidad)
- Probar con facturas variadas

### ❌ EVITAR:

- Fotos con sombras fuertes
- Ángulos muy inclinados
- Zoom excesivo (pixelado)
- Facturas arrugadas o dobladas
- Flash directo (puede crear reflejos)

---

## 📧 Enviar Resultados

Una vez completadas las pruebas, por favor envía:

1. **Formulario de evaluación** (completado)
2. **Cantidad de facturas probadas**
3. **Ejemplos de casos** (éxitos y errores)
4. **Sugerencias** de mejora

**Contacto**: [Tu email o método de contacto]

---

## 🎉 Próximos Pasos

Si la prueba es exitosa, el siguiente paso sería:

1. **Capacitación formal** (30 minutos)
2. **Integración con tu flujo de trabajo**
3. **Acceso para tus clientes** (opcional)
4. **Reportes personalizados** (si lo necesitas)

---

## ❓ Preguntas Frecuentes

### ¿Es seguro enviar facturas por Telegram?

**Sí**. Telegram usa cifrado y las imágenes se procesan en servidor seguro. Además:
- No se almacenan imágenes permanentemente
- Solo se guardan datos extraídos (sin imagen)
- Cumple con normativa de protección de datos

### ¿Qué pasa si el sistema se equivoca?

El sistema muestra los datos extraídos para que **tú los verifiques**. Nunca toma decisiones automáticas sin revisión humana.

### ¿Funciona con facturas electrónicas (e-Kuatia)?

Actualmente está optimizado para facturas físicas (RG-90). Soporte para e-Kuatia está en desarrollo.

### ¿Cuánto cuesta?

[A definir según modelo de negocio]
- Posible modelo freemium
- Posible suscripción mensual
- Posible pago por uso

### ¿Necesito internet?

Sí, el bot requiere conexión a internet para:
- Enviar/recibir mensajes de Telegram
- Procesar OCR
- Validar con DNIT

### ¿Funciona en todo Paraguay?

Sí, funciona en todo el territorio nacional y con cualquier RUC paraguayo.

---

## 📞 Soporte

¿Necesitas ayuda durante las pruebas?

- 📧 **Email**: [Tu email]
- 📱 **WhatsApp**: [Tu número]
- 💬 **Telegram**: [Tu usuario]

---

**¡Gracias por tu colaboración en mejorar Aranduka! 🇵🇾**

Tu feedback es invaluable para crear la mejor herramienta fiscal para contadores paraguayos.

---

**Desarrollado para Aranduka-Core Platform**
Sistema de gestión fiscal inteligente para Paraguay
