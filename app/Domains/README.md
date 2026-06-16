# Neriah ERP Domain Structure

Este proyecto se organiza por dominios de negocio bajo `app/Domains`.

El orden de construccion recomendado para Neriah es:

1. Modelo de dominio
2. Migraciones
3. Relaciones Eloquent
4. Seeders
5. Casos de uso en `Actions` y `Services`
6. Kardex de inventario
7. Produccion
8. Ventas
9. Finanzas
10. Dashboard
11. UI con Blade, Livewire y Alpine

El nucleo del sistema es el Kardex: el stock no se modifica manualmente; siempre cambia por movimientos de inventario asociados a compras, produccion, ventas, ajustes, mermas, entregas o devoluciones.
