export default {
	otherData: {
        show_invite_details: false,
        email : '',
        pending_recruited_users : [],
        recruited_users : [],
        available_credit : 0,
    },
    components: {
    },
	methods: {
        copyText(selector) {
            let element = document.querySelector(selector);
            if(element) {
                element.select();
                document.execCommand('copy');
            }
        },
        inviteUser() {
            this.isLoading = true;
            var data_params = {email : this.email};
            var url = routeList.invite_user;
            this.error_messages.email = false;
            var callback_function = (response_data) => {
                if(response_data.error) {
                    this.error_messages.email = response_data.error_message;
                    return false;
                }

                if(!response_data.status) {
                    let content = {title: response_data.error_message,message: response_data.status_message};
                    flashMessage(content,'danger');    
                    return false;
                }
                this.isLoading = false;
                this.email = '';
                let content = {title: 'success',message: response_data.status_message};
                flashMessage(content,'success');
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        getReferrals() {
            let url = routeList.get_referral;
            this.isLoading = true;
            let callback_function = (response_data) => {
                this.pending_recruited_users = response_data.pending_recruited_users;
                this.recruited_users = response_data.recruited_users;
                this.available_credit = response_data.available_credit;
            };

            this.makePostRequest(url,{},callback_function);
        },
        loadFunctions() {
            this.getReferrals();
        },
    },
};