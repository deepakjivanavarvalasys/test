<?php

namespace App\Http\Controllers;

use App\Models\EMENotification;
use App\Models\ManagerNotification;
use App\Models\Module;
use App\Models\QANotification;
use App\Models\QATLNotification;
use App\Models\RANotification;
use App\Models\RATLNotification;
use App\Models\VMNotification;
use App\Repository\Notification\EME\EMENotificationRepository;
use App\Repository\Notification\Manager\ManagerNotificationRepository;
use App\Repository\Notification\QA\QANotificationRepository;
use App\Repository\Notification\QATL\QATLNotificationRepository;
use App\Repository\Notification\RA\RANotificationRepository;
use App\Repository\Notification\RATL\RATLNotificationRepository;
use App\Repository\Notification\VM\VMNotificationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * @var ManagerNotificationRepository
     */
    private $managerNotificationRepository;
    /**
     * @var RATLNotificationRepository
     */
    private $RATLNotificationRepository;
    /**
     * @var RANotificationRepository
     */
    private $RANotificationRepository;
    /**
     * @var QATLNotificationRepository
     */
    private $QATLNotificationRepository;
    /**
     * @var QANotificationRepository
     */
    private $QANotificationRepository;
    /**
     * @var EMENotificationRepository
     */
    private $EMENotificationRepository;
    /**
     * @var VMNotificationRepository
     */
    private $VMNotificationRepository;

    public function __construct(
        ManagerNotificationRepository $managerNotificationRepository,
        RATLNotificationRepository $RATLNotificationRepository,
        RANotificationRepository $RANotificationRepository,
        QATLNotificationRepository $QATLNotificationRepository,
        QANotificationRepository $QANotificationRepository,
        EMENotificationRepository $EMENotificationRepository,
        VMNotificationRepository $VMNotificationRepository
    )
    {
        $this->managerNotificationRepository = $managerNotificationRepository;
        $this->RATLNotificationRepository = $RATLNotificationRepository;
        $this->RANotificationRepository = $RANotificationRepository;
        $this->QATLNotificationRepository = $QATLNotificationRepository;
        $this->QANotificationRepository = $QANotificationRepository;
        $this->EMENotificationRepository = $EMENotificationRepository;
        $this->VMNotificationRepository = $VMNotificationRepository;
    }

    public function update($id, Request $request)
    {
        $response = array('status' => false, 'message' => 'Something went wrong.');
        try {
            $attributes = $request->all();

            $module = Module::whereRoleId(Auth::user()->role_id)->first();

            $attributes['read_status'] = 1;
            $id = base64_decode($id);
            switch ($module->slug) {
                case 'admin': break;
                case 'manager':
                    $response = $this->managerNotificationRepository->update($id, $attributes);
                    break;
                case 'team_leader':
                    $response = $this->RATLNotificationRepository->update($id, $attributes);
                    break;
                case 'agent':
                    $response = $this->RANotificationRepository->update($id, $attributes);
                    break;
                case 'qa_team_leader':
                    $response = $this->QATLNotificationRepository->update($id, $attributes);
                    break;
                case 'quality_analyst':
                    $response = $this->QANotificationRepository->update($id, $attributes);
                    break;
                case 'email_marketing_executive':
                    $response = $this->EMENotificationRepository->update($id, $attributes);
                    break;
                case 'vendor_manager':
                    $response = $this->VMNotificationRepository->update($id, $attributes);
                    break;
            }

            if($response['status'] == TRUE) {
                return redirect($response['data']->url);
            } else {
                return redirect()->back()->with('error', ['title' => 'Error', 'message' => $response['message']]);
            }
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function markAllAsRead(Request $request)
    {
        $response = array('status' => false, 'message' => 'Something went wrong.');
        try {
            $attributes = $request->all();

            $module = Module::whereRoleId(Auth::user()->role_id)->first();

            $attributes['read_status'] = 1;

            switch ($module->slug) {
                case 'admin': break;
                case 'manager':
                    $resultNotifications = $this->managerNotificationRepository->get(array('read_status' => 0));
                    foreach ($resultNotifications as $notification) {
                        $response = $this->managerNotificationRepository->update($notification->id, $attributes);
                    }
                    break;
                case 'team_leader':
                    $resultNotifications = $this->RATLNotificationRepository->get(array('read_status' => 0));
                    foreach ($resultNotifications as $notification) {
                        $response = $this->RATLNotificationRepository->update($notification->id, $attributes);
                    }
                    break;
                case 'agent':
                    $resultNotifications = $this->RANotificationRepository->get(array('read_status' => 0));
                    foreach ($resultNotifications as $notification) {
                        $response = $this->RANotificationRepository->update($notification->id, $attributes);
                    }
                    break;
                case 'qa_team_leader':
                    $resultNotifications = $this->QATLNotificationRepository->get(array('read_status' => 0));
                    foreach ($resultNotifications as $notification) {
                        $response = $this->QATLNotificationRepository->update($notification->id, $attributes);
                    }
                    break;
                case 'quality_analyst':
                    $resultNotifications = $this->QANotificationRepository->get(array('read_status' => 0));
                    foreach ($resultNotifications as $notification) {
                        $response = $this->QANotificationRepository->update($notification->id, $attributes);
                    }
                    break;
                case 'email_marketing_executive':
                    $resultNotifications = $this->EMENotificationRepository->get(array('read_status' => 0));
                    foreach ($resultNotifications as $notification) {
                        $response = $this->EMENotificationRepository->update($notification->id, $attributes);
                    }
                    break;
                case 'vendor_manager':
                    $resultNotifications = $this->VMNotificationRepository->get(array('read_status' => 0));
                    foreach ($resultNotifications as $notification) {
                        $response = $this->VMNotificationRepository->update($notification->id, $attributes);
                    }
                    break;
            }

            if($response['status'] == TRUE) {
                return response()->json(array('status' => true, 'message' => 'Notification marked as read.'));
            } else {
                return response()->json(array('status' => false, 'message' => $response['message']));
            }
        } catch (\Exception $exception) {
            return response()->json(array('status' => false, 'message' => 'Something went wrong.'));
        }
    }

}
