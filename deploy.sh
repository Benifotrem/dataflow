#!/bin/bash

echo "================================================"
echo "   DEPLOYMENT SCRIPT - CONTAPLUS"
echo "   dataflow.guaraniappstore.com"
echo "================================================"
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar si estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: No se encontró el archivo artisan. ¿Estás en el directorio raíz de Laravel?${NC}"
    exit 1
fi

echo -e "${YELLOW}1. Verificando requisitos...${NC}"
php -v || { echo -e "${RED}PHP no está instalado${NC}"; exit 1; }
composer --version || { echo -e "${RED}Composer no está instalado${NC}"; exit 1; }

echo -e "${GREEN}✓ Requisitos OK${NC}"
echo ""

echo -e "${YELLOW}2. Instalando dependencias...${NC}"
composer install --optimize-autoloader --no-dev
echo -e "${GREEN}✓ Dependencias instaladas${NC}"
echo ""

echo -e "${YELLOW}3. Configurando entorno...${NC}"
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}Copiando .env.example a .env...${NC}"
    cp .env.example .env
    echo -e "${GREEN}✓ Archivo .env creado${NC}"
    echo -e "${YELLOW}⚠️  IMPORTANTE: Edita el archivo .env con tus credenciales${NC}"
    read -p "Presiona Enter cuando hayas configurado .env..."
fi

echo -e "${YELLOW}4. Generando APP_KEY...${NC}"
php artisan key:generate --force
echo -e "${GREEN}✓ APP_KEY generada${NC}"
echo ""

echo -e "${YELLOW}5. Creando enlaces simbólicos...${NC}"
php artisan storage:link
echo -e "${GREEN}✓ Storage link creado${NC}"

# Crear symlink para uploads si no existe
if [ ! -L "uploads" ] && [ -d "public/uploads" ]; then
    echo -e "${YELLOW}Creando symlink uploads -> public/uploads...${NC}"
    ln -sf public/uploads uploads
    echo -e "${GREEN}✓ Uploads symlink creado${NC}"
elif [ -L "uploads" ]; then
    echo -e "${GREEN}✓ Uploads symlink ya existe${NC}"
fi
echo ""

echo -e "${YELLOW}6. Ejecutando migraciones...${NC}"
read -p "¿Deseas ejecutar las migraciones? (s/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Ss]$ ]]; then
    php artisan migrate --force
    echo -e "${GREEN}✓ Migraciones ejecutadas${NC}"
fi
echo ""

echo -e "${YELLOW}7. Optimizando aplicación...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
echo -e "${GREEN}✓ Optimización completada${NC}"
echo ""

echo -e "${YELLOW}8. Configurando permisos...${NC}"
chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✓ Permisos configurados${NC}"
echo ""

echo "================================================"
echo -e "${GREEN}   ✓ DEPLOYMENT COMPLETADO${NC}"
echo "================================================"
echo ""
echo "Próximos pasos:"
echo "1. Configurar cron job para tareas automáticas"
echo "2. Verificar que el sitio sea accesible"
echo "3. Configurar Brevo (contaplus@guaraniappstore.com)"
echo "4. Agregar API Key de OpenAI en .env"
echo ""
echo "Tu aplicación estará en:"
echo "https://dataflow.guaraniappstore.com"
echo ""
