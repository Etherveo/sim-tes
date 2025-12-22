<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public $order;
    public $statusLabel;

    public function __construct(Order $order)
    {
        $this->order = $order;
        
        if ($order->status == 'ready') {
            $this->statusLabel = 'Siap Diambil';
        } elseif ($order->status == 'completed') {
            $this->statusLabel = 'Selesai';
        } else {
            $this->statusLabel = ucfirst($order->status);
        }
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'total_price' => $this->order->total_price,
            'message' => 'Status pesanan anda dengan kode order #' . $this->order->id . ' diperbarui menjadi: ' . $this->statusLabel,
            'time' => now(),
        ];
    }
}