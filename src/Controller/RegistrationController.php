<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private TranslatorInterface $translator
    ) {}

    /**
     * Registration page
     */
    #[Route('/{_locale}/register', name: 'app_register', requirements:[ '_locale' => '%app.locales%' ])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        SessionInterface $session
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$userRepository->findBy([
                'nickname' => $form->get('nickname')->getData()
            ]) || true) {
                // encode the plain password
                $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
    
                $entityManager->persist($user);
                $entityManager->flush();
    
                // generate a signed url and email it to the user
                $this->sendConfirmationEmail($user, '', $session, true);
                // do anything else you need here, like send an email
    
                return $this->redirectToRoute('app_login');
            } else {
                $form->addError(new FormError("Can't use same nickname"));
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * "Send confirmation e-mail" page
     */
    #[Route('/{_locale}/sendConfirmationEmail/{email}/{token}', name: 'app_send_confirmation_email', requirements:[ '_locale' => '%app.locales%' ], defaults: [ 'token' => '' ])]
    public function sendConfirmationEmail(
        User $user,
        string $token,
        SessionInterface $session,
        bool $bypassToken = false
    ): Response {
        if (
            !$bypassToken && (
                !$token ||
                $token != $session->get('reverifyToken')
            )
        ) return $this->redirectToRoute('app_login');

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($_ENV['admin_mail'], 'SmartChef'))
                ->to($user->getEmail())
                ->subject($this->translator->trans('messages.user.registration.confirmation.title'))
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        $session->remove('reverifyToken');

        return $this->redirectToRoute('app_login');
    }

    /**
     * Verify account URL (link received in e-mail)
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }
        
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        return $this->redirectToRoute('app_login');
    }
}
