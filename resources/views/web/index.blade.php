<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    {{-- <link rel="icon" href="../../../../favicon.ico"> --}}

    <title>Sigefact | Sistema de facturación electrónica</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ asset('web/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/Style.css') }}">
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <link rel="stylesheet" href="{{ asset('web/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/owl.theme.default.css') }}">
    <!-- Código de instalación Cliengo para facturacion@sigefact.pe --> 
    <script type="text/javascript">(function () { var ldk = document.createElement('script'); ldk.type = 'text/javascript'; ldk.async = true; ldk.src = 'https://s.cliengo.com/weboptimizer/6192b723d50abe002a7010de/6192b725d50abe002a7010e1.js?platform=registration'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ldk, s); })();</script>
</head>

<body>
	<div class="modal fade" id="fm-modal" tabindex="-1" role="dialog" aria-labelledby="fm-modal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="">Comprobantes de contigencia</h5>
					<button class="close" data-dismiss="modal" aria-label="Cerrar">
					    <span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<h2>Desde el 1er de Septiembre</h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				</div>
			</div>
		</div>
	</div>    

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a class="navbar-brand"><img src="{{ asset('web/imagenes/logo.png')}}" alt="logo"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="https://demo.sigefact.com/">Ingresar a Demo<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#registrar">Registrarse</a>
                </li>
                <!--<li class="nav-item">-->
                <!--  <a class="nav-link" href="https://sigefact.pse.pe/buscar">Buscar Comprobante</a>-->
                <!--</li>-->
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <img src="{{ asset('web/imagenes/sige-mype.png')}}" alt="">
            </form>
        </div>
    </nav>

<!--Slider -->
	<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="{{ asset('web/imagenes/1.jpg')}}" alt="First slide">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="{{ asset('web/imagenes/slider1.png')}}" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="{{ asset('web/imagenes/2.jpg')}}" alt="Third slide">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Anterios</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Siguente</span>
        </a>
	</div>
    <div class="segunda_parte entero">
        <div class="medio">
            <div class="titulo">
                <h2>Multiples</h2>
                <h1>&nbsp;soluciones de facturación electrónica&nbsp;</h1>
                <h2>para desarrolladores de software y empresas</h2>
            </div>
            <div class="serv_item_1">
                <img src="{{ asset('web/imagenes/logo.png')}}" alt=""> ONLINE CON INTERNET
                <img src="{{ asset('web/imagenes/ser1.PNG')}}" alt="">
                <h2 class="nombre">ONLINE</h2>
                <p>Puedes usar directamente nuestra aplicación versión ONLINE para emitir comprobantes electrónicos de forma inmediata desde nuestra plataforma WEB. Deberás darnos de ALTA con tu clave SOL desde la SUNAT. No necesitas contar con un certificado digital. Regístrate aquí gratis.</p>
                <a href="#registrar">Comprar ahora</a>
            </div>

            <div class="serv_item_1">
                <img src="{{ asset('web/imagenes/logo.png')}}" alt=""> OFFLINE SIN INTERNET
                <img src="{{ asset('web/imagenes/ser2.PNG')}}" alt="">
                <h2 class="nombre">OFFLINE</h2>
                <p>La versión OFFLINE es ideal para emitir documentos electrónicos de forma masiva. Esta solución informática de emisión de facturas electrónicas es indicada para empresas con alta y mediana volumetría. Instalaremos una APLICACIÓN en tu servidor o PC que permitirá emitir y validar facturas electrónicas. No necesitas un certificado digital. Compatilibilidad con cualquier ERP, Software y sistemas propios.</p>
                <a href="#registrar">Comprar ahora</a>
            </div>

            <div class="serv_item_1">
                <img src="{{ asset('web/imagenes/logo.png')}}" alt=""> INTEGRACION, API
                <img src="{{ asset('web/imagenes/ser3.PNG')}}" alt="">
                <h2 class="nombre">API-REST</h2>
                <p>Nuestra solución puede integrarse con cualquier software, sin importar el lenguaje de programación ni la base de datos que uses. Contamos con DOCUMENTACIÓN, MANUALES y EJEMPLOS DE CÓDIGO para mayor facilidad. Se puede integrar con archivos JSON o TXT. Compatilibilidad con cualquier ERP, software y sistemas propios. Puedes ver ejemplos aquí</p>
                <a href="#registrar">Comprar ahora</a>
            </div>
        </div>
    </div>
    
    <!--<div class="embed-responsive embed-responsive-16by9">
      <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/KttlP137o9s" frameborder="0" allowfullscreen></iframe>
    </div>-->
    <div class="video">
        <h1>La manera mas facil de emitir facturas electrónica</h1>
        <div  class="video embed-responsive-16by9" >
            <iframe src="https://www.youtube.com/embed/KttlP137o9s" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        </div>
    </div>
    
<div class="titulo_cliente entero">
    <div class="medio">
        <h2 class="clientes">Nuestros clientes</h2>
    </div>
</div>
    <div class="logos entero">
        <div class="medio owl-carousel" id="registrar">
            <img src="{{ asset('web/imagenes/1.png')}}" alt="">
            <img src="{{ asset('web/imagenes/2.png')}}" alt="">
            <img src="{{ asset('web/imagenes/3.png')}}" alt="">
            <img src="{{ asset('web/imagenes/4.png')}}" alt="">
            <img src="{{ asset('web/imagenes/5.png')}}" alt="">
            <img src="{{ asset('web/imagenes/6.png')}}" alt="">
            <img src="{{ asset('web/imagenes/7.png')}}" alt="">
            <img src="{{ asset('web/imagenes/8.png')}}" alt="">
            <img src="{{ asset('web/imagenes/9.png')}}" alt="">
            <img src="{{ asset('web/imagenes/10.png')}}" alt="">
        </div>
    </div>
    <h2 class="video">Completa el Formulario y contáctate con nosotros</h2>
    <div class="modal_entero entero ">
        <div class="medio">
            <form action="/send_mail" method="POST">
                @csrf
                <input type="text" placeholder="Nombre" name="nombre" required>
                <input type="text" placeholder="Empresa" name="empresa" required >
                <input type="number" placeholder="Telefono" name="telefono" required >
                <input type="number" placeholder="Ruc" name="ruc" required >
		        <input type="email" placeholder="Correo" name="correo" required >
                <textarea placeholder="Mensaje" name="mensaje" id="" cols="30" rows="10"></textarea>
                <button>Enviar</button>
            </form>
        </div>
    </div>
<div class="ultimo entero">
    <div class="medio">
        <img src="{{ asset('web/imagenes/logo.png') }}" alt="">
        <span>
            <h3>Datos de contacto </h3>
            <p class="tel:014800125">(01) 729 8128 | (+51) 970 542 769</p>
            <p>Jr. Iquitos 593, lima 31</p>
            <p href="mailto:gerencia@sigefact.pe">gerencia@sigefact.pe</p>
        </span>
    </div>
</div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

</body>
<footer class="entero">
    <div class="medio">
        <p> Todos los Derechos Reservados </p>
    </div>
</footer>
<script src="{{ asset('web/js/efectos.js') }}"></script>
<script src="{{ asset('web/js/owl.carousel.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $(".owl-carousel").owlCarousel();
    });

    $('.owl-carousel').owlCarousel({
        loop:true,
        margin:10,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
                nav:true
            },
            600:{
                items:3,
                nav:false
            },
            1000:{
                items:5,
                nav:true,
                loop:false
            }
        }
    })
</script>

</html>