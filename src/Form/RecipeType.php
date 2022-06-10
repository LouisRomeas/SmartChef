<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Form\RecipeIngredientType;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RecipeType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator) {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $messages = [
            'maxSizeMessage',
            'mimeTypesMessage',
        ];
        $messagesPaths = [];
        foreach ($messages as $message) {
            $messagesPaths[$message] = 'forms.constraints.imageUrl.' . $message;
        }

        $builder
            ->add('title')
            ->add('body', CKEditorType::class)
            ->add('portions')
            ->add('duration', null, [
                'widget' => 'single_text'
            ])
            ->add('imageUrl', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(array_merge([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*'
                        ]
                    ], $messagesPaths))
                ],
            ])
            ->add('recipeIngredients', CollectionType::class, [
                'entry_type' => RecipeIngredientType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
