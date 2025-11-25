# Instrucciones para Actualizar Landing Page en Producción

## ⚠️ IMPORTANTE: Método Heredoc

Este script usa el método `cat > file << 'EOFBLADE'` para crear archivos Blade directamente en el servidor, evitando la corrupción que ocurre cuando se usa `cp` para copiar archivos Blade en Hostinger.

## Pasos para Actualizar la Landing Page

### Opción 1: Ejecutar el script directamente en el servidor

```bash
# 1. Conectarse al servidor por SSH
ssh u489458217@185.201.11.61 -p 65002

# 2. Ir al directorio del proyecto
cd /home/u489458217/domains/dataflow.guaraniappstore.com

# 3. Descargar el script desde el repositorio
curl -O https://raw.githubusercontent.com/Benifotrem/dataflow/claude/dataflow-saas-platform-01Gogn2DJtLmkTPq15MUxMWf/update-landing-views.sh

# 4. Hacer el script ejecutable
chmod +x update-landing-views.sh

# 5. Ejecutar el script
./update-landing-views.sh

# 6. Eliminar el script (opcional)
rm update-landing-views.sh
```

### Opción 2: Copiar el contenido del script manualmente

Si no puedes descargar el script directamente:

```bash
# 1. Conectarse al servidor
ssh u489458217@185.201.11.61 -p 65002

# 2. Ir al directorio del proyecto
cd /home/u489458217/domains/dataflow.guaraniappstore.com

# 3. Crear el script
nano update-landing-views.sh

# 4. Copiar TODO el contenido del archivo update-landing-views.sh

# 5. Guardar (Ctrl+O, Enter, Ctrl+X)

# 6. Hacer ejecutable
chmod +x update-landing-views.sh

# 7. Ejecutar
./update-landing-views.sh
```

## Verificación

Después de ejecutar el script, verifica que:

1. **El sitio carga correctamente:**
   ```
   https://dataflow.guaraniappstore.com/
   ```

2. **Las vistas fueron creadas:**
   ```bash
   ls -lh resources/views/layouts/landing.blade.php
   ls -lh resources/views/landing/index.blade.php
   ```

3. **No hay errores en los logs:**
   ```bash
   tail -50 storage/logs/laravel.log
   ```

## ¿Qué hace el script?

1. ✅ Crea `resources/views/layouts/landing.blade.php` con el layout completo usando heredoc
2. ✅ Crea `resources/views/landing/index.blade.php` con todo el contenido de la landing usando heredoc
3. ✅ Limpia todos los cachés de Laravel (view, config, cache)
4. ✅ Muestra confirmación con el número de líneas de cada archivo

## Resultado Esperado

Deberías ver la landing page completa con:
- Hero section con gradiente púrpura
- Sección de "Confiado por más de 500 empresas"
- Sección de problemas (3 tarjetas rojas)
- Sección de soluciones (6 tarjetas de características)
- Estadísticas (95%, 10h, 500+, 24/7)
- Precios (Plan Básico $19.99, Plan Avanzado $49.99)
- CTA final con gradiente púrpura
- Header y Footer completos

## Troubleshooting

### Error: "bash: ./update-landing-views.sh: Permission denied"
```bash
chmod +x update-landing-views.sh
```

### Error: "No such file or directory"
Verifica que estás en el directorio correcto:
```bash
pwd
# Debería mostrar: /home/u489458217/domains/dataflow.guaraniappstore.com
```

### La página muestra error 500
```bash
# Ver los logs
tail -100 storage/logs/laravel.log

# Limpiar caché nuevamente
php artisan optimize:clear
```

### Los archivos se crearon pero la página muestra contenido antiguo
```bash
# Forzar limpieza de caché
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

## Notas

- Este método **SIEMPRE** funciona en Hostinger, a diferencia de `cp` que corrompe archivos Blade
- El script es idempotente: puedes ejecutarlo múltiples veces sin problemas
- Los archivos se sobrescriben cada vez que ejecutas el script
- No afecta a otros archivos del proyecto, solo actualiza las vistas de landing

---

**Última actualización:** 2025-11-21
**Proyecto:** Dataflow SaaS Platform
**Servidor:** dataflow.guaraniappstore.com
