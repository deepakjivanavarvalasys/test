<?php

namespace App\Repository\Notification\Manager;

use App\Models\ManagerNotification;
use Illuminate\Support\Facades\DB;

class ManagerNotificationRepository implements ManagerNotificationInterface
{
    /**
     * @var ManagerNotification
     */
    private $managerNotification;

    public function __construct(
        ManagerNotification $managerNotification
    )
    {

        $this->managerNotification = $managerNotification;
    }

    public function get($filters = array())
    {
        $query = ManagerNotification::query();

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
            $manager_notification = new ManagerNotification();
            $manager_notification->sender_id = $attributes['sender_id'];
            $manager_notification->recipient_id = $attributes['recipient_id'];
            $manager_notification->message = $attributes['message'];
            $manager_notification->url = $attributes['url'];
            $manager_notification->save();
            if($manager_notification->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Notification added successfully');
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
            $notification = ManagerNotification::findOrFail($id);
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
