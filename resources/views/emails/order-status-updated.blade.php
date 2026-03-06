<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Status Update</title>
<style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
    .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .header { background: #2c3e50; color: #fff; padding: 24px; text-align: center; }
    .header h1 { margin: 0; font-size: 20px; }
    .body { padding: 24px; color: #555; line-height: 1.6; }
    .status-badge { display: inline-block; padding: 8px 20px; border-radius: 20px; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
    .status-pending    { background: #ffc107; color: #333; }
    .status-confirmed  { background: #17a2b8; color: #fff; }
    .status-processing { background: #17a2b8; color: #fff; }
    .status-shipped    { background: #007bff; color: #fff; }
    .status-delivered  { background: #28a745; color: #fff; }
    .status-completed  { background: #28a745; color: #fff; }
    .status-cancelled  { background: #dc3545; color: #fff; }
    .order-details { background: #f9f9f9; border-radius: 6px; padding: 16px; margin: 16px 0; }
    .order-details table { width: 100%; }
    .order-details td { padding: 6px 0; color: #555; font-size: 13px; }
    .order-details td:first-child { color: #888; width: 40%; }
    .cta-btn { display: inline-block; margin-top: 20px; padding: 12px 28px; background: #2c3e50; color: #fff; text-decoration: none; border-radius: 6px; font-size: 14px; }
    .footer { background: #f9f9f9; padding: 16px 24px; text-align: center; font-size: 11px; color: #aaa; border-top: 1px solid #eee; }
</style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Status Update</h1>
        </div>
        <div class="body">
            <p>Hello <strong>{{ $order->customer->name ?? 'Valued Customer' }}</strong>,</p>

            <p>Your order status has been updated. Here are the details:</p>

            <div class="order-details">
                <table>
                    <tr>
                        <td>Order Number:</td>
                        <td><strong>{{ $order->invoice_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>Previous Status:</td>
                        <td>{{ ucfirst($oldStatus) }}</td>
                    </tr>
                    <tr>
                        <td>New Status:</td>
                        <td>
                            <span class="status-badge status-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Order Total:</td>
                        <td>{{ \App\Models\Setting::first()?->currency_symbol ?? '$' }} {{ number_format($order->final_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Order Date:</td>
                        <td>{{ \Carbon\Carbon::parse($order->sale_date)->format('d M Y') }}</td>
                    </tr>
                </table>
            </div>

            @if($order->status === 'shipped')
            <p>Your order is on its way! You will receive it shortly.</p>
            @elseif($order->status === 'delivered' || $order->status === 'completed')
            <p>Your order has been delivered. We hope you enjoy your purchase! Thank you for shopping with us.</p>
            @elseif($order->status === 'cancelled')
            <p>Your order has been cancelled. If you paid online, a refund will be processed shortly.</p>
            @elseif($order->status === 'confirmed')
            <p>Your order has been confirmed and is being prepared for shipment.</p>
            @endif

            <p>If you have any questions, feel free to contact our support team.</p>
        </div>
        <div class="footer">
            This email was automatically generated. Please do not reply to this email.
        </div>
    </div>
</body>
</html>
