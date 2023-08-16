import InboxChat from './../components/InboxChatComponent.vue';

export default {
	otherData: {
        chat_messages: [],
        message: '',
        message_id: 0,
        user_type: 'Guest',
        requestDetails: {
            message : '',
            reason : '',
            agree_terms : false,
        },
        showOfferForm: false,
        offerDetails: {
            id:'',
            listing: '',
            checkin: '',
            checkout: '',
            guests: '',
            currency_code: '',
            price: '',
            show_price_details: false,
            pricing_form: [],
        },
        status_message: '',
        email : '',
        reservation_id : '',
        can_share_itinerary :false,
        list_type : 'hotel',
    },
    components: {
    	InboxChat
	},
	methods: {
        shareItinerary() {
            this.isLoading = true;
            var data_params = {email : this.email,reservation_id : this.reservation_id,list_type:this.list_type};
            var url = routeList.share_itinerary;
            var callback_function = (response_data) => {
                if (!response_data.status) {
                    this.error_messages.email = response_data.error_message;
                    return false;
                }
                this.isLoading = false;
                this.email = '';
                let content = {title: 'success',message: response_data.status_message};
                flashMessage(content,'success');
                this.can_share_itinerary = false;
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        sendMessage() {
            var url = routeList.send_message;
            var data_params = this.getDataParams();

            var callback_function = (response_data) => {
                this.message = '';
                if(response_data.status) {
                    this.chat_messages.push(response_data.data);
                }
                else {
                    this.errors['inbox_message'] = response_data.status_message;
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        getDataParams(type = 'conversation') {
            let data_params = {
                user_type:this.user_type,
                message_id:this.message_id,
                message:this.message,
            };
            if(type == 'special_offer' || type == 'remove_special_offer') {
                data_params = {
                    id : this.offerDetails.id,
                    type:type,
                    user_type:this.user_type,
                    message_id:this.message_id,
                    room_id : this.offerDetails.listing,
                    host_id : this.user_id,
                    checkin: this.offerDetails.checkin,
                    checkout: this.offerDetails.checkout,
                    guests: this.offerDetails.guests,
                    currency_code: this.offerDetails.currency_code,
                    price: this.offerDetails.price,
                };
            }
            
            return data_params;
        },
        openRequestPopup(type) {
            openModal(type+'Modal');
        },
        requestAction(type) {
            let url = routeList.request_action;
            let data_params = this.getDataParams();
            data_params['type'] = type;
            data_params['reason'] = this.requestDetails.reason;
            data_params['message'] = this.requestDetails.message;

            let callback_function = (response_data) => {
                if(response_data.status) {
                    if(response_data.status_action == 'reload') {
                        window.location.reload();
                    }
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        calculatePrice() {
            let data_params = this.getDataParams('special_offer');
            let url = routeList.reserve_calculation;
            let callback_function = (response_data) => {
                if (!response_data.status) {
                    this.status_message = response_data.status_message;
                }
                this.offerDetails.is_available = response_data.price_details.is_available;
                this.offerDetails.pricing_form = response_data.pricing_form;
                setTimeout(() => {
                    let tooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    });
                },10);
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        sendSpecialOffer() {
            let url = routeList.send_special_offer;
            let data_params = this.getDataParams('special_offer');
            this.offerDetails.error_message = '';
            let callback_function = (response_data) => {
                if(response_data.error) {
                    this.offerDetails.error_message = response_data.error_message;
                }
                else if(!response_data.status) {
                    let content = {title: response_data.status_text,message: response_data.status_message};
                    flashMessage(content,'danger');
                } else if (response_data.status == 'reload'){
                    window.location.reload();
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        removeSpecialOffer() {
            let url = routeList.send_special_offer;
            let data_params = this.getDataParams('remove_special_offer');
            this.offerDetails.error_message = '';
            let callback_function = (response_data) => {
                if(response_data.error) {
                    // this.offerDetails.error_message = response_data.error_message;
                }
                else if(!response_data.status) {
                    // let content = {title: response_data.status_text,message: response_data.status_message};
                    // flashMessage(content,'danger');
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        registerEvents() {
            let resetReason = () => {
                this.requestDetails.message = '';
                this.requestDetails.reason = '';
                this.requestDetails.agree_terms = false;
            };
            attachEventToClass('.request_action-modal',resetReason,'hidden.bs.modal');
            
            let flatpickrOptions = {
                minDate: 'today',
                altInput: true,
                clickOpens: false,
                altFormat: flatpickrFormat,
                dateFormat: "Y-m-d",
            };
            if(this.offerDetails.checkin != '') {
                flatpickrOptions['defaultDate'] = this.offerDetails.checkin;
                flatpickr('#offer_checkin', flatpickrOptions);
                flatpickrOptions['defaultDate'] = this.offerDetails.checkout;
                flatpickr('#offer_checkout', flatpickrOptions);
            }
        },
	},
};