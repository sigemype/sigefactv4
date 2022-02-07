$('.registrar').on('click', function(){
    $('.modal_entero').addClass('modal_mostrar');
});
$('.x').on('click', function(){
    $('.modal_entero').toggleClass('modal_mostrar');
});



function scrollNav() {
  $('.menu a').click(function(){  
    //Animate
    $('html, body').stop().animate({
        scrollTop: $( $(this).attr('href') ).offset().top - 0
    }, 400);
    return false;
  });
  $('.scrollTop a').scrollTop();
}

scrollNav();

/*SCROLL END*/




var slider = $('.banner_todo');
var siguiente = $('.icon-derecha');
var anterior =  $('.icon-izquierda');
//mover ultima imagen al primer lugar
$('.banner_items:last').insertBefore('.banner_items:first');
//mostrar la primera imagen con un margen de -100%
slider.css('margin-left', '-'+100+'%');

function moverD() {
	slider.animate({
		marginLeft:'-'+200+'%'
	} ,700, function(){
		$('.banner_items:first').insertAfter('.banner_items:last');
		slider.css('margin-left', '-'+100+'%');
	});
};

function moverI() {
    slider.animate({
        marginLeft:0
    } ,700, function(){
        $('.banner_items:last').insertBefore('.banner_items:first');
        slider.css('margin-left', '-'+100+'%');
    });
};
//hacer que el slider sea automático
function autoplay() {
	interval = setInterval(function(){
		// moverDerecha();
	}, 3000);
};


siguiente.on('click',function() {
    moverD();
    clearInterval(interval);
    autoplay();
});

anterior.on('click',function() {
    moverI();
    clearInterval(interval);
    autoplay();
});