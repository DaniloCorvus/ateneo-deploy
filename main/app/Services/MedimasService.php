<?php

namespace App\Services;

use App\Models\RegimenType;
use App\Models\TypeDocument;

use Goutte\Client;

use App\Traits\ConsumeService;
use App\Traits\filterDocumentType;
use App\Traits\filterRegimenType;
use App\Traits\getNombrePartitions;
use Carbon\Carbon;
use App\Traits\manipulateDataFromExternalService;
use Illuminate\Support\Facades\Log;

class MedimasService
{
    use ConsumeService, getNombrePartitions, filterDocumentType, filterRegimenType, manipulateDataFromExternalService;

    /**
     * The base uri to be used to consume the authors service
     * @var string
     */
    public $body = [];
    public $url;
    public $documentType;
    public $documentNumber;
    public static $dataTemp = [];
    public static $dataformat = [];
    public $count = 0;
    public $client;
    private $viewState;
    private $eventValidation;

    /**
     * The secret to be used to consume the authors service
     * @var string
     */
    public $secret;


    public function __construct($documentType, $documentNumber)
    {
        $dataTemp = [];
        $dataformat = [];

        $this->documentType = $documentType;
        $this->documentNumber = $documentNumber;

        $this->client = new Client();
        $this->url = 'https://www.heon.com.co/Medimas/Contributivo/WebPPrestadores4747/PaginasVD/wfVerificaDerechosS.aspx?IdPrestador=g2qg9bUrk1kneDVR%2bGp88g==&iEpsPar=C11SVS2t5x4U6hmiE96hAQ==&sUsuarioLog=ZXDEmxmkK9u6NE4okOnmmw==';

        $this->verifyCredentials();;
        $this->documentypes = TypeDocument::all();
        $this->regimentypes = RegimenType::all();
    }

    public function verifyCredentials()
    {
        $crawler =   $this->client->request('POST', $this->url);
        $this->viewState = $crawler->filter('#__VIEWSTATE')->attr('value');
        $this->eventValidation = $crawler->filter('#__EVENTVALIDATION')->attr('value');
    }



    public function getDataMedimas()
    {
        self::$dataformat = [];
        self::$dataTemp = [];

        $this->body = [
            'ctl00$ContentPlaceHolder2$txtNumDocumento'  => $this->documentNumber,
            'ctl00$ContentPlaceHolder2$ddlTipoDocumento'  => $this->documentType,
            '__EVENTTARGET'  => 'ctl00$ContentPlaceHolder2$ddlTipoDocumento',
            '__EVENTARGUMENT'  => '',
            '__VIEWSTATE'  =>  $this->viewState,
            '__VIEWSTATEGENERATOR'  => 'B297FF07',
            '__EVENTVALIDATION'  =>   $this->eventValidation,
        ];

        $crawler =   $this->client->request('POST', $this->url, $this->body);

        $crawler->filter('#ctl00_ContentPlaceHolder2_ddlTipoDocumento > option')->each(function ($node, $i) {
            if ($node->attr('selected') == 'selected') {
                self::$dataformat['type_document_id'] = $node->attr('value');
            }
        });

        try {
            self::$dataformat['identifier'] = $crawler->filter('#ctl00_ContentPlaceHolder2_txtNumDocumento')->attr('value');
        } catch (\Throwable $th) {
            return $this;
        }

        $crawler->filter('table  > tr > td')->each(function ($node, $i) {
            if ($i > 8) {
                if ($i % 2  == 0) {
                    self::$dataTemp[$this->count]['Valor'] = $node->text();
                    $this->count++;
                } else {
                    self::$dataTemp[$this->count]['Columna'] = $node->text();
                }
            }
        });
        return $this;
    }


    public function loopDataMedimas()
    {

        foreach (self::$dataTemp as  $item) {
            $this->fillInDataMedimas($item);
        }
        return self::$dataformat;
    }

    public function fillInDataMedimas($data)
    {
        switch ($data['Columna']) {
            case 'Tipo Identificación':
                self::$dataformat['type_document_id'] = $this->filterDocumentType($data['Valor']);
                break;
            case 'Numero de Identificación':
                self::$dataformat['identifier'] = $data['Valor'];
                break;
            case 'Regimen:':
                self::$dataformat['regimen_id'] = $this->filterRegimenType($data['Valor']);
                break;
            case 'Nombres y apellidos:':
                $this->init($data['Valor']);
                self::$dataformat['firstname'] =   $this->firstname;
                self::$dataformat['middlename'] =   $this->middlename;
                self::$dataformat['secondsurname'] =   $this->secondsurname;
                self::$dataformat['surname'] =   $this->surname;
                break;
            case 'Edad:':
                $porciones = explode(" ", $data['Valor']);
                self::$dataformat['date_of_birth'] = $this->getDateOfBirth($porciones);
                break;
            case 'Estado:':
                self::$dataformat['state'] = ($data['Valor'] == 'Verificación de derechos positiva. Atención con cargo a la EPS.') ? 'Activo' : 'Inactivo';
                break;
            case 'Nivel IBC:':
                self::$dataformat['level_id'] = $data['Valor'];
                break;
            case 'Municipio Residencia:':
                if (isset($this->appendMunicipaly($data['Valor'])->id)) {
                    self::$dataformat['municipality_id'] = $this->appendMunicipaly($data['Valor'])->id;
                    self::$dataformat['department_id'] = $this->appendDeparment($data['Valor'])->id;
                    self::$dataformat['regional_id'] = $this->appendRegional(self::$dataformat['department_id']);
                }
                break;
            case 'IPS a la que pertenece:':
                self::$dataformat['ips_principal'] = $data['Valor'];
                break;
            case 'Tipo de afiliado:':
                self::$dataformat['affiliate_type'] = $data['Valor'];
                break;
            default:
                break;
        }
        self::$dataformat['eps_id'] = '1';
        self::$dataformat['database'] = 'medimas';
    }
}
