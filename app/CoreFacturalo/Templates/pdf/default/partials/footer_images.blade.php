@php
    $path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    
    $quantity_images = $images->count();
    $space = $quantity_images > 1 ? 4 : 0;
    $width = $quantity_images > 1 ? (100 / $quantity_images) - $space : 100;
    $height = 140 * $quantity_images;

@endphp
<head>
    <link href="{{ $path_style }}" rel="stylesheet" />
</head>
<body>
    <table class="full-width">
        <tr>
            @foreach ($images as $image)
                <td width="{{$width}}%">
                    <img style="width: 100%; height:{{$height}}px;"   src="{{ $image['url'] }}">
                </td>
                <td width="{{$space}}%"></td>
            @endforeach
        </tr>
    </table>
    <br>
    <br>
</body>