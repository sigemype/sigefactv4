<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $filename }}</title>
    <link href="{{ asset('pdf_fonts/fonts.css') }}" rel="stylesheet">
    <style>
        .t-color-line-right{border-right: 1px solid #000;}
        .t-color-line-top{border-top: 1px solid #000;}
        .t-color-line-bottom{border-bottom: 2px solid #000;}
        .t-bg-head{background-color: #fff;}
        .t-color-line-body{border-bottom: 1px solid #000;}
        .t-color-head-text{color: #000;}
        .t-color-line-table{border-bottom: 1px solid #ccc;}

        .t-line-1-left{border-left: 1px solid #000;}
        .t-line-1-right{border-right: 1px solid #000;}
        .t-line-1-top{border-top: 1px solid #000;}
        .t-line-1-bottom{border-bottom: 1px solid #000;}
        .t-line-1-all{border: 1px solid #000;}

        .t-line-2-left{border-left: 2px solid #000;}
        .t-line-2-right{border-right: 2px solid #000;}
        .t-line-2-top{border-top: 2px solid #000;}
        .t-line-2-bottom{border-bottom: 2px solid #000;}
        .t-line-2-all{border: 1px solid #000;}
    </style>
</head>
<body>
{!! $template_data !!}
</body>
</html>

