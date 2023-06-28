<?php

namespace App\Repository\Suppression\AccountName;

use App\Models\SuppressionAccountName;
use Illuminate\Support\Facades\DB;
use Excel;

class AccountNameRepository implements AccountNameInterface
{
    private $suppressionAccountName;

    public function __construct(
        SuppressionAccountName $suppressionAccountName
    )
    {
        $this->suppressionAccountName = $suppressionAccountName;
    }

    public function get($filters = array())
    {
        $query = SuppressionAccountName::query();

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

            $suppressionAccountName = new SuppressionAccountName();
            $suppressionAccountName->campaign_id = $attributes['campaign_id'];
            $suppressionAccountName->account_name = $attributes['account_name'];
            $suppressionAccountName->save();

            if($suppressionAccountName->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Account name suppression added successfully');
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

            $suppressionAccountNames = array();
            foreach ($excelData[0] as $key => $row) {
                $suppressionAccountNames[] = array(
                    'campaign_id' => $campaign_id,
                    'account_name' => trim($row[0]),
                );
            }

            if(!empty($suppressionAccountNames) && count($suppressionAccountNames)) {
                SuppressionAccountName::insert($suppressionAccountNames);
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Suppression account names added successfully');
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
