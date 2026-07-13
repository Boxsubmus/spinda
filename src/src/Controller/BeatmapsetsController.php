<?php

namespace App\Controller;

use App\Entity\BeatmapsetComment;
use App\Entity\BeatmapsetCommentVote;
use App\Repository\BeatmapsetCommentRepository;
use App\Repository\BeatmapsetCommentVoteRepository;
use App\Repository\BeatmapsetRepository;
use App\Service\CommentVoteService;
use App\Service\StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BeatmapsetsController extends AbstractController
{
    public function __construct(
        private readonly StorageService $storage,
    ) {}

    #[Route('/maps/{id}', name: 'app_beatmapsets')]
    public function index($id, BeatmapsetRepository $repository, BeatmapsetCommentRepository $commentRepository, BeatmapsetCommentVoteRepository $votesRepo): Response
    {
        $beatmapset = $repository->find($id);
        $comments = $commentRepository->findByBeatmapset($beatmapset);
        $votes = $votesRepo->findUserVotesForComments($this->getUser(), $comments);

        return $this->render('beatmapsets/index.html.twig', [
            'storage' => $this->storage,
            'controller_name' => 'BeatmapsetsController',
            'beatmapset' => $beatmapset,
            'comments' => $comments,
            'userVotes' => $votes,
        ]);
    }

    #[Route('/maps/{id}/download', name: 'app_beatmapsets_download')]
    public function download($id, BeatmapsetRepository $repository): Response
    {
        return new Response('what');
    }

    // --------------------------------
    // Comment stuff
    // --------------------------------

    #[Route('/maps/comments/{id}/vote/{type}', name: 'app_comment_vote', methods: ['POST'])]
    public function vote(
        BeatmapsetComment $comment,
        string $type,
        CommentVoteService $voteService,
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $voteType = match ($type) {
            'like' => BeatmapsetCommentVote::TYPE_LIKE,
            'dislike' => BeatmapsetCommentVote::TYPE_DISLIKE,
            default => throw $this->createNotFoundException(),
        };

        $newState = $voteService->vote($this->getUser(), $comment, $voteType);

        return $this->json([
            'state' => $newState,
            'likes' => $comment->getLikes(),
            'dislikes' => $comment->getDislikes(),
        ]);
    }
}
