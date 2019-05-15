<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class FbxPasswordType extends PasswordType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['always_empty']) {
            $view->vars['value'] = '';
        }
    }
}
