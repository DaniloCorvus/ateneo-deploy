<?php

namespace App\Services;

use App\Models\RegimenType;
use App\Models\TypeDocument;
use App\Traits\ConsumeService;
use App\Traits\filterDocumentType;
use App\Traits\filterRegimenType;
use App\Traits\getNombrePartitions;
use App\Traits\manipulateDataFromExternalService;
use DateTime;
use Goutte\Client;

class EcoopsosService
{
    use ConsumeService, filterDocumentType, filterRegimenType, getNombrePartitions,  manipulateDataFromExternalService;

    /**
     * The base uri to be used to consume the authors service
     * @var string
     */
    public $baseUri;
    public $queryParams;
    public static $dataTemp = [];
    public static $dataformat = [];
    public $count = 0;



    public function __construct($documentType, $documentNumber)
    {
        $this->documentypes = TypeDocument::all();
        $this->regimentypes = RegimenType::all();

        $this->baseUri = 'https://webecoopsos.ecoopsos.com.co/joomla/index/red-de-servicios/comprobador-de-derechos/ComprobadorEcoopsos.php';
        $this->queryParams = [
            "cedula=$documentNumber",
            "tip=2",
            'enviar=Verificar',
        ];
    }

    /**
     * Get the full list of authors from the authors service
     * @return object
     */

    public function getDataWebEcoopsos()
    {

        self::$dataformat = [];
        self::$dataTemp = [];
        $client = new Client();

        $content = implode('&',  $this->queryParams);

        $crawler = $client->request(
            'POST',
            'https://webecoopsos.ecoopsos.com.co/joomla/index/red-de-servicios/comprobador-de-derechos/ComprobadorEcoopsos.php',
            [],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded'],
            $content
        );

        $crawler->filter('table > tr > td')->each(function ($node, $i) {
            if ($i % 2  == 0) {
                self::$dataTemp[$this->count]['Columna'] = $node->text();
            } else {
                self::$dataTemp[$this->count]['Valor'] = $node->text();
                $this->count++;
            }
        });
        $crawler->filter('table > td')->each(function ($node, $i) {
            if ($i % 2  == 0) {
                self::$dataTemp[$this->count]['Columna'] = $node->text();
            } else {
                self::$dataTemp[$this->count]['Valor'] = $node->text();
                $this->count++;
            }
        });

        return  $this;
    }

    public function loopDataEcoopsos()
    {
        foreach (self::$dataTemp as  $item) {
            $this->fillInDataEcoopsos($item);
        }
        return self::$dataformat;
    }


    public function fillInDataEcoopsos($data)
    {
        switch ($data['Columna']) {
            case 'Tipo Identificación':
                self::$dataformat['type_document_id'] = $this->filterDocumentType($data['Valor']);
                break;
            case 'Numero de Identificación':
                self::$dataformat['identifier'] = $data['Valor'];
                break;
            case 'Tipo de Régimen':
                self::$dataformat['regimen_id'] = $this->filterRegimenType($data['Valor']);
            case 'Tipo de Afiliado':
                self::$dataformat['affiliate_type'] = $data['Valor'];
                break;
            case 'Categoria Afiliado':
                self::$dataformat['category_affiliate'] = $data['Valor'];
                break;
            case 'Nombres y Apellidos':
                $this->init($data['Valor']);
                self::$dataformat['firstname'] =   $this->firstname;
                self::$dataformat['middlename'] =   $this->middlename;
                self::$dataformat['secondsurname'] =   $this->secondsurname;
                self::$dataformat['surname'] =   $this->surname;
                break;
            case 'Fecha Nacimiento':
                self::$dataformat['date_of_birth'] = DateTime::createFromFormat('d/m/Y', $data['Valor'])->format('Y-m-d');
                break;
            case 'Estado en Base de Datos':
                self::$dataformat['state'] = $data['Valor'];
                break;
            case 'Nivel':
                self::$dataformat['level_id'] = $data['Valor'];
                break;
            case 'Ficha':
                self::$dataformat['token'] = $data['Valor'];
                break;
            case 'IPS Puerta de Entrada':
                self::$dataformat['ips_principal'] = $data['Valor'];
                break;
            case 'Municipio':
                if (isset($this->appendMunicipaly($data['Valor'])->id)) {
                    self::$dataformat['municipality_id'] = $this->appendMunicipaly($data['Valor'])->id;
                    self::$dataformat['department_id'] = $this->appendDeparment($data['Valor'])->id;
                    self::$dataformat['regional_id'] = $this->appendRegional(self::$dataformat['department_id']);
                }
                break;
            default:
                break;
        }
        self::$dataformat['eps_id'] = '2';
        self::$dataformat['database'] = 'ecoopsos';
    }
}
