<?php

namespace App\Repository\AgentLeadRepository;

use App\Exports\ImportLeadsFailedDataExport;
use App\Models\AgentLead;
use App\Models\CampaignAssignAgent;
use App\Models\SuppressionAccountName;
use App\Models\SuppressionDomain;
use App\Models\SuppressionEmail;
use App\Models\TargetDomain;
use App\Models\TransactionTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class AgentLeadRepository implements AgentLeadInterface
{
    private $agentLead;

    public function __construct(
        AgentLead $agentLead
    )
    {
        $this->agentLead = $agentLead;
    }

    public function get($filters = array())
    {
        $query = AgentLead::query();

        if (isset($filters['ca_agent_id']) && $filters['ca_agent_id']) {
            $query->where('ca_agent_id', $filters['ca_agent_id']);
        }

        return $query->get();
    }

    public function find($id, $with = array())
    {
        $query = AgentLead::query();

        return $query->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $agentLead = new AgentLead();
            $resultCAAgent = CampaignAssignAgent::findOrFail(base64_decode($attributes['ca_agent_id']));
            $work_type = $resultCAAgent->agent_work_type->slug;
            $resultTransactionTime = TransactionTime::where('status', 1)->orderBy('created_at', 'DESC')->first();

            $agentLead->ca_agent_id = $resultCAAgent->id;
            $agentLead->campaign_id = $resultCAAgent->campaign_id;
            $agentLead->agent_id = Auth::id();
            $agentLead->transaction_time = $resultTransactionTime->$work_type;
            $agentLead->first_name = $attributes['first_name'];
            $agentLead->last_name = $attributes['last_name'];
            $agentLead->company_name = $attributes['company_name'];
            $agentLead->email_address = $attributes['email_address'];
            $agentLead->specific_title = $attributes['specific_title'];
            if(isset($attributes['job_level']) && !empty(trim($attributes['job_level']))) {
                $agentLead->job_level = $attributes['job_level'];
            }
            if(isset($attributes['job_role']) && !empty(trim($attributes['job_role']))) {
                $agentLead->job_role = $attributes['job_role'];
            }
            $agentLead->phone_number = $attributes['phone_number'];
            $agentLead->address_1 = $attributes['address_1'];
            if(isset($attributes['address_2']) && !empty(trim($attributes['address_2']))) {
                $agentLead->address_2 = $attributes['address_2'];
            }
            $agentLead->city = $attributes['city'];
            $agentLead->state = $attributes['state'];
            $agentLead->zipcode = $attributes['zipcode'];
            $agentLead->country = $attributes['country'];
            $agentLead->industry = $attributes['industry'];
            $agentLead->employee_size = $attributes['employee_size'];
            if(isset($attributes['employee_size_2']) && !empty(trim($attributes['employee_size_2']))) {
                $agentLead->employee_size_2 = $attributes['employee_size_2'];
            }
            $agentLead->revenue = $attributes['revenue'];
            $agentLead->company_domain = $attributes['company_domain'];
            if(isset($attributes['website']) && !empty(trim($attributes['website']))) {
                $agentLead->website = $attributes['website'];
            }
            $agentLead->company_linkedin_url = $attributes['company_linkedin_url'];
            $agentLead->linkedin_profile_link = $attributes['linkedin_profile_link'];

            if(isset($attributes['linkedin_profile_sn_link']) && !empty(trim($attributes['linkedin_profile_sn_link']))) {
                $agentLead->linkedin_profile_sn_link = $attributes['linkedin_profile_sn_link'];
            }

            if(isset($attributes['comment']) && !empty(trim($attributes['comment']))) {
                $agentLead->comment = $attributes['comment'];
            }

            if(isset($attributes['comment_2']) && !empty(trim($attributes['comment_2']))) {
                $agentLead->comment_2 = $attributes['comment_2'];
            }

            if(isset($attributes['qc_comment']) && !empty(trim($attributes['qc_comment']))) {
                $agentLead->qc_comment = $attributes['qc_comment'];
            }

            $agentLead->save();
            if($agentLead->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Lead added successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => $exception->getMessage());
        }
        return $response;
    }

    public function update($id, $attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $agentLead = AgentLead::findOrFail($id);

            if(isset($attributes['ca_agent_id']) && !empty($attributes['ca_agent_id'])) {
                $agentLead->ca_agent_id = $attributes['ca_agent_id'];
            }

            if(isset($attributes['campaign_id']) && !empty($attributes['campaign_id'])) {
                $agentLead->campaign_id = $attributes['campaign_id'];
            }

            if(isset($attributes['agent_id']) && !empty($attributes['agent_id'])) {
                $agentLead->agent_id = $attributes['agent_id'];
            }

            if(isset($attributes['first_name']) && !empty($attributes['first_name'])) {
                $agentLead->first_name = $attributes['first_name'];
            }

            if(isset($attributes['last_name']) && !empty($attributes['last_name'])) {
                $agentLead->last_name = $attributes['last_name'];
            }

            if(isset($attributes['company_name']) && !empty($attributes['company_name'])) {
                $agentLead->company_name = $attributes['company_name'];
            }

            if(isset($attributes['email_address']) && !empty($attributes['email_address'])) {
                $agentLead->email_address = $attributes['email_address'];
            }

            if(isset($attributes['specific_title']) && !empty($attributes['specific_title'])) {
                $agentLead->specific_title = $attributes['specific_title'];
            }

            if(array_key_exists('job_level', $attributes)) {
                $agentLead->job_level = $attributes['job_level'];
            }

            if(array_key_exists('job_role', $attributes)) {
                $agentLead->job_role = $attributes['job_role'];
            }

            if(isset($attributes['phone_number']) && !empty($attributes['phone_number'])) {
                $agentLead->phone_number = $attributes['phone_number'];
            }

            if(isset($attributes['address_1']) && !empty($attributes['address_1'])) {
                $agentLead->address_1 = $attributes['address_1'];
            }

            if(array_key_exists('address_2', $attributes)) {
                $agentLead->address_2 = $attributes['address_2'];
            }

            if(isset($attributes['city']) && !empty($attributes['city'])) {
                $agentLead->city = $attributes['city'];
            }

            if(isset($attributes['state']) && !empty($attributes['state'])) {
                $agentLead->state = $attributes['state'];
            }

            if(isset($attributes['zipcode']) && !empty($attributes['zipcode'])) {
                $agentLead->zipcode = $attributes['zipcode'];
            }

            if(isset($attributes['country']) && !empty($attributes['country'])) {
                $agentLead->country = $attributes['country'];
            }

            if(isset($attributes['industry']) && !empty($attributes['industry'])) {
                $agentLead->industry = $attributes['industry'];
            }

            if(isset($attributes['employee_size']) && !empty($attributes['employee_size'])) {
                $agentLead->employee_size = $attributes['employee_size'];
            }

            if(array_key_exists('employee_size_2', $attributes)) {
                $agentLead->employee_size_2 = $attributes['employee_size_2'];
            }

            if(isset($attributes['revenue']) && !empty($attributes['revenue'])) {
                $agentLead->revenue = $attributes['revenue'];
            }

            if(isset($attributes['company_domain']) && !empty($attributes['company_domain'])) {
                $agentLead->company_domain = $attributes['company_domain'];
            }

            if(array_key_exists('website', $attributes)) {
                $agentLead->website = $attributes['website'];
            }

            if(isset($attributes['company_linkedin_url']) && !empty($attributes['company_linkedin_url'])) {
                $agentLead->company_linkedin_url = $attributes['company_linkedin_url'];
            }

            if(isset($attributes['linkedin_profile_link']) && !empty($attributes['linkedin_profile_link'])) {
                $agentLead->linkedin_profile_link = $attributes['linkedin_profile_link'];
            }

            if(array_key_exists('linkedin_profile_sn_link', $attributes)) {
                $agentLead->linkedin_profile_sn_link = $attributes['linkedin_profile_sn_link'];
            }

            if(array_key_exists('comment', $attributes)) {
                $agentLead->comment = $attributes['comment'];
            }

            if(array_key_exists('comment_2', $attributes)) {
                $agentLead->comment_2 = $attributes['comment_2'];
            }

            if(array_key_exists('qc_comment', $attributes)) {
                $agentLead->qc_comment = $attributes['qc_comment'];
            }

            if(array_key_exists('status', $attributes)) {
                $agentLead->status = $attributes['status'];
            }

            if($agentLead->save()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Lead details updated successfully', 'details' => $agentLead);
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

    public function import($ca_agent_id, $file)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', '0');

            $excelData = Excel::toArray('', $file);
            $inputExcelFormat = implode(',', $excelData[0][0]);
            $excelFormat = "first_name,last_name,company_name,email_address,specific_title,job_level,job_role,phone_number,address_1,address_2,city,state,zipcode,country,industry,employee_size,employee_size_2,revenue,company_domain,website,company_linkedin_url,linkedin_profile_link,linkedin_profile_sn_link,comment";

            if($inputExcelFormat == $excelFormat) {
                array_shift($excelData[0]);
                $totalDataCount = count($excelData[0]);

                if($totalDataCount < 1000) {
                    $resultCAAgent = CampaignAssignAgent::find($ca_agent_id);
                    $work_type = $resultCAAgent->agent_work_type->slug;
                    $resultTransactionTime = TransactionTime::where('status', 1)->orderBy('created_at', 'DESC')->first();
                    $transaction_time = $resultTransactionTime->$work_type;

                    $failedLeads = array();
                    $validLeads = array();
                    $invalidData = array();
                    $successCount = 0;
                    $error_file_path = 'false';

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
                            'employee_size_2' => trim($row[16]),
                            'revenue' => trim($row[17]),
                            'company_domain' => trim($row[18]),
                            'website' => trim($row[19]),
                            'company_linkedin_url' => trim($row[20]),
                            'linkedin_profile_link' => trim($row[21]),
                            'linkedin_profile_sn_link' => trim($row[22]),
                            'comment' => trim($row[23])
                        );

                        $resultValidate = $this->validateLeadData($leadData, $resultCAAgent->campaign_id);

                        if($resultValidate['status'] == TRUE) {
                            $validLead = $resultValidate['validatedLead'];
                            $validLead['status'] = 1;
                            $validLead['ca_agent_id'] = base64_encode($resultCAAgent->id);
                            $validLead['campaign_id'] = $resultCAAgent->campaign_id;
                            $validLead['agent_id'] = $resultCAAgent->user_id;
                            $validLead['transaction_time'] = $transaction_time;
                            $validLeads[] = $validLead;

                            //store
                            DB::beginTransaction();
                            $responseInsert = $this->store($validLead);

                            if($responseInsert['status'] == TRUE) {
                                $successCount++;
                                DB::commit();
                            } else {
                                DB::rollBack();
                                $failedLeads[] = $validLead;
                                $invalidData[] = array(
                                    'data' => $validLead,
                                    'invalidCells' => $resultValidate['invalidCells'][0] = 'Invalid',
                                    'errorMessage' => $responseInsert['message']
                                );
                            }

                        } else {
                            $failedLeads[] = $leadData;
                            $invalidData[] = array(
                                'data' => $leadData,
                                'invalidCells' => $resultValidate['invalidCells'],
                                'errorMessage' => implode(',', $resultValidate['errorMessage'])
                            );
                        }
                    }

                    if(count($invalidData)) {
                        $error_file_path = $this->generateExcelFailedLeads($invalidData);
                    }

                    if($successCount) {
                        if(count($invalidData)) {
                            $response = array('status' => TRUE, 'message' => 'Leads imported successfully', 'data' => ['success_count' => $successCount, 'failed_count' => count($failedLeads), 'failed_data_file' => $error_file_path]);
                        } else {
                            $response = array('status' => TRUE, 'message' => 'Leads imported successfully', 'data' => ['success_count' => $successCount]);
                        }
                    } else {
                        $response = array('status' => FALSE, 'message' => 'Please check leads and try again.', 'data' => ['failed_data_file' => $error_file_path]);
                    }

                } else {
                    throw new \Exception('Max row limit 1000, please check lead data.', 1);
                }
            } else {
                throw new \Exception('Invalid lead file, please upload valid file.', 1);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => $exception->getMessage());
        }
        return $response;
    }

    public function generateExcelFailedLeads($invalidData)
    {
        $path = 'public/failed_data/agent/';
        $path_to_download = '/public/storage/failed_data/agent/';

        $filename = 'Import-Leads-Failed-Data-'.time().'.xlsx';

        if(Excel::store(new ImportLeadsFailedDataExport($invalidData), $path.$filename)) {
            return $path_to_download.$filename;
        } else {
            return 'false';
        }
    }

    public function validateLeadData($leadData, $campaign_id)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');

        $validatedLead = array();
        $errorMessage = array();
        $invalidCells = array();

        try {
            //dd($leadData, $campaign_id);

            //Validate $leadData['first_name']
            if(!empty(trim($leadData['first_name']))) {
                $validatedLead['first_name'] = trim($leadData['first_name']);
            } else {
                $errorMessage['first_name'] = 'Enter first_name';
                $invalidCells[0] = 'Invalid';
            }

            //Validate $leadData['last_name']
            if(!empty(trim($leadData['last_name']))) {
                $validatedLead['last_name'] = trim($leadData['last_name']);
            } else {
                $errorMessage['last_name'] = 'Enter last_name';
                $invalidCells[1] = 'Invalid';
            }

            //Validate $leadData['company_name']
            if(!empty(trim($leadData['company_name']))) {
                $resultSuppressionAccountName = SuppressionAccountName::whereCampaignId($campaign_id)->whereAccountName(trim($leadData['company_name']))->exists();
                if(!$resultSuppressionAccountName) {
                    $validatedLead['company_name'] = trim($leadData['company_name']);
                } else {
                    $errorMessage['company_name'] = 'Account Name Suppression';
                    $invalidCells[2] = 'Invalid';
                }
            } else {
                $errorMessage['company_name'] = 'Enter company_name';
                $invalidCells[2] = 'Invalid';
            }

            //Validate $leadData['email_address']
            if(!empty(trim($leadData['email_address']))) {
                if(filter_var(trim($leadData['email_address']), FILTER_VALIDATE_EMAIL)) {
                    $leadExists = AgentLead::where('campaign_id', $campaign_id)->where('email_address', trim($leadData['email_address']))->count();
                    if(!$leadExists) {
                        $resultSuppressionEmail = SuppressionEmail::whereCampaignId($campaign_id)->whereEmail(trim($leadData['email_address']))->exists();
                        if(!$resultSuppressionEmail) {
                            $validatedLead['email_address'] = trim($leadData['email_address']);
                        } else {
                            $errorMessage['email_address'] = 'Email Suppression';
                            $invalidCells[3] = 'Invalid';
                        }
                    } else {
                        $errorMessage['email_address'] = 'Email address already exists';
                        $invalidCells[3] = 'Invalid';
                    }
                } else {
                    $errorMessage['email_address'] = 'Email valid email address';
                    $invalidCells[3] = 'Invalid';
                }

            } else {
                $errorMessage['email_address'] = 'Enter email address';
                $invalidCells[3] = 'Invalid';
            }

            //Validate $leadData['specific_title']
            if(!empty(trim($leadData['specific_title']))) {
                $validatedLead['specific_title'] = trim($leadData['specific_title']);
            } else {
                $errorMessage['specific_title'] = 'Enter specific_title';
                $invalidCells[4] = 'Invalid';
            }

            //Validate $leadData['job_level'] $invalidCells[5] = 'Invalid';
            $validatedLead['job_level'] = trim($leadData['job_level']);

            //Validate $leadData['job_role'] $invalidCells[6] = 'Invalid';
            $validatedLead['job_role'] = trim($leadData['job_role']);

            //Validate $leadData['phone_number']
            if(!empty(trim($leadData['phone_number']))) {
                $validatedLead['phone_number'] = trim($leadData['phone_number']);
            } else {
                $errorMessage['phone_number'] = 'Enter phone_number';
                $invalidCells[7] = 'Invalid';
            }

            //Validate $leadData['address_1']
            if(!empty(trim($leadData['address_1']))) {
                $validatedLead['address_1'] = trim($leadData['address_1']);
            } else {
                $errorMessage['address_1'] = 'Enter address_1';
                $invalidCells[8] = 'Invalid';
            }

            //Validate $leadData['address_2'] $invalidCells[9] = 'Invalid';
            $validatedLead['address_2'] = trim($leadData['address_2']);

            //Validate $leadData['city']
            if(!empty(trim($leadData['city']))) {
                $validatedLead['city'] = trim($leadData['city']);
            } else {
                $errorMessage['city'] = 'Enter city';
                $invalidCells[10] = 'Invalid';
            }

            //Validate $leadData['state']
            if(!empty(trim($leadData['state']))) {
                $validatedLead['state'] = trim($leadData['state']);
            } else {
                $errorMessage['state'] = 'Enter state';
                $invalidCells[11] = 'Invalid';
            }

            //Validate $leadData['zipcode']
            if(!empty(trim($leadData['zipcode']))) {
                $validatedLead['zipcode'] = trim($leadData['zipcode']);
            } else {
                $errorMessage['zipcode'] = 'Enter zipcode';
                $invalidCells[12] = 'Invalid';
            }

            //Validate $leadData['country']
            if(!empty(trim($leadData['country']))) {
                $validatedLead['country'] = trim($leadData['country']);
            } else {
                $errorMessage['country'] = 'Enter country';
                $invalidCells[13] = 'Invalid';
            }

            //Validate $leadData['industry']
            if(!empty(trim($leadData['industry']))) {
                $validatedLead['industry'] = trim($leadData['industry']);
            } else {
                $errorMessage['industry'] = 'Enter industry';
                $invalidCells[14] = 'Invalid';
            }

            //Validate $leadData['employee_size']
            if(!empty(trim($leadData['employee_size']))) {
                $validatedLead['employee_size'] = trim($leadData['employee_size']);
            } else {
                $errorMessage['employee_size'] = 'Enter employee_size';
                $invalidCells[15] = 'Invalid';
            }

            //Validate $leadData['employee_size_2'] $invalidCells[16] = 'Invalid';
            $validatedLead['employee_size_2'] = trim($leadData['employee_size_2']);

            //Validate $leadData['revenue']
            if(!empty(trim($leadData['revenue']))) {
                $validatedLead['revenue'] = trim($leadData['revenue']);
            } else {
                $errorMessage['revenue'] = 'Enter revenue';
                $invalidCells[17] = 'Invalid';
            }

            //Validate $leadData['company_domain']
            if(!empty(trim($leadData['company_domain']))) {
                $resultSuppressionDomain = SuppressionDomain::whereCampaignId($campaign_id)->whereDomain(trim($leadData['company_domain']))->exists();
                if(!$resultSuppressionDomain) {
                    if(TargetDomain::whereCampaignId($campaign_id)->exists()) {
                        $resultTargetDomain = TargetDomain::whereCampaignId($campaign_id)->whereDomain(trim($leadData['company_domain']))->exists();
                        if($resultTargetDomain) {
                            $validatedLead['company_domain'] = trim($leadData['company_domain']);
                        } else {
                            $errorMessage['company_domain'] = 'Target Domain Mismatch';
                            $invalidCells[18] = 'Invalid';
                        }
                    } else {
                        $validatedLead['company_domain'] = trim($leadData['company_domain']);
                    }
                } else {
                    $errorMessage['company_domain'] = 'Domain Suppression';
                    $invalidCells[18] = 'Invalid';
                }
            } else {
                $errorMessage['company_domain'] = 'Enter company_domain';
                $invalidCells[18] = 'Invalid';
            }

            //Validate $leadData['website'] $invalidCells[19] = 'Invalid';
            $validatedLead['website'] = trim($leadData['website']);

            //Validate $leadData['company_linkedin_url'] $invalidCells[20] = 'Invalid';
            $validatedLead['company_linkedin_url'] = trim($leadData['company_linkedin_url']);

            //Validate $leadData['linkedin_profile_link']
            if(!empty(trim($leadData['linkedin_profile_link']))) {
                $validatedLead['linkedin_profile_link'] = trim($leadData['linkedin_profile_link']);
            } else {
                $errorMessage['linkedin_profile_link'] = 'Enter linkedin_profile_link';
                $invalidCells[21] = 'Invalid';
            }

            //Validate $leadData['linkedin_profile_sn_link'] $invalidCells[22] = 'Invalid';
            $validatedLead['linkedin_profile_sn_link'] = trim($leadData['linkedin_profile_sn_link']);

            //Validate $leadData['comment'] $invalidCells[23] = 'Invalid';
            $validatedLead['comment'] = trim($leadData['comment']);

            if(empty($errorMessage)) {
                //dd($validatedLead);
                $response = array('status' => TRUE, 'validatedLead' => $validatedLead);
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }

        } catch (\Exception $exception) {
            //dd($errorMessage, $invalidCells);
            $response = array(
                'status' => FALSE,
                'errorMessage' => $errorMessage,
                'invalidCells' => $invalidCells
            );
        }

        return $response;
    }
}
