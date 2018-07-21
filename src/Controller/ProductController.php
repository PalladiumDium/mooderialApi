<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
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

class ProductController extends Controller
{
    /**
     * @Route("/product/add", name="Add_Product")
     *
     * @Method({"POST"})
     */
    public function AddProduct(Request $request)
    {
        $Product = new Product();
        $Product->setName($request->query->get('name'));
        $Product->setDescription($request->query->get('description'));
        $Product->setPrice($request->query->get('price'));
        $Product->setCategory($request->query->get('category'));

        $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Product);
            $entityManager->flush();

        $Category = $entityManager->getRepository(Category::class)->find($Product->getCategory());

        $json = $this->json([
            'name' => $Product->getName(),
            'description' => $Product->getDescription(),
            'price' => $Product->getPrice(),
            'category' => $Category->getLabel()
        ]);

        $response = new Response($json, Response::HTTP_CREATED);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/product/remove/{id}", name="Remove_Product")
     *
     * @Method({"DELETE"})
     */
    public function RemoveProduct($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $Product = $entityManager->getRepository(Product::class)->find($id);
        $entityManager->remove($Product);
        $entityManager->flush();

        $json = $this->json([ 'message' => "Product correctly deleted !" ]);
        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/product/set/{id}", name="Set_Product")
     *
     * @Method({"POST"})
     */
    public function SetProduct($id, Request $request)
    {
        $tmpProduct = new Product();
        $tmpProduct->setName($request->query->get('name'));
        $tmpProduct->setDescription($request->query->get('description'));
        $tmpProduct->setPrice($request->query->get('price'));
        $tmpProduct->setCategory($request->query->get('category'));

        $entityManager = $this->getDoctrine()->getManager();
        $Product = $entityManager->getRepository(Product::class)->find($id);

        if (!$Product) {
            throw $this->createNotFoundException('No Product found !');
        } else {
            $Product->setName($tmpProduct->getName());
            $Product->setDescription($tmpProduct->getDescription());
            $Product->setPrice($tmpProduct->getPrice());
            $Product->setCategory($tmpProduct->getCategory());
            $entityManager->flush();
        }

        $Category = $entityManager->getRepository(Category::class)->find($Product->getCategory());

        $json = $this->json([
            'id' => $Product->getId(),
            'name' => $Product->getName(),
            'description' => $Product->getDescription(),
            'price' => $Product->getPrice(),
            'category' => $Category->getLabel()
        ]);
        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/product/{id}", name="Product")
     *
     * @Method({"GET"})
     */
    public function ShowProductById($id)
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $Product = $repository->find($id);
        $Category = $this->getDoctrine()->getRepository(Category::class)->find($Product->getCategory());
        $json = $this->json([
            'id' => $Product->getId(),
            'name' => $Product->getName(),
            'description' => $Product->getDescription(),
            'price' => $Product->getPrice(),
            'category' => $Category->getLabel()
        ]);
        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/products", name="Show_Products")
     *
     * @Method({"GET"})
     */
    public function ShowProducts(Request $request) {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findAll();

        $json = $serializer->serialize($products, 'json');

        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
