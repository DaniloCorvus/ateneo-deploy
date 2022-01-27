<?php

namespace App\Services;

use App\Traits\ConsumeService;

class EspecialidadService
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
        $queryString = '{"MaxResults":0,"MatchAll":true,"SpecialityID":null,"InstitutionID":null,"InstitutionIDs":null,"SpecialtiesByInstitution":false}';
        return $this->performRequest('GET', '/service/SpecialityService.svc/specialities', $queryString);
    }
}
