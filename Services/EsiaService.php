<?php


namespace Modules\Esia\Services;


use Esia\Config;
use Esia\OpenId;
use Esia\Signer\SignerPKCS7;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Modules\Esia\Entities\EsiaAccount;

class EsiaService
{
    private $config;

    private $oid;

    private $errors;

    public function __construct(Request $request, array $config = [])
    {
        $config = array_merge_recursive($config, config('esia'));
        $account = $this->getAccountByUser($request->header('Current-User'));

        if (!$account) {
            throw new \Exception('Token mismatch');
        }

        $config['token'] = $account->token;
        $config['oid'] = $account->oid;

        $this->errors = require module_path('Esia', '/Resources/errors/esia_error_codes.php');

        $this->config = new Config($config);
        $this->oid = new OpenId($this->config);
    }

    public function getConfig()
    {
        return $this->oid->getConfig();
    }

    public function getConfigParam(string $name)
    {
        $name = 'get' . ucfirst($name);
        return $this->config->$name() ?? null;
    }

    public function getDocInfo()
    {
        return $this->oid->getDocInfo();
    }

    public function getPersonInfo()
    {
        return $this->oid->getPersonInfo();
    }

    public function getCategoriesInfo(Request $request, $oid)
    {
        $oid = $oid ?: $this->config->getOid();
        $url = $this->config->getPortalUrl() . 'categories/prns/' . $oid;
        $response = $this->sendRequest('get', $url, [], $request->all());
        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response['elements']);
        }
        return $response;
    }

    public function getOrganizationCategoriesInfo(Request $request, $oid)
    {
        $url = $this->config->getPortalUrl() . 'categories/orgs/' . $oid;
        $response = $this->sendRequest('get', $url);
        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response['elements']);
        }
        return $response;
    }

    public function getPersonsInfo(Request $request, $oid, $collection, $collectionEntityId)
    {
        $oid = $oid ?: $this->config->getOid();
        $url = $this->config->getPortalUrl() . 'rs/prns/' . $oid;
//        $url = $this->config->getPortalUrl() . '/esia-rs/api/public/v2/prns/' . $oid;
        if ($collection) {
            $url .= '/' . $collection;
        }
        if ($collectionEntityId) {
            $url .= '/' . $collectionEntityId;
        }
        $response = $this->sendRequest('get', $url, [], $request->all());

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response['elements']);
        }

        return $response;
    }

    public function getAddressInfo()
    {
        return $this->oid->getAddressInfo();
    }

    public function getContactsInfo()
    {
        $url = $this->config->getPersonUrl() . '/ctts';
        $response = $this->sendRequest('get', $url);

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response['elements']);
        }

        return $response;
    }

    public function getRolesInfo(Request $request)
    {
        $url = $this->config->getPersonUrl() . '/roles';
        $response = $this->sendRequest('get', $url, [], $request->all());

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response['elements']);
        }

        return $response;
    }

    public function getOrganizationInfo(
        Request $request,
        $orgId,
        $collection,
        $collectionEntityId,
        $groups,
        $groupId,
        $isSys
    ) {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId;

        if ($collection) {
            $url .= '/' . $collection;
        }
        if ($collectionEntityId) {
            $url .= '/' . $collectionEntityId;
        }
        if ($groups) {
            $url .= '/grps';
        }
        if ($groupId) {
            $url .= '/' . $groupId;
        }
        if ($isSys) {
            $url .= '/' . $isSys;
        }

        $response = $this->sendRequest('get', $url, [], $request->all());

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    public function updateOrganizationForm(Request $request, $orgId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId;

        $response = $this->getOrganizationInfo(
            $request,
            $orgId,
            null,
            null,
            null,
            null,
            null
        );

        if ($response) {
            $eTag = $response['eTag'];
            $leg = $response['leg'];

            $response = $this->sendRequest(
                'post',
                $url,
                ['If-Match' => $eTag, 'Cache-Control' => 'no-cache'],
                ['eTag' => $eTag, 'leg' => $request->leg]
            );
        }

        return $response;
    }

    // Contacts

    public function addOrganizationContact(Request $request, $orgId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/ctts';

        $response = $this->getOrganizationInfo(
            $request,
            $orgId,
            'ctts',
            null,
            null,
            null,
            null
        );
        if ($response) {
            $eTag = $response['eTag'];
            $type = $request->type;
            $value = $request->value;

            $response = $this->sendRequest(
                'post',
                $url,
                ['If-Match' => $eTag, 'Cache-Control' => 'no-cache'],
                ['type' => $type, 'value' => $value]
            );
        }

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    public function updateOrganizationContact(Request $request, $orgId, $contactId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/ctts/' . $contactId;

        $response = $this->getOrganizationInfo(
            $request,
            $orgId,
            'ctts',
            $contactId,
            null,
            null,
            null
        );

        if ($response) {
            $eTag = $response['eTag'];
            $type = $request->type;
            $value = $request->value;

            $response = $this->sendRequest(
                'post',
                $url,
                ['If-Match' => $eTag, 'Cache-Control' => 'no-cache'],
                ['eTag' => $eTag, 'type' => $type, 'value' => $value]
            );
        }

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    public function deleteOrganizationContact(Request $request, $orgId, $contactId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/ctts/' . $contactId;

        $response = $this->getOrganizationInfo(
            $request,
            $orgId,
            'ctts',
            $contactId,
            null,
            null,
            null
        );

        if ($response) {
            $eTag = $response['eTag'];
            $response = $this->sendRequest(
                'delete',
                $url,
                ['If-Match' => $eTag, 'Cache-Control' => 'no-cache']
            );
        }

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    // Addresses

    public function addOrganizationAddress(Request $request, $orgId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/addrs';

        $response = $this->getOrganizationInfo(
            $request,
            $orgId,
            'addrs',
            null,
            null,
            null,
            null
        );

        if ($response) {
            $eTag = $response['eTag'];
            $type = $request->type;
            $region = $request->region;
            $fiasCode = $request->fiasCode;
            $addressStr = $request->addressStr;
            $countryId = $request->countryId;
            $zipCode = $request->zipCode;
            $street = $request->street;
            $house = $request->house;
            $flat = $request->flat;
            $frame = $request->frame;
            $building = $request->building;

            $response = $this->sendRequest(
                'post',
                $url,
                ['If-Match' => $eTag, 'Cache-Control' => 'no-cache'],
                [
                    'type' => $type,
                    'region' => $region,
                    'fiasCode' => $fiasCode,
                    'addressStr' => $addressStr,
                    'countryId' => $countryId,
                    'zipCode' => $zipCode,
                    'street' => $street,
                    'house' => $house,
                    'flat' => $flat,
                    'frame' => $frame,
                    'building' => $building,
                ]
            );
        }

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    public function updateOrganizationAddress(Request $request, $orgId, $addressId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/addrs/' . $addressId;

        $response = $this->getOrganizationInfo(
            $request,
            $orgId,
            'addrs',
            $addressId,
            null,
            null,
            null
        );

        if ($response) {
            $eTag = $response['eTag'];
            $type = $request->type;
            $region = $request->region;
            $fiasCode = $request->fiasCode;
            $addressStr = $request->addressStr;
            $countryId = $request->countryId;
            $zipCode = $request->zipCode;
            $street = $request->street;
            $house = $request->house;
            $flat = $request->flat;
            $frame = $request->frame;
            $building = $request->building;

            $response = $this->sendRequest(
                'post',
                $url,
                ['If-Match' => $eTag, 'Cache-Control' => 'no-cache'],
                [
                    'type' => $type,
                    'region' => $region,
                    'fiasCode' => $fiasCode,
                    'addressStr' => $addressStr,
                    'countryId' => $countryId,
                    'zipCode' => $zipCode,
                    'street' => $street,
                    'house' => $house,
                    'flat' => $flat,
                    'frame' => $frame,
                    'building' => $building,
                ]
            );
        }

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    public function deleteOrganizationAddress(Request $request, $orgId, $addressId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/addrs/' . $addressId;

        $response = $this->getOrganizationInfo(
            $request,
            $orgId,
            'addrs',
            $addressId,
            null,
            null,
            null
        );

        if ($response) {
            $eTag = $response['eTag'];
            $response = $this->sendRequest(
                'delete',
                $url,
                ['If-Match' => $eTag, 'Cache-Control' => 'no-cache']
            );
        }

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    // Vehicles

    public function addOrganizationVehicle(Request $request, $orgId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/vhls';

        $response = $this->getOrganizationInfo(
            $request,
            $orgId,
            'vhls',
            null,
            null,
            null,
            null
        );

        if ($response) {
            $eTag = $response['eTag'];
            $name = $request->name;
            $numberPlate = $request->numberPlate;
            $series = $request->series;
            $number = $request->number;

            $response = $this->sendRequest(
                'post',
                $url,
                ['If-Match' => $eTag, 'Cache-Control' => 'no-cache'],
                [
                    'name' => $name,
                    'numberPlate' => $numberPlate,
                    'regCertificate' => [
                        'series' => $series,
                        'number' => $number
                    ],
                ]
            );
        }

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    public function updateOrganizationVehicle(Request $request, $orgId, $vehicleId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/vhls/' . $vehicleId;

        $response = $this->getOrganizationInfo(
            $request,
            $orgId,
            'vhls',
            $vehicleId,
            null,
            null,
            null
        );

        if ($response) {
            $eTag = $response['eTag'];
            $name = $request->name;
            $numberPlate = $request->numberPlate;
            $series = $request->series;
            $number = $request->number;

            $response = $this->sendRequest(
                'post',
                $url,
                ['If-Match' => $eTag, 'Cache-Control' => 'no-cache'],
                [
                    'eTag' => $eTag,
                    'name' => $name,
                    'numberPlate' => $numberPlate,
                    'regCertificate' => [
                        'series' => $series,
                        'number' => $number
                    ],
                ]
            );
        }

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    public function deleteOrganizationVehicle(Request $request, $orgId, $vehicleId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/vhls/' . $vehicleId;
        $response = $this->getOrganizationInfo(
            $request,
            $orgId,
            'vhls',
            $vehicleId,
            null,
            null,
            null
        );
        if ($response) {
            $eTag = $response['eTag'];
            $response = $this->sendRequest(
                'delete',
                $url,
                ['If-Match' => $eTag, 'Cache-Control' => 'no-cache']
            );
        }
        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }
        return $response;
    }

    // Invites

    public function getOrganizationInvites(Request $request, $orgId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/invts';
        $response = $this->sendRequest('get', $url, [], $request->all());
        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }
        return $response;
    }

    public function sendOrganizationInvite(Request $request, $orgId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/invts';

        $response = $this->sendRequest(
            'put',
            $url,
            [
                'Cache-Control' => 'no-cache'
            ],
            $request->all()
        );

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    public function deleteOrganizationInvite(Request $request, $orgId, $inviteId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/invts/' . $inviteId;

        $response = $this->sendRequest(
            'delete',
            $url,
            [
                'Cache-Control' => 'no-cache'
            ]
        );

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    // Employees

    public function updateOrganizationEmployee(Request $request, $orgId, $employeeId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/emps/' . $employeeId;

        $response = $this->sendRequest('get', $url);
        if ($response) {
            $eTag = $response['eTag'];
            $position = $request->position;
            $corporateContact = $request->corporateContact;
            $blocked = $request->blocked;
            $response = $this->sendRequest(
                'delete',
                $url,
                ['If-Match' => $eTag, 'Cache-Control' => 'no-cache'],
                [
                    'eTag' => $eTag,
                    'position' => $position,
                    'corporateContact' => $corporateContact,
                    'blocked' => $blocked,
                ]
            );
        }

        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }

        return $response;
    }

    public function deleteOrganizationEmployee(Request $request, $orgId, $employeeId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/emps/' . $employeeId;
        $response = $this->sendRequest('delete', $url);
        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }
        return $response;
    }

    // Branches

    public function addOrganizationBranch(Request $request, $orgId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/brhs';
        $response = $this->sendRequest('get', $url);
        if ($response) {
            $eTag = $response['eTag'];
            $name = $request->name;
            $kpp = $request->kpp;
            $response = $this->sendRequest(
                'post',
                $url,
                ['If-Match' => $eTag],
                [
                    'name' => $name,
                    'kpp' => $kpp,
                ]
            );
        }
        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }
        return $response;
    }

    public function updateOrganizationBranch(Request $request, $orgId, $branchId)
    {
        $url = $this->config->getPortalUrl() . 'rs/orgs/' . $orgId . '/brhs/' . $branchId;
        $response = $this->sendRequest('get', $url);
        if ($response) {
            $eTag = $response['eTag'];
            $name = $request->name;
            $kpp = $request->kpp;
            $response = $this->sendRequest(
                'post',
                $url,
                ['If-Match' => $eTag],
                [
                    'name' => $name,
                    'kpp' => $kpp,
                ]
            );
        }
        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }
        return $response;
    }

    // Updated persons

    public function getPersonsUpdatedSince(Request $request, $type = 'prns')
    {
        $url = $this->config->getPortalUrl() . 'rs/' . $type;
        $response = $this->sendRequest('get', $url, [], [
            'updatedSince' => $request->updatedSince
        ]);
        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }
        return $response;
    }

    // Import user account

    public function importUserAccount(Request $request)
    {
        $data = [
            "firstName" => $request->firstName,
            "lastName" => $request->lastName,
            "middleName" => $request->middleName,
            "birthDate" => $request->birthDate,
            "birthPlace" => $request->birthPlace,
            "gender" => $request->gender,
            "snils" => $request->snils,
            "contacts" =>  $request->contacts ?: [],
            "documents" => $request->documents ?: [],
            "addresses" => $request->addresses ?: []
        ];

        $jsonData = json_encode($data);
        $encodedData = base64_encode($jsonData);

        $signedData = (new SignerPKCS7(
            $this->config->getCertPath(),
            $this->config->getPrivateKeyPath(),
            $this->config->getPrivateKeyPassword(),
            $this->config->getTmpPath()
        ))->sign($jsonData);

        $url = $this->config->getPortalUrl() . '/esia-rs/api/public/v2/prns/import';
        $response = $this->sendRequest('post', $url, [
            'Request-Data-Sign' => $signedData,
            'Request-Data' => $encodedData,
        ], $data);
        if ($response && isset($response['size']) && $response['size'] > 0) {
            return $this->collectArrayElements($response);
        }
        return $response;
    }

    // Avatar

    public function getUserAvatar($avatarType)
    {
        $url = $url = $this->config->getPortalUrl()
            . 'esia-rs/api/public/v1/pso/'
            . $this->config->getOid() . '/avt/' . $avatarType;
        $response = $this->sendRequest('get', $url);
        return $response;
    }

    // ------------------------

    protected function sendRequest(string $method, string $url, array $headers = [], array $data = [])
    {
        $method = strtolower($method);

        if ($token = $this->config->getToken()) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->withData($data)
            ->asJson(true)
            ->allowRedirect()
            ->withConnectTimeout(60)
            ->returnResponseObject()
            ->enableDebug(base_path('storage/logs/curl.log'))
            ->$method();

        if ($response->status === 403
            || $response->status === 404
            || $response->status === 401) {
            $code = $response->content['code'] ?? '';
            return [
                'error_code' => $response->status,
                'code' => $code,
                'message' => $this->errors[$code] ?? 'Not found',
            ];
        }
//        if ($response->status === 404) {
//            throw new \Exception('Not found');
//        }

        $responseBody = $response->content;

        if (!is_array($responseBody)) {
//            throw new \RuntimeException(
//                sprintf(
//                    'Cannot decode response body. JSON error (%d): %s',
//                    json_last_error(),
//                    json_last_error_msg()
//                )
//            );
            $responseBody = null;
        }

        return $responseBody;
    }

    private function collectArrayElements($res): array
    {
        $elements = $res['elements'] ?? $res;
        $result = ['elements' => []];
        foreach ($elements as $element) {

            if (!is_array($element)) {
                $elementPayload = $this->sendRequest('GET', $element);
            } else {
                $elementPayload = $element;
            }

            if ($elementPayload) {
                $result['elements'][] = $elementPayload;
            }
        }

//        $result = array_merge($elements, $result);

        return $result;
    }

    private function getAccountByUser($userId)
    {
        return EsiaAccount::where('user_id', $userId)->first();
    }
}
