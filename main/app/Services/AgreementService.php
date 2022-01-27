<?php

namespace App\Services;

use App\Traits\ConsumeService;

class AgreementService
{
    use ConsumeService;

    /**
     * The base uri to be used to consume the authors service
     * @var string
     */
    public $baseUri;

    /**
     * The secret to be used to consume the authors service
     * @var string
     */
    public $secret;


    public function __construct()
    {
        $this->baseUri = env('BASE_URI');
        $this->token = env('TOKEN');
    }

    /**
     * Get the full list of authors from the authors service
     * @return string
     */
    public function get()
    {
        // 27 para medimas 
        // 17 para ecoopsos 
        $queryString = '{"MaxResults":0,"MatchAll":true,"IPSIDs":[],"SiteID":null,"EPSID":17,"Regime":null}';
        return $this->performRequest('GET', '/service/ContractService.svc/contract?', $queryString);
    }
}
