<?php

namespace App\Services\Api;

use App\Contracts\Repositories\DepartmentRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Services\Api\UserServiceInterface;
use App\Services\AbstractService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Exceptions\CheckAuthorizationException;
use Illuminate\Support\Facades\Redis;

class UserService extends AbstractService implements UserServiceInterface
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;
    protected $departmentRepository;
    protected $roleRepository;
    protected $categoryRepository;
    protected $requestRepository;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        DepartmentRepositoryInterface $departmentRepository,
        RoleRepositoryInterface $roleRepository,
        CategoryRepositoryInterface $categoryRepository,
        RequestRepositoryInterface $requestRepository
    ) {
        $this->userRepository = $userRepository;
        $this->departmentRepository = $departmentRepository;
        $this->roleRepository = $roleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->requestRepository = $requestRepository;
    }

    /**
     * @param $params
     * @return array
     */
    public function index($perPage, $name)
    {
        if ($name != "") {
            return $this->userRepository->search($name)->orderby('id', 'DESC')->paginate($perPage);
        }
        return $this->userRepository->getColumns(['*'], ['role:id,name', 'department:id,name'])
            ->orderby('id', 'DESC')->paginate($perPage);
    }

    public function create($params)
    {
        $params['password'] = Hash::make(str_random(8));
        if ($this->test($params['role_id'], $params['department_id'], $params['status'])) {
            return $this->test($params['role_id'], $params['department_id'], $params['status']);
        }
        if ($params['role_id'] == Config::get('constants.GET_ROLE_ID.Manager')) {
            $this->userRepository->changeRoleIdManagerToUser($params);
        }
        return [
            'code' => 200,
            'user' => $this->userRepository->store($params),
            'message' => 'Thêm User thành công'
        ];
    }

    public function login($params)
    {
        return $this->userRepository->login($params);
    }

    public function listUser()
    {
        return $this->userRepository->getColumns()->get();
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => 'error !'];
        }
        return ['code' => 200, 'mesage' => 'logout success !'];
    }

    public function listAdmin()
    {
        return $this->userRepository->listAdmin()->orderby('id', 'DESC')->get();
    }

    public function update($id, $params)
    {
        Redis::flushDB();
        $idRoleAdmin = config('constants.GET_ROLE_ID.Admin');
        $idRoleManager = config('constants.GET_ROLE_ID.Manager');
        $idRoleUser = config('constants.GET_ROLE_ID.User');
        $statusUserDisable = config('constants.statusUser.disable');
        $user = $this->userRepository->find($id);
        $tam = 0;
        if ($user->role_id == $idRoleManager) {
            $tam = Config::get('constants.updateUser.manager');
        } elseif ($user->role_id == $idRoleAdmin && $params['role_id'] != $idRoleAdmin) {
            $tam = Config::get('constants.updateUser.admin');
        } elseif ($user->role_id == $idRoleAdmin && $params['status'] == $statusUserDisable) {
            $tam = Config::get('constants.updateUser.disable');
        } elseif ($user->role_id == $idRoleUser) {
            $tam = Config::get('constants.updateUser.user');
        }

        switch ($tam) {
            case Config::get('constants.updateUser.manager'):
                return $this->updateManager($params, $idRoleManager, $user);
                break;
            case Config::get('constants.updateUser.admin'):
                return $this->getMessage('Đang là Admin không được xuống quyền !', 403);
                break;
            case Config::get('constants.updateUser.disable'):
                return $this->checkDisableUser($params, $user);
                break;
            case Config::get('constants.updateUser.user'):
                return $this->roleUser($params, $idRoleManager, $user);
                break;
            default:
                return $this->upadateDefaut($params, $user);
                break;
        }
    }

    public function getMessage($message, $code)
    {
        return [
            'code' => $code,
            'message' => $message
        ];
    }

    public function searchIdUpdate($key)
    {
        return $this->userRepository->searchIdUpdate($key)->first();
    }

    public function getListRoleUpdate($user)
    {
        $roleId = $user->role->id;
        $roleName = $user->role->name;
        $rolesObject = $this->roleRepository->getColumns()->get();
        $roles = [];
        $roles[$roleId] = $roleName;
        foreach ($rolesObject as $roleObject) {
            $roles[$roleObject->id] = $roleObject->name;
        }
        return $roles;
    }

    public function getListDepartmentUpdate($user)
    {
        $departmentId = $user->department->id;
        $departmentName = $user->department->name;
        $departmentsObject = $this->departmentRepository->getColumns()->get();
        $departments = [];
        $departments[$departmentId] = $departmentName;
        foreach ($departmentsObject as $departmentObject) {
            $departments[$departmentObject->id] = $departmentObject->name;
        }
        return $departments;
    }

    public function show($id)
    {
        return $this->userRepository->find($id);
    }

    public function getInfo()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $role = $this->roleRepository->find($user->role_id);
        return [
            'user' => $user,
            'role' => $role->name
        ];
    }

    public function loginGoogle($access_token)
    {
        $linkGetInfoUser = Config::get('constants.LINK_GET_INFO_USER_GOOGLE');
        try {
            $userGoogle = json_decode(file_get_contents($linkGetInfoUser . $access_token));
        } catch (\Throwable $th) {
            throw new CheckAuthorizationException('Access token expired.');
        }
        $emailTail = strstr($userGoogle->email, "@", false);
        if ($emailTail == config('constants.DEFAULT_TAIL_EMAIL')) {
            $user = $this->userRepository->findWhereEmail($userGoogle->email)->first();
            if ($user && $user->status == config('constants.statusUser.enable')) {
                $access_token = JWTAuth::fromUser($user);
                return [
                    'code' => 200,
                    'message' => 'Đăng nhập thành công !',
                    'access_token' => $access_token
                ];
            }
            return [
                'code' => 403,
                'message' => 'Tài khoản không tồn tại !'
            ];
        }
        return [
            'code' => 403,
            'message' => 'Không được phép đăng nhập !'
        ];
    }


    public function test($role_id, $department_id, $status)
    {
        $idDepartmentHcns = config('constants.GET_DEPARTMENT_ID.HCNS');
        $idRoleAdmin = config('constants.GET_ROLE_ID.Admin');
        $idRoleManager = config('constants.GET_ROLE_ID.Manager');
        $idRoleUser = config('constants.GET_ROLE_ID.User');
        if ($role_id == $idRoleAdmin && $department_id != $idDepartmentHcns) {
            return $this->getMessage('Là admin thì chỉ ở phòng HCNS !', 403);
        }
        if ($role_id == $idRoleManager && $department_id == $idDepartmentHcns) {
            return $this->getMessage('Là Quản lý thì không được ở phòng HCNS !', 403);
        }
        if ($role_id == $idRoleUser && $department_id == $idDepartmentHcns) {
            return $this->getMessage('Là User thì không được ở phòng HCNS !', 403);
        }
        if ($role_id == $idRoleManager && $status == config('constants.statusUser.disable')) {
            return $this->getMessage('Là Quản lý thì không được nghỉ việc !', 403);
        }
    }

    public function userCategory($categorys, $user, $params)
    {
        $idCategories = [$categorys];
        $nameCategory = [$this->categoryRepository->nameCategory($idCategories)];
        $this->categoryRepository->changeUserIdCategory($idCategories);
        $user = $this->userRepository->update($user, $params);
        return [
            'code' => 200,
            'user' => $user,
            'mess' => ['categories' => $nameCategory],
        ];
    }

    public function userRequest($requests, $user, $params)
    {
        $idRequests = [$requests];
        $nameRequests = [$this->requestRepository->nameRequest($idRequests)];
        $this->requestRepository->changeUserIdRequest($idRequests);
        $user = $this->userRepository->update($user, $params);
        return [
            'code' => 200,
            'user' => $user,
            'mess' => ['requests' => $nameRequests],
        ];
    }

    public function userRequestCategory($requests, $categorys, $user, $params)
    {
        $idRequests = [$requests];
        $nameRequests = [$this->requestRepository->nameRequest($idRequests)];
        $this->requestRepository->changeUserIdRequest($idRequests);
        $idCategories = [$categorys];
        $nameCategory = [$this->categoryRepository->nameCategory($idCategories)];
        $this->categoryRepository->changeUserIdCategory($idCategories);
        $user = $this->userRepository->update($user, $params);
        return [
            'code' => 200,
            'user' => $user,
            'mess' => ['categories' => $nameCategory, 'requests' => $nameRequests],
        ];
    }

    public function checkDisableUser($params, $user)
    {
        if ($this->test($params['role_id'], $params['department_id'], $params['status'])) {
            return $this->test($params['role_id'], $params['department_id'], $params['status']);
        }
        $categorys = $this->categoryRepository->findWhereUserId($user->id);
        $requests = $this->requestRepository->findWhereUserIdRequest($user->id);
        if (!empty($categorys) && empty($requests)) {
            return $this->userCategory($categorys, $user, $params);
        } elseif (!empty($categorys) && !empty($requests)) {
            return $this->userRequestCategory($requests, $categorys, $user, $params);
        } elseif (empty($categorys) && !empty($requests)) {
            return $this->userRequest($requests, $user, $params);
        } else {
            return [
                'code' => 200,
                'user' => $this->userRepository->update($user, $params)
            ];
        }
    }

    public function roleUser($params, $idRoleManager, $user)
    {
        if ($this->test($params['role_id'], $params['department_id'], $params['status'])) {
            return $this->test($params['role_id'], $params['department_id'], $params['status']);
        }
        if ($params['role_id'] == $idRoleManager) {
            return $this->changeRoleIdManagerToUser($params, $user);
        }
        return [
            'code' => 200,
            'user' => $this->userRepository->update($user, $params)
        ];
    }

    public function upadateDefaut($params, $user)
    {
        if ($this->test($params['role_id'], $params['department_id'], $params['status'])) {
            return $this->test($params['role_id'], $params['department_id'], $params['status']);
        }
        return [
            'code' => 200,
            'user' => $this->userRepository->update($user, $params)
        ];
    }

    public function updateManager($params, $idRoleManager, $user)
    {
        if ($params['role_id'] != $idRoleManager) {
            return $this->getMessage('Đang là quản lý không được di chuyển !', 403);
        }
        if ($params['status'] == config('constants.statusUser.disable')) {
            return $this->getMessage('Là Quản lý thì không được nghỉ việc !', 403);
        }
        if ($params['department_id'] != $user->department_id) {
            return $this->changeRoleIdManagerToUser($params, $user);
        }
    }

    public function changeRoleIdManagerToUser($params, $user)
    {
        $this->userRepository->changeRoleIdManagerToUser($params);
        return [
            'code' => 200,
            'user' => $this->userRepository->update($user, $params)
        ];
    }
}
