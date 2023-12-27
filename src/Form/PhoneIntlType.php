<?php

/**
 * @author Andrey Vinichenko <andrey.vinichenko@gmail.com>
 */

namespace Ameotoko\PhoneIntl\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneIntlType extends AbstractType
{
    private string $suffix = '_e164';

    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getParent(): string
    {
        return TelType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $builder->add($builder->getName() . $this->suffix, HiddenType::class);

        // $builder->addEventListener(
        //     FormEvents::PRE_SUBMIT,
        //     [$this, 'onPreSubmit']
        // );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // $resolver->setDefault('compound', true);
        $resolver->setDefault('allow_extra_fields', true);
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $formName = $event->getForm()->getRoot()->getName();
        $hiddenName = $event->getForm()->getName() . $this->suffix;
        $request = $this->requestStack->getCurrentRequest();

        $submittedData = ('' === $formName) ? $request->request->all() : $request->request->all()[$formName];

        $event->setData([$hiddenName => $submittedData[$hiddenName]]);

        unset($submittedData[$hiddenName]);

        if ('' === $formName) {
            $request->request->replace($submittedData);
        } else {
            $request->request->set($formName, $submittedData);
        }
    }
}
