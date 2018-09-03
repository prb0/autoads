<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Ad;
use App\Entity\Auto;

class AdsController extends AbstractController
{
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Ad::class);
        $ads = $repository->findAll();

        return $this->render('ads/ads.html.twig', [
            'ads' => $ads
        ]);
    }

    public function showAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Ad::class);
        $ad = $repository->find($id);

        return $this->render('ads/ad.html.twig', [
            'ad' => $ad
        ]);
    }

    public function new(Request $request)
    {
        $ad = new Ad();
        $formAd = $this->createFormAd($ad, 'Создать');

        $formAd->handleRequest($request);

        $message = '';

        if ($formAd->isSubmitted() && $formAd->isValid()) {
		    $obj = $formAd->getData();

	    	if ($obj instanceof Ad) {
	    		if ($obj->getAuto() instanceof Auto) {
		    		$em = $this->getDoctrine()->getManager();
			        $em->persist($obj);
			        $em->flush();
			        return $this->redirectToRoute('ad_new');
			    } else {
			    	$message .= 'Авто не выбрано';
			    }
		    } else {
		    	throw new Exception("Неизвестный класс создаваемого объекта");
		    }
	    }

        return $this->render('ads/new.html.twig', array(
            'formAd' => $formAd->createView(),
            'response' => $message
        ));
    }

    public function updateAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
	    $ad = $em->getRepository(Ad::class)->find($id);

	    if (!$ad) {
	        throw $this->createNotFoundException(
	            'Не найдено объявление номер '.$id
	        );
	    }

        $formAd = $this->createFormAd($ad, 'Изменить');

        $formAd->handleRequest($request);

        if ($formAd->isSubmitted() && $formAd->isValid()) {
		    $obj = $formAd->getData();

	    	if ($obj instanceof Ad) {
			    $ad->setDescription($obj->getDescription());
			    $ad->setAuto($obj->getAuto());
			    $ad->setPrice($obj->getPrice());
			    $ad->setDateBegin($obj->getDateBegin());
			    $ad->setDateEnd($obj->getDateEnd());
		        $em->flush();
		        return $this->redirectToRoute('ad_update', array(
		            'id' => $ad->getId(),
		        ));
		    } else {
		    	throw new Exception("Неизвестный класс редактируемого объекта");
		    }
	    }

        return $this->render('ads/update.html.twig', array(
            'formAd' => $formAd->createView(),
        ));
	}

	public function removeAction($id)
    {
        $message = '';
        $em = $this->getDoctrine()->getManager();
        $ad = $em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            throw $this->createNotFoundException(
                'Не найдено объявление номер '.$id
            );
        }

        $message .= 'Объявление № ' . $ad->getId() . ' удалено';
        $em->remove($ad);
        $em->flush();

        $ads = $em->getRepository(Ad::class)->findAll();

        return $this->render('ads/ads.html.twig', array(
            'ads' => $ads,
            'response' => $message
        ));
    }

    private function createFormAd(Ad $ad, $submitText)
    {
        return $this->createFormBuilder($ad)
            ->add('description', TextType::class)
            ->add('auto', EntityType::class, [
                'class' => Auto::class,
                'query_builder' => function (\App\Repository\AutoRepository $er) {
                    return $er->createQueryBuilder('a');
                },
                'choice_label' => 'auto',
            ])
            ->add('date_begin', DateType::class)
            ->add('date_end', DateType::class)
            ->add('price', IntegerType::class)
            ->add('save', SubmitType::class, array('label' => $submitText))
            ->getForm();
    }
}
