export default {
	otherData: {
		home_popular_cities: [],
		recommended_hotels: [],
		featured_cities: [],
		date_picker: [],
	},
	components: {
	},
	computed:{
		popular_text_show : function() {
			for(const [key,place] of Object.entries(this.search_results)) {
				if(place.hotels > 0) {
					return true;
				}
			}
			return false;
		}
	},
	methods:{
		async getHomeData() {
            var data_params = {};
            
            let callback_function = (response_data) => {
                if(response_data.status) {
                    this.recommended_hotels = response_data.recommended_hotels;
                    this.featured_cities = response_data.featured_cities;
                    this.home_popular_cities = response_data.home_popular_cities;
                    this.search_results = response_data.home_popular_cities;
                    setTimeout( () => {
				        updateSlider('.recommended_hotel','recommended');
                    });
                }
            }

            this.makePostRequest(routeList.get_home_data,data_params,callback_function);
        },
		initCheckinDatePicker() {
			var me = this;
            let flatpickrOptions = {
                minDate: "today",
                altInput: true,
                altFormat: flatpickrFormat,
                dateFormat: "Y-m-d",
                onClose: function (selectedDates, dateStr, instance) {
                	var selectedDay = selectedDates[0];
                    selectedDay.setDate(selectedDay.getDate() + 1);
                    me.date_picker['checkout'].set('minDate',selectedDay);
                    me.date_picker['checkout'].open();
                },
            };
            this.date_picker['checkin'] = flatpickr('.home_checkin', flatpickrOptions);
		},
		initCheckoutDatePicker() {
			var me = this;
            let flatpickrOptions = {
                minDate: "today",
                altInput: true,
                altFormat: flatpickrFormat,
                dateFormat: "Y-m-d",
                onClose: function (selectedDates, dateStr, instance) {                    
                },
            };
            this.date_picker['checkout'] = 	flatpickr('.home_checkout', flatpickrOptions);
		},
		loadFunctions() {
			this.initCheckinDatePicker();
			this.initCheckoutDatePicker();
		},
		registerEvents() {
        	updateSlider('#home-carousel','home');
			this.getHomeData();
		}
	},
};