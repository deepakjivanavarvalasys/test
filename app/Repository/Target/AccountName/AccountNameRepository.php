<?php

namespace App\Repository\Target\AccountName;

use App\Models\TargetAccountName;
use Illuminate\Support\Facades\DB;
use Excel;

class AccountNameRepository implements AccountNameInterface
{
    private $targetAccountName;

    public function __construct(
        TargetAccountName $targetAccountName
    )
    {
        $this->targetAccountName = $targetAccountName;
    }

    public function get($filters = array())
    {
        // TODO: Implement get() method.
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

            $targetAccountName = new TargetAccountName();
            $targetAccountName->campaign_id = $attributes['campaign_id'];
            $targetAccountName->account_name = $attributes['account_name'];
            $targetAccountName->save();

            if($targetAccountName->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Account name target added successfully');
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

            $targetAccountNames = array();
            foreach ($excelData[0] as $key => $row) {
                $targetAccountNames[] = array(
                    'campaign_id' => $campaign_id,
                    'account_name' => trim($row[0]),
                );
            }

            if(!empty($targetAccountNames) && count($targetAccountNames)) {
                TargetAccountName::insert($targetAccountNames);
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Target account names added successfully');
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
