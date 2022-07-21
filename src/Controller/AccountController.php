<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Form\PasswordChangeFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/{_locale}/account', name: 'app_account', requirements:[ '_locale' => '%app.locales%' ])]
    public function index(
        Request $request,
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepository,
        TranslatorInterface $translator,
        MailerInterface $mailer
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $passwordChangeForm = $this->createForm(PasswordChangeFormType::class, $user);

        $passwordChangeForm->handleRequest($request);

        if (
            $passwordChangeForm->isSubmitted() &&
            $passwordChangeForm->isValid()
        ) {
            if ($hasher->isPasswordValid($user, $passwordChangeForm->get('oldPlainPassword')->getData())) {
                $user->setPassword(
                    $hasher->hashPassword(
                            $user,
                            $passwordChangeForm->get('newPlainPassword')->getData()
                        )
                    );
    
                $userRepository->add($user, true);

                // Send an e-mail to warn the user in case the password change was initiated without their consent
                $email = (new TemplatedEmail())
                    ->from(new Address($_ENV['admin_mail'], 'SmartChef'))
                    ->to($user->getEmail())
                    ->subject($translator->trans('form.passwordChangeForm.passwordChanged'))
                    ->htmlTemplate('account/password_change_email.html.twig')
                    ->context([
                        'user' => $user
                    ])
                ;

                $mailer->send($email);
            } else $this->addFlash('form-error', 'messages.user.errors.currentPasswordIncorrect');
        }

        return $this->renderForm('account/index.html.twig', [
            'user' => $user,
            'passwordChangeForm' => $passwordChangeForm
        ]);
    }
}
