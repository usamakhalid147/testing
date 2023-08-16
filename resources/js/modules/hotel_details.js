export default {
    otherData: {
        hotel_id:'',
        host_id:'',
        is_saved:false,
        showNavigation: false,
        checkin : '',
        checkout : '',
        guests: '',
        code: '',
        adults: 1,
        children: 0,
        rooms: 1,
        max_guests: 16,
        all_rooms: [],
        selected_rooms: [],
        total_nights: 0,
        messages: [],
        fees: [],
        fee: [],
        isContentLoading: true,
        error_message: '',
        error_type: '',
        selected_plans: [],
        selected_beds: [],
    },
    computed: {
        get_display_formatted_dates: function() {
            if(this.checkin != '' && this.checkout != '') {
                var checkin = moment(this.checkin).format(displayFormat);
                var checkout = moment(this.checkout).format(displayFormat);
                return checkin+' to '+checkout;
            }   
            return '';
        },
        get_occupancy_text: function() {
            var text = this.adults > 1 ? 'adults' : 'adult';
            var adults = this.adults+' '+occupancy_messages[text];

            var text = this.children > 1 ? 'children' : 'child';
            var children = this.children+' '+occupancy_messages[text];

            var text = this.rooms > 1 ? 'rooms' : 'room';
            var rooms = this.rooms+' '+occupancy_messages[text];

            return adults+', '+children+', '+rooms;
        },
        total_room : function() {
            var me = this;
            var total_room = 0;
            if(me.checkin != '' && me.checkout != '') {
                for(const [key,room] of Object.entries(me.selected_rooms)) {
                    total_room +=parseInt(room.selected_count);
                }
            }
            return total_room;
        },
        total_price : function() {
            var me = this;
            var total_price = 0.00;
            if(me.checkin != '' && me.checkout != '' && me.selected_rooms.length > 0) {
                for(const [key,room] of Object.entries(me.selected_rooms)) {
                    total_price += room.total_price;
                }
            }
            return total_price;
        },
        service_fee: function() {
            var total_price = this.total_price;
            var service_fee = 0;
            if(total_price > 0) {
                for(const[key,data] of Object.entries(this.fees)) {
                    if(data.fee_type == 'fixed') {
                        service_fee += parseInt(data.fee);
                    }
                    else {
                        service_fee += (data.value / 100) * total_price;
                    }
                }
            }
            return service_fee;
        },
        service_charge : function() {
            var service_charge = 0;
            var total_price = this.total_price;
            if(total_price > 0) {
                for(const[key,data] of Object.entries(this.fees)) {
                    if(data.fee_type == 'fixed') {
                        total_price += parseInt(data.fee);
                    }
                    else {
                        total_price += (data.value / 100) * total_price;
                    }
                    if (data.service_charge > 0) {
                        if(data.service_charge_type == 'fixed') {
                            service_charge = parseInt(data.service_charge);
                        } else {
                            service_charge = (data.service_charge / 100) * total_price;
                        }
                    }
                }
            }
            return service_charge;
        },
        property_tax : function() {
            var property_tax = 0;
            var total_price = this.total_price;
            if(total_price > 0) {
                for(const[key,data] of Object.entries(this.fees)) {
                    if(data.fee_type == 'fixed') {
                        total_price += parseInt(data.fee);
                    }
                    else {
                        total_price += (data.value / 100) * total_price;
                    }
                    if (data.property_tax > 0) {
                        if(data.property_tax_type == 'fixed') {
                            property_tax = parseInt(data.property_tax);
                        } else {
                            property_tax = (data.property_tax / 100) * total_price;
                        }
                    }
                }
            }
            return property_tax;
        },
        total_price_with_tax : function() {
            var total_price = this.total_price;
            if(total_price > 0) {
                for(const[key,data] of Object.entries(this.fees)) {
                    if(data.fee_type == 'fixed') {
                        total_price += parseInt(data.fee);
                    }
                    else {
                        total_price += (data.value / 100) * total_price;
                    }
                    if (data.service_charge > 0) {
                        if(data.service_charge_type == 'fixed') {
                            total_price += parseInt(data.service_charge);
                        } else {
                            total_price += (data.service_charge / 100) * total_price;
                        }
                    }
                    if (data.property_tax > 0) {
                        if(data.property_tax_type == 'fixed') {
                            total_price += parseInt(data.property_tax);
                        } else {
                            total_price += (data.property_tax / 100) * total_price;
                        }
                    }
                }
            }
            return total_price;
        },
    },
    methods: {
        numberFormat(price) {
            return parseFloat(price).toFixed(2);
        },
        InitTooltips() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        },
        InitImageSlider() {
            $('.image-gallery').lightSlider({
                gallery: false,
                item:1,
                loop: true,
                pager: false,
                thumbItem:9,
                slideMargin:0,
                enableDrag: false,
                enableTouch:false,
                controls:true,
                currentPagerPosition:'left',
                onSliderLoad: function(el) {
                    el.lightGallery({
                        selector: '.image-gallery .lslide',
                        subHtmlSelectorRelative:true,
                        mode: 'lg-lollipop',
                        closable:true,
                        autoWidth:true,
                        enableDrag:true,
                        download: false,
                        pager: false,
                        fullScreen: false,
                        autoplay: false,
                        autoplayControls: false,
                        share: false,
                    });
                }
            });
        },
        initRoomSlider() {
            var sliderList = [].slice.call(document.querySelectorAll('.hotel-subrooms'))
            var slider = sliderList.map(function(sliderEl) {
                return tns({
                    "container": sliderEl,
                    "autoplay": true,
                    "arrowKeys": false,
                    "mouseDrag": true,
                    "swipeAngle": false,
                    "loop": true,
                    "nav": false,
                    "controlsText": ['<span class="icon icon-left-chevron"></span>','<span class="icon icon-right-chevron"></span>'],
                    "navPosition": "bottom",
                    "controls": true,
                    "autoplayButton": false,
                    "autoplayButtonOutput": false,
                    "items": 1,
                    "gutter": 0,
                });
            });
        },
        initSliderGalary(photos,index) {
            let photosList = [];
            photos.forEach(function (photo) {
                photosList.push({
                    src: photo.image_src,
                    thumb: photo.image_src,
                    subHtml: photo.name,
                });
            });
            console.log(photosList);
            $('.subroom-slider-'+index).lightGallery({
                mode: 'lg-lollipop',
                closable:true,
                autoWidth:true,
                enableDrag:true,
                download: false,
                pager: false,
                fullScreen: false,
                autoplay: false,
                autoplayControls: false,
                share: false,
                hash: false,
                dynamic: true,
                dynamicEl: photosList,
            });
        },
        ReviewSliderGalary(photos) {
            let photosList = [];
            photos.forEach(function (photo) {
                photosList.push({
                    src: photo.image_src,
                    thumb: photo.image_src,
                    subHtml: photo.image_src,
                });
            });
            $('.review-subrooms').lightGallery({
                mode: 'lg-lollipop',
                closable:true,
                autoWidth:true,
                enableDrag:true,
                download: false,
                pager: false,
                fullScreen: false,
                autoplay: false,
                autoplayControls: false,
                share: false,
                hash: false,
                dynamic: true,
                dynamicEl: photosList,
            });
        },
        initDatePicker() {
            var me = this;
            let flatpickrOptions = {
                mode: "range",
                minDate: "today",
                altInput: true,
                altFormat: flatpickrFormat,
                dateFormat: "Y-m-d",
                onClose: function (selectedDates, dateStr, instance) {
                    if(selectedDates.length < 2) {
                        var checkin = moment(me.checkin).format(displayFormat);
                        var checkout = moment(me.checkout).format(displayFormat);
                        setTimeout(() => {
                            $('.date_picker').val(checkin+' to '+checkout);
                        });
                        return false;
                    }
                    let start_date = selectedDates[0];
                    let end_date = selectedDates[1];
                    if(start_date.getTime() == end_date.getTime()) {
                        end_date.setDate(end_date.getDate() + 1);
                    }
                    
                    me.checkin = changeFormat(start_date);
                    me.checkout = changeFormat(end_date);
                    me.validateBookingDetails();
                    // if($(document).width() > 991) {
                    //     $('#occupancy').dropdown('show');
                    // }
                    // else {
                    //     $('#mob-occupancy').dropdown('show');
                    // }
                },
            };
            flatpickr('.date_picker', flatpickrOptions);
        },
        validateBookingDetails() {
            var result = true;
            if(checkInValidInput(this.checkin) || checkInValidInput(this.checkout)) {
                result = false;
            }

            if(result) {
                setTimeout(() => {
                    if($(document).width() > 991) {
                        $('#occupancy').dropdown('hide');
                    }
                    else {
                        $('#mob-occupancy').dropdown('hide');
                    }
                    this.checkAvailability();
                });
            }
        },
        getBasicDataParams() {
            return { hotel_id: this.hotel_id, 'checkin' : this.checkin, 'checkout' : this.checkout, 'adults' : this.adults, 'children' : this.children, 'rooms' : this.rooms,  'code' : this.code, 'selected_plans' : this.selected_plans.toString(), 'selected_beds' : this.selected_beds.toString() };
        },
        checkAvailability() {
            var data_params = this.getBasicDataParams();
            var url = routeList.check_availability;
            var callback_function = (response_data) => {
                this.all_rooms = response_data.all_rooms;
                this.total_nights = response_data.total_nights;
                this.selected_rooms = response_data.selected_rooms;
                this.fees = response_data.fees;
                this.fee = response_data.fees[0];
                this.error_type = response_data.error_type;
                this.error_message = response_data.error_message;
                if(this.all_rooms.length > 0) {
                    setTimeout( () => {
                        this.initRoomSlider();
                        $('.btn-more').on('click',function(){
                            if($(this).text() == this.messages['more']) {
                                $(this).text(this.messages['less']);
                            }
                            else {
                                $(this).text(this.messages['more']);
                            }
                        })
                    },10);
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        addRoom(index) {
            if(this.total_room < 2) {
                // return false;
            }
            let room = this.all_rooms[index];
            room.selected_count += 1;
            room.add_rooms.push({
                'adults'    : room.adults,
                'children'  : 0,
                'price'     : parseInt(room.price)
            });
            room.total_price = parseInt(room.price);
            room.total_adults = parseInt(room.adults);
            this.selected_rooms.push(room);
            this.all_rooms[index].is_selected = true;
        },
        removeRoom(index){
            var room = this.selected_rooms[index];
            if(confirm('Are you sure you want to remove '+room.name+' room?')) {
                this.selected_rooms.splice(index,1);
                for(const[key,value] of Object.entries(this.all_rooms)) {
                    if(value.id == room.id) {
                        this.all_rooms[key].is_selected = false;
                        this.all_rooms[key].selected_count = 0;
                        this.all_rooms[key].add_rooms = [];
                        this.all_rooms[key].total_price = 0;
                        this.all_rooms[key].total_adults = 0;
                        this.all_rooms[key].total_children = 0;
                    }
                }
            }
            return true;
        },
        addExtraRoom(index) {
            var room = this.selected_rooms[index];
            this.selected_rooms[index].selected_count += 1;
            this.selected_rooms[index].total_price += parseInt(room.price);
            this.selected_rooms[index].total_adults += parseInt(room.adults);
            this.selected_rooms[index].total_children += parseInt(room.children);
            this.selected_rooms[index].add_rooms.push({
                'adults'    : room.adults,
                'children'  : room.children,
                'price'     : parseInt(room.price)
            });
        },
        removeExtraRoom(index) {
            this.selected_rooms[index].selected_count -= 1;
            this.selected_rooms[index].add_rooms.pop();
            this.calcRoomTotalPrice(index)
        },
        calcRoomPrice(index,n) {
            var room = this.selected_rooms[index].add_rooms[n];
            var adults = parseInt(room.adults);
            var children =parseInt(room.children);
            var standard_adults = parseInt(this.selected_rooms[index].adults);
            var standard_children = parseInt(this.selected_rooms[index].children);
            for(const[key,combo] of Object.entries(this.selected_rooms[index].combo_rooms)) {
                if(combo.adults == adults && combo.children == children) {
                    this.selected_rooms[index].add_rooms[n].price = combo.price;
                }
            }
            this.calcRoomTotalPrice(index);
        },
        calcRoomTotalPrice(index) {
            var total_price = 0;
            var total_adults = 0;
            var total_children = 0;
            for(const[key,room] of Object.entries(this.selected_rooms[index].add_rooms)) {
                total_price += parseInt(room.price);
                total_adults += parseInt(room.adults);
                total_children += parseInt(room.children);
            }
            this.selected_rooms[index].total_price = total_price;
            this.selected_rooms[index].total_adults = total_adults;
            this.selected_rooms[index].total_children = total_children;
            var self = this;
            var price = 0;
            this.selected_rooms[index].selected_plans.forEach(function (plan,key) {
                var plan_index = self.selected_rooms[index].meal_plans.findIndex(meal => meal.id == plan);
                price += parseInt(self.selected_rooms[index].meal_plans[plan_index].price);
            });
            this.selected_rooms[index].selected_beds.forEach(function (plan,key) {
                var plan_index = self.selected_rooms[index].bed_types.findIndex(bed => bed.id == plan);
                price += parseInt(self.selected_rooms[index].bed_types[plan_index].price);
            });
            this.selected_rooms[index].total_price += parseInt(price);
        },
        getRoomOccupancyText(number,type) {
            if(type == 'adults') {
                var text = number > 1 ? occupancy_messages['adults'] : occupancy_messages['adult'];
            }
            else {
                var text = number > 1 ? occupancy_messages['children'] : occupancy_messages['child'];
            }
            return number+' '+text;
        },
        confirmReserve() {
            var url = routeList.confirm_reserve;
            var data_params = this.getBasicDataParams();
            data_params.rooms = _.map(this.selected_rooms, function (item) {
                return _.pick(item, ['id','name','number','selected_count','total_price','total_adults','total_children','add_rooms','meal_plan_price','meal_plans','bed_price','bed_types','selected_plans','selected_beds','applied_promotions']);
            });
            var callback_function = (response_data) => {
                if(response_data.status == 'redirect') {
                    window.location.href = response_data.redirect_url;
                }
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        initMap() {
            if(IN_ONLINE != true) {
                return false;
            }

            let mapCanvas = document.getElementById('map');
            let mapCenter = { lat: parseFloat($('#map').data('lat')), lng: parseFloat($('#map').data('lng')) };

            let mapOptions = {
                center: new google.maps.LatLng(mapCenter),
                zoom: 13,
                scrollwheel: false,
                fullscreenControl: false,
                mapTypeControl: false,
                streetViewControl: false,
                panControl: false,
                scaleControl: false,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.TOP_RIGHT
                }
            };
            let map = new google.maps.Map(mapCanvas, mapOptions);

            new google.maps.Circle({
                map: map,
                center: mapCenter,
                strokeColor: '#11848E',
                strokeOpacity: 0.75,
                strokeWeight: 1.5,
                fillColor: '#7FDDC4',
                fillOpacity: 0.30,
                radius: 500
            }); 
        },
        choosePlan(plan_index,room_index) {
            var meal_plan = this.all_rooms[room_index].meal_plans[plan_index];
            if (meal_plan.is_selected) {
                meal_plan.is_selected = false;
                this.all_rooms[room_index].meal_plan_price -= meal_plan.price;
                // this.all_rooms[room_index].price -= parseInt(meal_plan.price);
                // this.all_rooms[room_index].original_price -= parseInt(meal_plan.price);
                var selected_index = this.selected_rooms.findIndex(room => room.id == this.all_rooms[room_index].id);
                this.selected_rooms[selected_index].total_price -= parseInt(meal_plan.price);
                // this.selected_rooms[selected_index].meal_plan_price -= meal_plan.price;
                var index = this.selected_rooms[selected_index].selected_plans.findIndex(plan => plan == meal_plan.id);
                this.selected_rooms[selected_index].selected_plans.splice(index,1);
                var plan_index = this.selected_plans.findIndex((select_plan) => select_plan == meal_plan.id);
                this.selected_plans.splice(plan_index,1);
                return true;
            }
            meal_plan.is_selected = true;
            // this.all_rooms[room_index].price += parseInt(meal_plan.price);
            // this.all_rooms[room_index].original_price += parseInt(meal_plan.price);
            this.all_rooms[room_index].meal_plan_price += meal_plan.price;
            var selected_index = this.selected_rooms.findIndex(room => room.id == this.all_rooms[room_index].id);
            // this.selected_rooms[selected_index].meal_plan_price += meal_plan.price;
            this.selected_rooms[selected_index].total_price += parseInt(meal_plan.price);
            this.selected_rooms[selected_index].selected_plans.push(meal_plan.id);
            this.selected_plans.push(meal_plan.id);
        },
        chooseBeds(plan_index,room_index) {
            var extra_bed = this.all_rooms[room_index].bed_types[plan_index];
            if (extra_bed.is_selected) {
                extra_bed.is_selected = false;
                this.all_rooms[room_index].bed_price -= extra_bed.price;
                // this.all_rooms[room_index].price -= parseInt(extra_bed.price);
                // this.all_rooms[room_index].original_price -= parseInt(extra_bed.price);
                var selected_index = this.selected_rooms.findIndex(room => room.id == this.all_rooms[room_index].id);
                this.selected_rooms[selected_index].total_price -= parseInt(extra_bed.price);
                // this.selected_rooms[selected_index].bed_price -= extra_bed.price;
                var index = this.selected_rooms[selected_index].selected_beds.findIndex(bed => bed == extra_bed.id);
                this.selected_rooms[selected_index].selected_beds.splice(index,1);
                var bed_index = this.selected_beds.findIndex((select_bed) => select_bed == extra_bed.id);
                this.selected_beds.splice(bed_index,1);
                return true;
            }
            extra_bed.is_selected = true;
            // this.all_rooms[room_index].price += parseInt(extra_bed.price);
            // this.all_rooms[room_index].original_price += parseInt(extra_bed.price);
            this.all_rooms[room_index].bed_price += extra_bed.price;
            var selected_index = this.selected_rooms.findIndex(room => room.id == this.all_rooms[room_index].id);
            this.selected_rooms[selected_index].total_price += parseInt(extra_bed.price);
            this.selected_rooms[selected_index].selected_beds.push(extra_bed.id);
            // this.selected_rooms[selected_index].bed_price += extra_bed.price;
            this.selected_beds.push(extra_bed.id);
        },
        loadFunctions() {
            setTimeout( () => {
                // this.initMap();
                this.initDatePicker();
                this.InitImageSlider();
            },10);
            this.validateBookingDetails();
        },
        registerEvents() {
            var self = this;

            $(document).on('click','.navigation-links',function(event) {
                event.preventDefault();
                let target = $(this).attr("href");
                let top = $(target).offset().top - $('.sticky-navigation').outerHeight();

                $('html, body').stop().animate({
                    scrollTop: top
                }, 1000);
            });

            
            $(document).ready(function() {
                self.isContentLoading = false;
                let rtl = ($("html").attr('lang')  == 'ar');
                updateSlider('#similar_hotels','similar_type');

                var booking_form = function() {
                    var header_height = $(".navbar ").outerHeight();
                    var detail_banner = $('.hotel_image-container').position().top + $('.hotel_image-container').outerHeight();
                    $('.booking-box').css({
                        "top": (header_height + 5) + "px"
                    });
                    // if ($(window).scrollTop() >= (detail_banner - header_height)) {
                    //     $('.booking-box').addClass('active');
                    //     $('.detail-sticky').addClass('active');
                    // } else {
                    //     $('.booking-box').removeClass('active');
                    //     $('.detail-sticky').removeClass('active');
                    // }
                };
                booking_form(); 
                $(window).scroll(booking_form);
                $(window).resize(booking_form);

                $(".border-focus").focusin(function() {
                    $(this).css("box-shadow", "0px 0px 1px 1.5px black");
                    $(this).css("border-radius", "7px");
                });
                $(".border-focus").focusout(function() {
                    $(this).css("box-shadow", "unset");
                    $(this).css("border-radius", "unset");
                });
            });

            $("#amenity-collapse").on('click',function() {
                if($("#amenity-collapse").html() == "+ View More"){
                    $("#amenity-collapse").html("- View Less");
                }
                else{
                    $("#amenity-collapse").html("+ View More");
                }
            });
            
        },
    },
};
