#!/usr/bin/env bash

set -e

# Constantes para el nombre y directorio del tema
THEME_NAME="md-press"
THEME_DIR="/app/wp/web/app/themes/${THEME_NAME}"

# Variables de configuración de ejecución
WITH_DATA=false
for arg in "$@"; do
    if [ "$arg" = "--with-data" ]; then
        WITH_DATA=true
    fi
done

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Función para mostrar mensajes
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_info "🚀 Iniciando el entorno Enterprise Wordpress!"

# Nos aseguramos de estar en la raíz de la aplicación WordPress
cd /app/wp

log_info "🔧 Configurando parámetros del cliente SQL para entorno local..."
cat <<EOF > ~/.my.cnf
[client]
ssl-mode=DISABLED
skip-ssl
EOF

if [ ! -d "/app/wp/vendor" ]; then
    log_info "📦 Instalando dependencias de PHP con Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
    composer dump-autoload --optimize
    log_success "✅ Dependencias de PHP instaladas correctamente"
else
    log_success "✅ Dependencias de PHP (Composer) ya instaladas"
fi

if [ ! -f "/app/wp/.env" ]; then
    log_info "📄 Creando archivo .env desde la plantilla .env.example..."
    cp /app/wp/.env.example /app/wp/.env
    log_warning "⚠️  ¡ATENCIÓN!: Revisa tu archivo .env y asegúrate de que las credenciales de la BD coincidan con tu docker-compose.yml."
fi

log_info "⏳ Verificando que la base de datos MySQL esté disponible..."
MAX_RETRIES=20
RETRY_COUNT=0

until wp db check --allow-root > /dev/null 2>&1; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        log_error "❌ Error: La base de datos MySQL no respondió tras ${MAX_RETRIES} intentos."
        log_info "Detalle de error devuelto por WP-CLI:"
        wp db check --allow-root || true
        exit 1
    fi
    sleep 2
done
log_success "✅ Base de datos MySQL disponible"

if ! wp core is-installed --allow-root; then
    log_info "🌐 WordPress no está instalado. Iniciando instalación limpia vía WP-CLI..."

    WP_SITE_URL=${WP_HOME:-"http://localhost"}
    WP_ADMIN_USER="admin_enterprise"
    WP_ADMIN_PASS="pass_enterprise_2026"
    WP_ADMIN_EMAIL="architecture@enterprise.com"

    wp core install \
        --url="${WP_SITE_URL}" \
        --title="Directorio Médico Enterprise" \
        --admin_user="${WP_ADMIN_USER}" \
        --admin_password="${WP_ADMIN_PASS}" \
        --admin_email="${WP_ADMIN_EMAIL}" \
        --skip-email \
        --allow-root

    log_success "✅ WordPress instalado con éxito."
    log_info "🔑 Usuario Administrador: ${WP_ADMIN_USER}"
    log_info "🔑 Contraseña: ${WP_ADMIN_PASS}"
else
    log_success "✅ WordPress ya se encuentra instalado en la base de datos."
fi

log_info "🔗 Configurando la estructura de enlaces permanentes (/blog/%postname%/)..."
wp rewrite structure '/blog/%postname%/' --hard --allow-root
wp rewrite flush --hard --allow-root


if [ -d "${THEME_DIR}" ]; then
    if [ -f "${THEME_DIR}/composer.json" ] && [ ! -d "${THEME_DIR}/vendor" ]; then
        log_info "📦 Instalando dependencias PHP (Composer) del tema Sage..."
        composer install --working-dir="${THEME_DIR}" --no-interaction --prefer-dist --optimize-autoloader
    fi

    log_info "🎨 Activando el tema Sage: ${THEME_NAME}..."
    wp theme activate "${THEME_NAME}" --allow-root
    log_success "✅ Tema Sage activado correctamente"
else
    log_error "❌ ERROR: No se encontró la carpeta del tema en ${THEME_DIR}. Verifica el nombre."
    exit 1
fi

log_info "🔌 Activando Plugins Críticos (ACF & Redis Cache)..."
wp plugin activate advanced-custom-fields redis-cache --allow-root
log_success "✅ Plugins activados correctamente"

if [ -f "${THEME_DIR}/package.json" ]; then
    cd "${THEME_DIR}"
    if [ ! -d "node_modules" ]; then
        log_info "⚛️  Instalando dependencias de Node.js (React / Tailwind) en el tema..."
        pnpm install
    else
        log_success "✅ Dependencias de Node.js ya instaladas."
    fi
    
    log_info "🏗️  Compilando assets de desarrollo con Vite..."
    pnpm run build
else
    log_error "⚠️  No se encontró package.json en el tema. Saltando instalación de frontend."
    exit 1
fi

log_info "Migrando tablas de schema..."
wp schedule:migrate --allow-root
wp appointment:migrate --allow-root
log_success "✅ Tablas de schema migradas correctamente"

log_success "🎉 Entorno aprovisionado y listo para desarrollo enterprise!"

log_info "Creando páginas estáticas esenciales (Pricing, Directorio)..."
wp page:seed --allow-root

if [ "$WITH_DATA" = true ]; then
    log_info "Generando datos demo para el directorio médico..."
    wp doctor:seed --count=25 --allow-root
    wp schedule:seed --allow-root
    wp appointment:seed --count=50 --allow-root
    log_success "✅ Datos demo generados correctamente"
else
    log_info "Saltando la generación de datos demo (ejecuta con --with-data si deseas incluirlos)."
fi
