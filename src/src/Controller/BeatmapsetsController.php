<?php

namespace App\Controller;

use App\Entity\BeatmapsetComment;
use App\Entity\BeatmapsetCommentVote;
use App\Repository\BeatmapsetCommentRepository;
use App\Repository\BeatmapsetCommentVoteRepository;
use App\Repository\BeatmapsetRepository;
use App\Service\CommentVoteService;
use App\Service\StorageService;
use Nytodev\InertiaBundle\Service\Inertia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class BeatmapsetsController extends AbstractController
{
    public function __construct(
        private readonly StorageService $storage,
    ) {}

    #[Route('/maps/{id}', name: 'app_beatmapsets')]
    public function show($id, Inertia $inertia, BeatmapsetRepository $repository, BeatmapsetCommentRepository $commentRepository, BeatmapsetCommentVoteRepository $votesRepo): Response
    {

        $beatmapset = $repository->find($id);
        $author = $beatmapset->getAuthor();
        $comments = $commentRepository->findByBeatmapset($beatmapset);
        $votes = $votesRepo->findUserVotesForComments($this->getUser(), $comments);

        /*
        return $this->render('beatmapsets/index.html.twig', [
            'storage' => $this->storage,
            'controller_name' => 'BeatmapsetsController',
            'beatmapset' => $beatmapset,
            'comments' => $comments,
            'userVotes' => $votes,
        ]);
        */

        $commentsData = array_map(function ($comment) use ($votes) {
            $userVote = $votes[$comment->getId()] ?? null; // adjust based on what findUserVotesForComments returns

            return [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'createdAt' => $comment->getCreatedAt(),
                'author' => [
                    'id' => $comment->getAuthor()->getId(),
                    'username' => $comment->getAuthor()->getUsername(),
                    'avatarURL' => $comment->getAuthor()->getAvatarURL(),
                ],
                'userVote' => $userVote, // e.g. 'up', 'down', or null
                'likes' => $comment->getLikes(),
                'dislikes' => $comment->getDislikes()
            ];
        }, $comments);

        return $inertia->render('beatmapsets/Show', [
            'beatmapset' => [
                'id' => $beatmapset->getId(),
                'coverURL' => $beatmapset->getCoverUrl($this->storage),
                'title' => $beatmapset->getTitle(),
                'description' => $beatmapset->getDescription(),
                'artist' => $beatmapset->getArtist(),
                'createdAt' => $beatmapset->getCreatedAt(),
                'author' => [
                    'id' => $author->getId(),
                    'username' => $author->getUsername(),
                    'avatarURL' => $author->getAvatarURL()
                ],
                'likes' => $beatmapset->getLikes(),
                'dislikes' => $beatmapset->getLikes(),
                'downloads' => $beatmapset->getDownloads(),
                'favorites' => $beatmapset->getFavorites(),
                'comments' => $commentsData
            ]
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

    #[Route('/api/maps/comments/{id}/vote/{type}', name: 'app_comment_vote', methods: ['POST'])]
    public function vote(
        BeatmapsetComment $comment,
        string $type,
        Request $request,
        CommentVoteService $voteService,
        CsrfTokenManagerInterface $csrfTokenManager,
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $token = $request->headers->get('X-CSRF-TOKEN');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('inertia', $token))) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

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

    #[Route('/api/debug-headers', name: 'debug_headers')]
    public function debugHeaders(Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->json([
            'authorization_header' => $request->headers->get('Authorization'),
            'all_headers' => $request->headers->all(),
            'server_auth' => $_SERVER['HTTP_AUTHORIZATION'] ?? 'NOT SET',
            'server_redirect_auth' => $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? 'NOT SET',
        ]);
    }
}
