import Pagination from './../components/PaginationComponent.vue';
import InboxThread from './../components/InboxThreadComponent.vue';
export default {
	otherData: {
        translations: {},
        messages: {
            data: [],
            total: 0,
            per_page: 2,
            from: 1,
            to: 0,
            current_page: 1
        },
        offset: 4,
        inbox_filter: 'all',
    },
    components: {
    	Pagination,
    	InboxThread
	},
	methods: {
        getMessages() {
            var url = routeList.message_list+'?page='+this.messages.current_page;
            var data_params = {filter: this.inbox_filter};
            var callback_function = (response_data) => {
                this.messages = response_data
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        updateMessages() {
            this.messages.current_page = 1;
            this.getMessages();
        },
        updateMessageStatus(data_params) {
            var url = routeList.inbox_action;
            var callback_function = (response_data) => {
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        loadFunctions() {
            this.getMessages();
        },
	},
};