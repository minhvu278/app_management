<?php

namespace Botble\AppManagement\Repositories\Eloquent;

use Botble\AppManagement\Models\App;
use Botble\AppManagement\Models\AppVersion;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\AppManagement\Repositories\Interfaces\AppVersionInterface;
use Illuminate\Support\Str;

class AppVersionRepository extends RepositoriesAbstract implements AppVersionInterface
{
    public function handleStatus($status, $platform, $appId)
    {
        if($status == 'active' && $platform == 'ios'){
            AppVersion::where('app_id', $appId)
                    ->where('platform', 'ios')
                    ->update(['status' => 'deactive']);
        }elseif($status == 'active' && $platform == 'android'){
            AppVersion::where('app_id', $appId)
                    ->where('platform', 'android')
                    ->update(['status' => 'deactive']);
        }
    }
    
    public function uploadPlist($app_id){
        $test = $this->plist();
        $appname = App::where('id', $app_id)->get();
        $namepath = Str::of($appname[0]->name)->slug('-');
        $filename = public_path('storage/') .  $namepath . '.plist';
        $myfile = fopen($filename, "w") or die("Unable to open file!");
        $resource = fwrite($myfile, $test);
        fclose($myfile);
        $data['url'] = isset($resource) ? $filename : '';
        return $data; 
    }

    public function plist()
    {
        $plist = AppVersion::where('platform','ios')
                ->where('status','active')
                ->first();
        return  \Theme::scope('hello', compact('plist'))->render(); 
    }
}
