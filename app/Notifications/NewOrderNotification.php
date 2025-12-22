<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class NewOrderNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'user_name' => $this->order->user->nama_lengkap,
            'total_price' => $this->order->total_price,
            'message' => 'Pesanan baru dengan kode order #' . $this->order->id . ' dari ' . $this->order->user->nama_lengkap,
            'time' => now(),
        ];
    }
}