<template>
<ul class="inbox-thread px-0">
	<li class="d-flex px-2 py-3 my-2 inbox-card hover-card align-items-md-center align-items-start border" v-for="(message,index) in messages" :class="{ 'unread': message.read != '1'}">
		<div class="col-3 col-md-2 col-lg-1">
			<a :href="message.user_link">
				<img class="rounded-profile-image-normal" :title="message.user_name" :src="message.profile_picture_src">
			</a>
		</div>
		<div class="col-9 col-md-10 col-lg-11 p-0 d-md-flex mt-1">
			<div class="ps-md-0 ps-lg-2 col-12 col-md-3">
				<h5 class="user-name"> {{ message.user_name }} </h5>
				<p class="text-truncate"> {{ message.since }} </p>
			</div>
			<div class="col-12 col-md-5 my-1 my-md-0 pe-0 pe-md-3">
				<a class="inbox-link" :href="message.target_link">
				<p class="position-relative text-line-1">
					{{ message.message }}
				    <span class="float-end badge badge-primary message-thread-count text-danger" v-show="message.unread_count > 1"> {{ message.unread_count }} </span>
				</p>
				<p class="text-small h6 text-line-1"> {{ message.formatted_address }} </p>
				</a>
			</div>
			<div class="list-status col-12 col-md-2 my-1 my-md-0">
				<span class="d-block font-weight-bold">
					{{ message.status }}
				</span>
				<span class="total-price">
					{{ message.currency_symbol }} {{ message.total }}
				</span>
			</div>
			<div class="col-md-2 ps-md-0 inbox-thread-actions mt-1 ms-auto">
				<a href="javascript:void(0);" class="d-flex align-items-center" v-on:click="updateMessageStatus(index,'star')">
					<span class="icon icon-inbox-star" v-if="message.star == 0"></span>
					<span class="icon icon-inbox-star-filled" v-else></span>
					<span class="ms-1" v-if="message.star == 0"> {{ translations.star }} </span>
					<span class="ms-1" v-else> {{ translations.unstar }} </span>
				</a>
				<a href="javascript:void(0);" class="d-flex align-items-center text-red" v-on:click="updateMessageStatus(index,'archive')">
					<span class="icon icon-outlined icon-archive" v-if="message.archive == 0"></span>
					<span class="icon icon-archive" v-else></span>
					<span class="ms-1" v-if="message.archive == 0"> {{ translations.archive }} </span>
					<span class="ms-1" v-else> {{ translations.unarchive }} </span>
				</a>
			</div>
		</div>
	</li>
</ul>
</template>
<script>
	export default {
		props: {
			messages: {
				type: Array,
				required: true,
			},
			translations: {
				type: Object,
			},
		},
		methods : {
			updateMessageStatus(index,type) {
				let message = this.messages[index];
				let dataParams = {};
				dataParams['message_id'] = message.id;
				dataParams['user_id'] = message.user_id;
				dataParams['type'] = type;
				dataParams['user_type'] = (USER_ID == message.user_id) ? 'guest' : 'host';
				if(type == 'star') {
					dataParams['action'] = (message.star == 1) ? '0' : '1';
					message.star = (message.star == 1) ? 0 : 1;
				}
				if(type == 'archive') {
					dataParams['action'] = message.archive == 1 ? '0' : '1';
					this.messages.splice(index, 1);
				}
				this.$emit('inbox_status_updated',dataParams);
			}
		},
	};
</script>
<style scoped>
.inbox-thread {
	background: #FFFFFF;
	color: black;
	text-decoration: none;
	font-weight: 100;
}

</style>