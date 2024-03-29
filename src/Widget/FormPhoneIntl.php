<?php

/**
 * @author Andrey Vinichenko <andrey.vinichenko@gmail.com>
 */

namespace Ameotoko\PhoneIntl\Widget;

use Contao\Environment;
use Contao\FormTextField;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @property string $initialCountry
 * @property bool $lookupCountry
 * @property string $ipinfoToken
 * @property null|string $preferredCountries
 * @property string $countryListType
 * @property string $countryList
 */
class FormPhoneIntl extends FormTextField
{
    protected $strTemplate = 'form_phone_intl';

    public function __get($strKey)
    {
        if ('type' == $strKey) {
            return 'tel';
        }

        return parent::__get($strKey);
    }

    public function parse($arrAttributes = null): string
    {
        $container = System::getContainer();
        $request = $container->get('request_stack');

        if ($container->get('contao.routing.scope_matcher')->isFrontendRequest($request->getCurrentRequest())) {
            $assets = $container->get('assets.packages');

            $GLOBALS['TL_CSS'][] = $assets->getUrl('css/intlTelInput.min.css', 'ameotoko_phone_intl');
            $GLOBALS['TL_JAVASCRIPT'][] = $assets->getUrl('js/intlTelInput.min.js', 'ameotoko_phone_intl');

            $this->utilsScript = $assets->getUrl('js/utils.js', 'ameotoko_phone_intl');

            if ($this->lookupCountry) {
                $this->initialCountry = $this->getInitialCountry();
            }

            if (null !== $this->preferredCountries) {
                $this->preferred = json_encode(StringUtil::deserialize($this->preferredCountries, true));
            }

            if ('all' != $this->countryListType) {
                $this->countries = json_encode(StringUtil::deserialize($this->countryList, true));
            }
        }

        return parent::parse($arrAttributes);
    }

    protected function getPost($strKey)
    {
        /** @var Request $request */
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        return $request->request->get($strKey . '_e164', Input::post($strKey));
    }

    protected function getInitialCountry(): string
    {
        // Skip API calls for localhost
        if ('::1' == ($ip = Environment::get('ip'))) {
            return '';
        }

        $client = System::getContainer()->get('phoneintl.http_client');

        try {
            $response = $client->request('GET', $ip . '/country', [
                'base_uri' => 'https://ipinfo.io',
                'max_duration' => 5,
                'auth_bearer' => $this->ipinfoToken
            ]);

            return trim($response->getContent());
        } catch (HttpExceptionInterface | TransportExceptionInterface $e) {
            return '';
        }
    }
}
