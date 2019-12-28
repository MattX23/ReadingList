<?php

namespace App\Traits;

trait AuthorizeSoftDeletesTrait {

    /**
     * @param string $class
     * @param int    $objectId
     * @param string $policyMethod
     * @param bool   $retrieve
     *
     * @return mixed
     */
    protected function authorizeSoftDeletedModel(string $class, int $objectId, string $policyMethod, bool $retrieve = false)
    {
        $object = (new $class)->withTrashed()->find($objectId);

        $this->authorize($policyMethod, $object);

        if ($retrieve) return $object;
    }
}
