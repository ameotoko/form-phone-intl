<?php

/**
 * @author Andrey Vinichenko <andrey.vinichenko@gmail.com>
 */

namespace Ameotoko\PhoneIntl\Widget;

use Contao\Environment;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

trait PhoneIntlTrait
{
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
        if (in_array($ip = Environment::get('ip'), ['::1', '127.0.0.1'])) {
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
