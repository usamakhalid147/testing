export default {
    otherData: {
        form_modified: false,
        listing_progress: '',
        completed_percent: '0',
        room: {
            room_address:{},
            room_photos:[],
            room_price:{},
            length_of_stay_rules:[],
            early_bird_rules:[],
        },
        original_room: {},
        room_id: 0,
        current_step: {},
        removed_photos: [],
        step_data: {},
        autocomplete_used:false,
        location_found:true,
        map:{},
        location_marker:'',
        currentBedRoomEditor: -1,
        room_beds: [],
        length_of_stay_options: [],
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
    },
    components: {
    },
    methods: {
    	updateBedEditor(index) {
            this.currentBedRoomEditor = index;
        },
        updateBeds(index,key,type) {
            if(type == 'decrease') {
                this.room_beds[index].bed_types[key].beds--;
                this.room_beds[index].total_beds--;
            }
            else {
                this.room_beds[index].bed_types[key].beds++;
                this.room_beds[index].total_beds++;
            }
        },
        updateBedRooms() {
            if(this.room.bedrooms <= this.room_beds.length) {
                let diff = this.room_beds.length - this.room.bedrooms;
                this.room_beds.splice(this.room.bedrooms,diff);
            }
            else {
                let index = this.room.bedrooms.length;
                let diff = this.room.bedrooms - this.room_beds.length;
                for (let i = 0; i < diff; i++) {
                    let room_bed = cloneObject(this.default_bed_type);
                    let bed_types = this.default_bed_type.bed_types.map(bed_types => cloneObject(bed_types));
                    room_bed['index'] = ++index;
                    room_bed['bed_types'] = bed_types;
                    this.room_beds.push(room_bed);
                }
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
            this.original_room = cloneObject(this.room);
            this.completed_percent = this.room.completed_percent;
        },
        navigationChanged(step_name) {
            setGetParameter('current_tab',step_name);
            if(step_name == 'calendar') {
                setTimeout(() => {
                    this.initFullCalendar();
                },1000);
            }
            else if(step_name == 'photos') {
                this.initDraggableSection();
            }
        },
        goToStep(step) {
            this.current_step = step;
            this.navigationChanged(step.step);
        },
        getCurrentStepData() {
            return new FormData(listingForm);
        },
        saveStep() {
            let data_params = this.getCurrentStepData();
            let url = routeList.update_listing;
            this.isLoading = true;
            this.error_messages = {};
            let callback_function = (response_data) => {
                this.isLoading = false;
                if(response_data.error) {
                    this.error_messages = response_data.error_messages;
                    return false;
                }
                document.getElementById('upload_photos').value = '';
                this.removed_photos = [];
                this.room = response_data.room;
                this.step_data = response_data.step_data;
                this.updateSavedData();
                this.updateFormStatus(false);
                $('#upload_photos').val('');
                let content = {title: response_data.status,message: response_data.status_message};
                flashMessage(content,'success');
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        canDisplayLengthOfStayRule(period) {
            let index = this.room.length_of_stay_rules.findIndex((rule) => rule.period == period);
            return index == -1;
        },
        addLengthOfStayRule() {
            this.room.length_of_stay_rules.push({
                id:'',
                period: '',
                discount: '',
            });
        },
        removeLengthOfStayRule(index) {
            this.room.length_of_stay_rules.splice(index, 1);
        },
        addEarlyBirdRule() {
            this.room.early_bird_rules.push({
                id:'',
                period: '',
                discount: '',
            });
        },
        removeEarlyBirdRule(index) {
            this.room.early_bird_rules.splice(index, 1);
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
            this.room.room_address.latitude = '';
            this.room.room_address.longitude = '';
            this.autocomplete_used = false;
        },
        fetchMapAddress(data, from_autocomplete = true) {
            // Fetch Location details after choose address from autocomplete
            let room_address = this.room.room_address;
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

            room_address.address_line_1 = '';
            for (let i = 0; i < place.address_components.length; i++) {
                let addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    let val = place.address_components[i][componentForm[addressType]];
                    if(addressType == 'street_number') {
                        street_number = val;
                    }
                    if(addressType == 'route') {
                        room_address.address_line_1 = street_number + ' ' + val;
                    }
                    if(addressType == 'neighborhood' && room_address.address_line_1 == '') {
                        room_address.address_line_1 = val;
                    }
                    if(addressType == 'postal_code') {
                        room_address.postal_code = val;
                    }
                    if(addressType == 'locality') {
                        room_address.city = val;
                    }
                    if(addressType == 'administrative_area_level_1') {
                        room_address.state = val;
                    }
                    if(addressType == 'country') {
                        room_address.country_code = val;
                    }
                }
            }
            room_address.latitude = place.geometry.location.lat();
            room_address.longitude = place.geometry.location.lng();
            this.moveMarker(room_address.latitude, room_address.longitude);
            
            if(from_autocomplete) {
                this.location_found = true;
                if(room_address.address_line_1 == '') {
                    this.location_found = false;
                }                
            }

            this.room.room_address = room_address;
        },
        initMap() {
            // Initialize Location map
            let room_address = cloneObject(this.room.room_address);
            let map_el = document.getElementById('location_map');
            if(!map_el || IN_ONLINE != true) {
                return false;
            }
            if(!room_address.latitude || !room_address.longitude) {
                room_address.latitude = '24.487249';
                room_address.longitude = '54.357464';
            }

            this.map = new google.maps.Map(map_el, {
                center: { lat: parseFloat(room_address.latitude), lng: parseFloat(room_address.longitude) },
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
            let room_address = this.room.room_address;
            if(!room_address.latitude || !room_address.longitude) {
                this.map.setZoom(3);
                return false;
            }

            this.location_marker = new google.maps.Marker({
                map : this.map,
                draggable : true,
                animation : google.maps.Animation.DROP,
                position : new google.maps.LatLng(
                    room_address.latitude, room_address.longitude
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

                this.room.room_address.latitude = marker_location.lat();
                this.room.room_address.longitude = marker_location.lng();
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
            this.saveStep();
            /*let element = this.$refs['file'];

            let self = this;
            for (let i = 0; i < element.files.length; i++) {
                let file = element.files[i];
                let reader = new FileReader();
                reader.onload = function (e) {
                    let selectedImage = {
                        id: '',
                        temp_photo: true,
                        image_src: e.target.result,
                    };
                    self.room.room_photos.push(selectedImage);
                }
                reader.readAsDataURL(file);
            }*/
        },
        updatePhotoOrder() {
            let new_image_order = $(".image_id").map(function() {
                return $(this).val();
            }).get();

            let url = routeList.update_photo_order;
            let data_params = {room_id : this.room_id,user_id : USER_ID,image_order_list:new_image_order};
            let callback_function = (response_data) => {

            };
            this.makePostRequest(url,data_params,callback_function);
        },
        deletePhoto(index) {
            this.removed_photos.push(this.room.room_photos[index].id);
            this.room.room_photos.splice(index, 1);
        },
        initDraggableSection() {
            if($(window).width() < 767) {
                return false;
            }
            $('.listing_image-row').sortable({
                axis: "x,y",
                revert: true,
                scroll: true,
                placeholder: 'sortable-placeholder',
                cursor: 'move',
                tolerance:'pointer',
                containment: $('.listing_image-container'),
                start: () => {
                    $('.listing_image-row').addClass('sorting');
                },
                stop: () => {
                    $('.listing_image-row').removeClass('sorting');
                    this.updatePhotoOrder();
                }
            });
        },
        updateListingStatus(status) {
            let data_params = {room_id : this.room_id,user_id : USER_ID,step : 'room_status', status:status};
            let url = routeList.update_listing;
            let callback_function = (response_data) => {
                if(response_data.room.status == 'pending') {
                 window.location.reload();
                }
                console.log(this.currentRouteName);
                if(this.currentRouteName == 'rooms') {

                }
                let content = {title: response_data.status,message: response_data.status_message};
                flashMessage(content,'success');
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        sentAdminApproval() {
            let data_params = {room_id : this.room_id,user_id : USER_ID,step : 'room_status',status : this.room.status, admin_status: 'pending',resubmit_reason: ''};
            let url = routeList.update_listing;
            let callback_function = (response_data) => {
                 window.location.reload();
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        initFullCalendar() {
            let calendarEl = document.getElementById('full_calendar');
            let calendarOptions = {
                headerToolbar: {
                    start: 'dayGridMonth',
                    center: 'title'
                },
                locale: userLanguage,
                themeSystem: 'bootstrap5',
                buttonIcons: {
                    prev: 'icon icon icon-left-chevron',
                    next: 'icon icon icon-right-chevron',
                },
                initialView: 'dayGridMonth',
                initialDate: this.now,
                selectable: true,
                selectOverlap: false,
                expandRows: true,
                allDaySlot: false,
                longPressDelay: 500,
                eventSources: [{
                    url: routeList.get_calendar_data,
                    extraParams: () => {
                        return {
                            user_id : this.user_id,
                            room_id : this.room_id,
                        };
                    },
                    success: function(response) {
                        if(response.status) {
                            return response.events;
                        }
                    }
                }],
                loading: (isLoading) => {
                    this.isLoading = isLoading;
                },
                select: (selectionInfo) => {
                    if(selectionInfo.start <= this.now) {
                        this.unselectCalendar();
                        let content = {title: "Failed",message: "Please Choose After Current Date"}; 
                        flashMessage(content,'danger');
                        return false;
                    }
                    this.updateFormFields(this.room_id, selectionInfo);
                },
                eventClick: (eventClickInfo) => {
                    let extendedProps = eventClickInfo.event.extendedProps;
                    if(extendedProps.source == 'Reservation') {
                        return false;
                    }
                    this.updateFormFields(extendedProps.room_id, eventClickInfo.event,extendedProps.price, extendedProps.status,extendedProps.notes,extendedProps.calendar_id);
                },
                eventContent: (arg) => {
                    console.log(arg);
                }
            };

            this.calendar = new FullCalendar.Calendar(calendarEl, calendarOptions);
            this.calendar.render();
        },
        updateCalendarEvent(type = '') {
            this.isLoading = true;
            let data_params = this.calendar_data;
            data_params['user_id'] = this.user_id;
            data_params['type'] = type;
            data_params['currency_code'] = this.room.room_price.currency_code;
            let url = routeList.update_calendar_event;

            let callback_function = (response_data) => {
                if(!response_data.status) {
                    this.isLoading = false;
                    this.error_messages = response_data.error_messages;
                    openModal('calendarEventModal');
                }
                else {
                    let content = {title: response_data.status_text,message: response_data.status_message};
                    flashMessage(content);                    
                }

                closeModal('calendarEventModal');
                this.calendar.refetchEvents();
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        unselectCalendar() {
            this.calendar.unselect();
        },
        updateFormFields(room_id, selectionInfo, price = 0, status = 'available', notes = '', calendar_id='') {
            let start = selectionInfo.start;
            let end = selectionInfo.end;
            if(end == null) {
                end = selectionInfo.start;
            }
            else {
                end.setMinutes(end.getMinutes() - 1);
            }
            
            let startDate = convertToMoment(start);
            let endDate = convertToMoment(end);
            this.calendar_data.start_date = startDate.format('YYYY-MM-DD');
            this.calendar_data.end_date = endDate.format('YYYY-MM-DD');
            this.calendar_data.formatted_start_date = flatpickr.formatDate(start,flatpickrFormat);
            this.calendar_data.formatted_end_date = flatpickr.formatDate(end,flatpickrFormat);
            this.calendar_data.room_id = room_id;
            this.calendar_data.status = status;
            this.calendar_data.price = price > 0 ? price : this.room.room_price.price;
            this.calendar_data.notes = notes;
            this.calendar_data.calendar_id = calendar_id;
            $('#calendarEventModal').modal('show');
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
        openCancelModal(model) {
            openModal(model);
        },
        registerEvents() {
            if(this.currentRouteName == 'manage_listing') {
                this.navigationChanged(this.current_step.step);
            }
            
            let self = this;
            // Event to update amenities scope when user check/Uncheck values
            $(document).on('change', '.amenities', function() {
                let amenities = getSelectedData('.amenities');
                self.room.amenities = amenities.toString();
            });

            // Event to update guest accesses scope when user check/Uncheck values
            $(document).on('change', '.guest_accesses', function() {
                let guest_accesses = getSelectedData('.guest_accesses');
                self.room.guest_accesses = guest_accesses.toString();
            });

            // Event to update house rules scope when user check/Uncheck values
            $(document).on('change', '.house_rules', function() {
                let house_rules = getSelectedData('.house_rules');
                self.room.house_rules = house_rules.toString();
            });
        },
        loadFunctions() {
            this.updateSavedData();
            this.updateListingProgress();
            if(IN_ONLINE) {
                this.initAutocomplete();
                setTimeout( () => {
                    this.initMap();
                });
            }
        },
    },
};