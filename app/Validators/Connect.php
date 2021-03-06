<?php

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Repos\Connect as ConnectRepo;

class Connect extends Validator
{

    public function checkConnect($id)
    {
        return $this->checkConnectById($id);
    }

    public function checkConnectById($id)
    {
        $connectRepo = new ConnectRepo();

        $connect = $connectRepo->findById($id);

        if (!$connect) {
            throw new BadRequestException('connect.not_found');
        }

        return $connect;
    }

    public function checkConnectByOpenId($openId, $provider)
    {
        $connectRepo = new ConnectRepo();

        $connect = $connectRepo->findByOpenId($openId, $provider);

        if (!$connect) {
            throw new BadRequestException('connect.not_found');
        }

        return $connect;
    }
}
