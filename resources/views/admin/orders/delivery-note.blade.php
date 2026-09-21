<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.delivery_note') }} - {{ $order->order_number }} - POWER</title>
    <style>
        @page { size: A4; margin: 15mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; background: #f3f4f6; font: 14px/1.5 Arial, sans-serif; }
        .toolbar { max-width: 210mm; margin: 20px auto; padding: 0 20px; display: flex; justify-content: space-between; gap: 16px; align-items: center; flex-wrap: wrap; }
        .toolbar a { color: #333; }
        .toolbar button { border: 0; border-radius: 8px; padding: 12px 20px; color: white; background: #111; font: inherit; font-weight: 700; cursor: pointer; }
        .sheet { width: 100%; max-width: 210mm; margin: 0 auto 30px; padding: 15mm; background: white; }
        .heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; padding-bottom: 20px; border-bottom: 2px solid #111; }
        .brand { font-size: 25px; font-weight: 800; }
        h1 { font-size: 23px; margin: 0 0 6px; }
        h2 { font-size: 13px; text-transform: uppercase; letter-spacing: .06em; margin: 0 0 12px; }
        p { margin: 4px 0; overflow-wrap: anywhere; }
        .reference { text-align: end; }
        .order-number { font-size: 18px; font-weight: 700; }
        .muted { color: #555; font-size: 12px; }
        .recipient { margin: 24px 0; padding: 18px; border: 1px solid #aaa; border-radius: 8px; }
        .recipient-name { font-size: 20px; font-weight: 700; }
        .phone { font-size: 19px; font-weight: 700; }
        .address, .notes { white-space: pre-line; }
        .notes { margin-top: 18px; padding-top: 12px; border-top: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { padding: 12px 8px; text-align: start; vertical-align: top; border-bottom: 1px solid #ddd; overflow-wrap: anywhere; }
        th { font-size: 12px; border-bottom: 2px solid #111; }
        .product { width: 48%; }
        .quantity { width: 12%; text-align: center; }
        .money { width: 20%; text-align: end; }
        .totals { width: 65%; margin: 20px 0 0 auto; break-inside: avoid; }
        [dir="rtl"] .totals { margin: 20px auto 0 0; }
        .total-row { display: flex; justify-content: space-between; gap: 20px; padding: 5px 0; }
        .amount { border-top: 2px solid #111; margin-top: 8px; padding-top: 12px; font-size: 18px; font-weight: 700; }
        .payment { text-align: end; font-size: 12px; }
        .signatures { display: flex; gap: 36px; margin-top: 42px; break-inside: avoid; }
        .signature { flex: 1; min-height: 80px; border-top: 1px solid #777; padding-top: 8px; font-size: 12px; }
        thead { display: table-header-group; }
        tr { break-inside: avoid; }
        @media screen and (max-width: 600px) {
            .sheet { padding: 20px; }
            .heading { flex-wrap: wrap; }
            .reference { text-align: start; }
            .totals { width: 100%; }
            th, td { padding: 8px 4px; font-size: 12px; }
        }
        @media print {
            body { background: white; font-size: 11pt; }
            .toolbar { display: none !important; }
            .sheet { max-width: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <nav class="toolbar">
        <a href="{{ route('admin.orders.show', $order) }}">{{ __('admin.back_to_order') }}</a>
        <button type="button" onclick="window.print()">{{ __('admin.print_or_save_pdf') }}</button>
    </nav>
    <main class="sheet">
        <header class="heading">
            <div>
                <div class="brand"><bdi>POWER.</bdi></div>
                <h1>{{ __('admin.delivery_note') }}</h1>
            </div>
            <div class="reference">
                <p class="order-number"><bdi>#{{ $order->order_number }}</bdi></p>
                <p>{{ __('admin.date') }} : <bdi>{{ $order->created_at->format('d/m/Y H:i') }}</bdi></p>
                <p class="muted">{{ __('admin.status') }} : {{ $order->status->label() }}</p>
            </div>
        </header>

        <section class="recipient">
            <h2>{{ __('admin.delivery_recipient') }}</h2>
            <p class="recipient-name" dir="auto">{{ $order->fullName() }}</p>
            <p class="phone">{{ __('storefront.phone') }} : <bdi>{{ $order->phone }}</bdi></p>
            <p class="address" dir="auto">{{ $order->address }}</p>
            <p><strong><bdi>{{ $order->city }}</bdi></strong></p>
            @if ($order->notes)
                <div class="notes"><strong>{{ __('admin.delivery_notes') }} :</strong><p dir="auto">{{ $order->notes }}</p></div>
            @endif
        </section>

        <h2>{{ __('admin.order_items') }}</h2>
        <table>
            <thead>
                <tr>
                    <th scope="col" class="product">{{ __('admin.product') }}</th>
                    <th scope="col" class="quantity">{{ __('storefront.quantity') }}</th>
                    <th scope="col" class="money">{{ __('admin.price') }}</th>
                    <th scope="col" class="money">{{ __('storefront.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            <strong><bdi>{{ $item->product_name }}</bdi></strong>
                            @if ($item->size_label)<div class="muted">{{ __('storefront.size') }} : <bdi>{{ $item->size_label }}</bdi></div>@endif
                            @if ($item->color_name)<div class="muted">{{ __('storefront.color') }} : <bdi>{{ $item->color_name }}</bdi></div>@endif
                        </td>
                        <td class="quantity">{{ $item->quantity }}</td>
                        <td class="money"><bdi>{{ number_format($item->unit_price, 3) }} {{ __('storefront.currency') }}</bdi></td>
                        <td class="money"><bdi>{{ number_format($item->line_total, 3) }} {{ __('storefront.currency') }}</bdi></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <section class="totals">
            <div class="total-row"><span>{{ __('storefront.subtotal') }}</span><bdi>{{ number_format($order->subtotal, 3) }} {{ __('storefront.currency') }}</bdi></div>
            @if ($order->discount_amount > 0)
                <div class="total-row"><span>{{ __('storefront.discount') }}</span><bdi>-{{ number_format($order->discount_amount, 3) }} {{ __('storefront.currency') }}</bdi></div>
            @endif
            <div class="total-row"><span>{{ __('storefront.shipping') }}</span><bdi>{{ number_format($order->shipping_fee, 3) }} {{ __('storefront.currency') }}</bdi></div>
            <div class="total-row amount">
                <span>{{ in_array($order->status, [\App\Enums\OrderStatus::Delivered, \App\Enums\OrderStatus::Cancelled]) ? __('storefront.total') : __('admin.amount_to_collect') }}</span>
                <bdi>{{ number_format($order->total, 3) }} {{ __('storefront.currency') }}</bdi>
            </div>
            <p class="payment">{{ __('storefront.cod_only') }}</p>
        </section>

        <footer class="signatures">
            <div class="signature">{{ __('admin.courier_signature') }}</div>
            <div class="signature">{{ __('admin.recipient_signature') }}</div>
        </footer>
    </main>
</body>
</html>
