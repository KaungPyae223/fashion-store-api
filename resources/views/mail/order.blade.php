<div style="font-family: Arial, sans-serif; text-align: center; padding: 20px; background-color: #f8f8f8;">
    <div style="background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <img src="{{ asset('/storage/images/alexa-fashion-logo.svg') }}" alt="Alexa Fashion Store" style="max-width: 150px; margin-bottom: 20px;">
        <h2 style="color: #333;">Your Order is Complete</h2>
        <p style="color: #555; font-size: 16px;">Thank you for shopping with <strong>Alexa Fashion Store</strong>. Your order has been successfully placed.</p>
        <p style="color: #555; font-size: 16px;">We will send you the tracking details shortly.</p>

        <!-- Order Button -->
        <a href="{{ url($url) }}"
           style="display: inline-block; padding: 10px 20px; margin-top: 15px; color: white; background-color: #007bff; text-decoration: none; border-radius: 5px;">
           View Order Details
        </a>

        <p style="color: #888; font-size: 14px;">If you have any questions, feel free to contact our support team.</p>
    </div>
</div>
