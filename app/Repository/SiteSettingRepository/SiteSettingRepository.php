<?php

namespace App\Repository\SiteSettingRepository;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;

class SiteSettingRepository implements SiteSettingInterface
{
    private $siteSetting;

    public function __construct(SiteSetting $siteSetting)
    {
        $this->siteSetting = $siteSetting;
    }

    public function get($filters = array())
    {
        return $this->siteSetting->get();
    }

    public function find($id)
    {
        return $this->siteSetting->findOrFail($id);
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $site_setting = new SiteSetting();
            $site_setting->key = $attributes['key'];
            $site_setting->value = $attributes['value'];
            $site_setting->status = $attributes['status'];
            $site_setting->save();
            if($site_setting->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Site setting added successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function update($id, $attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $site_setting = $this->find($id);
            $site_setting->key = $attributes['key'];
            $site_setting->value = $attributes['value'];
            $site_setting->status = $attributes['status'];
            $site_setting->update();
            if($site_setting->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Site setting updated successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function destroy($id): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $site_setting = $this->find($id);
            if($site_setting->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Site setting deleted successfully');
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
