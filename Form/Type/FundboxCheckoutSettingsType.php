<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Fundbox\Bundle\FundboxCheckoutBundle\Entity\FundboxCheckoutSettings;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Oro\Bundle\SecurityBundle\Form\DataTransformer\Factory\CryptedDataTransformerFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FundboxCheckoutSettingsType extends AbstractType
{   
    const BLOCK_PREFIX = 'fbx_setting_setting_type';

    /**
     * @var CryptedDataTransformerFactory
     */
    protected $cryptedDataTransformerFactory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param CryptedDataTransformerFactory $cryptedDataTransformerFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        CryptedDataTransformerFactory $cryptedDataTransformerFactory,
        TranslatorInterface $translator
    ) {
        $this->cryptedDataTransformerFactory = $cryptedDataTransformerFactory;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'labels',
                LocalizedFallbackValueCollectionType::class,
                [
                    'label' => 'fundbox.checkout.settings.labels.label',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                    'client_validation' => true,
                    'attr' => array('readonly' => true),
                ]
            )
            ->add(
                'shortLabels',
                LocalizedFallbackValueCollectionType::class,
                [
                    'label' => 'fundbox.checkout.settings.short_labels.label',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                    'client_validation' => true,
                    'attr' => array('readonly' => true),
                ]
            )
            ->add(
                'environment',
                ChoiceType::class,
                [
                    'choices' => ['testing', 'production'],
                    'choices_as_values' => true,
                    'choice_label' => function ($action) {
                        return $this->translator->trans(
                            sprintf('fundbox.checkout.settings.environment.types.%s', $action)
                        );
                    },
                    'label' => 'fundbox.checkout.settings.environment.label',
                    'tooltip' => 'fundbox.checkout.settings.environment.tooltip',
                    'required' => true,
                ]
            )
            ->add($this->createCryptedField(
                $builder, 'productionPublicKey', TextType::class, [
                    'label' => 'fundbox.checkout.settings.production_public_key.label',
                    'tooltip' => 'fundbox.checkout.settings.production_public_key.tooltip',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
            )
            ->add($this->createCryptedField(
                $builder, 'productionPrivateKey', FbxPasswordType::class, [
                    'label' => 'fundbox.checkout.settings.production_private_key.label',
                    'tooltip' => 'fundbox.checkout.settings.production_private_key.tooltip',
                    'required' => true,
                    'always_empty' => false,
                    'constraints' => [new NotBlank()],
                ])
            )
            ->add($this->createCryptedField(
                $builder, 'testPublicKey', TextType::class, [
                    'label' => 'fundbox.checkout.settings.test_public_key.label',
                    'tooltip' => 'fundbox.checkout.settings.test_public_key.tooltip',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
            )
            ->add($this->createCryptedField(
                $builder, 'testPrivateKey', FbxPasswordType::class, [
                    'label' => 'fundbox.checkout.settings.test_private_key.label',
                    'tooltip' => 'fundbox.checkout.settings.test_private_key.tooltip',
                    'required' => true,
                    'always_empty' => false,
                    'constraints' => [new NotBlank()],
                ])
            )
            ->add(
                'logEnabled',
                CheckboxType::class,
                [
                    'label' => 'fundbox.checkout.settings.log_enabled.label',
                    'tooltip' => 'fundbox.checkout.settings.log_enabled.tooltip',
                    'required' => false,
                ]
            )
            ->add(
                'paymentAction',
                ChoiceType::class,
                [
                    'choices' => ['capture', 'authorize'],
                    'choices_as_values' => true,
                    'choice_label' => function ($action) {
                        return $this->translator->trans(
                            sprintf('fundbox.checkout.settings.payment_action.types.%s', $action)
                        );
                    },
                    'label' => 'fundbox.checkout.settings.payment_action.label',
                    'tooltip' => 'fundbox.checkout.settings.payment_action.tooltip',
                    'required' => true,
                ]
            )
            ->add(
                'minimumOrder',
                IntegerType::class,
                [
                    'label' => 'fundbox.checkout.settings.minimum_order.label',
                    'tooltip' => 'fundbox.checkout.settings.minimum_order.tooltip',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ]
            )
            ->add(
                'maximumOrder',
                IntegerType::class,
                [
                    'label' => 'fundbox.checkout.settings.maximum_order.label',
                    'tooltip' => 'fundbox.checkout.settings.maximum_order.tooltip',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ]
            )
            ->addEventListener(FormEvents::POST_SET_DATA, [$this, 'postSetData']);
    }

    protected function createCryptedField(
        FormBuilderInterface $builder,
        $name, $type, $options) {
        $clientIdFieldBuilder = $builder->create($name, $type, $options);
        $clientIdFieldBuilder->addModelTransformer($this->cryptedDataTransformerFactory->create());
        return $clientIdFieldBuilder;
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        /** @var FundboxCheckoutSettings $data */
        $form = $event->getForm();
        if ($form->has('labels')) {
            $defaultValue = new LocalizedFallbackValue();
            $defaultValue->setString('Fundbox Credit (Free Net Terms)');
            $form->get('labels')->setData(new ArrayCollection([$defaultValue])); // Set default labels and short labels
        }
        if ($form->has('shortLabels')) {
            $defaultValue = new LocalizedFallbackValue();
            $defaultValue->setString('Fundbox Credit');
            $form->get('shortLabels')->setData(new ArrayCollection([$defaultValue]));
        }

        $data = $event->getData();
        if (!$data || !$data->getId()) {
            if ($form->has('minimumOrder')) {
                $form->get('minimumOrder')->setData(10);
            }
            if ($form->has('maximumOrder')) {
                $form->get('maximumOrder')->setData(100000);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => FundboxCheckoutSettings::class,
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }

}
