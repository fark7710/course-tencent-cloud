<?php

namespace App\Validators;

use App\Caches\MaxPackageId as MaxPackageIdCache;
use App\Caches\Package as PackageCache;
use App\Exceptions\BadRequest as BadRequestException;
use App\Models\Package as PackageModel;
use App\Repos\Package as PackageRepo;

class Package extends Validator
{

    /**
     * @param int $id
     * @return PackageModel
     * @throws BadRequestException
     */
    public function checkPackageCache($id)
    {
        $id = intval($id);

        $maxPackageIdCache = new MaxPackageIdCache();

        $maxPackageId = $maxPackageIdCache->get();

        /**
         * 防止缓存穿透
         */
        if ($id < 1 || $id > $maxPackageId) {
            throw new BadRequestException('package.not_found');
        }

        $packageCache = new PackageCache();

        $package = $packageCache->get($id);

        if (!$package) {
            throw new BadRequestException('package.not_found');
        }

        return $package;
    }

    public function checkPackage($id)
    {
        $packageRepo = new PackageRepo();

        $package = $packageRepo->findById($id);

        if (!$package) {
            throw new BadRequestException('package.not_found');
        }

        return $package;
    }

    public function checkTitle($title)
    {
        $value = $this->filter->sanitize($title, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2) {
            throw new BadRequestException('package.title_too_short');
        }

        if ($length > 50) {
            throw new BadRequestException('package.title_too_long');
        }

        return $value;
    }

    public function checkSummary($summary)
    {
        $value = $this->filter->sanitize($summary, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length > 255) {
            throw new BadRequestException('package.summary_too_long');
        }

        return $value;
    }

    public function checkMarketPrice($price)
    {
        $value = $this->filter->sanitize($price, ['trim', 'float']);

        if ($value < 0.01 || $value > 10000) {
            throw new BadRequestException('package.invalid_market_price');
        }

        return $value;
    }

    public function checkVipPrice($price)
    {
        $value = $this->filter->sanitize($price, ['trim', 'float']);

        if ($value < 0.01 || $value > 10000) {
            throw new BadRequestException('package.invalid_vip_price');
        }

        return $value;
    }

    public function checkPublishStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('package.invalid_publish_status');
        }

        return $status;
    }

}
