<?php

/**
 * @author Andrey Vinichenko <andrey.vinichenko@gmail.com>
 */

namespace Ameotoko\PhoneIntl\Widget;

/**
 * @property string $initialCountry
 * @property bool $lookupCountry
 * @property string $ipinfoToken
 * @property null|string $preferredCountries
 * @property string $countryListType
 * @property string $countryList
 */
if (class_exists(\Contao\FormTextField::class)) {
    class FormPhoneIntl extends \Contao\FormTextField
    {
        use PhoneIntlTrait;
        protected $strTemplate = 'form_phone_intl';
    }
} else {
    class FormPhoneIntl extends \Contao\FormText
    {
        use PhoneIntlTrait;
        protected $strTemplate = 'form_phone_intl';
    }
}
