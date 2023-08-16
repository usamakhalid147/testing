export default {
	otherData: {
        help_categories:[],
        currentCategory:0,
        help_category: {
            helps: [],
        },
        search_text: '',
        show_result: false,
        error_messages: {},
	},
	components: {},
	methods: {
        chooseCategory(help_category) {
            this.help_category = help_category;
            this.currentCategory = this.help_category.id;
        },
        toggleHelp(id) {
            let elem = document.getElementById('#category-help-'+id);
            let collapse = new bootstrap.Collapse(elem, {toggle: false});
            collapse.toggle();
        },
        closeAllCategories() {
            this.help_category = {};
            this.currentCategory = 0;
        },
        searchFilter() {
            if (this.search_text == '') {
                this.show_result = false;
                this.isLoading = false;
                return true;
            }
            this.isLoading = true;
            var url = routeList.help_search_result;
            var data_params = { 'search_text' : this.search_text };
            var callback_function = (response_data) => {
                this.show_result = true;
                if (!response_data.status) {
                    this.help_category.helps = [];
                    this.error_messages = response_data.status_message;
                }
                if (response_data.status) {
                    this.help_category.helps = response_data.data;
                }
            };
            this.makePostRequest(url,data_params,callback_function);
        },
    },
};