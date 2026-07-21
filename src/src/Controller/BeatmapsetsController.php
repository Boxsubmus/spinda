<?php

namespace App\Controller;

use App\Entity\Beatmapset;
use App\Entity\BeatmapsetComment;
use App\Entity\BeatmapsetCommentVote;
use App\Entity\FavoriteBeatmapset;
use App\Repository\BeatmapsetCommentRepository;
use App\Repository\BeatmapsetCommentVoteRepository;
use App\Repository\BeatmapsetRepository;
use App\Repository\FavoriteBeatmapsetRepository;
use App\Security\Voter\BeatmapsetVoter;
use App\Serializer\BeatmapsetSerializer;
use App\Service\BeatmapsetStorageService;
use App\Service\CommentVoteService;
use App\Service\StorageService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Nytodev\InertiaBundle\Service\Inertia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class BeatmapsetsController extends AbstractController
{
    public function __construct(
        private readonly StorageService $storage,
    ) {}

    #[Route('/maps/{id}', name: 'app_beatmapsets_show')]
    public function show($id,
        Inertia $inertia,
        BeatmapsetRepository $repository,
        BeatmapsetCommentRepository $commentRepository,
        BeatmapsetCommentVoteRepository $votesRepo,
        FavoriteBeatmapsetRepository $favoriteRepo): Response
    {
        $beatmapset = $repository->find($id);
        $user = $this->getUser();
        
        $comments = $commentRepository->findByBeatmapset($beatmapset);
        $votes = $votesRepo->findUserVotesForComments($user, $comments);

        $commentsData = array_map(function ($comment) use ($votes) {
            return [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'createdAt' => $comment->getCreatedAt(),
                'author' => [
                    'id' => $comment->getAuthor()->getId(),
                    'username' => $comment->getAuthor()->getUsername(),
                    'avatarURL' => $comment->getAuthor()->getAvatarURL(),
                ],
                'userVote' => $votes[$comment->getId()] ?? null,
                'likes' => $comment->getLikes(),
                'dislikes' => $comment->getDislikes()
            ];
        }, $comments);

        $isFavorited = $favoriteRepo->isFavorited($user, $beatmapset);

        return $inertia->render('beatmapsets/Show', [
            'beatmapset' => BeatmapsetSerializer::serializeVerbose($beatmapset, $this->storage),
            'comments' => $commentsData,
            'isFavorited' => $user ? $isFavorited : false,
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

    #[Route('/api/maps/{id}/description', methods: ['POST'])]
    public function descriptionEdit(Beatmapset $beatmapsetEditing, Request $request, CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(BeatmapsetVoter::EDIT, $beatmapsetEditing);

        $token = $request->headers->get('X-CSRF-TOKEN');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('inertia', $token))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $content = trim($request->toArray()['content'] ?? '');

        if (mb_strlen($content) > 2048) {
            return new JsonResponse(['error' => 'Content is too long!'], 413);
        }

        $beatmapsetEditing->setDescription($content);
        $em->persist($beatmapsetEditing);
        $em->flush();

        return $this->json([
            'description' => $beatmapsetEditing->getDescription(),
        ]);
    }

    #[Route('/api/maps/{id}/favorite', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function toggleFavorite(
        $id,
        BeatmapsetRepository $repository,
        FavoriteBeatmapsetRepository $favoriteRepo,
        EntityManagerInterface $em): Response
    {
        $beatmapset = $repository->find($id);
        $user = $this->getUser();

        if ($beatmapset->getAuthor() === $user) {
            return $this->json(['error' => "You can't favorite your own beatmap!"], Response::HTTP_FORBIDDEN);
        }

        $existing = $favoriteRepo->findOneBy(['user' => $user, 'beatmapset' => $beatmapset]);
        $delta = $existing ? -1 : 1;

        if ($existing) {
            $em->remove($existing);
        } else {
            $favorite = new FavoriteBeatmapset();
            $favorite->setUser($user);
            $favorite->setBeatmapset($beatmapset);
            $favorite->setCreatedAt(new \DateTimeImmutable());
            $em->persist($favorite);
        }

        try {
            $em->flush();

        } catch (UniqueConstraintViolationException) {
            // Lost a race to favorite the same map twice, treat as already-favorited
            return $this->json([
                'favorited' => true,
                'favoriteCount' => $beatmapset->getFavorites(),
            ]);
        }

        $favoriteRepo->incrementFavoriteCount($beatmapset, $delta);

        return $this->json([
            'favorited' => $delta === 1,
            'favoriteCount' => $beatmapset->getFavorites() + $delta
        ]);
    }

    #[Route('/api/maps/{id}/feature', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[IsGranted('ROLE_ADMIN')]
    public function feature(
        $id,
        BeatmapsetRepository $repository,
        EntityManagerInterface $em): Response
    {
        /** @var Beatmapset $beatmapset */
        $beatmapset = $repository->find($id);
        $user = $this->getUser();

        $beatmapset->setIsFeatured(true);
        $beatmapset->setFeaturedAt(new \DateTimeImmutable());
        $em->persist($beatmapset);

        $em->flush();

        return $this->json([
            'featured' => true,
        ]);
    }
}
