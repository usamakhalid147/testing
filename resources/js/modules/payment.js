export default {
    otherData: {
        stripe:{},
        card:{},
        payment_method:'pay_at_hotel',
        coupon_code: {
            showInput: false,
            couponApplied: false,
            code: '',
            status_message: '',
        },
        pi_client_secret:'',
        paypal_purchase_data:[],
        payment_data:[],
        pricing_form:[],
        saved_payment_method:'',
        booking_attempt_id: '',
        hotel_id : '',
        booking_type: 'request_book',
        user_message:"",
    },
    components: {
    },
    methods: {
        initStripePayment() {
            // Create a Stripe client.
            this.stripe = Stripe(STRIPE_PUBLISH_KEY);

            // Create an instance of Elements.
            var elements = this.stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            // (Note that this demo uses a wider set of styles than the guide below.)
            var style = {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };

            // Create an instance of the card Element.
            this.card = elements.create('card', {
                hidePostalCode: true,
                style: style,
            });

            // Add an instance of the card Element into the `card-element` <div>.
            this.card.mount('#card-element');
            // Handle real-time validation errors from the card Element.
            this.card.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');
                displayError.textContent = '';
                if (event.error) {
                    displayError.textContent = event.error.message;
                }
            });
        },
        // Submit the form with the token ID.
        stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            document.getElementById('stripe-token').value = token.id;
            document.getElementById('payment-form').submit();
        },
        handleServerResponse(pi_client_secret) {
            var self = this;
            this.stripe.confirmCardPayment(pi_client_secret)
                .then(function(result) {
                if (result.error) {
                    var displayError = document.getElementById('card-errors');
                    displayError.textContent = '';
                    displayError.textContent = result.error.message;
                    self.isLoading = false;
                    
                    // document.getElementById("stripe-intent_id").value = '';
                }
                else {
                    // The card action has been handled & The PaymentIntent can be confirmed again on the server
                    document.getElementById("stripe-intent_id").value = result.paymentIntent.id;
                    document.getElementById('payment-form').submit();
                }
            });
        },
        initPaypalPayment() {
            var self = this;
            paypal.Buttons({
                createOrder(data, actions) {
                    // This function sets up the details of the transaction, including the amount and line item details.
                    return actions.order.create({
                        "purchase_units": [self.paypal_purchase_data],
                    });
                },
                onApprove(data, actions) {
                    self.isLoading = true;
                    var url = routeList.complete_payment;
                    var data_params = {
                        hotel_id : self.payment_data.hotel_id,
                        booking_attempt_id : booking_attempt_id.value,
                        payment_method : self.payment_method,
                        order_id : data.orderID
                    };
                    return axios.post(url,data_params)
                    .then((response) => {
                        var response_data = response.data;
                        if(response_data.status == 'redirect') {
                            window.location = response_data.redirect_url;
                        }
                        else if(response_data.status == 'false') {
                            let content = {title: response_data.status_text,message: response_data.status_message};
                            flashMessage(content,'danger');
                            self.isLoading = false;
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                }
            }).render('#paypal-button-container');
        },
        validateCoupon(type) {
            var data_params = {
                hotel_id : this.payment_data.hotel_id,
                booking_attempt_id : booking_attempt_id.value,
                coupon_code : this.coupon_code.code,
                payment_method : this.payment_method,
                type : type,
            };
            var url = routeList.validate_coupon;
            var callback = (response_data) => {
                this.isLoading = false;
                this.coupon_code.status_message = '';
                if(response_data.error) {
                    this.coupon_code.status_message = response_data.error_message;
                    return false;
                }
                if(type == 'remove') {
                    this.coupon_code.showInput = false;
                    this.coupon_code.code = '';
                }
                this.coupon_code.couponApplied = (type == 'apply');
                this.pricing_form = response_data.pricing_form;
                this.paypal_purchase_data = response_data.paypal_purchase_data;
                let content = {title: response_data.status_text,message: response_data.status_message};
                flashMessage(content);
            };
            this.isLoading = true;
            this.makePostRequest(url,data_params,callback);
        },
        nextStep() {
            this.isLoading = true;
            if(this.saved_payment_method != '' || this.payment_method == 'pay_at_hotel' || this.payment_method == 'one_pay') {
                document.getElementById('payment-form').submit();
                return true;
            }

            let self = this;
            this.stripe.createToken(this.card).then(function(result) {
                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                    self.isLoading = false;
                }
                else {
                    // Send the token to your server.
                    self.stripeTokenHandler(result.token);
                }
            });
            return true;
        },
        registerEvents() {
            this.initStripePayment();
            // this.initPaypalPayment();

            setTimeout( () => {
                var pi_client_secret = document.getElementById("pi_client_secret");
                if(pi_client_secret != undefined) {
                    if(pi_client_secret.value != '') {
                        this.isLoading = true;
                        this.handleServerResponse(pi_client_secret.value);
                    }
                }
            });
        }
    },
};