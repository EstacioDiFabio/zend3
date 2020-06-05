define(['jquery', 'bootbox'],
    function($, bootbox) {

        'use strict';

        var global = function() {

            var $public = {};
            var $private = {};

            /* Metodo publico que exporta os metodos privados */
            $public.init = function init() {

                $public.url();
            };
            /* Metodo que retorna o controller da url */
            $public.url = function url() {
                var str = window.location.pathname;
                var slice = str.split('/');
                var total = slice.length;
                return slice[total-1];
            }
            /* Metodo que recarrega a pagina */
            $public.reload = function reload() {

                bootbox.dialog({
                   message: '<p class="load text-center"><i class="fa fa-spin fa-spinner fa-5x"></i></p>',
                   closeButton: false
                });

                setTimeout(function() {
                    window.location = $public.url()
                }, 1000);
            }

            return $public;
        };

        return global();
});