<?php
// https://sandbox.payhere.lk/pay/checkout

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $merchant_id = $_POST['merchant_id'];
    $order_id = $_POST['order_id'];
    $amount = $_POST['amount'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $address_street = $_POST['address_street'];
    $city = $_POST['city'];
    $hash = $_POST['hash'];
    $country = $_POST['country'];
    $currency = $_POST['currency'];

    if (!empty($merchant_id) || !empty($order_id) || !empty($amount) || !empty($first_name) || !empty($last_name) || !empty($email) || !empty($mobile) || !empty($address) || !empty($address_street) || !empty($city) || !empty($hash) || !empty($country) || !empty($currency)) {

        $return_url = "http://localhost/stayfinder/payment/payment.php?plan_id=" . urlencode($order_id) . 
        "&plan_id=" . urlencode($order_id) .
        "&amount=" . urlencode($amount) .
        "&firstname=" . urlencode($first_name) .
        "&lastname=" . urlencode($last_name) .
        "&email=" . urlencode($email) .
        "&contactNumber=" . urlencode($mobile) .
        "&addressNo=" . urlencode($address) .
        "&addressStreet=" . urlencode($address_street) .
        "&addressCity=" . urlencode($city) .
        "&country=" . urlencode($country) .
        "&currency=" . urlencode($currency) .
        "&hash=" . urlencode($hash) . "&payment=success";

        $data = [
            'merchant_id' => $merchant_id,
            'order_id' => $order_id,
            'amount' => $amount,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'mobile' => $mobile,
            'address' => $address,
            'address_street' => $address_street,
            'city' => $city,
            'country' => $country,
            'currency' => $currency,
            'hash' => $hash,
            'return_url' => $return_url
        ];

        $json_data = json_encode($data);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceed Payments</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
        }

        body {
            height: 100vh;
            background-image: linear-gradient(rgba(0,0,0,0.80),rgba(0,0,0,0.80)), url(../src-image/background-image.jpg);
            background-size: cover;
            background-position: center;
        }
    </style>

</head>
<body>
<script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
<script>
    // Parse the PHP JSON data into a JavaScript object
    var data = JSON.parse('<?php echo $json_data; ?>');

    payhere.onCompleted = function onCompleted(orderId) {
        console.log("Payment completed. OrderID: " + orderId);
        window.location.href = data.return_url;
    };

    payhere.onDismissed = function onDismissed() {
        console.log("Payment dismissed");
    };

    payhere.onError = function onError(error) {
        console.log("Error: " + error);
    };

    var payment = {
        "sandbox": true,
        "merchant_id": data.merchant_id,
        "return_url": data.return_url,
        "cancel_url": "http://localhost/cancel",
        "notify_url": "http://sample.com/notify",
        "order_id": data.order_id,
        "items": "Door bell wireless",
        "amount": data.amount,
        "currency": data.currency,
        "hash": data.hash,
        "first_name": data.first_name,
        "last_name": data.last_name,
        "email": data.email,
        "phone": data.mobile,
        "address": data.address,
        "city": data.city,
        "country": data.country,
    };

    // Automatically start the payment process on page load
    window.onload = function () {
        payhere.startPayment(payment);
    };
</script>
</body>
</html>
