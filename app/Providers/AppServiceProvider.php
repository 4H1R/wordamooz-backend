<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Knuckles\Scribe\Scribe;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();
        if (class_exists(\Knuckles\Scribe\Scribe::class)) {
            Scribe::beforeResponseCall(function (Request $request) {
                $user = User::query()->firstOrFail();
                $token = $user->createToken('scribe token')->plainTextToken;
                $request->headers->add(["Authorization" => "Bearer $token"]);
            });
        }
        Schema::defaultStringLength(191);
        DB::listen(function ($sql) {
            Log::info($sql->sql, $sql->bindings, $sql->time);
        });
    }
}
