#!/usr/bin/env bash

set -e

# Constantes para el nombre y directorio del tema
THEME_NAME="md-press"
THEME_DIR="/app/wp/web/app/themes/${THEME_NAME}"

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

log_info "⏳ Verificando que la base de datos MySQL esté disponible"
until wp db check --allow-root > /dev/null 2>&1; do
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


if [ -d "${THEME_DIR}" ]; then
    log_info "🎨 Activando el tema Sage: ${THEME_NAME}..."
    wp theme activate "${THEME_NAME}" --allow-root
    log_success "✅ Tema Sage activado correctamente"
else
    log_error "❌ ERROR: No se encontró la carpeta del tema en ${THEME_DIR}. Verifica el nombre."
    exit 1
fi

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

log_success "🎉 Entorno aprovisionado y listo para desarrollo enterprise!"