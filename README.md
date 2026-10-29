# 🚀 Laravel Modular Architecture
Arquitectura modular profesional, elegante y ligera para Laravel (Modular Monolith) creada por **Daniel Alva**.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/danielalva/laravel-modular.svg?style=flat-square)](https://packagist.org/packages/danielalva/laravel-modular)
[![Total Downloads](https://img.shields.io/packagist/dt/danielalva/laravel-modular.svg?style=flat-square)](https://packagist.org/packages/danielalva/laravel-modular)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.2-777bb4.svg?style=flat-square)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-10%20%7C%2011%20%7C%2012-FF2D20.svg?style=flat-square)](https://laravel.com)

---

## 👨‍💻 Autor y Contacto
- **Creador:** **Daniel Alva (Luis Daniel Alva Luján)**
- **LinkedIn:** [https://www.linkedin.com/in/luis-daniel-alva-lujan/](https://www.linkedin.com/in/luis-daniel-alva-lujan/)
- **GitHub:** [@luisdaniel9986](https://github.com/luisdaniel9986)

---

## 💡 ¿Por qué usar Laravel Modular?

En proyectos medianos y grandes de Laravel, las carpetas por defecto (`app/Http/Controllers`, `app/Models`, `database/migrations`) tienden a saturarse con cientos de archivos mezclados.

**Laravel Modular** resuelve esto transformando cada funcionalidad en un **módulo autocontenido**:

```text
modules/
├── Productos/
│   ├── Controllers/
│   │   └── ProductoController.php
│   ├── Models/
│   │   └── Producto.php
│   ├── Migrations/
│   │   └── 2026_10_29_000000_create_productos_table.php
│   ├── Routes/
│   │   ├── web.php
│   │   └── api.php
│   ├── Views/
│   │   └── index.blade.php
│   └── Requests/
└── Ventas/
    └── ...
```

---

## 📦 Instalación

En tu proyecto Laravel:

```bash
composer require danielalva/laravel-modular
```

Gracias al **Package Discovery** de Laravel, el paquete se registra automáticamente.

### Configurar Autoload de Composer
Añade el namespace de los módulos a tu `composer.json` principal:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Modules\\": "modules/"
    }
}
```

Luego regenera el autoloader:
```bash
composer dump-autoload
```

---

## ⚡ Uso y Comandos Artisan

### 1. Crear un Módulo Completo
```bash
php artisan make:module Productos
```
Crea al instante la estructura completa del módulo con su controlador, modelo, migración base, rutas (web y api) y vista Blade.

### 2. Crear un Controlador en un Módulo
```bash
php artisan module:controller Productos DetalleController
```

### 3. Crear un Modelo (con migración opcional)
```bash
php artisan module:model Productos Categoria -m
```

### 4. Crear una Migración adicional
```bash
php artisan module:migration Productos add_stock_to_productos_table
```

---

## 🔄 Carga Automática de Componentes

- **Rutas Web:** Se cargan desde `modules/{Modulo}/Routes/web.php` con middleware `web`.
- **Rutas API:** Se cargan desde `modules/{Modulo}/Routes/api.php` con prefijo `/api` y middleware `api`.
- **Migraciones:** Se integran automáticamente a `php artisan migrate`.
- **Vistas Blade:** Se invocan usando el namespace del módulo en minúsculas:
  ```php
  return view('productos::index');
  ```

---

## ⚙️ Configuración (Opcional)

Si deseas cambiar el directorio base de los módulos o deshabilitar alguna carga automática, publica la configuración:

```bash
php artisan vendor:publish --tag=modular-config
```

---

## 📄 Licencia

Este proyecto está bajo la Licencia **MIT**. Consulta el archivo [LICENSE](LICENSE) para más detalles.
