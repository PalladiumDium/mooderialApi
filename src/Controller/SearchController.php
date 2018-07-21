<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SearchController extends Controller
{
    /**
     * @Route("/search/name/{name}", name="search_product_name")
     *
     * @Method({"GET"})
     */
    public function SearchProductByName($name, Request $request)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findBy( ['name' => $name] );

        $json = $serializer->serialize($products, 'json');

        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/search/price/{price}", name="search_product_price")
     *
     * @Method({"GET"})
     */
    public function SearchProductByPrice($price, Request $request)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findBy( ['price' => $price] );

        $json = $serializer->serialize($products, 'json');

        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/search/category/{category}", name="search_product_categ")
     *
     * @Method({"GET"})
     */
    public function SearchProductByCateg($category, Request $request) {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $Category = $this->getDoctrine()->getRepository(Category::class)->findOneBy( ['label' => $category] );
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findBy( ['category' => $Category->getId()] );

        $json = $serializer->serialize($products, 'json');

        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
