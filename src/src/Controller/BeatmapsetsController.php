<?php

namespace App\Controller;

use App\Entity\Beatmapset;
use App\Entity\BeatmapsetComment;
use App\Entity\BeatmapsetCommentVote;
use App\Repository\BeatmapsetCommentRepository;
use App\Repository\BeatmapsetCommentVoteRepository;
use App\Repository\BeatmapsetRepository;
use App\Serializer\BeatmapsetSerializer;
use App\Service\BeatmapsetStorageService;
use App\Service\CommentVoteService;
use App\Service\StorageService;
use Doctrine\ORM\EntityManagerInterface;
use Nytodev\InertiaBundle\Service\Inertia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    #[Route('/maps/{id}', name: 'app_beatmapsets_show')]
    public function show($id, Inertia $inertia, BeatmapsetRepository $repository, BeatmapsetCommentRepository $commentRepository, BeatmapsetCommentVoteRepository $votesRepo): Response
    {
        $beatmapset = $repository->find($id);
        $comments = $commentRepository->findByBeatmapset($beatmapset);
        $votes = $votesRepo->findUserVotesForComments($this->getUser(), $comments);

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
            'beatmapset' => BeatmapsetSerializer::serializeVerbose($beatmapset, $this->storage),
            'comments' => $commentsData
        ]);
    }

    #[Route('/maps', name: 'app_beatmapsets_index')]
    public function index(Request $request, Inertia $inertia, BeatmapsetRepository $repository)
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 10;

        $result = $repository->paginate($page, $perPage);

        return $inertia->render('beatmapsets/Index', [
            'beatmapsets' => array_map(
                fn(Beatmapset $beatmap) => BeatmapsetSerializer::serializeVerbose($beatmap, $this->storage),
                $result['items']
            ),
            'pagination' => [
                'currentPage' => $page,
                'lastPage' => $result['lastPage'],
                'perPage' => $perPage,
                'total' => $result['total'],
            ],
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

    #[Route('/api/maps/{id}/comments/post', name: 'app_comment_post', methods: ['POST'])]
    public function createComment(
        Beatmapset $beatmapset,
        Request $request,
        EntityManagerInterface $em,
    ): RedirectResponse {
        $content = trim($request->toArray()['content'] ?? '');

        if ($content === '' || mb_strlen($content) > 1000) {
            // Inertia expects validation errors as a keyed array on redirect back
            throw ValidationException::withMessages([
                'content' => 'Comment must be between 1 and 1000 characters.',
            ]);
        }

        $comment = new BeatmapsetComment();
        $comment->setContent($content);
        $comment->setAuthor($this->getUser());
        $comment->setBeatmapset($beatmapset);
        $comment->setLikes(0);
        $comment->setDislikes(0);
        $comment->setCreatedAt(new \DateTimeImmutable());
        $em->persist($comment);
        $em->flush();

        return $this->redirectToRoute('app_beatmapsets_show', ['id' => $beatmapset->getId()]);
    }
}
