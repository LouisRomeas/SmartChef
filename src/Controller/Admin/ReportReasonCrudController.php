<?php

namespace App\Controller\Admin;

use App\Entity\ReportReason;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ReportReasonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ReportReason::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
    }
}
