<?php

namespace App\Services;

use App\Traits\ConsumeService;

class PersonService
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
        //Para medicos persones
        //  https://mmedical.mmgroup.com.co/service/UserService.svc/doctors?queryOptions={"MaxResults":0,"MatchAll":true,"RoleID":3,"RoleIDs":null,"InstitutionID":32,"InstitutionIDs":null,"SpecialityID":,"ExamGroupID":null,"IsParticular":null,"IsInstitutional":true}&
        //  token=C0T4Of8t2UjOTTfo4g5Dj1hw4kEyVKoPqolm2A1XrHeR%2FsvFVSYEDlNOs%2BHk7xvccWRvdcPBzzSo8nffrBpiHUVbgzoQwI7Pq1JyvZr5Sw9%2F8X3UOKfcdIuDloUMmkeEckY3PZ5yFrLhDjIsFDb7%2Bo9Or7Hd9O2uc%2FVALPZTTbjj8c75XJw0ogT2Wj%2FiMYPH%2FPo4ONseUZuqw2ZmR1yaKA%3D%3D


        $queryString = '{"MaxResults":0,"MatchAll":true,"RoleID":3,"RoleIDs":null,"InstitutionID":null,"InstitutionIDs":null,"SpecialityID":null,"ExamGroupID":null,"IsParticular":null,"IsInstitutional":true}';
        // $queryString = '{"MaxResults":500,"MatchAll":true,"PatientInternalID":null,"PatientBirthDate":null,"StartCreatedDate":null,"EndCreatedDate":null,"Institutions":null,"IncludeInstitutions":null}';
        // $queryString = '{"MaxResults":1000,"MatchAll":true,"RoleID":null,"RoleIDs":null,"InstitutionID":null,"InstitutionIDs":null,"SpecialityID":null,"ExamGroupID":null,"IsParticular":null,"IsInstitutional":true}';
        // return $this->performRequest('GET', 'service/UserService.svc/doctors', $queryString);
        return $this->performRequest('GET', '/service/UserService.svc/doctors', $queryString);
        // return $this->performRequest('GET', 'service/PatientService.svc/patients', $queryString);
    }
}
