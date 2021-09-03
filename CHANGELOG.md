## 4.1.3

### docs
2021-07-20 : docs | changelog update<br>


### fixed
2021-08-06 : fixed | version php causa error sintaxis en consulta de series de notas de venta por api<br>
2021-08-06 : fixed | #446 | Removiendo debug<br>
2021-08-06 : fixed | #446 | Finanzas: Cuentas por cobrar: Ajuste de orden para orden de compra y plataforma<br>
2021-08-06 : fixed | #446 | Finanzas: Cuentas por cobrar: Ajuste de orden para orden de compra y plataforma<br>
2021-08-06 : fixed | #446 | Platform por Plataforma<br>
2021-08-06 : fixed | #446 | Ajuste de columnas en reporte general de productos<br>
2021-08-06 : fixed | - | Hoteles: Habitaciones: Ajuste para no seleccionar almacenes en habitaciones<br>
2021-08-05 : fixed | #777 | Notas: Ajuste cargos globales en nota credito y debito<br>
2021-08-04 : fixed | - | Formato de codigo y simplificacion de querys<br>
2021-08-04 : fixed | - | Formato de codigo<br>
2021-08-04 : fixed | - | Problemas de short tag en php,<br>
2021-08-04 : fixed | - | validando retorno para null en configuracion<br>
2021-08-02 : fixed | #787 |Compile JS<br>
2021-08-02 : fixed | #787 | Guia: Añadiendo posiblidad de anular la guia, si el documento relacionado se encuentra anlulado (se tomará en cuenta el ultimo documento relacionado)<br>
2021-08-02 : fixed | - | El reporte de venta, se le quita la extension xlsx para pdf, ya que no es necesaria<br>
2021-08-02 : fixed | - | S extra en sale notes<br>
2021-07-30 : fixed | #808 | Reporte General de Productos: Ajuste para SaleNoteItem<br>
2021-07-30 : fixed | #787 | Compile JS<br>
2021-07-30 : fixed | - | Añadiendo Log como Log::channel('facturalo')->debug('X'); para log especificos. Quitando log de error de bank en global payment<br>
2021-07-30 : fixed | - | Propiedad Bank no se encuentra<br>
2021-07-30 : fixed | #787 | Lista de Documentos: Aádido Pedidos a la tabla de documentos<br>
2021-07-27 : fixed | #757 | Documentos: Ajustes observaciones descuentos por linea<br>
2021-07-27 : fixed | #787 | Compile JS<br>
2021-07-27 : fixed | #787 | Colecciones devuelven Nota de venta para Documentos<br>
2021-07-27 : fixed | #787 | Documentos: En el listado de documentos, se añade nota de venta para cuando esta cuente con las relaciones. Tambien se añade el estado de las mismas<br>
2021-07-27 : fixed | - | Minor: Ajuste de estilo<br>
2021-07-27 : fixed | #656 | Comprobante: Se extiende la exoneracion de igv para los items:<br>
2021-07-27 : fixed | #656 | Compile JS<br>
2021-07-27 : fixed | #656 | Nota de venta : Se extiende la exoneracion de igv para los items:<br>
2021-07-27 : fixed | #656 | Nota de venta / invoice: Cuando se generan notas de venta y pasan a factura, estas no generan correctamente el IGV, se ajusta para que se obtenga el IGV del item en DB<br>
2021-07-26 : fixed | #446 | Compile JS<br>
2021-07-26 : fixed | #446 | Reporte de general de venta: Se añade la columna plataforma. Reporte de nota de ventas: Se añade Status del pago.<br>
2021-07-26 : fixed | #745 | Reporte de Venta: Ajuste para buscar correctamente purchase_item<br>
2021-07-26 : fixed | - | testingPrice en reporte - venta AÑADIENDO LOG<br>
2021-07-26 : fixed | - | testingPrice en reporte - venta AÑADIENDO LOG<br>
2021-07-26 : fixed | - | testingPrice en reporte - venta<br>
2021-07-26 : fixed | - | testingPrice en reporte - venta<br>
2021-07-26 : fixed | - | testingPrice en reporte - venta<br>
2021-07-23 : fixed | #773 | Compile JS<br>
2021-07-23 : fixed | #773 | Reporte de ventas general : Añadiento campos de modelo y orden de compra<br>
2021-07-23 : fixed | #745 | Ajuste para evaluar el recurso cuando es llamado desde el reporte<br>
2021-07-23 : fixed | #773 | Ajuste para evaluar el recurso cuadno es llamado desde el reporte<br>
2021-07-23 : fixed | #711 | encabezado de tabla de banccos sobre plantilla default3_banks<br>
2021-07-23 : fixed | #743 | Compile JS<br>
2021-07-23 : fixed | #743 | Items Compuestos: Modelo pasa a la ultima columna<br>
2021-07-23 : fixed | #743 | Items Compuestos: Añadiendo modelo a la edicion y al ejempo de xlsx para importar<br>
2021-07-23 : fixed | #790 | Compile JS<br>
2021-07-23 : fixed | #790 | Devolucion de Item: Se ajusta inventory warehouse y kardex para el item devuelto al editarlo.<br>
2021-07-23 : fixed | #790 | Devolucion de Item: Se ajusta inventory warehouse y kardex para el item devuelto al editarlo.<br>
2021-07-21 : fixed | #745 | Reportes: Reporte general de productos: Ajuste para validar los totales de ganancia en USD y PEN<br>
2021-07-21 : fixed | #613 | Reporte de Venta: Ajuste para mostrar impuesto.<br>
2021-07-21 : fixed | #718 | Nota de venta: Generar CPE: Titulo ajustado correctamente a su nota de venta<br>
2021-07-21 : fixed | | Minor: Ajuste de array $attributes<br>
2021-07-21 : fixed | | fixed error array_exist in  /Requests/Inputs/DocumentInput.php:221<br>
2021-07-21 : fixed | | fixed error array_exist in  /Requests/Inputs/DocumentInput.php:221<br>


### feature
2021-08-06 : feature | #596 | Compile JS<br>
2021-08-06 : feature | #596 | Cotizacion/Pedido: Pedido desde cotizacion, solo es posible por el admin y si no se tiene un pedido.<br>
2021-08-05 : feature | #596 | Cotizacion/Pedido: Habilita generar pedido desde cotizaciones.<br>
2021-08-05 : feature | #772 | Se agrega importacion series en compras - seleccion automatica de serie en ventas nuevo cpe<br>
2021-08-05 : feature | #446 | Compile JS<br>
2021-08-05 : feature | #446 | Finanzas: Cuentas por cobrar: Reporte de Todos. Añadiendo plataforma y orden de compra<br>
2021-08-04 : feature | #446 | Finanzas: Cuentas por cobrar: Reporte Formas de pago (dias)<br>
2021-08-04 : feature | #446 | Finanzas: Cuentas por cobrar: Exportar Pdf<br>
2021-08-04 : feature | #446 | Finanzas: Cuentas por cobrar: Exportar Excel<br>
2021-08-04 : feature | #446 | Finanzas: Cuentas por cobrar: Ajuste para componentes reactivos<br>
2021-08-04 : feature | #446 | Finanzas: Cuentas por cobrar: Añadiendo plataforma y orden de compra.<br>
2021-08-04 : feature | #446 | Añadiendo web_platforms a cuentas por cobrar<br>
2021-08-04 : feature | #446 | Reporte Nota de venta: Añadiendo v-if="row.web_platforms !== undefined" en caso que la vista sea consultada desde otro modulo<br>
2021-08-04 : feature | #446 | Reporte Documentos: Añadido plataforma antes de orden de compra<br>
2021-08-04 : feature | #446 | Reporte Nota de Venta: Ajsute para añadir plataforma antes de orden de compra.<br>
2021-08-04 : feature | #446 | Reporte Nota de Venta: Ajsute para añadir plataforma. Se origina desde los items, por lo que pueden haber mas de una plataforma<br>
2021-08-04 : feature | #622 | Compile JS<br>
2021-08-04 : feature | #622 | Nota de venta: Mostrar la url de destino de la consulta<br>
2021-08-03 : feature | #622 | Nota de venta: Ajuste para mensajes de comunicacion e insercion.<br>
2021-08-03 : feature | #622 | Nota de venta: Ajuste para mensajes de comunicacion e insercion.<br>
2021-08-03 : feature | #622 | Nota de venta: Ajuste para mensajes de comunicacion e insercion.<br>
2021-08-03 : feature | #622 | Nota de venta: Enviando Datos al servidor B<br>
2021-08-03 : feature | #622 | Configuracion: Añadiendo web, token y habilitar por configuracion para el envio de nota de venta.<br>
2021-08-03 : feature | #622 | Guarda la nota de venta en otro servidor mediante api. Si el item no se encuentra, se crea.<br>
2021-06-24 : feature | #622 | Obliga al api a crear clientes.<br>
2021-06-24 : feature | #622 | Exportar Nota de venta a otra plataforma Facturalo<br>
2021-08-02 : feature | #773 | $web_platform por $platform<br>
2021-08-02 : feature | #773 | Reporte General de Productos: Añadiendo items de pack en el reporte excel / pdf<br>
2021-07-30 : feature | #794 | Compile JS<br>
2021-07-30 : feature | #794 | Usuarios: Generando cambio de token<br>
2021-07-27 : feature | - | Añadiendo filtrado a tipo de afectacion en el dialogo de item<br>
2021-07-27 : feature | - | Añadiendo constantes para affectation_igv_types_exonerated_unaffected = '20', '21', '30', '31', '32', '33', '34', '35', '36', '37' en la coleccion de configuracion<br>
2021-07-27 : feature | #800 | Reporte de Documentos: Añadiendo Distrito, provincia y departamento para descarga en PDF y XLSX<br>


## 4.1.2

### docs
2021-07-07 : docs | changelog<br>


### fixed
2021-07-20 : fixed | #770 #789 | Compile JS<br>
2021-07-20 : fixed | #770 #789 | Nota de venta y CPE: Ajuste para igv en en exonerado al añadir item<br>
2021-07-20 : fixed | #601 | consulta campo json en mariabd no soportada<br>
2021-07-19 : fixed | #613 | Contabilidad: Exportar Reporte: Venta: Validando condicion para tipo de documento y status del documento<br>
2021-07-16 : fixed | slack | problema al descargar reporte de items para wordpress<br>
2021-07-16 : fixed | #782 | Nota de venta: Modal de pagos: Cambio de number fil de identifier a getNumberFullAttribute()<br>
2021-07-15 : fixed | #761 | Guia de Remision: Ajuste para retirar el item de la guia.<br>
2021-07-15 : fixed | #702 | $type_movement leido correctamente en el reporte excel y pdf<br>
2021-07-15 : fixed | #613 | Llamado correcto al modelo en ReportItemController.php<br>
2021-07-15 : fixed | #613 | Contabilidad: Exportar Reporte: Ajuste para mostrar la moneda de los movimientos en el reporte<br>
2021-07-15 : fixed | #746 | Fixed index<br>
2021-07-14 : fixed | #761 #775 | Compile JS<br>
2021-07-14 : fixed | #761 | Comprobantes Avanzados: Guias de Remision: Añadir item: Ajuste para añadir items correctamente.<br>
2021-07-13 : fixed | #702 | Ajuste error MovementCollection.php:53<br>
2021-07-13 : fixed | #742 | plantillas de guias muestran productos compuestos nuevamente - se recrea el pdf a partir del boton opciones - mismo boton muestra el pdf en el navegador mientras el otro lo descarga directamente<br>
2021-07-13 : fixed | #702 | Finanzas: Movimiento: Posiblidad de descargar el pdf y el xml ordenado<br>
2021-07-12 : fixed | #686 | pie de pagina legenda amazonia - ajuste de espacios respecto a configuracion de visual pdf<br>
2021-07-12 : fixed | #747 | Reporte de Venta: Cambio de orden de columnas distrito por departamento<br>
2021-07-12 : fixed | #771 | Compile JS<br>
2021-07-12 : fixed | #749 | Reporte Caja Chica POS: Ajuste para que los pagos de compra se sumen al egreso total.<br>
2021-07-12 : fixed | #771 | Customer por defecto en CPE<br>
2021-07-12 : fixed | #686 | pto1, plantilla legend_amazonia cuenta con leyenda correcta para todos los formatos tipo ticket de factura y boleta<br>
2021-07-09 : fixed | #753 | Reporte de cuentas por cobrar: La columna total de expenses estaba en la posicion 9, la posicion correcta es 8<br>
2021-07-09 : fixed | #652 | scroll para listado de guias al generar mediante multiple seleccion<br>
2021-07-09 : fixed | #741 | Reporte de Venta: Ajuste de columna en reporte por documentos<br>
2021-07-08 : fixed | #747 | Reporte de Venta: Cambio de localizacion de establecimiento por cliente<br>
2021-07-08 : fixed | - | Añadiendo comentario de la ruta url para facilitar buscarla<br>
2021-07-08 : fixed | #642 | Item: descripcion de productos: Se establece el stock para el establecimiento del usuario (no general) en los componentes d eañadir item<br>
2021-07-08 : fixed | #310 | Guias: corrección ortográfica<br>
2021-07-07 : fixed | #718 | Nota de venta: Generar CPE desde multiples NV: Corrige mostrar los CPE relacionados en el listado de nota de venta.<br>


### feature
2021-07-20 : feature | #771 | template basado en default3_new_account con mas espacio para las cuentas bancarias<br>
2021-07-19 : feature | #702 | Removiendo # de las tablas<br>
2021-07-19 : feature | #596 | Compile JS<br>
2021-07-19 : feature | #596 | Compile JS<br>
2021-07-19 : feature | #596 | Reporte de inventario: Añadiendo profit como diferencia entre costo y precio.<br>
2021-07-19 : feature | #596 | Textos entre cliente y proveedor<br>
2021-07-19 : feature | #596 | Cliente: Dias de crédito: Mostrando la columna de dias<br>
2021-07-19 : feature | #596 | Cliente: Dias de crédito: Añadiendo campo de dias de credito<br>
2021-07-16 : feature | #601 | envio de mensaje al administrador cuando se genera un pedido en ecommerce (plus : historial de pedidos)<br>
2021-07-14 : feature | #746 | Api: Guias de remision:  PDF: Se muestra documento afectado.Se añade el campo documento_afectado de la forma siguiente :    "documento_afectado": {     "serie_documento": "F001",     "numero_documento": "190",     "codigo_tipo_documento": "01"   }<br>
2021-07-13 : feature | #748 | Compile #702 #748 #679<br>
2021-07-13 : feature | #748 | Template DEFAULT_DATE_END: Valida la configuracion, si es farmacia, se le ajusta el registro sanitario del producto<br>
2021-07-13 : feature | #679 | Compras: Añadiendo Nota dedebito y nota de credito<br>
2021-07-12 : feature | loretosot habilita metodo de pago en mobilecontroller para uso en app<br>
2021-07-12 : feature | #686 | pie de pagina legenda amazonia - ajuste de espacios<br>
2021-07-08 : feature | #640 | crear nuevo cliente en formulario de guia lo asigna automaticamente al campo correspondiente<br>
2021-07-08 : feature | api validador para app<br>
2021-07-07 : feature | #310 | Comprobantes avanzados: Se agrega funcionalidad para control de stock en guias<br>
2021-07-07 : feature | #747 | Reporte de ventas: Añadiendo departamento, distrito y provincia para exportar pdf y excel<br>
2021-07-07 : feature | #557 | estructura de plantilla heaer_image_full_width aplicada en cotizaciones, pedidos y notas de venta<br>
2021-07-06 : feature | #310 | Comprobantes avanzados: Se agrega flujo inventario a guias (avance)<br>


## 4.1.1

### docs
2021-06-25 : docs | Update README.md<br>
2021-06-10 : docs | update changelog<br>


### fixed
2021-07-07 : fixed | #699 | mostrando cantidades de lista de precios y calculando montos de compra en funcion a estos<br>
2021-07-07 : fixed | #615 | Compile JS<br>
2021-07-07 : fixed | #615 | Nota de venta: Listado: Seleccionar el vendedor en la nota de venta.<br>
2021-07-07 : fixed | #716 #615 #454 #547 #674 | Compile JS<br>
2021-07-06 : fixed | #674 | Nota de ventas: Vendedor puede crear items desde nota de ventas, si esta habilitado por la configuracion<br>
2021-07-06 : fixed | #716 #615 #454 #547 | Nota de ventas: Añadiendo nombre personalizado de item en planilla default. Posibilidad de añadir vendedor en las notas de venta. Enviando configuracion standar. Modal de generar CPE es cerrado correctamente. Item se homologa a item CPE, puede buscarse por codigo de barra, editarlo, impuesto a bolsa plastica, atributos adicionales.<br>
2021-07-06 : fixed | - | buble continuo en validador<br>
2021-07-06 : fixed | #493 | Compile JS<br>
2021-07-06 : fixed | #493 | Reporte general: Compras: Ajuste para ver serie de productos comprados.<br>
2021-07-06 : fixed | - | Tramite documentario: Ajuste para siguiente y anterior.<br>
2021-07-06 : fixed | - | Tramite documentario: Ajuste para siguiente y anterior.<br>
2021-07-06 : fixed | - | Compile JS - localStorage almacena 5mb solamente. sessionStorage puede almacena mas datos mientras la Tab no se cierre.<br>
2021-07-05 : fixed | #141 | Nuevo cpe: Ajustes metodos de pago en edicion de cpe<br>
2021-07-05 : fixed | #141 | Ajuste orden asignacion moneda cpe<br>
2021-07-05 : fixed | #738 | Reportes: Ajustes reporte compras<br>
2021-07-01 : fixed | #627 | Cotizaciones: se agrega la posibilidad de elegir vendedores<br>
2021-07-01 : fixed | #722 | Compras: validacion caja<br>
2021-06-29 : fixed | #659 | Ventas: Ajustes valores facturacion en importacion de documentos F1<br>
2021-06-28 : fixed | #507 | Pos - Productos: Aplica precios por almacen a productos - Ajuste crud precios por almacen<br>
2021-06-28 : fixed | #720 | compile js<br>
2021-06-25 : fixed | - | Probando descargar 5000 items<br>
2021-06-25 : fixed | #269 | Compile JS<br>
2021-06-25 : fixed | #269 | Compras: Importar XML : El importador se basa en el mismo XML firmado generado por Facturalo. Se añade obtener 600 items debido que el importador requiere de estos para comparar los datos<br>
2021-06-25 : fixed | - | Compile js<br>
2021-06-25 : fixed | - | CPE: Cuando añades metodos de pago, se divide entre cada uno.<br>
2021-06-24 : fixed | #650 | eliminado parametro width de <td> en la vista, al parecer impide en ciertas versiones la descarga correcta<br>
2021-06-24 : fixed | #714 | Compile JS<br>
2021-06-24 : fixed | #714 | CPE: Ajuste en contado para que ajuste el total directamente. Ajuste para ampliar lotes.<br>
2021-06-24 : fixed | #590 | Compile JS<br>
2021-06-24 : fixed | - | Ajuste para no mostrar error en ticket_58<br>
2021-06-24 : fixed | #590 | Farmacia: Ajuste para importar y exportar items de DIGEMID.<br>
2021-06-23 : fixed | - | CPE: Lista todos los usuarios cuando tienes uno por defecto, no borra los anteriores.<br>
2021-06-21 : fixed | #590 | Ajuste para guardar correctamente el permiso de farmacia<br>
2021-06-17 : fixed | #590 | Creacion de modulo DIGEMID. Falta importacion por catalogo y menu<br>
2021-06-17 : fixed | #308 | Reportes consolidados: Exportar items en pdf: fix de indices no encontrados<br>
2021-06-15 : fixed | #664 #631 #657 | Compile js<br>
2021-06-15 : fixed | #681 | Permitir busqueda por codigo de barras en POS en el campo normal<br>
2021-06-14 : fixed | #631 | Pedido: Habilita vendedor para que pueda generar comprobantes<br>
2021-06-14 : fixed | #631 | Configuracion: Cambio de texto "Permite habilitar las acciones en oportunidad de venta para vendedores" a "Permite habilitar las acciones para vendedores".<br>
2021-06-14 : fixed | #664 | Registro de items en caja: Al editar un item, se reporta en caja nuevamente. Se realiza ajuste para que cuando se edite, pueda actualizarse y no duplicar el registro<br>
2021-06-14 : fixed | #637 | Reporte Cuentas por pagar, añadiendo columna vendedor<br>
2021-06-14 : fixed | #678 | Compile JS<br>
2021-06-09 : fixed | #591 | Comando: php artisan tenancy:run recurrency:sale-note : Se ajusta para que el numero sea consecutivo basado el serie y prefijo<br>


### feature
2021-07-06 : feature | - | Tramite Docuemntario: Añadiendo dias festivos<br>
2021-07-06 : feature | - | Tramite Documentario: Correccion de fecha final de la etapa<br>
2021-07-06 : feature | - | Tramite Documentario: Compile JS<br>
2021-07-05 : feature | #551 | Cotizaciones: Agrega seleccion de almacen al generar cpe<br>
2021-07-01 : feature | - | Tramite Documentario: Ajuste para las 4 etapas de tramite documentario / Erro ticket_58 en cotizacion<br>
2021-07-01 : feature | - | Tramite Documentario: Ajuste para las 4 etapas de tramite documentario<br>
2021-07-01 : feature | - | Tramite Documentario: Ajuste para las 4 etapas de tramite documentario<br>
2021-07-01 : feature | - | Tramite Documentario: Ajuste para las 4 etapas de tramite documentario<br>
2021-07-01 : feature | #722 | Pos: Se integra compras a caja chica<br>
2021-07-01 : feature | - | Tramite Documentario: Compile JS<br>
2021-07-01 : feature | - | Tramite Documentario: Texto Oficina a Etapa, Icono de ojo para historico de observaciones. Filtro se establece como inicial el dia de hoy.<br>
2021-06-30 : feature | - | Tramite Documentario: Falta ajustar devolver.<br>
2021-06-30 : feature | - | Tramite Documentario: Ajuste para cuando ya no se tiene procesos hijos<br>
2021-06-30 : feature | - | Tramite Documentario: Ajuste para cuando ya no se tiene procesos hijos<br>
2021-06-30 : feature | - | Tramite Documentario: Compile JS<br>
2021-06-30 : feature | #722 | Pos: Agregando compras a caja chica<br>
2021-06-30 : feature | - | Tramite Documentario: Admin puede ver todos los expedientes, pero solo se podran ver los expedientes en la etapa asignada para los usuarios.<br>
2021-06-30 : feature | - | Tramite Documentario: Ajuste para obtener las observaciones. Textos y visual.<br>
2021-06-30 : feature | #232 | Ventas: Se agregan cargos globales a nuevo cpe<br>
2021-06-30 : feature | - | Tramite Documentario: Ajuste de retornos<br>
2021-06-30 : feature | - | Tramite Documentario: Ajuste de retornos<br>
2021-06-30 : feature | - | Tramite Documentario: Compilacion JS<br>
2021-06-30 : feature | - | Tramite Documentario: Ajuste para carga de archivos, atras y adelante en el proceso de tramite<br>
2021-06-30 : feature | mostrando boton de servidor alterno para que puedan cambiar a NO en la bd de cada facturador, complementar con configuracion de archivo .env<br>
2021-06-30 : feature | #699 | unidad de medida en listado de productos generales<br>
2021-06-29 : feature | - | Tramite Documentario: Mejoras al modulo, Ajustes para cargar archivos por dropzone, descargar y borrarlo. Cuadro de observaciones.<br>
2021-06-29 : feature | #232 | configuracion: se agrega config para recargo al consumo<br>
2021-06-28 : feature | - | Tramite Documentario: Mejoras al modulo, Oficinas se llama Etapas. Pueden tener 1 hijo. Proceso tiene precio.<br>
2021-06-28 : feature | #695 | template full_height_ticket ancho de imagen logo completa, solo para tickets<br>
2021-06-25 : feature | - | instalacion de Vuex<br>
2021-06-25 : feature | #629 | Reporte de Caja POS: Añadiendo Nota de debito y credito a los documentos que se relacionan.<br>
2021-06-25 : feature | - | Añadiendo info de version de laravel a la config<br>
2021-06-23 : feature | #702 | Aumento a 1000 registros para provocar error 504<br>
2021-06-23 : feature | #702 | Compile JS<br>
2021-06-23 : feature | #702 | Finanzas: Movimiento: Ajuste de la tabla para que sea por frontend y pueda ordenarse.<br>
2021-06-22 : feature | #702 |   ini_set('max_execution_time', 0);<br>
2021-06-22 : feature | #702 | Compile JS<br>
2021-06-22 : feature | #702 | removiendo paginacion<br>
2021-06-22 : feature | #702 | Compile JS<br>
2021-06-22 : feature | #702 | Balance: Finanzas: Ajuste de tabla con el-table para testing. Se cambia la paginacion por frontend<br>
2021-06-22 : feature | #596 - 5 | Clientes: Listado: Añadiendo columnas para su visualizacion opcional<br>
2021-06-22 : feature | #596 - 5 | Menor: Ajuste de estilo de codigo<br>
2021-06-22 : feature | #596 - 5 | Clientes: Añadiendo datos de clientes (Zona, Observacion, Sitio Web)<br>
2021-06-22 : feature | #596 - 5 | Clientes: Añadiendo datos de clientes (Zona, Observacion, Sitio Web)<br>
2021-06-21 : feature | #493 | Compile js<br>
2021-06-21 : feature | #493 | Reporte: Compra: Ocultando plataforma cuando es por compras<br>
2021-06-21 : feature | #493 | Reporte: Compra: Reporte por productos: Exportar productos de forma individual<br>
2021-06-21 : feature | #493 | Ajuste para busqueda de productos por compra<br>
2021-06-21 : feature | #493 | Documentacion de Modelos<br>
2021-06-21 : feature | #493 | Reporte: Compra: Porductos generales<br>
2021-06-18 : feature | #493 | Añadiendo ruta para los reportes<br>
2021-06-18 : feature | #590 | Compile JS<br>
2021-06-18 : feature | #590 | Ajuste para no sobreescribir datos del item<br>
2021-06-18 : feature | #590 | Modulo DIGEMID: Ajuste de permisos para DIGEMID<br>
2021-06-18 : feature | #590 | Modulo DIGEMID: Ajuste de importacion masiva<br>
2021-06-18 : feature | reporte general de productos muestra nota de credito en opciones del filtro y reporte excel para ventas muestra vendedor<br>
2021-06-18 : feature | #688 | moneda en reportes de cuentas por cobrar/pagar<br>
2021-06-16 : feature | #599 | Guias de Remision: Ajuste para prevenir el enviado automatico a Sunat por configuracion. En vez de eso, se hará manual por la lista.<br>
2021-06-16 : feature | #599 | Guias de Remision: Ajuste para prevenir el enviado automatico a Sunat por configuracion. En vez de eso, se hará manual por la lista.<br>
2021-06-16 : feature | #627 | Cotizacion: Generar CPE: Permie seleccionar vendedor<br>
2021-06-16 : feature | #603 | Cotizacion: Nueva Cotizacion: Generar comprobante: Se añade Observacion y orden de compra<br>
2021-06-16 : feature | #308 |  Compile JS<br>
2021-06-16 : feature | #308 | Reporte: Guias: Creando el modulo de Consolidado Por Items.<br>
2021-06-16 : feature | #308 | Ajuste para normalizar las colleciones mediante el modelo.<br>
2021-06-15 : feature | #533 | mejoras visuales en pos<br>
2021-06-15 : feature | #308 | Ajuste de estilo de codigo<br>
2021-06-15 : feature | #657 | Documentacion de variables<br>
2021-06-15 : feature | #657 | Pedidos: Nuevo / Editar producto se le añade nombre pdf. Template default para order note se ajusta el nombre pdf o, la descripcion del item<br>
2021-06-10 : feature | #588 #648 | Compile JS<br>
2021-06-10 : feature | #588 | Producto/Servicio: Producto: Se añade la lista de columnas si la configuracion es de farmacia<br>
2021-06-10 : feature | #648 | Finanzas: Pagos: Se añade cuentas bancarias al destino de modo BankAccount::class.'::'. id, y asi filtrar el banco correspondiente.<br>
2021-06-10 : feature | #588 | Producto/Servicio: Producto: Si esta habilitada la configuracion de farmacia, se muestra registro sanitario y codigo de observacion<br>
2021-06-10 : feature | #588 | Configuracion: Empresa: Añade la posibilidad de colocar el codigo de observacion DIGEMID a la empresa<br>


## 4.1.0

### docs
2021-05-17 : docs | changelog<br>


### fixed
2021-06-10 : fixed | #649 | si no se aplica tipo vendedor y selecciona un nombre de vendedor se sobre entiende que se usa -vendedor asignado-<br>
2021-06-09 : fixed | #630 | Ventas: Pedidios: Nuevo/Editar: Item: Si esta activado la configuracion "Permitir Editar precio unitario a vendedores" y no es admin, podra editar el precio unitario en los pedidos.<br>
2021-06-09 : fixed | #676 | reporte documentos pdf, se traslada los totales a la posicion correcta<br>
2021-06-08 : fixed | - | Clave de usuarios de tenant 5.3.1 para para restaurarlas en mysql.user<br>
2021-06-08 : fixed | #661 | marca repetida al seleccionar con enter un producto en pos<br>
2021-06-07 : fixed | #653 | relacion de item en cotizacion para mostrar el nombre de producto para pdf<br>
2021-06-04 : fixed | #653 | nombre producto pdf en plantilla customer_contact<br>
2021-06-04 : fixed | #455 | Test Set time limit 3900 para balance<br>
2021-06-04 : fixed | #307 | Ajuste en ruta para series<br>
2021-06-03 : fixed | #307 | Menor: Ajuste de estilo<br>
2021-06-03 : fixed | #568 | Producto: Duplicado: Se añade (duplicado) a la descripcion del item para poder distinguirlo cuando es duplicado<br>
2021-06-03 : fixed | #572 | Configuracion: Metodo de ingreso: Añadiendo columna de condicion de pago en ingresos.<br>
2021-06-03 : fixed | #572 | Ajuste de templates para mostrar metodo de pago cuando es credito.<br>
2021-06-03 : fixed | #572 | Comrpobante electronico: Generar pdf: Ajuste para almacenar el metodo de pago en fee y poder filtrarlo al momento de mostrar la factura o boleta.<br>
2021-06-02 : fixed | #514 | Compile JS<br>
2021-06-02 : fixed | #514 | Compile JS<br>
2021-06-02 : fixed | #514 | Compras: Gastos diversos: Nuevo: Añadiendo filtro a Motivo.<br>
2021-06-01 : fixed | #455 | Finanzas: Ingreso y Egreso por metodo de pago: Añadiendo Totales. Mejorando el rendimiento del reporte.<br>
2021-06-01 : fixed | #455 | Finanzas: Balance: Añadiendo saldo inicial. Totales. Mejorando el rendimiento del reporte.<br>
2021-05-31 : fixed | #455 | menor, ajuste de estilo.<br>
2021-05-31 : fixed | #572 | Ajustando documentacion para excluir elementos en metodo de pago, Ajustando scope para ExcludeMethodTypes.<br>
2021-05-28 : fixed | #572 | Venta: Comprobante Electrónico: Añadiendo Crédito y Crédito con cuotas a la factura.<br>
2021-05-27 : fixed | #626 | Compile JS<br>
2021-05-27 : fixed | #626 | Venta: Oportunidad de venta: Habilita la configuracion para que un vendedor pueda tener acciones en las oportunidades de venta para generar comprobantes.<br>
2021-05-27 : fixed | #616 | Compile JS<br>
2021-05-27 : fixed | #616 | Pedido: Generar Comprobante: Se añade boleta y nota de venta cuando el customer tiene Doc.trib.no.dom.sin.ruc o DNI como documento de identificacion<br>
2021-05-26 : fixed | #399 #580 | Compile JS<br>
2021-05-26 : fixed | #580 | Editar/Crear item: Al momento de crear el modal, se limpia el precio por almacen.<br>
2021-05-26 : fixed | #399 #580 |<br>
2021-05-26 : fixed | #399 | Integraicion del commit cdd7c6ab<br>
2021-05-25 : fixed | #591 | Nota de venta: Crear/Editar: Cuando el tipo de periodo y cantidad de periodo no esten vacios, se mostrará un aviso para mostrar cuando se duplicará la nota de venta<br>
2021-05-25 : fixed | #616 | Pedidos: Listado: Generar Comprobante: Habilitando todos los tipos de comprobantes enviados por modules/Order/Http/Controllers/OrderNoteController.php::212<br>
2021-05-25 : fixed | #269 |  Compras: Listado: Importar: Añadiendo notificaciones para XML que no cumplan con los elementos requeridos.<br>
2021-05-25 : fixed | #619 | error pagina en blanco al exportar productos general; se aumenta la cantidad que soporta php a convertir el html de la vista para el reporte<br>
2021-05-24 : fixed | #578 | Contabilidad: Exportar Reporte: Venta: Si no se encuentra el documento, se añaden los datos de  data_affected_document para mostrarlo en el reporte<br>
2021-05-24 : fixed | #482 | Reporte de Caja: Si el documento es a Credito, no se sumará si esta anulado.<br>
2021-05-21 : fixed | #500 | Ventas: Pedidos:  Anular: Evaluar correctamente el has_sale<br>
2021-05-21 : fixed | #500 | Ventas: Pedidos: Series: Cuando se añade un producto, la serie se evalua correctamente al realizar un pedido<br>
2021-05-21 : fixed | #500 | Ventas: Pedidos: Al anular un pedido cuyos items tengan lotes, estos se vuelven a habilitar.<br>
2021-05-21 : fixed | #587 | reporte general de productos campos codigo interno y unidad de medida<br>
2021-05-20 : fixed | #578 | Contabilidad: exportar reporte: venta: si existe data_affected_document, se buscara el documento, si este existe, se tomará para el calculo.<br>
2021-05-20 : fixed | #473 | Finanzas: Movimientos: Exportar Excel: Se ajusta el nombre de la hoja para maximo 30 caracteres.<br>
2021-05-20 : fixed | #609 | ajustes al mostrar las guias de remision de la plantilla legend_amazonia<br>
2021-05-19 : fixed | #611 | redireccion de diferentes usuario en la vista de ordenes de compras<br>
2021-05-19 : fixed | #604 | Productos: Item: Exportar Excel: Se añade la lista de precios para los elementos consultados en el reporte.<br>
2021-05-19 : fixed | #602 | enlaces eliminados u ocultos en ecommerce<br>
2021-05-19 : fixed | #357 | Pos: Venta: Packs: Evalua el inventario de cada item del pack multiplicando la cantidad individual en el pack por la cantidad solicitada. Debe habilitarse stock_control para la validacion<br>
2021-05-18 : fixed | #595 | error en variables que muestran cargos, agregado los cargos globales en totales del pdf<br>


### feature
2021-06-09 : feature | #646 #645 #644 #630 #586 | Compile JS<br>
2021-06-09 : feature | #646 #645 #644 #630 #586 | Compile JS<br>
2021-06-09 : feature | #645 | Comprobantes avanzados: Guias de remisión: Al crear, Permite seleccionar rapidamente transportistas y conductores. Se llena el input con los valores de los selectores.<br>
2021-06-09 : feature | #645 | Comprobantes avanzados: Guias de remisión: Al crear, Permite seleccionar rapidamente transportistas y conductores. Se llena el input con los valores de los selectores.<br>
2021-06-09 : feature | #646 | Productos y servicios: Productos: Añadido Marcas y Modelo al listado.<br>
2021-06-09 : feature | #586 | Ventas: Nota de ventas: Habilita la  funcion de duplicar la nota de venta.<br>
2021-06-09 : feature | #645 | Comprobantes avanzados: Guias de remisión: Permite seleccionar rapidamente transportistas y conductores. Se llena el input con los valores de los selectores.<br>
2021-06-09 : feature | #645 | Menor: Ajuste de estilo de codigo.<br>
2021-06-09 : feature | #644 | Configuracion: Empresa: Avanzado: Pdf: Ajuste para permitir la seleccion de actualizacion del pdf de documento al generar la guia.<br>
2021-06-09 : feature | #644 | Ajuste para añadir configuraciones de actualizar documentos al generar despacho.<br>
2021-06-09 : feature | #644 | Al generar la guia, se actualiza automaticamente el pdf de factura.<br>
2021-06-09 : feature | #644 | Ajuste en modelo para actualizar los archivos pdf.<br>
2021-06-07 : feature | #650 | campo numerico ahora es alfanumerico<br>
2021-06-07 : feature | #650 | reporte excel para gastos diversos<br>
2021-06-04 : feature | #654 | en campo vendedores de formulario de venta se muestran tanto admin con vendedores del establecimiento actual<br>
2021-06-04 : feature | #307 #572 | Compile JS<br>
2021-06-04 : feature | #307 | Comprobante Electronico: Ajuste para seleccionar automaticamente la factura y serie por defecto<br>
2021-06-04 : feature | #307 | Usuarios: Editar/crear usuario: Ajuste grafico por tabs.<br>
2021-06-04 : feature | #651 | cliente en plantilla de compra customer_contact y reporte de compras totales<br>
2021-06-03 : feature | #307 | Comprobante Electronico: Ajuste para seleccionar automaticamente la factura y serie por defecto<br>
2021-06-03 : feature | #307 | Usuarios: Editar usuario: Seleccionar un tipo de documento y serie por defecto para el usuario<br>
2021-06-03 : feature | #584 | fecha de vencimiento en reporte de documentos<br>
2021-06-03 : feature | issues | plantillas predefinidas para generar issues en gitlab<br>
2021-06-02 : feature  | #424 | Compile JS<br>
2021-06-02 : feature  | #424 | Reporte: Pedidos: Consolidado de items: Añadiendo Totales por productos con exporte pdf y excel.<br>
2021-06-02 : feature  | #424 | Menor: Ajuste de estilo<br>
2021-05-26 : feature | - | Añadiendo informacion sobre datos del archivo de configuracion PHP<br>
2021-05-26 : feature | - | Añadiendo informacion sobre datos del archivo de configuracion PHP<br>
2021-05-26 : feature | - | Añadiendo informacion sobre datos del archivo de configuracion PHP<br>
2021-05-21 : feature | #518 | visualizacion de campos de pago en pago de notas de ventas<br>
2021-05-18 : feature | #592 | plantilla para notas de venta en formato ticket_58<br>


## 4.0.9

### docs
2021-05-05 : docs | changelog<br>


### fixed
2021-05-17 : fixed | #521 | Reporte: Ventas: Ajuste de tiempo maximo para pdf a 1800 segundos (30 minutos)<br>
2021-05-14 : fixed | #517 | Compile JS<br>
2021-05-14 : fixed | #517 | Nota de Ventas: Añadiendo fecha de pago al metodo de pago<br>
2021-05-14 : fixed | #389 | permisos y restricciones de accesos mediante urls<br>
2021-05-13 : fixed | #519 | missing use configuration<br>
2021-05-13 : fixed | #519 #577 | Fix Response<br>
2021-05-13 : fixed | #519 | Compile js<br>
2021-05-13 : fixed | #519 | Finanzas: Balance: Oculta el saldo total basado en la configuracion seller_can_view_balance<br>
2021-05-13 : fixed | #519 | Configuracion: Avanzada: Visual: Permite mostrar opcion de vendedor para ver balance en finanzas<br>
2021-05-13 : fixed | #578 | Contabilidad: Exportar reporte: Reporte de venta: Se amplia nota de debito y credito cuando afecta a un documento, para evaluar su status y, de estar anulada, se ajusta a 0 el total de la misma<br>
2021-05-12 : fixed | #516 #577 | Compile JS<br>
2021-05-12 : fixed | #577 | Item: DocumentFormItem evalua si es vendedor y si puede crear producto.<br>
2021-05-12 : fixed | #577 | Configuracion: Visual: Se añade posibilidad de vendedor para crear producto<br>
2021-05-12 : fixed | #389 | accesos de usuarios a vistas sin permisos<br>
2021-05-12 : fixed | #516 | Admin: Lista de usuarios: Ajuste grafico para orden de comprobantes<br>
2021-05-12 : fixed | #578 | Contabilidad: Exportar Reporte: Ajuste para colocar en 0 los totales cuando sea factura o bolenta, con status Rechazado o Anulado<br>
2021-05-12 : fixed | #578 | Contabilidad: Exportar Reporte: Ajuste para mostrar las notas correctamente.<br>
2021-05-12 : fixed | 582 | errores en filename y selector de series al editar comprobante<br>
2021-05-11 : fixed | #482 | Cash: Reportes de caja: Generacion de datos en el controlador. Ajuste para añadir credito y su calculo en los reportes de caja<br>
2021-05-11 : fixed | #582 | actualizando correlativo al modificar tipo de comprobante tomando el proximo numero a registrar en la serie seleccionada<br>
2021-05-11 : fixed | #576 | Compras: Anular Compra: Ajuste para verificar que exista lots_enabled.<br>
2021-05-10 : fixed | #407 #554 | Compile js<br>
2021-05-10 : fixed | #496 | Administración: Usuarios: Restaurando configuración de correo por cliente<br>
2021-05-10 : fixed | #482 #554 | Revirtiendo Cambios 3a2d670b838dabcddbcd3698d29c1d9bc13e93d0 37a23f9f91feaa47ae8bb695706c531e842d6de5 496e0cb02dd740c0d0cca36b285b5f5b69dfaa8d 8cfda276e32db47e7834ebfcd2e260dcccc09bb5 664d0a047d62c286396ead521c9dd71616b4e828<br>
2021-05-10 : fixed | #550 | altura cuerpo de plantilla<br>
2021-05-10 : fixed | #407 | Compras: Añadir Producto: Codigo de barras: Al encontrar el item, se elimina la busqueda por barras y se ajusta normal.<br>
2021-05-10 : fixed | #563 | Marcas y Categorías: Crear/Editar: Se añade validacion para marca o categoría única<br>
2021-05-07 : fixed | #433 | Reporte Caja: Muestra todos los ingresos a caja. No suma Credito al total de caja<br>
2021-05-07 : fixed | #433 #468 #554 | Compile js<br>
2021-05-07 : fixed | #554 | Documentos por Regularizar: Archivo de migracion<br>
2021-05-07 : fixed | #554 | Documentos por Regularizar: Se añade posiblidad de eliminar documentos con regularize_shipping y response_regularize_shipping, que tambien, tengan un documento con serie y con numero igual. Softdelete a tabla documents<br>
2021-05-07 : fixed | #433 | Reporte: Venta: Documento: Se añade opciones de descarga en columna<br>
2021-05-06 : fixed | #468 | Nota de venta: Se añade fecha de vencimiento a las nota de venta. Se lista Fecha de vencimiento a las nota de ventas<br>
2021-05-06 : fixed | #511 #512 | Finanzas: Cuentas por Cobrar/ Cuentas por pagar: Añadiendo la opcion a todos en proveedores y clientes<br>


### feature
2021-05-17 : feature | #592 | ticket formato 58mm para notas de venta<br>
2021-05-13 : feature | #589 | mejoras visuales de tablas y datos del dashboard<br>
2021-05-07 : feature | #550 | plantilla con bordes redondeados -rounded-<br>
2021-05-06 : feature | - | Tarea Programada: Añadiendo bat para tareas programadas en windows<br>


## 4.0.8

### docs
2021-04-21 : docs | changelog<br>


### fixed
2021-05-05 : fixed | #565 | mostrando logs al ejecutar una tarea programada<br>
2021-05-05 : fixed | #513 | Compile js<br>
2021-05-05 : fixed | #565 | mostrando logs al ejecutar una tarea programada<br>
2021-05-05 : fixed | #482 | CashControllerRevision: Separando Tipo de documenos de Metodos de pago<br>
2021-05-04 : fixed | #496 #407 | Compile JS<br>
2021-05-04 : fixed | #496 | Administración: Usuarios: Notificacion para correo como informacion adiciona en el manual. Cambia la configuracion si todos los campos estan llenos. Contraseña queda excluida de la actualizacion<br>
2021-05-04 : fixed | #544 | Cotizacion: Nueva cotizacion: Añadido Marca al PDF default A5.<br>
2021-05-04 : fixed | #544 | Cotizacion: Nueva cotizacion: Añadido Marca al PDF default.<br>
2021-05-04 : fixed | #566 | plantilla model3<br>
2021-05-04 : fixed | #540 | llamados en mobilecontroller<br>
2021-05-04 : fixed | #407 | Compras: Añadir Producto: Codigo de barras: Se añade la funcionabilidad de buscar por codigo de barras.<br>
2021-05-03 : fixed | #545 | valor booleano desde resources<br>
2021-05-03 : fixed | #496 | Administración: Usuarios: Añade configuracion de correo por clientes.<br>
2021-05-01 : fixed | #141 | se reparo el error al convertir dolares a soles y viceversa, al editar comprobantes<br>
2021-04-30 : fixed | #503 | Pos: Busqueda: Se ajusta para que la paginacion funcione correctamente por input_item<br>
2021-04-30 : fixed | #555 | consulta de productos mediante busqueda en guias de remision<br>
2021-04-30 : fixed | #503 | Pos: Busqueda: Se ajusta para que la paginacion funcione correctamente por input_item<br>
2021-04-30 : fixed | #539 | Admin: Usuarios: Modulos: Permite que el los nodos hijos se seleccionen cuando el padre es seleccionado, y quiten la seleccion cuando el caso contrario suceda<br>
2021-04-30 : fixed | #498 | Reporte: Venta: GENERAL DE PRODUCTOS: Muestra Ventas Anuladas en los registros cuando no deberia.<br>
2021-04-30 : fixed | #555 | vista guia tarda mucho en realizar consultas, se elimina repeticion de queries de localidades<br>
2021-04-29 : fixed | #539 | Compile JS<br>
2021-04-29 : fixed | cambio de plantilla solo cuando se tenga un establecimiento seleccionado<br>
2021-04-29 : fixed | #539 | Usuarios: Permisos: Ajuste para seleccionar un elemento y sus hijos. Ajusta el mismo status de check a los hijos<br>
2021-04-29 : fixed | #556 | Pos: Añade item al buscar por Codigo de barras<br>
2021-04-29 : fixed | #482 | CashControllerRevision: Nomenclatura correcta.<br>
2021-04-29 : fixed | #513 | Dashboard: Grafico Nota de venta - Comprobante: Total Pagado por Total Cobrado / Total por Pagar por Pendiente de cobro<br>
2021-04-28 : fixed | #482 | Removiendo PaymentMethodType del template<br>
2021-04-28 : fixed | #482 | compile js<br>
2021-04-28 : fixed | #425 | Compile JS<br>
2021-04-28 : fixed | #503 | Compile JS<br>
2021-04-28 : fixed | #503 | Pos: Filtrado de items: Ajuste para evaluar si description es null, sino se toma name o internal_id. Se mantiene el filtrado input_item<br>
2021-04-27 : fixed | #482 | Pos: Caja chica Pos: Reporte PDF: Se remueven metodos de pago a credito de facturacion, para que en el reporte puedan salir correctamente listados y de el monto correcto en el ingreso a caja<br>
2021-04-26 : fixed | #329 | Compile JS<br>
2021-04-26 : fixed | #541 | Nota de venta: Creacion con producto con lotes: Validar que exista presentation -> quantity_unit en el array<br>
2021-04-26 : fixed | #466 | Contabilidad: Exportar Reporte: Ajuste para validar que documento exista cuando document_type_id es 07 o 08<br>
2021-04-26 : fixed | #425 | REPORTES: CONSISTENCIA DE DOCUMENTOS POR RANGO DE FECHAS: Al front, se limita a 30 dias la seleccion final de consulta.<br>
2021-04-26 : fixed | #425 | REPORTES: CONSISTENCIA DE DOCUMENTOS POR RANGO DE FECHAS: Se elimina la validacion getIsClient, se inicia con 1 mes pero el cliente puede consultar todo el rango de fechas( documents created_at min hasta documents created_at max)<br>
2021-04-26 : fixed | #530 | Facturas: (invoice) Ocultar cuentas inhabilitadas y que tengan "Mostrar cuenta en los reportes de comprobantes" desactivado para el tema Top Ruc<br>
2021-04-26 : fixed | #329 | Pedidos: Ajuste para establecer seller_id desde Pedidos al generar el comprobante<br>
2021-04-23 : fixed | #329 | Compile js<br>
2021-04-23 : fixed | #529 | Compra: Nueva compra desde Orden de Compra: update_price si es verdadero accede a la actualizaion de precios.<br>
2021-04-23 : fixed | #529 | Compra: Nueva compra desde Orden de Compra: Error Undefined index: update_price al crear Nueva Compra<br>
2021-04-22 : fixed | #329 | Nota de ventas: Ajuste para establecer seller_id desde nota de ventas al generar el comprobante<br>
2021-04-22 : fixed | #451 #479 | Compile JS<br>
2021-04-22 : fixed | #451 | Item: Marcas: Ajuste para la url de brands. No guarda desde el modulo de compra<br>
2021-04-22 : fixed | #451 | Item: Categorias: Ajuste para la url de categorias. No guarda desde el modulo de compra<br>
2021-04-22 : fixed | #479 | Nota de ventas: Generar Comprobante: Ajuste para que v-modal no se sobreponga en el modal de impresion<br>
2021-04-22 : fixed | #432 | Compile JS<br>
2021-04-22 : fixed | #399<br>
2021-04-21 : fixed | #408 | Actualizar precio de compra en formulario de PRODUCTO en base al precio de COMPRA del formulario de COMPRAs<br>
2021-04-21 : fixed | i-399 | PROBLEMA NO COINCIDE STOCK DE CATALOGO/PRODUCTOS CON REPORTE KARDEX<br>
2021-04-21 : fixed | #515 | titulos y listas en modo oscudo<br>
2021-04-21 : fixed | #432 | Se revierte la configuracion para stock 0 y solo items por almacen de usuario. En vez de eso, se mostrará el almacen del item<br>
2021-04-21 : fixed | #481 | error lots_enable (no existe key en json)<br>
2021-04-21 : fixed | #497 | Log Diario de laravel en .env.example<br>
2021-04-21 : fixed | #497 | Compile JS<br>
2021-04-21 : fixed | #497 | Ajuste para validar user_id como creador de sales note<br>
2021-04-20 : fixed | #497 | Reporte general de productos: El modelo sale_notes no permite registrar el vendor. Se ajusta para que nota de venta no muestre vendedor.<br>


### feature
2021-05-04 : feature | #434 | organizando anulaciones por orden de fecha de emision<br>
2021-05-03 : feature | #540 | mobileContrlller y ruta para subir imagenes desde apk personalizadas por terceros<br>
2021-04-29 : feature | #545 | creado ticket 58mm para pedidos<br>
2021-04-29 : feature | habilitando ticket de 58mm desde configuracion avanzada<br>
2021-04-28 : feature | #545 | rediseño de vista configuracion avanzada<br>
2021-04-27 : feature | #450 | compile js<br>
2021-04-27 : feature | #450 | modulo de compras funcionando con formatos por establecimientos<br>
2021-04-27 : feature | #450 | modulo de ventas funcionando con formatos por establecimientos<br>
2021-04-27 : feature | #450 | plantilla de establecimiento funcionando en cotizaciones (recorriendo todos los documentos para que tomen esta configuracion)<br>
2021-04-27 : feature | #450 | plantilla de establecimiento funcionando en oportunidades de venta (recorriendo todos los documentos para que tomen esta configuracion)<br>
2021-04-27 : feature | #450 | plantilla de establecimiento funcionando en notas de venta (recorriendo todos los documentos para que tomen esta configuracion)<br>
2021-04-23 : feature | #450 | guardando plantilla por establecimiento, funcionando solo en documents (guias notas y demas en proceso)<br>
2021-04-22 : feature | #450 | campo en establecimiento para formato pdf, consulta en vista del formato actual por establecimiento (en proceso)<br>


## 4.0.7

### docs
2021-04-12 : docs | changelog<br>


### fixed
2021-04-21 : fixed | complie js, padding top nota de credito/debito<br>
2021-04-21 : fixed | condicion para editar precio en nota de credito<br>
2021-04-20 : fixed | #480 | Compile JS<br>
2021-04-20 : fixed | #480 | Lista de pedidos en tienda virtual: Se determina que description es el nombre del producto (desde creacion del item) por lo tanto, se cambia la etiqueta de la propiedad<br>
2021-04-19 : fixed | #432 | Filtro de productos, seleccion de solo item.<br>
2021-04-19 : fixed | #501 | Reporte de Ventas: Ajuste para alinear totales en descarga (pdf y excel)<br>
2021-04-18 : fixed | #436 | Editar precio en perfil vendedor - pos<br>
2021-04-18 : fixed | #367 | Pago de servicio tecnico en caja<br>
2021-04-16 : fixed | #425 | REPORTES: CONSISTENCIA DE DOCUMENTOS POR RANGO DE FECHAS: Se ajsuta la validacion para seleccionar por rango de fechas.<br>
2021-04-15 : fixed | #309 | TIENDA VIRTUAL: Al seleccionar la categoria, se mostrará resaltado y enviará correctamente a la categoria<br>
2021-04-15 : fixed | #480 | Tienda virtual: Pedidos: Añadiendo descripcion a la tabla de pedidos (lupa)<br>
2021-04-15 : fixed | #495 | NV desde POS: Se ajusta id en false, type_period y quantity_period se valida que existan para que no falle la creacion<br>
2021-04-15 : fixed | #449 | Api: Guia de Remision: Ejemplo para crear items al momento de enviar la guia: removiendo codigo_tipo_item<br>
2021-04-15 : fixed | #449 | Api: Guia de Remision: Ejemplo para crear items al momento de enviar la guia.<br>
2021-04-15 : fixed | #486 | POS: Nota de venta: Ajuste para mensaje por whatsapp<br>
2021-04-14 : fixed | #463 | visualizacion categorias en pos<br>
2021-04-14 : fixed | #491 | menu header comprobantes no enviados<br>
2021-04-14 : fixed | #485 | Compile JS<br>
2021-04-14 : fixed | #485 | Ingreso de Producto en inventario: Convirtiendo cantidad en numero para comparacion<br>
2021-04-14 : fixed | #485 | Ingreso de Producto en inventario: Convirtiendo cantidad en numero para comparacion<br>
2021-04-13 : fixed | #433 | Compile js<br>
2021-04-12 : fixed | #462 | datos visuales en letras blanco con modo oscuro<br>
2021-04-08 : fixed | #433 | Filtro para buscar por numero de guia en Reportes -> Ventas -> Documentos<br>
2021-04-08 : fixed | #433 | Filtro para buscar por numero de guia en Ventas -> Listado de Comprobantes<br>
2021-04-07 : fixed | #449 | Api: Guia de Remision: Ejemplo para crear items al momento de enviar la guia<br>

### feature
2021-04-14 : feature | i-367 | EMITIR FACTURA DESDE UN SERVICIO DE SOPORTE TÉCNICO<br>

## 4.0.6

### docs
2021-03-24 : docs | changelog<br>


### fixed
2021-04-12 : fixed | #477 | Editar Producto: Fecha de vencimiento en formato Y-m-d H:i:s<br>
2021-04-12 : fixed | #477 | Editar Item: Fecha de vencimiento en formato Y-m-d H:i:s<br>
2021-04-12 : fixed | #477 | Editar Item: Ajuste para guardar la fecha de vencimiento como fecha y no como array<br>
2021-04-12 : fixed | #476 | Ajuste de validacion de configuracion ( #432 ) en el modelo y aplicado a la busqueda<br>
2021-04-09 : fixed | #390 | Packs: Edicion/Nuevo pack, muestra el total y se puede modificar la cantidad de los items que lo componen<br>
2021-04-09 : fixed | #432 | mejoras en la lista de productos, evita duplicados por stock si no se tiene la configuracion habilitada<br>
2021-04-08 : fixed | #475 | GUIA DE REMISION - Cambio de nomenclatura por migracion<br>
2021-04-07 : fixed | #441 | Nota de venta: Ajuste para añadir formas de pago Credito y Contado. Similar a Cotizacion<br>
2021-04-07 : fixed | #441 | Cotizacion: Ajuste de diseño en los campos de fecha y total.<br>
2021-04-07 : fixed | #441 | Cotizacion: Modal al 50%, Se muestra la moneda (currency_type_id) y el total<br>
2021-04-07 : fixed | #441 | Cotizacion: Al momento de generar el comprobante, se selecciona credito o contado. Default Contado.<br>
2021-04-07 : fixed | #432 | Ajuste de texto en el sidebar<br>
2021-04-07 : fixed | #432 | Ajuste de estilo de codigo.<br>
2021-04-06 : fixed | #435 | Ajuste para guardar la fecha en formato Y-m-d y no un array<br>
2021-04-06 : fixed | #393 | error de texto azanvado x avanzado<br>
2021-04-05 : fixed | #414 #301 #277 | Añadido filtro por marcas y categorias como opcional. Ajuste para ordenar por nombre de item<br>
2021-04-03 : fixed | i-418 | Detalle del Item al Generar FACTURA de una Cotización no se Copia<br>
2021-04-03 : fixed | carga de items en movimiento de inventario<br>
2021-04-03 : fixed | carga de items en movimiento de inventario<br>
2021-04-03 : fixed | carga de items en movimiento de inventario<br>
2021-04-03 : fixed | carga de items en movimiento de inventario<br>
2021-04-03 : fixed | i-395 | error en la busqueda al momento de ingresar en el modulo de movimiento<br>
2021-04-03 : fixed | reporte de inventario<br>
2021-04-03 : fixed | reporte de inventario<br>
2021-04-03 : fixed | reporte de inventario<br>
2021-04-03 : fixed | reporte de inventario<br>
2021-04-03 : fixed | reporte de inventario<br>
2021-04-03 : fixed | reporte de inventario<br>
2021-03-31 : fixed | i-1 | Removiendo "ELECTRÓNICA" de "GUIA DE REMISIÓN REMITENTE ELECTRÓNICA"<br>
2021-03-31 : fixed | #448 | stock 0 para servicios<br>
2021-03-31 : fixed | #458 | boton permite duplicados<br>
2021-03-30 : fixed | campos giro grifo activo form de venta<br>
2021-03-30 : fixed | campos de informacion adicional en formulario de venta<br>
2021-03-30 : fixed | observaciones en formulario de venta<br>
2021-03-30 : fixed | t-373 | Corregir edición items en cotizaciones luego de ser generadas, así como en ventas<br>
2021-03-29 : fixed | direcciones de cliente en form de venta<br>
2021-03-29 : fixed | direcciones de cliente en form de venta<br>
2021-03-29 : fixed | direcciones de cliente en formulario de venta<br>
2021-03-26 : fixed | #387 | solución al error en reporte de caja, montos a credito no aparecian<br>
2021-03-25 : fixed | js<br>
2021-03-25 : fixed | compilado<br>
2021-03-25 : fixed | #420 | cierre inesperado del popup de pagos en notas de venta, solucionado<br>
2021-03-25 : fixed | #327 | bug de permisos, usuarios secundarios pueden entrar a modulos no asignados, reparado}<br>
2021-03-25 : fixed | #233 | se eliminó la duplicidad del nombre del vendedor en los pdfs de cotizaciones<br>
2021-03-24 : fixed | #327 | se ocultaron los modules y componentes no otorgados a la empresa al momento de editar un usuario secundario<br>
2021-03-24 : fixed | #306 | se corrigieron los detalles del buscador de clientes en el popup de generar cpe desde multiples guías y se agrego el número del comprobante en la plantilla blank<br>


### feature
2021-04-11 : feature | #233 | se agrego el cuadro para cambiar la fecha de pago cuando se genera un comprobante a partir de una cotización<br>
2021-04-08 : feature | cambio de url para consulta interna la sunat<br>
2021-04-07 : feature | #439 | se agregó el modod cargando en las opciones de los cpe<br>
2021-04-06 : feature | detalles dashboard<br>
2021-04-06 : feature | #443 | Ajuste para nota de venta, Copia de default y ajuste para orden visual de las cuentas<br>
2021-04-06 : feature | #443 | Plantilla PDF basada en default3_new para añadir las cuentas bancarias al final de la factura a4<br>
2021-04-05 : feature | #390 | poder editar la cantidad del producto luego de creado<br>
2021-04-05 : feature | i-436 | Agregó configuración: Permitir editar precio unitario a vendedores<br>
2021-04-05 : feature | i-367 | REQUERIMIENTO PARA PODER EMITIR FACTURA DESDE UN SERVICIO DE SOPORTE TÉCNICO<br>
2021-04-05 : feature | #313 | se agregó la columna cantidad a los cpe generados desde nota de venta<br>
2021-04-05 : feature | #401 | mostrar el precio de venta de producto al editar el precio en las compras<br>
2021-04-02 : feature | i-358 | REPORTE DE PRODUCTOS SIN STOCK Y CON STOCK MÍNIMO<br>
2021-04-01 : feature | #435 | Añadiendo modelo de factura con fecha de vencimiento por producto. Se quita Serie para fecha de vencimiento<br>
2021-04-01 : feature | #285 | módulos y componentes controlados desde el superadmin<br>
2021-03-31 : feature | #313 | generar un cpe desde múltiples notas de ventas. listo<br>
2021-03-31 : feature | #401 | opción de editar el precio de venta de un producto desde las compras fue aadido<br>
2021-03-31 : feature | #423 | template pedido sin mostrar igv<br>
2021-03-29 : feature | #391 | se agregaron los campos tipos de usuario y vendedor y categoría de los productos en el reporte general del productos<br>
2021-03-29 : feature | rediseño posiciones en formulario de venta<br>
2021-03-25 : feature | diseño formulario venta<br>
2021-03-25 : feature | condicion de pago<br>
2021-03-25 : feature | arreglos de diseño sidebar, colores e iconos<br>
2021-03-25 : feature | json retorna url de imagen a apk<br>


## 4.0.5

### docs
2021-03-24 : cambios para ver el google docs viewer<br>
2021-03-10 : docs | changelog<br>


### fixed
2021-03-24 : fixed | https://gitlab.com/carlomagno83/facturadorpro4/-/issues/431 | no listan tareas programadas<br>
2021-03-24 : fixed | margen top en panel admin<br>
2021-03-23 : fixed | i-306 | se ha asociado el documento generado desde multiples guías a sus repectivas guías<br>
2021-03-22 : fixed | t-416 | Requerimiento para Cotizaciones - Referencia<br>
2021-03-19 : fixed | i-327 | error al seleccionar un modulo con hijos arreglado<br>
2021-03-18 : fixed | i-306 | se corrigió el error al crear cpe desde las guías<br>
2021-03-17 : fixed | i-141 | se oculto el boton editar de los cpe<br>
2021-03-17 : fixed | POS | problema con nombres de 50 caracteres<br>
2021-03-17 : fixed | i-141 | error en el campo leyenda al editar un cpe, solucionado<br>
2021-03-16 : fixed | mostrando orden de compra en plantilla full_height<br>
2021-03-15 : fixed | i-392 | se limito la busqueda de productos a los vendedores, ahora solo ven los items de su almacen<br>
2021-03-15 : fixed | i-282 | se modificó la formula para obtener las ganancias<br>
2021-03-12 : fixed | subida de logo en formato jpg<br>
2021-03-10 : fixed | errores varios<br>


### feature
2021-03-24 : feature | i-405 | problema al mostrar el pdf en moviles, arreglado<br>
2021-03-22 : feature | t-338 | Permitir generar comprobante de pago desde cotización a vendedores<br>
2021-03-21 : feature | sidebar espacios visible para submenus<br>
2021-03-21 : feature | header mas uniforme en diseño, cambio de switch, tooltip para todos<br>
2021-03-20 : feature | i-249 | backups independientes por empresas<br>
2021-03-20 : feature | i-327 | se cambió el orden para mostrar de los modulos(en el superadmin y los tenant)<br>
2021-03-19 : feature | i-327 | se agrego una la opción para activar o desactivar las opciones del menu ventas<br>
2021-03-18 : feature | i-261 | se puede cargar plantillas personalizada[*] sin afectacion a las actualizaciones<br>
2021-03-18 : feature | i-380 | telefono y vendedor en nota de venta y guia de remision, plantilla default<br>
2021-03-17 : feature | i-346 | se agrego una ruta más para editar un producto a partir de su id<br>
2021-03-16 : feature | i-306 | se agregó la opción para seleccionar la dirección de llegada dependiendo del cliente seleccionado en las guías de remisión<br>
2021-03-16 : feature | i-365 | reduccion de espacios superiores en formulario de creacion<br>
2021-03-15 : feature | i-369 | el usuario vendedor ya no puede editar el precio de venta de un producto al emitir un cpe<br>
2021-03-15 : feature | i-398 | telefono de cliente en plantilla brand<br>
2021-03-12 : feature | i-306 | se agrego la opción para generar un cpe desde multiples guías de remisión<br>
2021-03-12 : feature | i-306 | se agrego la opción para generar un cpe desde multiples guías de remisi+on<br>
2021-03-11 : feature | i-374 | se eliminó la marca de la descripción del producto cuando se usa la vista lista<br>
2021-03-11 : feature | i-129 | se agrego la plantilla multilples_logos que habilita la opción de imprimir el logo del establecimiento en los cpe<br>


## 4.0.4

### docs
2021-03-04 : docs | Update README.md<br>
2021-03-03 : docs | changelog<br>


### fixed
2021-03-19 : fixed | i-332 | optimización visual del pos
2021-03-10 : fixed | solución al error  logo not found<br>
2021-03-09 : fixed | i-361 | template header_image_full_width<br>
2021-03-08 : fixed | i-220-2 | se eliminó el icono de la frase genear guía<br>
2021-03-03 : fixed | i-363-2 | Se modificó la forma de importar productos, ahora el almacén se selecciona en el popup de importación<br>


### feature
2021-03-22 : feature | i-416 | Requerimiento para Cotizaciones - Referencia
2021-03-10 : feature | template center_note (solo cambia ticket en nota de venta)<br>
2021-03-09 : feature | i-374 | se agrego la marca del producto a la descripción en la sección POS<br>
2021-03-09 : feature | i-381 | se agregó una opción para subir un logo global, tambien una opción para ocultar el logo global, se ocultó la opción de configurar el login en los clientes<br>
2021-03-08 : feature | i-381 | se agregó una opción para configurar el login de los clientes desde el superadmin<br>
2021-03-05 : feature | feature-dashboard-tenants | Se hicieron algunas modificaciones al dashboard y se resolvió el problema de los totales en el dashboard<br>
2021-03-04 : feature | i-220 | se agregó el botón Generar Guía en la sección Reportes/Ventas/Consolidado de items<br>
2021-03-03 : feature | i-368 | se modificó la plantilla datatime, se agregó la hora de creación del pedido en los tickets<br>
2021-03-03 : feature | i-334 | se agregó la opción para agregar un lote a un producto desde las guías de remisión<br>


## 4.0.3

### fixed
2021-03-03 : fixed | i-361 | ocultando descuento en plantilla header_image_full_width<br>
2021-03-02 : fixed | compilando archivo estaticos<br>
2021-03-01 : fixed | a5 brand sale_note<br>
2021-02-24 : fixed | i-345 | La busqueda por rango de fechas se modifico, se dejó de usar la columna created_at por date_of_issue<br>
2021-02-24 : fixed | fixed-colores-sidebar-superadmin | Se modifico el color del menu del superadmin<br>
2021-02-24 : fixed | i-348 | template brand - error tickets y a5 en notas de venta<br>


### feature
2021-03-02 : feature | i-363 | Se agregó la columna almacén a la importación de productos.<br>
2021-03-01 : feature | i-386 | Se editaron las plantillas default, default, default2, default3, default4, font_sm, legend_amazonia y se le agregaron la hora a los reportes de tamaño ticket<br>
2021-03-01 : feature | i-343 | dashboard tenant sin espacio en disco<br>
2021-03-01 : feature | i-361 | cod por dto en plantilla image_full<br>
2021-02-26 : feature | feature-tramite-documentario | Se agrego la vista de crear expedientes<br>
2021-02-25 : feature | feature-sercofi-2 | se separó los servicios de los productos, ahora los servicios tienen su item en el sidebar, además se preselecciona la unidad de medida zz desde el boton crear<br>
2021-02-25 : feature | header Mi empresa<br>
2021-02-24 : feature | i-350 | se agrego el boton editar en las cotizaciones<br>


## 4.0.2

### fixed
2021-02-24 : fixed | i-348 | template brand - error tickets y a5<br>
2021-02-17 : fixed | header de tenant en diferentes opciones<br>


### feature
2021-02-23 : feature | feature-topbar | se agregó el botón de pedidos al topbar, se arreglo el modo responsivo en el topbar y se corrigió la combinación de colores en el sidabr<br>
2021-02-23 : feature | feature-topbar | Se egregó el boton modo oscuro en el header, se cambiaron los colores de los botones del topbar, se cambió los estilos del sidebar en sus diferentes variantes<br>
2021-02-22 : feature | feature-topbar | Cambios visuales en el sidebar, cambios finales, esperando feedback<br>
2021-02-22 : feature | feature-topbar | Cambios visuales en el sidebar, limpiando el fondo blanco del div nano<br>
2021-02-22 : feature | feature-topbar | Cambios visuales en el sidebar, limpiando el fondo blanco<br>
2021-02-22 : feature | feature-topbar | Cambios visuales en el sidebar<br>
2021-02-22 : feature | feature-topbar | Se agrego el sidebar de colores<br>
2021-02-19 : feature | feature-sercofi | Se separaron los filtros en cuentas por pagar y en cuentas por cobrar se agregó el boton mostrar penalidad por mora<br>
2021-02-19 : feature | sidebar estilo paneles oscuros<br>
2021-02-19 : feature | switcher-top color<br>


## 4.0.1

### docs
chagelod

## 4.0.0

### feature
Despliegue de PRO4
