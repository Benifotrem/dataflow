# Capturas de Pantalla para Email de Backup Mensual

Este directorio contiene las imágenes que se muestran en el email de backup mensual enviado a los clientes.

## Imágenes Requeridas

### backup-step1.png
**Descripción:** Captura mostrando cómo descargar el archivo adjunto de Gmail
- Tamaño recomendado: 600px de ancho
- Debe mostrar: Un email de Gmail con un archivo Excel adjunto, resaltando el botón de descarga
- Contexto: Gmail en español con el email de Dataflow abierto

**Cómo capturar:**
1. Abre Gmail
2. Busca un email con archivo adjunto (puede ser de prueba)
3. Haz zoom para que se vea el archivo adjunto claramente
4. Captura la pantalla mostrando el archivo Excel adjunto
5. Opcionalmente, añade una flecha o círculo resaltando el botón de descarga

---

### backup-step2.png
**Descripción:** Captura de la página principal de Google Drive
- Tamaño recomendado: 600px de ancho
- Debe mostrar: La interfaz principal de Google Drive (drive.google.com)
- Contexto: Vista de "Mi unidad" con algunas carpetas existentes

**Cómo capturar:**
1. Ve a https://drive.google.com
2. Asegúrate de estar en la vista "Mi unidad"
3. Captura la pantalla mostrando el área principal de Drive
4. Opcionalmente, resalta el área donde se pueden ver las carpetas

---

### backup-step3.png
**Descripción:** Captura mostrando cómo crear una nueva carpeta en Google Drive
- Tamaño recomendado: 600px de ancho
- Debe mostrar: El menú contextual de Google Drive con la opción "Nueva carpeta" resaltada
- Contexto: Click derecho en el área de Drive o el botón "Nuevo"

**Cómo capturar:**
1. En Google Drive, haz clic derecho en un espacio vacío
2. Captura el menú que aparece, mostrando claramente "Nueva carpeta"
3. Alternativamente, captura el botón "Nuevo" → "Carpeta"
4. Opcionalmente, resalta la opción "Nueva carpeta"

---

### backup-step4.png
**Descripción:** Captura mostrando cómo subir archivos a Google Drive
- Tamaño recomendado: 600px de ancho
- Debe mostrar: El proceso de arrastrar un archivo Excel a una carpeta de Drive O el botón de subir archivo
- Contexto: Vista de una carpeta abierta con un archivo siendo arrastrado o el diálogo de subida

**Cómo capturar:**
1. Opción A: Captura mientras arrastras un archivo Excel hacia Drive
2. Opción B: Captura el menú "Nuevo" → "Subir archivo"
3. Opción C: Captura el diálogo de selección de archivos
4. Opcionalmente, resalta el área de drop zone o el botón de subida

---

### backup-step5.png
**Descripción:** Captura mostrando cómo etiquetar un correo en Gmail
- Tamaño recomendado: 600px de ancho
- Debe mostrar: El menú de etiquetas de Gmail o el proceso de crear/aplicar una etiqueta
- Contexto: Email seleccionado con menú de etiquetas abierto

**Cómo capturar:**
1. En Gmail, selecciona un email
2. Haz clic en el icono de etiquetas (tag icon)
3. Captura el menú de etiquetas que aparece
4. Opcionalmente, muestra el proceso de crear una nueva etiqueta "Backup Dataflow"
5. Opcionalmente, resalta la opción de crear etiqueta nueva

---

## Instrucciones de Edición

### Herramientas Recomendadas:
- **Windows:** Snipping Tool o Snip & Sketch (Win + Shift + S)
- **Mac:** Screenshot Tool (Cmd + Shift + 4)
- **Online:** Photopea.com, Canva.com
- **Edición:** GIMP, Photoshop, Paint.NET

### Formato:
- Formato: PNG
- Ancho: 600px (máximo)
- Calidad: Alta
- Peso: Máximo 200KB por imagen (optimizar si es necesario)

### Mejoras Opcionales:
- Añadir flechas o círculos para resaltar elementos importantes
- Usar colores de la marca Dataflow (#6366F1 - Indigo)
- Añadir sombras suaves para dar profundidad
- Asegurar que el texto sea legible

---

## Placeholder Actual

Mientras no se agreguen las imágenes reales, el email mostrará placeholders. El email seguirá siendo funcional y las instrucciones textuales son suficientemente claras.

Para reemplazar los placeholders:
1. Captura las pantallas según las instrucciones anteriores
2. Edítalas y optimízalas
3. Nómbralas exactamente como se indica arriba
4. Súbelas a este directorio: `public/images/email/`
5. El email las mostrará automáticamente

---

## Verificación

Para verificar que las imágenes se muestran correctamente:
1. Ejecuta el comando de prueba: `php artisan backup:monthly --tenant-id=1`
2. Revisa el email recibido
3. Verifica que todas las imágenes cargan correctamente
4. Verifica que sean claras y útiles para el usuario

---

**Nota:** Si prefieres no incluir capturas de pantalla, puedes comentar o eliminar las secciones `.screenshot` en el archivo `/resources/views/emails/monthly-backup.blade.php`. Las instrucciones textuales son suficientemente claras para que los usuarios sigan el proceso.
