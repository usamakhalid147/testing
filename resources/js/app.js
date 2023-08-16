'use strict';

require('./bootstrap');

document.addEventListener('DOMContentLoaded', function() {
    // Init ToolTips
    setTimeout( () => {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },1000);
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import VueLazyLoad from 'vue3-lazyload';

import Pagination from './components/PaginationComponent.vue';

let vueCommonData = {
    APP_URL: APP_URL,
    SITE_NAME: SITE_NAME,
    user_id: USER_ID,
    currentRouteName: currentRouteName,
    terms_of_use: false,
    cookie_policy: false,
    privacy_policy: false,
    refund_and_cancellation_policy: false,
    footerShown: false,
    userCurrency: userCurrency,
    userLanguage: userLanguage,
    translation_messages: {},
    list_type: 'hotel',
    error_messages: {},
    isLoading: false,
    inbox_count: 0,
	isContentLoading: true,
    search_results: [],
    location: '',
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
                        flashMessage("File upload Size is greater than 1mb, try uploading again or Refreshing", 'danger');
                    }
                    $("#owner_agree_tac").prop("checked", true);
                    $("#signup").removeAttr("disabled");
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
        catch (error) {
            this.isLoading = false;
        }
    },
    toggleFooter() {
        this.footerShown = (this.footerShown == true) ? false : true;
    },
    doGTranslate(lang_pair) {
        var lang = lang_pair.split('|')[1];
        var plang = location.pathname.split('/')[1];
        if(plang.length !=2 && plang.toLowerCase() != 'zh-cn' && plang.toLowerCase() != 'zh-tw') {
            plang='en';
        }
        if(lang == 'en') {
            window.location.href = location.protocol+'//'+location.host+location.pathname.replace('/'+plang+'/', '/')+location.search;
        }
        else {
            window.location.href = location.protocol+'//'+location.host+'/'+lang+location.pathname.replace('/'+plang+'/', '/')+location.search;
        }
    },
    updateUserDefault(type) {
        this.isLoading = true;
        var url = routeList.update_user_default;
        var data_params = {type: type,currency: this.userCurrency,language: this.userLanguage, route_name: currentRouteName};

        var callback_function = (response_data) => {
            if(type == 'language') {
                // this.doGTranslate('en|'+this.userLanguage);
                window.location.reload();
            }
            else {
                window.location.reload();
            }
        };

        this.makePostRequest(url,data_params,callback_function);
    },
    displayNotification(title,reservation,options={}) {
        options.icon = SITE_LOGO;
        options.dir = 'auto';
        options.requireInteraction = true;
        options.vibrate = [200, 100, 200];
        var notification = new Notification(title, options);
        notification.onclick = function(event) {
            event.preventDefault();
            if(event.target.data) {
                if(currentRouteName != 'inbox') {
                    window.location = event.target.data.url;
                }
            }
        };
    },
    startSpeechRecognition() {
        if(!window.hasOwnProperty('webkitSpeechRecognition')) {
            return false;
        }

        $(".voice-search-button").addClass("blink");
        
        let recognition = new webkitSpeechRecognition();

        recognition.continuous = false;
        recognition.interimResults = false;

        // recognition.lang = "en-US";
        recognition.start();
        recognition.onresult = function(e) {
        	$(".voice-search-button").removeClass("blink");
        	let result = e.results[0][0].transcript;
            recognition.stop();
            $('.autocomplete-input').val(result);
            $('.search-form').submit();
        };

        recognition.onerror = function(e) {
            $(".voice-search-button").removeClass("blink");
            recognition.stop();
        }
    },
    getAutoCompleteResults() {
        this.search_results = [];
        const myDropdown = document.getElementById('locationDropdown');
        myDropdown.classList.remove('show');
        $('.location-error').addClass('d-none');
        let location = $('#location').val();
        if(location != '') {
            this.autocomplete_used = false;
            var data_params = {'location' : location};            
            let callback_function = (response_data) => {
                this.search_results = response_data.home_popular_cities;
                if(this.search_results.length > 0) {
                    myDropdown.classList.add('show');
                }
            }

            this.makePostRequest(routeList.search_hotels,data_params,callback_function);
        }
    },
    setSearchLocation(index) {
        var place = this.search_results[index];
        document.getElementById('place_id').value = place.place_id;
        document.getElementById('location').value = place.main_text;
        this.autocomplete_used = true;
        if(currentRouteName == 'hotel_search') {
            this.searchFilter.location = place.main_text;
        }
        else {
            this.location = place.main_text;
        }
        document.getElementById('locationDropdown').classList.remove('show');
        closeModal('searchKey');
    },
};

if(USER_ID == 0 || HOST_ID == 0) {
    let module_data = require('./modules/auth.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
}

if(currentRouteName == 'home') {
    let module_data = require('./modules/home.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
    computed = {...computed,...module_data.computed};
}

if(currentRouteName == 'hotel_search') {
    let module_data = require('./modules/hotel_search.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
    computed = {...computed,...module_data.computed};
}

if(currentRouteName == 'payment.home') {
    let module_data = require('./modules/payment.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
}


if(['update_account_settings','view_profile'].includes(currentRouteName)) {
    let module_data = require('./modules/update_account_settings.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
}

if(currentRouteName == 'hotel_details') {
    let module_data = require('./modules/hotel_details.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
    computed = {...computed,...module_data.computed};
}

if(['wishlists','wishlist.list','hotel_details', 'hotel_search'].includes(currentRouteName)) {
    let module_data = require('./modules/wishlists.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
}

if(currentRouteName == 'bookings') {
    let module_data = require('./modules/bookings.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
}

if(currentRouteName == 'inbox') {
    let module_data = require('./modules/inbox.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
}

if(currentRouteName == 'conversation') {
    let module_data = require('./modules/conversation.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
}

if(currentRouteName == 'invite') {
    let module_data = require('./modules/invite.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
}

if(currentRouteName == 'edit_review') {
    otherData = {
        review: {},
        review_photos: [],
        removed_photos: [],
        error_messages: [],
    };
    vueMethods = {
        previewPhoto(event) {
            let element = this.$refs['file'];
            var image_count = element.files.length + this.review_photos.length;
            if (image_count > 6) {
                this.error_messages.photos = 'you can upload only 6 Images';
                document.getElementById('upload_photos').value = '';
                return false;
            }
            let self = this;
            for (let i = 0; i < element.files.length; i++) {
                let file = element.files[i];
                let reader = new FileReader();
                reader.onload = function (e) {
                    let selectedImage = {
                        id: '',
                        temp_photo: true,
                        image_src: e.target.result,
                        is_error: e.total/1024 > 5120,
                    };
                    self.review_photos.push(selectedImage);
                }
                reader.readAsDataURL(file);
            }
        },
        deletePhoto(index) {
            this.removed_photos.push(this.review_photos[index].id);
            this.review_photos.splice(index, 1);
        },
        writeReview() {
            var url = routeList.update_review;
            var data_params = new FormData(review_form);

            var callback_function = (response_data) => {
                if (!response_data.status) {
                    this.error_messages = response_data.status_message;
                }
                if (response_data.status == 'redirect') {
                    window.location = window.redirect_url;
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        }
    };
}

if(currentRouteName == 'help') {
    let module_data = require('./modules/help.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
}

try {
    let module_data = require('./modules/real_time_chat.js').default;
    otherData = {...otherData,...module_data.otherData};
    vueMethods = {...vueMethods,...module_data.methods};
    components = {...components,...module_data.components};
}
catch(err) {
    
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

            // Init all data
            if(objectLength(window.vueInitData) > 0) {
                for (const [key, value] of Object.entries(window.vueInitData)) {
                    this[key] = value;
                }
            }

            this.inbox_count = inbox_count;

            if(USER_ID > 0) {
                if(typeof firebase != 'undefined') {
                    this.initFirebase();
                    this.listenAfterAuthenticate();
                }
            }

            if(currentRouteName == 'wishlists') {
                this.getAllWishlists();
            }
            if(currentRouteName == 'wishlist.list') {
                if(this.wishlist_list.hotels.length > 0) {
                    setTimeout(() => {
                        updateSlider('.hotel-slider','search');
                    },10);
                }
            }

            if(currentRouteName == 'host_coupon_codes.create' || currentRouteName == 'host_coupon_codes.edit') {
                flatpickr('#start_date', {
                    altInput: true,
                    altFormat: flatpickrFormat,
                    dateFormat: "Y-m-d",
                });

                flatpickr('#end_date', {
                    altInput: true,
                    altFormat: flatpickrFormat,
                    dateFormat: "Y-m-d",
                });
            }

            if (typeof this.registerEvents === "function") {
                this.registerEvents();
            }
            if (typeof this.loadFunctions === "function") {
                this.loadFunctions();
            }

			$(document).ready(() => {
                this.isContentLoading = false;
            })
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