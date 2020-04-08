<?php

namespace App\Services\Frontend\Account;

use App\Repos\Account as AccountRepo;
use App\Services\Frontend\Service;
use App\Validators\Account as AccountValidator;
use App\Validators\Security as SecurityValidator;

class PhoneUpdate extends Service
{

    public function updatePhone()
    {
        $post = $this->request->getPost();

        $user = $this->getLoginUser();

        $accountRepo = new AccountRepo();

        $account = $accountRepo->findById($user->id);

        $accountValidator = new AccountValidator();

        $phone = $accountValidator->checkPhone($post['phone']);

        if ($phone != $account->phone) {
            $accountValidator->checkIfPhoneTaken($post['phone']);
        }

        $accountValidator->checkOriginPassword($account, $post['origin_password']);

        $securityValidator = new SecurityValidator();

        $securityValidator->checkVerifyCode($post['phone'], $post['verify_code']);

        $account->phone = $phone;

        $account->update();

        return $account;
    }

}