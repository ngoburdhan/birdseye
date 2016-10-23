<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use Cache;

class Controller extends BaseController
{
    private $cacheKey;
    protected $cacheUsed = false;


    public function __construct() {
        if( !env('CACHE_KEY',false) ) {  //}|| !is_executable( env('BIRDC') ) ) {
            abort( 500, "Cache key not specified" );
        }
        $this->cacheKey = env('CACHE_KEY');
    }

    public function cacheKey() {
        return $this->cacheKey;
    }

    protected function verifyAndSendJSON( $key, $response, $api = null ) {
        if( $api === null ) {
            $api = [];
        }

        $api['version'] = env('API_VERSION','0.0.0');
        $api['env']     = $_ENV['BIRDSEYE_ENV_FILE'];

        if( !is_array($response) ) {
            abort(503, "Unknown internal error");
        }

        return response()->json(['api' => $api, $key => $response]);
    }

    protected function getSymbols() {
        if( $symbols = Cache::get( $this->cacheKey() . 'symbols' ) ) {
            $this->cacheUsed = true;
        } else {
            $symbols = app('Bird')->symbols();
            Cache::put($this->cacheKey() . 'symbols', $symbols, env( 'CACHE_SHOW_SYMBOLS', 1 ) );
        }
        return $symbols;
    }

}
