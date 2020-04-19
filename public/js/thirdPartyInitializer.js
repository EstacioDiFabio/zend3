define(['jquery', 'bootbox', 'summernote', 'summernote_pt', 'pekeUpload',
        'dataTablesBootStrap', 'datatables.net', 'timepicker', 'datepicker',
        'datepicker_pt', 'waves'],
    function($, bootbox, summernote, summernote_pt, pekeUpload, dataTablesBootStrap,
             datatables, timepicker, datepicker_pt, datepicker, waves) {

        'use strict';

        var thirdPartyInitializer = function() {

            var $public = {};
            var $private = {};

            /* Metodo publico que exporta os metodos privados */
            $public.init = function init() {

                $private.initSummernote();
                $private.initPekeUpload();
                $private.initDataTables();
                $private.initTimePicker();
                $private.initDatePicker();

            };
            /* Metodo para inicar o editor de textos em textearea */
            $private.initSummernote = function initSummernote() {

                if($('.summerNote').length > 0){
                    $('.summerNote').summernote({
                        lang: 'pt-BR',
                        height: 300,
                        dialogsInBody: true,
                        hintDirection : 'bottom',
                        toolbar: [
                            ['misc', ['style', 'undo', 'redo', 'codeview', 'fullscreen']],
                            ['font', ['fontname', 'fontsize', 'bold', 'italic', 'underline', 'strikethrough', 'color', 'clear']],
                            ['format', ['height', 'ol', 'ul', 'paragraph', 'table', 'hr']],
                            ['media', ['picture', 'link', 'video']],
                            ['help', ['help']],
                        ],
                        popover: {
                            image: [
                                ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
                                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                                ['remove', ['removeMedia']]
                            ],
                            link: [
                                ['link', ['linkDialogShow', 'unlink']]
                            ],
                        }
                    });

                    $(".modal-dialog .modal-footer > .text-center a").remove();

                }
            }
            /* Metodo para inicar o uploader de imagem */
            $private.initPekeUpload = function initPekeUpload() {

                if ($(".fileUpload").length > 0) {

                    $(".fileUpload").pekeUpload({
                        onSubmit: true,
                        allowedExtensions: 'jpg|png|jpeg|gif|doc|pdf|csv|ods|odt',
                        btnText: '<br><span class="btn btn-success fileinput-button"><i class="fas fa-plus-circle"></i></span>',
                        delfiletext: '<br><span class="btn btn-danger"><i class="fas fa-times-circle"></i></span>',
                        maxSize: 15000000,
                        invalidExtError: 'Extensão do arquivo inválido.',
                        sizeError: "O seu arquivo é muito grande.",
                        errorOnResponse: "Houve um erro ao processar o envio do seu arquivo, tente novamente em instantes.",
                        onFileError:function(file, error){
                            bootbox.alert({
                                message:"<h4>Erro ao enviar o arquivo</h4> <br>"+file.name+"<br> <strong>"+error+"</strong>",
                                backdrop: true
                            });
                        },
                    });
                }
            }
            /* Metodo para iniciar o datatables*/
            $private.initDataTables = function initDataTables(){

                var table = $(".dataTables").DataTable({

                    stateSave: false,
                    searching: false,
                    "lengthMenu": [ [10, 50, 100, -1], [10, 50, 100, "Todos"] ],
                    "columnDefs": [
                        {
                            "orderable": false,
                            "targets": -1
                        }
                    ],
                    "language":{
                        "sEmptyTable": "Nenhum registro encontrado",
                        "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                        "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                        "sInfoPostFix": "",
                        "sInfoThousands": ".",
                        "sLengthMenu": "_MENU_ resultados por página",
                        "sLoadingRecords": "Carregando...",
                        "sProcessing": "Processando...",
                        "sZeroRecords": "Nenhum registro encontrado",
                        "sSearch": "Pesquisar",
                        "oPaginate": {
                            "sNext": "Próximo",
                            "sPrevious": "Anterior",
                            "sFirst": "Primeiro",
                            "sLast": "Último"
                        },
                        "oAria": {
                            "sSortAscending": ": Ordenar colunas de forma ascendente",
                            "sSortDescending": ": Ordenar colunas de forma descendente"
                        },
                        "decimal": ",",
                        "thousands": "."
                    },

                });
            }
            /* Metodo para iniciar o plugin de timePicker (http://timepicker.co)*/
            $private.initTimePicker = function initTimePicker(){

                $(".timepicker").timepicker({
                    timeFormat: 'HH:mm',
                    interval: 30,
                    minTime: '08',
                    maxTime: '20:00pm',
                    defaultTime: '--',
                    startTime: '08:00',
                    dynamic: false,
                    dropdown: true,
                    scrollbar: true
                });

                $(".now").on('click', function(){

                    $(".timepicker").val("");

                    var ctime = new Date(),
                        min = ctime.getMinutes() > 9 ? ctime.getMinutes() : "0"+ctime.getMinutes();

                    $(".timepicker").val(ctime.getHours()+":"+min);
                });

                if($(".timepicker").length > 2){
                    var i = 0;

                    for (i = i; i <= $(".timepicker").length; i++) {
                        var input = $(".timepicker")[i];

                        if($(input).attr('value') != ''){
                            $(input).val($(input).attr('value'))
                        }

                    }

                }

            }
            /* Metodo para iniciar o plugin de datePicker do Bootstrap*/
            $private.initDatePicker = function initDatePicker(){

                var date = new Date();
                    date.setDate(date.getDate()-1);
                var month = new Date();
                    month.setMonth(month.getMonth()+2);

                $(".datepicker").datepicker({
                    language: "pt-BR",
                    startDate: date,
                    daysOfWeekDisabled: "0,6",
                    todayHighlight: true,
                    toggleActive: true,
                    maxViewMode: 2,
                    autoclose: true
                });

                $(".today").on('click', function(){
                    $( '.datepicker' ).val("");

                    var ctime = new Date(),
                        month = ctime.getMonth()+1;
                        if(month > 9){
                            month = "0"+month;
                        }
                    var today = ctime.getDate()+"/"+month+"/"+ctime.getFullYear();

                    $( '.datepicker' ).datepicker( "setDate", today );
                });

                $(".tomorrow").on('click', function(){
                    $( '.datepicker' ).val("");

                    var ctime = new Date(),
                        month = ctime.getMonth()+1;
                        if(month > 9){
                            month = "0"+month;
                        }

                    var day = ctime.setDate(ctime.getDate()+1),
                        today = ctime.getDate()+"/"+month+"/"+ctime.getFullYear();

                    $( '.datepicker' ).datepicker( "setDate", today );
                });
            }


            return $public;
        }

        return thirdPartyInitializer();
});