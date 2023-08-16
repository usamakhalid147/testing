// Common JS functions
// Initialize Date Picker
function initDatePicker(selector)
{
    flatpickr(selector, {
        minDate: "today",
        dateFormat: flatpickrFormat,
    });
}

function initRangeDatePicker(selector)
{
    let flatpickrOptions = {
        mode: "range",
        minDate: "today",
        altInput: true,
        altFormat: flatpickrFormat,
        dateFormat: "Y-m-d",
        onClose: function (selectedDates, dateStr, instance) {
            if(selectedDates.length < 2) {
                return false;
            }
            let start_date = selectedDates[0];
            let end_date = selectedDates[1];
            if(start_date.getTime() == end_date.getTime()) {
                end_date.setDate(end_date.getDate() + 1);
            }
            
            $('.popup_checkin').val(changeFormat(start_date));
            $('.popup_checkout').val(changeFormat(end_date));
        },
        altFormat: flatpickrFormat,
        dateFormat: "Y-m-d",
    };

    flatpickr(selector, flatpickrOptions);
}

function initTooltips()
{
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function updateSlider(selector,type = 'common')
{
    if($(selector).length == 0) {
        return false;
    }
    
    if(type == 'home') {
        tns({
            "container": selector,
            "autoplay": true,
            "lazyload": true,
            "mouseDrag": true,
            "nav": false,
            "controls": false,
            "autoplayButton": false,
            "autoplayButtonOutput": false,
        });
    }
    else if(type == 'search') {
        document.querySelectorAll(selector).forEach(slider => {
            tns({
                "container": slider,
                "lazyload": true,
                "autoplay": false,
                "arrowKeys": false,
                "mouseDrag": true,
                "swipeAngle": false,
                "loop": true,
                "nav": false,
                "controlsText": ['<span class="material-icons">chevron_left</span>','<span class="material-icons">chevron_right</span>'],
                "navPosition": "bottom",
                "controls": true,
                "autoplayButton": false,
                "autoplayButtonOutput": false,
                "items": 1,
            });
        });
    }
    else if(type == 'community') {
        tns({
            "container": selector,
            "lazyload": true,
            "autoplay": true,
            "arrowKeys": false,
            "mouseDrag": true,
            "swipeAngle": false,
            "loop": true,
            "nav": false,
            "navPosition": "bottom",
            "controls": false,
            "autoplayButton": false,
            "autoplayButtonOutput": false,
            "gutter": 30,
            "responsive" : {
                0 : {
                    "items":1,
                },
                568 : {
                   "items":2,
                },
                768 : {
                    "items":3,
                },
            }
        });
    }
    else if(type == 'recommended') {
        tns({
            "container": selector,
            "lazyload": true,
            "autoplay": true,
            "arrowKeys": false,
            "mouseDrag": true,
            "swipeAngle": false,
            "loop": true,
            "nav": false,
            "controlsText": ['<span class="material-icons">keyboard_arrow_left</span>','<span class="material-icons">keyboard_arrow_right</span>'],
            "navPosition": "top",
            "controls": true,
            "autoplayButton": false,
            "autoplayButtonOutput": false,
            "gutter": 20,
            "responsive" : {
                0 : {
                    "items":1,
                    "controls": false,
                    "slideBy": 1
                },
                568 : {
                   "items":2,
                   "controls": false,
                   "slideBy": 2
                },
                768 : {
                    "items":3,
                    "controls": true,
                    "slideBy": 3
                },
                1099 : {
                    "items":4,
                    "controls": true,
                    "slideBy": 4
                }
            }
        });
    }
    else if(type == 'top_picks') {
        tns({
            "container": selector,
            "lazyload": true,
            "autoplay": true,
            "arrowKeys": false,
            "mouseDrag": true,
            "swipeAngle": false,
            "loop": true,
            "nav": false,
            "controlsText": ['<span class="material-icons">keyboard_arrow_left</span>','<span class="material-icons">keyboard_arrow_right</span>'],
            "navPosition": "top",
            "controls": true,
            "autoplayButton": false,
            "autoplayButtonOutput": false,
            "gutter": 7,
            "responsive" : {
                0 : {
                    "items":1,
                    "controls": false,
                    "slideBy": 1
                },
                568 : {
                   "items":2,
                   "controls": false,
                   "slideBy": 2
                },
                768 : {
                    "items":3,
                    "controls": true,
                    "slideBy": 3
                },
                1099 : {
                    "items":4,
                    "controls": true,
                    "slideBy": 4
                }
            }
        });
    }
    else if(type == 'property_type') {
        tns({
            "container": selector,
            "lazyload": true,
            "autoplay": true,
            "arrowKeys": false,
            "mouseDrag": true,
            "swipeAngle": false,
            "loop": true,
            "nav": false,
            "controlsText": ['<span class="material-icons">keyboard_arrow_left</span>','<span class="material-icons">keyboard_arrow_right</span>'],
            "navPosition": "bottom",
            "controls": true,
            "autoplayButton": false,
            "autoplayButtonOutput": false,
            "items": 4,
            "gutter": 8,
            "responsive" : {
                0 : {
                    "items":1,
                    "controls": false,
                },
                568 : {
                   "items":2,
                   "controls": false,
                },
                768 : {
                    "items":3,
                    "controls": true,
                },
                1099 : {
                    "items":4,
                    "controls": true,
                }
            }
        });
    }
    else if(type == 'similar_type') {
        tns({
            "container": selector,
            "lazyload": true,
            "autoplay": true,
            "arrowKeys": false,
            "mouseDrag": true,
            "swipeAngle": false,
            "loop": true,
            "nav": false,
            "controlsText": ['<span class="material-icons">keyboard_arrow_left</span>','<span class="material-icons">keyboard_arrow_right</span>'],
            "navPosition": "bottom",
            "controls": true,
            "autoplayButton": false,
            "autoplayButtonOutput": false,
            "items": 4,
            "gutter": 8,
            "responsive" : {
                0 : {
                    "items":1,
                    "controls": false,
                },
                568 : {
                   "items":2,
                   "controls": false,
                },
                768 : {
                    "items":4,
                    "controls": true,
                },
                1099 : {
                    "items":4,
                    "controls": true,
                }
            }
        });
    }
    else {
        tns({
            "container": selector,
            "lazyload": true,
            "autoplay": true,
            "arrowKeys": false,
            "mouseDrag": true,
            "swipeAngle": false,
            "nav": false,
            "navPosition": "bottom",
            "controls": false,
            "autoplayButton": false,
            "autoplayButtonOutput": false,
            "items": 4,
            "gutter": 15,
            "edgePadding": 7,
            "responsive" : {
                0 : {
                    "items":1,
                },
                568 : {
                   "items":2,
                },
                768 : {
                    "items":3,
                },
                1099 : {
                    "items":4,
                }
            }
        });
    }
}

function destroySlider(selector)
{

}

// Get Checkbox Checked Values Based on given selector
function getSelectedData(selector)
{
    var value = [];
    $(selector+':checked').each(function() {
        value.push($(this).val());
    });
    return value;
}

function checkInValidInput(value)
{
    if(value === null) {
        return true;
    }
    if(typeof value == 'object') {
        return value.length == 0;
    }
    return (value === undefined || value === 0 || value === '');
}

// Clone Object
function cloneObject(obj,merge = {})
{
    return Object.assign({}, merge, obj);
}

// Object Length
function objectLength(obj)
{
    if(typeof obj != 'object') {
        return 0;
    }
    return Object.keys(obj).length;
}

// Pluck Particulat key from array
function pluck(array, key)
{
    return array.map(o => o[key]);
}

function setGetParameter(paramName, paramValue)
{
    var url = window.location.href;

    if (url.indexOf(paramName + "=") >= 0) {
        var prefix = url.substring(0, url.indexOf(paramName));
        var suffix = url.substring(url.indexOf(paramName));
        suffix = suffix.substring(suffix.indexOf("=") + 1);
        suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";
        url = prefix + paramName + "=" + paramValue + suffix;
    }
    else {
        url += (url.indexOf("?") < 0) ? "?" : "&";
        url += paramName + "=" + paramValue;
    }
    history.replaceState(null, null, url);
}

function getParameterByName(name)
{
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(window.location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function flashMessage(content, state = 'success')
{
    content.icon = 'icon icon-bell';
    
    $.notify(content,{
        template: '<div data-notify="container" class="col-xs-11 col-sm-4 alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="btn-close" data-notify="dismiss"></button><span data-notify="icon"></span> <span data-notify="title">{1}</span> <span data-notify="message">{2}</span><div class="progress" data-notify="progressbar"><div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div><a href="{3}" target="{4}" data-notify="url"></a></div>',
        type: state,
        placement: {
            from: "top",
            align: "center"
        },
        delay: 5000,
    });
}

// Change date to given format
function changeFormat(date,format = 'YYYY-MM-DD') {
    date = convertToMoment(date);
    return date.format(format);
}

// Convert normal date to moment object
function convertToMoment(date)
{
    return moment(date);
}

function attachEventToClass(selector,handler,event = 'click')
{
    document.querySelectorAll(selector).forEach(item => {
        item.addEventListener(event, handler);
    });
}

function openModal(selector)
{
    let target = document.getElementById(selector);
    let targetModal = bootstrap.Modal.getInstance(target);
    if(targetModal == null) {
        targetModal = new bootstrap.Modal(target)
    }
    setTimeout( () => targetModal.show(), 500);
}

// Close Current Modal and Open New
function closeModal(selector)
{
    let curModal = bootstrap.Modal.getInstance(document.getElementById(selector));
    if(curModal != null) {
        curModal.hide();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const csrf_token = $("input[name='_token']").val();
    const lang = $("html").attr('lang');
    const rtl = (lang  == 'ar');
    $navbar = $('.navbar');
    const scroll_distance = $navbar.attr('color-on-scroll') || 50;

    if(window.hasOwnProperty('webkitSpeechRecognition')) {
        $(".voice-search-button").removeClass('d-none');
    }

    // Close Current modal and open New
    closeAndOpenModal = function(event) {
        closeModal(this.dataset.current);
        openModal(this.dataset.target);
    };
    attachEventToClass('.open-modal',closeAndOpenModal);

    $(document).on("click", '.search-label', function() {
        $('.search-label').removeClass('active');
        $(this).addClass('active');
    });

    $('.dropdown-menu').on('click', function(e) {
        if($(this).hasClass('keep-open')) {
            e.stopPropagation();
        }
    });

    function updateScrollTop() {
	    var header_height = $navbar.outerHeight();
	    $('main').css({ "margin-top": header_height + "px" });
	}

	// $(window).on('scroll', updateScrollTop);
    // $(window).on('resize', updateScrollTop);

    

    setTimeout( () => {
        $('.select-picker').select2({
            width: '100%'
        });
    },2000);

    function toggleUserProfilePopup(type)
    {
        if(type == 'hide') {
            $('.user-profile-link').parent('.logged-links').removeClass('active');
            $('.userProfileModal').addClass('d-none');
        }
        else {
            $('.logged-links').removeClass('active');
            $('.user-profile-link').parent('.logged-links').addClass('active');
            $('.userProfileModal').removeClass('d-none');
        }
    }

    $(document).on("click", '.user-profile-link', function(event) {
        event.preventDefault();
        event.stopPropagation();
        toggleUserProfilePopup('show');
    });

    var onScroll = function() {
        if ($(window).scrollTop() < scroll_distance) {
            $navbar.removeClass('navbar-sm');
        }
        else {
            $navbar.addClass('navbar-sm');
        }
    };

    // Top navigation Bar
    if(currentRouteName == 'home') {
        onScroll();
        $(window).on('scroll', onScroll);
    }

    // updateScrollTop();

    if(currentRouteName == 'search') {
        var updateSearchMarginTop = function() {
            $('.map-canvas').css({ "top": $navbar.outerHeight() + "px" });
        };

        updateSearchMarginTop();
        $(window).on('scroll', updateSearchMarginTop);
        $(window).on('resize', updateSearchMarginTop);
    }

    var updateMargins = function() {
        let window_height = window.innerHeight;
        let footer_height = $(".page-footer").outerHeight();
        let inbox_body = (window_height - $navbar.outerHeight() - footer_height - 24);
        let header_height = $navbar.outerHeight() + 10;
        $('.main-container').css({
            // "margin-top": header_height +"px",
            "min-height": inbox_body + "px",
        });

        let responsive_footer_height = $('.responsive-footer').outerHeight() + 10;
    };

    updateMargins();
    $(window).on('scroll', updateMargins);
    $(window).on('resize', updateMargins);

    $('.sideLink').hover(
       function(){ $(this).addClass('rotate-ver') },
       function(){ $(this).removeClass('rotate-ver') }
    )

    var footerScroll = function() {
        var footer_height = $('.static-footer').outerHeight() || 0;
        if(($(window).scrollTop() + $(window).height()) > ($(document).height() - footer_height - 100)) {
            $($('.footer').addClass('no-transition'));
            if($('.footer').hasClass('footer-shown')) {
                $('.footer-toggle').removeClass("active");
            }
            $('.footer').addClass('footer-shown').addClass('static-footer');
            $('.footer-toggle').addClass('d-none');
        }
        else {
            if($('.footer').hasClass('static-footer')) {
                $('.footer').removeClass('static-footer').removeClass('footer-shown');
                $('.footer-toggle').removeClass('d-none');
                setTimeout( () => $('.footer').removeClass('no-transition'),100);
            }
        }
    };
    if(currentRouteName != 'search') {
        footerScroll();
        $(window).on('scroll', footerScroll);
        $(window).on('resize', footerScroll);
    }

    let footer_height = $('.responsive-footer').outerHeight() + 10;
    $('.userProfileModal').css({ "padding-bottom": footer_height + "px" });
    $('.filter-section').css({ "padding-bottom": footer_height + "px" });

    // Show Password Functionality
    togglePasswordField = function(event) {
        let parentElement = event.target.closest(".password-with-toggler");
        var password = 'password';
        if (currentRouteName == 'update_account_settings') {
            password = this.dataset.password;
        }
        let passwordElem = parentElement.querySelector('.'+password);
        let type = passwordElem.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordElem.setAttribute('type', type);
        this.classList.toggle('icon-eye-slash');
    };
    attachEventToClass('.toggle-password',togglePasswordField);

    // Disable Current element after click
    $(document).on('click','.disable_after_click',function(event) {
        setTimeout( () => $(this).attr('disabled','disabled') ,1);
    });

    var deleteModal = document.getElementById('confirmDeleteModal')
    if(deleteModal != null) {
        deleteModal.addEventListener('shown.bs.modal', function (event) {
            let action = event.relatedTarget.dataset.action;
            document.getElementById('common-delete-form').action = action;
        });        
    }

    // Bootstrap dropdown Keep open while click outside
    $('.dropdown.keep-open').on({
        "shown.bs.dropdown": () => this.closable = false,
        "click":             () => this.closable = true,
        "hide.bs.dropdown":  () => this.closable
    });

    // Save to Cookie when bootstrap alert closed
    var cookieAlert = document.getElementById('cookie-alert');
    if(cookieAlert != null) {
        cookieAlert.addEventListener('closed.bs.alert', function () {
            saveCookieByName('cookie_accepted','1',28);
        });
    }

    var url = window.location.href;
    var domain = url.replace('http://','').replace('https://','').split(/[/?#]/)[0];

    var saveCookieByName = function(name, value, days) {
        var dt = new Date();
        dt.setTime(dt.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+dt.toGMTString();
        document.cookie = name+"="+value+expires+'; domain='+domain+ "; path=/";
    };

    var checkCookieAccepted = function() {
        var pair = document.cookie.match(new RegExp('cookie_accepted' + '=([^;]+)'));
        var result = pair ? pair[1] : 0;
        if(!result) {
            $('#cookie-alert').removeClass('d-none').addClass('show');
        }
    };

    checkCookieAccepted();
    initRangeDatePicker('.popup_date_picker');
    initRangeDatePicker('.popup_mobile_date_picker');

    $('.popup-btn-plus').on('click',function(){
        var type = $(this).data('type');
        var value = parseInt($('.popup_'+type).val()) + 1;
        $('.popup_'+type).val(value );
        if(value == max_guests) {
            $(this).attr('disabled',true);
        }
        if(value > 1) {
            $('.popup_minus_'+type).attr('disabled',false);
        }
        updateOccupancyText();
    })

    $('.popup-btn-minus').on('click',function(){
        var type = $(this).data('type');
        var value = parseInt($('.popup_'+type).val()) - 1;
        $('.popup_'+type).val(value);
        if(type == 'children') {
            if(value == 0) {
                $(this).attr('disabled',true);
            }
        }
        else if(value == 1) {
            $(this).attr('disabled',true);
        }
        if(value < max_guests) {
            $('.popup_plus_'+type).attr('disabled',false);
        }
        updateOccupancyText();
    })

    var updateOccupancyText = function() {
        var rooms = parseInt($('.popup_rooms').val());
        var adults = parseInt($('.popup_adults').val());
        var children = parseInt($('.popup_children').val());

        var text = adults > 1 ? 'adults' : 'adult';
        var occupancy_text = adults+' '+occupancy_messages[text]+', ';

        var text = children > 1 ? 'children' : 'child';
        occupancy_text += children+' '+occupancy_messages[text]+', ';

        var text = rooms > 1 ? 'rooms' : 'room';
        occupancy_text += rooms+' '+occupancy_messages[text];

        $('.popup_occupancy').attr('value',occupancy_text);
    }
    updateOccupancyText();

    let autoCompleteInputs = document.getElementsByClassName('autocomplete-input');
    let autocomplete = [];
    if(autoCompleteInputs.length > 0 && IN_ONLINE) {
        for (let index = 0 ;index < autoCompleteInputs.length; index++) {
            autocomplete[index] = new google.maps.places.Autocomplete(autoCompleteInputs[index], {types: ['(cities)']});
            autocomplete[index].setFields(['address_component','geometry','place_id']);
            google.maps.event.addListener(autocomplete[index], 'place_changed', () => {
                var searchPlace = autocomplete[index].getPlace();
                if(!searchPlace.geometry) {
                    return false;
                }
                $('.autocomplete-place_id').val(searchPlace.place_id);
                $('#latitude').val(searchPlace.geometry.location.lat());
                $('#longitude').val(searchPlace.geometry.location.lng()); 
            });
        }
    }

    $(document).on('submit','#search-form',(event) => {
        let place_id = document.getElementById("ac-place_id");
        let location = document.getElementById("header-location");
        let guests = document.getElementById("guests");
        let hasError = false;
        $('.header-search-error').addClass('d-none');
        if(hasError) {
            event.preventDefault();
            event.stopPropagation();
        }
    });
});

function initFacebookSignIn()
{
    let fb_login_elems = document.querySelectorAll(".fb-login-btn");

    fb_login_elems.forEach(function(element, index) {
        attachFacebookSignin(element);
    });
}

function attachFacebookSignin(element)
{
    element.addEventListener('click', function() {
        FB.login((response) => {
            if(response.status != 'connected') {
                return false;
            }
            if (response.authResponse) {
                let accessToken = response.authResponse.accessToken;
                window.location = routeList.complete_social_signup+'?auth_type=Facebook&access_token='+accessToken;
            }
        }, {scope: 'email,public_profile', return_scopes: true});
    }, false);
}

function initAppleSignIn()
{
    document.querySelectorAll(".apple-signin").forEach(function(element, index) {
        element.addEventListener('click', () => {
            (async () => {
                const response = await window.AppleID.auth.signIn();
                return response;
            })()
            .then(function(result) {
                console.log(result);
            }).catch(function(result) {
                console.log(result.error);
            });
        });
    });
}