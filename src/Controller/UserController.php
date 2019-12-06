<?php
declare(strict_types=1);

namespace App\Controller;

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
        $user =  $user->setName($request->get("name"))
            ->setEmailId($request->get("email_id"))
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
     * @FOSRest\Post("/dummy")
     * @param EntityManagerInterface $em
     * @return View
     */
    public function postInBulk(EntityManagerInterface $em)
    {
        $faker = Faker\factory::create();
        for($i = 0; $i < 50; $i++)
        {
            $user = new User();
            $user = $user->setName($faker->get("name"))
                ->setEmailId($faker->get("name")."@gmail.com")
                ->setUserType($faker->get("userAgent"))
                ->setCreatedAt(new \DateTime("now"))
                ->setCompanyName($faker->get("company"));
            $em->persist($user);
            $em->flush();
        }

        return new Response("added the products successfully",Response::HTTP_OK);

    }


    /**
     * Fetch a user
     *
     * @FOSRest\Get("/{userid}")
     * @param $id
     *
     * @return View
     */
    public function getUsers($userid, EntityManagerInterface $em)
    {
        $repository=$em->getRepository(User::class);
        $user=$repository->find(["id"=>$userid]);
        return View::create($user, Response::HTTP_OK);
    }


    /**
     * Fetches all the Users From the database
     *
     * @FOSRest\Get("/allusers")
     * @param EntityManagerInterface $em
     * @return View
     */
    public function getAllUsers(EntityManagerInterface $em)
    {
        $repository=$em->getRepository(User::class);
        $Userlist=$repository->findALL();
        var_dump($Userlist);
        return View::create($Userlist, Response::HTTP_OK);
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
        return new Response("Deleted user with user id $id successfully",Response::HTTP_OK);
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
    }
}
