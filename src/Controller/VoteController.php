<?php

namespace App\Controller;

use App\Entity\Vote;
use App\Entity\Recipe;
use App\Repository\VoteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/vote')]
class VoteController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/{recipe}/cast/{positive}', name: 'app_vote')]
    public function vote(Recipe $recipe, bool $positive, VoteRepository $voteRepository): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$user->isVerified()) return $this->json([
            'hasWorked' => false,
            'newScore' => $recipe->getScore()
        ]);

        $vote = $voteRepository->findOneBy([
            'user' => $this->getUser(),
            'recipe' => $recipe
        ]) ?? (new Vote)->setUser($this->getUser())->setRecipe($recipe);

        $vote->setIsPositive($positive);

        $voteRepository->add($vote, true);

        return $this->json([
            'hasWorked' => true,
            'newScore' => $recipe->getScore()
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{recipe}', name: 'app_vote_check')]
    public function checkVote(Recipe $recipe, VoteRepository $voteRepository): Response {
        $vote = $voteRepository->findOneBy([
            'user' => $this->getUser(),
            'recipe' => $recipe
        ]);

        return $this->json([
            'hasVoted' => boolval($vote),
            'positive' => ($vote ? $vote->isPositive() : null)
        ]);
    }

    
    #[IsGranted('ROLE_USER')]
    #[Route('/{recipe}/remove', name: 'app_vote_remove')]
    public function removeVote(Recipe $recipe, VoteRepository $voteRepository): Response {
        $vote = $voteRepository->findOneBy([
            'user' => $this->getUser(),
            'recipe' => $recipe
        ]);
        
        if ($vote !== null) $voteRepository->remove($vote, true);

        return $this->json([
            'hasWorked' => true,
            'newScore' => $recipe->getScore()
        ]);
    }
}
