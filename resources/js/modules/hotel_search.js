import Pagination from './../components/PaginationComponent.vue';
import HotelDetails from './../components/HotelDetailsComponent.vue';
import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';
import {CustomOverlay} from './CustomOverlay.js';

export default {
    otherData: {
        hotels: {
            data: [],
            top_picks_hotels: [],
            total: 0,
            per_page: 10,
            from: 1,
            to: 0,
            current_page: 1
        },
        map: {},
        map_data: {},
        searchByMap: false,
        infoWindow: {},
        hotel_markers: [],
        mapZoomLevel: 10,
        search_latitude:0,
        search_longitude:0,
        map_bounds:{},
        active_hotel_id: 0,
        originalFilter: {
            star_rating: [],
            property_type: [],
            amenities: [],
        },
        searchFilter: {
            star_rating: [],
            property_type: [],
            amenities: [],
            toggle_map: false,
            page: 1,
        },
        default_data: {},
        filterText: {
            date_text: '',
            price:'',
        },
        search_viewport : {},
        is_enabled_map : true,
        wishlists : {},
        wishlist : {},
        wishlist_error : {},
        messages: [],
        show_guest_text: false,
        search_room_type : false,
        search_guest : false,
        search_price : false,
        search_more_filters : false,
        hotelLoading : false,
    },
    components: {
        Pagination,
        HotelDetails
    },
    computed: {
        getGuestText: function() {
            if(!this.show_guest_text) {
                return '';
            }
            var text = this.searchFilter.adults > 1 ? 'adults' : 'adult';
            var adults = this.searchFilter.adults+' '+occupancy_messages[text];

            var text = this.searchFilter.children > 1 ? 'children' : 'child';
            var children = this.searchFilter.children+' '+occupancy_messages[text];

            var text = this.searchFilter.rooms > 1 ? 'rooms' : 'room';
            var rooms = this.searchFilter.rooms+' '+occupancy_messages[text];

            return adults+', '+children+', '+rooms;
        }
    },
    methods: {
        getBathroomCount(bath_rooms){
            return this.searchFilter.bath_rooms=parseFloat(bath_rooms)+0.5;
        },
        formatQueryString(jsonUrl) {
            return new URLSearchParams(jsonUrl).toString();
        },
        updateSearchUrl() {
            let formatted_url = this.formatQueryString(this.searchFilter);
            var newRelativePathQuery = window.location.pathname + '?' + formatted_url;
            history.replaceState(null, '', newRelativePathQuery);
        },
        updateSlider() {
            var me = this;
            setTimeout(() => {
                updateSlider('.hotel-slider','search');
                if(me.hotels.top_picks_hotels.length) {
                    updateSlider('.recommended_hotel','top_picks');
                    updateSlider('.hotel-recommended-image','search');
                }
            });
        },
        resetPagination() {
            this.hotels.current_page = 1;
            this.searchFilter.page = 1;
        },
        changePage() {
            this.searchFilter.page = this.hotels.current_page;
            this.searchListings();
        },
        toggleDropdown(selector) {
            let dropdown = bootstrap.Dropdown.getInstance(document.getElementById(selector));
            dropdown.toggle();
        },
        applyFilter(filter) {
            this.resetPagination();
            this.searchListings();
        },
        resetFilter(filter) {
            this.searchFilter = cloneObject(this.originalFilter);
            this.updateRangeSlider();
            if(filter == 'more_filters') {
                closeModal('moreFiltersModal');
            }
            else {
                this.toggleDropdown(filter+'-dropdown-menu');
            }
        },
        getSearchDataParams() {
            let data_params = {map_data:this.map_data,...this.searchFilter};
            data_params['list_type'] = this.list_type;
            return data_params;
        },
        searchListings() {
            $('.location-error').addClass('d-none');
            if(this.searchFilter.location != '' && !this.autocomplete_used) {
                $('.location-error').removeClass('d-none');
                return false;
            }
        	this.searchFilter.page = this.hotels.current_page;
            var url = routeList.search_result+'?page='+this.hotels.current_page;
            var data_params = this.getSearchDataParams();
            this.hotels.data = [];
            this.hotels.top_picks_hotels = [];
            this.hotelLoading = true;
            var callback_function = (response_data) => {
                this.hotels = response_data;
                this.hotelLoading = false;
                let hotel_data = this.hotels.data;
                if(hotel_data.length > 0) {
                    this.map_bounds = new google.maps.LatLngBounds();
                    hotel_data.forEach((hotel) => {
                        let lat_lng = new google.maps.LatLng(hotel.latitude,hotel.longitude);
                        this.map_bounds.extend(lat_lng);
                    });

                    this.search_latitude = this.map_bounds.getCenter().lat();
                    this.search_longitude = this.map_bounds.getCenter().lng();
                }

                this.originalFilter = cloneObject(this.searchFilter);
                
                /*if(GOOGLE_MAP_ENABLED) {
                    this.initMap();
                }
                else {
                    this.initLeafletMap();
                }*/
                setTimeout( () => {
                    this.updateSlider();
                },100);
                $('html, body').animate({scrollTop: 0}, 100);
            };

            this.makePostRequest(url,data_params,callback_function);
            this.updateSearchUrl();
        },
        updateRangeSlider() {
            var slider = document.getElementById('price-slider');
            slider.noUiSlider.set([parseInt(this.searchFilter.min_price),parseInt(this.searchFilter.max_price)]);
        },
        initRangeSlider() {
            var slider = document.getElementById('price-slider');
            noUiSlider.create(slider, {
                start: [this.searchFilter.min_price, this.searchFilter.max_price],
                keyboardSupport: false,
                connect: true,
                step: 1,
                margin: 2,
                range: {
                    'min': parseInt(this.default_data.min_price),
                    'max': parseInt(this.default_data.max_price),
                }
            });

            slider.noUiSlider.on('update', (values, handle) => {
                if (handle) {
                    this.searchFilter.max_price = parseInt(values[handle]);
                }
                else {
                    this.searchFilter.min_price = parseInt(values[handle]);
                }
            });

            slider.noUiSlider.on('change', (values, handle) => {
                this.searchFilter.min_price = parseInt(values[0]);
                this.searchFilter.max_price = parseInt(values[1]);
                this.resetPagination();
                this.searchListings();
            });
        },
        updateAutoCompleteResult(place) {
            $(".autocomplete-place_id").val(place.place_id);
            this.searchFilter.location = place.formatted_address;
            this.searchFilter.place_id = place.place_id;
            this.search_latitude = place.geometry.location.lat();
            this.search_longitude = place.geometry.location.lng();
            this.map_bounds = place.geometry.viewport;
            this.searchByMap = false;
        },
        initAutoComplete() {
            let locationInput = document.getElementById('searchLocationInput');
            if(locationInput != null) {
                var autocomplete = new google.maps.places.Autocomplete(locationInput, {types: ['(cities)']});
                autocomplete.setFields(['geometry','place_id','formatted_address']);
                autocomplete.addListener('place_changed', () => {
                    let searchPlace = autocomplete.getPlace();
                    if(!searchPlace.geometry) {
                        return false;
                    }
                    this.updateAutoCompleteResult(searchPlace);
                });
            }
            
            let locationInputMobile = document.getElementById('headerLocationMobile');
            if(locationInputMobile != null) {
                var autocompleteMobile = new google.maps.places.Autocomplete(locationInputMobile, {types: ['(cities)']});
                autocompleteMobile.setFields(['geometry','place_id','formatted_address']);
                autocompleteMobile.addListener('place_changed', () => {
                    let searchPlace = autocompleteMobile.getPlace();
                    if(!searchPlace.geometry) {
                        return false;
                    }
                    this.updateAutoCompleteResult(searchPlace);
                });
            }
        },
        initMap() {
            var mapCenter = new google.maps.LatLng(this.search_latitude, this.search_longitude);

            var mapProp = {
                scrollwheel: false,
                center: mapCenter,
                zoom: this.mapZoomLevel,
                minZoom: 2,
                maxZoom: 18,
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.LEFT_TOP,
                    style: google.maps.ZoomControlStyle.SMALL
                },
                mapTypeControl: false,
                streetViewControl: false,
                navigationControl: false,
                backgroundColor: '#a4ddf5',
                gestureHandling: 'cooperative'
            }

            var mapEl = document.getElementById("map-canvas");
            if(mapEl == undefined) {
                return false;
            }
            
            this.map = new google.maps.Map(mapEl, mapProp);
            if (this.search_latitude != 0 && this.search_longitude != 0 && !this.searchByMap) {
                this.map.fitBounds(this.map_bounds);
            }

            this.updateMapListeners();
            this.createMarkers(this.hotel.data);
        },
        getInfoWindowContent(hotel) {
            let ratingHTML = '';
            let instantBookHTML = '';
            if(hotel.total_rating > 0) {
                ratingHTML = '<span class="ml-auto"> <span class="align-items-center d-flex"> <i class="review-star material-icons" area-hidden="true">star</i> <span class="pl-1 review_res"> '+hotel.rating+' </span> <span class="reviewers">( '+hotel.total_rating+')</span></span></span>';
            }
            if(hotel.booking_type == 'instant_book') {
                instantBookHTML = '<span class="icon icon-instant-book"></span>';
            }
            return '<div class="hotel-details"><ul class="hotel-slider"><li class="hotel-image"><img src="'+hotel.photos_list[0].image_src+'"></li></ul></div><div class="desc_list d-flex flex-column"><div class="room_type mt-2 w-100 mb-2 d-inline-flex align-items-center "><div class="d-flex">'+hotel.property_type_name+'</div>'+ ratingHTML +'</div><div class="hotel-title h5 font-weight-bold"> <a href="'+hotel.url+'" target="hotel_'+hotel.id+'" class="common-link">'+ hotel.name +' </a> </div><div class="hotel-info">'+hotel.guests_text+'<span>·</span>'+hotel.sub_room_count+'</div><div class="d-flex align-items-end justify-content-end hotel-price mt-2"><div class="text-right"><span class="price">'+hotel.price_text+'</span>'+instantBookHTML+'</div></div></div></div>';
        },
        checkAndCloseInfoWindow() {
            if (typeof this.infoWindow.close === "function") {
                this.infoWindow.close();
            }
        },
        createInfoWindow(content,latLng) {
            this.infoWindow = new google.maps.InfoWindow({
                content:content,
                maxWidth: 300,
                position:latLng,
            });
        },
        createMarker(listing,key) {
            let position = new google.maps.LatLng(listing.latitude, listing.longitude);

            let price_text = listing.currency_symbol+''+listing.price;
            let marker = new CustomOverlay(position, price_text);

            marker.addListener("mouseover", () => {
                this.active_listing_id = listing.id;
            });

            marker.addListener("mouseout", () => {
                this.active_listing_id = 0;
            });

            marker.addListener('click', () => {
                this.checkAndCloseInfoWindow();
                let content = this.getInfoWindowContent(listing);
                this.createInfoWindow(content,position);
                this.infoWindow.open(this.map);
            });
            return marker;
        },
        createMarkers(listing_data) {
            this.clearAllMarkers();
            this.listing_markers = [];
            listing_data.forEach((listing,key) => {
                let marker = this.createMarker(listing,key);
                this.listing_markers.push(marker);
            });
            this.pinMarkerOnMap(this.map);
        },
        clearAllMarkers() {
            this.checkAndCloseInfoWindow();
            for (let i = 0; i < this.listing_markers.length; i++) {
                this.listing_markers[i].setMap(null);
            }
        },
        pinMarkerOnMap(map) {
            for (let i = 0; i < this.listing_markers.length; i++) {
                this.listing_markers[i].setMap(map);
            }
        },
        updateMapData() {
            this.mapZoomLevel = this.map.getZoom();
            let bounds = this.map.getBounds();
            this.search_latitude = bounds.getCenter().lat();
            this.search_longitude = bounds.getCenter().lng();
            if(this.searchByMap) {
                this.map_data = {
                    minLat: bounds.getSouthWest().lat(),
                    minLng: bounds.getSouthWest().lng(),
                    maxLat: bounds.getNorthEast().lat(),
                    maxLng: bounds.getNorthEast().lng(),
                };
            }
        },
        updateMapListeners() {
            this.map.addListener('idle', () => {
                this.updateMapData();
            });

            this.map.addListener('dragend', () => {
                this.searchByMap = true;
                this.checkAndCloseInfoWindow();
                this.updateMapData();
            });

            google.maps.event.addListenerOnce(this.map, 'mousemove', () => {
                this.map.addListener('zoom_changed', () => {
                    this.searchByMap = true;
                    this.checkAndCloseInfoWindow();
                    this.updateMapData();
                    this.resetPagination();
                    this.searchListings();
                });
            });
        },
        createFeatureList(listing_data,key) {
            return {
                "type" : "Feature",
                "geometry": {
                    "type": "Point",
                    "coordinates": [listing_data.longitude,listing_data.latitude]
                },  
                "properties": {
                    "id": listing_data.id,
                    "index": key,
                    "isActive": true,
                    "listing_data": listing_data,
                    "tooltip_text": listing_data.currency_symbol+''+listing_data.price,
                }
            };
        },
        initLeafletMap() {
            var self = this;
            var markerData = {
                type: "FeatureCollection",
                features : []
            };
            var markersGroup = [];

            var settings = {
                markerPath: APP_URL+'/images/icons/map_icon-dark.png',
                markerPathHighlight: APP_URL+'/images/icons/map_pin.png',
            };

            var isMobile = ($(window).width() < 992);
            
            var map = L.map('map-canvas', {
                zoom: this.mapZoomLevel,
                scrollWheelZoom: false,
                dragging: !isMobile,
                tap: !isMobile,
                scrollWheelZoom: true
            });

            L.tileLayer('https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}{r}.png', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://wikimediafoundation.org/wiki/Maps_Terms_of_Use">Wikimedia maps</a>',
                minZoom: 2,
                maxZoom: 11
            }).addTo(map);

            this.hotels.data.forEach((listing,key) => {
                let feature_list = this.createFeatureList(listing,key);
                markerData.features.push(feature_list);
            });

            L.geoJSON(markerData, {
                pointToLayer: (feature, latlng) => {
                    return L.marker(latlng, {
                        id: feature.properties.id,
                        opacity: 0
                    });
                },
                onEachFeature: (feature, layer) => {
                    layer.on({
                        mouseover: function(e) {
                            self.active_listing_id = e.target.feature.properties.id;
                        },
                        mouseout: function(e) {
                            self.active_listing_id = 0;
                        }
                    });

                    if (feature.properties && feature.properties.tooltip_text) {
                        let popupContent = self.getInfoWindowContent(feature.properties.listing_data);
                        layer.bindPopup(popupContent, {
                            maxWidth: 300,
                        });

                        layer.bindTooltip('<div class="customOverlay" id="customTooltip-' + feature.properties.id + '">'+ feature.properties.tooltip_text + '</div>', {
                            direction: 'top',
                            permanent: true,
                            opacity: 1,
                            interactive: true,
                            className: 'map-custom-tooltip'
                        });
                    }
                    markersGroup.push(layer);
                }
            }).addTo(map);

            if (markersGroup.length > 0) {
                var featureGroup = new L.featureGroup(markersGroup);
                map.fitBounds(featureGroup.getBounds());
            }
        },
        changeStarRateFilter(index) {
            var element = document.getElementById("star_rate_"+index);
            if(!element.classList.contains('active')) {
                this.searchFilter.star_rating.push(index);
            }
            else {
                var pop = this.searchFilter.star_rating.indexOf(index);
                this.searchFilter.star_rating.splice(pop,1);
            }
            element.classList.toggle("active");
            this.applyFilter('star_rating');
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
                        var checkin = moment(me.searchFilter.checkin).format(displayFormat);
                        var checkout = moment(me.searchFilter.checkout).format(displayFormat);
                        setTimeout(() => {
                            $('.search_date_picker').val(checkin+' to '+checkout);
                        });
                        return false;
                    }
                    let start_date = selectedDates[0];
                    let end_date = selectedDates[1];
                    if(start_date.getTime() == end_date.getTime()) {
                        end_date.setDate(end_date.getDate() + 1);
                    }
                    
                    me.searchFilter.checkin = changeFormat(start_date);
                    me.searchFilter.checkout = changeFormat(end_date);
                },
            };
            flatpickr('.search_date_picker', flatpickrOptions);
        },
        registerEvents() {
        	$(document).on('change','#popup_date_picker',(event) => {
                this.searchFilter.checkin = $('.search_checkin').val();
                this.searchFilter.checkout = $('.search_checkout').val();
            });
            
            this.show_guest_text = true;
            $(document).on('submit','.search-form',(event) => {
                event.preventDefault();
                this.map_data = {};
                closeModal("headerSearchModal");
                closeModal("mobileSearchModal");
                this.resetPagination();
                this.searchListings();
            });

            $('.show-map').click(function() {
                $(this).hide();
                $('.show-result').show();
                $('.map-canvas').addClass('active');
            });

            $('.show-result').click(function() {
                $(this).hide();
                $('.show-map').show();
                $('.map-canvas').removeClass('active');
            });

            $("#amenity-collapse").on('click',function() {
                if($("#amenity-collapse").html() == "+ View More"){
                    $("#amenity-collapse").html("- View Less");
                }
                else{
                    $("#amenity-collapse").html("+ View More");
                }
            });

            $("#property-collapse").on('click',function() {
                if($("#property-collapse").html() == "+ View More"){
                    $("#property-collapse").html("- View Less");
                }
                else{
                    $("#property-collapse").html("+ View More");
                }
            });
        },
        loadFunctions() {
            this.map_bounds = new google.maps.LatLngBounds(this.search_viewport.southwest,this.search_viewport.northeast);
            
            this.originalFilter = cloneObject(this.searchFilter);
            this.searchListings();
            this.initAutoComplete();
            this.initDatePicker();
            this.initRangeSlider();
        },
    },
};