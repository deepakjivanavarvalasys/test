<?php

use Illuminate\Support\Facades\Http;

if(!function_exists('add_campaign_history')) {
    function send_notification($sender_id, $recipient_id, $message, $url)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong');

        $resultUser = \App\Models\User::find($recipient_id);

        $attributes = array(
            'sender_id' => $sender_id,
            'recipient_id' => $recipient_id,
            'message' => $message,
            'url' => $url
        );

        switch ($resultUser->designation->slug) {
            case 'admin': break;

            case 'sr_program_manager_business_delivery':
            case 'deputy_program_manager_business_delivery':
                $response = \App\Repository\Notification\Manager\ManagerNotificationRepository::store($attributes);
                break;

            case 'ra_team_leader':
            case 'ra_team_leader_business_delivery':
                $response = \App\Repository\Notification\RATL\RATLNotificationRepository::store($attributes);
                break;

            case 'research_analyst':
                $response = \App\Repository\Notification\RA\RANotificationRepository::store($attributes);
                break;
            case 'email_marketing_executive':
                $response = \App\Repository\Notification\EME\EMENotificationRepository::store($attributes);
                break;

            case 'qa_team_leader':
                $response = \App\Repository\Notification\QATL\QATLNotificationRepository::store($attributes);
                break;
            case 'quality_analyst':
                $response = \App\Repository\Notification\QA\QANotificationRepository::store($attributes);
                break;
            case 'sr_vendor_management_specialist':
                $response = \App\Repository\Notification\VM\VMNotificationRepository::store($attributes);
                break;

            case 'mis_executive': break;
        }

        return $response;
    }
}
