<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidationTrait {

    public $errors;

    public function validate($data) : bool {
        $Reflection = new \ReflectionClass(__CLASS__);
        $ReflectionClass = $Reflection->newInstance();

        if (empty($ReflectionClass->rules)) return TRUE;

        $v = Validator::make($data, $ReflectionClass->rules);

        if ($v->fails()) {
            // TODO customise error messages for failures
            $this->errors = $v->failed();

            return FALSE;
        }

        return TRUE;
    }

    public function validationErrors() {

        return $this->errors;
    }
}
