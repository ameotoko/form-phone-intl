<?php

/**
 * @author Andrey Vinichenko <andrey.vinichenko@gmail.com>
 */

namespace Ameotoko\PhoneIntl\Widget;

use Contao\Environment;
use Contao\FormTextField;
use Contao\Input;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @property string $initialCountry
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
            $GLOBALS['TL_CSS'][] = 'bundles/ameotokophoneintl/css/intlTelInput.min.css';
            $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/ameotokophoneintl/js/intlTelInput.min.js';

            $this->setInitialCountry();
        }

        return parent::parse($arrAttributes);
    }

    protected function getPost($strKey)
    {
        /** @var Request $request */
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        return $request->request->get($strKey . '_e164', Input::post($strKey));
    }

    protected function setInitialCountry(): void
    {
        $client = System::getContainer()->get('phoneintl.http_client');

        try {
            $response = $client->request('GET', Environment::get('ip') . '/country', [
                'base_uri' => 'https://ipinfo.io',
                'max_duration' => 5,
                'auth_bearer' => System::getContainer()->getParameter('ipinfoToken')
            ]);

            $this->initialCountry = trim($response->getContent());
        } catch (HttpExceptionInterface | TransportExceptionInterface $e) {
            $this->initialCountry = '';
        }
    }
}
