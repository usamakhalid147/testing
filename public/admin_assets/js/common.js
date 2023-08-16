// Common JS functions
// Initialize Date Picker
function initDatePicker(selector)
{
    flatpickr(selector, {
        minDate: "today",
        dateFormat: flatpickrFormat,
    });
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

function flashMessage(content, state = 'success')
{
    content.icon = 'fa fa-bell';
    
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

function formattedPrice(price)
{
    return CURRENCY_SYMBOL+price;
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

function closeModal(selector)
{
    let curModal = bootstrap.Modal.getInstance(document.getElementById(selector));
    if(curModal != null) {
        curModal.hide();
    }
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

// Close Current Modal and Open New
document.addEventListener('DOMContentLoaded', () => {
    const csrf_token = $("input[name='_token']").val();
    var lang = $("html").attr('lang');
    var rtl = (lang  == 'ar');

    // Disable Current element after click
    $(document).on('click','.disable_after_click',function(event) {
        setTimeout( () => $(this).attr('disabled','disabled') ,1);
    });

    $('button[type="submit"]').on('click', function() {
        setTimeout(() => $('button[type="submit"]').prop('disabled', true) , 1);
    });

    var deleteModalEl = document.getElementById('confirmDeleteModal');
    if(deleteModalEl !== null) {
        deleteModalEl.addEventListener('shown.bs.modal', function (event) {
            let action = event.relatedTarget.dataset.action;
            document.getElementById('confirmDeleteForm').setAttribute('action',action);
        });

        deleteModalEl.addEventListener('hidden.bs.modal', function (event) {
            document.getElementById('confirmDeleteForm').setAttribute('action','#');
        });
    }

    var payoutModalEl = document.getElementById('confirmPayoutModal');
    if(payoutModalEl !== null) {
        payoutModalEl.addEventListener('show.bs.modal', function (event) {
            payoutModalEl.querySelector('.modal-content').classList.add('loading');
            let payout_id =event.relatedTarget.dataset.payout_id;
            let action = event.relatedTarget.dataset.action;
            document.getElementById('confirmPayoutForm').setAttribute('action',action);
            $('.confirm_text').addClass('d-none');
            let notify_elem = document.getElementById("send_notification_to_user");
            if(notify_elem !== null) {
                notify_elem.classList.add('d-none');
            }

            let confirm_payout_btn_elem = document.getElementById("confirm-payout-btn");
            if(confirm_payout_btn_elem !== null) {
                confirm_payout_btn_elem.classList.remove('d-none');
            }
            
            document.getElementById("payout_id").value = payout_id;
            let payout_details = JSON.parse(event.relatedTarget.dataset.payout_details);
            var HTMLContent = "";
            $('#pay_hotel-btn').addClass('d-none');
            if(payout_details.has_payout_data) {
                $('.payout_text').removeClass('d-none');
                for(const [key, value] of Object.entries(payout_details)){
                    if(key != 'has_payout_data') {
                        HTMLContent += "<tr><td>"+ key + "</td><td>"+ value + "</td></tr>"
                    }
                }
            }
            else if(payout_details.is_refund) {
                $('.refund_text').removeClass('d-none');
            }
            else {
                if(!payout_details.upcoming_payout) {
                    $('.send_notification_to_user').removeClass('d-none');
                }

                HTMLContent += "<tr><td class='text-center'>"+ payout_details.payout_message + "</td></tr>";
                document.getElementById("confirm-payout-btn").classList.add('d-none');
            }
            if (payout_details['Payout Method'] == 'Pay at Hotel') {
                $('.payhotel_text').removeClass('d-none');
                $('.payout_text').addClass('d-none');
                $('.refund_text').addClass('d-none');
                $('#confirm-payout-btn').addClass('d-none');
                $('#pay_hotel-btn').removeClass('d-none');
            }

            document.getElementById("payout-info").innerHTML =  HTMLContent;
            payoutModalEl.querySelector('.modal-content').classList.remove('loading');
        });

        payoutModalEl.addEventListener('hidden.bs.modal', function (event) {
            document.getElementById('confirmPayoutForm').setAttribute('action','#');
        });
    }

    let autoCompleteInputs = document.getElementsByClassName('autocomplete-input');
    let autocomplete = [];
    if(autoCompleteInputs.length > 0) {
        for (let index = 0 ;index < autoCompleteInputs.length; index++) {
            autocomplete[index] = new google.maps.places.Autocomplete(autoCompleteInputs[index], {types: ['(cities)']});
            autocomplete[index].setFields(['geometry','place_id']);
            google.maps.event.addListener(autocomplete[index], 'place_changed', () => {
                var searchPlace = autocomplete[index].getPlace();
                if(!searchPlace.geometry) {
                    return false;
                }
                $('#viewport').val(searchPlace.geometry.viewport);
                $('#latitude').val(searchPlace.geometry.location.lat());
                $('#longitude').val(searchPlace.geometry.location.lng()); 
                $('#place_id').val(searchPlace.place_id);
            });
        }
    }

    setTimeout( () => {
        $('.select-with-add').select2({
            width: '100%',
            tags: true,
        });
    },500);
});
