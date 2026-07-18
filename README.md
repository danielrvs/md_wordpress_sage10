# Directorio Médico Enterprise (MD Press)

Este repositorio contiene una plataforma moderna y de alto rendimiento para la gestión y búsqueda de un directorio médico, junto con un portal de noticias/blog especializado en salud. Está estructurado utilizando prácticas de nivel empresarial sobre WordPress, acoplando una arquitectura basada en dominios y caché distribuida.

---

## 🚀 Arquitectura y Tecnologías

La infraestructura del proyecto está completamente dockerizada y construida sobre el siguiente stack:

*   **Servidor Web y PHP**: **FrankenPHP 1.4** (servidor de alto rendimiento construido sobre Caddy) ejecutando **PHP 8.4**.
*   **Base de datos**: **MySQL 8.4**.
*   **Caché y Almacenamiento Key-Value**: **Redis 8.4** alpino.
*   **Estructura WordPress**: **Roots Bedrock** (gestión de dependencias vía Composer, configuración multi-entorno mediante variables `.env` y estructura de directorios moderna).
*   **Tema del Sitio**: **Sage (Roots)**, que integra el contenedor de servicios de **Laravel (Acorn 6)** y motor de plantillas **Blade**.
*   **Compilador de Assets**: **Vite v8**.
*   **Maquetación y UI**: **Tailwind CSS v4** con una paleta de acentos basados en **Verde Esmeralda** y diseño oscuro/glassmorphic.
*   **Componentes Reactivos**: **React 19** y **TypeScript** integrados en el tema Sage (utilizado para el componente interactivo de búsqueda de especialistas).

---

## 🛠️ Requisitos Previos

*   [Docker](https://www.docker.com/) instalado en tu sistema.
*   [Docker Compose](https://docs.docker.com/compose/) instalado.

---

## 🏁 Configuración e Inicio

### 1. Levantar el entorno local
El proyecto dispone de scripts sencillos para administrar los contenedores de Docker. En la raíz del repositorio, ejecuta:

```bash
./start.sh
```

Este script leerá el archivo `.env` en la raíz, detectará el entorno (`dev`) e iniciará los contenedores de MySQL, Redis y FrankenPHP en segundo plano.

Para detener el entorno, ejecuta:
```bash
./stop.sh
```

### 2. Ejecutar la instalación inicial (Setup)
La primera vez que levantes el entorno, es necesario ejecutar el script de aprovisionamiento para descargar dependencias de PHP y Node, instalar la base de datos de WordPress, activar el tema `md-press` y habilitar los plugins principales (como ACF y Redis Cache):

```bash
docker-compose -f infrastructure/docker-compose.dev.yml exec app /app/wp/bin/setup.sh
```

### 3. URL de Acceso
*   **Sitio Web (Frontend)**: [http://localhost](http://localhost)
*   **Administrador de WordPress (Backend)**: [http://localhost/wp/wp-admin/](http://localhost/wp/wp-admin/)
    *   *Credenciales de Instalación por Defecto:*
        *   **Usuario**: `admin_enterprise`
        *   **Contraseña**: `pass_enterprise_2026`

---

## 🎨 Desarrollo de Assets (Vite)

Para compilar y compilar en tiempo real los estilos (Tailwind CSS v4) y la lógica de React (TypeScript) dentro del tema Sage, debes ejecutar los comandos correspondientes **dentro del contenedor de PHP/App** donde se encuentra instalado Node/pnpm.

### Modo de Desarrollo (Hot Reloading / Dev Server)
```bash
docker-compose -f infrastructure/docker-compose.dev.yml exec app pnpm --dir /app/wp/web/app/themes/md-press dev
```

### Compilación para Producción (Build)
```bash
docker-compose -f infrastructure/docker-compose.dev.yml exec app pnpm --dir /app/wp/web/app/themes/md-press build
```

---

## 📁 Estructura del Proyecto

```text
├── infrastructure/               # Configuración de Docker
│   ├── php/Dockerfile.dev        # Dockerfile para FrankenPHP, Node.js y extensiones PHP
│   └── docker-compose.dev.yml    # Orquestación de servicios (app, db, redis)
├── wp/                           # Núcleo Bedrock de WordPress
│   ├── config/                   # Configuración del core y constantes del entorno
│   │   ├── application.php       # Configuración global común
│   │   └── environments/         # Overrides por entorno (development.php, staging.php)
│   ├── web/                      # Document Root público
│   │   └── app/
│   │       └── themes/
│   │           └── md-press/     # Tema Sage con Tailwind, Blade y React
└── README.md                     # Documentación principal
```

### Tema: `md-press`
La lógica y diseño de la aplicación se encapsulan en el tema Sage:
*   **`app/Domain/`**: Contiene la lógica del negocio estructurada en dominios aislados.
    *   **Doctors**: CPT Doctor, DTOs, Repositorio (`WpQueryDoctorRepository`), Repositorio Decorador con Caché (`CachedDoctorRepository`), e Invalidador de caché en base a hooks de guardado de posts.
*   **`app/Providers/ThemeServiceProvider.php`**: Inicializa los setups de dominios y realiza el Binding en el contenedor IoC de Laravel para inyectar repositorios/servicios.
*   **`resources/views/`**: Vistas de Blade.
    *   `front-page.blade.php`: Pantalla de inicio con métricas y el punto de montaje de React.
    *   `home.blade.php`: Archivo general del Blog que lista las noticias del portal médico.
    *   `partials/content-single.blade.php`: Plantilla para entradas individuales de blog que soporta Gutenberg nativo y comentarios.
    *   `sections/header.blade.php`: Cabecera con menú (Inicio, Directorio, About, Blog), buscador integrado y acceso a login.
*   **`resources/js/components/`**: Componentes de React en TypeScript, como el buscador dinámico `MedicalSearchDirectory.tsx`.
*   **`resources/css/app.css`**: Hoja de estilos con los imports de Tailwind v4 y estilos específicos para la maquetación de Gutenberg y la paginación de WordPress.

---

## ⚡ Caching de Datos (Redis + Laravel Cache)

La API de médicos utiliza un patrón de repositorio decorador para acelerar las consultas:
1.  **`CachedDoctorRepository`**: Intercepta las llamadas de consulta de médicos (`all`, `search`, `findById`, `count`) y almacena los resultados en Redis utilizando el Driver de Cache de Laravel.
2.  **Invalidación de Caché**: Cada vez que se crea, edita o elimina un post del Custom Post Type `doctor` (tanto desde la administración como vía API), se disparan los hooks `save_post_doctor` y `before_delete_post` que ejecutan `invalidate_doctor_cache()`, limpiando automáticamente las llaves específicas y el patrón de listados (`doctors:*`) de la memoria de Redis.

---

## 📋 Comandos Útiles

### Ejecutar comandos WP-CLI
```bash
docker-compose -f infrastructure/docker-compose.dev.yml exec app wp <comando> --allow-root
```

### Vaciar la caché de vistas Blade de Acorn
Si realizas modificaciones en las vistas Blade y el compilador no refleja los cambios de inmediato, ejecuta:
```bash
docker-compose -f infrastructure/docker-compose.dev.yml exec app wp acorn view:clear --allow-root
```
