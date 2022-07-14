<?php

/**
 * @author Andrey Vinichenko <andrey.vinichenko@gmail.com>
 */

use Contao\CoreBundle\Intl\Countries;
use Contao\System;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Types\Types;

$GLOBALS['TL_DCA']['tl_form_field']['palettes']['phone_intl'] = '{type_legend},type,name,label;{fconfig_legend},mandatory,placeholder;{phonecountry_legend},lookupCountry,preferredCountries;{expert_legend:hide},class,value,accesskey,tabindex;{template_legend:hide},customTpl;{invisible_legend:hide},invisible';

$GLOBALS['TL_DCA']['tl_form_field']['palettes']['__selector__'][] = 'lookupCountry';
$GLOBALS['TL_DCA']['tl_form_field']['subpalettes']['lookupCountry'] = 'ipinfoToken';

$GLOBALS['TL_DCA']['tl_form_field']['fields']['lookupCountry'] = [
    'inputType' => 'checkbox',
    'exclude' => true,
    'search' => false,
    'filter' => false,
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) COLLATE ascii_bin NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_form_field']['fields']['ipinfoToken'] = [
    'inputType' => 'text',
    'exclude' => true,
    'search' => false,
    'filter' => false,
    'eval' => ['tl_class' => 'w50 clr', 'mandatory' => true],
    'sql' => ['type' => Types::STRING, 'length' => 64, 'default' => '']
];

$GLOBALS['TL_DCA']['tl_form_field']['fields']['preferredCountries'] = [
    'inputType' => 'select',
    'exclude' => true,
    'search' => false,
    'filter' => false,
    'options_callback' => static function() {
        return System::getContainer()->get(Countries::class)->getCountries();
    },
    'eval' => ['tl_class' => 'w50 clr', 'multiple' => true, 'chosen' => true],
    'sql' => ['type' => Types::TEXT, 'length' => AbstractMySQLPlatform::LENGTH_LIMIT_TEXT, 'notnull' => false]
];
