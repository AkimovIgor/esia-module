<?php


namespace Modules\Esia\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Esia\Services\EsiaService;

class EsiaController extends Controller
{
    private $esiaService;

    public function __construct(Request $request)
    {
        $config = [];
        $this->esiaService = new EsiaService($request, $config);
    }

    public function getContacts()
    {
        $data = $this->esiaService->getContactsInfo();
        return response()->json($data);
    }

    public function getDocuments()
    {
        $data = $this->esiaService->getDocInfo();
        return response()->json($data);
    }

    public function getAddresses()
    {
        $data = $this->esiaService->getConfigParam('token');
        return response()->json($data);
    }

    public function getPerson()
    {
        $data = $this->esiaService->getPersonInfo();
        return response()->json($data);
    }

    public function getPersons(Request $request, $collection = null, $collectionEntityId = null, $oid = null)
    {
        $data = $this->esiaService->getPersonsInfo(
            $request,
            $oid,
            $collection,
            $collectionEntityId
        );
        return response()->json($this->getResponse($data));
    }

    public function getCategories(Request $request, $oId = null)
    {
        $data = $this->esiaService->getCategoriesInfo($request, $oId);
        return response()->json($this->getResponse($data));
    }

    public function getOrganizationCategories(Request $request, $oId)
    {
        $data = $this->esiaService->getOrganizationCategoriesInfo($request, $oId);
        return response()->json($this->getResponse($data));
    }

    public function getConfig()
    {
        $data = $this->esiaService->getConfig();
        return response()->json($data);
    }

    public function getRoles(Request $request)
    {
        $data = $this->esiaService->getRolesInfo($request);
        return response()->json($data);
    }

    public function getOrganization(
        Request $request,
        $org_id,
        $collection = null,
        $entityId = null,
        $groups = null,
        $groupId = null,
        $isSys = null
    ) {
        $data = $this->esiaService
            ->getOrganizationInfo(
                $request,
                $org_id,
                $collection,
                $entityId,
                $groups,
                $groupId,
                $isSys
            );
        return response()->json($this->getResponse($data));
    }

    public function updateOrganizationForm(Request $request, $org_id)
    {
        $data = $this->esiaService->updateOrganizationForm($request, $org_id);
        return response()->json($this->getResponse($data));
    }

    // Contacts

    public function addOrganizationContact(Request $request, $org_id)
    {
        $data = $this->esiaService->addOrganizationContact($request, $org_id);
        return response()->json($data['elements'] ?? $data);
    }

    public function updateOrganizationContact(Request $request, $org_id, $contactId)
    {
        $data = $this->esiaService->updateOrganizationContact($request, $org_id, $contactId);
        return response()->json($data['elements'] ?? $data);
    }

    public function deleteOrganizationContact(Request $request, $org_id, $contactId)
    {
        $data = $this->esiaService->deleteOrganizationContact($request, $org_id, $contactId);
        return response()->json($data['elements'] ?? $data);
    }

    // Addresses

    public function addOrganizationAddress(Request $request, $org_id)
    {
        $data = $this->esiaService->addOrganizationAddress($request, $org_id);
        return response()->json($data['elements'] ?? $data);
    }

    public function updateOrganizationAddress(Request $request, $org_id, $addressId)
    {
        $data = $this->esiaService->updateOrganizationAddress($request, $org_id, $addressId);
        return response()->json($data['elements'] ?? $data);
    }

    public function deleteOrganizationAddress(Request $request, $org_id, $addressId)
    {
        $data = $this->esiaService->deleteOrganizationAddress($request, $org_id, $addressId);
        return response()->json($data['elements'] ?? $data);
    }

    // Vehicles

    public function addOrganizationVehicle(Request $request, $org_id)
    {
        $data = $this->esiaService->addOrganizationVehicle($request, $org_id);
        return response()->json($data['elements'] ?? $data);
    }

    public function updateOrganizationVehicle(Request $request, $org_id, $vehicleId)
    {
        $data = $this->esiaService->updateOrganizationVehicle($request, $org_id, $vehicleId);
        return response()->json($data['elements'] ?? $data);
    }

    public function deleteOrganizationVehicle(Request $request, $org_id, $vehicleId)
    {
        $data = $this->esiaService->deleteOrganizationVehicle($request, $org_id, $vehicleId);
        return response()->json($data['elements'] ?? $data);
    }

    // Invites

    public function getOrganizationInvites(Request $request, $org_id)
    {
        $data = $this->esiaService->getOrganizationInvites($request, $org_id);
        return response()->json($this->getResponse($data));
    }

    public function sendOrganizationInvite(Request $request, $org_id)
    {
        $data = $this->esiaService->sendOrganizationInvite($request, $org_id);
        return response()->json($data['elements'] ?? $data);
    }

    public function deleteOrganizationInvite(Request $request, $org_id, $inviteId)
    {
        $data = $this->esiaService->deleteOrganizationInvite($request, $org_id, $inviteId);
        return response()->json($data['elements'] ?? $data);
    }

    // Employees

    public function updateOrganizationEmployee(Request $request, $org_id, $employeeId)
    {
        $data = $this->esiaService->updateOrganizationEmployee($request, $org_id, $employeeId);
        return response()->json($data['elements'] ?? $data);
    }

    public function deleteOrganizationEmployee(Request $request, $org_id, $employeeId)
    {
        $data = $this->esiaService->deleteOrganizationEmployee($request, $org_id, $employeeId);
        return response()->json($data['elements'] ?? $data);
    }

    // Branches

    public function addOrganizationBranch(Request $request, $org_id)
    {
        $data = $this->esiaService->addOrganizationBranch($request, $org_id);
        return response()->json($data['elements'] ?? $data);
    }

    public function updateOrganizationBranch(Request $request, $org_id, $branchId)
    {
        $data = $this->esiaService->updateOrganizationBranch($request, $org_id, $branchId);
        return response()->json($data['elements'] ?? $data);
    }

    // Update persons

    public function getPersonsUpdatedSince(Request $request, $type)
    {
        $data = $this->esiaService->getPersonsUpdatedSince($request, $type);
        return response()->json($data['elements'] ?? $data);
    }

    // Import user account

    public function importUserAccount(Request $request)
    {
        $data = $this->esiaService->importUserAccount($request);
        return response()->json($data['elements'] ?? $data);
    }

    public function getUserAvatar(Request $request, $avatarType = 'circle')
    {
        $data = $this->esiaService->getUserAvatar($avatarType);
        return response()->json($this->getResponse($data));
    }

    protected function getResponse($data)
    {
        if (isset($data['elements'])) {
            if (isset($data['elements'][0]) && !isset($data['elements'][1]) && is_array($data['elements'][0])) {
                return $data['elements'][0];
            }
            return $data['elements'];
        }
        return $data;
    }

}
