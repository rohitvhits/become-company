<?php

namespace App\Services;

use App\Model\DomainConfig;

class DomainConfigService
{
    public function getAll()
    {
        return DomainConfig::orderBy('id', 'desc')->get();
    }

    public function getDetailById($id)
    {
        return DomainConfig::where('id', $id)->first();
    }

    public function getByDomain($domain)
    {
        return DomainConfig::where('domain', $domain)->first();
    }

    public function save($data)
    {
        $insert = new DomainConfig($data);
        $insert->save();
        return $insert;
    }

    public function update($data, $where)
    {
        return DomainConfig::where($where)->update($data);
    }

    public function delete($id)
    {
        $config = DomainConfig::find($id);
        if ($config) {
            $config->deleted_by  = auth()->id();
            $config->delete_flag = 1;
            $config->save();
            $config->delete();
        }
    }

    public function uploadFile($file)
    {
        $destination = public_path('assets/images/domain_config');
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move($destination, $filename);
        return 'assets/images/domain_config/' . $filename;
    }
}
