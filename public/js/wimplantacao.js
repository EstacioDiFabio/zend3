define(['jquery', 'bootbox', 'windel', 'jtimepicker', 'easytimer'],
    function($, bootbox, windel, jtimepicker, easytimer) {

        'use strict';

        var wimplantacao = function() {

            var $public = {};
            var $private = {};

            /* METODO PUBLICO QUE EXPORTA OS METODOS PRIVADOS. */
            $public.init = function init() {

                var timer = $private.invokeTimer();
                $private.habilitarAbas();
                $private.iniciarImplantacao(timer);
                $private.toggleFieldsAwnser();
                $private.toogleReschedule();
                $private.formManager(timer);
                $private.stopAndReschedule(timer);
            };
            $private.invokeTimer = function invokerTimer() {

                return new easytimer();
            }
            /* HABILITA AS ABAS JÁ PREENCHIDAS E SINALIZA O PRÓXIMO */
            $private.habilitarAbas = function habilitarAbas(){

                var tab = $(".tab-pane").find('input[data-respondido="true"]'),
                    index = '',
                    lastIndex = '',
                    className = '';

                if(tab){
                    tab.each(function() {

                        var form = $(this).closest('form').attr('class');

                        $('.'+form+':not(form)').attr('data-toggle', 'tab');
                        $('.'+form).addClass('preenchido');
                        $('.'+form+':not(form)').append('<i class="fas fa-check"></i>');
                        $("."+form+":not(.formulario)").children('.text-left').remove();

                        index = $('.'+form+':not(form)').data('index');
                        if (undefined !== index) {
                            className = $('.'+form+':not(form)').data('index', index).attr('href');
                        }
                    });

                    var classFormat = className.replace(/#/g,'');
                    if (classFormat !== '') {
                        var index = $("."+classFormat).data('index') + 1,
                            item = $('a[data-index="'+index+'"]');

                        item.addClass('continue');
                            //.attr('data-toggle', 'tab')
                        $('form[class$="preenchido"]').children('.fieldset-preenchido').attr('disabled', true)
                        $('form[class$="preenchido"]').children('.fieldset-preenchido').children('.text-left').remove();
                    }
                }
            }
            /* MANIPULADOR DE NAVEGAÇÃO ENTRE ABAS.*/
            $private.iniciarImplantacao = function iniciarImplantacao(timer){

                var _this = this;

                $(".iniciarImplantacao").on('click', function(){

                    $(".dados").removeClass('active');
                    $("#dados").removeClass('active');

                    if ($("a").hasClass('continue')) {

                        $(".continue").addClass('active ');
                        var href = $(".continue").attr('href').replace(/#/g,''),
                            index = $(".continue").data('index');

                        $("#"+href).addClass('active show');
                        $(".continue").attr('data-toggle', 'tab');

                        index = parseInt(index);
                        if(index >= 1){
                            _this.iniciarCronometro(timer);
                            _this.startImplantacao();
                        }

                        _this.iniciarImplantacaoCRM();

                    } else {

                        var tabs = $(".implantacao-tab > .nav-item").length,
                            fill = $(".implantacao-tab > .nav-item .preenchido").length,
                            tabTotal = parseInt(tabs) - 2;

                        // SE NAO FOR PREENCHIDO TODOS
                        if (tabTotal > fill) {
                            var target = 0,
                                formu = $(".formulario").data('index', target),
                                href = formu.attr('href'),
                                classForm = href.replace(/#/g,'');

                            $("."+classForm+":not(form)").addClass('active continue')
                                                         .attr('data-toggle', 'tab')
                                                         .attr('role', 'tab')

                            $("."+classForm+":not(form)").children('.text-left').remove();

                            $(href).addClass('active show')
                                   .attr('data-toggle', 'pills');

                        } else {

                            $(".nav-finalizar").attr('role', 'tab');
                            $(".nav-finalizar > a").attr('data-toggle', 'tab');

                            $("#finalizar").addClass('active show');
                            $("#dados").removeClass('active show');
                        }
                    }

                });
            }
            /* ALTERNA ENTRE O TIPO DE CAMPO QUE DEVE SER EXIBIDO. */
            $private.toggleFieldsAwnser = function toggleFieldsAwnser(){

                $(".partial").hide();

                if ($(".type").val() == 4 || $(".type").val() == 5) {
                    $(".partial").show();
                } else {
                    $(".partial").hide();
                }

                $(".type").on('change', function(){
                    if($(this).val() == 4 || $(this).val() == 5 ){
                        $(".partial").show();
                    } else {
                        $(".partial").hide();
                    }
                });
            }
            /* MOSTRA O CALENDÁRIO DO TÉCNICO, PARA REAGENDAR UMA IMPLANTACAO. */
            $private.toogleReschedule = function toogleReschedule(){

                $(".hidden").hide();

                $(".reagendar").on('click', function(){

                    $('.add-half').addClass('half-trace-before');
                    $(".hidden").fadeIn();
                    $("input[name=date]").addClass('datepicker').removeAttr('readonly');

                    var date = new Date();
                        date.setDate(date.getDate()-1);
                    var month = new Date();
                        month.setMonth(month.getMonth()+2);

                    $(".datepicker").datepicker({
                        language: "pt-BR",
                        startDate: date,
                        endDate: month,
                        daysOfWeekDisabled: "0,6",
                        todayHighlight: false,
                        toggleActive: false,
                        maxViewMode: 2,
                        autoclose: true,
                    }).on('changeDate', function (ev) {

                        $("input[name=time]").addClass('timepicker').removeAttr('readonly').removeAttr('disabled');

                        var selDate = ev.date,
                            d       = new Date(selDate),
                            month   = ''+(d.getMonth()+1),
                            day     = ''+(d.getDate()),
                            year    = d.getFullYear();

                        $private.doGetHoursByDate(year+"-"+month+"-"+day)

                    });

                });

                return false;
            }
            /* POPULA O TIMEPICKER COM OS HORARIOS INDISPONÍVEIS PARA A DATA JÁ SELECIONADA. */
            $private.firstInitiation = function firstInitiation(){

                var selDate = $(".datepicker").val(),
                    d       = new Date(selDate),
                    month   = ''+(d.getMonth()+1),
                    day     = ''+(d.getDate()),
                    year    = d.getFullYear(),
                    _this   = this;

                _this.doGetHoursByDate(year+"-"+day+"-"+month);
            }
            /* POPULA O TIMEPICKER COM OS HORARIOS INDISPONIVEIS PARA A DATA SELECIONADA. */
            $private.doGetHoursByDate = function doGetHoursByDate(date, timer){

                var baseUrl = $("#baseUrl").val(),
                    url     = baseUrl+'/agenda-cliente/getHoursByDate';

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        date: date,
                    },
                })
                .done(function(result) {

                    $('.timepicker').timepicker('remove');
                    var disabled = [],
                        fullYear = date;

                    disabled.push(['12', '14']);

                    if(result){

                        result.data.map(function(item) {
                            disabled.push([item.start, item.end]);
                        })
                    }

                    $(".timepicker").timepicker({
                        lang: 'hr',
                        timeFormat: 'H:i',
                        disableTextInput: true,
                        disableTimeRanges: disabled,
                        wrapHours: true,
                        step: 60*1,
                        minTime: '09',
                        maxTime: '17:00pm',
                        className: 'w-timepicker'
                    }).on('changeTime', function() {
                        var hora_inicial = $(this).val();
                        $private.secondInitiation(hora_inicial, disabled);
                    });

                })
                .fail(function(x, e, m) {
                    console.log(x, e, m);
                }).always(function(){
                    $private.pararCronometro(timer);
                });
            }
            /* POPULA O TIMEPICKER DE HORA FINAL DE ACORDO COM O HORARIO ESCOLHIDO. */
            $private.secondInitiation = function secondInitiation(hora_inicial, disabled){

                $('.timepickerEnd').timepicker('remove');
                $('.timepickerEnd').removeAttr('readonly').removeAttr('disabled');

                var nDisabled = [],
                    horas_cliente = $(".cliente-hour").text();

                nDisabled = disabled
                nDisabled.push(['12:30', '13:30']);

                if(parseInt(hora_inicial) < 12){ // caso for de manha

                    nDisabled.push(['08', hora_inicial]); // bloqueia horarios anteriores
                    nDisabled.push(['13:30', '18:30']); // bloqueia toda tarde

                    if(horas_cliente < 3){
                        var bloq = parseInt(hora_inicial)+parseInt(horas_cliente)+1;
                        nDisabled.push([bloq*60*60, '12:30']); // bloqueia do horario limite ate o fim do turno
                    }

                } else { // caso for a tarde

                    nDisabled.push(['08', '12:30']); // bloqueia toda manha
                    nDisabled.push(['13:30', hora_inicial]); // bloqueia horarios posteriores
                    var hora_limite = parseInt(hora_inicial) + 3;
                    var hora_final = '18:30';

                    if(horas_cliente < 4){
                        var bloq = parseInt(hora_inicial)+parseInt(horas_cliente)+1
                    } else if(hora_limite <= 17) {
                        var bloq = parseInt(hora_limite)+1;
                    } else {
                        var bloq = parseInt(hora_limite)+1;
                        hora_final = '18:00';
                    }

                    nDisabled.push([bloq*60*60, hora_final]); // bloqueia do horario limite ate o fim do turno

                }

                var hf = parseInt(hora_inicial)+1;
                nDisabled.push([hora_inicial, String(hf)]);

                $(".timepickerEnd").timepicker({

                    lang: 'hr',
                    timeFormat: 'H:i',
                    disableTextInput: true,
                    disableTimeRanges: nDisabled,
                    wrapHours: true,
                    step: 60*1,
                    minTime: '09',
                    maxTime: '18:00pm',
                    className: 'w-timepicker'

                })
            }
            /* SALVA DADOS NO BANCO. */
            $private.formManager = function formManager(timer){

                var _this = this;
                $(".avancarImplantacao").on('click', function(){

                    var finalizar = $(this).parent('div')
                                           .parent('fieldset')
                                           .parent('form')
                                           .parent('div')
                                           .parent('div')
                                           .parent('.tab-pane')
                                           .next('.tab-pane')
                                           .attr('id');

                    if(finalizar == 'finalizar'){
                        var value = 'Finalizar';
                    } else {
                        var value = $(this).children('input').val()
                    }

                    var target = $(this).children('input').data('target'),
                        key    = $(this).children('input').data('key');

                    if(key == 'termo_de_levantamento_de_dados'){
                        _this.iniciarImplantacaoCRM();
                    }

                    _this.formSubmit(value, target, key, timer);
                });
            }
            /* SALVA OS DADOS NO BANCO E FAZ O REAGENDAMENETO DA MESMA PARA OUTRA DATA SELECIONADA. */
            $private.stopAndReschedule = function stopAndReschedule(timer){

                if ($(".pausarImplantacao").length > 0) {
                    var _this = this;
                    $(".pausarImplantacao").on('click', function(){

                        var bt = this,
                            date = new Date(),
                            month = new Date();

                            date.setDate(date.getDate()-1),
                            month.setMonth(month.getMonth()+2);

                        $("#modalReagendamento").modal({
                            backdrop: 'static'
                        });

                        $("#modalReagendamento").on('shown.bs.modal', function(e){
                            _this.pararCronometro(timer);
                        });
                        $("#modalReagendamento").on('hide.bs.modal, hidden.bs.modal', function(e){
                            _this.iniciarCronometro(timer);
                        });

                        $(".pickerdate").datepicker({
                            language: "pt-BR",
                            startDate: date,
                            endDate: month,
                            daysOfWeekDisabled: "0,6",
                            todayHighlight: false,
                            toggleActive: true,
                            maxViewMode: 2,
                            autoclose: true
                        }).on('changeDate', function (ev) {

                            var selDate = ev.date,
                                d       = new Date(selDate),
                                month   = ''+(d.getMonth()+1),
                                day     = ''+(d.getDate()),
                                year    = d.getFullYear();

                            _this.doGetHoursByDate(year+"-"+month+"-"+day, timer)

                        });

                        $(".confirmReschedule").on('click', function(){
                            if ($(".pickerDate").val() !== "") {

                                $(bt).siblings('.avancarImplantacao').children('input').trigger('click');

                                var baseUrl = $("#baseUrl").val(),
                                    url     = baseUrl+'/agenda-tecnico/insertReschedule',
                                    data    = {id: $(".deployment_schedule_id").val(),
                                               date: $(".pickerdate").val(),
                                               time: $(".timepicker").val(),
                                               time_end: $(".timepickerEnd").val()};

                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: data,
                                })
                                .done(function(data) {

                                    var value  = 'Pausar',
                                        target = $(bt).data('target'),
                                        key    = $(bt).data('key');

                                    bootbox.dialog({
                                       message: '<p class="load text-center"><i class="fa fa-spin fa-spinner fa-5x"></i></p>',
                                       closeButton: false
                                    });

                                    $("#modalReagendamento").modal('hide');

                                    setTimeout(function(data) {
                                        _this.formSubmit(value, target, key, timer);
                                        window.location = baseUrl+'/agenda-tecnico/index';

                                    }, 1000);

                                })
                                .fail(function(x, e, m) {
                                    console.log("error");
                                });
                            }
                        });

                    });
                    $(".bootbox-close-button").addClass('hoverable-rotate hoverable-rotate-moviment');
                }
            }
            /* SUBMETE OS DADOS PARA O CONTROLADOR PHP. */
            $private.formSubmit = function formSubmit(value, target, key, timer){

                var baseUrl = $("#baseUrl").val(),
                        url = baseUrl+'/agenda-tecnico/insertData',
                    _this   = this;

                if(value !== 'Pausar'){
                    bootbox.dialog({
                       message: '<p class="load text-center"><i class="fa fa-spin fa-spinner fa-5x"></i></p>',
                       closeButton: false
                    });
                }

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $("."+key).serialize(),
                })
                .done(function(data) {

                    setTimeout(function() {

                        if ( value == 'Finalizar') {

                            $(".finalizar").addClass('active continue');
                            $("#finalizar").addClass('active');

                            $("#"+key).removeClass('active');
                            $("."+key).removeClass('active');
                                //
                            $(".nav-finalizar").attr('role', 'tab');
                            $(".nav-finalizar > a").attr('data-toggle', 'tab');

                            $("#finalizar").addClass('active show');

                            _this.pararCronometro(timer);
                            $(".tempo-decorrido").val($(".time").html());

                        } else if(value !== 'Pausar'){

                            // PROXIMO
                            var href = $("."+target).attr('id');
                            $("#"+href).addClass('active show');
                            $("."+href+":not(form)").addClass('active continue')
                                                    .attr('data-toggle', "tab");

                            var index = target.replace(/question_/g,'');
                                index = parseInt(index);

                            if(index >= 1 && (timer.isRunning() == false)){
                                _this.iniciarCronometro(timer);
                                _this.startImplantacao();
                            }
                            // ANTERIOR
                            var Khref = $("#"+key).attr('id');
                            $("#"+Khref).removeClass('active show');

                            var txt = $("."+Khref+":not(form)").text();

                            $('form[class="'+Khref+'"]').children('.fieldset-preenchido')
                                                        .attr('disabled', true)

                            $('form[class="'+Khref+'"]').children('.fieldset-preenchido')
                                                        .children('.text-left')
                                                        .remove();

                            $("."+Khref+":not(form)").removeClass('active continue')
                                                     .addClass('preenchido')
                                                     .attr('data-toggle', "tab")
                                                     .html(txt)
                                                     .append('<i class="fas fa-check"></i>');

                        }
                        if(value !== 'Pausar'){
                            bootbox.hideAll();
                        }
                    }, 500);

                })
                .fail(function(x, h, r) {
                    bootbox.hideAll();
                    console.error(x, h, r);
                });
            }
            /* INICIA O CONTADOR DE HORAS */
            $private.iniciarCronometro = function iniciarCronometro(timer){

                if(timer.isRunning() == false) {

                    timer.start();
                    timer.addEventListener('secondsUpdated', function (e) {
                        $('.time').html(timer.getTimeValues().toString());
                    });
                }
            }
            /* FINALIZA O CONTADOR DE HORAS */
            $private.pararCronometro = function pararCronometro(timer){

                if(timer.isRunning() == true){
                    timer.pause();
                }
            }
            /* MODIFICA O ATENDIMENTO PARA O STATUS DE IMPLANTACAO INICIADA */
            $private.iniciarImplantacaoCRM = function iniciarImplantacaoCRM(){

                var scheduling = $('input[name=scheduling]').val(),
                       baseUrl = $("#baseUrl").val(),
                           url = baseUrl+'/agenda-tecnico/iniciarImplantacaoCRM';

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {scheduling: scheduling},
                })
                .fail(function(x, h, r){
                    console.error(x, h, r);
                });
            }
            /* ADICIONA A HORA QUE INICIA O ATENDIMENTO */
            $private.startImplantacao = function startImplantacao(){

                var scheduling = $('input[name=scheduling]').val(),
                       baseUrl = $("#baseUrl").val(),
                           url = baseUrl+'/agenda-tecnico/startImplantacao';

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {scheduling: scheduling},
                })
                .fail(function(x, h, r){
                    console.error(x, h, r);
                });
            }

            return $public;
        };

        return wimplantacao();
});