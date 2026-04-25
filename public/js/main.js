(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 50);
    };
    spinner();
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Sidebar Toggler
    $('.sidebar-toggler').click(function () {
        $('.sidebar, .content').toggleClass("open");
        return false;
    });


    // Progress Bar
    $('.pg-bar').waypoint(function () {
        $('.progress .progress-bar').each(function () {
            $(this).css("width", $(this).attr("aria-valuenow") + '%');
        });
    }, {offset: '80%'});


    // Calender
    $('#calender').datetimepicker({
        inline: true,
        format: 'L'
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        items: 1,
        dots: true,
        loop: true,
        nav : false
    });




    

    $("#formulario").submit(function(event) {
        event.preventDefault(); // Impede o envio padrão do formulário
        $('#spinner').addClass('show');
        const formUrl = $(this).attr("action");

        // Obtém os dados do formulário usando serialize
        var formData = $(this).serialize();

        // Envia os dados via AJAX
        $.ajax({
            type: "POST",
            url: formUrl, // Substitua pelo endpoint do seu servidor
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Lida com a resposta de sucesso
                
                if (response.hasOwnProperty('erros')) {
                    var resposta = '';
                    for (var i in response.erros) {
                        console.log(i);
                        if ($('#erro-'+i).length) {
                            $('#erro-'+i).html(response.erros[i]);
                          } else {
                            if(resposta != '') resposta+= '<br>';
                            resposta += response.erros[i];
                          }
                    }
                    if(resposta != '' && $('#erroGeral').length ){
                        $('#erroGeral').html(resposta);
                    }

                }
                console.log(resposta);
                $('#spinner').removeClass('show');
            },
            error: function(error) {
                // Lida com a resposta de erro
                console.error("Erro ao enviar dados:", error);
                $('#spinner').removeClass('show');
            }
        });
    });

    
})(jQuery);

function mudarStatusProjeto(projetoId){
    $.post( "/projetos/mudarStatusProjeto", { id: projetoId })
        .done(function( data ) {
           
            var botaoClicado = $('#btn_status_'+projetoId);

            if(data.sucess){
                
                if(data.novoStatus==1){
                    botaoClicado.removeClass('btn-danger');
                    botaoClicado.addClass('btn-success');
                    botaoClicado.html('ATIVO');
                }else if(data.novoStatus==0){
                    botaoClicado.removeClass('btn-success');
                    botaoClicado.addClass('btn-danger');
                    botaoClicado.html('INATIVO');
                }
            }
            
    }, "json");
}

msgAlerta= document.getElementById('msgAlerta').value;



if(msgAlerta){
    elementos= msgAlerta.split('-');
    console.log(msgAlerta);
    Swal.fire({
    title: elementos[0],
    text: elementos[1],
    icon: elementos[2]
  });
}


