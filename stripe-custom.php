<?php
// Edited boucher/gist:1750375 to work with stripe-php v2
require 'vendor/autoload.php';
if ($_POST) {
    // 4111111111111111
  \Stripe\Stripe::setApiKey("sk_test_51K4vy2FXBQa8XZVzLB6LDmnUiduQ85lJHdAhgEXjXvD4n42XhfRm0a0moR7MLpGpr2Mghh0ofHBs8DIsdDogpnVY00kZIXXApV");
  $error = '';
  $success = '';
try {
        if (!isset($_POST['stripeToken']))
                throw new Exception("The Stripe Token was not generated correctly");

        $customer = \Stripe\Customer::create(array(
            'email' => $_POST['email'],
            'source'  => $_POST['stripeToken'],
        ));

        $charge = \Stripe\Charge::create(array(
                "amount" => (2.90*100), // amount in cents, again
                "currency" => "usd",
                "customer" => $customer->id,
                "description" => "Example charge")
        );
        if ($charge->status == 'succeeded') {
            $success = 'Your payment was sucessful.';
        } else {
            $success = 'Your payment failed.';
        }
} catch(\Stripe\Error\Card $e) {
        // The card has been declined
        $error = $e->getMessage();
}
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Stripe Getting Started Form</title>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <!-- jQuery is used only for this example; it isn't required to use Stripe -->
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
        <script type="text/javascript">

            function __validate_card_cvv_expiry_date() {
                $ = jQuery;
                Stripe.setPublishableKey('pk_test_51K4vy2FXBQa8XZVzDkqkRD5fOHNgD7DzxNbcqeeAz4PTM03ujvFFDHZhxegOLiDWRkcQUMUGNBFqSV5QYUc0XFBs00jFaFFYch');
                $('.stripe-payment-errors').html('');
                var validCardDetails = true;
                if (!Stripe.card.validateCardNumber($('.card-number').val())) {
                    validCardDetails = false;
                    $('.stripe-payment-errors').append('<p class="error-msg">Credit card number is invalid.</p>');
                }
                if (!Stripe.card.validateCVC($('.card-cvc').val())) {
                    validCardDetails = false;
                    $('.stripe-payment-errors').append('<p class="error-msg">CVC number is invalid.</p>');
                }
                if (!Stripe.card.validateExpiry($('.card-expiry-month').val(), $('.card-expiry-year').val())) {
                    validCardDetails = false;
                    $('.stripe-payment-errors').append('<p class="error-msg">Expiry date is invalid.</p>');
                }
                
                return validCardDetails;
            }

            // this identifies your website in the createToken call below
            Stripe.setPublishableKey('pk_test_51K4vy2FXBQa8XZVzDkqkRD5fOHNgD7DzxNbcqeeAz4PTM03ujvFFDHZhxegOLiDWRkcQUMUGNBFqSV5QYUc0XFBs00jFaFFYch');
            function stripeResponseHandler(status, response) {
                if (response.error) {
                    // re-enable the submit button
                    $('.submit-button').removeAttr("disabled");
                    // show the errors on the form
                    $(".payment-errors").html(response.error.message);
                } else {
                    var form$ = $("#payment-form");
                    // token contains id, last4, and card type
                    var token = response['id'];
                    // insert the token into the form so it gets submitted to the server
                    form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
                    // and submit
                    form$.get(0).submit();
                }
            }
            $(document).ready(function() {
                $("#payment-form").submit(function(event) {
                    // disable the submit button to prevent repeated clicks
                    $('.submit-button').attr("disabled", "disabled");
                    // createToken returns immediately - the supplied callback submits the form if there are no errors
                    if (__validate_card_cvv_expiry_date()) {
                        Stripe.createToken({
                            number: $('.card-number').val(),
                            cvc: $('.card-cvc').val(),
                            exp_month: $('.card-expiry-month').val(),
                            exp_year: $('.card-expiry-year').val()
                        }, stripeResponseHandler);

                    }
                    return false; // submit from callback
                });
            });
        </script>
    </head>
    <body>
        <h1>Charge $10 with Stripe</h1>
        <!-- to display errors returned by createToken -->
        <span class="payment-errors"><?= $error ?></span>
        <span class="payment-success"><?= $success ?></span>
        <form action="" method="POST" id="payment-form">
            <div class="form-row">
                <label>Email</label>
                <input type="text" name="email" placeholder="email">
            </div>
            <div class="form-row">
                <label>Card Number</label>
                <input type="text" size="16" autocomplete="off" class="card-number" maxlength="16" />
            </div>
            <div class="form-row">
                <label>CVC</label>
                <input type="text" size="4" autocomplete="off" class="card-cvc" maxlength="4" />
            </div>
            <div class="form-row">
                <label>Expiration (MM/YYYY)</label>
                <input type="text" size="2" class="card-expiry-month" maxlength="2"/>
                <span> / </span>
                <input type="text" size="4" class="card-expiry-year" maxlength="4"/>
            </div>
            <button type="submit" class="submit-button">Submit Payment</button>
        </form>
    </body>
</html>