import './app';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
const returnUrl = window.checkoutSuccessUrl;
const cancelUrl = window.checkoutCancelUrl;

$(document).ready(function () {
    const form = $('#payment-form');

    form.on('submit', async function(e) {
        e.preventDefault();
        const result = await stripe.confirmPayment({
            elements: elements,
            confirmParams: {
                return_url: returnUrl,
            },
            redirect: 'if_required',
        });

        // console.log(result.paymentIntent.status);
        if (result.paymentIntent.status == "succeeded") {
            $.ajax({
               url: '/create-order?action=createOrder',
               type: 'POST',
               data: {paymentId: result.paymentIntent.id},
               success: function (response) {
                    if(response.status == 200) {
                        window.location.href = response.redirect_url;
                    }
               }
            });
        }

        if (result.error) {
            console.error(result.error.message);
            console.log(result.error);
            alert("payment failed");
        }
    })
});
