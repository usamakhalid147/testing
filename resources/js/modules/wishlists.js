export default {
    otherData: {
        wishlists: [],
        wishlist_list: {
            hotels : [],
        },
        list_id: 0,
        list_type: 'hotel',
        wishlist: {
            wishlist_name : '',
            wishlist_privacy : '1',
        },
        wishlist_error: '',
    },
    components: {
    },
    methods: {
        getAllWishlists(list_id = 0) {
            var url = routeList.all_wishlists;
            this.list_id = list_id;
            $('.no-wishlists').addClass('d-none');
            var callback_function = (response_data) => {
                $('.no-wishlists').removeClass('d-none');
                this.wishlists = response_data.data;
            };

            this.makePostRequest(url,{},callback_function);
        },
        switchTab(target) {
            this.list_type = target;
            this.getAllWishlists(this.list_id);
        },
        createWishlist() {
            var url = routeList.create_wishlist;
            var data_params = this.wishlist;
            this.wishlist_error = '';
            var callback_function = (response_data) => {
                if(response_data.status == false) {
                    this.wishlist_error = response_data.status_message;
                    return false;
                }

                this.wishlists = response_data.data;
                if(this.list_id == 0) {
                    closeModal('createWishlistModal');
                    return true;
                }

                let wishlist_id = this.wishlists[this.wishlists.length-1].id;
                this.isLoading = true;
                this.saveToWishlist(wishlist_id);
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        saveToWishlist(wishlist_id) {
            var url = routeList.save_to_wishlist;
            var data_params = {
                wishlist_id: wishlist_id,
                list_id: this.list_id,
                list_type: this.list_type
            };
            this.isLoading = true;
            this.wishlist_error = '';
            var callback_function = (response_data) => {
                if(response_data.status == false) {
                    this.wishlist_error = response_data.status_message;
                    return false;
                }

                if(this.currentRouteName.includes('search')) {
                    let index = this.hotels.data.findIndex(listing => listing.id == this.list_id);
                    this.hotels.data[index].is_saved = true;
                    this.list_id = 0;
                }

                this.is_saved = true;
                let content = {title: response_data.status_text,message: response_data.status_message};
                flashMessage(content);
                closeModal('createWishlistModal');
                closeModal('saveToListModal');
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        removeFromWishlist(list_id,list_type) {
            var url = routeList.remove_from_wishlist;
            var data_params = {list_id,list_type};
            console.log(data_params);
            var callback_function = (response_data) => {
                if(response_data.status == false) {
                    this.wishlist_error = response_data.status_message;
                    return false;
                }

                if(this.currentRouteName == 'wishlist.list') {
                    destroySlider('.hotel-slider');
                    if (list_type == 'hotel') {
                        let index = this.wishlist_list.hotels.findIndex(listing => listing.id == list_id);
                        this.wishlist_list.hotels.splice(index, 1);
                    }
                    else {
                        let index = this.wishlist_list.experiences.findIndex(listing => listing.id == list_id);
                        this.wishlist_list.experiences.splice(index, 1);
                    }
                    setTimeout(() => {
                        updateSlider('.hotel-slider','search');
                    },10);
                }
                else if(this.currentRouteName.includes('search')) {
                    let index = this.hotels.data.findIndex(listing => listing.id == list_id);
                    this.hotels.data[index].is_saved = false;
                }
                else {
                    this.is_saved = false;
                }
                let content = {title: response_data.status_text,message: response_data.status_message};
                flashMessage(content);
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        updateOrRemoveWishlist(remove = false) {
            var url = (remove) ? routeList.destroy_wishlist : routeList.create_wishlist;
            var data_params = this.wishlist;
            this.wishlist_error = '';
            var callback_function = (response_data) => {
                if(response_data.status == false) {
                    this.wishlist_error = response_data.status_message;
                    return false;
                }
                closeModal('editWishlistModal');
                this.wishlist.saved_name = this.wishlist.wishlist_name;
                let content = {title: response_data.status_text,message: response_data.status_message};
                flashMessage(content);
            };

            this.makePostRequest(url,data_params,callback_function);
        },
    },
};