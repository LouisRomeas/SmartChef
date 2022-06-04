<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('entity.user._singular')
            ->setEntityLabelInPlural('entity.user._plural')
        ;
    }
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'entity._id')->hideOnForm();
        yield EmailField::new('email', 'entity.user.email');
        yield TextField::new('nickname', 'entity.user.nickname');
        yield BooleanField::new('isVerified', 'entity.user.isVerified');
        yield ChoiceField::new('roles', 'entity.user.roles')->allowMultipleChoices()->setChoices([
            'Admin' => 'ROLE_ADMIN',
            'Editor' => 'ROLE_EDITOR'
        ]);
    }
}
