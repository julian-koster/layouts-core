<?php

namespace Netgen\BlockManager\Tests\Form;

use Netgen\BlockManager\Form\KeyValuesType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class KeyValuesTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new KeyValuesType();
    }

    /**
     * @covers \Netgen\BlockManager\Form\KeyValuesType::buildForm
     * @covers \Netgen\BlockManager\Form\KeyValuesType::buildView
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'some_key' => 'key_value',
            'some_value' => array('value1', 'value2'),
        );

        $form = $this->factory->create(
            KeyValuesType::class,
            null,
            array(
                'key_name' => 'some_key',
                'key_label' => 'Key',
                'values_name' => 'some_value',
                'values_label' => 'Value',
                'values_type' => TextType::class,
                'values_constraints' => array(
                    new Constraints\NotBlank(),
                ),
            )
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($submittedData, $form->getData());

        $this->assertEquals('Key', $form->get('some_key')->getConfig()->getOption('label'));
        $this->assertEquals('Value', $form->get('some_value')->getConfig()->getOption('label'));

        $this->assertInstanceOf(
            CollectionType::class,
            $form->get('some_value')->getConfig()->getType()->getInnerType()
        );

        $view = $form->createView();

        $this->assertArrayHasKey('key_name', $view->vars);
        $this->assertArrayHasKey('values_name', $view->vars);

        $this->assertEquals('some_key', $view->vars['key_name']);
        $this->assertEquals('some_value', $view->vars['values_name']);

        $this->assertArrayHasKey('some_key', $view->children);
        $this->assertArrayHasKey('some_value', $view->children);
    }

    /**
     * @covers \Netgen\BlockManager\Form\KeyValuesType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = array(
            'key_name' => 'some_key',
            'key_label' => 'some_key_label',
            'values_name' => 'some_values',
            'values_label' => 'some_values_label',
            'values_type' => 'some_type',
            'values_constraints' => array('constraint'),
        );

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertEquals('some_key', $resolvedOptions['key_name']);
        $this->assertEquals('some_key_label', $resolvedOptions['key_label']);
        $this->assertEquals('some_values', $resolvedOptions['values_name']);
        $this->assertEquals('some_values_label', $resolvedOptions['values_label']);
        $this->assertEquals('some_type', $resolvedOptions['values_type']);
        $this->assertEquals(array('constraint'), $resolvedOptions['values_constraints']);
    }

    /**
     * @covers \Netgen\BlockManager\Form\KeyValuesType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ngbm_key_values', $this->formType->getBlockPrefix());
    }
}
