export default {
    otherData: {
        form_modified: false,
        listing_progress: '',
        completed_percent: '0',
        current_tab: '',
        hotel: {
            hotel_address:{},
            hotel_photos:[],
        },
        original_hotel: {},
        hotel_rooms: {},
        removed_rooms: [],
        hotel_errors: {},
        hotel_id: 0,
        user_id: 0,
        current_step: {},
        removed_photos: [],
        step_data: {},
        autocomplete_used:false,
        location_found:true,
        map:{},
        location_marker:'',
        now: new Date,
        calendar: {},
        calendar_data: {
            start_date: "",
            formatted_start_date: "",
            end_date: "",
            formatted_end_date: "",
            notes: '',
            status: 'available',
            calendar_id : '',
        },
        locale : '',
        translations : [],
        removed_translations : [],
        countries: [],
        cities: [],
        temp_hotel_photos: [],
    },
    components: {
    },
    vueMethods: {
        updateServiceCharge() {
            if(this.hotel.service_charge_type != 'fixed') {
                this.hotel.service_charge = parseInt(this.hotel.service_charge).toFixed(0);
            }
            if(isNaN(this.hotel.service_charge)) {
                this.hotel.service_charge = null;
            }
        },
        updatePropertyTax() {
            if(this.hotel.property_tax_type != 'fixed') {
                this.hotel.property_tax = parseInt(this.hotel.property_tax).toFixed(0);
            }
            if(isNaN(this.hotel.property_tax)) {
                this.hotel.property_tax = null;
            }
        },
        updateFormStatus(status = true) {
            // Update form_modified status to save step details
            this.form_modified = status;
        },
        updateListingProgress() {
            this.listing_progress = {width:this.completed_percent+'%' };
        },
        updateSavedData() {
            // Apply Saved data to original data
            this.original_hotel = cloneObject(this.hotel);
            this.completed_percent = this.hotel.completed_percent;
        },
        navigationChanged(step_name) {
            this.current_tab = step_name;
            setGetParameter('current_tab',step_name);
            if(this.current_tab == 'photos') {
                this.initDraggableSection();
            }
        },
        goToStep(step) {
            this.current_step = step;
            this.navigationChanged(step.step);
        },
        getCurrentStepData() {
            return new FormData(hotel_form);
        },
        saveStep() {
            let data_params = this.getCurrentStepData();
            let url = routeList.update_hotel;
            this.error_messages = {};
            let callback_function = (response_data) => {
                if(response_data.error) {
                    if (this.current_tab != 'photos') {
                        this.error_messages = response_data.error_messages;
                        let content = {title: 'Failed',message: response_data.status_message};
                        flashMessage(content,'danger');
                        return false;
                    }
                    document.getElementById('upload_photos').value = '';
                    this.removed_photos = [];
                    this.step_data = response_data.step_data;
                    var self = this;
                    self.hotel.hotel_photos = self.temp_hotel_photos;
                    return true;
                }
                document.getElementById('upload_photos').value = '';
                this.removed_photos = [];
                this.hotel = response_data.hotel;
                this.step_data = response_data.step_data;
                this.updateSavedData();
                this.updateFormStatus(false);
                this.updateListingProgress();
                this.updateServiceCharge();
                this.updatePropertyTax();
                $('#upload_photos').val('');
                let content = {title: response_data.status,message: response_data.status_message};
                flashMessage(content,'success');
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        initAutocomplete() {
            // Add Google Autocomplete to address input
            let address_line = document.getElementById('address_line_1');
            if(!address_line) {
                return false;
            }
            let autocomplete = new google.maps.places.Autocomplete(address_line);
            autocomplete.setFields(['address_component','geometry','place_id']);
            autocomplete.addListener('place_changed', () => {
                let place = autocomplete.getPlace();
                this.fetchMapAddress(place);
                this.autocomplete_used = true;
            });
        },
        resetAutoComplete() {
            // Reset Google Autocomplete when edit address line field
            this.hotel.hotel_address.latitude = '';
            this.hotel.hotel_address.longitude = '';
            this.autocomplete_used = false;
        },
        fetchMapAddress(data, from_autocomplete = true) {
            // Fetch Location details after choose address from autocomplete
            let hotel_address = this.hotel.hotel_address;
            let componentForm = {
                neighborhood : 'long_name',
                street_number : 'short_name',
                route : 'long_name',
                sublocality_level_1 : 'long_name',
                sublocality : 'long_name',
                locality : 'long_name',
                administrative_area_level_1 : 'long_name',
                country : 'short_name',
                postal_code : 'short_name'
            };

            let street_number = '';
            let place = data;

            hotel_address.address_line_1 = '';
            for (let i = 0; i < place.address_components.length; i++) {
                let addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    let val = place.address_components[i][componentForm[addressType]];
                    if(addressType == 'street_number') {
                        street_number = val;
                    }
                    if(addressType == 'route') {
                        hotel_address.address_line_1 = street_number + ' ' + val;
                    }
                    if(addressType == 'neighborhood' && hotel_address.address_line_1 == '') {
                        hotel_address.address_line_1 = val;
                    }
                    if(addressType == 'postal_code') {
                        hotel_address.postal_code = val;
                    }
                    if(addressType == 'locality') {
                        hotel_address.city = val;
                    }
                    if(addressType == 'administrative_area_level_1') {
                        hotel_address.state = val;
                    }
                    if(addressType == 'country') {
                        hotel_address.country_code = val;
                    }
                }
            }
            hotel_address.latitude = place.geometry.location.lat();
            hotel_address.longitude = place.geometry.location.lng();
            // this.moveMarker(room_address.latitude, room_address.longitude);
            
            // if(from_autocomplete) {
            //     this.location_found = true;
            //     if(room_address.address_line_1 == '') {
            //         this.location_found = false;
            //     }                
            // }

            this.hotel.hotel_address = hotel_address;
        },
        initMap() {
            // Initialize Location map
            let hotel_address = cloneObject(this.hotel.hotel_address);
            let map_el = document.getElementById('location_map');
            if(!map_el || IN_ONLINE != true) {
                return false;
            }
            if(!hotel_address.latitude || !hotel_address.longitude) {
                hotel_address.latitude = '24.487249';
                hotel_address.longitude = '54.357464';
            }

            this.map = new google.maps.Map(map_el, {
                center: { lat: parseFloat(hotel_address.latitude), lng: parseFloat(hotel_address.longitude) },
                zoom: 16,
                scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                disableDefaultUI: true,
                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.SMALL
                }
            });

            // Limit the zoom level
            google.maps.event.addListener(this.map, 'zoom_changed', () => {
                if (this.map.getZoom() < 3) this.map.setZoom(3);
            });
            this.initMarker();
        },
        initMarker() {
            // Initialize map Pin and add dragend listener to update location based on pin
            let hotel_address = this.hotel.hotel_address;
            if(!hotel_address.latitude || !hotel_address.longitude) {
                this.map.setZoom(3);
                return false;
            }

            this.location_marker = new google.maps.Marker({
                map : this.map,
                draggable : true,
                animation : google.maps.Animation.DROP,
                position : new google.maps.LatLng(
                    hotel_address.latitude, hotel_address.longitude
                ),
                icon :new google.maps.MarkerImage(
                    APP_URL+'/images/icons/map_pin.png',
                    new google.maps.Size(34, 50),
                    new google.maps.Point(0, 0),
                    new google.maps.Point(17, 50)
                )
            });

            const geocoder = new google.maps.Geocoder();
            // Update Location after drag end
            google.maps.event.addListener(this.location_marker, 'dragend', () => {
                let marker_location = this.location_marker.getPosition();

                let latlng = {
                    lat: marker_location.lat(),
                    lng: marker_location.lng()
                };
                geocoder.geocode({ location: latlng }, (results, status) => {
                    if (status === "OK") {
                        if (results[0]) {
                            this.fetchMapAddress(results[0],false);
                            this.location_found = true;
                            this.map.setZoom(16);
                            this.map.setCenter(latlng);
                        }
                    }
                });

                this.hotel.hotel_address.latitude = marker_location.lat();
                this.hotel.hotel_address.longitude = marker_location.lng();
            });
        },
        moveMarker(lat, lng) {
            let latlng = new google.maps.LatLng(lat, lng);
            this.map.panTo(latlng);
            this.map.setZoom(16);

            if(this.location_marker == '') {
                this.initMarker();
            }
            else {
                this.location_marker.setPosition(latlng);
            }
        },
        previewPhoto(event) {
            let element = this.$refs['file'];
            var image_count = element.files.length + this.hotel.hotel_photos.length;
            if (image_count > 10) {
                this.error_messages.photos = 'you can upload only 10 Images';
                document.getElementById('upload_photos').value = '';
                return false;
            }
            let self = this;
            for (let i = 0; i < element.files.length; i++) {
                let file = element.files[i];
                let reader = new FileReader();
                reader.onload = function (e) {
                    let selectedImage = {
                        id: '',
                        temp_photo: true,
                        image_src: e.target.result,
                        is_error: e.total/1024 > 5120,
                    };
                    self.hotel.hotel_photos.push(selectedImage);
                }
                reader.readAsDataURL(file);
            }
            this.temp_hotel_photos = this.hotel.hotel_photos;
            this.saveStep();
        },
        updatePhotoOrder() {
            let new_image_order = $(".image_id").map(function() {
                return $(this).val();
            }).get();

            let url = routeList.update_photo_order;
            let data_params = {hotel_id : this.hotel_id,user_id : this.hotel.user_id,image_order_list:new_image_order};
            let callback_function = (response_data) => {
                let content = {title: response_data.status,message: response_data.status_message};
                flashMessage(content,'success');
            };
            this.makePostRequest(url,data_params,callback_function);
        },
        deletePhoto(index) {
            this.removed_photos.push(this.hotel.hotel_photos[index].id);
            this.hotel.hotel_photos.splice(index, 1);
        },
        initDraggableSection() {
            if($(window).width() < 767) {
                return false;
            }
            $('.hotel_image-row').sortable({
                axis: "x,y",
                revert: true,
                scroll: true,
                placeholder: 'sortable-placeholder',
                cursor: 'move',
                tolerance:'pointer',
                containment: $('.hotel_image-container'),
                start: () => {
                    $('.hotel_image-row').addClass('sorting');
                },
                update: () => {
                    this.updatePhotoOrder();
                },
                stop: () => {
                    $('.hotel_image-row').removeClass('sorting');  
                }
            });
        },
        updateListingStatus(status) {
            let data_params = {hotel_id : this.hotel_id,user_id : USER_ID,step : 'room_status', status:status};
            let url = routeList.update_listing;
            let callback_function = (response_data) => {
                if(response_data.room.status == 'pending') {
                 window.location.reload();
                }
                let content = {title: response_data.status,message: response_data.status_message};
                flashMessage(content,'success');
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        addNewTranslation(locale) {
            let translation = [];
            translation['locale'] = locale;
            this.translations.push(translation);
            this.locale = '';
        },
        canDisplayLanguage(locale) {
            if(locale == DEFAULT_LANGUAGE) {
                return false;
            }
            let index = this.translations.findIndex((x) => x.locale == locale);
            return index == -1;
        },
        removeTranslation(locale,index) {
            this.translations.splice(index, 1);
            this.removed_translations.push(locale);
        },
        getExtraGuests(index) {
            var guests = parseInt(this.hotel_rooms[index].guests);
            var max_guests = parseInt(this.hotel_rooms[index].max_guests);
            var extra_guests = max_guests - guests;
            if(extra_guests > 0){
                var number = max_guests - this.hotel_rooms[index].hotel_room_prices.length;
                if(number > 0) {
                     for(let i = 0; i < number; i++) {
                        this.hotel_rooms[index].hotel_room_prices.push({
                            extra_price: ''
                        });
                    }
                }
                return extra_guests;
            }
            return 0;
        },
        subRoomAmenities(index,amenity_id) {
            var sub_room_amenities = this.hotel_rooms[index].amenities;
            if(sub_room_amenities == undefined){
                return false;
            }
            if(sub_room_amenities.includes(amenity_id.toString())){
              return true;
            }
            return false;
        },
        checkRoomAmenities(index) {
            var amenities = getSelectedData('.amenities_'+index);
            this.hotel_rooms[index].amenities = amenities;
        },
        openCancelModal(model) {
            openModal(model);
        },
        openResubmitModal(modal) {
            if (this.hotel.admin_status == 'resubmit') {
                openModal(modal);
            }
        },
/*        getHotelPhotoErrorIndex(id) {
            this.error_messages.photo
        },*/
        registerEvents() {
            var current_tab = getParameterByName('current_tab');
            current_tab = checkInValidInput(current_tab) ?'description' :  current_tab ;
            this.navigationChanged(current_tab);

            $('.hotel_navigation').find('[href="#'+current_tab+'"]').tab('show');
            
            let self = this;
            // Event to update amenities scope when user check/Uncheck values
            $(document).on('change', '.amenities', function() {
                let amenities = getSelectedData('.amenities');
                self.hotel.amenities = amenities.toString();
            });

            // Event to update guest accesses scope when user check/Uncheck values
            $(document).on('change', '.guest_accesses', function() {
                let guest_accesses = getSelectedData('.guest_accesses');
                self.hotel.guest_accesses = guest_accesses.toString();
            });

            // Event to update house rules scope when user check/Uncheck values
            $(document).on('change', '.hotel_rules', function() {
                let hotel_rules = getSelectedData('.hotel_rules');
                self.hotel.hotel_rules = hotel_rules.toString();
            });
        },
        loadFunctions() {
            this.updateSavedData();
            this.updateListingProgress();
            this.updateServiceCharge();
            this.updatePropertyTax();
            if(IN_ONLINE) {
                // this.initAutocomplete();
                setTimeout( () => {
                    // this.initMap();
                });
            }
        },
    },
};