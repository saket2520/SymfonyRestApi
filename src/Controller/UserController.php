<?php
declare(strict_types=1);

namespace App\Controller;

use Faker;
use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UserController extends FOSRestController
{
    /**
     * Create a User
     *
     * @FOSRest\Post("/")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return View
     */
    public function postUser(Request $request, EntityManagerInterface $em)
    {
        $user = new User();
        $user =  $user->setName($request->query->get("name"))
            ->setEmailId($request->query->get("email_id"))
            ->setUserType($request->get("user_type"))
            ->setCreatedAt(new \DateTime("now"))
            ->setCompanyName($request->get("company_name"));
        $em->persist($user);
        $em->flush();
        return View::create($user,Response::HTTP_CREATED);
    }

    /**
     * creates users in bulk
     *
     * @FOSRest\Post("/users")
     * @param EntityManagerInterface $em
     * @return View
     */
    public function postUsers(EntityManagerInterface $em)
    {
        $faker = Faker\Factory::create();
        for($i = 0; $i < 10; $i++)
        {
            $user = new User();
            $user = $user->setName($faker->name)
                ->setEmailId($faker->name."@gmail.com")
                ->setUserType($faker->creditCardType)
                ->setCreatedAt(new \DateTime("now"))
                ->setCompanyName($faker->company);
            $em->persist($user);
        }

        $em->flush();
        return new Response("added the products successfully",Response::HTTP_OK);

    }


    /**
     * Fetch a user
     *
     * @Route("/{id<\d+>}",methods={"GET"})
     * @param $id
     *
     * @return View
     */
    public function getUsers($id, EntityManagerInterface $em)
    {
        $repository = $em->getRepository(User::class);
        $user = $repository->find(["id"=>$id]);
        return View::create($user, Response::HTTP_OK);
    }


    /**
     * Fetches all the Users From the database
     *
     * @FOSRest\Get("/users")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function getUserCollection(EntityManagerInterface $em)
    {
        $repository = $em->getRepository(User::class);
        $userlist = $repository->findALL();
        return View::create($userlist,Response::HTTP_OK); 
    }


    /**
     * Deletes a user with a specific id
     *
     * @FOSRest\Delete("/{id}")
     * @param $id
     */
    public function deleteUser($id,EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find(["id"=>$id]);
        $em->remove($user);
        $em->flush();
        return new Response("Deleted $id successfully",Response::HTTP_OK);
    }


    /**
     * Updates a User object in the database.
     *
     * @FOSRest\Put("/{id}")
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     */
    public function updateUser($id,Request $request,EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find(["id"=>$id]);
        $postdata = json_decode($request->getContent());
        $user = $user->setName($postdata->name)
            ->setEmailId($postdata->email_id)
            ->setUserType($postdata->user_type)
            ->setCreatedAt(new \DateTime($postdata->created_at))
            ->setCompanyName($postdata->company_name);
        $em->persist($user);
        $em->flush();
        return View::create($user,Response::HTTP_OK);
    }
}
