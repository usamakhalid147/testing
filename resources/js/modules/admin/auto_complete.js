export default {
    otherData : {
        address: '',
        latitude: '',
        longitude: '',
        place_id: '',
        country_code: '',
        viewport: {},
    },
    components: {
    },
    vueMethods: {
        initAutocomplete() {
            // Add Google Autocomplete to address input
            let address_line = document.getElementById('address');
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
            this.latitude = '';
            this.longitude = '';
            this.place_id = '';
        },
        fetchMapAddress(data, from_autocomplete = true) {
            // Fetch Location details after choose address from autocomplete
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
            let place = data;

            for (let i = 0; i < place.address_components.length; i++) {
                let addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    let val = place.address_components[i][componentForm[addressType]];

                    if(addressType == 'country') {
                        this.country_code = val;
                    }
                }
            }

            this.viewport = JSON.stringify(place.geometry.viewport);
            this.latitude = place.geometry.location.lat();
            this.longitude = place.geometry.location.lng();
            this.place_id = place.place_id;
        },
        registerEvents() {
            this.initAutocomplete();
        }
    },
};