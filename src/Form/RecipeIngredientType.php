<?php

namespace App\Form;

use App\Entity\RecipeIngredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\IngredientTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RecipeIngredientType extends AbstractType
{
    public function __construct(private IngredientTransformer $ingredientTransformer) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', options: [
                'label' => false
            ])
            ->add('isOptional', options: [
                'label' => 'entity.recipeIngredient.isOptional'
            ])
            ->add('ingredient', TextType::class, [
                'label' => false
            ])
        ;
        $builder->get('ingredient')->addModelTransformer($this->ingredientTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredient::class,
        ]);
    }
}
