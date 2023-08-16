/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
'use strict';

window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

window.$ = window.jQuery = require('jquery');

window.bootstrap = require('bootstrap');

import "flatpickr";
import "bootstrap-notify/bootstrap-notify.min.js";

import "select2/dist/js/select2.js";

import { createApp } from 'vue';

import VueLazyLoad from 'vue3-lazyload';

let vueCommonData = {
    APP_URL: APP_URL,
    ADMIN_URL: ADMIN_URL,
    SITE_NAME: SITE_NAME,
    currentRouteName: currentRouteName,
    userCurrency: userCurrency,
    userLanguage: userLanguage,
    translations: [],
    removed_translations: [],
    locale: '',
    error_messages: {},
    isLoading: false,
};
let components = {};
let otherData = {};
let vueMethods = {};
let computed = {};

let vueCommonMethods = {
    makePostRequest(url,data_params,callback) {
        try {
            this.isLoading = true;
            axios.post(url,data_params)
                .then((response) => {
                    if(response.data.status == 'redirect') {
                        window.location.href = response.data.redirect_url;
                    }
                    else if(response.data.status == 'reload') {
                        window.location.reload();
                    }
                    else {
                        callback(response.data);
                    }
                })
                .catch((error) => {
                if(error.response.status==413)
                    {
                        flashMessage("File upload Size is greater than 5mb, try uploading again or Refreshing", 'danger');
                    }
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
        catch (error) {
            this.isLoading = false;
        }
    },
    addNewTranslation(locale) {
        var translation = [];
        translation['locale'] = locale;
        this.translations.push(translation);
        this.locale = '';
        setTimeout(() => {
            this.initRichTextEditor('.rich-text-editor');
        },10);
    },
    canDisplayLanguage(locale) {
        if(locale == DEFAULT_LANGUAGE) {
            return false;
        }
        let index = this.translations.findIndex((x) => x.locale == locale);
        return index == -1;
    },
    removeTranslation(locale,index) {
        this.translations.splice(index, 1);
        this.removed_translations.push(locale);
    },
    initRichTextEditor(selector) {
        let editors = document.querySelectorAll(selector);
        editors.forEach(editor => {
            let editorSelector = editor.id;
            $('#'+editorSelector).summernote({
                height: 250,
                callbacks: {
                    onImageUpload: function(files) {
                        $('.note-editor').addClass('loading');
                        for(let i=0; i < files.length; i++) {
                            let file = files[i];
                            let formData = new FormData();
                            formData.append('file', file, file.name);
                            axios.post(routeList.upload_image, formData, {
                                headers: {'Content-Type': 'multipart/form-data'}
                            }).then((response) => {
                                let response_data = response.data;
                                if(response_data.status) {
                                    let imgNode = document.createElement('img');
                                    imgNode.src = response_data.src;
                                    $('#'+editorSelector).summernote('insertNode', imgNode);
                                }
                                $('.note-editor').removeClass('loading');
                            });
                        }
                    }
                }
            });
        });
    }
};

if(currentRouteName == 'admin.dashboard') {
    otherData = {
        minYear:'',
        maxYear:'',
        currentYear:'',
        dashboard_data: {},
        geo_data: {},
        myLineChart: {},
        myPieChart: {},
    };
    vueMethods = {
        drawNewCircleChart(circle,selector) {
            let displayText = function(value) {
                let text = circle.count;
                if(circle.new > 0) {
                    text += '<span class="new-count"><i class="fas fa-caret-up"></i>'+circle.new+'</span>';
                }
                return text;
            };
            Circles.create({
                id: selector,
                radius: 50,
                value: circle.value,
                maxValue: 100,
                width: 7,
                text: displayText,
                colors: circle.colors,
                duration: 400,
                wrpClass: 'circles-wrp',
                textClass: 'circles-text',
                styleWrapper: true,
                styleText: true
            });
        },
        drawNewLineChart(chartData) {
            var lineChart = document.getElementById('LineChart').getContext('2d');
            this.myLineChart = new Chart(lineChart, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        borderColor: "#1d7af3",
                        pointBorderColor: "#FFF",
                        pointBackgroundColor: "#1d7af3",
                        pointBorderWidth: 2,
                        pointHoverRadius: 4,
                        pointHoverBorderWidth: 1,
                        pointRadius: 4,
                        backgroundColor: 'transparent',
                        fill: true,
                        borderWidth: 2,
                        data: chartData.amount,
                    }]
                },
                options : {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        bodySpacing: 4,
                        mode:"nearest",
                        intersect: 0,
                        position:"nearest",
                        xPadding:10,
                        yPadding:10,
                        caretPadding:10,
                        callbacks: {
                            label: function(tooltipItems, data) {
                                return '  '+CURRENCY_SYMBOL +data.datasets[0].data[tooltipItems.index];
                            }
                        }
                    },
                    layout:{
                        padding:{left:15,right:15,top:15,bottom:15}
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Month'
                            }
                        }],
                        yAxes: [{
                            display: true,
                            ticks: {
                                min: 0,
                                suggestedMin: 0,
                                suggestedMax: 100,
                                beginAtZero: true,
                            }
                        }]
                    },
                }
            });
        },
        drawNewPieChart(chartData) {
            var pieChart = document.getElementById('totalIncomeChart').getContext('2d');
            this.myPieChart = new Chart(pieChart, {
                type: 'pie',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        data: chartData.data,
                        backgroundColor :["#1d7af3","#f3545d","#fdaf4b"],
                        borderWidth: 0
                    }],
                },
                options : {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position : 'right',
                        labels : {
                            fontColor: 'rgb(154, 154, 154)',
                            fontSize: 12,
                            usePointStyle : true,
                            padding: 15
                        }
                    },
                    pieceLabel: {
                        render: 'percentage',
                        fontColor: 'white',
                        fontSize: 14,
                    },
                    tooltips: {
                        mode:"nearest",
                        callbacks: {
                            label: function(tooltipItems, data) {
                                return '  '+CURRENCY_SYMBOL +data.datasets[0].data[tooltipItems.index];
                            }
                        }
                    },
                    layout: {
                        padding: {
                            left: 20,
                            right: 20,
                            top: 20,
                            bottom: 20
                        }
                    }
                }
            });
        },
        updateChartData(type) {
            this.isLoading = true;
            if (type == 'increment') {
                this.currentYear = this.currentYear - 0 + 1;
            }
            else {
                this.currentYear = this.currentYear - 1;
            }
            var data_params = {year:this.currentYear};

            var callback_function = (response_data) => {
                if(response_data.status) {
                    this.myLineChart.data.datasets[0].data = response_data.line_chart.amount;
                    this.myLineChart.update();

                    this.myPieChart.data.datasets[0].data = response_data.pie_chart.data;
                    this.myPieChart.update();

                    this.admin_earnings = response_data.admin_earnings;
                    setTimeout( () => {
                        this.isLoading = false;
                    });
                }
            };
            this.makePostRequest(routeList.admin_dashboard,data_params,callback_function);
        },
    };
}

if(['admin.hosts.edit','admin.hosts.create','admin.users.edit','admin.users.create'].includes(currentRouteName)) {
    otherData = {
        cities: [],
        selected_country: '',
        selected_city: '',
        selected_company_country: '',
        selected_company_city: '',
        verification_status: 'pending',
    };
}

if(currentRouteName == 'admin.static_page_header') {
    otherData = {
        static_page_headers: [],
    };
}

if(currentRouteName == 'admin.email_to_users') {
    otherData = {
        mail_to: 'specific',
    };
    vueMethods = {
        registerEvents() {
            let mail_to = this.mail_to;
            this.mail_to = 'specific';
            setTimeout(() => {
                $('#emails').select2();
                this.mail_to = mail_to;
            },100);
        },
    };
}

if(currentRouteName == 'admin.global_settings') {
    otherData = {
        default_currency : '',
    };
    vueMethods = {
        changeCurrency(type) {
            if (type == 'no') {
                document.getElementById('default_currency').value = this.default_currency;
                return false;
            }
            closeModal('confirmChangeCurrencyModal');
        },
        registerEvents() {
            var self = this;
            $(document).on('change', '#default_currency', function() {
                self.default_currency = $(this).data('default_currency');
                openModal('confirmChangeCurrencyModal');
            });
        },
    };
}

if(currentRouteName == 'admin.coupon_codes.create' || currentRouteName == 'admin.coupon_codes.edit') {
    vueMethods = {
        InitDatePicker(selector) {
            var flatpickrOptions = {
                altInput: true,
                altFormat: flatpickrFormat,
                dateFormat: "Y-m-d",
            };
            flatpickr(selector, flatpickrOptions);
        },
        registerEvents() {
            this.InitDatePicker('#start_date');
            this.InitDatePicker('#end_date');
        },
    };
}

if(currentRouteName == 'admin.reports') {
    otherData = {
        report_data: {},
        filter_list: {},
        report: {},
        filter_text: '',
        currentFilter: '',
        flatpickrs:[],
        showExport:false,
    };
    vueMethods = {
        InitStartDatePicker() {
            let self = this;
            let flatpickrOptions = {
                mode: 'single',
                altInput: true,
                altFormat: flatpickrFormat,
                dateFormat: "Y-m-d",
                maxDate: 'today',
                onChange: function(selectedDates, dateStr, instance) {
                    let selectedDay = selectedDates[0];
                    self.report.to = '';
                    self.flatpickrs['end'].set('minDate', selectedDay);
                    self.flatpickrs['end'].open();
                },
            };
            this.flatpickrs['start'] = flatpickr("#report_from", flatpickrOptions);
        },
        InitEndDatePicker() {
            var flatpickrOptions = {
                mode: 'single',
                altInput: true,
                altFormat: flatpickrFormat,
                dateFormat: "Y-m-d",
                maxDate: 'today',
            };
            this.flatpickrs['end'] = flatpickr("#report_to", flatpickrOptions);
        },
        exportReports() {
            this.updateCurrentFilter(this.report.category);
            setTimeout(() => {
                $('#exportReportForm').submit();
            });
        },
        fetchReports() {
            this.updateCurrentFilter(this.report.category);
            let data_params = this.report;
            this.showExport = false;
            let callback_function = (response_data) => {
                if(response_data.status) {
                    this.filter_text = response_data.filter_text;
                    this.report_data = response_data.data;
                    this.showExport = true;
                }
            };
            this.makePostRequest(routeList.fetch_report,data_params,callback_function);
        },
        updateCurrentFilter(category) {
            let index = this.filter_list.findIndex((x) => x.name == category);
            this.currentFilter = this.filter_list[index];
        },
        registerEvents() {
            this.InitStartDatePicker();
            this.InitEndDatePicker();
        },
    };
}

if(currentRouteName == 'admin.hotels') {
    vueMethods = {
        updateHotelStatus(hotel_id,status) {
            var data_params = { hotel_id : hotel_id, status:status,type:'status'};
            var callback_function = function(response_data) {
                window.location.reload();
            };

            this.makePostRequest(routeList.update_hotel_options,data_params,callback_function);
        },
        updateHotelRecommendStatus(hotel_id,type = 'recommend') {
            var data_params = { hotel_id : hotel_id,type:type};
            var callback_function = function(response_data) {
                window.location.reload();
            };

            this.makePostRequest(routeList.update_hotel_options,data_params,callback_function);
        },
        registerEvents() {
            var self = this;
            $(document).on('change', '.hotel-admin_status', function() {
                var hotel_id = $(this).data('id');
                var status = $(this).val();
                self.updateHotelStatus(hotel_id,status);
            });

            $(document).on('click', '.hotel-recommended', function() {
                var hotel_id = $(this).data('id');
                self.updateHotelRecommendStatus(hotel_id,'recommend');
            });

            $(document).on('click', '.hotel-top_picks', function() {
                var hotel_id = $(this).data('id');
                self.updateHotelRecommendStatus(hotel_id,'top_picks');
            });
        },
    };
}

if(currentRouteName == 'admin.rooms') {
    vueMethods = {
        updateRoomStatus(room_id,status) {
            var url = routeList.update_room_options;
            var data_params = { room_id : room_id, status:status};

            var callback_function = function(response_data) {
                window.location.reload();
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        registerEvents() {
            var self = this;
            $(document).on('change', '.room-admin_status', function() {
                var room_id = $(this).data('id');
                var status = $(this).val();
                self.updateRoomStatus(room_id,status);
            });
        },
    };
}

if(currentRouteName == 'admin.hotels.edit') {
    let module_data = require('./modules/common/manage_hotel.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.vueMethods};
    components = {...components,...module_data.components};
    computed = {...computed,...module_data.computed};
}

if(currentRouteName == 'admin.rooms.edit') {
    let module_data = require('./modules/common/manage_rooms.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.vueMethods};
    components = {...components,...module_data.components};
    computed = {...computed,...module_data.computed};
}

if(['admin.featured_cities.create','admin.featured_cities.edit','admin.popular_cities.create','admin.popular_cities.edit',
    'admin.popular_localities.create','admin.popular_localities.edit'].includes(currentRouteName)) {
    // let module_data = require('./modules/admin/auto_complete.js').default;
    // otherData = {...otherData,...module_data.otherData};
    // vueMethods = {...vueMethods,...module_data.vueMethods};
    // components = {...components,...module_data.components};
}

if(currentRouteName == 'admin.fees') {
    otherData = {
        service_fee_type: 'percentage',
        penalty_days: 7,
    };
}

if(currentRouteName == 'admin.coupon_codes'){
    otherData = {
        type: 'percentage',
        penalty_days: 7,
    };
}

if(currentRouteName == 'admin.translations') {
    otherData = {
        file: 'admin_messages',
        language: '',
        translation_data: [],
        translation_result: {
            translation: '',
        },
        isLoading: false,
        search_text: '',
    };
    vueMethods = {
        getTranslationData(search_text = '') {
            var data_params = {};
            data_params['file'] = this.file;
            data_params['language'] = this.language;
            data_params['search_text'] = search_text;
            this.search_text = search_text;
            if (search_text == '') {
                this.translation_data = [];
                if($.fn.DataTable.isDataTable('#translation-table')) {
                    $('#translation-table').DataTable().clear().destroy();
                }
            } else {
                openModal('TranslationModal');
            }

            var callback_function = (response_data) => {
                this.translation_result = response_data.data.translation_result;
                if (search_text == '') {
                    this.translation_data = response_data.data.translation_data;
                    setTimeout( () => {
                        $('#translation-table').DataTable({});
                    });
                }
            };

            this.makePostRequest(routeList.get_translations,data_params,callback_function);
        },
    }
}

const myApp = Vue.createApp({
    data() {
        return { ...vueCommonData,...otherData};
    },
    mounted() {
        window.addEventListener('load', () => {
            // Init Default Data
            if(objectLength(default_init_data) > 0) {
                for (const [key, value] of Object.entries(default_init_data)) {
                    this[key] = value;
                }
            }

            // Init translation data
            if(objectLength(window.translationInitData) > 0) {
                for (const [key, value] of Object.entries(window.translationInitData)) {
                    this[key] = value;
                }
            }

            // Init all data
            if(objectLength(window.vueInitData) > 0) {
                for (const [key, value] of Object.entries(window.vueInitData)) {
                    this[key] = value;
                }
            }

            if(currentRouteName == 'admin.dashboard') {
                for(let key in this.dashboard_data.statistics_data) {
                    this.drawNewCircleChart(this.dashboard_data.statistics_data[key],key);
                }
                this.drawNewLineChart(this.dashboard_data.line_chart);
                this.drawNewPieChart(this.dashboard_data.pie_chart);
            }

            setTimeout(() => {
                this.initRichTextEditor('.rich-text-editor');
            },100);

            if (typeof this.registerEvents === "function") {
                this.registerEvents();
            }

            if (typeof this.loadFunctions === "function") {
                this.loadFunctions();
            }
        });
    },
    components: components,
    methods: { ...vueCommonMethods,...vueMethods},
	computed: computed,
});

myApp.use(VueLazyLoad, {
    log : false,
    observerOptions : {
        threshold: 0.3
    },
});


myApp.mount('#app');