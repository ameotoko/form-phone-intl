<?php

/**
 * @author Andrey Vinichenko <andrey.vinichenko@gmail.com>
 */

namespace Ameotoko\PhoneIntl;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AmeotokoPhoneIntlBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
