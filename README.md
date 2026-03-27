
# ERP - Ferretería

Sistema de gestión para ferreterías desarrollado con Laravel. Permite llevar un control completo de ventas, clientes, productos, presupuestos, caja y cuenta corriente.

## Tecnologías

* Backend: Laravel 11 (PHP 8.2+)
* Frontend: Blade, Tailwind CSS, JavaScript, jQuery
* Base de datos: MySQL / MariaDB
* Librerías adicionales: Select2, Font Awesome

## Funcionalidades

### Productos

* Registrar y administrar productos: nombre, código, precio, stock, categoría y marca.
* Control de stock mínimo y ubicación en depósito.
* Actualización masiva de precios.

### Clientes

* Registro de clientes con datos de contacto y documento.
* Cuenta corriente con seguimiento de saldo y límite de crédito.
* Historial de ventas y pagos.

### Ventas

* Registrar ventas con varios productos.
* Métodos de pago: efectivo, tarjeta, transferencia y cuenta corriente.
* Estados: completada, pendiente, cancelada.
* Ticket optimizado para impresión térmica.
* Control de pagos parciales y saldos pendientes.

### Presupuestos

* Crear y administrar presupuestos.
* Estados: borrador, enviado, aprobado, rechazado y convertido a venta.
* Conversión directa a venta desde un presupuesto aprobado.
* Impresión tipo factura AFIP.

### Cuenta Corriente

* Movimientos automáticos al registrar ventas.
* Registro manual de pagos y ajustes.
* Visualización de saldo actualizado.

### Caja

* Apertura y cierre de turnos de caja.
* Control de ingresos, egresos y ventas del día.
* Reporte de diferencias entre monto esperado y real.

### Reportes

* Filtros por fecha, estado, cliente y método de pago.
* Resumen rápido de ventas, facturación y saldos pendientes.

### Usuarios y roles

* Autenticación con Laravel.
* Roles: administrador, vendedor, cajero.
* Registro de intentos de acceso.

## Base de datos

Tablas principales por módulo:

| Módulo      | Tablas                                         |
| ------------ | ---------------------------------------------- |
| Productos    | productos, categorias, marcas, unidades_medida |
| Clientes     | clientes, cuenta_corriente_movimientos         |
| Ventas       | ventas, venta_detalles                         |
| Presupuestos | presupuestos, presupuesto_detalles             |
| Caja         | turnos_caja, movimientos_caja                  |
| Usuarios     | usuarios, roles                                |
| Pedidos      | pedidos                                        |

## Instalación


# Clonar repositorio

git clone https://github.com/tiagoraminelli/ERP---ferreteria.git
cd ERP---ferreteria

# Instalar dependencias PHP

composer install

# Instalar dependencias frontend

npm install && npm run build

# Copiar archivo de entorno

cp .env.example .env

# Configurar base de datos en .env y ejecutar migraciones

php artisan migrate

# Iniciar servidor local

php artisan serve
