<?php

namespace App\Repository\CampaignRepository;

use App\Exports\ArrayToExcel;
use App\Models\Campaign;
use App\Models\CampaignAssignAgent;
use App\Models\CampaignAssignRATL;
use App\Models\CampaignCountry;
use App\Models\CampaignDeliveryDetail;
use App\Models\CampaignFile;
use App\Models\CampaignFilter;
use App\Models\CampaignSpecification;
use App\Models\CampaignStatus;
use App\Models\CampaignType;
use App\Models\Country;
use App\Models\ManagerNotification;
use App\Models\PacingDetail;
use App\Models\User;
use App\Repository\CampaignFile\CampaignFileRepository;
use App\Repository\CampaignTypeRepository\CampaignTypeRepository;
use App\Models\SiteSetting;

use App\Repository\Suppression\AccountName\AccountNameRepository as SuppressionAccountNameRepository;
use App\Repository\Suppression\Domain\DomainRepository as SuppressionDomainRepository;
use App\Repository\Suppression\Email\EmailRepository as SuppressionEmailRepository;

use App\Repository\Target\AccountName\AccountNameRepository as TargetAccountNameRepository;
use App\Repository\Target\Domain\DomainRepository as TargetDomainRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;
use Illuminate\Support\Facades\File;
use Zip;

class CampaignRepository implements CampaignInterface
{
    private $campaign;
    private $campaignTypeRepository;
    private $campaignFileRepository;
    private $suppressionEmailRepository;
    private $suppressionDomainRepository;
    private $suppressionAccountNameRepository;
    private $targetDomainRepository;
    private $targetAccountNameRepository;


    public function __construct(
        Campaign $campaign,
        CampaignTypeRepository $campaignTypeRepository,
        CampaignFileRepository $campaignFileRepository,
        SuppressionEmailRepository $suppressionEmailRepository,
        SuppressionDomainRepository $suppressionDomainRepository,
        SuppressionAccountNameRepository $suppressionAccountNameRepository,
        TargetDomainRepository $targetDomainRepository,
        TargetAccountNameRepository $targetAccountNameRepository
    )
    {
        $this->campaign = $campaign;
        $this->campaignTypeRepository = $campaignTypeRepository;
        $this->campaignFileRepository = $campaignFileRepository;
        $this->suppressionEmailRepository = $suppressionEmailRepository;
        $this->suppressionDomainRepository = $suppressionDomainRepository;
        $this->suppressionAccountNameRepository = $suppressionAccountNameRepository;
        $this->targetDomainRepository = $targetDomainRepository;
        $this->targetAccountNameRepository = $targetAccountNameRepository;
    }

    public function get($filters = array())
    {
        $query = Campaign::query();

        if(isset($filters['campaign_to_assign']) && $filters['campaign_to_assign']) {
            $query->whereNull('parent_id');
        }

        return $query->get();
    }

    public function find($id, $with = array())
    {

        $query = Campaign::query();

        if(in_array('delivery_detail', $with)) {
            $query->with('delivery_detail');
        }

        //$query->with('children');

        return $query->findOrFail($id);
    }

    public function store($attributes)
    {
        //dd($attributes);
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $resultCampaign = Campaign::where('name', $attributes['name'])->first();

            $campaign = new Campaign();

            if(isset($attributes['parent_id']) && base64_decode($attributes['parent_id'])) {
                $resultCampaign = $this->find(base64_decode($attributes['parent_id']));
                $campaignId = $resultCampaign->campaign_id;
                $campaign->type  = 'incremental';
                $campaign->parent_id  = base64_decode($attributes['parent_id']);
            } else {
                $lastRecord = Campaign::latest('id')->first();
                if(isset($lastRecord)) {
                    $lastId = str_pad($lastRecord->id + 1,6,"0",STR_PAD_LEFT);
                } else {
                    $lastId = str_pad('1',6,"0",STR_PAD_LEFT);
                }
                $resultCampaignType = $this->campaignTypeRepository->find($attributes['campaign_type_id']);
                $campaignAbbreviation = SiteSetting::where('key', 'Campaign Abbreviation')->first()->value;
                $campaignId = $campaignAbbreviation.$resultCampaignType->name.'-'.$lastId;
            }

            if(!isset($attributes['parent_id']) && !empty($resultCampaign->id)) {
                throw new \Exception('Campaign already exists.');
            }

            $campaign->name = $attributes['name'];
            $campaign->campaign_id = $campaignId;
            $campaign->v_mail_campaign_id = $attributes['v_mail_campaign_id'];
            $campaign->campaign_filter_id  = $attributes['campaign_filter_id'];
            $campaign->campaign_type_id  = $attributes['campaign_type_id'];

            if(isset($attributes['note']) && !empty($attributes['note'])) {
                $campaign->note = $attributes['note'];
            }

            $campaign->start_date = date('Y-m-d', strtotime($attributes['start_date']));
            $campaign->end_date = date('Y-m-d', strtotime($attributes['end_date']));

            if(isset($attributes['deliver_count']) && $attributes['deliver_count'] > 0) {
                $campaign->deliver_count = $attributes['deliver_count'];
            }
            if(isset($attributes['shortfall_count']) && $attributes['shortfall_count'] > 0) {
                $campaign->shortfall_count = $attributes['shortfall_count'];
            }

            $campaign->allocation = $attributes['allocation'];
            $campaign->campaign_status_id  = $attributes['campaign_status_id'];
            $campaign->pacing  = $attributes['pacing'];

            $campaign->save();
            if($campaign->id) {

                //Save Campaign's Countries
                if(!empty($attributes['country_id']) && count($attributes['country_id'])) {
                    $insertCampaignCountries = array();
                    foreach ($attributes['country_id'] as $country) {
                        array_push($insertCampaignCountries, ['campaign_id' => $campaign->id, 'country_id' => $country]);
                    }
                    CampaignCountry::insert($insertCampaignCountries);
                }

                //Save Campaign's Specifications
                $insertCampaignSpecifications = array();
                if(isset($attributes['specifications']) && !empty($attributes['specifications'])) {
                    foreach ($attributes['specifications'] as $file) {
                        $extension = $file->getClientOriginalExtension();
                        //$filename  = $campaign->campaign_id.'-' . str_shuffle(time()) . '.' . $extension;
                        $filename  = $file->getClientOriginalName();
                        $path = 'public/campaigns/'.$campaign->campaign_id;
                        $result  = $file->storeAs($path, $filename);
                        $insertCampaignSpecifications[] = [
                            'campaign_id' => $campaign->id,
                            'file_name' => $filename,
                            'extension' => $extension
                        ];
                    }
                    CampaignSpecification::insert($insertCampaignSpecifications);
                }

                //Save Pacing Details/Sub-Allocation
                if(isset($attributes['sub-allocation']) && !empty($attributes['sub-allocation'])) {
                    //Pacing Details
                    $insertPacingDetails = array();
                    foreach ($attributes['sub-allocation'] as $date => $sub_allocation) {
                        $insertPacingDetails[] = [
                            'campaign_id' => $campaign->id,
                            'date' => $date,
                            'sub_allocation' => $sub_allocation,
                            'day' => date('w', strtotime($date))
                        ];
                    }
                    PacingDetail::insert($insertPacingDetails);
                    //--Pacing Details
                }

                //Save Suppression Email
                if(isset($attributes['suppression_email']) && !empty($attributes['suppression_email'])) {
                    $file = $attributes['suppression_email'];
                    $responseSuppressionEmail = $this->suppressionEmailRepository->bulkUpload($campaign->id, $file);
                    if($responseSuppressionEmail['status'] == FALSE) {
                        $responseFlag  = 0;
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                    $path = 'public/campaigns/'.$campaign->campaign_id;
                    $result  = $file->storeAs($path, $file->getClientOriginalName());
                    $attributesCampaignFile = array(
                        'campaign_id' => $campaign->id,
                        'file_type' => 'suppression_email',
                        'file_name' => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                    );
                    $responseCampaignFile = $this->campaignFileRepository->store($attributesCampaignFile);
                    if($responseCampaignFile['status'] == FALSE) {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                }
                //Save Suppression Domain
                if(isset($attributes['suppression_domain']) && !empty($attributes['suppression_domain'])) {
                    $file = $attributes['suppression_domain'];
                    $responseSuppressionDomain = $this->suppressionDomainRepository->bulkUpload($campaign->id, $file);
                    if($responseSuppressionDomain['status'] == FALSE) {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                    $path = 'public/campaigns/'.$campaign->campaign_id;
                    $result  = $file->storeAs($path, $file->getClientOriginalName());
                    $attributesCampaignFile = array(
                        'campaign_id' => $campaign->id,
                        'file_type' => 'suppression_domain',
                        'file_name' => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                    );
                    $responseCampaignFile = $this->campaignFileRepository->store($attributesCampaignFile);
                    if($responseCampaignFile['status'] == FALSE) {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                }
                //Save Suppression Account Name
                if(isset($attributes['suppression_account_name']) && !empty($attributes['suppression_account_name'])) {
                    $file = $attributes['suppression_account_name'];
                    $responseSuppressionAccountName = $this->suppressionAccountNameRepository->bulkUpload($campaign->id, $file);
                    if($responseSuppressionAccountName['status'] == FALSE) {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                    $path = 'public/campaigns/'.$campaign->campaign_id;
                    $result  = $file->storeAs($path, $file->getClientOriginalName());
                    $attributesCampaignFile = array(
                        'campaign_id' => $campaign->id,
                        'file_type' => 'suppression_account_name',
                        'file_name' => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                    );
                    $responseCampaignFile = $this->campaignFileRepository->store($attributesCampaignFile);
                    if($responseCampaignFile['status'] == FALSE) {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                }
                //Save Target Domain
                if(isset($attributes['target_domain']) && !empty($attributes['target_domain'])) {
                    $file = $attributes['target_domain'];
                    $responseTargetDomain = $this->targetDomainRepository->bulkUpload($campaign->id, $file);
                    if($responseTargetDomain['status'] == FALSE) {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                    $path = 'public/campaigns/'.$campaign->campaign_id;
                    $result  = $file->storeAs($path, $file->getClientOriginalName());
                    $attributesCampaignFile = array(
                        'campaign_id' => $campaign->id,
                        'file_type' => 'target_domain',
                        'file_name' => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                    );
                    $responseCampaignFile = $this->campaignFileRepository->store($attributesCampaignFile);
                    if($responseCampaignFile['status'] == FALSE) {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                }
                //Save Target Account Name
                if(isset($attributes['target_account_name']) && !empty($attributes['target_account_name'])) {
                    $file = $attributes['target_account_name'];
                    $responseTargetAccountName = $this->targetAccountNameRepository->bulkUpload($campaign->id, $file);
                    if($responseTargetAccountName['status'] == FALSE) {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                    $path = 'public/campaigns/'.$campaign->campaign_id;
                    $result  = $file->storeAs($path, $file->getClientOriginalName());
                    $attributesCampaignFile = array(
                        'campaign_id' => $campaign->id,
                        'file_type' => 'target_account_name',
                        'file_name' => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                    );
                    $responseCampaignFile = $this->campaignFileRepository->store($attributesCampaignFile);
                    if($responseCampaignFile['status'] == FALSE) {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                }

                //Send Notification
                $dataNotification = array();
                $resultManagers = User::whereHas('role', function ($role){ $role->where('slug', 'manager')->where('status', 1); })->where('status', 1)->where('id','!=', Auth::id())->get();
                if(!empty($resultManagers) && $resultManagers->count()) {
                    foreach ($resultManagers as $manager) {
                        $dataNotification[] = array(
                            'sender_id' => Auth::id(),
                            'recipient_id' => $manager->id,
                            'message' => 'New campaign added - '.$campaign->name,
                            'url' => secured_url(route('manager.campaign.show', base64_encode($campaign->id)))
                        );
                    }
                    $responseNotification = ManagerNotification::insert($dataNotification);
                }

                //Add Delivery Detail entry
                if(isset($attributes['parent_id'])) {
                    CampaignDeliveryDetail::where('campaign_id', $campaign->parent_id)->update(array('campaign_progress' => 'Campaign IN - INCR', 'updated_by' => Auth::id()));
                } else {
                    CampaignDeliveryDetail::insert(array('campaign_id' => $campaign->id, 'updated_by' => Auth::id()));
                }
                DB::commit();

                //Add Campaign History
                add_campaign_history($campaign->id, $campaign->parent_id, 'Campaign added - '.$campaign->name);
                add_history('Campaign added', 'Campaign added - '.$campaign->name);

                $response = array('status' => TRUE, 'message' => 'Campaign added successfully', 'campaign_id' => $campaign->campaign_id, 'id' => $campaign->id);
            } else {
                throw new \Exception('Please check data and try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => $exception->getMessage());
        }
        return $response;
    }

    public function update($id, $attributes): array
    {
        //dd($attributes);
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();

            if(isset($attributes['id']) && !empty($attributes['id'])) {
                $id = base64_decode($attributes['id']);
            }

            $campaign = $this->find($id);
            $campaign_copy = $campaign->toArray();

            if(isset($attributes['name']) && !empty($attributes['name'])) {
                $campaign->name = $attributes['name'];
            }

            if(array_key_exists('v_mail_campaign_id', $attributes)) {
                $campaign->v_mail_campaign_id = $attributes['v_mail_campaign_id'];
            }

            if(isset($attributes['campaign_filter_id']) && !empty($attributes['campaign_filter_id'])) {
                $campaign->campaign_filter_id = $attributes['campaign_filter_id'];
            }

            if(isset($attributes['campaign_type_id']) && !empty($attributes['campaign_type_id'])) {
                $campaign->campaign_type_id = $attributes['campaign_type_id'];
            }

            if(isset($attributes['note'])) {
                $campaign->note = $attributes['note'];
            }

            if(isset($attributes['start_date']) && !empty($attributes['start_date'])) {
                $campaign->start_date = date('Y-m-d', strtotime($attributes['start_date']));
            }

            if(isset($attributes['end_date']) && !empty($attributes['end_date'])) {
                $campaign->end_date = date('Y-m-d', strtotime($attributes['end_date']));
            }

            if(isset($attributes['deliver_count'])) {
                $campaign->deliver_count = $attributes['deliver_count'];
            }

            if(isset($attributes['campaign_status_id']) && $attributes['campaign_status_id'] == 6 && isset($attributes['shortfall_count'])) {
                $campaign->shortfall_count = $attributes['shortfall_count'];
            } else {
                $campaign->shortfall_count = 0;
            }

            if(isset($attributes['allocation'])) {
                $campaign->allocation = $attributes['allocation'];
            }

            if(isset($attributes['campaign_status_id'])) {
                $campaign->campaign_status_id = $attributes['campaign_status_id'];
            }

            if(isset($attributes['pacing'])) {
                $campaign->pacing = $attributes['pacing'];
            }

            $campaign->save();

            if($campaign->id) {
                $campaign_updated = $campaign->getChanges();

                unset($campaign_updated['updated_at']);

                //if end date updated the update CARATL and CAAgent display date
                if(array_key_exists('end_date', $campaign_updated)) {
                    $resultQuery1 = CampaignAssignRATL::where('campaign_id', $campaign->id)->update(array('display_date' => date('Y-m-d', strtotime('-2 day' , strtotime($campaign->end_date)))));
                    $resultQuery2 = CampaignAssignAgent::where('campaign_id', $campaign->id)->update(array('display_date' => date('Y-m-d', strtotime('-2 day' , strtotime($campaign->end_date)))));
                }

                //Save Campaign's Countries
                $resultCampaignCountries = CampaignCountry::whereCampaignId($campaign->id)->get();
                if(isset($attributes['country_id'])) {

                    CampaignCountry::whereCampaignId($campaign->id)->delete();
                    $insertCampaignCountries = array();
                    $countryNames = array();
                    foreach ($attributes['country_id'] as $country) {
                        $resultCountry = Country::find($country);
                        $countryNames[] = $resultCountry->name;
                        array_push($insertCampaignCountries, ['campaign_id' => $campaign->id, 'country_id' => $country]);
                    }
                    if(!empty($insertCampaignCountries)) {
                        CampaignCountry::insert($insertCampaignCountries);
                        $campaign_updated['country_id'] = implode(',', $countryNames);
                    }
                }

                //if delivered change progress status
                if(array_key_exists('campaign_status_id', $campaign_updated) && $attributes['campaign_status_id'] == 4) {
                    CampaignDeliveryDetail::where('campaign_id', $campaign->id)->update(array('campaign_progress' => 'Delivered', 'updated_by' => Auth::id()));
                }

                DB::commit();

                //Update Incremental Campaigns if any changes
                if(!empty($campaign_updated)) {
                    $resultIncrements = Campaign::where('parent_id', $campaign->id)->get();
                    //dd($campaign_updated, $insertCampaignCountries);
                    if(!empty($resultIncrements)) {
                        foreach ($resultIncrements as $incrementCampaign) {
                            if(array_key_exists('name', $campaign_updated)) {
                                $incrementCampaign->name = $campaign_updated['name'];
                            }
                            if(array_key_exists('campaign_filter_id', $campaign_updated)) {
                                $incrementCampaign->campaign_filter_id = $campaign_updated['campaign_filter_id'];
                            }
                            if(array_key_exists('campaign_type_id', $campaign_updated)) {
                                $incrementCampaign->campaign_type_id = $campaign_updated['campaign_type_id'];
                            }
                            if(array_key_exists('v_mail_campaign_id', $campaign_updated)) {
                                $incrementCampaign->v_mail_campaign_id = $campaign_updated['v_mail_campaign_id'];
                            }
                            if(array_key_exists('note', $campaign_updated)) {
                                $incrementCampaign->note = $campaign_updated['note'];
                            }
                            if(array_key_exists('country_id', $campaign_updated)) {
                                CampaignCountry::whereCampaignId($incrementCampaign->id)->delete();
                                $insertCampaignCountries = array();
                                foreach ($attributes['country_id'] as $country) {
                                    $resultCountry = Country::find($country);
                                    $countryNames[] = $resultCountry->name;
                                    $insertCampaignCountries[] = ['campaign_id' => $incrementCampaign->id, 'country_id' => $country];
                                }
                                if(!empty($insertCampaignCountries)) {
                                    CampaignCountry::insert($insertCampaignCountries);
                                }
                            }
                            $incrementCampaign->save();
                        }
                    }
                }

                //Add Campaign History
                $oldData = $newData = array();
                if(!empty($campaign_updated)) {

                    foreach ($campaign_updated as $key => $value) {
                        switch ($key) {
                            case 'campaign_type_id':
                                $old = CampaignType::find($campaign_copy[$key]);
                                $new = CampaignType::find($value);

                                $oldData[$key] = $old->name;
                                $newData[$key] = $new->name;
                                break;
                            case 'campaign_filter_id':
                                $old = CampaignFilter::find($campaign_copy[$key]);
                                $new = CampaignFilter::find($value);

                                $oldData[$key] = $old->name;
                                $newData[$key] = $new->name;
                                break;
                            case 'campaign_status_id': break;
                            case 'country_id':
                                if(!empty($resultCampaignCountries) && $resultCampaignCountries->count()) {
                                    $oldCountries = array();
                                    foreach ($resultCampaignCountries as $country) {
                                        $oldCountries[] = $country->country->name;
                                    }
                                    $oldCountries = implode(',', $oldCountries);
                                } else {
                                    $oldCountries = null;
                                }
                                if($oldCountries != $value) {
                                    $oldData['country'] = $oldCountries;
                                    $newData['country'] = $value;
                                }
                                break;
                            default:
                                $oldData[$key] = $campaign_copy[$key];
                                $newData[$key] = $value;
                        }
                    }

                    //Delete Sub-Allocations if start date, end date or pacing updated
                    if(array_key_exists('pacing', $newData) || array_key_exists('start_date', $newData) || array_key_exists('start_date', $newData)) {
                        $res = PacingDetail::where('campaign_id', $campaign->id)->delete();
                    }

                    if(!empty($newData)) {
                        $historyMessage = get_history_message($oldData, $newData);
                        add_campaign_history($campaign->id, $campaign->parent_id, 'Campaign details updated - '.$historyMessage, array('oldData' => $oldData, 'newData' => $newData));
                        add_history('Campaign details updated', 'Campaign updated data are - '.$historyMessage, array('oldData' => $oldData, 'newData' => $newData));
                    }
                }

                $response = array('status' => TRUE, 'message' => 'Campaign details updated successfully');
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

    public function updateSpecification($id, $attributes = [])
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $campaign = $this->find($id);
            //Campaign Specifications
            $insertCampaignSpecifications = array();
            $fileNames = [];
            $path = 'public/campaigns/'.$campaign->campaign_id;
            foreach ($attributes['specifications'] as $file) {

                $extension = $file->getClientOriginalExtension();
                //$filename  = $campaign->campaign_id.'-' . str_shuffle(time()) . '.' . $extension;
                $filename  = $file->getClientOriginalName();
                $result  = $file->storeAs($path, $filename);
                array_push($insertCampaignSpecifications, ['campaign_id' => $campaign->id, 'file_name' => $filename, 'extension' => $extension]);
                array_push($fileNames, $filename);
            }
            //dd($insertCampaignSpecifications, $fileNames);
            //--Campaign Specifications

            if(CampaignSpecification::insert($insertCampaignSpecifications)) {
                $lastInserted = CampaignSpecification::whereIn('file_name', $fileNames)->get();
                $response = array('status' => TRUE, 'message' => 'Campaign specification added successfully', 'data' => $lastInserted);
                DB::commit();

                $historyMessage = implode(',', $fileNames);
                add_campaign_history($campaign->id, $campaign->parent_id, 'Campaign specification(s) updated - '.$historyMessage);
                add_history('Campaign specification(s) updated', 'Campaign specification(s) updated - '.$historyMessage);

            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function updateCampaignFile($id, $attributes = array())
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $campaign = $this->find($id);
            $responseFlag = 1;
            $lastInserted = array();
            $historyMessage = array();
            //Save Suppression Email
            if(isset($attributes['suppression_email']) && !empty($attributes['suppression_email'])) {
                $file = $attributes['suppression_email'];
                $responseSuppressionEmail = $this->suppressionEmailRepository->bulkUpload($campaign->id, $file);
                if($responseSuppressionEmail['status'] == FALSE) {
                    $responseFlag  = 0;
                    throw new \Exception('Something went wrong, please try again.', 1);
                }
                $path = 'public/campaigns/'.$campaign->campaign_id;
                $result  = $file->storeAs($path, $file->getClientOriginalName());
                $attributesCampaignFile = array(
                    'campaign_id' => $campaign->id,
                    'file_type' => 'suppression_email',
                    'file_name' => $file->getClientOriginalName(),
                    'extension' => $file->getClientOriginalExtension(),
                );
                $resultCampaignFile = $this->campaignFileRepository->store($attributesCampaignFile);
                if($resultCampaignFile['status'] == FALSE) {
                    $responseFlag  = 0;
                    throw new \Exception('Something went wrong, please try again.', 1);
                } else {
                    $lastInserted['suppression_email'] = CampaignFile::whereCampaignId($attributesCampaignFile['campaign_id'])
                        ->whereFileType($attributesCampaignFile['file_type'])
                        ->whereFileName($attributesCampaignFile['file_name'])
                        ->with('campaign')
                        ->first();
                    $historyMessage[] = 'Email suppression updated';
                }
            }

            //Save Suppression Domain
            if(isset($attributes['suppression_domain']) && !empty($attributes['suppression_domain'])) {
                $file = $attributes['suppression_domain'];
                $responseSuppressionDomain = $this->suppressionDomainRepository->bulkUpload($campaign->id, $file);
                if($responseSuppressionDomain['status'] == FALSE) {
                    $responseFlag  = 0;
                    throw new \Exception('Something went wrong, please try again.', 1);
                }
                $path = 'public/campaigns/'.$campaign->campaign_id;
                $result  = $file->storeAs($path, $file->getClientOriginalName());
                $attributesCampaignFile = array(
                    'campaign_id' => $campaign->id,
                    'file_type' => 'suppression_domain',
                    'file_name' => $file->getClientOriginalName(),
                    'extension' => $file->getClientOriginalExtension(),
                );
                $responseCampaignFile = $this->campaignFileRepository->store($attributesCampaignFile);
                if($responseCampaignFile['status'] == FALSE) {
                    $responseFlag  = 0;
                    throw new \Exception('Something went wrong, please try again.', 1);
                } else {
                    $lastInserted['suppression_domain'] = CampaignFile::whereCampaignId($attributesCampaignFile['campaign_id'])
                        ->whereFileType($attributesCampaignFile['file_type'])
                        ->whereFileName($attributesCampaignFile['file_name'])
                        ->with('campaign')
                        ->first();
                    $historyMessage[] = 'Domain suppression updated';
                }
            }

            //Save Suppression Account Name
            if(isset($attributes['suppression_account_name']) && !empty($attributes['suppression_account_name'])) {
                $file = $attributes['suppression_account_name'];
                $responseSuppressionAccountName = $this->suppressionAccountNameRepository->bulkUpload($campaign->id, $file);
                if($responseSuppressionAccountName['status'] == FALSE) {
                    $responseFlag  = 0;
                    throw new \Exception('Something went wrong, please try again.', 1);
                }
                $path = 'public/campaigns/'.$campaign->campaign_id;
                $result  = $file->storeAs($path, $file->getClientOriginalName());
                $attributesCampaignFile = array(
                    'campaign_id' => $campaign->id,
                    'file_type' => 'suppression_account_name',
                    'file_name' => $file->getClientOriginalName(),
                    'extension' => $file->getClientOriginalExtension(),
                );
                $responseCampaignFile = $this->campaignFileRepository->store($attributesCampaignFile);
                if($responseCampaignFile['status'] == FALSE) {
                    $responseFlag  = 0;
                    throw new \Exception('Something went wrong, please try again.', 1);
                } else {
                    $lastInserted['suppression_account_name'] = CampaignFile::whereCampaignId($attributesCampaignFile['campaign_id'])
                        ->whereFileType($attributesCampaignFile['file_type'])
                        ->whereFileName($attributesCampaignFile['file_name'])
                        ->with('campaign')
                        ->first();
                    $historyMessage[] = 'Account name suppression updated';
                }
            }

            //Save Target Domain
            if(isset($attributes['target_domain']) && !empty($attributes['target_domain'])) {
                $file = $attributes['target_domain'];
                $responseTargetDomain = $this->targetDomainRepository->bulkUpload($campaign->id, $file);
                if($responseTargetDomain['status'] == FALSE) {
                    $responseFlag  = 0;
                    throw new \Exception('Something went wrong, please try again.', 1);
                }
                $path = 'public/campaigns/'.$campaign->campaign_id;
                $result  = $file->storeAs($path, $file->getClientOriginalName());
                $attributesCampaignFile = array(
                    'campaign_id' => $campaign->id,
                    'file_type' => 'target_domain',
                    'file_name' => $file->getClientOriginalName(),
                    'extension' => $file->getClientOriginalExtension(),
                );
                $responseCampaignFile = $this->campaignFileRepository->store($attributesCampaignFile);
                if($responseCampaignFile['status'] == FALSE) {
                    $responseFlag  = 0;
                    throw new \Exception('Something went wrong, please try again.', 1);
                } else {
                    $lastInserted['target_domain'] = CampaignFile::whereCampaignId($attributesCampaignFile['campaign_id'])
                        ->whereFileType($attributesCampaignFile['file_type'])
                        ->whereFileName($attributesCampaignFile['file_name'])
                        ->with('campaign')
                        ->first();

                    $historyMessage[] = 'Target domain updated';
                }
            }

            //Save Target Account Name
            if(isset($attributes['target_account_name']) && !empty($attributes['target_account_name'])) {
                $file = $attributes['target_account_name'];
                $responseTargetAccountName = $this->targetAccountNameRepository->bulkUpload($campaign->id, $file);
                if($responseTargetAccountName['status'] == FALSE) {
                    $responseFlag  = 0;
                    throw new \Exception('Something went wrong, please try again.', 1);
                }
                $path = 'public/campaigns/'.$campaign->campaign_id;
                $result  = $file->storeAs($path, $file->getClientOriginalName());
                $attributesCampaignFile = array(
                    'campaign_id' => $campaign->id,
                    'file_type' => 'target_account_name',
                    'file_name' => $file->getClientOriginalName(),
                    'extension' => $file->getClientOriginalExtension(),
                );
                $responseCampaignFile = $this->campaignFileRepository->store($attributesCampaignFile);
                if($responseCampaignFile['status'] == FALSE) {
                    $responseFlag  = 0;
                    throw new \Exception('Something went wrong, please try again.', 1);
                } else {
                    $lastInserted['target_account_name'] = CampaignFile::whereCampaignId($attributesCampaignFile['campaign_id'])
                        ->whereFileType($attributesCampaignFile['file_type'])
                        ->whereFileName($attributesCampaignFile['file_name'])
                        ->with('campaign')
                        ->first();

                    $historyMessage[] = 'Target account name updated';
                }
            }

            if($responseFlag) {

                $historyMessage = implode(',', $historyMessage);
                add_campaign_history($campaign->id, $campaign->parent_id, 'Campaign details updated - '.$historyMessage);
                add_history('Campaign details updated', 'Campaign updated data are - '.$historyMessage);

                $response = array('status' => TRUE, 'message' => 'Campaign file(s) added successfully', 'data' => $lastInserted, 'message2' => $historyMessage);
                DB::commit();
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function import($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {

            if(isset($attributes['specification_file']) && !empty($attributes['specification_file'])) {
                $specification_file = $attributes['specification_file'];
                if($specification_file->getClientOriginalExtension() != 'zip') {
                    throw new \Exception('Please upload valid file [zip]', 1);
                }
            }
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', '0');

            $excelData = Excel::toArray('', $attributes['campaign_file']);
            array_shift($excelData[0]);

            $validatedData = array();
            $invalidData = array();
            $errorMessages = array();
            //Validate Data
            foreach ($excelData[0] as $key => $row) {
                $responseValidate = $this->validateCampaignData($row);

                if($responseValidate['status'] == TRUE) {
                    $validatedData[] = $responseValidate['validatedData'];
                } else {
                    $invalidData[] = array(
                        'data' => $row,
                        'invalidCells' => $responseValidate['invalidCells'],
                        'errorMessage' => implode(',', $responseValidate['errorMessage'])
                    );
                }
            }

            //Insert Validated Data
            $successCount = 0;
            $failedCount = 0;

            foreach ($validatedData as $index => $attributes) {
                $responseStore = $this->store($attributes);

                if($responseStore['status'] == TRUE) {
                    if(isset($specification_file) && !empty($specification_file)) {
                        $zip = Zip::open($specification_file);
                        $fileList = $zip->listFiles();
                        if(!empty($fileList)) {
                            $campaign_path = 'public/storage/campaigns/'.$responseStore['campaign_id'].'/';
                            $unzips_path = 'public/storage/unzips';
                            foreach ($fileList as $filename) {
                                $exploded = explode('/', $filename);
                                if($attributes['name'] == $exploded[0] && !empty($exploded[1])) {
                                    $zip->extract($unzips_path, $filename);
                                    if(!File::exists($campaign_path)) {
                                        File::makeDirectory($campaign_path, $mode = 0777, true, true);
                                    }
                                    File::move($unzips_path.'/'.$exploded[0].'/'.$exploded[1], $campaign_path.$exploded[1]);
                                    File::deleteDirectory($unzips_path.'/'.$exploded[0]);

                                    CampaignSpecification::insert([['campaign_id' => $responseStore['id'], 'file_name' => explode('/', $filename)[1], 'extension' => pathinfo($exploded[1], PATHINFO_EXTENSION)]]);
                                }
                            }
                        }
                    }
                    $successCount++;
                } else {
                    $failedCount++;
                }
            }

            if(!empty($invalidData)) {
                $file = Excel::download(new ArrayToExcel($invalidData), 'InvalidCampaigns'. time() . ".xlsx");
                $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.', 'file' => $file);
            } else {
                $response = array('status' => TRUE, 'message' => 'All Campaigns imported successfully');
            }
        } catch (\Exception $exception) {
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function validateCampaignData($data = array())
    {
        $validatedData = array();
        $errorMessage = array();
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        $invalidCells = array();

        try {
            //Validate Campaign Name $data[0]
            if(!empty(trim($data[0]))) {
                $campaign = Campaign::whereName(trim($data[0]))->count();
                if($campaign == 0) {
                    $validatedData['name'] = trim($data[0]);
                } else {
                    $errorMessage['Campaign Name'] = 'Campaign name already exists';
                    $invalidCells[0] = 'Invalid';
                }
            } else {
                $errorMessage['Campaign Name'] = 'Enter campaign name';
                $invalidCells[0] = 'Invalid';
            }

            //Validate V-Mail Campaign ID $data[1]
            $validatedData['v_mail_campaign_id'] = trim($data[1]);

            //Validate Campaign Type $data[2]
            if(!empty(trim($data[2]))) {
                $campaignType = CampaignType::whereName(trim($data[2]))->first();
                if(!empty($campaignType)) {
                    $validatedData['campaign_type_id'] = $campaignType->id;
                } else {
                    $errorMessage['Campaign Type'] = 'Enter valid campaign type';
                    $invalidCells[2] = 'Invalid';
                }
            } else {
                $errorMessage['Campaign Type'] = 'Enter campaign type';
                $invalidCells[2] = 'Invalid';
            }

            //Validate Campaign Filter $data[3]
            if(!empty(trim($data[3]))) {
                $campaignFilter = CampaignFilter::whereName(trim($data[3]))->first();
                if(!empty($campaignFilter)) {
                    $validatedData['campaign_filter_id'] = $campaignFilter->id;
                } else {
                    $errorMessage['Campaign Filter'] = 'Enter valid campaign filter';
                    $invalidCells[3] = 'Invalid';
                }
            } else {
                $errorMessage['Campaign Filter'] = 'Enter campaign filter';
                $invalidCells[3] = 'Invalid';
            }

            //Validate Country(s) $data[4]
            if(!empty(trim($data[4]))) {
                $countries = explode(',', strtolower(trim($data[4])));
                $country = Country::select('id')->whereIn('name', $countries)->get();
                if(($country->count() == count($countries))) {
                    $country_id = array();
                    foreach ($country as $item) {
                        array_push($country_id, $item->id);
                    }
                    $validatedData['country_id'] = $country_id;
                } else {
                    $errorMessage['Countries'] = 'Enter valid countries';
                    $invalidCells[4] = 'Invalid';
                }
            }

            $start_date = $end_date = null;
            //Validate Start Date $data[5]
            if(!empty(trim($data[5]))) {
                if(is_numeric($data[5])) {
                    $start_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[5]);
                    $start_date = date_format($start_date, 'Y-m-d');
                } else {
                    $start_date = date('Y-m-d', strtotime($data[5]));
                }

                if($start_date != '1970-01-01') {
                    $validatedData['start_date'] = $start_date;
                } else {
                    $errorMessage['Start Date'] = 'Enter valid start date';
                    $invalidCells[5] = 'Invalid';
                }
            } else {
                $errorMessage['Start Date'] = 'Enter start date';
                $invalidCells[5] = 'Invalid';
            }

            //Validate End Date $data[6]
            if(!empty(trim($data[6]))) {
                if(is_numeric($data[6])) {
                    $end_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[6]);
                    $end_date = date_format($end_date, 'Y-m-d');
                } else {
                    $end_date = date('Y-m-d', strtotime($data[6]));
                }

                if($end_date != '1970-01-01') {
                    $validatedData['end_date'] = $end_date;
                } else {
                    $errorMessage['End Date'] = 'Enter valid end date';
                    $invalidCells[6] = 'Invalid';
                }
            } else {
                $errorMessage['End Date'] = 'Enter end date';
                $invalidCells[6] = 'Invalid';
            }

            //Check Start Date & End Date
            if($start_date > $end_date) {
                $errorMessage['Start Date & End Date'] = 'Enter valid start date & end date';
            }

            //Validate Allocation $data[7]
            if(!empty(trim($data[7]))) {
                $allocation = trim($data[7]);
                if(is_numeric($allocation) && $allocation > 0) {
                    $validatedData['allocation'] = $allocation;
                } else {
                    $errorMessage['Allocation'] = 'Enter valid allocation';
                    $invalidCells[7] = 'Invalid';
                }
            } else {
                $errorMessage['Allocation'] = 'Enter allocation';
                $invalidCells[7] = 'Invalid';
            }

            //Validate Status $data[8]
            if(!empty(trim($data[8]))) {
                $campaignStatus = CampaignStatus::whereName(trim($data[8]))->first();
                if(!empty($campaignStatus)) {
                    $validatedData['campaign_status_id'] = $campaignStatus->id;
                } else {
                    $errorMessage['Status'] = 'Enter valid status';
                    $invalidCells[8] = 'Invalid';
                }
            } else {
                $errorMessage['Status'] = 'Enter valid status';
                $invalidCells[8] = 'Invalid';
            }

            //Validate Pacing $data[9]
            if(!empty(trim($data[9]))) {
                $pacings = array('Daily', 'Monthly', 'Weekly');
                if(in_array(ucfirst(trim($data[9])), $pacings)) {
                    $validatedData['pacing'] = ucfirst(trim($data[9]));
                } else {
                    $errorMessage['Pacing'] = 'Enter valid pacing';
                    $invalidCells[9] = 'Invalid';
                }
            } else {
                $errorMessage['Pacing'] = 'Enter pacing';
                $invalidCells[9] = 'Invalid';
            }

            //Validate Delivery Count $data[10]
            if(!empty(trim($data[10]))) {
                $deliver_count = trim($data[10]);
                if(is_numeric($deliver_count) && $deliver_count > 0) {
                    $validatedData['deliver_count'] = $deliver_count;
                } else {
                    $errorMessage['Delivery Count'] = 'Enter valid delivery count';
                    $invalidCells[10] = 'Invalid';
                }
            }

            if(empty($errorMessage)) {
                $response = array('status' => TRUE, 'validatedData' => $validatedData);
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }


        } catch (\Exception $exception) {
            $response = array(
                'status' => FALSE,
                'errorMessage' => $errorMessage,
                'invalidCells' => $invalidCells
            );
        }

        return $response;
    }
}
