<?php

namespace App\Services;

use App\Model\AppToken;
use Illuminate\Support\Str;

class AppTokenService
{
    /**
     * Generate a random token
     *
     * @return string
     */
    public function generateToken(): string
    {
        return Str::random(40);
    }

    /**
     * Create a new app token
     *
     * @param array $data
     * @return AppToken
     */
    public function create(array $data): AppToken
    {
        $auth = auth()->user();
        $data['token'] = $this->generateToken();

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];

        return AppToken::create($data);
    }

    /**
     * Update an existing app token
     *
     * @param AppToken $token
     * @param array $data
     * @return AppToken
     */
    public function update(AppToken $token, array $data): AppToken
    {
        // Remove token from data to prevent updating it
        unset($data['token']);

        $auth = auth()->user();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];

        $token->update($data);

        return $token;
    }

    /**
     * Delete an app token
     *
     * @param AppToken $token
     * @return void
     */
    public function delete(AppToken $token): void
    {
        $auth = auth()->user();

        $token->update([
            'deleted_at' => now(),
            'deleted_by' => $auth->id,
            'del_flag'   => 'Y',
        ]);

        $token->delete();
    }

    public function getList($search)
    {
        $query = AppToken::select(
            'app_tokens.id',
            'app_tokens.app_name',
            'app_tokens.token',
            'app_tokens.description',
            'app_tokens.created_at',
            'app_tokens.created_by',
            'users.first_name',
            'users.last_name',
            'master_table.name'
        )->where('app_tokens.del_flag', 'N');

        $query->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'app_tokens.created_by');
        });
        $query->leftJoin('master_table', function ($join) {
            $join->on('master_table.id', '=', 'app_tokens.referral_type');
        })->where('master_table.master_type_fk', 34);
        if (isset($search['app_name']) && $search['app_name'] != "") {
            $query->where('app_tokens.app_name', 'LIKE', '%' . $search['app_name'] . '%');
        }

        return $query->orderBy('app_tokens.id', 'desc')->paginate(50);
    }

    public function getDetailTokenWise($token){
        return AppToken::where('del_flag','N')->where('token',$token)->first();
    }

    public function getDetailsById($id){
        return AppToken::where('del_flag','N')->where('id',$id)->first();
    }
}
