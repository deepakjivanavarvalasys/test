<?php

namespace App\Repository\History;

use App\Models\History;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistoryRepository implements HistoryInterface
{
    /**
     * @var History
     */
    private $history;

    public function __construct(History $history)
    {
        $this->history = $history;
    }

    public function get($filters = array())
    {
        $query = History::query();

        return $query->get();
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();

            $history = new History();

            $history->user_id = Auth::id();
            $history->action = $attributes['action'];
            $history->message = $attributes['message'];

            if(isset($attributes['data']) && !empty($attributes['data'])) {
                $history->data = json_encode($attributes['data']);
            } else {
                $history->data = '{}';
            }

            $history->save();

            if($history->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'History added successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }
}
