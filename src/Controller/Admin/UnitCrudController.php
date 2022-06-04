<?php

namespace App\Controller\Admin;

use App\Entity\Unit;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UnitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Unit::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('entity.unit._singular')
            ->setEntityLabelInPlural('entity.unit._plural')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'entity._id')->hideOnForm();
        yield TextField::new('name', 'entity.unit.name');
    }
}
