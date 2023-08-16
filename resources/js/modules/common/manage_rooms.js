export default {
    otherData: {
        form_modified: false,
        listing_progress: '',
        percent_completed: '0',
        room: {
            hotel_room_photos: [],
            hotel_room_price: {},
            cancellation_policies: [],
            meal_plans: [],
            extra_beds: [],
        },        
        current_step: {},
        step_data: {},
        removed_photos: [],
        errorList: {},
        error_messages: {},
        meal_plan_options: [],
        bed_types: [],
        hotel_room_promotions: {
            early_bird: [],
            min_max: [],
            day_before_checkin: [],
        },
        current_tab: 'room_details',
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
        calendar_price: 0,
        calendar_status: 'available',
        calendar_notes: '',
        temp_room_photos: [],
    },
    components: {
    },
    computed: {
        checkStepsCompleted: function() {
            for(const[key,data] of Object.entries(this.step_data)) {
                if(data.step != 'room_status' && data.completed == false) {
                    return true;
                }
            }
            return false;
        }
    },
    vueMethods: {
        navigationChanged (step_name) {
            this.current_tab = step_name;
            setGetParameter('current_tab',step_name);
            if(step_name == 'calendar') {
                setTimeout(() => {
                    this.initFullCalendar();
                },1000);
            }
            else if(this.current_tab == 'photos') {
                this.initDraggableSection();
            }
        },
        updateFormStatus(status = true) {
            this.form_modified = status;
        },
        updateSavedData() {
            // Apply Saved data to original data
            this.original_hotel = cloneObject(this.hotel);
        },
        getStepData() {
            return new FormData(room_form);
        },
        saveStep() {
            let data_params = this.getStepData();
            let url = routeList.update_room;
            this.error_messages = [];
            this.photos_errors = false;
            let callback_function = (response_data) => {
                this.step_data = response_data.step_data;
                if(response_data.error) {
                    if (this.current_step.step != 'photos') {
                        this.error_messages = response_data.error_messages;
                        let content = {title: response_data.status,message: response_data.status_message};
                        flashMessage(content,'danger');
                        return false;
                    }
                    this.photos_errors = true;
                    document.getElementById('upload_photos').value = '';
                    this.removed_photos = [];
                    this.step_data = response_data.step_data;
                    var self = this;
                    self.room.room_photos = self.temp_room_photos;
                    return true;
                }
                let content = {title: response_data.status,message: response_data.status_message};
                flashMessage(content,'success');
            };
            this.makePostRequest(url,data_params,callback_function);
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
                    $('.hotel_image-row').removeClass('sorting');
                    this.updatePhotoOrder();
                }
            });
        },
        updatePhotoOrder() {
            var new_image_order = $(".image_id").map(function() {
                return $(this).val();
            }).get();

            let url = routeList.update_room_photo_order;
            var data_params = {room_id : this.room.id,image_order_list:new_image_order};
            let callback_function = (response_data) => {
                let content = {title: response_data.status,message: response_data.status_message};
                flashMessage(content,'success');
            };
            this.makePostRequest(url,data_params,callback_function);
        },
        deletePhoto(index) {
            this.removed_photos.push(this.room.hotel_room_photos[index].id);
            this.room.hotel_room_photos.splice(index, 1);
        },
        openImageBrowser(temp_id) {
            $('#upload_photos_'+temp_id).trigger('click');
        },
        addNewTranslation(locale) {
            var translation = [];
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
        addCancellationPolicy(){
            this.room.cancellation_policies.push({
              id: '',
              days: '',
              percentage: '',
          });
              
        },
        removeCancellationPolicy(index) {
            this.room.cancellation_policies.splice(index, 1);
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
        previewPhoto(event) {
            let element = this.$refs['file'];
            let self = this;
            var image_count = element.files.length + this.room.hotel_room_photos.length;
            if (image_count > 8) {
                this.error_messages.photos = 'you can upload only 8 Images';
                document.getElementById('upload_photos').value = '';
                return false;
            }
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
                    self.room.hotel_room_photos.push(selectedImage);
                }
                reader.readAsDataURL(file);
            }
            this.temp_room_photos = this.room.room_photos;
            this.saveStep();
        },
        goToStep(step) {
            this.current_step = step;
            this.navigationChanged(step.step);
        },
        initFullCalendar() {
            var me = this;
            let calendarEl = document.getElementById('full_calendar');
            let calendarOptions = {
                headerToolbar: {
                    start: 'dayGridMonth',
                    center: 'title'
                },
                locale: userLanguage,
                themeSystem: 'bootstrap5',
                buttonIcons: {
                    prev: 'chevron-left',
                    next: 'chevron-right',
                },
                initialView: 'dayGridMonth',
                initialDate: this.now,
                selectable: true,
                expandRows: false,
                showNonCurrentDates: false,
                fixedWeekCount: false,
                allDaySlot: false,
                longPressDelay: 500,
                selectOverlap: function(event) {
                    me.calendar_price = event._def.extendedProps.price;
                    me.calendar_status = event._def.extendedProps.status;
                    me.calendar_notes = event._def.extendedProps.notes ?? '';
                    return true;
                },
                eventSources: [{
                    url: routeList.get_calendar_data,
                    extraParams: () => {
                        return {
                            user_id : this.user_id,
                            hotel_id : this.hotel_id,
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
                eventClick: (eventClickInfo) => {
                    let extendedProps = eventClickInfo.event.extendedProps;
                    if(extendedProps.source == 'Reservation') {
                        return false;
                    }
                    this.updateFormFields(extendedProps.room_id, eventClickInfo.event,extendedProps.price, extendedProps.status,extendedProps.notes,extendedProps.calendar_id);
                },
                select: (selectionInfo) => {
                    if(convertToMoment(selectionInfo.start).format('YYYY-MM-DD') < convertToMoment(this.now).format('YYYY-MM-DD')) {
                        this.unselectCalendar();
                        let content = {title: "Failed",message: "Please Choose After Current Date"}; 
                        flashMessage(content,'danger');
                        return false;
                    }
                    this.updateFormFields(this.room_id, selectionInfo, this.calendar_price, this.calendar_status, this.calendar_notes);
                },
                eventDidMount: function(eventClickInfo) { 
                    let extendedProps = eventClickInfo.event.extendedProps;
                    var title = eventClickInfo.el.querySelector(".fc-event-title");
                    if(extendedProps.status == 'available') {
                        let available = $.parseHTML("<div class='text-center'><div>"+me.calendar_text.available+" / "+me.calendar_text.sold+"</div><div>"+extendedProps.available+" / "+extendedProps.sold+"</div></div>")[0];
                        let price = $.parseHTML("<div class='text-center'><div>"+me.calendar_text.price+"</div><div>"+extendedProps.room_price+"</div></div>")[0];
                        title.append(available);
                        title.append(price);
                    }
                    else {
                        let status = $.parseHTML("<div class='text-center'>"+me.calendar_text[extendedProps.status]+"</div>")[0];
                        title.append(status);   
                    }
                },
                eventContent: (arg) => {
                    // console.log('ss')
                    // console.log(arg);
                },
            };

            try {
                this.calendar = new FullCalendar.Calendar(calendarEl, calendarOptions);
                this.calendar.render();
            }
            catch(err) {
                window.location.reload();
            }
        },
        updateCalendarEvent(type = '') {
            this.isLoading = true;
            let data_params = this.calendar_data;
            data_params['user_id'] = this.user_id;
            data_params['type'] = type;
            data_params['currency_code'] = this.room.hotel_room_price.currency_code;
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
            this.calendar_data.hotel_id = this.hotel_id;
            this.calendar_data.room_id = room_id;
            this.calendar_data.status = status;
            this.calendar_data.price = price > 0 ? price : this.room.hotel_room_price.price;
            this.calendar_data.notes = notes;
            this.calendar_data.calendar_id = calendar_id;
            $('#calendarEventModal').modal('show');
        },
        canDisplayMealPlan(id) {
            let index = this.room.meal_plans.findIndex((plan) => plan.type_id == id);
            return index == -1;
        },
        addMealPlan() {
            this.room.meal_plans.push({
                id:'',
                type_id:'',
                price: '',
            });
        },
        removeMealPlan(index) {
            this.room.meal_plans.splice(index, 1);
        },
        canDisplayExtraBed(id) {
            let index = this.room.extra_beds.findIndex((plan) => plan.type_id == id);
            return index == -1;
        },
        addExtraBed() {
            this.room.extra_beds.push({
                id:'',
                type_id:'',
                price: '',
                size: '',
                guest_type: 'adult',
            });
        },
        removeExtraBed(index) {
            this.room.extra_beds.splice(index, 1);
        },
        addPromotion(type) {
            if(type == 'early_bird' || true) {
                this.hotel_room_promotions[type].push({
                    id: 0,
                    name: '',
                    value_type: 'percentage',
                    status: 0,

                });
            }

        },
        canDisplayEarlyBird(type,day) {
            let index = this.hotel_room_promotions[type].findIndex((rule) => rule.days == day);
            return index == -1;
        },
        removePromotion(type,index) {
            this.hotel_room_promotions[type].splice(index,1);
        },
        registerEvents() {
            var self = this;
            var current_tab = getParameterByName('current_tab');
            current_tab = checkInValidInput(current_tab) ?'room_details' :  current_tab ;
            this.navigationChanged(current_tab);

            // Event to update amenities scope when user check/Uncheck values
            $(document).on('change', '.amenities', function() {
                let amenities = getSelectedData('.amenities');
                self.room.amenities = amenities.toString();
            });

            // Event to update Payment Methods scope when user check/Uncheck values
            $(document).on('change', '.payment_method', function() {
                let payment_method = getSelectedData('.payment_method');
                self.room.payment_method = payment_method.toString();
            });

            if(current_tab == 'photos') {
                self.initDraggableSection();
            }
        },
    },
};