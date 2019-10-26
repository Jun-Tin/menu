<?php

namespace App\Events\Wokerman;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Events
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    public static function onWorkerStart($businessWorker)
    {

    }

    public static function onConnect($client_id)
    {
        Gateway::sendToClient($client_id, json_encode(['type' => 'init', 'client_id' => $client_id]));
    }

    public static function onWebSocketConnect($client_id, $data)
    {
    }

    public static function onMessage($client_id, $message)
    {
        $response = ['errcode' => 0, 'msg' => 'ok', 'data' => []];
        $message = json_decode($message);

        if (!isset($message->mode)) {
            $response['msg'] = 'missing parameter mode';
            $response['errcode'] = ERROR_CHAT;
            Gateway::sendToClient($client_id, json_encode($response));
            return false;
        }

        switch ($message->mode) {
            case 'say':   #处理发送的聊天
                // if (self::authentication($message->order_id, $message->user_id)) {
                //     OrderChat::store($message->order_id, $message->type, $message->content, $message->user_id);
                // } else {
                //     $response['msg'] = 'Authentication failure';
                //     $response['errcode'] = ERROR_CHAT;
                // }
                $response['data'] = ['chats' => 'testing'];
                break;
            case 'chats':  #获取聊天列表
                $chats = OrderChat::where('order_id', $message->order_id)->get();
                $response['data'] = ['chats' => $chats];
                break;
            default:
                $response['errcode'] = ERROR_CHAT;
                $response['msg'] = 'Undefined';
        }

        Gateway::sendToClient($client_id, json_encode($response));
    }

    public static function onClose($client_id)
    {
        Log::info('close connection' . $client_id);
    }

    private static function authentication($order_id, $user_id): bool
    {
        $order = Order::find($order_id);
        if (is_null($order)) {
            return false;
        }
        return in_array($user_id, [$order->user_id, $order->to_user_id]) ? true : false;   #判断属不属于这个订单的两个人
    }
}
