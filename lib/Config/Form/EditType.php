<?php

namespace Netgen\BlockManager\Config\Form;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditType extends AbstractType
{
    /**
     * @var string[]
     */
    protected $enabledConfigs = array();

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('configurable', 'configType', 'configKeys'));

        $resolver->setAllowedTypes('configType', 'string');
        $resolver->setAllowedTypes('configKeys', array('string', 'array', 'null'));
        $resolver->setAllowedTypes('configurable', ConfigAwareValue::class);
        $resolver->setAllowedTypes('data', ConfigAwareStruct::class);

        $resolver->setDefault('configKeys', null);
        $resolver->setDefault('constraints', function (Options $options) {
            return array(
                new ConfigAwareStructConstraint(
                    array(
                        'payload' => $options['configurable'],
                    )
                ),
            );
        });
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\API\Values\Config\ConfigAwareValue $configAwareValue */
        $configAwareValue = $options['configurable'];
        $configs = $configAwareValue->getConfigs();

        /** @var \Netgen\BlockManager\API\Values\Config\ConfigStruct[] $configStructs */
        $configStructs = $options['data']->getConfigStructs();

        $configKeys = $options['configKeys'];
        if ($configKeys === null) {
            $configKeys = array_keys($configStructs);
        } elseif (is_string($configKeys)) {
            $configKeys = array($configKeys);
        }

        foreach ($configKeys as $configKey) {
            if (!isset($configs[$configKey])) {
                continue;
            }

            $configDefinition = $configs[$configKey]->getDefinition();
            if (!$configDefinition->isEnabled($configAwareValue)) {
                continue;
            }

            $this->enabledConfigs[$configKey] = $configs[$configKey];

            $builder->add(
                $configKey,
                ParametersType::class,
                array(
                    'data' => $configStructs[$configKey],
                    'property_path' => 'configStructs[' . $configKey . ']',
                    'parameter_collection' => $configDefinition,
                    'label_prefix' => 'config.' . $options['configType'] . '.' . $configKey,
                )
            );
        }
    }

    /**
     * Builds the form view.
     *
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['enabled_configs'] = $this->enabledConfigs;
        $view->vars['configurable'] = $options['configurable'];
    }
}
