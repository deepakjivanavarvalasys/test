<?php

namespace App\Repository\Target\Domain;

use App\Models\TargetDomain;
use Illuminate\Support\Facades\DB;
use Excel;

class DomainRepository implements DomainInterface
{
    private $targetDomain;

    public function __construct(
        TargetDomain $targetDomain
    )
    {
        $this->targetDomain = $targetDomain;
    }

    public function get($filters = array())
    {
        $query = TargetDomain::query();

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

            $targetDomain = new TargetDomain();
            $targetDomain->campaign_id = $attributes['campaign_id'];
            $targetDomain->domain = $attributes['domain'];
            $targetDomain->save();

            if($targetDomain->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Domain target added successfully');
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

            $targetDomains = array();
            foreach ($excelData[0] as $key => $row) {
                $targetDomains[] = array(
                    'campaign_id' => $campaign_id,
                    'domain' => trim($row[0]),
                );
            }

            if(!empty($targetDomains) && count($targetDomains)) {
                TargetDomain::insert($targetDomains);
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Target domains added successfully');
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
