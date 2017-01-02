<?php

namespace lewiscowles\core\validation;

use lewiscowles\core\validation\ValidatorInterface;

class StringValidator implements ValidatorInterface {
    public function validate($input) : bool {
        return \is_string($input);
    }
}
