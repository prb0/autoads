<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Ad;
use App\Entity\Auto;

class AdsController extends AbstractController
{
    /**
     * @Route("/ads", name="ads")
     */
    public function index()
    {
        // $ads = new Ad();
        // $ads->setPrice(100);
        // $ads->setDescription('Машинка');
        // $ads->setDateBegin(new \DateTime("2018-08-01"));
        // $ads->setDateEnd(new \DateTime("2018-08-10"));

        // $auto = new Auto();
        // $auto->setManufacturer('Запорожец');
        // $auto->setOriginCountry('СССР');
        // $auto->setReleaseYear(1985);

        // $ads->setAuto($auto);

        // $em = $this->getDoctrine()->getManager();
        // $em->persist($ads);
        // $em->persist($auto);
        // $em->flush();

		// // или найти по имени и цене
		// $product = $repository->findOneBy([
		//     'name' => 'Keyboard',
		//     'price' => 19.99,
		// ]);

		// // искать несколько объектов Товар, соответствующих имени, упорядоченные по цене
		// $products = $repository->findBy(
		//     ['name' => 'Keyboard'],
		//     ['price' => 'ASC']
		// );

        $repository = $this->getDoctrine()->getRepository(Ad::class);
        $ads = $repository->findAll();

        return $this->render('ads/ads.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * @Route("/ads/{id}", name="ad")
     */
    public function showAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Ad::class);
        $ad = $repository->find($id);

        return $this->render('ads/ad.html.twig', [
            'ad' => $ad
        ]);
    }
}
