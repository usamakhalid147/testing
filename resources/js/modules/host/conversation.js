export default {
	otherData: {
        auth_user: {},
		chat_messages: {},
        message: '',
        message_id: 0,
        reservation_id: '',
        list_type: 'hotel',
        user_type: 'Host',
	},
	components: {},
	vueMethods: {
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
		sendMessage() {
            var url = routeList.send_message;
            var data_params = this.getDataParams();

            var callback_function = (response_data) => {
                this.message = '';
                if(response_data.status) {
                    this.chat_messages.unshift(response_data.data);
                }
                else {
                    this.errors['inbox_message'] = response_data.status_message;
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
		loadFunctions() {
			// this.getMessages();
		},
		registerEvents() {

		},
	},
};