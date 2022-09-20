<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Envio de Comprobante de Pago Electrónico</title>
    <style>
        body {
            color: #000;
        }

        ul {
            list-style: none;
        }
    </style>
</head>
<body>
<p>Hola
    @if($document->customer)
        {{ $document->customer->name }}
    @else
        {{ $document->supplier->name }}
    @endif
    ,</p>
<p>Te enviamos tu comprobante de pago código: {{ $document->series.'-'.$document->number }}</p>
<p>con número de folio: {{ $document->folio }}</p>
<p>Ante cualquier duda o inconveniente con tu comprobante, comunícate a: contabilidad@ccgroupperu.com</p>
<p>Gracias por tu compra.</p>
<p>CC Group Perú.</p>
</body>
</html>
