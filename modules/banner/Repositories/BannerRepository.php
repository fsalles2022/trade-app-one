<?php

namespace Banner\Repositories;

use Banner\Exceptions\BannerNotFound;
use Banner\Models\Banner;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BannerRepository implements RepositoryInterface
{
    protected $table = 'banners';

    public function save($attributes)
    {
        $attributes               = array_filter(get_object_vars($attributes));
        $attributes["created_at"] = \Carbon\Carbon::now();
        $attributes["updated_at"] = \Carbon\Carbon::now();
        if ($id = DB::table($this->table)->insertGetId($attributes)) {
            $bannerFromDatabase = DB::table($this->table)->find($id);
            $banner             = new Banner();
            $banner->fill(get_object_vars($bannerFromDatabase));
            return $banner;
        }
        return null;
    }

    public function find($id)
    {
        $bannerFromDatabase = DB::table($this->table)->find($id);
        $banner             = new Banner();
        $banner->fill(get_object_vars($bannerFromDatabase));
        return $banner;
    }

    public function edit($id, array $attributes)
    {
        $exists = DB::table($this->table)->find($id);
        throw_if(! $exists, new BannerNotFound());
        unset($attributes['id']);
        $edit               = DB::table($this->table)->where('id', $id)->update($attributes);
        $bannerFromDatabase = DB::table($this->table)->find($id);
        $banner             = new Banner();
        $bannerAttributes   = filled($bannerFromDatabase) ? get_object_vars($bannerFromDatabase) : [];
        $banner->fill($bannerAttributes);
        return $banner;
    }

    public function findByCredentials(string $client, string $secret)
    {
        return DB::table('clients')->where('access_key')->where('access_secret')->get();
    }

    public function getByKey(string $key)
    {
        return DB::table($this->table)
            ->where('key', 'like', $key . '%')
            ->where('deleted_at', null)
            ->get();
    }

    public function destroy($id)
    {
        return DB::table($this->table)->where('id', $id)->update(['deleted_at' => Carbon::now()]);
    }
}
