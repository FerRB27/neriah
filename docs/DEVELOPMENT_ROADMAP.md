# Neriah ERP Development Roadmap

Esta ruta mantiene el desarrollo centrado en el negocio antes de construir pantallas. El orden importa: Kardex, Produccion y Finanzas son el corazon del ERP.

## Fase 1: Seguridad y base operativa

- Login web con Blade/Livewire.
- Roles y permisos con Spatie: Administrador, Elaborador, Vendedor, Distribuidor, Consulta.
- Middleware por permiso para cada modulo.
- Layout interno, navegacion lateral y dashboard vacio protegido.
- Auditoria base con Spatie Activity Log para modelos criticos.

## Fase 2: Catalogos maestros

- Personas: elaboradores, vendedores y distribuidores.
- Clientes con indicadores comerciales.
- Insumos, categorias, productos y variantes.
- Formulas y sus ingredientes.
- Proveedores y canales de venta.

## Fase 3: Kardex e inventario

- Pantalla de Kardex por item.
- Entradas por compra.
- Salidas por produccion, venta, merma y ajuste.
- Stock actual calculado desde movimientos y persistido en `inventory_items`.
- Alertas de inventario critico.

## Fase 4: Compras

- Registro de proveedores.
- Orden/registro de compra con lineas.
- Confirmacion de compra.
- Actualizacion de costo promedio.
- Historial de costos.

## Fase 5: Produccion

- Orden de produccion basada en formula.
- Calculo de insumos requeridos.
- Confirmacion de produccion.
- Descuento de insumos.
- Entrada de producto terminado.
- Costo real de produccion.

## Fase 6: Ventas y comisiones

- Registro de venta por cliente, vendedor, elaborador, canal y promocion.
- Confirmacion de venta.
- Descuento de inventario.
- Calculo de utilidad visible y utilidad oculta.
- Generacion automatica de comisiones y pagos a elaboradores.

## Fase 7: Finanzas y fondo social

- Distribucion automatica 10/40/25/25.
- Capital del fundador: aportes, reintegros y saldo pendiente.
- Fondo social: acumulados, donaciones, beneficiarios y evidencias.
- Reportes de utilidad visible, oculta y total.

## Fase 8: Dashboard ejecutivo

- Ventas semana, mes y anio.
- Utilidad visible, oculta y total.
- Produccion.
- Inventario critico.
- Pagos pendientes.
- Capital fundador pendiente.
- Fondo social acumulado.
- Top vendedores, clientes y productos.

## Fase 9: Endurecimiento

- Tests de acciones criticas: compras, produccion, ventas, Kardex y finanzas.
- Policies por dominio.
- Jobs/queues para reportes, backups y notificaciones.
- Backups programados.
- Semillas demo ampliadas.
