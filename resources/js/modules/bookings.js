export default {
	otherData: {
        reservations: [],
        user_type: 'Guest',
        active_tab: 'current',
        selectedReservation: {},
        cancelDetails: {
            cancelReason : '',
            cancelMessage : '',
        },
    },
    components: {
	},
	methods: {
        switchTab(target) {
            this.active_tab = target;
            this.getReservations();
        },
        getReservations() {
            setGetParameter('type',this.active_tab);
            var url = routeList.get_reservations;
            var data_params = {type: this.active_tab,user_type: this.user_type};
            var callback_function = (response_data) => {
                if(response_data.status) {
                    this.reservations = response_data.data;
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        OpenCancelModal(index) {
            this.selectedReservation = this.reservations[index];
            document.getElementById('cancel-form').action = this.selectedReservation.cancel_url;
            openModal('cancelReservationModal');
        },
        loadFunctions() {
            this.getReservations();
        },
    },
};