export default {
    otherData: {
        original_user: {
            user_information: {
                language_array : {
                    code: [],
                    display_list: ''
                }
            }
        },
        user: {
            user_information: {
                language_array : {
                    code: []
                }
            }
        },
        user_birthday: {},
        section_shown: false,
        show_legal_name: false,
        show_gender: false,
        show_dob: false,
        show_email_addr: false,
        show_phone_number: false,
        show_user_document_src : false,
        show_verification_code: false,
        verfication_data: {
            code : '',
            error_message : ''
        },
        show_address: false,
        current_password: '',
        password: '',
        password_confirmation: '',
        show_password: false,
        showUpdateOptions: false,
        show_user_language: false,
        show_user_currency: false,
        show_timezone: false,
        currentPage: '',
        transaction_history: {
            filter: 'completed',
            data: [],
            summary_amount: CURRENCY_SYMBOL+'0.00',
        },
        list_type : 'hotel',
    },
    components: {
    },
    methods: {
        getType(type) {
            this.list_type = type;
        },
        toggleSection(section,action) {
            this[section] = action;
            if(!action) {
                this.user = cloneObject(this.original_user);
                this.section_shown = false;
            }
            else {
                this.section_shown = true;
            }
        },
        getEditProfileClass(status) {
            if(!status && this.section_shown) {
                return 'disabled pointer-none';
            }
        },
        getBasicDataParams() {
            var data_params = {};
            data_params['user_id'] = this.user.id;
            return data_params;
        },
        updateUserProfile() {
            var url = routeList.update_profile;

            var data_params = this.getBasicDataParams();
            data_params['about'] = this.user.user_information.about;
            data_params['work'] = this.user.user_information.work;
            data_params['location'] = this.user.user_information.location;
            data_params['language'] = $('.language').val();
            data_params['data_from'] = 'view_profile';

            var callback_function = (response_data) => {
                if(response_data.status) {
                    this.showUpdateOptions = false;
                    this.original_user = response_data.user;
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        saveUserDocument(e) {
            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            };

            let formData = new FormData();
            formData.append('file', e.target.files[0]);
            this.isLoading = true;
            axios.post(routeList.upload_user_document, formData, config)
            .then((response) => {
                window.location.reload();
            });
        },
        getPersonalInfoData(type) {
            var data_params = {};
            if(type == 'legal_name') {
                data_params['first_name']   = this.user.first_name;
                data_params['last_name']    = this.user.last_name;
            }
            if(type == 'gender') {
                data_params['gender']       = this.user.user_information.gender;
            }
            if(type == 'dob') {
                data_params['birthday_year'] = this.user_birthday.year;
                data_params['birthday_month'] = this.user_birthday.month;
                data_params['birthday_day']  = this.user_birthday.day;
            }
            if(type == 'email_addr') {
                data_params['email']        = this.user.email;
            }
            if(type == 'address') {
                data_params['address_line_1'] = this.user.user_information.address_line_1;
                data_params['address_line_2'] = this.user.user_information.address_line_2;
                data_params['city'] = this.user.user_information.city;
                data_params['state'] = this.user.user_information.state;
                data_params['country_code'] = this.user.user_information.country_code;
                data_params['postal_code'] = this.user.user_information.postal_code;
            }
            if(type == 'user_language') {
                data_params['user_language'] = this.user.user_language;
            }
            if(type == 'user_currency') {
                data_params['user_currency'] = this.user.user_currency;
            }
            if(type == 'timezone') {
                data_params['timezone'] = this.user.timezone;
            }
            if(type == 'send_otp' || type == 'verify_otp') {
                data_params['country_code'] = this.user.country_code;
                data_params['phone_number'] = this.user.phone_number;
                if(type == 'verify_otp') {
                    data_params['verification_code'] = this.verfication_data.code;
                }
            }
            if(type == 'password') {
                data_params['current_password'] = this.current_password;
                data_params['password'] = this.password;
                data_params['password_confirmation'] = this.password_confirmation;
            }
            return data_params;
        },
        saveProfileData(section,type) {
            var url = routeList.update_profile;

            var data_params = this.getBasicDataParams();
            data_params['data_from'] = section;
            data_params['data_type'] = type;
            data_params = cloneObject(this.getPersonalInfoData(type),data_params);
            
            var callback_function = (response_data) => {
                this.verfication_data.error_message = '';
                if(response_data.status) {
                    if(type == 'password') {
                        this.current_password = this.password = this.password_confirmation = '';
                    }
                    this['show_'+type] = false;
                    this.section_shown = false;
                    this.user = response_data.user;
                    this.original_user = cloneObject(response_data.user);
                    let content = {title: response_data.status_text,message: response_data.status_message};
                    flashMessage(content);
                }
                else {
                    this.verfication_data.error_message = response_data.error_message;
                    let content = {title: response_data.error_text,message: response_data.error_message};
                    flashMessage(content,'danger');
                }
            };

            this.makePostRequest(url,data_params,callback_function);
            location.reload();
        },
        removeProfilePicture() {
            this.isLoading = true;
            axios.post(routeList.remove_profile_picture)
            .then((response) => {
                window.location.reload();
            });
        },
        saveProfilePicture(e) {
            const input = e.target;
            const file = input.files[0];
            const fileSize = file.size / (1024 * 1024); // in MB
            if (fileSize > 1) {
                flashMessage("File upload Size is greater than 1mb, try uploading a smaller image", 'danger');
                input.value = ""; // clear the file input
                return;
            }

            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            };

            let formData = new FormData();
            formData.append('file', e.target.files[0]);
            this.isLoading = true;
            axios.post(routeList.update_profile_picture, formData, config)
            .then((response) => {
                window.location.reload();
            });
        },
        numberVerification(type) {
            var url = routeList.number_verification;

            var data_params = this.getBasicDataParams();
            data_params['type'] = type;
            data_params = cloneObject(this.getPersonalInfoData(type),data_params);
            this.verfication_data.error_message = '';
            var callback_function = (response_data) => {
                if(response_data.status) {
                    this.verfication_data.status_message = response_data.status_message;
                    if(type == 'send_otp') {
                        if(response_data.verify_code != '') {
                            this.verfication_data.code = response_data.verify_code;
                        }
                        this.show_phone_number = false;
                        this.show_verification_code = true;
                    }
                    if(type == 'verify_otp') {
                        this.show_verification_code = false;
                        this.section_shown  = false;
                        this.user           = response_data.user;
                        this.original_user  = cloneObject(response_data.user);
                    }
                }
                else {
                    this.verfication_data.error_message = response_data.status_message;
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        switchTab(target) {
            this.transaction_history.filter = target;
            this.resetAndGetPayoutData();
        },
        resetAndGetPayoutData() {
            let date = new Date;
            this.transaction_history.start_month = 1;
            this.transaction_history.start_year = 2020;
            this.transaction_history.end_month = date.getMonth()+1;
            this.transaction_history.end_year = date.getFullYear();
            this.transaction_history.summary_amount = CURRENCY_SYMBOL+'0.00';
            this.transaction_history.data = [];
            this.getTransactionHistory();
        },
        getTransactionHistory() {
            var url = routeList.transaction_history;
            var data_params = this.transaction_history;
            var callback_function = (response_data) => {
                if(response_data.status) {
                    this.transaction_history.data = response_data.data;
                    this.transaction_history.summary_amount = CURRENCY_SYMBOL+response_data.summary_amount;
                    setTimeout(() => {
                        $('#transaction_history').dataTable({
                            "dom": '<"row"<"col text-center"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>r<"table-responsive mt-2"t><"row mt-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                            "ordering": false,
                        });
                    });
                }
            };
            this.makePostRequest(url,data_params,callback_function);
        },
        loadFunctions() {
            console.log(this.currentPage);
            if(this.currentPage == 'transactions') {
                this.resetAndGetPayoutData();
            }
        },
    },
};