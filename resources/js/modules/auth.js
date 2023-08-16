export default {
	otherData: {
        mobile_login_data: {
            country_code: 'US',
            phone_number: '',
            show_verification_code: false,
            show_resend_btn: false,
            error_message: '',
            status_message: '',
            verify_code: '',
            auth_code: '',
        },
        countries: [],
        cities: [],
        user: {
            email: '',
            first_name: '',
            last_name: '',
            password: '',
            password_confirmation: '',
            country: '',
            city: '',
            phone_number: '',
            title: '',
            company_name: '',
            company_tax_number: '',
            company_tele_phone_number: '',
            company_fax_number: '',
            address_line1: '',
            address_line2: '',
            company_state: '',
            company_city: '',
            company_country: '',
            company_pincode: '',
            company_website: '',
            company_email: '',
            dob: '',
            gender: '',
        },
        selected_country: '',
        selected_city: '',
        tab: 'profile',
        skip: false,
	},
	components: {
	},
	methods: {
        LoginWithPhoneNumber(type) {
            let url = routeList.authenticate_mobile;
            let data_params = {
                type: type,
                login_country_code: this.mobile_login_data.country_code,
                login_phone_number: this.mobile_login_data.phone_number,
                code: this.mobile_login_data.verify_code,
                auth_code: this.mobile_login_data.auth_code,
            };

            this.isLoading = true;
            this.mobile_login_data.error_message = '';
            let callback_function = (response_data) => {
                if(response_data.status) {
                    if(type == 'send_otp') {
                        if(response_data.verify_code != '') {
                            this.mobile_login_data.verify_code = response_data.verify_code;
                            this.mobile_login_data.auth_code = response_data.auth_code;
                        }
                        setTimeout( () => {
                            this.mobile_login_data.show_resend_btn = true;
                        },30000);
                        this.mobile_login_data.show_verification_code = true;
                    }
                }
                else {
                    this.mobile_login_data.error_message = response_data.status_message;
                    if(response_data.invalid_otp) {
                        return false;    
                    }
                    this.mobile_login_data.show_verification_code = false;
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        checkUserButtonIsDisabled() {
            var result = false;
            if (this.user.full_name == '' || this.user.email == '' || this.user.password == '' || this.user.password_confirmation == ''|| this.user.country == ''|| this.user.city == ''|| this.user.phone_number == undefined ||  this.user.manager_title == '') {
                result = true;
            }
            return result;
        },
        checkCompanyButtonIsDisabled() {
            var result = false;
            if (!this.terms_of_use ||!this.cookie_policy || !this.privacy_policy || !this.refund_and_cancellation_policy) {
                result = true;
            }
            return result;
        },
        nextStep(type='') {
            this.skip = type == 'skip' ? true : false;
            document.getElementById('signup').disabled = true;
            let data_params = new FormData(owner_signup_form);
            data_params.set('skip',type == 'skip' ? true : false);
            let url = routeList.host_signup;
            this.isLoading = true;
            this.error_messages = {};
            let callback_function = (response_data) => {
                this.isLoading = false;
                if(response_data.status == 'error') {
                    document.getElementById('signup').disabled = false;
                    this.error_messages = response_data.error_messages;
                    if (this.error_messages.first_name == '' || this.error_messages.last_name == '' || this.error_messages.email == '' || this.error_messages.password == '' || this.error_messages.password_confirmation == ''|| this.error_messages.country == ''|| this.error_messages.city == ''|| this.error_messages.phone_number == '') {
                        this.tab = 'profile';
                        var new_tab = this.tab == 'profile' ? 'company' : 'profile';
                        $('#'+new_tab).removeClass('active d-block');
                        $('#'+this.tab).addClass('active d-block');
                    } else {
                        this.tab = 'company';
                        var new_tab = this.tab == 'company' ? 'profile' : 'company';
                        $('#'+new_tab).removeClass('active d-block');
                        $('#'+this.tab).addClass('active d-block');
                    }
                    return false;
                }
                if(response_data.status == 'redirect') {
                    window.location.reload(response_data.redirect_url);
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        validateTab(tab) {
            let data_params = new FormData(owner_signup_form);
            var url = routeList.create_host_validation;

            let callback_function = (response_data) => {
                this.isLoading = false;
                if(response_data.status == 'error') {
                    document.getElementById('signup').disabled = false;
                    this.error_messages = response_data.error_messages;
                } else {
                    this.nextTab('company');
                }
            };
            this.makePostRequest(url,data_params,callback_function);
        },
        nextTab(tab) {
            var new_tab = tab == 'profile' ? 'company' : 'profile';
            $('#'+tab).removeClass('active d-block');
            $('#'+new_tab).removeClass('active d-block');
            this.tab = tab;
            setTimeout( () => {
                $('#'+tab).addClass('active d-block');
            });
        },
        registerEvents() {
            flatpickr('#dob', {
                altInput: true,
                maxDate: 'today',
                altFormat: flatpickrFormat,
                dateFormat: "Y-m-d",
            });
            /* if (this.error_messages != '') {
                if (this.error_messages.company_name != '' || this.error_messages.company_tax_number != '' || this.error_messages.company_tele_phone_number != '' || this.error_messages.company_fax_number != '' || this.error_messages.address_line1 != '' || this.error_messages.address_line2 != '' || this.error_messages.company_state != '' || this.error_messages.company_city != '' || this.error_messages.company_country != '' || this.error_messages.company_pincode != '' || this.error_messages.company_website != '' || this.error_messages.company_email != '') {
                    this.tab = 'company';
                }
                if (this.error_messages.first_name != '' || this.error_messages.last_name != '' || this.error_messages.email != '' || this.error_messages.password != '' || this.error_messages.password_confirmation != ''|| this.error_messages.country != ''|| this.error_messages.city != ''|| this.error_messages.phone_number != '') {
                    this.tab = 'profile';
                }
            }*/
        },
     },
};