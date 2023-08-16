<template>
    <div class="inbox-chat no-transition">
        <div class="row my-3" v-for="message in chatMessages">
            <div class="col-md-2 col-3 ps-0" v-if="message.user_from == other_user.id && message.message != ''">
                <img class="rounded-profile-image" :src="other_user.profile_picture_src" />
            </div>
            <div class="col-md-6 col-9" :class="{'offset-md-4 pe-md-5' : message.user_from == user.id ,'ps-md-5' : message.user_from != user.id }" v-if="message.message != ''">
                <div class="card shadow-card">
                    <div class="card-body" :class="message.user_from == user.id ? 'curve-right' : 'curve-left'" v-if="message.message != ''"> 
                        {{ message.message }} 
                    </div>
                </div>
                <div class="my-1" :class="message.user_from == other_user.id ? 'text-start' : 'text-end'">
                    <span class="text-small">
                        {{ message.sent_at }}
                    </span>
                </div>
            </div>
            <div class="col-md-2 col-3 ps-0" v-if="message.user_from == user.id && message.message != ''">
                <img class="rounded-profile-image" :src="user.profile_picture_src" />
            </div>

            <!-- Inbox Notification Text -->
            <div class="booking-notification py-4 mx-md-5 d-flex align-items-center w-100 text-uppercase" v-if="message.header_notification_text != ''">
                <span>
                    {{ message.header_notification_text }}
                </span>
                <span class="ps-0">
                    {{ message.sent_at }}
                </span>
            </div>
            <!-- Inbox Notification Text -->
        </div>
    </div>
</template>
<script>
export default {
    props: {
        messages: {
            type: Array,
            required: true
        },
        user: {
            type: Object,
            required: true
        },
        other_user: {
            type: Object,
            required: true
        }
    },
    methods: {
    },
    computed: {
        chatMessages() {
            return this.messages.slice().reverse();
        }
    }
};
</script>