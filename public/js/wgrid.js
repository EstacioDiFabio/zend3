define(['jquery', 'bootbox', 'windel'],
    function($, bootbox, windel) {

        'use strict';

        var wgrid = function() {

            var $public = {};
            var $private = {};

            /* Metodo publico que exporta os metodos privados */
            $public.init = function init() {

                $public.removeRow();
                $public.statusCheck();

            };
            /* Metodo para deletar um item na listagem */
            $public.removeRow = function removeRow() {

                $(".delete-action").on('click', function(){

                    var id = $(this).data('id');

                    bootbox.confirm({
                        title: "Remover Registro? ",
                        message: "Deseja realmente excluir esse registro? Essa ação não pode ser revertida.",
                        buttons: {
                            cancel: {
                                label: '<i class="fa fa-times"></i> Não',
                                className: 'btn hoverable-scale'
                            },
                            confirm: {
                                label: '<i class="fa fa-check"></i> Sim',
                                className: 'btn hoverable-scale btn-primary'
                            }
                        },
                        callback: function (result) {

                            if(result){

                                $.ajax({
                                    url: windel.url()+'/remove/',
                                    type: 'POST',
                                    data: {id: id},
                                })
                                .done(function(data) {
                                    windel.reload();
                                })
                                .fail(function(xhr, status, error) {
                                    console.error(xhr.responseText);
                                });

                            }
                        }
                    });
                    $(".bootbox-close-button").addClass('hoverable-rotate hoverable-rotate-moviment')
                });
            };
            /* Metodo para alterar o status de um item na listagem mais rapidamente */
            $public.statusCheck = function statusCheck() {


                $(".status-index").each(function() {

                    if( $(this).val() == 1)
                        $(this).attr('checked', 'checked');

                    $(this).on('click', function(){

                        var id           = $(this).data('id'),
                            status       = $(this).is(":checked"),
                            statusString = $(this).data('status'),
                            _this        = $(this),
                            contents     = $('.status_string_'+id).html();

                        if($.trim(contents) == 'Ativo'){
                            $('.status_string_'+id).text('Inativo');
                        } else {
                            $('.status_string_'+id).text('Ativo');
                        }

                        bootbox.confirm({

                            title: "Alterar o status? ",
                            message: "Deseja realmente alterar o estado desse item? Essa ação pode ter efeitos colaterais.",
                            buttons: {
                                cancel: {
                                    label: '<i class="fa fa-times"></i> Não',
                                    className: 'btn hoverable-scale'
                                },
                                confirm: {
                                    label: '<i class="fa fa-check"></i> Sim',
                                    className: 'btn hoverable-scale btn-primary'
                                }
                            },
                            callback: function (result) {

                                if (result) {

                                    var url = windel.url();

                                    $.ajax({
                                        url: url+'/toggleActive',
                                        type: 'POST',
                                        data: {
                                            id: id,
                                            status: status,
                                        },
                                    })
                                    .done(function(result) {
                                        windel.reload();
                                    })
                                    .fail(function(xhr, status, error) {
                                        console.error(status);
                                    });

                                } else {

                                    var contents = $('.status_string_'+id).html();

                                    if($.trim(contents) == 'Ativo'){
                                        $('.status_string_'+id).text('Inativo');
                                        _this.removeAttr('checked')
                                    } else {
                                        $('.status_string_'+id).text('Ativo');
                                        _this.attr('checked', 'checked')
                                    }

                                }
                            }
                        });

                        $(".bootbox-close-button").addClass('hoverable-rotate hoverable-rotate-moviment')
                    })

                });
            }

            return $public;
        };

        return wgrid();
});