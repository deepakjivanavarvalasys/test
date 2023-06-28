<?php

namespace App\Repository\DataRepository;

use App\Models\AgentData;
use App\Models\CampaignAssignAgent;
use App\Models\Data;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class DataRepository implements DataInterface
{
    /**
     * @var Data
     */
    private $data;

    public function __construct(Data $data)
    {

        $this->data = $data;
    }

    
    public function get($filters = array(), $suppressionList = array(), $targetList = array())
    {
        $query = Data::query();

        if(isset($filters['job_level']) && !empty($filters['job_level'])) {
            $query->whereIn('job_level', $filters['job_level']);
        }

        if(isset($filters['job_role']) && !empty($filters['job_role'])) {
            $query->whereIn('job_role', $filters['job_role']);
        }

        if(isset($filters['employee_size']) && !empty($filters['employee_size'])) {
            $query->whereIn('employee_size', $filters['employee_size']);
        }

        if(isset($filters['revenue']) && !empty($filters['revenue'])) {
            $query->whereIn('revenue', $filters['revenue']);
        }

        if(isset($filters['country']) && !empty($filters['country'])) {
            $query->whereIn('country', $filters['country']);
        }

        if(isset($filters['state']) && !empty($filters['state'])) {
            $query->whereIn('state', $filters['state']);
        }

        if(!empty($suppressionList)) {
            if(isset($suppressionList['suppression_email']) && !empty($suppressionList['suppression_email']) && $suppressionList['suppression_email']->count()) {
                $query->whereNotIn('email_address', $suppressionList['suppression_email']->pluck('email')->toArray());
            }

            if(isset($suppressionList['suppression_domain']) && !empty($suppressionList['suppression_domain']) && $suppressionList['suppression_domain']->count()) {
                $query->whereNotIn('company_domain', $suppressionList['suppression_domain']->pluck('domain')->toArray());
            }

            if(isset($suppressionList['suppression_account_name']) && !empty($suppressionList['suppression_account_name']) && $suppressionList['suppression_account_name']->count()) {
                $query->whereNotIn('company_name', $suppressionList['suppression_account_name']->pluck('account_name')->toArray());
            }
        }

        if(!empty($targetList)) {
            if(isset($targetList['target_domain']) && !empty($targetList['target_domain']) && $targetList['target_domain']->count()) {
                $query->whereIn('company_domain', $targetList['target_domain']->pluck('domain')->toArray());
            }
        }

        if(isset($filters['ca_ratl_id']) && !empty($filters['ca_ratl_id'])) {
            //Get Data Ids Already Assigned and live
            $resultCAAgents = CampaignAssignAgent::where('campaign_assign_ratl_id', base64_decode($filters['ca_ratl_id']))->get();
            $ca_agent_ids = $resultCAAgents->pluck('id')->toArray();

            $resultAgentData = AgentData::whereIn('ca_agent_id', $ca_agent_ids);
            $agent_data_ids = $resultAgentData->pluck('data_id')->toArray();

            $query->whereNotIn('id', $agent_data_ids);
        }


        if(isset($filters['limit']) && !empty($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        $query->inRandomOrder();
        $query->orderBy('first_name');
        //dd($query->get()->toArray());
        return $query->get();
    }

    public function find($id)
    {
        $query = Data::query();
        return $query->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();

            //$campaign->save();

            if(0) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Data added successfully');
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

            $data = $this->find($id);

            if(isset($attributes['first_name']) && !empty($attributes['first_name'])) {
                $data->first_name = trim($attributes['first_name']);
            }
            if(isset($attributes['last_name']) && !empty($attributes['last_name'])) {
                $data->last_name = trim($attributes['last_name']);
            }
            if(isset($attributes['company_name']) && !empty($attributes['company_name'])) {
                $data->company_name = trim($attributes['company_name']);
            }
            if(isset($attributes['email_address']) && !empty($attributes['email_address'])) {
                $data->email_address = trim($attributes['email_address']);
            }
            if(isset($attributes['specific_title']) && !empty($attributes['specific_title'])) {
                $data->specific_title = trim($attributes['specific_title']);
            }
            if(array_key_exists('job_level', $attributes)) {
                $data->job_level = trim($attributes['job_level']);
            }
            if(array_key_exists('job_role', $attributes)) {
                $data->job_role = trim($attributes['job_role']);
            }
            if(isset($attributes['phone_number']) && !empty($attributes['phone_number'])) {
                $data->phone_number = trim($attributes['phone_number']);
            }
            if(isset($attributes['address_1']) && !empty($attributes['address_1'])) {
                $data->address_1 = trim($attributes['address_1']);
            }
            if(array_key_exists('address_2', $attributes)) {
                $data->address_2 = trim($attributes['address_2']);
            }
            if(isset($attributes['city']) && !empty($attributes['city'])) {
                $data->city = trim($attributes['city']);
            }
            if(isset($attributes['state']) && !empty($attributes['state'])) {
                $data->state = trim($attributes['state']);
            }
            if(isset($attributes['zipcode']) && !empty($attributes['zipcode'])) {
                $data->zipcode = trim($attributes['zipcode']);
            }
            if(isset($attributes['country']) && !empty($attributes['country'])) {
                $data->country = trim($attributes['country']);
            }
            if(isset($attributes['industry']) && !empty(trim($attributes['industry']))) {
                $data->industry = $attributes['industry'];
            }
            if(isset($attributes['employee_size']) && !empty($attributes['employee_size'])) {
                $data->employee_size = trim($attributes['employee_size']);
            }
            if(isset($attributes['employee_size_2']) && !empty($attributes['employee_size_2'])) {
                $data->employee_size_2 = trim($attributes['employee_size_2']);
            }
            if(isset($attributes['revenue']) && !empty($attributes['revenue'])) {
                $data->revenue = trim($attributes['revenue']);
            }
            if(isset($attributes['company_domain']) && !empty($attributes['company_domain'])) {
                $data->company_domain = trim($attributes['company_domain']);
            }
            if(array_key_exists('website', $attributes)) {
                $data->website = trim($attributes['website']);
            }
            if(isset($attributes['company_linkedin_url']) && !empty($attributes['company_linkedin_url'])) {
                $data->company_linkedin_url = trim($attributes['company_linkedin_url']);
            }
            if(isset($attributes['linkedin_profile_link']) && !empty($attributes['linkedin_profile_link'])) {
                $data->linkedin_profile_link = trim($attributes['linkedin_profile_link']);
            }
            if(array_key_exists('linkedin_profile_sn_link', $attributes)) {
                $data->linkedin_profile_sn_link = trim($attributes['linkedin_profile_sn_link']);
            }
            if(isset($attributes['comment']) && !empty(trim($attributes['comment']))) {
                $data->comment = $attributes['comment'];
            }

            if(isset($attributes['status'])) {
                $data->status = trim($attributes['status']);
            }

            $data->updated_by = Auth::id();

            if($data->update()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Data updated successfully');
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

    public function import($file): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', '0');

            $excelData = Excel::toArray('', $file);
            $inputExcelFormat = implode(',', $excelData[0][0]);
            $excelFormat = "first_name,last_name,company_name,email_address,specific_title,job_level,job_role,phone_number,address_1,address_2,city,state,zipcode,country,industry,employee_size,revenue,company_domain,website,company_linkedin_url,linkedin_profile_link,linkedin_profile_sn_link";

            if($inputExcelFormat == $excelFormat) {
                array_shift($excelData[0]);
                $totalDataCount = count($excelData[0]);

                if($totalDataCount < 25000) {
                    $failedData = array();
                    $validData = array();
                    $updated_by = Auth::id();
                    foreach ($excelData[0] as $key => $row) {
                        $leadData = array(
                            'first_name' => trim($row[0]),
                            'last_name' => trim($row[1]),
                            'company_name' => trim($row[2]),
                            'email_address' => trim($row[3]),
                            'specific_title' => trim($row[4]),
                            'job_level' => trim($row[5]),
                            'job_role' => trim($row[6]),
                            'phone_number' => trim($row[7]),
                            'address_1' => trim($row[8]),
                            'address_2' => trim($row[9]),
                            'city' => trim($row[10]),
                            'state' => trim($row[11]),
                            'zipcode' => trim($row[12]),
                            'country' => trim($row[13]),
                            'industry' => trim($row[14]),
                            'employee_size' => trim($row[15]),
                            'revenue' => trim($row[16]),
                            'company_domain' => trim($row[17]),
                            'website' => trim($row[18]),
                            'company_linkedin_url' => trim($row[19]),
                            'linkedin_profile_link' => trim($row[20]),
                            'linkedin_profile_sn_link' => trim($row[21]),
                            'updated_by' => $updated_by,
                            'status' => 1,
                        );
                        $resultValidate = $this->validateLeadData($leadData);
                        if(empty($resultValidate)) {
                            $validData[] = $leadData;
                        } else {
                            $failedData[] = $leadData;
                        }
                    }

                    //dd($validData);

                    if(count($validData)) {
                        DB::disableQueryLog();
                        $finalData = array_chunk($validData, 2500);
                        foreach ($finalData as $key => $chunk) {
                            if(!DB::table('data')->insert($chunk)) {
                                throw new \Exception('Something went wrong, please try again.', 1);
                            }
                            //Data::insert($validData);
                        }
                        DB::commit();
                        $response = array('status' => TRUE, 'message' => 'Data imported successfully', 'data' => ['success_count' => count($validData), 'failed_data' => $failedData]);
                    } else {
                        $response = array('status' => FALSE, 'message' => 'Please check data and try again.', 'data' => ['failed_data' => $failedData]);
                    }
                } else {
                    $response = array('status' => FALSE, 'message' => 'Max row limit 25000, please check data.');
                }

            } else {
                $response = array('status' => FALSE, 'message' => 'Invalid data file, please upload valid file.');
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function validateLeadData($leadData)
    {
        $response = array();

        //Check Duplicate entry
        if(isset($leadData['email_address']) && !empty($leadData['email_address'])) {
            $resultData = Data::whereEmailAddress($leadData['email_address'])->first();
            if(!empty($resultData)) {
                $response['email_address'] = 'Email Id already exists';
            }
        } else {
            $response['email_address'] = 'Email Id already exists';
        }

        return $response;
    }
}
