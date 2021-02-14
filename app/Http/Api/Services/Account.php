<?php

namespace App\Http\Api\Services;

use App\Repos\User as UserRepo;
use App\Services\Auth\Api as AuthService;
use App\Services\Logic\Account\Register as RegisterService;
use App\Validators\Account as AccountValidator;

class Account extends Service
{

    /**
     * @var AuthService
     */
    protected $auth;

    public function __construct()
    {
        $this->auth = $this->getDI()->get('auth');
    }

    public function register()
    {
        $service = new RegisterService();

        $account = $service->handle();

        $userRepo = new UserRepo();

        $user = $userRepo->findById($account->id);

        $token = $this->auth->saveAuthInfo($user);

        $this->eventsManager->fire('Account:afterRegister', $this, $user);

        return $token;
    }

    public function loginByPassword()
    {
        $post = $this->request->getPost();

        /**
         * 使用[account|phone|email]做账户名字段兼容
         */
        if (isset($post['phone'])) {
            $post['account'] = $post['phone'];
        } elseif (isset($post['email'])) {
            $post['account'] = $post['email'];
        }

        $validator = new AccountValidator();

        $user = $validator->checkUserLogin($post['account'], $post['password']);

        $token = $this->auth->saveAuthInfo($user);

        $this->eventsManager->fire('Account:afterLogin', $this, $user);

        return $token;
    }

    public function loginByVerify()
    {
        $post = $this->request->getPost();

        /**
         * 使用[account|phone|email]做账户名字段兼容
         */
        if (isset($post['phone'])) {
            $post['account'] = $post['phone'];
        } elseif (isset($post['email'])) {
            $post['account'] = $post['email'];
        }

        $validator = new AccountValidator();

        $user = $validator->checkVerifyLogin($post['account'], $post['verify_code']);

        $token = $this->auth->saveAuthInfo($user);

        $this->eventsManager->fire('Account:afterLogin', $this, $user);

        return $token;
    }

    public function logout()
    {
        $user = $this->getLoginUser();

        $this->auth->clearAuthInfo();

        $this->eventsManager->fire('Account:afterLogout', $this, $user);
    }

}
