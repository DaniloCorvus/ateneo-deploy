<?php

namespace App\Services;

use App\Traits\ConsumeService;

class TestService
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
    public function obtainConsumes()
    {
        $queryOptions = '{"MaxResults":25,"MatchAll":true,"PatientInternalID":null,"PatientBirthDate":null,"StartCreatedDate":null,"EndCreatedDate":null,"Institutions":null,"IncludeInstitutions":null}';
        return $this->performRequest('GET', '/service/PatientService.svc/patients', $queryOptions);
    }

    /**
     * Create an instance of author using the authors service
     * @return string
     */
    public function createConsume($data)
    {
        return $this->performRequest('POST', '/authors', $data);
    }

    /**
     * Get a single author from the authors service
     * @return string
     */
    public function obtainConsume($author)
    {
        return $this->performRequest('GET', "/authors/{$author}");
    }

    /**
     * Edit a single author from the authors service
     * @return string
     */
    public function editConsume($data, $author)
    {
        return $this->performRequest('PUT', "/authors/{$author}", $data);
    }

    /**
     * Remove a single author from the authors service
     * @return string
     */
    public function deleteConsume($author)
    {
        return $this->performRequest('DELETE', "/authors/{$author}");
    }
}
