<?php

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class IngredientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ingredient::class;
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('entity.ingredient._singular')
            ->setEntityLabelInPlural('entity.ingredient._plural')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'entity._id')->hideOnForm();
        yield TextField::new('name', 'entity.ingredient.name');
        yield TextField::new('emoji', 'entity.ingredient.emoji')->setMaxLength(1);
        yield AssociationField::new('unit', 'entity.unit._singular');
        yield AssociationField::new('category', 'entity.category._singular');
    }
}
