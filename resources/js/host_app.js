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
    HOST_URL: HOST_URL,
    SITE_NAME: SITE_NAME,
    currentRouteName: currentRouteName,
    userCurrency: userCurrency,
    userLanguage: userLanguage,
    currency_symbol: CURRENCY_SYMBOL,
    translations: [],
    removed_translations: [],
    translatable_fields: [],
    locale: '',
    error_messages: {},
    isLoading: false,
};
let components = {};
let otherData = {};
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
            $('.page_content').summernote({
                height: 250,
            });
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
};
let vueMethods = {};

if(currentRouteName == 'host.dashboard') {
    otherData = {
        minYear:'',
        maxYear:'',
        currentYear:'',
        dashboard_data: {},
        bar_chart: {
            month_names:[],
            month_index: 0,
            current_year: 0
        },
        geo_data: {},
        myLineChart: {},
        myPieChart: {},
        myBarChart: {},
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
                        borderColor: "#e83f22",
                        pointBorderColor: "#FFF",
                        pointBackgroundColor: "#e83f22",
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
        drawNewBarChart(chartData) {
            var barChart = document.getElementById('BarChart').getContext('2d');
            this.myBarChart = new Chart(barChart, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        borderColor: "#384477",
                        pointBorderColor: "#FFF",
                        pointBackgroundColor: "#384477",
                        pointBorderWidth: 2,
                        pointHoverRadius: 4,
                        pointHoverBorderWidth: 1,
                        pointRadius: 4,
                        backgroundColor: "#384477",
                        borderWidth: 2,
                        data: chartData.count,
                        earnings: chartData.earnings,
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
                                return '  '+data.datasets[0].earnings[tooltipItems.index]+' Earnings';
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
                                labelString: 'Dates'
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Reservations'
                            },
                            ticks: {
                                min: 0,
                                suggestedMin: 0,
                                suggestedMax: 10,
                                beginAtZero: true,
                            }
                        }]
                    },
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
                this.myLineChart.data.datasets[0].data = response_data.line_chart.amount;
                this.myLineChart.update();

                this.myPieChart.data.datasets[0].data = response_data.pie_chart.data;
                this.myPieChart.update();

                this.admin_earnings = response_data.admin_earnings;
                setTimeout( () => {
                    this.isLoading = false;
                });
            };
            this.makePostRequest(routeList.host_dashboard,data_params,callback_function);
        },
        updateBarChartData(type) {
            this.isLoading = true;
            var bar_chart = this.bar_chart;
            if (type == 'increment') {
                if(bar_chart.month_index == 12) {
                    bar_chart.current_year = parseInt(bar_chart.current_year) + 1;
                    bar_chart.month_index = 1;
                }
                else {
                    bar_chart.month_index = parseInt(bar_chart.month_index) + 1;
                }
            }
            else {
                if(bar_chart.month_index == 1) {
                    bar_chart.current_year = parseInt(bar_chart.current_year) - 1;
                    bar_chart.month_index = 12;
                }
                else {
                    bar_chart.month_index = parseInt(bar_chart.month_index) - 1;
                }
            }
            var data_params = {month:bar_chart.month_index,year:bar_chart.current_year};
            var callback_function = (response_data) => {
                this.myBarChart.data.datasets[0].data = response_data.data.count;
                this.myBarChart.data.datasets[0].earnings = response_data.data.earnings;
                this.myBarChart.data.labels = response_data.data.labels;
                this.myBarChart.update();
                setTimeout( () => {
                    this.isLoading = false;
                });
            };
            this.makePostRequest(routeList.host_dashboard,data_params,callback_function);
        },
        registerEvents() {
            for(let key in this.dashboard_data.statistics_data) {
                this.drawNewCircleChart(this.dashboard_data.statistics_data[key],key);
            }
            this.drawNewLineChart(this.dashboard_data.line_chart);
            this.drawNewBarChart(this.bar_chart.data);
        },
    };
}

if(['host.edit','host.users.create','host.users.edit','host.edit_company'].includes(currentRouteName)) {
    otherData = {
        cities: [],
        selected_country: '',
        selected_city: '',
    };
}

if(currentRouteName == 'host.coupon_codes.create' || currentRouteName == 'host.coupon_codes.edit') {
    otherData = {
        type: 'amount',
    };
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
            this.InitDatePicker('#expired_at');
        },
    };
}

if(currentRouteName == 'host.reservations' || currentRouteName == 'host.payouts') {
    otherData = {
        type: "",
    };
    vueMethods = {
        changeFilterType(){
            var url = '';
            if(currentRouteName == 'host.reservations') {
                url = routeList.reservations;
            }
            else if(currentRouteName == 'host.payouts') {
                url = routeList.payouts;
            }
            window.location.href = url+'/'+this.type;
        },
        registerEvents() {
            
        },
    };

}
if(currentRouteName == 'host.reports') {
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

if(currentRouteName == 'host.hotels') {
    vueMethods = {
        updateHotelStatus(hotel_id,status) {
            var url = routeList.update_hotel_options;
            var data_params = { hotel_id : hotel_id, status:status,type:'status'};

            var callback_function = function(response_data) {
                window.location.reload();
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        registerEvents() {
            var self = this;
            $(document).on('change', '.hotel-status', function() {
                var hotel_id = $(this).data('id');
                var status = $(this).val();
                self.updateHotelStatus(hotel_id,status);
            });
        },
    };
}

if(currentRouteName == 'host.rooms') {
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
if(currentRouteName == 'host.edit_company'){
     computed = {
        isDisabled() {
            return window.vueInitData.isDisabled;
        },
     }  
}

if(currentRouteName == 'host.payout_methods.create'){
    let module_data = require('./modules/host/add_payout.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.vueMethods};
    components = {...components,...module_data.components};
    computed = {...computed,...module_data.computed};    
}

if(currentRouteName == 'host.hotels.edit') {
    let module_data = require('./modules/common/manage_hotel.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.vueMethods};
    components = {...components,...module_data.components};
    computed = {...computed,...module_data.computed};
}
if(currentRouteName == 'host.rooms.edit') {
    let module_data = require('./modules/common/manage_rooms.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.vueMethods};
    components = {...components,...module_data.components};
    computed = {...computed,...module_data.computed};
}

if(currentRouteName == 'host.messages.edit') {
    let module_data = require('./modules/host/conversation.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.vueMethods};
    components = {...components,...module_data.components};
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