define(['jquery', 'bootbox', 'global', 'wgrid', 'notifIt', 'wform'],
    function($, bootbox, global, wgrid, notif, wform) {

        'use strict';

        var wfilter = function() {

            var $public = {};
            var $private = {};

            /* Metodo publico que exporta os metodos privados */
            $public.init = function init() {

                $private.doSearch();
                $private.cloneFirst();
                $private.cloneFilter();
                $private.removeDinamicLine();
                $private.toggleCollapseIcon();
            };
            /* Metodo que faz a pesquisa de acordo com os filtros preenchidos */
            $private.doSearch = function doSearch() {

                if ($("#search-form").length > 0) {

                    $('#button-filter').click( function() {

                        try {

                            var column = [];

                            $('.cloned select[name^="search_columns"]').map(function() {
                                if($(this).val() !== '')
                                    column.push($(this).val());
                                else
                                    throw (new Error("Campo Coluna não pode ficar vazio"));
                            });

                            var operator = [];
                            $('.cloned select[name^="search_operators"]').map(function() {
                                if($(this).val() !== '')
                                    operator.push($(this).val());
                                else
                                    throw (new Error("Campo Operador não pode ficar vazio"));
                            });

                            var value = [];
                            $('.cloned input[name^="search_value"]').map(function() {
                                if($(this).val() !== '')
                                    value.push($(this).val());
                                else
                                    throw "Campo Valor não pode ficar vazio";
                            });

                            if(value == "" || operator == "" || column == ""){
                                throw (new Error("Campo não pode ficar vazio"));
                            }

                            $.ajax({

                                type: 'GET',
                                url: global.url()+"/search",
                                data: {
                                    'column': column,
                                    'operator': operator,
                                    'value': value
                                },
                                success: function(data) {

                                    var dataTab = data.data;
                                    var oTable = $('.dataTables').dataTable({
                                        retrieve: true,
                                    });

                                    oTable.fnClearTable();

                                    if(dataTab.length !== undefined || $.isEmptyObject(dataTab.data) == false)
                                        oTable.fnAddData(dataTab);

                                    wgrid.removeRow();
                                    wform.initCheckbox();
                                    wgrid.statusCheck();
                                }

                            })
                            .fail(function(error, status, type) {
                                return false;
                            });
                        }
                        catch (e) {

                            notif.notif({
                                msg: "<b>Oops!</b> "+e,
                                type: "warning",
                                position: "right",
                                autohide: false,
                                clickable: true,
                                offset: 50,
                            });

                            return false;
                        }

                    });

                }
            }
            /* Inicia os campos do filtro */
            $private.cloneFirst = function cloneFirst() {

                if ($(".clone").data('noclone') !== true) {

                    var cloned = $(".clone").data('index', '0').clone();

                    cloned.attr('data-index', 1);
                    cloned.removeClass('clone').addClass('cloned').addClass('cloned_1');
                    cloned.find('.remove').css({visibility: 'hidden'});
                    cloned.insertAfter(".clone");
                }

            }
            /* Duplica o campo de filtro */
            $private.cloneFilter = function cloneFilter() {

                var total = $("#search_columns").find('option').length;

                $("#add-more").on('click', function(){

                    var cloned = $(".clone").clone();
                    var clonedLen = $(".cloned").length + 1;
                    var lastCloned = $(".cloned").last().data('index') - 1;

                    cloned.attr('data-index', clonedLen);
                    cloned.removeClass('clone').addClass('cloned').addClass('cloned_'+clonedLen);
                    cloned.find('.remove-line').attr('data-index', clonedLen).addClass('remove-line_'+clonedLen);
                    cloned.insertAfter($(".cloned").last());

                });
            }
            /* Remove o a linha duplicada */
            $private.removeDinamicLine = function removeDinamicLine() {

                $("#search-form").on('click', '.remove-line', function(){

                    if($(this).data('index') == 1)
                        return false;
                    else{
                        $('.cloned_'+$(this).data('index')).remove();
                    }
                });
            }
            /* Muda o estado de exibicao do icone no cabecalho da pesquisa */
            $private.toggleCollapseIcon = function toggleCollapseIcon(){

                $(".toggle").html('<i class="fas fa-angle-down"></i>');

                $(".collapse").on('hide.bs.collapse', function(){
                    $(".toggle").html('<i class="fas fa-angle-down"></i>');
                    $("#form-filter > .card > .card-header").css({
                        'box-shadow': 'unset'
                    });
                });
                $(".collapse").on("show.bs.collapse", function(){
                    $(".toggle").html('<i class="fas fa-angle-up"></i>');
                    $("#filter-inputs > .card-body").css({
                        'box-shadow': '0px 5px 15px #6c757d'

                    });
                    // $("#form-filter > .card > .card-header").css({
                    //     'box-shadow': '5px 0px 5px #6c757d'
                    // });
                });
            }
            return $public;
        };

        return wfilter();
});