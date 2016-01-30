/**
 * Timber - Ultimate Freelancer Platform
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 */

/*!
 * Stop Dropzone default action
 */
Dropzone.autoDiscover = false;
alertify.defaults.glossary.title = 'Confirmation';
alertify.defaults.transition = "zoom";
alertify.defaults.theme.ok = "ui positive button";
alertify.defaults.theme.cancel = "ui black button";

var timber = timber || {};

/*!
 * Timber Utils Module
 */
timber.utils = (function (window, document, $) {
    'use strict';

    var utils = {
        init : function() {
            utils.plugins();
            utils.alerts();
        },
        plugins : function() {
            $(document).ajaxStart(function() {
                Pace.restart();
            });

            $('[data-toggle="popup"]').popup();
            $('.ui.checkbox').checkbox();
            $('.dropdown').dropdown();
            $('section#main-wrapper')
              .on('click', '.message .close', function() {
                $(this)
                  .closest('.message')
                  .transition('fade');
              });

            $('.menu .item').tab();
            $('.page_uploader').on('click', function(event) {
                event.preventDefault();
                $("#" + $(this).attr('data-target')).modal('show');
            });

            $('.sidebar-toggle').on('click', function() {
                var bodyEl = $('#main-wrapper');
                ($(window).width() > 767) ? $(bodyEl).toggleClass('sidebar-mini'): $(bodyEl).toggleClass('sidebar-opened');
            });
            $(".sidebar .nav").navgoco({
                caretHtml: false,
                accordion: true
            });


            $.fn.DataTable.ext.pager.numbers_length = 5;
            $('table.data_table').dataTable({
                "searching": true,
                "lengthChange": false,
                "responsive": true,
                "language": {
                    "paginate": {
                        "previous": '<i class="left chevron icon"></i>',
                        "next": '<i class="right chevron icon"></i>'
                    }
                },
                "fnDrawCallback": function( oSettings ) {
                    $(".dataTables_paginate a.paginate_button").removeClass('paginate_button').addClass('ui tiny button');
                }
            });
        },
        fix : function() {
            $("dataTables_paginate a.paginate_button").each(function(index, el) {
                el.removeClass('paginate_button').addClass('ui tiny button');
            });
        },
        alerts : function(){
            toastr.options = {
                closeButton : true,
                debug : false,
                progressBar : true,
                positionClass : "toast-top-right",
                showDuration : 400,
                hideDuration : 1000,
                timeOut : 7000,
                extendedTimeOut : 1000,
                showEasing : "swing",
                hideEasing : "linear",
                showMethod : "fadeIn",
                hideMethod : "fadeOut"
            };
            if(typeof system_alerts_url !== 'undefined'){
                $.get(system_alerts_url, {}, function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            setTimeout(function() {
                                if( response.type == "success" ){
                                    toastr.success(response.data);
                                }
                                if( response.type == "error" ){
                                    toastr.error(response.data);
                                }
                                if( response.type == "info" ){
                                    toastr.info(response.data);
                                }
                                if( response.type == "warning" ){
                                    toastr.warning(response.data);
                                }
                            }, 1300);
                        }
                    }
                }, 'json');
            }
        },
    };
    return {
        init: utils.init,
        fix : utils.fix,
    };
})(window, document, jQuery);


/*!
 * Timber Install Module
 */
timber.install = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
        },
        init : function() {
            formUtils.submit();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        handler: function(event) {
            formUtils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            formUtils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        success : function(response){
            location.reload();
        },
        error : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.error(response.data);
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);



/*!
 * Timber Login Module
 */
timber.login = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
        },
        init : function() {
            formUtils.submit();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        handler: function(event) {
            formUtils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            formUtils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        success : function(response){
            toastr.success(response.data);
            location.reload();
        },
        error : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.error(response.data);
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);



/*!
 * Timber Register Module
 */
timber.register = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
        },
        init : function() {
            formUtils.submit();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        handler: function(event) {
            formUtils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            formUtils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        success : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.success(response.data);
        },
        error : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.error(response.data);
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);



/*!
 * Timber FPWD Module
 */
timber.fpwd = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
        },
        init : function() {
            formUtils.submit();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        handler: function(event) {
            formUtils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            formUtils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        success : function(response){
            toastr.success(response.data);
            location.reload();
        },
        error : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.error(response.data);
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);



/*!
 * Timber Dashboard Module
 */
timber.dashboard = (function (window, document, $) {
    'use strict';

    var Utils = {
        turnChartPart : 1,
        lineChartCallback: false,

        init : function() {

        },
        getChartData : function(part){
            return unique_visits.slice( (unique_visits.length) - (7 * part), (unique_visits.length) - (7 * (part-1)) );
        },
        initChart : function(){
            Utils.lineChartCallback = new Morris.Line({
                            element: 'statistics_chart',
                            data: Utils.getChartData(Utils.turnChartPart),
                            hideHover: true,
                            xkey: 'day',
                            resize: true,
                            ykeys: ['count'],
                            labels: [axis_labels['v']],
                            lineColors: ['#4899db'],
                            lineWidth: 2,
                            pointSize: 4,
                            gridLineColor: '#e0e0e0',
                            gridTextColor: '#1f1f1f',
                            xLabels: "day",
                            xLabelFormat: function (d) {
                                return date_labels[d.getMonth()] + ' ' + d.getDate();
                            }
                        });
        },
        turnChart : function(){
            $('a#statistics_chart_previous_period').on('click', function(event) {
                event.preventDefault();
                if(this == event.target){
                    if(Utils.turnChartPart == 1){
                        Utils.turnChartPart += 1;
                    }else if(Utils.turnChartPart == 2){
                        Utils.turnChartPart += 1;
                    }else if(Utils.turnChartPart == 3){
                        Utils.turnChartPart += 1;
                    }
                    Utils.lineChartCallback.setData(Utils.getChartData(Utils.turnChartPart));
                }
            });
            $('a#statistics_chart_next_period').on('click', function(event) {
                event.preventDefault();
                if(this == event.target){
                    if(Utils.turnChartPart == 2){
                        Utils.turnChartPart -= 1;
                    }else if(Utils.turnChartPart == 3){
                        Utils.turnChartPart -= 1;
                    }else if(Utils.turnChartPart == 4){
                        Utils.turnChartPart -= 1 ;
                    }
                    Utils.lineChartCallback.setData(Utils.getChartData(Utils.turnChartPart));
                }
            });
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);



/*!
 * Timber Profile Module
 */
timber.profile = (function (window, document, $) {

    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            verifyEmail : $("a#profile_verify_alert"),
        },
        init : function() {
            formUtils.submit();
            formUtils.passwordSwitch();
            formUtils.verifyEmail();
            if( gravatar_platform == 'native' ){
                formUtils.avatarUpload();
            }
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        handler: function(event) {
            formUtils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        avatarUpload : function() {
            var uploadedFiles = "";
            var lastUploadedFile = "";
            var uploaded_image_path = "";
            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'profile_avatar',
                    paramName: "profile_avatar", // The name that will be used to transfer the file
                    acceptedFiles: avatar_uploader_settings.acceptedfiles,
                    maxFiles: avatar_uploader_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: avatar_uploader_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            //$('input[name="profile_avatar"]').val(new_response.info.new_name);
                            uploadedFiles += response + "&&&&";
                            lastUploadedFile = new_response.info.new_name + "--||--" + new_response.info.name;
                            uploaded_image_path = new_response.info.path;
                            $('#uploader_files').val(uploadedFiles);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        if( uploaded_file_data.info.new_name + "--||--" + uploaded_file_data.info.name == lastUploadedFile ){
                            lastUploadedFile = "";
                            uploaded_image_path = $('img#profile_image').attr('data-default');
                        }
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                if(this == event.target){
                    if(lastUploadedFile != ''){
                        $("input[name='profile_avatar']").val(lastUploadedFile);
                        $('img#profile_image').attr('src', uploaded_image_path);
                    }else{
                        $("input[name='profile_avatar']").val('');
                        $('img#profile_image').attr('src', $('img#profile_image').attr('data-default'));
                    }
                }
            });
        },
        verifyEmail : function(){
            formUtils.el.verifyEmail.on("click", formUtils.verifyEmailHandler);
        },
        verifyEmailHandler : function(){
                if(event.target == this){
                        event.preventDefault();
                var _self = $(this);
                Pace.track(function(){
                    $.post(_self.attr('href'), { user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            }
        },
        passwordSwitch : function(){
            $('input[name="change_pwd"]').on('change', function(event) {
                event.preventDefault();
                if(this == event.target){
                    if($('input[name="change_pwd"]').is(':checked')){
                        $('input[name="user_old_pwd"]').removeAttr('disabled');
                        $('input[name="user_new_pwd"]').removeAttr('disabled');
                    }else{
                        $('input[name="user_old_pwd"]').attr('disabled', 'disabled');
                        $('input[name="user_new_pwd"]').attr('disabled', 'disabled');
                    }
                }
            });
        },
        data : function(){
            var inputs = {};
            formUtils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        success : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.success(response.data);
        },
        error : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.error(response.data);
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);




/*!
 * Timber Settings Module
 */
timber.settings = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            cform : $("form#c_form_action"),
            csubmitButt : $("form#c_form_action button[type='submit']"),
            aform : $("form#a_form_action"),
            asubmitButt : $("form#a_form_action button[type='submit']"),
            rform : $("form#r_form_action"),
            rsubmitButt : $("form#r_form_action button[type='submit']"),
            pform : $("form#p_form_action"),
            psubmitButt : $("form#p_form_action button[type='submit']"),
            tform : $("form#t_form_action"),
            tsubmitButt : $("form#t_form_action button[type='submit']"),
            bform : $("form#b_form_action"),
            bsubmitButt : $("form#b_form_action button[type='submit']"),
            cronRefresh : $("#cron_link_refresh"),
            forceBackup : $("#force_new_backup"),

            verifyTpl: $("[name='t_verify_email_tpl']"),
            fpwdTpl : $("[name='t_fpwd_tpl']"),
            loginTpl : $("[name='t_login_info_tpl']"),
            inviteTpls : $("[name='t_register_invite_tpl']"),
            newProjectTpl : $("[name='t_new_project_tpl']"),
            newProjectTaskTpl : $("[name='t_new_project_task_tpl']"),
            newProjectMilestoneTpl : $("[name='t_new_project_milestone_tpl']"),
            newProjectTicketTpl : $("[name='t_new_project_ticket_tpl']"),
            newProjectFileTpl : $("[name='t_new_project_files_tpl']"),
            newMessageTpl : $("[name='t_new_message_tpl']"),
            newQuotationTpl : $("[name='t_new_quotation_tpl']"),
            newPublicQuotationTpl : $("[name='t_new_public_quotation_tpl']"),
            newSubscriptionTpl : $("[name='t_new_subscription_tpl']"),
            newInvoiceTpl : $("[name='t_new_invoice_tpl']"),
            newEstimateTpl : $("[name='t_new_estimate_tpl']"),
        },
        init : function() {
            Utils.formSubmit();
            Utils.cronRefresh();
            Utils.forceBackup();
            Utils.logoUpload();
            Utils.taxRatesHandler();
            Utils.templates();
        },
        templates : function(){
            Utils.el.verifyTpl = Utils.el.verifyTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.fpwdTpl = Utils.el.fpwdTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });

            Utils.el.loginTpl = Utils.el.loginTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.inviteTpls = Utils.el.inviteTpls.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.newProjectTpl = Utils.el.newProjectTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.newProjectTaskTpl = Utils.el.newProjectTaskTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.newProjectMilestoneTpl = Utils.el.newProjectMilestoneTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.newProjectTicketTpl = Utils.el.newProjectTicketTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.newProjectFileTpl = Utils.el.newProjectFileTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.newMessageTpl = Utils.el.newMessageTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.newQuotationTpl = Utils.el.newQuotationTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.newPublicQuotationTpl = Utils.el.newPublicQuotationTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.newSubscriptionTpl = Utils.el.newSubscriptionTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.newInvoiceTpl = Utils.el.newInvoiceTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            Utils.el.newEstimateTpl = Utils.el.newEstimateTpl.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });

            $("[name='t_verify_email_tpl']").htmlcode($("[name='t_verify_email_tpl']").text());
            $("[name='t_fpwd_tpl']").htmlcode($("[name='t_fpwd_tpl']").text());
            $("[name='t_login_info_tpl']").htmlcode($("[name='t_login_info_tpl']").text());
            $("[name='t_register_invite_tpl']").htmlcode($("[name='t_register_invite_tpl']").text());
            $("[name='t_new_project_tpl']").htmlcode($("[name='t_new_project_tpl']").text());
            $("[name='t_new_project_task_tpl']").htmlcode($("[name='t_new_project_task_tpl']").text());
            $("[name='t_new_project_milestone_tpl']").htmlcode($("[name='t_new_project_milestone_tpl']").text());
            $("[name='t_new_project_ticket_tpl']").htmlcode($("[name='t_new_project_ticket_tpl']").text());
            $("[name='t_new_project_files_tpl']").htmlcode($("[name='t_new_project_files_tpl']").text());
            $("[name='t_new_message_tpl']").htmlcode($("[name='t_new_message_tpl']").text());
            $("[name='t_new_quotation_tpl']").htmlcode($("[name='t_new_quotation_tpl']").text());
            $("[name='t_new_public_quotation_tpl']").htmlcode($("[name='t_new_public_quotation_tpl']").text());
            $("[name='t_new_subscription_tpl']").htmlcode($("[name='t_new_subscription_tpl']").text());
            $("[name='t_new_invoice_tpl']").htmlcode($("[name='t_new_invoice_tpl']").text());
            $("[name='t_new_estimate_tpl']").htmlcode($("[name='t_new_estimate_tpl']").text());
        },
        taxRatesHandler : function(){
            $('#c13').on("click", '.tax_dump', function(event){
                event.preventDefault();
                if( this == event.target ){
                    $(this).closest('tr').remove();
                }
            });
            $('#c13').on("click", '.tax_add', function(event){
                event.preventDefault();
                if( this == event.target ){
                    var new_tax_container = $('tr.tax_container').clone();
                    new_tax_container.removeClass('tax_container');
                    new_tax_container.insertBefore('tr.add_tax_container').show();
                }
            });
        },
        logoUpload : function() {
            var uploadedFiles = "";
            var lastUploadedFile = "";
            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'site_logo',
                    paramName: "c_site_logo", // The name that will be used to transfer the file
                    acceptedFiles: avatar_uploader_settings.acceptedfiles,
                    maxFiles: avatar_uploader_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: avatar_uploader_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            //$('input[name="c_site_logo"]').val(new_response.info.new_name);
                            uploadedFiles += response + "&&&&";
                            lastUploadedFile = new_response.info.new_name + "--||--" + new_response.info.name
                            $('#uploader_files').val(uploadedFiles);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        if( uploaded_file_data.info.new_name + "--||--" + uploaded_file_data.info.name == lastUploadedFile ){
                            lastUploadedFile = "";
                        }
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                if(this == event.target){
                    if(lastUploadedFile != ''){
                        $("#logo_select").text(lastUploadedFile.split("--||--")[1]);
                        $("input[name='c_site_logo']").val(lastUploadedFile);
                    }else{
                        $("#logo_select").text($("#logo_select").attr('data-text'));
                        $("input[name='c_site_logo']").val('');
                    }
                }
            });
        },
        cronRefresh : function() {
            Utils.el.cronRefresh.on("click", Utils.cronRefreshHandler);
        },
        cronRefreshHandler : function(event) {
            event.preventDefault();
            if(this == event.target){
                Utils.el.cronRefresh.addClass('loading');
                Pace.track(function(){
                    $.post(Utils.el.cronRefresh.attr('data-action'), {action : 'cron_refresh', user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                $('input[name="r_crons_link"]').val(response.data);
                                Utils.el.cronRefresh.removeClass('loading');
                            }else{
                                $('input[name="r_crons_link"]').val(response.data);
                                Utils.el.cronRefresh.removeClass('loading');
                            }
                        }
                    }, 'json');
                });
            }
        },
        forceBackup : function() {
            Utils.el.forceBackup.on("click", Utils.forceBackupHandler);
        },
        forceBackupHandler : function(event) {
            event.preventDefault();
            if(this == event.target){
                Utils.el.forceBackup.addClass('loading');
                Pace.track(function(){
                    $.post(Utils.el.forceBackup.attr('href'), {action : 'force_backup', user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                Utils.el.forceBackup.closest('td').append(response.data);
                                Utils.el.forceBackup.remove();
                            }else{
                                Utils.el.forceBackup.closest('td').append(response.data);
                                Utils.el.forceBackup.remove();
                            }
                        }
                    }, 'json');
                });
            }
        },
        formSubmit : function(){
            Utils.el.cform.on("submit", Utils.cformHandler);
            Utils.el.aform.on("submit", Utils.aformHandler);
            Utils.el.rform.on("submit", Utils.rformHandler);
            Utils.el.pform.on("submit", Utils.pformHandler);
            Utils.el.tform.on("submit", Utils.tformHandler);
            Utils.el.bform.on("submit", Utils.bformHandler);
        },
        cformHandler: function(event) {
            Utils.el.cform.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.cform.attr('action'), Utils.cformData(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            Utils.formSuccess(response);
                        }else{
                            Utils.formError(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        aformHandler: function(event) {
            Utils.el.aform.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.aform.attr('action'), Utils.aformData(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            Utils.formSuccess(response);
                        }else{
                            Utils.formError(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        rformHandler: function(event) {
            Utils.el.rform.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.rform.attr('action'), Utils.rformData(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            Utils.formSuccess(response);
                        }else{
                            Utils.formError(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        pformHandler: function(event) {
            Utils.el.pform.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.pform.attr('action'), Utils.pformData(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            Utils.formSuccess(response);
                        }else{
                            Utils.formError(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        tformHandler: function(event) {
            Utils.el.tform.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.tform.attr('action'), Utils.tformData(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            Utils.formSuccess(response);
                        }else{
                            Utils.formError(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        bformHandler: function(event) {
            Utils.el.bform.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.bform.attr('action'), Utils.bformData(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            Utils.formSuccess(response);
                        }else{
                            Utils.formError(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        cformData : function(){
            var inputs = {};
            Utils.el.cform.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            inputs['c_site_tax_rates'] = {
                'title' : $('input[name="c_site_tax_rates[title][]"]').map(function(){return $(this).val();}).get(),
                'value' : $('input[name="c_site_tax_rates[value][]"]').map(function(){return $(this).val();}).get(),
            };
            return inputs;
        },
        aformData : function(){
            var inputs = {};
            Utils.el.aform.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        rformData : function(){
            var inputs = {};
            Utils.el.rform.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
                inputs['r_staff_permissions'] = $('[name="r_staff_permissions"]').val();
                inputs['r_client_permissions'] = $('[name="r_client_permissions"]').val();
                if( inputs['r_staff_permissions'] === null ){ inputs['r_staff_permissions'] = ''; }else{ inputs['r_staff_permissions'] = inputs['r_staff_permissions'].join(','); }
                if( inputs['r_client_permissions'] === null ){ inputs['r_client_permissions'] = ''; }else{ inputs['r_client_permissions'] = inputs['r_client_permissions'].join(','); }
            });
            return inputs;
        },
        pformData : function(){
            var inputs = {};
            Utils.el.pform.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        tformData : function(){
            var inputs = {};
            Utils.el.tform.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            inputs['t_verify_email_tpl'] = Utils.el.verifyTpl.htmlcode();
            inputs['t_fpwd_tpl'] = Utils.el.fpwdTpl.htmlcode();
            inputs['t_login_info_tpl'] = Utils.el.loginTpl.htmlcode();
            inputs['t_register_invite_tpl'] = Utils.el.inviteTpls.htmlcode();
            inputs['t_new_project_tpl'] = Utils.el.newProjectTpl.htmlcode();
            inputs['t_new_project_task_tpl'] = Utils.el.newProjectTaskTpl.htmlcode();
            inputs['t_new_project_milestone_tpl'] = Utils.el.newProjectMilestoneTpl.htmlcode();
            inputs['t_new_project_ticket_tpl'] = Utils.el.newProjectTicketTpl.htmlcode();
            inputs['t_new_project_files_tpl'] = Utils.el.newProjectFileTpl.htmlcode();
            inputs['t_new_message_tpl'] = Utils.el.newMessageTpl.htmlcode();
            inputs['t_new_quotation_tpl'] = Utils.el.newQuotationTpl.htmlcode();
            inputs['t_new_public_quotation_tpl'] = Utils.el.newPublicQuotationTpl.htmlcode();
            inputs['t_new_subscription_tpl'] = Utils.el.newSubscriptionTpl.htmlcode();
            inputs['t_new_invoice_tpl'] = Utils.el.newInvoiceTpl.htmlcode();
            inputs['t_new_estimate_tpl'] = Utils.el.newEstimateTpl.htmlcode();

            return inputs;
        },
        bformData : function(){
            var inputs = {};
            Utils.el.bform.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        formSuccess : function(response){
            Utils.el.cform.removeClass('loading');
            Utils.el.aform.removeClass('loading');
            Utils.el.rform.removeClass('loading');
            Utils.el.pform.removeClass('loading');
            Utils.el.tform.removeClass('loading');
            Utils.el.bform.removeClass('loading');
            toastr.success(response.data);
        },
        formError : function(response){
            Utils.el.cform.removeClass('loading');
            Utils.el.aform.removeClass('loading');
            Utils.el.rform.removeClass('loading');
            Utils.el.pform.removeClass('loading');
            Utils.el.tform.removeClass('loading');
            Utils.el.bform.removeClass('loading');
            toastr.error(response.data);
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);



/*!
 * Timber Themes Module
 */
timber.themes = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            activate : $("button.activate_theme"),
            save : $("button.save_theme"),
            delete : $("button.delete_theme"),
            themebox : $('div.wide.column'),
        },
        init : function() {
            Utils.el.activate.on("click", Utils.activateThemeHandler);
            Utils.el.save.on("click", Utils.saveThemeHandler);
            Utils.el.delete.on("click", Utils.deleteThemeHandler);

            Utils.el.themebox.bind('mouseenter', function(){
                $(this).find('div.theme_settings').transition('scale');
            }).bind('mouseleave', function(){
                $(this).find('div.theme_settings').transition('scale');
            });
        },
        activateThemeHandler : function(){
            if(event.target == this){
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    var _selfParent = _self.closest('div.wide.column');
                    var skin = _selfParent.find('[name="theme_skin"]').val();
                    var font = _selfParent.find('[name="theme_font"]').val();
                    _self.addClass('loading');
                    Pace.track(function(){
                        $.post(_self.attr('data-url'), { user_nonce : current_user_nonce, theme : _self.attr('data-theme'), skin : skin, font : font }, function( response, textStatus, jqXHR ){
                            if( jqXHR.status == 200 && textStatus == 'success' ) {
                                if( 'success' == response.status ){
                                    _selfParent.find('button.delete_theme').remove();
                                    toastr.success(response.data);
                                    location.reload();
                                }else{
                                    _self.removeClass('loading');
                                    toastr.error(response.data);
                                }
                            }
                        }, 'json');
                    });
                });
            }
        },
        saveThemeHandler : function(){
            if(event.target == this){
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.closest('div.wide.column');
                var skin = _selfParent.find('[name="theme_skin"]').val();
                var font = _selfParent.find('[name="theme_font"]').val();
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('data-url'), { user_nonce : current_user_nonce, theme : _self.attr('data-theme'), skin : skin, font : font }, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                location.reload();
                            }else{
                                _self.removeClass('loading');
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            }
        },
        deleteThemeHandler : function(){
            if(event.target == this){
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    var _selfParent = _self.closest('div.wide.column');
                    _self.addClass('loading');
                    Pace.track(function(){
                        $.post(_self.attr('data-url'), { user_nonce : current_user_nonce, theme : _self.attr('data-theme') }, function( response, textStatus, jqXHR ){
                            if( jqXHR.status == 200 && textStatus == 'success' ) {
                                if( 'success' == response.status ){
                                    _selfParent.remove();
                                    toastr.success(response.data);
                                }else{
                                    _self.removeClass('loading');
                                    toastr.error(response.data);
                                }
                            }
                        }, 'json');
                    });
                });
            }
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);



/*!
 * Timber Plugins Module
 */
timber.plugins = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            activate : $("button.activate_plugin"),
            deactivate : $("button.deactivate_plugin"),
            delete : $("button.delete_plugin"),
        },
        init : function() {
            Utils.el.activate.on("click", Utils.activatePluginHandler);
            Utils.el.deactivate.on("click", Utils.deactivatePluginHandler);
            Utils.el.delete.on("click", Utils.deletePluginHandler);
        },
        activatePluginHandler : function(){
            if(event.target == this){
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    var _selfParent = _self.closest('div.column');
                    _self.addClass('loading');
                    Pace.track(function(){
                        $.post(_self.attr('data-url'), { user_nonce : current_user_nonce, plugin : _self.attr('data-plugin') }, function( response, textStatus, jqXHR ){
                            if( jqXHR.status == 200 && textStatus == 'success' ) {
                                if( 'success' == response.status ){
                                    _self.hide();
                                    _self.removeClass('loading');
                                    _selfParent.find('button.deactivate_plugin').show();
                                    toastr.success(response.data);
                                }else{
                                    _self.find('span').hide();
                                    _self.removeClass('disabled');
                                    toastr.error(response.data);
                                }
                            }
                        }, 'json');
                    });
                });
            }
        },
        deactivatePluginHandler : function(){
            if(event.target == this){
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    var _selfParent = _self.closest('div.column');
                    _self.addClass('loading');
                    Pace.track(function(){
                        $.post(_self.attr('data-url'), { user_nonce : current_user_nonce, plugin : _self.attr('data-plugin') }, function( response, textStatus, jqXHR ){
                            if( jqXHR.status == 200 && textStatus == 'success' ) {
                                if( 'success' == response.status ){
                                    _self.hide();
                                    _self.removeClass('loading');
                                    _selfParent.find('button.activate_plugin').show();
                                    toastr.success(response.data);
                                }else{
                                    _self.find('span').hide();
                                    _self.removeClass('disabled');
                                    toastr.error(response.data);
                                }
                            }
                        }, 'json');
                    });
                });
            }
        },
        deletePluginHandler : function(){
            if(event.target == this){
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    var _selfParent = _self.closest('div.column');
                    _self.addClass('loading');
                    Pace.track(function(){
                        $.post(_self.attr('data-url'), { user_nonce : current_user_nonce, plugin : _self.attr('data-plugin') }, function( response, textStatus, jqXHR ){
                            if( jqXHR.status == 200 && textStatus == 'success' ) {
                                if( 'success' == response.status ){
                                    _selfParent.remove();
                                    toastr.success(response.data);
                                }else{
                                    _self.removeClass('loading');
                                    toastr.error(response.data);
                                }
                            }
                        }, 'json');
                    });
                });
            }
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Members List Module
 */
timber.membersList = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            records_start : 0,
        },
        init : function() {
            $('#members_main_container').on('click', 'a.delete_member', Utils.deleteMemberHandler);
            Utils.el.records_start = parseInt(members_records_start);
            Utils.pagination();
        },
        deleteMemberHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                Pace.track(function(){
                    $.post(_self.attr('href'), {member_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                _self.closest('div.wide.column').fadeOut( "slow", function() {
                                    _self.closest('div.wide.column').remove();
                                });
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        pagination : function() {
            $(window).scroll(function(){
                if ( ($(window).scrollTop() == $(document).height() - $(window).height()) && (parseInt(members_total_records) > parseInt(Utils.el.records_start) ) ){
                    Utils.renderMembers();
                    Utils.incrementData();
                }
            });
        },
        renderMembers : function(){
            Pace.track(function(){
                $.post(members_render_socket, { records_start : Utils.el.records_start, user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        $("#members_main_container").append(response);
                        $("div.wide.column").fadeIn('500');
                        //$('aside.sidebar').height($('.main-content-wrapper').height());
                        $('[data-toggle="popup"]').popup();
                    }
                });
            });
        },
        incrementData : function(){
            Utils.el.records_start += 20;
        }
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Members Add Module
 */
timber.membersAdd = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
        },
        init : function() {
            formUtils.submit();
            if( gravatar_platform == 'native' ){
                formUtils.avatarUpload();
            }
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        handler: function(event) {
            formUtils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        avatarUpload : function() {
            var uploadedFiles = "";
            var lastUploadedFile = "";
            var uploaded_image_path = "";
            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'profile_avatar',
                    paramName: "profile_avatar", // The name that will be used to transfer the file
                    acceptedFiles: avatar_uploader_settings.acceptedfiles,
                    maxFiles: avatar_uploader_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: avatar_uploader_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            //$('input[name="profile_avatar"]').val(new_response.info.new_name);
                            uploadedFiles += response + "&&&&";
                            lastUploadedFile = new_response.info.new_name + "--||--" + new_response.info.name;
                            uploaded_image_path = new_response.info.path;
                            $('#uploader_files').val(uploadedFiles);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        if( uploaded_file_data.info.new_name + "--||--" + uploaded_file_data.info.name == lastUploadedFile ){
                            lastUploadedFile = "";
                            uploaded_image_path = $('img#profile_image').attr('data-default');
                        }
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                if(this == event.target){
                    if(lastUploadedFile != ''){
                        $("input[name='profile_avatar']").val(lastUploadedFile);
                        $('img#profile_image').attr('src', uploaded_image_path);
                    }else{
                        $("input[name='profile_avatar']").val('');
                        $('img#profile_image').attr('src', $('img#profile_image').attr('data-default'));
                    }
                }
            });
        },
        data : function(){
            var inputs = {};
            formUtils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        success : function(response){
            toastr.success(response.data);
            window.location = response.next_link;
        },
        error : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.error(response.data);
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Members Edit Module
 */
timber.membersEdit = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
        },
        init : function() {
            formUtils.submit();
            if( gravatar_platform == 'native' ){
                formUtils.avatarUpload();
            }
            formUtils.passwordSwitch();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        handler: function(event) {
            formUtils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        passwordSwitch : function(){
            $('input[name="change_pwd"]').on('change', function(event) {
                event.preventDefault();
                if(this == event.target){
                    if($('input[name="change_pwd"]').is(':checked')){
                        $('input[name="user_new_pwd"]').removeAttr('disabled');
                    }else{
                        $('input[name="user_new_pwd"]').attr('disabled', 'disabled');
                    }
                }
            });
        },
        avatarUpload : function() {
            var uploadedFiles = "";
            var lastUploadedFile = "";
            var uploaded_image_path = "";
            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'profile_avatar',
                    paramName: "profile_avatar", // The name that will be used to transfer the file
                    acceptedFiles: avatar_uploader_settings.acceptedfiles,
                    maxFiles: avatar_uploader_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: avatar_uploader_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            //$('input[name="profile_avatar"]').val(new_response.info.new_name);
                            uploadedFiles += response + "&&&&";
                            lastUploadedFile = new_response.info.new_name + "--||--" + new_response.info.name;
                            uploaded_image_path = new_response.info.path;
                            $('#uploader_files').val(uploadedFiles);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        if( uploaded_file_data.info.new_name + "--||--" + uploaded_file_data.info.name == lastUploadedFile ){
                            lastUploadedFile = "";
                            uploaded_image_path = $('img#profile_image').attr('data-default');
                        }
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                if(this == event.target){
                    if(lastUploadedFile != ''){
                        $("input[name='profile_avatar']").val(lastUploadedFile);
                        $('img#profile_image').attr('src', uploaded_image_path);
                    }else{
                        $("input[name='profile_avatar']").val('');
                        $('img#profile_image').attr('src', $('img#profile_image').attr('data-default'));
                    }
                }
            });
        },
        data : function(){
            var inputs = {};
            formUtils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        success : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.success(response.data);
        },
        error : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.error(response.data);
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Members View Module
 */
timber.membersView = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {

        },
        init : function() {

        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);



/*!
 * Timber Items List Module
 */
timber.itemsList = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            deleteItem : $('a.delete_item'),
        },
        init : function() {
            Utils.el.deleteItem.on('click', Utils.deleteItemHandler);
        },
        deleteItemHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                Pace.track(function(){
                    $.post(_self.attr('href'), {item_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Items Add Module
 */
timber.itemsAdd = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
        },
        init : function() {
            formUtils.submit();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        handler: function(event) {
            formUtils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            toastr.success(response.data);
                            window.location = response.next_link;
                        }else{
                            formUtils.el.form.removeClass('loading');
                            toastr.error(response.data);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            formUtils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Items Edit Module
 */
timber.itemsEdit = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
        },
        init : function() {
            formUtils.submit();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        handler: function(event) {
            formUtils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            formUtils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        success : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.success(response.data);
        },
        error : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.error(response.data);
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Messages List Module
 */
timber.messagesList = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            doneMsg : $("a.done_message"),
            favoriteMsg : $("a.favorite_message"),
            unfavoriteMsg : $("a.unfavorite_message"),
            trashMsg : $("a.trash_message"),
            untrashMsg : $("a.untrash_message"),
            deleteMsg : $("a.delete_message")
        },
        init : function() {
            formUtils.el.doneMsg.on("click", formUtils.doneHandler);
            formUtils.el.favoriteMsg.on("click", formUtils.favoriteHandler);
            formUtils.el.unfavoriteMsg.on("click", formUtils.unfavoriteHandler);
            formUtils.el.trashMsg.on("click", formUtils.trashHandler);
            formUtils.el.untrashMsg.on("click", formUtils.untrashHandler);
            formUtils.el.deleteMsg.on("click", formUtils.deleteHandler);
        },
        doneHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {user_nonce : current_user_nonce, action: 'done', message_id: _self.attr('data-id') } , function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                _self.closest('tr').removeClass('warning');
                                _self.remove();
                                toastr.success(response.data);
                            }else{
                                _self.removeClass('loading');
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        favoriteHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {user_nonce : current_user_nonce, action: 'favorite', message_id: _self.attr('data-id') } , function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                _self.remove();
                                toastr.success(response.data);
                            }else{
                                _self.removeClass('loading');
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        unfavoriteHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {user_nonce : current_user_nonce, action: 'unfavorite', message_id: _self.attr('data-id') } , function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                                toastr.success(response.data);
                            }else{
                                _self.removeClass('loading');
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        trashHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {user_nonce : current_user_nonce, action: 'trash', message_id: _self.attr('data-id') } , function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                                toastr.success(response.data);
                            }else{
                                _self.removeClass('loading');
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        untrashHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {user_nonce : current_user_nonce, action: 'untrash', message_id: _self.attr('data-id') } , function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                                toastr.success(response.data);
                            }else{
                                _self.removeClass('loading');
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        deleteHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {user_nonce : current_user_nonce, action: 'delete', message_id: _self.attr('data-id') } , function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                                toastr.success(response.data);
                            }else{
                                _self.removeClass('loading');
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Messages Add Module
 */
timber.messagesAdd = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            msgContent : $("[name='msg_content']")
        },
        init : function() {
            formUtils.submit();
            formUtils.el.msgContent = formUtils.el.msgContent.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            formUtils.attachmentsUpload();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        attachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="msg_attachments"]').val(uploadedFilesField);
            });
        },
        handler: function(event) {
            formUtils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            formUtils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            inputs['msg_content'] = formUtils.el.msgContent.htmlcode();
            return inputs;
        },
        success : function(response){
            toastr.success(response.data);
            window.location = response.next_link;
        },
        error : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.error(response.data);
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Messages View Module
 */
timber.messagesView = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            msgContent : $("[name='msg_content']")
        },
        init : function() {
            formUtils.submit();
            formUtils.el.msgContent = formUtils.el.msgContent.wysibb({
                buttons: "bold,italic,underline,|,img,link,|,bullist,numlist,|,code,quote"
            });
            formUtils.attachmentsUpload();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        attachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="msg_attachments"]').val(uploadedFilesField);
            });
        },
        handler: function(event) {
            formUtils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            formUtils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            inputs['msg_content'] = formUtils.el.msgContent.htmlcode();
            return inputs;
        },
        success : function(response){
            toastr.success(response.data);
            location.reload();
        },
        error : function(response){
            formUtils.el.form.removeClass('loading');
            toastr.error(response.data);
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Invoices List Module
 */
timber.invoicesList = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            deleteInvoice : $('a.delete_invoice'),
            checkoutInvoice: $('a.checkout'),
            uncheckoutInvoice : $('a.un_checkout'),
        },
        init : function() {
            Utils.el.deleteInvoice.on('click', Utils.deleteInvoiceHandler);
            Utils.el.checkoutInvoice.on('click', Utils.checkoutInvoiceHandler);
            Utils.el.uncheckoutInvoice.on('click', Utils.uncheckoutInvoiceHandler);
            Utils.checkoutMessage();
        },
        deleteInvoiceHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {invoice_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                            }else{
                                toastr.error(response.data);
                                _self.removeClass('loading');
                            }
                        }
                    }, 'json');
                });
            });
        },
        checkoutInvoiceHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {invoice_id : _self.attr('data-id'), action : 'checkout', user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                location.reload();
                            }else{
                                toastr.error(response.data);
                                _self.removeClass('loading');
                            }
                        }
                    }, 'json');
                });
            });
        },
        uncheckoutInvoiceHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {invoice_id : _self.attr('data-id'), action : 'un_checkout', user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                location.reload();
                            }else{
                                toastr.error(response.data);
                                _self.removeClass('loading');
                            }
                        }
                    }, 'json');
                });
            });
        },
        checkoutMessage : function(){
            if( checkout_return_message != '' ){
                toastr.info(checkout_return_message);
            }
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Invoices Add Module
 */
timber.invoicesAdd = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            notes : $("[name='inv_terms[notes]']"),
            datePicker : $(".datepicker")
        },
        init : function() {
            formUtils.submit();

            formUtils.el.notes = formUtils.el.notes.wysibb({
                buttons: "bold,italic,underline,|,link,|,bullist,numlist"
            });

            formUtils.attachmentsUpload();
            formUtils.el.datePicker.datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d',
            });
            formUtils.invoiceCalc();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        attachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="inv_attachments"]').val(uploadedFilesField);
            });
        },
        handler: function(event) {
            $('div.metabox-body').addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){

                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            var i = 1;
            formUtils.el.form.serializeArray().map(function(item, index) {
                var name = item.name;
                name = name.replace("[]", "[" + i + "]");
                inputs[name] = item.value;
                i += 1;
            });
            inputs['inv_terms[notes]'] = formUtils.el.notes.htmlcode();
            return inputs;
        },
        success : function(response){
            toastr.success(response.data);
            window.location = response.next_link;
        },
        error : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.error(response.data);
        },
        invoiceCalc : function(){

            // Delete Item Event Handler
            $('div.metabox-body').on('click', '.delete_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    _self.closest('div.inline.fields').fadeOut( "slow", function() {
                        _self.closest('div.inline.fields').remove();

                        var _SubTotal = 0;
                        $('[name="inv_terms[items][item_sub_total][]"]').each(function(index, element){
                            var sub_total = new Decimal($(this).val() || 0);
                            _SubTotal = sub_total.plus(_SubTotal);
                        });
                        $('[name="inv_terms[overall][sub_total]"]').val(_SubTotal);
                        $('[name="inv_terms[overall][sub_total]"]').trigger('change');
                    });
                });
            });

            // Add Item Event Handler
            $('div.metabox-body').on('click', '.add_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _items_proto_clone = $('div.items_proto').clone();
                _items_proto_clone.removeClass('items_proto');
                _self.before(_items_proto_clone);
                _items_proto_clone.find('[data-toggle="popup"]').popup();
                _items_proto_clone.find('.dropdown').dropdown();
                _items_proto_clone.show();
            });

            // Item Select Event Handler
            $('div.metabox-body').on('change', '[name="inv_terms[items][item_select][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');
                var _selfItem = _self.val().split(' -- ');

                _selfParent.find('[name="inv_terms[items][item_title][]"]').val(_selfItem[1]);
                _selfParent.find('[name="inv_terms[items][item_description][]"]').val(_selfItem[2]);
                _selfParent.find('[name="inv_terms[items][item_quantity][]"]').val(1);
                _selfParent.find('[name="inv_terms[items][item_unit_price][]"]').val(_selfItem[3]);
                _selfParent.find('[name="inv_terms[items][item_sub_total][]"]').val(_selfItem[3]);

                var _SubTotal = 0;
                $('[name="inv_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="inv_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="inv_terms[overall][sub_total]"]').trigger('change');
            });

            // Item Change Event Handler
            $('div.metabox-body').on('change', '[name="inv_terms[items][item_quantity][]"], [name="inv_terms[items][item_unit_price][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');

                var _selfQuantity = _selfParent.find('[name="inv_terms[items][item_quantity][]"]').val();
                var _selfUnitPrice = _selfParent.find('[name="inv_terms[items][item_unit_price][]"]').val();
                _selfQuantity = new Decimal(_selfQuantity);
                _selfUnitPrice = new Decimal(_selfUnitPrice);
                var _Result = _selfQuantity.times(_selfUnitPrice);
                _selfParent.find('[name="inv_terms[items][item_sub_total][]"]').val(_Result.valueOf());

                var _SubTotal = 0;
                $('[name="inv_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="inv_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="inv_terms[overall][sub_total]"]').trigger('change');
            });

            // Tax Select Event Handler
            $('div.metabox-body').on('change', '[name="inv_terms[overall][tax_select]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                $('[name="inv_terms[overall][tax_value]"]').val(_self.val());
            });

            // Overall Calculations Event Handler
            $('div.metabox-body').on('change', '[name="inv_terms[overall][sub_total]"], [name="inv_terms[overall][tax_type]"], [name="inv_terms[overall][tax_select]"], [name="inv_terms[overall][tax_value]"], [name="inv_terms[overall][discount_type]"], [name="inv_terms[overall][discount_value]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _subTotal = new Decimal($('[name="inv_terms[overall][sub_total]"]').val() || 0);
                var _taxType = $('[name="inv_terms[overall][tax_type]"]').val();
                var _taxValue = new Decimal($('[name="inv_terms[overall][tax_value]"]').val() || 0);
                var _discountType = $('[name="inv_terms[overall][discount_type]"]').val();
                var _discountValue = new Decimal($('[name="inv_terms[overall][discount_value]"]').val() || 0);

                if( _discountType == 'percent' ){
                    _subTotal = _subTotal.minus(_subTotal.times(_discountValue.valueOf()).dividedBy(100).valueOf());
                }else if( _discountType == 'flat' ){
                    _subTotal = _subTotal.minus(_discountValue.valueOf());
                }

                if( _taxType == 'percent' ){
                    _subTotal = _subTotal.plus(_subTotal.times(_taxValue.valueOf()).dividedBy(100).valueOf());
                }else if( _taxType == 'flat' ){
                    _subTotal = _subTotal.plus(_taxValue.valueOf());
                }

                var _totalValue = _subTotal.valueOf();
                $('[name="inv_terms[overall][total_value]"]').val(_totalValue);
            });
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Invoices Edit Module
 */
timber.invoicesEdit = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            notes : $("[name='inv_terms[notes]']"),
            datePicker : $(".datepicker")
        },
        init : function() {
            formUtils.submit();
            formUtils.el.notes = formUtils.el.notes.wysibb({
                buttons: "bold,italic,underline,|,link,|,bullist,numlist"
            });
            $("[name='inv_terms[notes]']").htmlcode($("[name='inv_terms[notes]']").text());
            formUtils.attachmentsDump();
            formUtils.attachmentsUpload();
            formUtils.el.datePicker.datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d',
            });
            formUtils.invoiceCalc();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        attachmentsDump : function(){
            $('i.delete_file').on('click', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    var oldAttachments = $('input[name="inv_old_attachments"]');
                    var oldAttachmentsArr = oldAttachments.val().split(',');
                    var index = oldAttachmentsArr.indexOf(_self.attr('data-id'));

                    if (index > -1) {
                        oldAttachmentsArr.splice(index, 1);
                    }

                    oldAttachments.val(oldAttachmentsArr.join(','));
                    _self.closest('a').remove();
                });
            });
        },
        attachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="inv_attachments"]').val(uploadedFilesField);
            });
        },
        handler: function(event) {
            $('div.metabox-body').addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){

                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            var i = 1;
            formUtils.el.form.serializeArray().map(function(item, index) {
                var name = item.name;
                name = name.replace("[]", "[" + i + "]");
                inputs[name] = item.value;
                i += 1;
            });
            inputs['inv_terms[notes]'] = formUtils.el.notes.htmlcode();
            return inputs;
        },
        success : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.success(response.data);
        },
        error : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.error(response.data);
        },
        invoiceCalc : function(){

            // Delete Item Event Handler
            $('div.metabox-body').on('click', '.delete_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    _self.closest('div.inline.fields').fadeOut( "slow", function() {
                        _self.closest('div.inline.fields').remove();

                        var _SubTotal = 0;
                        $('[name="inv_terms[items][item_sub_total][]"]').each(function(index, element){
                            var sub_total = new Decimal($(this).val() || 0);
                            _SubTotal = sub_total.plus(_SubTotal);
                        });
                        $('[name="inv_terms[overall][sub_total]"]').val(_SubTotal);
                        $('[name="inv_terms[overall][sub_total]"]').trigger('change');
                    });
                });
            });

            // Add Item Event Handler
            $('div.metabox-body').on('click', '.add_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _items_proto_clone = $('div.items_proto').clone();
                _items_proto_clone.removeClass('items_proto');
                _self.before(_items_proto_clone);
                _items_proto_clone.find('[data-toggle="popup"]').popup();
                _items_proto_clone.find('.dropdown').dropdown();
                _items_proto_clone.show();
            });

            // Item Select Event Handler
            $('div.metabox-body').on('change', '[name="inv_terms[items][item_select][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');
                var _selfItem = _self.val().split(' -- ');

                _selfParent.find('[name="inv_terms[items][item_title][]"]').val(_selfItem[1]);
                _selfParent.find('[name="inv_terms[items][item_description][]"]').val(_selfItem[2]);
                _selfParent.find('[name="inv_terms[items][item_quantity][]"]').val(1);
                _selfParent.find('[name="inv_terms[items][item_unit_price][]"]').val(_selfItem[3]);
                _selfParent.find('[name="inv_terms[items][item_sub_total][]"]').val(_selfItem[3]);

                var _SubTotal = 0;
                $('[name="inv_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="inv_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="inv_terms[overall][sub_total]"]').trigger('change');
            });

            // Item Change Event Handler
            $('div.metabox-body').on('change', '[name="inv_terms[items][item_quantity][]"], [name="inv_terms[items][item_unit_price][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');

                var _selfQuantity = _selfParent.find('[name="inv_terms[items][item_quantity][]"]').val();
                var _selfUnitPrice = _selfParent.find('[name="inv_terms[items][item_unit_price][]"]').val();
                _selfQuantity = new Decimal(_selfQuantity);
                _selfUnitPrice = new Decimal(_selfUnitPrice);
                var _Result = _selfQuantity.times(_selfUnitPrice);
                _selfParent.find('[name="inv_terms[items][item_sub_total][]"]').val(_Result.valueOf());

                var _SubTotal = 0;
                $('[name="inv_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="inv_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="inv_terms[overall][sub_total]"]').trigger('change');
            });

            // Tax Select Event Handler
            $('div.metabox-body').on('change', '[name="inv_terms[overall][tax_select]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                $('[name="inv_terms[overall][tax_value]"]').val(_self.val());
            });

            // Overall Calculations Event Handler
            $('div.metabox-body').on('change', '[name="inv_terms[overall][sub_total]"], [name="inv_terms[overall][tax_type]"], [name="inv_terms[overall][tax_select]"], [name="inv_terms[overall][tax_value]"], [name="inv_terms[overall][discount_type]"], [name="inv_terms[overall][discount_value]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _subTotal = new Decimal($('[name="inv_terms[overall][sub_total]"]').val() || 0);
                var _taxType = $('[name="inv_terms[overall][tax_type]"]').val();
                var _taxValue = new Decimal($('[name="inv_terms[overall][tax_value]"]').val() || 0);
                var _discountType = $('[name="inv_terms[overall][discount_type]"]').val();
                var _discountValue = new Decimal($('[name="inv_terms[overall][discount_value]"]').val() || 0);

                if( _discountType == 'percent' ){
                    _subTotal = _subTotal.minus(_subTotal.times(_discountValue.valueOf()).dividedBy(100).valueOf());
                }else if( _discountType == 'flat' ){
                    _subTotal = _subTotal.minus(_discountValue.valueOf());
                }

                if( _taxType == 'percent' ){
                    _subTotal = _subTotal.plus(_subTotal.times(_taxValue.valueOf()).dividedBy(100).valueOf());
                }else if( _taxType == 'flat' ){
                    _subTotal = _subTotal.plus(_taxValue.valueOf());
                }

                var _totalValue = _subTotal.valueOf();
                $('[name="inv_terms[overall][total_value]"]').val(_totalValue);
            });
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Invoices View Module
 */
timber.invoicesView = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {

        },
        init : function() {

        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Invoices Checkout Module
 */
timber.invoicesCheckout = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            uncheckoutInvoice : $('a.un_checkout'),
        },
        init : function() {
            Utils.el.uncheckoutInvoice.on('click', Utils.uncheckoutInvoiceHandler);
            Utils.checkoutMessage();
        },
        uncheckoutInvoiceHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {invoice_id : _self.attr('data-id'), action : 'un_checkout', user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                location.reload();
                            }else{
                                toastr.error(response.data);
                                _self.removeClass('loading');
                            }
                        }
                    }, 'json');
                });
            });
        },
        checkoutMessage : function(){
            if( checkout_return_message != '' ){
                toastr.info(checkout_return_message);
            }
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Estimates List Module
 */
timber.estimatesList = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            deleteEstimate : $('a.delete_estimate'),
        },
        init : function() {
            Utils.el.deleteEstimate.on('click', Utils.deleteEstimateHandler);
        },
        deleteEstimateHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                Pace.track(function(){
                    $.post(_self.attr('href'), {estimate_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Estimates Add Module
 */
timber.estimatesAdd = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            notes : $("[name='est_terms[notes]']"),
            datePicker : $(".datepicker")
        },
        init : function() {
            formUtils.submit();
            formUtils.el.notes = formUtils.el.notes.wysibb({
                buttons: "bold,italic,underline,|,link,|,bullist,numlist"
            });
            formUtils.attachmentsUpload();
            formUtils.el.datePicker.datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d',
            });
            formUtils.estimateCalc();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        attachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="est_attachments"]').val(uploadedFilesField);
            });
        },
        handler: function(event) {
            $('div.metabox-body').addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){

                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            var i = 1;
            formUtils.el.form.serializeArray().map(function(item, index) {
                var name = item.name;
                name = name.replace("[]", "[" + i + "]");
                inputs[name] = item.value;
                i += 1;
            });
            inputs['est_terms[notes]'] = formUtils.el.notes.htmlcode();
            return inputs;
        },
        success : function(response){
            toastr.success(response.data);
            window.location = response.next_link;
        },
        error : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.error(response.data);
        },
        estimateCalc : function(){

            // Delete Item Event Handler
            $('div.metabox-body').on('click', '.delete_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    _self.closest('div.inline.fields').fadeOut( "slow", function() {
                        _self.closest('div.inline.fields').remove();

                        var _SubTotal = 0;
                        $('[name="est_terms[items][item_sub_total][]"]').each(function(index, element){
                            var sub_total = new Decimal($(this).val() || 0);
                            _SubTotal = sub_total.plus(_SubTotal);
                        });
                        $('[name="est_terms[overall][sub_total]"]').val(_SubTotal);
                        $('[name="est_terms[overall][sub_total]"]').trigger('change');
                    });
                });
            });

            // Add Item Event Handler
            $('div.metabox-body').on('click', '.add_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _items_proto_clone = $('div.items_proto').clone();
                _items_proto_clone.removeClass('items_proto');
                _self.before(_items_proto_clone);
                _items_proto_clone.find('[data-toggle="popup"]').popup();
                _items_proto_clone.find('.dropdown').dropdown();
                _items_proto_clone.show();
            });

            // Item Select Event Handler
            $('div.metabox-body').on('change', '[name="est_terms[items][item_select][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');
                var _selfItem = _self.val().split(' -- ');

                _selfParent.find('[name="est_terms[items][item_title][]"]').val(_selfItem[1]);
                _selfParent.find('[name="est_terms[items][item_description][]"]').val(_selfItem[2]);
                _selfParent.find('[name="est_terms[items][item_quantity][]"]').val(1);
                _selfParent.find('[name="est_terms[items][item_unit_price][]"]').val(_selfItem[3]);
                _selfParent.find('[name="est_terms[items][item_sub_total][]"]').val(_selfItem[3]);

                var _SubTotal = 0;
                $('[name="est_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="est_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="est_terms[overall][sub_total]"]').trigger('change');
            });

            // Item Change Event Handler
            $('div.metabox-body').on('change', '[name="est_terms[items][item_quantity][]"], [name="est_terms[items][item_unit_price][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');

                var _selfQuantity = _selfParent.find('[name="est_terms[items][item_quantity][]"]').val();
                var _selfUnitPrice = _selfParent.find('[name="est_terms[items][item_unit_price][]"]').val();
                _selfQuantity = new Decimal(_selfQuantity);
                _selfUnitPrice = new Decimal(_selfUnitPrice);
                var _Result = _selfQuantity.times(_selfUnitPrice);
                _selfParent.find('[name="est_terms[items][item_sub_total][]"]').val(_Result.valueOf());

                var _SubTotal = 0;
                $('[name="est_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="est_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="est_terms[overall][sub_total]"]').trigger('change');
            });

            // Tax Select Event Handler
            $('div.metabox-body').on('change', '[name="est_terms[overall][tax_select]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                $('[name="est_terms[overall][tax_value]"]').val(_self.val());
            });

            // Overall Calculations Event Handler
            $('div.metabox-body').on('change', '[name="est_terms[overall][sub_total]"], [name="est_terms[overall][tax_type]"], [name="est_terms[overall][tax_select]"], [name="est_terms[overall][tax_value]"], [name="est_terms[overall][discount_type]"], [name="est_terms[overall][discount_value]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _subTotal = new Decimal($('[name="est_terms[overall][sub_total]"]').val() || 0);
                var _taxType = $('[name="est_terms[overall][tax_type]"]').val();
                var _taxValue = new Decimal($('[name="est_terms[overall][tax_value]"]').val() || 0);
                var _discountType = $('[name="est_terms[overall][discount_type]"]').val();
                var _discountValue = new Decimal($('[name="est_terms[overall][discount_value]"]').val() || 0);

                if( _discountType == 'percent' ){
                    _subTotal = _subTotal.minus(_subTotal.times(_discountValue.valueOf()).dividedBy(100).valueOf());
                }else if( _discountType == 'flat' ){
                    _subTotal = _subTotal.minus(_discountValue.valueOf());
                }

                if( _taxType == 'percent' ){
                    _subTotal = _subTotal.plus(_subTotal.times(_taxValue.valueOf()).dividedBy(100).valueOf());
                }else if( _taxType == 'flat' ){
                    _subTotal = _subTotal.plus(_taxValue.valueOf());
                }

                var _totalValue = _subTotal.valueOf();
                $('[name="est_terms[overall][total_value]"]').val(_totalValue);
            });
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Estimates Edit Module
 */
timber.estimatesEdit = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            notes : $("[name='est_terms[notes]']"),
            datePicker : $(".datepicker")
        },
        init : function() {
            formUtils.submit();
            formUtils.el.notes = formUtils.el.notes.wysibb({
                buttons: "bold,italic,underline,|,link,|,bullist,numlist"
            });
            $("[name='est_terms[notes]']").htmlcode($("[name='est_terms[notes]']").text());
            formUtils.attachmentsDump();
            formUtils.attachmentsUpload();
            formUtils.el.datePicker.datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d',
            });
            formUtils.estimateCalc();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        attachmentsDump : function(){
            $('i.delete_file').on('click', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    var oldAttachments = $('input[name="est_old_attachments"]');
                    var oldAttachmentsArr = oldAttachments.val().split(',');
                    var index = oldAttachmentsArr.indexOf(_self.attr('data-id'));

                    if (index > -1) {
                        oldAttachmentsArr.splice(index, 1);
                    }

                    oldAttachments.val(oldAttachmentsArr.join(','));
                    _self.closest('a').remove();
                });
            });
        },
        attachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="est_attachments"]').val(uploadedFilesField);
            });
        },
        handler: function(event) {
            $('div.metabox-body').addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){

                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            var i = 1;
            formUtils.el.form.serializeArray().map(function(item, index) {
                var name = item.name;
                name = name.replace("[]", "[" + i + "]");
                inputs[name] = item.value;
                i += 1;
            });
            inputs['est_terms[notes]'] = formUtils.el.notes.htmlcode();
            return inputs;
        },
        success : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.success(response.data);
        },
        error : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.error(response.data);
        },
        estimateCalc : function(){

            // Delete Item Event Handler
            $('div.metabox-body').on('click', '.delete_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    _self.closest('div.inline.fields').fadeOut( "slow", function() {
                        _self.closest('div.inline.fields').remove();

                        var _SubTotal = 0;
                        $('[name="est_terms[items][item_sub_total][]"]').each(function(index, element){
                            var sub_total = new Decimal($(this).val() || 0);
                            _SubTotal = sub_total.plus(_SubTotal);
                        });
                        $('[name="est_terms[overall][sub_total]"]').val(_SubTotal);
                        $('[name="est_terms[overall][sub_total]"]').trigger('change');
                    });
                });
            });

            // Add Item Event Handler
            $('div.metabox-body').on('click', '.add_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _items_proto_clone = $('div.items_proto').clone();
                _items_proto_clone.removeClass('items_proto');
                _self.before(_items_proto_clone);
                _items_proto_clone.find('[data-toggle="popup"]').popup();
                _items_proto_clone.find('.dropdown').dropdown();
                _items_proto_clone.show();
            });

            // Item Select Event Handler
            $('div.metabox-body').on('change', '[name="est_terms[items][item_select][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');
                var _selfItem = _self.val().split(' -- ');

                _selfParent.find('[name="est_terms[items][item_title][]"]').val(_selfItem[1]);
                _selfParent.find('[name="est_terms[items][item_description][]"]').val(_selfItem[2]);
                _selfParent.find('[name="est_terms[items][item_quantity][]"]').val(1);
                _selfParent.find('[name="est_terms[items][item_unit_price][]"]').val(_selfItem[3]);
                _selfParent.find('[name="est_terms[items][item_sub_total][]"]').val(_selfItem[3]);

                var _SubTotal = 0;
                $('[name="est_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="est_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="est_terms[overall][sub_total]"]').trigger('change');
            });

            // Item Change Event Handler
            $('div.metabox-body').on('change', '[name="est_terms[items][item_quantity][]"], [name="est_terms[items][item_unit_price][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');

                var _selfQuantity = _selfParent.find('[name="est_terms[items][item_quantity][]"]').val();
                var _selfUnitPrice = _selfParent.find('[name="est_terms[items][item_unit_price][]"]').val();
                _selfQuantity = new Decimal(_selfQuantity);
                _selfUnitPrice = new Decimal(_selfUnitPrice);
                var _Result = _selfQuantity.times(_selfUnitPrice);
                _selfParent.find('[name="est_terms[items][item_sub_total][]"]').val(_Result.valueOf());

                var _SubTotal = 0;
                $('[name="est_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="est_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="est_terms[overall][sub_total]"]').trigger('change');
            });

            // Tax Select Event Handler
            $('div.metabox-body').on('change', '[name="est_terms[overall][tax_select]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                $('[name="est_terms[overall][tax_value]"]').val(_self.val());
            });

            // Overall Calculations Event Handler
            $('div.metabox-body').on('change', '[name="est_terms[overall][sub_total]"], [name="est_terms[overall][tax_type]"], [name="est_terms[overall][tax_select]"], [name="est_terms[overall][tax_value]"], [name="est_terms[overall][discount_type]"], [name="est_terms[overall][discount_value]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _subTotal = new Decimal($('[name="est_terms[overall][sub_total]"]').val() || 0);
                var _taxType = $('[name="est_terms[overall][tax_type]"]').val();
                var _taxValue = new Decimal($('[name="est_terms[overall][tax_value]"]').val() || 0);
                var _discountType = $('[name="est_terms[overall][discount_type]"]').val();
                var _discountValue = new Decimal($('[name="est_terms[overall][discount_value]"]').val() || 0);

                if( _discountType == 'percent' ){
                    _subTotal = _subTotal.minus(_subTotal.times(_discountValue.valueOf()).dividedBy(100).valueOf());
                }else if( _discountType == 'flat' ){
                    _subTotal = _subTotal.minus(_discountValue.valueOf());
                }

                if( _taxType == 'percent' ){
                    _subTotal = _subTotal.plus(_subTotal.times(_taxValue.valueOf()).dividedBy(100).valueOf());
                }else if( _taxType == 'flat' ){
                    _subTotal = _subTotal.plus(_taxValue.valueOf());
                }

                var _totalValue = _subTotal.valueOf();
                $('[name="est_terms[overall][total_value]"]').val(_totalValue);
            });
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Expenses List Module
 */
timber.expensesList = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            deleteExpense : $('a.delete_expense'),
        },
        init : function() {
            Utils.el.deleteExpense.on('click', Utils.deleteExpenseHandler);
        },
        deleteExpenseHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                Pace.track(function(){
                    $.post(_self.attr('href'), {expense_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Expenses Add Module
 */
timber.expensesAdd = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            datePicker : $(".datepicker")
        },
        init : function() {
            formUtils.submit();
            formUtils.attachmentsUpload();
            formUtils.el.datePicker.datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d',
            });
            formUtils.expenseCalc();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        attachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="exp_attachments"]').val(uploadedFilesField);
            });
        },
        handler: function(event) {
            $('div.metabox-body').addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){

                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            var i = 1;
            formUtils.el.form.serializeArray().map(function(item, index) {
                var name = item.name;
                name = name.replace("[]", "[" + i + "]");
                inputs[name] = item.value;
                i += 1;
            });
            return inputs;
        },
        success : function(response){
            toastr.success(response.data);
            window.location = response.next_link;
        },
        error : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.error(response.data);
        },
        expenseCalc : function(){

            // Tax Select Event Handler
            $('div.metabox-body').on('change', '[name="exp_terms[tax_select]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                $('[name="exp_terms[tax_value]"]').val(_self.val());
            });

            // Overall Calculations Event Handler
            $('div.metabox-body').on('change', '[name="exp_terms[sub_total]"], [name="exp_terms[tax_type]"], [name="exp_terms[tax_select]"], [name="exp_terms[tax_value]"], [name="exp_terms[discount_type]"], [name="exp_terms[discount_value]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _subTotal = new Decimal($('[name="exp_terms[sub_total]"]').val() || 0);
                var _taxType = $('[name="exp_terms[tax_type]"]').val();
                var _taxValue = new Decimal($('[name="exp_terms[tax_value]"]').val() || 0);
                var _discountType = $('[name="exp_terms[discount_type]"]').val();
                var _discountValue = new Decimal($('[name="exp_terms[discount_value]"]').val() || 0);

                if( _discountType == 'percent' ){
                    _subTotal = _subTotal.minus(_subTotal.times(_discountValue.valueOf()).dividedBy(100).valueOf());
                }else if( _discountType == 'flat' ){
                    _subTotal = _subTotal.minus(_discountValue.valueOf());
                }

                if( _taxType == 'percent' ){
                    _subTotal = _subTotal.plus(_subTotal.times(_taxValue.valueOf()).dividedBy(100).valueOf());
                }else if( _taxType == 'flat' ){
                    _subTotal = _subTotal.plus(_taxValue.valueOf());
                }

                var _totalValue = _subTotal.valueOf();
                $('[name="exp_terms[total_value]"]').val(_totalValue);
            });
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Expenses Edit Module
 */
timber.expensesEdit = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            datePicker : $(".datepicker")
        },
        init : function() {
            formUtils.submit();
            formUtils.attachmentsDump();
            formUtils.attachmentsUpload();
            formUtils.el.datePicker.datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d',
            });
            formUtils.expenseCalc();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        attachmentsDump : function(){
            $('i.delete_file').on('click', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    var oldAttachments = $('input[name="exp_old_attachments"]');
                    var oldAttachmentsArr = oldAttachments.val().split(',');
                    var index = oldAttachmentsArr.indexOf(_self.attr('data-id'));

                    if (index > -1) {
                        oldAttachmentsArr.splice(index, 1);
                    }

                    oldAttachments.val(oldAttachmentsArr.join(','));
                    _self.closest('a').remove();
                });
            });
        },
        attachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="exp_attachments"]').val(uploadedFilesField);
            });
        },
        handler: function(event) {
            $('div.metabox-body').addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            var i = 1;
            formUtils.el.form.serializeArray().map(function(item, index) {
                var name = item.name;
                name = name.replace("[]", "[" + i + "]");
                inputs[name] = item.value;
                i += 1;
            });
            return inputs;
        },
        success : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.success(response.data);
        },
        error : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.error(response.data);
        },
        expenseCalc : function(){

            // Tax Select Event Handler
            $('div.metabox-body').on('change', '[name="exp_terms[tax_select]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                $('[name="exp_terms[tax_value]"]').val(_self.val());
            });

            // Overall Calculations Event Handler
            $('div.metabox-body').on('change', '[name="exp_terms[sub_total]"], [name="exp_terms[tax_type]"], [name="exp_terms[tax_select]"], [name="exp_terms[tax_value]"], [name="exp_terms[discount_type]"], [name="exp_terms[discount_value]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _subTotal = new Decimal($('[name="exp_terms[sub_total]"]').val() || 0);
                var _taxType = $('[name="exp_terms[tax_type]"]').val();
                var _taxValue = new Decimal($('[name="exp_terms[tax_value]"]').val() || 0);
                var _discountType = $('[name="exp_terms[discount_type]"]').val();
                var _discountValue = new Decimal($('[name="exp_terms[discount_value]"]').val() || 0);

                if( _discountType == 'percent' ){
                    _subTotal = _subTotal.minus(_subTotal.times(_discountValue.valueOf()).dividedBy(100).valueOf());
                }else if( _discountType == 'flat' ){
                    _subTotal = _subTotal.minus(_discountValue.valueOf());
                }

                if( _taxType == 'percent' ){
                    _subTotal = _subTotal.plus(_subTotal.times(_taxValue.valueOf()).dividedBy(100).valueOf());
                }else if( _taxType == 'flat' ){
                    _subTotal = _subTotal.plus(_taxValue.valueOf());
                }

                var _totalValue = _subTotal.valueOf();
                $('[name="exp_terms[total_value]"]').val(_totalValue);
            });
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Subscriptions List Module
 */
timber.subscriptionsList = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            deleteSubscription : $('a.delete_subscription'),
        },
        init : function() {
            Utils.el.deleteSubscription.on('click', Utils.deleteSubscriptionHandler);
        },
        deleteSubscriptionHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                Pace.track(function(){
                    $.post(_self.attr('href'), {subscription_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Subscriptions Add Module
 */
timber.subscriptionsAdd = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            notes : $("[name='sub_terms[notes]']"),
            datePicker : $(".datepicker")
        },
        init : function() {
            formUtils.submit();
            formUtils.el.notes = formUtils.el.notes.wysibb({
                buttons: "bold,italic,underline,|,link,|,bullist,numlist"
            });
            formUtils.attachmentsUpload();
            formUtils.el.datePicker.datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d',
            });
            formUtils.subscriptionCalc();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        attachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="sub_attachments"]').val(uploadedFilesField);
            });
        },
        handler: function(event) {
            $('div.metabox-body').addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){

                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            var i = 1;
            formUtils.el.form.serializeArray().map(function(item, index) {
                var name = item.name;
                name = name.replace("[]", "[" + i + "]");
                inputs[name] = item.value;
                i += 1;
            });
            inputs['sub_terms[notes]'] = formUtils.el.notes.htmlcode();
            return inputs;
        },
        success : function(response){
            toastr.success(response.data);
            window.location = response.next_link;
        },
        error : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.error(response.data);
        },
        subscriptionCalc : function(){

            // Delete Item Event Handler
            $('div.metabox-body').on('click', '.delete_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    _self.closest('div.inline.fields').fadeOut( "slow", function() {
                        _self.closest('div.inline.fields').remove();

                        var _SubTotal = 0;
                        $('[name="sub_terms[items][item_sub_total][]"]').each(function(index, element){
                            var sub_total = new Decimal($(this).val() || 0);
                            _SubTotal = sub_total.plus(_SubTotal);
                        });
                        $('[name="sub_terms[overall][sub_total]"]').val(_SubTotal);
                        $('[name="sub_terms[overall][sub_total]"]').trigger('change');
                    });
                });
            });

            // Add Item Event Handler
            $('div.metabox-body').on('click', '.add_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _items_proto_clone = $('div.items_proto').clone();
                _items_proto_clone.removeClass('items_proto');
                _self.before(_items_proto_clone);
                _items_proto_clone.find('[data-toggle="popup"]').popup();
                _items_proto_clone.find('.dropdown').dropdown();
                _items_proto_clone.show();
            });

            // Item Select Event Handler
            $('div.metabox-body').on('change', '[name="sub_terms[items][item_select][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');
                var _selfItem = _self.val().split(' -- ');

                _selfParent.find('[name="sub_terms[items][item_title][]"]').val(_selfItem[1]);
                _selfParent.find('[name="sub_terms[items][item_description][]"]').val(_selfItem[2]);
                _selfParent.find('[name="sub_terms[items][item_quantity][]"]').val(1);
                _selfParent.find('[name="sub_terms[items][item_unit_price][]"]').val(_selfItem[3]);
                _selfParent.find('[name="sub_terms[items][item_sub_total][]"]').val(_selfItem[3]);

                var _SubTotal = 0;
                $('[name="sub_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="sub_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="sub_terms[overall][sub_total]"]').trigger('change');
            });

            // Item Change Event Handler
            $('div.metabox-body').on('change', '[name="sub_terms[items][item_quantity][]"], [name="sub_terms[items][item_unit_price][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');

                var _selfQuantity = _selfParent.find('[name="sub_terms[items][item_quantity][]"]').val();
                var _selfUnitPrice = _selfParent.find('[name="sub_terms[items][item_unit_price][]"]').val();
                _selfQuantity = new Decimal(_selfQuantity);
                _selfUnitPrice = new Decimal(_selfUnitPrice);
                var _Result = _selfQuantity.times(_selfUnitPrice);
                _selfParent.find('[name="sub_terms[items][item_sub_total][]"]').val(_Result.valueOf());

                var _SubTotal = 0;
                $('[name="sub_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="sub_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="sub_terms[overall][sub_total]"]').trigger('change');
            });

            // Tax Select Event Handler
            $('div.metabox-body').on('change', '[name="sub_terms[overall][tax_select]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                $('[name="sub_terms[overall][tax_value]"]').val(_self.val());
            });

            // Overall Calculations Event Handler
            $('div.metabox-body').on('change', '[name="sub_terms[overall][sub_total]"], [name="sub_terms[overall][tax_type]"], [name="sub_terms[overall][tax_select]"], [name="sub_terms[overall][tax_value]"], [name="sub_terms[overall][discount_type]"], [name="sub_terms[overall][discount_value]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _subTotal = new Decimal($('[name="sub_terms[overall][sub_total]"]').val() || 0);
                var _taxType = $('[name="sub_terms[overall][tax_type]"]').val();
                var _taxValue = new Decimal($('[name="sub_terms[overall][tax_value]"]').val() || 0);
                var _discountType = $('[name="sub_terms[overall][discount_type]"]').val();
                var _discountValue = new Decimal($('[name="sub_terms[overall][discount_value]"]').val() || 0);

                if( _discountType == 'percent' ){
                    _subTotal = _subTotal.minus(_subTotal.times(_discountValue.valueOf()).dividedBy(100).valueOf());
                }else if( _discountType == 'flat' ){
                    _subTotal = _subTotal.minus(_discountValue.valueOf());
                }

                if( _taxType == 'percent' ){
                    _subTotal = _subTotal.plus(_subTotal.times(_taxValue.valueOf()).dividedBy(100).valueOf());
                }else if( _taxType == 'flat' ){
                    _subTotal = _subTotal.plus(_taxValue.valueOf());
                }

                var _totalValue = _subTotal.valueOf();
                $('[name="sub_terms[overall][total_value]"]').val(_totalValue);
            });
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Subscriptions Edit Module
 */
timber.subscriptionsEdit = (function (window, document, $) {
    'use strict';

    var formUtils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            notes : $("[name='sub_terms[notes]']"),
            datePicker : $(".datepicker")
        },
        init : function() {
            formUtils.submit();
            formUtils.el.notes = formUtils.el.notes.wysibb({
                buttons: "bold,italic,underline,|,link,|,bullist,numlist"
            });
            $("[name='sub_terms[notes]']").htmlcode($("[name='sub_terms[notes]']").text());
            formUtils.attachmentsDump();
            formUtils.attachmentsUpload();
            formUtils.el.datePicker.datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d',
            });
            formUtils.subscriptionCalc();
        },
        submit : function(){
            formUtils.el.form.on("submit", formUtils.handler);
        },
        attachmentsDump : function(){
            $('i.delete_file').on('click', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    var oldAttachments = $('input[name="sub_old_attachments"]');
                    var oldAttachmentsArr = oldAttachments.val().split(',');
                    var index = oldAttachmentsArr.indexOf(_self.attr('data-id'));

                    if (index > -1) {
                        oldAttachmentsArr.splice(index, 1);
                    }

                    oldAttachments.val(oldAttachmentsArr.join(','));
                    _self.closest('a').remove();
                });
            });
        },
        attachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="sub_attachments"]').val(uploadedFilesField);
            });
        },
        handler: function(event) {
            $('div.metabox-body').addClass('loading');
            Pace.track(function(){
                $.post(formUtils.el.form.attr('action'), formUtils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){

                            formUtils.success(response);
                        }else{
                            formUtils.error(response);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            var i = 1;
            formUtils.el.form.serializeArray().map(function(item, index) {
                var name = item.name;
                name = name.replace("[]", "[" + i + "]");
                inputs[name] = item.value;
                i += 1;
            });
            inputs['sub_terms[notes]'] = formUtils.el.notes.htmlcode();
            return inputs;
        },
        success : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.success(response.data);
        },
        error : function(response){
            $('div.metabox-body').removeClass('loading');
            toastr.error(response.data);
        },
        subscriptionCalc : function(){

            // Delete Item Event Handler
            $('div.metabox-body').on('click', '.delete_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    _self.closest('div.inline.fields').fadeOut( "slow", function() {
                        _self.closest('div.inline.fields').remove();

                        var _SubTotal = 0;
                        $('[name="sub_terms[items][item_sub_total][]"]').each(function(index, element){
                            var sub_total = new Decimal($(this).val() || 0);
                            _SubTotal = sub_total.plus(_SubTotal);
                        });
                        $('[name="sub_terms[overall][sub_total]"]').val(_SubTotal);
                        $('[name="sub_terms[overall][sub_total]"]').trigger('change');
                    });
                });
            });

            // Add Item Event Handler
            $('div.metabox-body').on('click', '.add_item', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _items_proto_clone = $('div.items_proto').clone();
                _items_proto_clone.removeClass('items_proto');
                _self.before(_items_proto_clone);
                _items_proto_clone.find('[data-toggle="popup"]').popup();
                _items_proto_clone.find('.dropdown').dropdown();
                _items_proto_clone.show();
            });

            // Item Select Event Handler
            $('div.metabox-body').on('change', '[name="sub_terms[items][item_select][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');
                var _selfItem = _self.val().split(' -- ');

                _selfParent.find('[name="sub_terms[items][item_title][]"]').val(_selfItem[1]);
                _selfParent.find('[name="sub_terms[items][item_description][]"]').val(_selfItem[2]);
                _selfParent.find('[name="sub_terms[items][item_quantity][]"]').val(1);
                _selfParent.find('[name="sub_terms[items][item_unit_price][]"]').val(_selfItem[3]);
                _selfParent.find('[name="sub_terms[items][item_sub_total][]"]').val(_selfItem[3]);

                var _SubTotal = 0;
                $('[name="sub_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="sub_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="sub_terms[overall][sub_total]"]').trigger('change');
            });

            // Item Change Event Handler
            $('div.metabox-body').on('change', '[name="sub_terms[items][item_quantity][]"], [name="sub_terms[items][item_unit_price][]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _selfParent = _self.parents('div.inline.fields');

                var _selfQuantity = _selfParent.find('[name="sub_terms[items][item_quantity][]"]').val();
                var _selfUnitPrice = _selfParent.find('[name="sub_terms[items][item_unit_price][]"]').val();
                _selfQuantity = new Decimal(_selfQuantity);
                _selfUnitPrice = new Decimal(_selfUnitPrice);
                var _Result = _selfQuantity.times(_selfUnitPrice);
                _selfParent.find('[name="sub_terms[items][item_sub_total][]"]').val(_Result.valueOf());

                var _SubTotal = 0;
                $('[name="sub_terms[items][item_sub_total][]"]').each(function(index, element){
                    var sub_total = new Decimal($(this).val() || 0);
                    _SubTotal = sub_total.plus(_SubTotal);
                });
                $('[name="sub_terms[overall][sub_total]"]').val(_SubTotal);
                $('[name="sub_terms[overall][sub_total]"]').trigger('change');
            });

            // Tax Select Event Handler
            $('div.metabox-body').on('change', '[name="sub_terms[overall][tax_select]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                $('[name="sub_terms[overall][tax_value]"]').val(_self.val());
            });

            // Overall Calculations Event Handler
            $('div.metabox-body').on('change', '[name="sub_terms[overall][sub_total]"], [name="sub_terms[overall][tax_type]"], [name="sub_terms[overall][tax_select]"], [name="sub_terms[overall][tax_value]"], [name="sub_terms[overall][discount_type]"], [name="sub_terms[overall][discount_value]"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                var _subTotal = new Decimal($('[name="sub_terms[overall][sub_total]"]').val() || 0);
                var _taxType = $('[name="sub_terms[overall][tax_type]"]').val();
                var _taxValue = new Decimal($('[name="sub_terms[overall][tax_value]"]').val() || 0);
                var _discountType = $('[name="sub_terms[overall][discount_type]"]').val();
                var _discountValue = new Decimal($('[name="sub_terms[overall][discount_value]"]').val() || 0);

                if( _discountType == 'percent' ){
                    _subTotal = _subTotal.minus(_subTotal.times(_discountValue.valueOf()).dividedBy(100).valueOf());
                }else if( _discountType == 'flat' ){
                    _subTotal = _subTotal.minus(_discountValue.valueOf());
                }

                if( _taxType == 'percent' ){
                    _subTotal = _subTotal.plus(_subTotal.times(_taxValue.valueOf()).dividedBy(100).valueOf());
                }else if( _taxType == 'flat' ){
                    _subTotal = _subTotal.plus(_taxValue.valueOf());
                }

                var _totalValue = _subTotal.valueOf();
                $('[name="sub_terms[overall][total_value]"]').val(_totalValue);
            });
        },
    };
    return {
        init: formUtils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Subscriptions List Module
 */
timber.subscriptionsView = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            newInvoice : $('a.new_invoice'),
            deleteInvoice : $('a.delete_invoice'),
            checkoutInvoice: $('a.checkout'),
            uncheckoutInvoice : $('a.un_checkout'),
        },
        init : function() {
            Utils.el.deleteInvoice.on('click', Utils.deleteInvoiceHandler);
            Utils.el.checkoutInvoice.on('click', Utils.checkoutInvoiceHandler);
            Utils.el.uncheckoutInvoice.on('click', Utils.uncheckoutInvoiceHandler);
            Utils.el.newInvoice.on('click', Utils.newInvoiceHandler);
        },
        newInvoiceHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {sub_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                location.reload();
                            }else{
                                toastr.error(response.data);
                                _self.removeClass('loading');
                            }
                        }
                    }, 'json');
                });
            });
        },
        deleteInvoiceHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {invoice_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                            }else{
                                toastr.error(response.data);
                                _self.removeClass('loading');
                            }
                        }
                    }, 'json');
                });
            });
        },
        checkoutInvoiceHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {invoice_id : _self.attr('data-id'), action : 'checkout', user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                location.reload();
                            }else{
                                toastr.error(response.data);
                                _self.removeClass('loading');
                            }
                        }
                    }, 'json');
                });
            });
        },
        uncheckoutInvoiceHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {invoice_id : _self.attr('data-id'), action : 'un_checkout', user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                location.reload();
                            }else{
                                toastr.error(response.data);
                                _self.removeClass('loading');
                            }
                        }
                    }, 'json');
                });
            });
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Calendar Module
 */
timber.calendar = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            calendar : $("#calendar")
        },
        init : function() {
            Utils.buildCalendar();
        },
        buildCalendar : function(){
            Utils.el.calendar.fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                defaultDate: current_date,
                selectable: false,
                selectHelper: false,
                editable: false,
                eventLimit: true,
                eventMouseover: function(calEvent, jsEvent, view) {
                    if (view.name !== 'agendaDay') {
                        $(jsEvent.target).attr('title', calEvent.type + calEvent.title);
                    }
                },
                eventClick: function(calEvent, jsEvent, view)
                {
                    if( calEvent.iden == 'p' ){
                        $("#schedule_container").find('.header').text(calEvent.type + calEvent.title);
                        $("#schedule_container").find('.content').html(
                            "<strong>" + calEvent_title + ": </strong>" + calEvent.title + "<br/>" +
                            "<strong>" + calEvent_reference + ": </strong>" + calEvent.reference + "<br/>" +
                            "<strong>" + calEvent_ref_id + ": </strong>" + calEvent.ref_id + "<br/>" +
                            "<strong>" + calEvent_description + ": </strong>" + calEvent.description + "<br/>" +
                            "<strong>" + calEvent_version + ": </strong>" + calEvent.version + "<br/>" +
                            "<strong>" + calEvent_progress + ": </strong>" + calEvent.progress + "%" + "<br/>" +
                            "<strong>" + calEvent_budget + ": </strong>" + calEvent.budget + " " + calEvent.currency + "<br/>" +
                            "<strong>" + calEvent_status + ": </strong>" + calEvent.nice_status + "<br/>" +
                            "<strong>" + calEvent_owners + ": </strong>" + Utils.putifyMembers( calEvent.owners ) + "<br/>" +
                            "<strong>" + calEvent_staff + ": </strong>" + Utils.putifyMembers( calEvent.staff ) + "<br/>" +
                            "<strong>" + calEvent_clients + ": </strong>" + Utils.putifyMembers( calEvent.clients ) + "<br/>" +
                            "<strong>" + calEvent_start_at + ": </strong>" + calEvent.start_at + "<br/>" +
                            "<strong>" + calEvent_end_at + ": </strong>" + calEvent.end_at + "<br/>" +
                            "<strong>" + calEvent_created_at + ": </strong>" + calEvent.created_at
                        );
                    }else if( calEvent.iden == 't' ){
                        $("#schedule_container").find('.header').text(calEvent.type + calEvent.title);
                        $("#schedule_container").find('.content').html(
                            "<strong>" + calEvent_progress + ": </strong>" + calEvent.progress + "%" + "<br/>" +
                            "<strong>" + calEvent_mi_title + ": </strong>" + calEvent.mi_title + "<br/>" +
                            "<strong>" + calEvent_assign_to_name + ": </strong>" + calEvent.assign_to_name + "<br/>" +
                            "<strong>" + calEvent_assign_to_email + ": </strong>" + calEvent.assign_to_email + "<br/>" +
                            "<strong>" + calEvent_description + ": </strong>" + calEvent.description + "<br/>" +
                            "<strong>" + calEvent_status + ": </strong>" + calEvent.nice_status + "<br/>" +
                            "<strong>" + calEvent_priority + ": </strong>" + calEvent.nice_priority + "<br/>" +
                            "<strong>" + calEvent_start_at + ": </strong>" + calEvent.start_at + "<br/>" +
                            "<strong>" + calEvent_end_at + ": </strong>" + calEvent.end_at + "<br/>" +
                            "<strong>" + calEvent_created_at + ": </strong>" + calEvent.created_at
                        );
                    }
                    $("#schedule_container").modal('show');
                },
                eventSources: [
                    {
                        events: JSON.parse(projectsEvents),
                        color: projectsEventsColor,
                        textColor: projectsEventsTextColor
                    },
                    {
                        events: JSON.parse(tasksEvents),
                        color: tasksEventsColor,
                        textColor: tasksEventsTextColor
                    },
                ]
            });
        },
        putifyMembers : function(members){
            var members_data = "";
            members.forEach(function(member, index) {
                if( members_data == '' ){
                    members_data += member['full_name'] + " &lt;" + member['email'] + "&gt;";
                }else{
                    members_data += ", " + member['full_name'] + " &lt;" + member['email'] + "&gt;";
                }
            });
            return members_data;
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);



/*!
 * Timber Quotations List Module
 */
timber.quotationsList = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            deleteQuotation : $('a.delete_quotation'),
        },
        init : function() {
            Utils.el.deleteQuotation.on('click', Utils.deleteQuotationHandler);
        },
        deleteQuotationHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                Pace.track(function(){
                    $.post(_self.attr('href'), {quotation_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Quotations Add Module
 */
timber.quotationsAdd = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            newElement : $(".new_element"),
            saveElement : $("#element_save"),
            elementModal : $("#element_settings_container"),
            elements : new Array(),
            elementsPrev : $('span#elements_preview'),
            resetElements : $('[type="reset"]'),
        },
        init : function() {
            Utils.elements();
            Utils.form();
        },
        form : function(){
            Utils.el.resetElements.on('click', function(event) {
                event.preventDefault();
                Utils.el.elements = new Array();
                Utils.fetch();
            });
            Utils.el.form.on("submit", Utils.handler);
        },
        handler: function(event) {
            Utils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.form.attr('action'), Utils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            toastr.success(response.data);
                            window.location = response.next_link;
                        }else{
                            Utils.el.form.removeClass('loading');
                            toastr.error(response.data);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            var i = 1;
            Utils.el.form.serializeArray().map(function(item, index) {
                var name = item.name;
                name = name.replace("[]", "[" + i + "]");
                inputs[name] = item.value;
                i += 1;
            });
            inputs['quotation_terms'] = JSON.stringify(Utils.el.elements);
            return inputs;
        },
        elements : function(){

            Utils.el.saveElement.on('click', function(event) {
                event.preventDefault();
                var _self = $(this);
                var element = {
                    type : false,
                    required : false,
                    label : false,
                    name : false,
                    data : {
                        placeholder : false,
                        items : false
                    }
                };

                element.type = _self.parents('#element_settings_container').attr('data-element');

                if( element.type == 'text_elem' ){

                    element.label = $('[name="text_element_label"]').val();
                    element.name = $('[name="text_element_label"]').val();
                    element.required = $('[name="text_element_required"]').val();
                    element.data.placeholder = $('[name="text_element_placeholder"]').val();

                    $('[name="text_element_label"]').val('');
                    $('[name="text_element_placeholder"]').val('');

                }else if( element.type == 'para_elem' ){

                    element.label = $('[name="para_element_label"]').val();
                    element.name = $('[name="para_element_label"]').val();
                    element.required = $('[name="para_element_required"]').val();
                    element.data.placeholder = $('[name="para_element_placeholder"]').val();

                    $('[name="para_element_label"]').val('');
                    $('[name="para_element_placeholder"]').val('');

                }else if( element.type == 'chek_elem' ){

                    element.label = $('[name="chek_element_label"]').val();
                    element.name = $('[name="chek_element_label"]').val();

                    $('[name="chek_element_label"]').val('');

                }else if( element.type == 'mult_elem' ){

                    element.label = $('[name="mult_element_label"]').val();
                    element.name = $('[name="mult_element_label"]').val();
                    element.data.items = $('[name="mult_element_items"]').val().split(',');
                    element.required = $('[name="mult_element_required"]').val();

                    $('[name="mult_element_label"]').val('');
                    $('[name="mult_element_items"]').val('');

                }else if( element.type == 'drop_elem' ){

                    element.label = $('[name="drop_element_label"]').val();
                    element.name = $('[name="drop_element_label"]').val();
                    element.data.items = $('[name="drop_element_items"]').val().split(',');
                    element.required = $('[name="drop_element_required"]').val();

                    $('[name="drop_element_label"]').val('');
                    $('[name="drop_element_items"]').val('');

                }else if( element.type == 'date_elem' ){

                    element.label = $('[name="date_element_label"]').val();
                    element.name = $('[name="date_element_label"]').val();
                    element.required = $('[name="date_element_required"]').val();
                    element.data.placeholder = $('[name="date_element_placeholder"]').val();

                    $('[name="date_element_label"]').val('');
                    $('[name="date_element_placeholder"]').val('');
                }

                Utils.el.elements.push(element);
                Utils.fetch();
            });

            Utils.el.newElement.on('click', function(event) {
                event.preventDefault();
                var _self = $(this);

                if( _self.attr('data-id') == 'sect_elem' ){

                    Utils.el.elements.push({
                        type : "sect_elem",
                        required : false,
                        label : false,
                        name : false,
                        data : {
                            placeholder : false,
                            items : false
                        }
                    });

                    Utils.fetch();

                }else if( _self.attr('data-id') == 'text_elem' ){

                    Utils.el.elementModal.find('.header').text(_self.text());
                    Utils.el.elementModal.find('span').hide();
                    Utils.el.elementModal.find('span#text_element').show();
                    Utils.el.elementModal.attr('data-element', 'text_elem');
                    Utils.el.elementModal.modal('show');

                }else if( _self.attr('data-id') == 'para_elem' ){

                    Utils.el.elementModal.find('.header').text(_self.text());
                    Utils.el.elementModal.find('span').hide();
                    Utils.el.elementModal.find('span#para_element').show();
                    Utils.el.elementModal.attr('data-element', 'para_elem');
                    Utils.el.elementModal.modal('show');

                }else if( _self.attr('data-id') == 'chek_elem' ){

                    Utils.el.elementModal.find('.header').text(_self.text());
                    Utils.el.elementModal.find('span').hide();
                    Utils.el.elementModal.find('span#chek_element').show();
                    Utils.el.elementModal.attr('data-element', 'chek_elem');
                    Utils.el.elementModal.modal('show');

                }else if( _self.attr('data-id') == 'mult_elem' ){

                    Utils.el.elementModal.find('.header').text(_self.text());
                    Utils.el.elementModal.find('span').hide();
                    Utils.el.elementModal.find('span#mult_element').show();
                    Utils.el.elementModal.attr('data-element', 'mult_elem');
                    Utils.el.elementModal.modal('show');

                }else if( _self.attr('data-id') == 'drop_elem' ){

                    Utils.el.elementModal.find('.header').text(_self.text());
                    Utils.el.elementModal.find('span').hide();
                    Utils.el.elementModal.find('span#drop_element').show();
                    Utils.el.elementModal.attr('data-element', 'drop_elem');
                    Utils.el.elementModal.modal('show');

                }else if( _self.attr('data-id') == 'date_elem' ){

                    Utils.el.elementModal.find('.header').text(_self.text());
                    Utils.el.elementModal.find('span').hide();
                    Utils.el.elementModal.find('span#date_element').show();
                    Utils.el.elementModal.attr('data-element', 'date_elem');
                    Utils.el.elementModal.modal('show');

                }
            });

        },

        fetch : function(){

            if( Utils.el.elements.lenght <= 0 ){
                return true;
            }

            Utils.el.elementsPrev.empty();

            var _HTMLElem = '';

            Utils.el.elements.forEach(function(element, index){


                if( element.type == 'text_elem' ){

                    if( element.required == '1' ){
                        _HTMLElem += '<div class="field" data-index="' + index + '" data-element="' + element + '">';
                    }else if( element.required == '2' ){
                        _HTMLElem += '<div class="field required" data-index="' + index + '" data-element="' + element + '">';
                    }
                    _HTMLElem += '<label>' + element.label + '</label>';
                    _HTMLElem += '<input type="text" name="' + element.name + '" placeholder="' + element.data.placeholder + '"></div>';

                }else if( element.type == 'para_elem' ){

                    if( element.required == '1' ){
                        _HTMLElem += '<div class="field" data-index="' + index + '" data-element="' + element + '">';
                    }else if( element.required == '2' ){
                        _HTMLElem += '<div class="field required" data-index="' + index + '" data-element="' + element + '">';
                    }
                    _HTMLElem += '<label>' + element.label + '</label>';
                    _HTMLElem += '<textarea name="' + element.name + '" placeholder="' + element.data.placeholder + '"></textarea></div>';

                }else if( element.type == 'chek_elem' ){

                    _HTMLElem += '<div class="inline field" data-index="' + index + '" data-element="' + element + '"><div class="ui checkbox"><input type="checkbox" name="' + element.name + '" tabindex="0" class="hidden"><label>' + element.label + '</label></div></div>';

                }else if( element.type == 'mult_elem' ){

                    if( element.required == '1' ){
                        _HTMLElem += '<div class="field" data-index="' + index + '" data-element="' + element + '">';
                    }else if( element.required == '2' ){
                        _HTMLElem += '<div class="field required" data-index="' + index + '" data-element="' + element + '">';
                    }
                    _HTMLElem += '<label>' + element.label + '</label>';
                    _HTMLElem += '<select multiple="" name="' + element.name + '" class="ui dropdown">';

                    element.data.items.forEach(function(item, index){
                        _HTMLElem += '<option value="' + item + '">' + item + '</option>';
                    });

                    _HTMLElem += '</select>';
                    _HTMLElem += '</div>';

                }else if( element.type == 'drop_elem' ){

                    if( element.required == '1' ){
                        _HTMLElem += '<div class="field" data-index="' + index + '" data-element="' + element + '">';
                    }else if( element.required == '2' ){
                        _HTMLElem += '<div class="field required" data-index="' + index + '" data-element="' + element + '">';
                    }
                    _HTMLElem += '<label>' + element.label + '</label>';
                    _HTMLElem += '<select name="' + element.name + '" class="ui dropdown">';

                    element.data.items.forEach(function(item, index){
                        _HTMLElem += '<option value="' + item + '">' + item + '</option>';
                    });

                    _HTMLElem += '</select>';
                    _HTMLElem += '</div>';

                }else if( element.type == 'date_elem' ){


                    if( element.required == '1' ){
                        _HTMLElem += '<div class="field" data-index="' + index + '" data-element="' + element + '">';
                    }else if( element.required == '2' ){
                        _HTMLElem += '<div class="field required" data-index="' + index + '" data-element="' + element + '">';
                    }
                    _HTMLElem += '<label>' + element.label + '</label>';
                    _HTMLElem += '<input type="text" class="datepicker" name="' + element.name + '" placeholder="' + element.data.placeholder + '"></div>';

                }else if( element.type == 'sect_elem' ){

                    _HTMLElem += '<div class="hr-line-dashed"></div>';

                }

            });

            Utils.el.elementsPrev.append(_HTMLElem);
            $('.ui.checkbox').checkbox();
            $('.dropdown').dropdown();
            $(".datepicker").datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d',
            });
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Quotations View Module
 */
timber.quotationsView = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            selectTo : $('select[name="send_to"]'),
            viewSubm : $('a.view_submission'),
            deleteSubm : $('a.delete_submission'),
            submList : $('#quotations_submissions'),
        },
        init : function() {
            Utils.submit();
            Utils.viewSubmit();
            Utils.deleteSubmit();
        },
        submit : function(){
            Utils.el.form.on("submit", Utils.handler);
            Utils.el.selectTo.on('change', function(event) {
                event.preventDefault();
                $('#email_field').hide();
                $('#client_field').hide();
                $('#' + Utils.el.selectTo.val()).show();
            });
        },
        handler: function(event) {
            Utils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.form.attr('action'), Utils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            Utils.el.form.removeClass('loading');
                            toastr.success(response.data);
                        }else{
                            Utils.el.form.removeClass('loading');
                            toastr.error(response.data);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            Utils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },
        viewSubmit : function(){
            Utils.el.viewSubm.on('click', function(event) {
                event.preventDefault();
                var _self = $(this);
                Utils.el.submList.find('div.metabox').hide();
                Utils.el.submList.find('[data-id="' + _self.attr('data-id') + '"]').show();
            });
        },
        deleteSubmit : function(){
            Utils.el.deleteSubm.on('click', Utils.deleteSubmissionHandler);
        },
        deleteSubmissionHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                Pace.track(function(){
                    $.post(_self.attr('href'), {action: 'delete_submit', submit_id : _self.attr('data-id'), quotation_id: _self.attr('data-qu') , user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                _self.closest('tr').fadeOut( "slow", function() {
                                    _self.closest('tr').remove();
                                });
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Quotations Private Submit Module
 */
timber.quotationsSubmit = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
        },
        init : function() {
            Utils.submit();
            $(".datepicker").datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d',
            });
        },
        submit : function(){
            Utils.el.form.on("submit", Utils.handler);
        },
        handler: function(event) {
            Utils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.form.attr('action'), Utils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            Utils.el.form.removeClass('loading');
                            toastr.success(response.data);
                            setTimeout(function(){ window.close(); }, 4000);

                        }else{
                            Utils.el.form.removeClass('loading');
                            toastr.error(response.data);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            Utils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });

            $('.pubquo_muli').each(function(index, el) {
                var _self = $(this);
                var _selfSelec = _self.find('select');

                 inputs[_selfSelec.attr('name')] = _selfSelec.val();
                 if( _selfSelec.val() === null ){ inputs[_selfSelec.attr('name')] = ''; }else{ inputs[_selfSelec.attr('name')] = _selfSelec.val().join(','); }
            });

            $('input[type="checkbox"]').each(function(index, el) {
                var _self = $(this);

                if( _self.is(':checked') ){
                    inputs[_self.attr('name')] = 'Yes';
                }else{
                    inputs[_self.attr('name')] = 'No';
                }
            });

            return inputs;
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Quotations Public Submit Module
 */
timber.quotationsPubSubmit = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
        },
        init : function() {
            Utils.submit();
            $(".datepicker").datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d',
            });
        },
        submit : function(){
            Utils.el.form.on("submit", Utils.handler);
        },
        handler: function(event) {
            Utils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.form.attr('action'), Utils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            Utils.el.form.removeClass('loading');
                            toastr.success(response.data);
                        }else{
                            Utils.el.form.removeClass('loading');
                            toastr.error(response.data);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            Utils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });

            $('.pubquo_muli').each(function(index, el) {
                var _self = $(this);
                var _selfSelec = _self.find('select');

                 inputs[_selfSelec.attr('name')] = _selfSelec.val();
                 if( _selfSelec.val() === null ){ inputs[_selfSelec.attr('name')] = ''; }else{ inputs[_selfSelec.attr('name')] = _selfSelec.val().join(','); }
            });

            $('input[type="checkbox"]').each(function(index, el) {
                var _self = $(this);

                if( _self.is(':checked') ){
                    inputs[_self.attr('name')] = 'Yes';
                }else{
                    inputs[_self.attr('name')] = 'No';
                }
            });

            return inputs;
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Projects List Module
 */
timber.projectsList = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            records_start : 0,
        },
        init : function() {
            $('#projects_main_container').on('click', 'a.delete_project', Utils.deleteProjectHandler);
            Utils.el.records_start = parseInt(projects_records_start);
            Utils.pagination();
        },
        deleteProjectHandler : function(event) {
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                Pace.track(function(){
                    $.post(_self.attr('href'), {project_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                _self.closest('div.wide.column').fadeOut( "slow", function() {
                                    _self.closest('div.wide.column').remove();
                                });
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        pagination : function() {
            $(window).scroll(function(){
                if ( ($(window).scrollTop() == $(document).height() - $(window).height()) && (parseInt(projects_total_records) > parseInt(Utils.el.records_start) ) ){
                    Utils.renderProjects();
                    Utils.incrementData();
                }
            });
        },
        renderProjects : function(){
            Pace.track(function(){
                $.post(projects_render_socket, { records_start : Utils.el.records_start, user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        $("#projects_main_container").append(response);
                        $("div.wide.column").fadeIn('500');
                        //$('aside.sidebar').height($('.main-content-wrapper').height());
                        $('[data-toggle="popup"]').popup();
                    }
                });
            });
        },
        incrementData : function(){
            Utils.el.records_start += 20;
        }
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Projects List Module
 */
timber.projectsStrictList = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {},
        init : function() {}
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Projects Add Module
 */
timber.projectsAdd = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            datePicker : $(".datepicker"),
        },
        init : function() {
            Utils.submit();
            Utils.formElements();
        },
        submit : function(){
            Utils.el.form.on("submit", Utils.handler);
        },
        handler: function(event) {
            Utils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.form.attr('action'), Utils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            toastr.success(response.data);
                            window.location = response.next_link;
                        }else{
                            Utils.el.form.removeClass('loading');
                            toastr.error(response.data);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            Utils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });

            inputs['project_staff'] = $('[name="project_staff"]').val();
            inputs['project_clients'] = $('[name="project_clients"]').val();
            if( inputs['project_staff'] === null ){ inputs['project_staff'] = ''; }else{ inputs['project_staff'] = inputs['project_staff'].join(','); }
            if( inputs['project_clients'] === null ){ inputs['project_clients'] = ''; }else{ inputs['project_clients'] = inputs['project_clients'].join(','); }

            return inputs;
        },
        formElements : function(){
            Utils.el.datePicker.datetimepicker({
                timepicker: false,
                format: 'Y-m-d',
            });

            $('div.metabox-body').on('change', '[name="project_tax_select"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                $('[name="project_tax_value"]').val(_self.val());
            });
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);

/*!
 * Timber Projects Edit Module
 */
timber.projectsEdit = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            form : $("form"),
            submitButt : $("form button[type='submit']"),
            datePicker : $(".datepicker"),
        },
        init : function() {
            Utils.submit();
            Utils.formElements();
        },
        submit : function(){
            Utils.el.form.on("submit", Utils.handler);
        },
        handler: function(event) {
            Utils.el.form.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.form.attr('action'), Utils.data(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            Utils.el.form.removeClass('loading');
                            toastr.success(response.data);
                        }else{
                            Utils.el.form.removeClass('loading');
                            toastr.error(response.data);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        data : function(){
            var inputs = {};
            Utils.el.form.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });

            inputs['project_staff'] = $('[name="project_staff"]').val();
            inputs['project_clients'] = $('[name="project_clients"]').val();
            if( inputs['project_staff'] === null ){ inputs['project_staff'] = ''; }else{ inputs['project_staff'] = inputs['project_staff'].join(','); }
            if( inputs['project_clients'] === null ){ inputs['project_clients'] = ''; }else{ inputs['project_clients'] = inputs['project_clients'].join(','); }

            return inputs;
        },
        formElements : function(){
            Utils.el.datePicker.datetimepicker({
                timepicker: false,
                format: 'Y-m-d',
            });

            $('div.metabox-body').on('change', '[name="project_tax_select"]', function(event) {
                event.preventDefault();
                var _self = $(this);
                $('[name="project_tax_value"]').val(_self.val());
            });
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);


/*!
 * Timber Projects View Module
 */
timber.projectsView = (function (window, document, $) {
    'use strict';

    var Utils = {
        el: {
            filesForm : $("form#files_form"),

            milestoneForm : $("form#milestone_form"),
            milestoneFormSubmit : $("form#milestone_form button[type='submit']"),
            milestoneDelete : $("a.delete_milestone"),
            milestoneView : $('a.milestone_view'),

            taskForm : $("form#task_form"),
            taskFormSubmit : $("form#task_form button[type='submit']"),
            taskDelete : $("a.delete_task"),
            taskView : $('a.task_view'),
            taskDone : $('a.done_task'),

            ticketForm : $("form#ticket_form"),
            ticketFormSubmit : $("form#ticket_form button[type='submit']"),
            ticketDelete : $("a.delete_ticket"),
            replyDelete : $('a.delete_reply'),
            ticketView : $('a.ticket_view'),
            ticketContent : $('textarea[name="ticket_content"]'),
            ticketClose : $('a.close_ticket'),
        },
        init : function() {

            if( project_sub_tab == 'stats' ){
                Utils.stats();
            }else if(project_sub_tab == 'files'){
                Utils.files();
            }else if(project_sub_tab == 'tasks'){
                Utils.task();
            }else if(project_sub_tab == 'milestones'){
                Utils.milestone();
            }else if(project_sub_tab == 'tickets'){
                Utils.tickets();
            }

            $(".datepicker").datetimepicker({
                timepicker: false,
                format: 'Y-m-d',
            });
        },
        stats : function(){

        },
        tickets : function(){
            Utils.el.ticketDelete.on('click', Utils.ticketDeleteHandler);
            Utils.el.replyDelete.on('click', Utils.replyDeleteHandler);
            Utils.el.ticketForm.on("submit", Utils.ticketFormHandler);
            Utils.el.ticketView.on('click', Utils.ticketViewHandler);
            Utils.el.ticketClose.on('click', Utils.ticketCloseHandler);

            Utils.el.ticketContent = Utils.el.ticketContent.wysibb({
                buttons: "bold,italic,underline,|,link,|,bullist,numlist"
            });

            if( $('textarea[name="ticket_content"]').hasClass('edit_content') ){
                $('textarea[name="ticket_content"]').htmlcode($('textarea[name="ticket_content"]').text());
            }

            Utils.ticketAttachmentsDump();
            Utils.ticketAttachmentsUpload();
        },
        files : function(){
            Utils.attachmentsUpload();
            Utils.attachmentsStore();
            Utils.attachmentsDump();
        },
        task : function(){
            Utils.el.taskDelete.on('click', Utils.taskDeleteHandler);
            Utils.el.taskForm.on("submit", Utils.taskFormHandler);
            Utils.el.taskView.on('click', Utils.taskViewHandler);
            Utils.el.taskDone.on('click', Utils.taskDoneHandler);
        },
        ticketViewHandler : function(event){
             event.preventDefault();
             var _self = $(this);
             _self = _self.closest('tr');
             var _selfDesc = $('tr[data-desc="' + _self.attr('data-id') + '"]');
             if( _selfDesc.is(':hidden') ){
                _selfDesc.show();
             }else{
                _selfDesc.hide();
             }
        },
        ticketFormHandler : function(event){
            Utils.el.ticketForm.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.ticketForm.attr('action'), Utils.ticketData(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            toastr.success(response.data);
                            if(response.next_link){
                                window.location = response.next_link;
                            }else{
                                Utils.el.ticketForm.removeClass('loading');
                            }
                        }else{
                            Utils.el.ticketForm.removeClass('loading');
                            toastr.error(response.data);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        ticketDeleteHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {ticket_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                $('tr[data-id="' + _self.attr('data-id') + '"]').fadeOut( "slow", function() {
                                    $('tr[data-id="' + _self.attr('data-id') + '"]').remove();
                                });
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        ticketCloseHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {ticket_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                location.reload();
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        replyDeleteHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                Pace.track(function(){
                    $.post(_self.attr('href'), {ticket_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                _self.closest('div.comment').remove();
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        ticketData : function(){
            var inputs = {};
            Utils.el.ticketForm.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            inputs['ticket_content'] = Utils.el.ticketContent.htmlcode();
            return inputs;
        },
        ticketAttachmentsDump : function(){
            $('i.delete_file').on('click', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    var oldAttachments = $('input[name="tic_old_attachments"]');
                    var oldAttachmentsArr = oldAttachments.val().split(',');
                    var index = oldAttachmentsArr.indexOf(_self.attr('data-id'));

                    if (index > -1) {
                        oldAttachmentsArr.splice(index, 1);
                    }

                    oldAttachments.val(oldAttachmentsArr.join(','));
                    _self.closest('a').remove();
                });
            });
        },
        ticketAttachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="tic_attachments"]').val(uploadedFilesField);
            });
        },
        taskViewHandler : function(event){
             event.preventDefault();
             var _self = $(this);
             _self = _self.closest('tr');
             var _selfDesc = $('tr[data-desc="' + _self.attr('data-id') + '"]');
             if( _selfDesc.is(':hidden') ){
                _selfDesc.show();
             }else{
                _selfDesc.hide();
             }
        },
        taskFormHandler : function(event){
            Utils.el.taskForm.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.taskForm.attr('action'), Utils.taskData(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            toastr.success(response.data);
                            if(response.next_link){
                                window.location = response.next_link;
                            }else{
                                Utils.el.taskForm.removeClass('loading');
                            }
                        }else{
                            Utils.el.taskForm.removeClass('loading');
                            toastr.error(response.data);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        taskDeleteHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {task_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                $('tr[data-id="' + _self.attr('data-id') + '"]').fadeOut( "slow", function() {
                                    $('tr[data-id="' + _self.attr('data-id') + '"]').remove();
                                });
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        taskDoneHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {task_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                location.reload();
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        taskData : function(){
            var inputs = {};
            Utils.el.taskForm.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },


        milestone : function(){
            Utils.el.milestoneDelete.on('click', Utils.milestoneDeleteHandler);
            Utils.el.milestoneForm.on("submit", Utils.milestoneFormHandler);
            Utils.el.milestoneView.on('click', Utils.milestoneViewHandler);
        },
        milestoneViewHandler : function(event){
             event.preventDefault();
             var _self = $(this);
             _self = _self.closest('tr');
             var _selfDesc = $('tr[data-desc="' + _self.attr('data-id') + '"]');
             if( _selfDesc.is(':hidden') ){
                _selfDesc.show();
             }else{
                _selfDesc.hide();
             }
        },
        milestoneFormHandler : function(event){
            Utils.el.milestoneForm.addClass('loading');
            Pace.track(function(){
                $.post(Utils.el.milestoneForm.attr('action'), Utils.milestoneData(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            toastr.success(response.data);
                            if(response.next_link){
                                window.location = response.next_link;
                            }else{
                                Utils.el.milestoneForm.removeClass('loading');
                            }
                        }else{
                            Utils.el.milestoneForm.removeClass('loading');
                            toastr.error(response.data);
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        milestoneDeleteHandler : function(event){
            event.preventDefault();
            var _self = $(this);
            alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                _self.addClass('loading');
                Pace.track(function(){
                    $.post(_self.attr('href'), {milestone_id : _self.attr('data-id'), user_nonce : current_user_nonce}, function( response, textStatus, jqXHR ){
                        if( jqXHR.status == 200 && textStatus == 'success' ) {
                            if( 'success' == response.status ){
                                toastr.success(response.data);
                                $('tr[data-id="' + _self.attr('data-id') + '"]').fadeOut( "slow", function() {
                                    $('tr[data-id="' + _self.attr('data-id') + '"]').remove();
                                });
                            }else{
                                toastr.error(response.data);
                            }
                        }
                    }, 'json');
                });
            });
        },
        milestoneData : function(){
            var inputs = {};
            Utils.el.milestoneForm.serializeArray().map(function(item, index) {
                inputs[item.name] = item.value;
            });
            return inputs;
        },

        attachmentsUpload : function() {
            var uploadedFiles = [];

            var myDropzone = new Dropzone('#dropzone_uploader', {
                    url: upload_file_socket + 'record_attachments',
                    paramName: "record_attachment", // The name that will be used to transfer the file
                    acceptedFiles: uploader_global_settings.acceptedfiles,
                    maxFiles: uploader_global_settings.maxFiles, // Maximum Number of Files
                    maxFilesize: uploader_global_settings.maxfilesize, // MB
                    addRemoveLinks: true,
                    // The setting up of the dropzone
                    init: function() {
                        this.on('success', function(file, response) {
                            var new_response = JSON.parse(response);
                            var nice_response = [];
                            nice_response['new_name'] = new_response.info.new_name;
                            nice_response['name'] = new_response.info.name;
                            nice_response['path'] = new_response.info.path;
                            uploadedFiles.push(nice_response);
                        });
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("user_nonce", current_user_nonce);
                    },
                    removedfile: function(file) {
                        var uploaded_file_data = JSON.parse(file.xhr.response),
                        _ref = file.previewElement;

                        uploadedFiles = uploadedFiles.filter(function(el) {
                            return el['new_name'] != uploaded_file_data.info.new_name;
                        });
                        //uploaded_file_data.info
                        return _ref != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
            });
            $("#uploader_save").on('click', function(event) {
                event.preventDefault();
                var uploadedFilesField = '';

                uploadedFiles.forEach(function(el, index){
                    uploadedFilesField = uploadedFilesField + el['new_name'] + "--||--" + el['name'] + "----||||----";
                });

                $('[name="pro_attachments"]').val(uploadedFilesField);
                $('[name="pro_attachments"]').trigger('change');
            });
        },
        attachmentsStore : function(){
            $('input[name="pro_old_attachments"], input[name="pro_attachments"]').on('change', function(event) {
                event.preventDefault();
                $('a.store_files').show();
            });
            $('a.store_files').on('click', Utils.filesHandler);
        },
        attachmentsDump : function(){
            $('a.dump_file').on('click', function(event) {
                event.preventDefault();
                var _self = $(this);
                alertify.confirm(i18nStrings.alert).set('onok', function(closeEvent){
                    var oldAttachments = $('input[name="pro_old_attachments"]');
                    var oldAttachmentsArr = oldAttachments.val().split(',');
                    var index = oldAttachmentsArr.indexOf(_self.attr('data-id'));

                    if (index > -1) {
                        oldAttachmentsArr.splice(index, 1);
                    }

                    oldAttachments.val(oldAttachmentsArr.join(','));
                    _self.closest('div.file-box').remove();
                    oldAttachments.trigger('change');
                });
            });
        },
        filesHandler: function(event) {
            var _self = $(this);
            _self.remove();
            Pace.track(function(){
                $.post(Utils.el.filesForm.attr('action'), Utils.filesData(), function( response, textStatus, jqXHR ){
                    if( jqXHR.status == 200 && textStatus == 'success' ) {
                        if( 'success' == response.status ){
                            toastr.success(response.data);
                            location.reload();
                        }else{
                            toastr.error(response.data);
                            location.reload();
                        }
                    }
                }, 'json');
            });
            event.preventDefault();
        },
        filesData : function(){
            var inputs = {};
            Utils.el.filesForm.serializeArray().map(function(item, index) {
                var name = item.name;
                inputs[name] = item.value;
            });
            return inputs;
        },
    };
    return {
        init: Utils.init,
    };
})(window, document, jQuery);