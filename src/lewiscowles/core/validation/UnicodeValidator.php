<?php

namespace lewiscowles\core\validation;

use lewiscowles\core\validation\ValidatorInterface;

class UnicodeValidator implements ValidatorInterface {
    public function validate($input) : bool {
        return ( $input == 0x9 || $input == 0xa || $input == 0xd ||
			    ($input >= 0x20 && $input <= 0xd7ff) ||
			    ($input >= 0xe000 && $input <= 0xfffd) ||
			    ($input >= 0x10000 && $input <= 0x10ffff) );
    }
}
