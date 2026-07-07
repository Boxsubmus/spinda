<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(TaskRepository $repo): JsonResponse
    {
        $tasks = $repo->findAll();

        dd($tasks);
        
        return $this->json($tasks);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function get(Task $task): JsonResponse
    {
        return $this->json($task);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $task = new Task();
        $task->setTitle($data['title'] ?? '');
        $task->setDone($data['done'] ?? false);

        $em->persist($task);
        $em->flush();

        return $this->json($task, 201);
    }
}