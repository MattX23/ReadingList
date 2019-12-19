<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidationTrait {

    /**
     * @var
     */
    public $errors;

    /**
     * @param $data
     *
     * @return bool
     * @throws \ReflectionException
     */
    public function validate($data) : bool {
        $Reflection = new \ReflectionClass(__CLASS__);
        $ReflectionClass = $Reflection->newInstance();

        if (empty($ReflectionClass->rules)) return true;

        $v = Validator::make($data, $ReflectionClass->rules);

        if ($v->fails()) {
            $this->errors = $v->errors()->first();

            return false;
        }

        return true;
    }

    public function validationErrors() {

        return $this->errors;
    }
}
