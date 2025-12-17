# Fix: Imágenes del Blog Retornando 404

## 📋 Problema

Las imágenes de artículos del blog no se mostraban, retornando HTTP 404 a pesar de que:
- Los archivos existían físicamente en el servidor
- La base de datos tenía las rutas correctas
- Los permisos de archivo eran correctos (644)

### Síntomas
- Imágenes antiguas: ✅ Funcionaban (servidas desde cache de Cloudflare)
- Imágenes nuevas: ❌ Error 404
- Header `x-powered-by: PHP/8.3.19` en las respuestas 404 (Laravel procesando las requests)
- Header `cf-cache-status: DYNAMIC` (Cloudflare no podía cachear)

### Ejemplo
- ✅ Funciona: https://dataflow.guaraniappstore.com/blog/ruc-paraguay-guia-completa-para-la-inscripcion-y-gestion-fiscal
- ❌ Fallaba: https://dataflow.guaraniappstore.com/blog/regimen-simplificado-en-paraguay-guia-completa-para-pymes

## 🔍 Causa Raíz

**Desajuste entre estructura de directorios y document root:**

```
# Estructura Real del Servidor:
/home/u489458217/domains/dataflow.guaraniappstore.com/public_html/
├── public/                          # Laravel public directory
│   └── uploads/                     # ← Aquí Laravel guarda los archivos
│       └── blog/
│           └── WaiwR2IY...jpg      # ← Archivo existe aquí
└── uploads/                         # ← Directorio vacío/inexistente
    └── blog/
        └── WaiwR2IY...jpg          # ← Servidor busca aquí (404)
```

**¿Qué pasó?**
1. `PexelsService::downloadAndSave()` guarda imágenes en `public_path("uploads/blog")` → `/public_html/public/uploads/blog/`
2. La vista usa `asset('uploads/' . $post->featured_image)` → `https://dataflow.../uploads/blog/imagen.jpg`
3. El servidor busca en `/public_html/uploads/` pero los archivos están en `/public_html/public/uploads/`
4. Laravel procesa la request, no encuentra la ruta, retorna 404

**¿Por qué las imágenes viejas funcionaban?**
- Cloudflare las tenía en cache desde antes de algún cambio de configuración del servidor
- El cache de Cloudflare servía las imágenes directamente sin consultar el servidor

## ✅ Solución

Crear un **symlink** desde `public_html/uploads` hacia `public_html/public/uploads`:

```bash
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html
rm -rf uploads  # Eliminar directorio viejo si existe
ln -sf public/uploads uploads
```

### Verificación
```bash
# Verificar que el symlink se creó correctamente
ls -la | grep uploads
# Output esperado: lrwxrwxrwx uploads -> public/uploads

# Probar acceso a imagen
curl -I https://dataflow.guaraniappstore.com/uploads/blog/WaiwR2IY2sxgwd8y5iZUKC4r2aHFjcNutXKdaI3X.jpg
# Output esperado: HTTP/2 200
```

### Resultado
- ✅ HTTP 200 en lugar de 404
- ✅ `content-type: image/jpeg` (archivo estático)
- ✅ Sin header `x-powered-by: PHP` (ya no pasa por Laravel)
- ✅ Cloudflare puede cachear correctamente (`cf-cache-status: MISS` → `HIT`)

## 🔒 Hacer la Solución Permanente

### 1. Script de Deployment Actualizado

El archivo `deploy.sh` ahora incluye la creación automática del symlink:

```bash
# Crear symlink para uploads si no existe
if [ ! -L "uploads" ] && [ -d "public/uploads" ]; then
    echo -e "${YELLOW}Creando symlink uploads -> public/uploads...${NC}"
    ln -sf public/uploads uploads
    echo -e "${GREEN}✓ Uploads symlink creado${NC}"
fi
```

### 2. Documentación Actualizada

La guía `DEPLOYMENT.md` ahora documenta explícitamente:
- La estructura de directorios requerida
- El comando para crear el symlink
- La explicación de por qué es necesario

### 3. En Futuros Deployments

Al ejecutar `./deploy.sh`, el symlink se creará automáticamente.

Para deployments manuales:
```bash
cd /ruta/al/proyecto
ln -sf public/uploads uploads
```

## 📝 Archivos Modificados

### `/deploy.sh`
- Añadido: Creación automática del symlink `uploads -> public/uploads`

### `/DEPLOYMENT.md`
- Añadida: Sección sobre estructura de directorios
- Añadido: Comando para crear symlink
- Añadida: Explicación de por qué es necesario

### `/docs/BLOG_IMAGES_FIX.md` (este archivo)
- Documentación técnica completa del problema y solución

## 🎯 Lecciones Aprendidas

1. **Symlinks en producción**: Cuando el document root no coincide con la estructura esperada de Laravel, los symlinks son esenciales
2. **Cloudflare cache**: El cache puede ocultar problemas temporalmente, dando falsos positivos
3. **Headers de debug**: El header `x-powered-by: PHP` fue clave para identificar que Laravel estaba procesando requests de archivos estáticos
4. **Documentación**: Documentar estas configuraciones específicas del servidor previene futuros problemas

## 🔄 Mantenimiento Futuro

### Si las imágenes vuelven a fallar después de un deployment:

```bash
# 1. Verificar que el symlink existe
ls -la /ruta/al/proyecto/ | grep uploads

# 2. Si no existe, recrearlo
cd /ruta/al/proyecto/
ln -sf public/uploads uploads

# 3. Verificar acceso
curl -I https://dataflow.guaraniappstore.com/uploads/blog/test.jpg

# 4. Si sigue fallando, verificar permisos
chmod 755 public/uploads
chmod 644 public/uploads/blog/*.jpg
```

### Purgar cache de Cloudflare si es necesario:

1. Login en Cloudflare
2. Seleccionar dominio `guaraniappstore.com`
3. Caching → Configuration
4. Purge Everything o Purge by URL para imágenes específicas

## ✅ Estado Actual

- [x] Problema identificado
- [x] Solución implementada en producción
- [x] Deploy script actualizado
- [x] Documentación actualizada
- [x] Todas las imágenes del blog funcionando
- [x] Symlink persistente y documentado

---

**Fecha del fix:** 17 de Diciembre de 2025
**Aplicado en:** dataflow.guaraniappstore.com
**Servidor:** /home/u489458217/domains/dataflow.guaraniappstore.com/public_html
**Commit:** (pending)
