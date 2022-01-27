<?php

namespace App\Services;

use App\Traits\ConsumeService;

class RegimenService
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
        $queryString = '{"MaxResults":0,"MatchAll":true,"RoleID":3,"RoleIDs":null,"InstitutionID":32,"InstitutionIDs":null,"SpecialityID":102,"ExamGroupID":null,"IsParticular":null,"IsInstitutional":true}';

        return $this->performRequest('GET', '/callcenter/scripts/core/utils/json/regimes.json');
    }
}
