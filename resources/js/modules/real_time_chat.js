export default {
	otherData: {
	},
	components: {
	},
	methods: {
		updateReadStatus() {
            var url = routeList.update_read_status;
            var data_params = this.getdataParams();

            var callback_function = (response_data) => {
            };

            this.makePostRequest(url,data_params,callback_function);
        },
        initFirebase() {
			firebase.initializeApp(firebaseConfig);
            function onAuthStateChanged(user) {
                if(!user && authToken) {
                    firebase.auth().signInWithCustomToken(authToken).then((userCredential) => {
                        window.localStorage.setItem('firebase_auth_token', authToken);
                    })
                    .catch(function(error) {
                        window.localStorage.removeItem('firebase_auth_token');
                    });
                }
                else {
                    if(authToken != window.localStorage.getItem('firebase_auth_token')) {
                        firebase.auth().signOut().then(function() {
                            window.localStorage.removeItem('firebase_auth_token');
                        })
                        .catch(function(error) {
                        });
                    }
                }
            }
            firebase.auth().onAuthStateChanged(onAuthStateChanged);
		},
		listenDatabase() {
            let ref = firebasePrefix+'/users/'+USER_ID+'/messages/';
            let messagesRef = firebase.database().ref(ref);
            messagesRef.on('child_added',(snapshot) => {
                let data = snapshot.val();
                let message = data.message;
                if(this.currentRouteName == 'conversation') {
                    this.chat_messages.push(message);
                    this.updateReadStatus();
                }
                else {
                    this.inbox_count = data.inbox_count;
                }
                firebase.database().ref(ref).remove();
            });
        },
        listenAfterAuthenticate() {
            if(authToken == window.localStorage.getItem('firebase_auth_token')) {
                this.listenDatabase();
            }
            else {
                setTimeout( () => {
                    this.listenAfterAuthenticate();
                },1000);
            }
        },
	},
};