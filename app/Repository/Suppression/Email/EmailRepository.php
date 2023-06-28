<?php

namespace App\Repository\Suppression\Email;

use App\Models\Data;
use App\Models\SuppressionEmail;
use Illuminate\Support\Facades\DB;
use Excel;

class EmailRepository implements EmailInterface
{

    /**
     * @var SuppressionEmail
     */
    private $suppressionEmail;

    public function __construct(
        SuppressionEmail $suppressionEmail
    )
    {
        $this->suppressionEmail = $suppressionEmail;
    }

    public function get($filters = array())
    {
        $query = SuppressionEmail::query();

        if(isset($filters['campaign_id']) && $filters['campaign_id']) {
            $query->whereIn('campaign_id', $filters['campaign_id']);
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

            $suppressionEmail = new SuppressionEmail();
            $suppressionEmail->campaign_id = $attributes['campaign_id'];
            $suppressionEmail->email = $attributes['email'];
            $suppressionEmail->save();

            if($suppressionEmail->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Email suppression added successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function bulkUpload($campaign_id, $file)
    {

        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();

            $excelData = Excel::toArray('', $file);
            array_shift($excelData[0]);

            $suppressionEmails = array();
            foreach ($excelData[0] as $key => $row) {
                $suppressionEmails[] = array(
                    'campaign_id' => $campaign_id,
                    'email' => trim($row[0]),
                );
            }

            if(!empty($suppressionEmails) && count($suppressionEmails)) {
                SuppressionEmail::insert($suppressionEmails);
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Suppression emails added successfully');
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
        // TODO: Implement update() method.
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
    }
}
