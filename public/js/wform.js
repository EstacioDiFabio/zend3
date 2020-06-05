define(['jquery', 'bootbox', 'global', 'wgrid', 'notifIt'],
    function($, bootbox, global, wgrid, notif) {

        'use strict';

        var wform = function() {

            var $public = {};
            var $private = {};

            /* Metodo publico que exporta os metodos privados */
            $public.init = function init() {

                $public.initCheckbox();
                $public.initCheckboxRequired();
                $private.matInput();
                $private.scrollEffect();
                $private.initWaves();
            };
            /* Metodo para iniciar o plugin de checkbox */
            $public.initCheckbox = function initCheckbox(){

                var id = $("input[name=status]:not(.status-index)").attr('name');
                $("input[name=status]:not(.status-index)").attr('id', id);

                $private.checkStatus($("#status"));

                $("#status").on('click', function(){
                    $private.checkStatus($(this));
                });
            }
            /* Metodo para iniciar o plugin de checkbox */
            $public.initCheckboxRequired = function initCheckboxRequired(){

                var id = $("input[name=required]:not(.required-index)").attr('name');
                $("input[name=required]:not(.required-index)").attr('id', id);

                $private.checkStatuRequired($("#required"));

                $("#required").on('click', function(){
                    $private.checkStatuRequired($(this));
                });
            }
            $private.checkStatuRequired = function checkStatuRequired(obj){

                if(obj.is(":checked")){

                    if($(".status_description_required").length > 0){
                        $(".status_description_required").html(" ");
                        $(".status_description_required").html("Obrigatório");
                    }

                } else {
                    if($(".status_description_required").length > 0){
                        $(".status_description_required").html(" ");
                        $(".status_description_required").html("Opcional");
                    }
                }
            }
            /* Altera o label quando o checkbox é manipulado */
            $private.checkStatus = function checkStatus(obj){

                if(obj.is(":checked")){

                    if($(".status_description").length > 0){

                        $(".status_description").html(" ");
                        $(".status_description").html("Ativo");
                    }

                    if($(".status_description_form").length > 0){
                        $(".status_description_form").html(" ");
                        $(".status_description_form").html("Habilitado");
                    }

                } else {

                    if($(".status_description").length > 0){

                        $(".status_description").html(" ");
                        $(".status_description").html("Inativo");
                    }

                    if($(".status_description_form").length > 0){
                        $(".status_description_form").html(" ");
                        $(".status_description_form").html("Desabilitado");
                    }
                }
            }
            /* Altera a exibição do input padrão para a exibição no estilo Materialize */
            $private.matInput = function matInput(){

                $(".form-signin").trigger('click');

                if($(".mat-input").val() !== ''){
                    $(".mat-input").parent().addClass("is-active is-completed");
                }

                $(".mat-input").focus(function(){
                    $(this).parent().addClass("is-active is-completed");
                });

                $(".mat-input").focusout(function(){

                    if($(this).val() === "")
                        $(this).parent().removeClass("is-completed");
                    $(this).parent().removeClass("is-active");
                })
            }
            /* Adiciona uma sombra no header quando ele se sobrepor a algum elemento */
            $private.scrollEffect = function scrollEffect(){

                $(window).scroll(function() {

                    if ($(this).scrollTop() >= 10) {

                            $(".navbar").addClass("shadow");

                    } else {
                        $(".navbar").removeClass("shadow");
                    }
                });

                // $('#return-to-top').click(function() {
                //     $('body,html').animate({
                //         scrollTop : 0
                //     }, 500);
                // });
            }
            /* Metodo para iniciar o efeito de ondas no clique dos botoes. */
            $private.initWaves = function initWaves(){
                Waves.attach('.btn, .card-header:not(.note-toolbar)');
                Waves.init();
            }
            return $public;
        };

        return wform();
});