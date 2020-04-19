;(function( u ) {
  'use strict';

    if (document.body.contains(document.getElementById('baseUrl'))) {

        var baseUrl = document.getElementById('baseUrl').value ;

        require.config({
            baseUrl: baseUrl+'/js',
            paths: {
                jquery:              'jsvendor/jquery-3.1.0.min',
                popper:              'jsvendor/popper.min',
                bootstrap:           'jsvendor/bootstrap.bundle.min',
                fontawesome:         'jsvendor/fontawesome-all.min',
                bootbox:             'jsvendor/bootbox.min',
                summernote:          'jsvendor/summernote.min',
                summernote_pt:       'lang/summernote-pt-BR',
                pekeUpload:          'jsvendor/pekeUpload.min',
                dataTablesBootStrap: 'jsvendor/dataTables.bootstrap.min',
                'datatables.net':    'jsvendor/jquery.dataTables.min',
                notifIt:             'jsvendor/notifIt.min',
                timepicker:          'jsvendor/timepicker.min',
                jtimepicker:         'jsvendor/jquery.timepicker',
                datepicker:          'jsvendor/datepicker.min',
                datepicker_pt:       'lang/datepicker.pt-BR.min',
                waves:               'jsvendor/waves.min',
                easytimer:           'jsvendor/easytimer.min'
            },
            shim: {
                "popper": {
                    deps: ["jquery"]
                },
                "bootstrap": {
                    deps: ["jquery", "popper"]
                },
                "fontawesome": {
                    deps: ["jquery"]
                },
                "bootbox": {
                    deps: ["jquery", "bootstrap"],
                    exports: 'bootbox'
                },
                "summernote_pt": {
                    deps: ["jquery", "summernote"],
                    exports: "summernote_pt"
                },
                "summernote": {
                    deps: ["jquery"],
                },
                "pekeUpload": {
                    deps: ["jquery"]
                },
                "dataTablesBootStrap":{
                    deps: ["jquery"]
                },
                "datatables.net":{
                    deps: ["jquery"]
                },
                "notifIt":{
                    deps: ["jquery"]
                },
                "timepicker":{
                    deps: ["jquery"]
                },
                "jtimepicker":{
                    deps: ["jquery"]
                },
                "datepicker_pt": {
                    deps: ["jquery", "datepicker"],
                    exports: 'datepicker_pt'
                },
                "datepicker": {
                    deps: ["jquery"]
                },
                "waves": {
                    deps: ["jquery"]
                },
                "easytimer": {
                    deps: ["jquery"]
                }
            },
            urlArgs: "v=" + (new Date()).getTime()
        });

        var requireFullList = ['jquery', 'fontawesome', 'bootbox', 'summernote',
                               'summernote_pt', 'pekeUpload',
                               'dataTablesBootStrap', 'datatables.net',
                               'timepicker', 'datepicker_pt', 'datepicker',
                               'waves', 'jtimepicker'];

        require(['jquery', 'popper'], function($, Popper) {
            require(['bootstrap'], function() {

                /* Contém funções genericas comuns para todos os lugares*/
                require(['jquery', 'bootbox'], function($, bt) {

                    require(['windel'], function(app) {

                        $(document).ready(function(){
                            app.init();
                        });
                    });
                });
                /* Inicia e configura as bibliotecas de terceiros */
                require(requireFullList, function($, f, bt, sn, snpt, pu, dtbt, dtn, n, tp, dp, dppt, wv) {

                    require(['thirdPartyInitializer'], function(app) {

                        $(document).ready(function(){
                            app.init();
                        });
                    });
                });
                /* Contem metodos para gerenciar os grids */
                require(['jquery', 'bootbox', 'windel'], function($, bt, w) {

                    require(['wgrid'], function(app) {

                        $(document).ready(function(){
                            app.init();
                        });
                    });
                });
                /* Contem metodos para gerenciar o formulario */
                require(['jquery', 'bootbox', 'windel', 'wgrid', 'notifIt'], function($, bt, w, wg, n) {

                    require(['wform'], function(app) {

                        $(document).ready(function(){
                            app.init();
                        });
                    });
                });
                /* Contem metodos para gerenciar o formulario */
                require(['jquery', 'bootbox', 'windel', 'jtimepicker', 'easytimer'], function($, bt, w, jtp, et) {

                    require(['wimplantacao'], function(app) {

                        $(document).ready(function(){
                            app.init();
                        });
                    });
                });
                /* Contem metodos para gerenciar os filtros de pesquisa */
                require(['jquery', 'bootbox', 'windel', 'wgrid', 'notifIt', 'wform'], function($, bt, w, wg, n, wf) {

                    require(['wfilter'], function(app) {

                        $(document).ready(function(){
                            app.init();
                        });
                    });
                });
            });
        });
    }
})(document);