<?php
?>

<form action="<?php echo site_url('payment/tes_checkout_order'); ?>" method="POST">
    <input type="hidden" name="order_id" value="16">
    <input type="submit" value="Pay with Snap Redirect">
</form>