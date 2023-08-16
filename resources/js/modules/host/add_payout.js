export default {
	otherData: {
        currentStep:1,
        totalSteps:3,
        address1: '',
        address2:'',
        city:'',
        state:'',
        postal_code:'',
        payout_method:'paypal',
        payout_country:'',
        payout_currency:'',
        paypal_email:'',
        bank_holder_name:'',
        bank_account_number:'',
        bank_name:'',
        bank_location:'',
        bank_code:'',
        branch_name:'',
        routing_number:'',
        branch_code:'',
        account_number:'',
        holder_name:'',
        ssn_last_4:'',
        kanji_address1:'',
        kanji_address2:'',
        kanji_city:'',
        kanji_state:'',
        kanji_postal_code:'',
        stripe_error_message:'',
        phone_number:'',
        payout_country_list: {},
        payout_currency_list: {},
        iban_req_countries: [],
        branch_code_req_countries: [],
        mandatory_fields: [],
        stripe_account_type:'custom',
        stripe:'',
        displayFormThreeError: false,
    },
    components: {
	},
	vueMethods: {
        resetErrorMessages() {
            this.error_messages = {};
        },
        createPayoutMethod() {
            var data_params = new FormData(createPayoutForm);
            var url = routeList.create_payout;
            this.isLoading = true;
            this.resetErrorMessages();
            var callback_function = (response_data) => {
                this.isLoading = false;
                if(response_data.error) {
                    this.error_messages = response_data.error_messages;
                    return false;
                }
                if(!response_data.status) {
                    let content = {title: response_data.status_text,message: response_data.status_message};
                    flashMessage(content,'danger');
                    return false;
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        prevStep() {
            this.resetErrorMessages();
            this.currentStep--;
            this.isLoading = false;
        },
        nextStep() {
            this.resetErrorMessages();
            let isValid = this.validateCurrentStepData();
            if(!isValid) {
                return false;
            }
            if(this.currentStep == 2 && this.payout_method == 'stripe' && this.stripe_account_type == 'express') {
                this.createPayoutMethod();
                return true;
            }
            this.isLoading = true;
            if(this.currentStep == this.totalSteps) {
                if(this.payout_method != 'stripe') {
                    this.createPayoutMethod();
                }

                return true;
            }
            this.currentStep++;
            this.isLoading = false;
        },
        validateMethodData() {
            let isValid = true;
            if(checkInValidInput(this.payout_country)) {
                this.error_messages['payout_country'] = true;
                isValid = false;
            }
            return (isValid == true);
        },
        validateAddressFormData() {
            let isValid = true;
            if(checkInValidInput(this.address1)) {
                this.error_messages['address1'] = true;
                isValid = false;
            }
            if(checkInValidInput(this.city)) {
                this.error_messages['city'] = true;
                isValid = false;
            }
            if(checkInValidInput(this.state)) {
                this.error_messages['state'] = true;
                isValid = false;
            }
            if(checkInValidInput(this.postal_code)) {
                this.error_messages['postal_code'] = true;
                isValid = false;
            }
            return (isValid == true);
        },
        validateStripeData() {
            this.stripe_error_message = '';
            if(checkInValidInput(this.payout_currency) || checkInValidInput(this.holder_name)) {
                this.stripe_error_message = this.translation_messages.please_fill_all_required_fields;
                return false;
            }

            if(this.payout_country == 'US' && checkInValidInput(this.ssn_last_4)) {
                this.stripe_error_message = this.translation_messages.please_fill_all_required_fields;
                return false;
            }

            var bankAccountParams = {
                country: this.payout_country,
                currency: this.payout_currency,
                account_number: this.account_number,
                account_holder_name: this.holder_name,
                account_holder_type: "individual"
            }

            if(is_iban.value == 'No') {
                if(is_branch_code.value == 'Yes') {
                    if(this.payout_country != 'GB' && this.payout_currency != 'EUR') {
                        if(checkInValidInput(this.routing_number) || checkInValidInput(this.branch_code)) {
                            this.stripe_error_message = this.translation_messages.please_fill_all_required_fields;
                            return false;
                        }
                        bankAccountParams.routing_number = this.routing_number +'-'+ this.branch_code;
                    }
                }
                else {
                    if(this.payout_country != 'GB' && this.payout_currency != 'EUR') {
                        if(checkInValidInput(this.routing_number)) {
                            this.stripe_error_message = this.translation_messages.please_fill_all_required_fields;
                            return false;
                        }
                        bankAccountParams.routing_number = this.routing_number;
                    }
                }
            }
            this.stripe.
            createToken('bank_account', bankAccountParams)
            .then((result) => {
                this.stripe_error_message = '';
                if (result.error) {
                    this.stripe_error_message = result.error.message;
                    this.isLoading = false;
                    return false;
                }

                document.getElementById("stripe_token").value = result.token.id;
                this.createPayoutMethod();
            });
            return true;
        },
        validatePayoutMethodData() {
            this.displayFormThreeError = false;
            let isValid = true;
            if(this.payout_method == 'stripe') {
                isValid = this.validateStripeData();
            }

            return (isValid == true);
        },
        validateCurrentStepData() {
            if(this.currentStep == 1) {
                return this.validateMethodData();
            }
            if(this.currentStep == 2) {
                return this.validateAddressFormData();
            }
            if(this.currentStep == 3) {
                return this.validatePayoutMethodData();
            }
        },
        loadFunctions() {
            this.stripe = Stripe(STRIPE_PUBLISH_KEY);
        },
	},
};