<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class RecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'entity.recipe.title'),
            TextEditorField::new('body', 'entity.recipe.body'),
            IntegerField::new('portions', 'entity.recipe.portions'),
            TimeField::new('duration', 'entity.recipe.duration'),
            ImageField::new('imageUrl', 'entity.recipe.thumbnail')->setUploadDir('public/upload/')->setBasePath('upload'),
            AssociationField::new('author', 'entity.recipe.author'),
            AssociationField::new('reports', 'entity.report._plural')
        ];
    }
}
