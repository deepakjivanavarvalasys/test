<?php

namespace App\Repository\Notification\VM;

use App\Models\VMNotification;
use Illuminate\Support\Facades\DB;

class VMNotificationRepository implements VMNotificationInterface
{
    /**
     * @var VMNotification
     */
    private $VMNotification;

    public function __construct(VMNotification $VMNotification)
    {
        $this->VMNotification = $VMNotification;
    }

    public function get($filters = array())
    {
        $query = VMNotification::query();

        if(array_key_exists('read_status', $filters)) {
            $query->whereReadStatus($filters['read_status']);
        }

        return $query->get();
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $notification = new VMNotification();

            $notification->sender_id = $attributes['sender_id'];
            $notification->recipient_id = $attributes['recipient_id'];
            if(array_key_exists('read_status', $attributes)) {
                $notification->read_status = $attributes['read_status'];
            }
            $notification->message = $attributes['message'];
            if(isset($attributes['url']) && !empty($attributes['url'])) {
                $notification->url = $attributes['url'];
            }
            $notification->save();

            if($notification->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Notification updated successfully', 'details' => $notification);
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function update($id, $attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $notification = VMNotification::findOrFail($id);
            if(isset($attributes['sender_id']) && !empty($attributes['sender_id'])) {
                $notification->sender_id = $attributes['sender_id'];
            }
            if(isset($attributes['recipient_id']) && !empty($attributes['recipient_id'])) {
                $notification->recipient_id = $attributes['recipient_id'];
            }
            if(isset($attributes['message']) && !empty($attributes['message'])) {
                $notification->message = $attributes['message'];
            }
            if(isset($attributes['url']) && !empty($attributes['url'])) {
                $notification->url = $attributes['url'];
            }
            if(array_key_exists('read_status', $attributes)) {
                $notification->read_status = $attributes['read_status'];
            }
            $notification->save();
            if($notification->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Notification updated successfully', 'data' => $notification);
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
    }
}
