<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class CreateType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    public function __construct(LayoutTypeRegistryInterface $layoutTypeRegistry)
    {
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setAllowedTypes('data', LayoutCreateStruct::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'layoutType',
            ChoiceType::class,
            [
                'label' => 'layout.type',
                'required' => true,
                'choices' => $this->layoutTypeRegistry->getLayoutTypes(true),
                'choice_value' => 'identifier',
                'choice_name' => 'identifier',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'expanded' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
                'property_path' => 'layoutType',
            ]
        );

        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'layout.name',
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                    new LayoutName(),
                ],
                'property_path' => 'name',
            ]
        );

        $builder->add(
            'description',
            TextareaType::class,
            [
                'label' => 'layout.description',
                'required' => false,
                'constraints' => [
                    new Constraints\Type(['type' => 'string']),
                ],
                'property_path' => 'description',
            ]
        );

        $builder->add(
            'shared',
            CheckboxType::class,
            [
                'label' => 'layout.shared',
                'constraints' => [
                    new Constraints\NotNull(),
                ],
                'property_path' => 'shared',
            ]
        );
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var \Netgen\BlockManager\Layout\Type\LayoutTypeInterface $layoutType */
        foreach ($this->layoutTypeRegistry->getLayoutTypes(true) as $layoutType) {
            $view['layoutType'][$layoutType->getIdentifier()]->vars['layout_type'] = $layoutType;
        }
    }
}
