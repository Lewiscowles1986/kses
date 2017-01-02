<?php

namespace lewiscowles\core\validation;

interface ValidatorInterface {
    public function validate($input) : bool;
}
