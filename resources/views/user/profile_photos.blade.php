<div class="row">
	<div class="update_form col-md-9 mt-4 px-0">
		<div class="container profile_photo_section">
			<div class="card default-hover">
				<div class="card-header bg-light">
					<p class="h5 card-title"> @lang('messages.profile_photo') </p>
				</div>
				<div class="card-body photos-section">
					<div class="row">
						<div class="col flex-grow-0 text-center" :class="{'loading' : isLoading}">
							<div class="position-relative crop-img">
								<div class="position-absolute cropout-img">
									<img class="img-fluid" :src="user.profile_picture_src"/>
								</div>
								<div class="rounded-circle">
									<img class="img-fluid" :src="user.profile_picture_src"/>
								</div>
								<a href="#" class="common-link right_corner_icon hover-card text-white" v-on:click="removeProfilePicture();">
									<i class="icon icon-delete" area-hidden="true"></i>
								</a>
							</div>
						</div>
						<div class="col mt-md-0 mt-3">
							<p class="h6 text-justify lh-base"> @lang('messages.profile_photos_desc2',['replace_key_1' => $site_name]) </p>
							<button type="button" class="bg-white btn btn-block mt-4 text-black-50 w-100" onclick="$('#profile_picture').trigger('click');" :disabled="isLoading">
							 @lang('messages.upload_file')
							</button>
							<input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*" v-on:change="saveProfilePicture($event);" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>