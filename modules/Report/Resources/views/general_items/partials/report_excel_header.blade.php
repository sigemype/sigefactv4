<?php
$document_type_id = isset($document_type_id) ? $document_type_id : null;
$type = $type ?? 'sale';
?>
<th>FECHA DE EMISIÓN</th>
@if($document_type_id != '80' && $type == 'sale')
    <th>DIST</th>
    <th>DPTO</th>
    <th>PROV</th>
@endif
<th> TIPO DOCUMENTO</th>
<th> ID TIPO</th>
<th> SERIE</th>
<th> NÚMERO</th>
@if( $type == 'sale')
    <th> ORDEN DE COMPRA</th>
    <th> PLATAFORMA</th>
@endif
<th> ANULADO</th>
<th> DOC ENTIDAD TIPO DNI RUC</th>
<th> DOC ENTIDAD NÚMERO</th>
<th> DENOMINACIÓN ENTIDAD</th>
@if($type == 'sale')
    <th> VENDEDOR</th>
@endif
<th> MONEDA</th>
<th> TIPO DE CAMBIO</th>
<th> UNIDAD DE MEDIDA</th>
<th> CÓDIGO INTERNO</th>
<th> DESCRIPCIÓN</th>
<th> CANTIDAD</th>
<th> SERIES</th>
@if($type == 'sale')
    <th> MODELO</th>
@endif
<th> COSTO UNIDAD</th>
<th> VALOR UNITARIO</th>
<th> PRECIO UNITARIO</th>
<th> DESCUENTO</th>
<th> SUBTOTAL</th>
<th> TIPO DE IGV</th>
<th> IGV</th>
<th> TIPO DE ISC</th>
<th> ISC</th>
<th> IMPUESTO BOLSAS</th>
<th> TOTAL</th>
@if($type == 'sale')
    <th> TOTAL COMPRA</th>
    <th> GANANCIA</th>
    <th> MARCA</th>
    <th> CATEGORÍA</th>
@endif
