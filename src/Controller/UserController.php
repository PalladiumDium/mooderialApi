<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    /**
     * @Route("/user/add", name="Add_user")
     *
     * @Method({"POST"})
     */
    public function AddUser(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $plainPassword = $request->query->get('password');
        $password = $encoder->encodePassword($user, $plainPassword);
        $user->setEmail($request->query->get('name'));
        $user->setPassword($password);
        $user->setEmail($request->query->get('email'));
        $user->setIsActive(true);
        $user->addRole("ROLE_USER");

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

        if (!$user) {
            throw $this->createNotFoundException('No user found !');
        } else {
            $json = $serializer->serialize($user, 'json');
        }

        $response = new Response($json, Response::HTTP_CREATED);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/user/remove/{id}", name="Remove_user")
     *
     * @Method({"DELETE"})
     */
    public function RemoveUser($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($user);
        $entityManager->flush();

        $json = $this->json([ 'message' => "User correctly deleted !" ]);
        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/user/set/{id}", name="Set_user")
     *
     * @Method({"POST"})
     */
    public function SetUser($id, Request $request)
    {
        $tmpUser = new User();
        $tmpUser->setName($request->query->get('name'));
        $tmpUser->setPassword($request->query->get('password'));
        $tmpUser->setEmail($request->query->get('email'));

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);



        if (!$user) {
            throw $this->createNotFoundException('No user found !');
        } else {
            $json = $serializer->serialize($user, 'json');
        }
        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
    * @Route("/user/auth", name="Authenticate_user")
    *
    * @Method({"GET"})
    */
    public function Authenticate(Request $request, UserPasswordEncoderInterface $encoder) {

        $repository = $this->getDoctrine()->getRepository(User::class);

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $user = $repository->findOneBy([
            "name" => $request->query->get('name'),
        ]);

        if (!$encoder->isPasswordValid($user,$request->query->get('password'))){
            return new Response("wrong password", Response::HTTP_NOT_FOUND);

        } else {
            if (!$user) {
                throw $this->createNotFoundException('No user found !');
            } else {
                $json = $serializer->serialize($user, 'json');
            }
            $response = new Response($json, Response::HTTP_OK);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    /**
     * @Route("/user/{id}", name="user")
     *
     * @Method({"GET"})
     */
    public function ShowUser($id)
    {

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);


        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        $json = $serializer->serialize($user, 'json');
        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
