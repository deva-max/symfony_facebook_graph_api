<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Psr7\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private $manager;
    private $user;

    public function __construct(EntityManagerInterface $manager, UserRepository $user)
    {
        $this->manager = $manager;
        $this->user = $user;
    }


    #[Route('/userCreate', name: 'user_create', methods: 'POST')]
    public function userCreate(Request $request): HttpFoundationResponse
    {

        $data = json_decode($request->getContent(), true);

        $email= $data['email'];
        $password= $data['password'];

        $email_exist = $this->user->findByOneByEmail($email);
        if($email_exist)
        {
            return new JsonResponse
            (
                [
                    'status' => false,
                    'message' => 'email not found'
                ]
            );
        }else{

            #class obj creation
            $user= new User();

            #obj assign variables
            $user->setEmail($email)->setPassword(sha1($password));

            $this->manager->persist($user);
            $this->manager->flush();

            return new JsonResponse
            (
                [
                    'status' => true,
                    'message' => 'email found success'
                ]
            );

        }

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/getAllUsers', name: 'get_allusera', methods: 'GET')]
    public function getAllUsers(): HttpFoundationResponse
    {
        $users = $this->user->findAll();

        return new JsonResponse
        (
            [
                'status'=>true,
                'users'=>$users
            ]
        );
    }
}
