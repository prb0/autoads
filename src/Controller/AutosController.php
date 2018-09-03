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
use App\Entity\Auto;
use App\Entity\Ad;

class AutosController extends AbstractController
{
    public function index($filterBy, $value)
    {
        $em = $this->getDoctrine()->getManager();
        $autoRepository = $em->getRepository(Auto::class);
        if ($filterBy && $value) {
            $autos = $autoRepository->findBy(
                [$filterBy => $value]
            );
        } else {
            $autos = $autoRepository->findAll();    
        }
        
        $filterFields = $this->getFilterFields();
        

        return $this->render('autos/autos.html.twig', [
            'autos' => $autos,
            'filterFields' => $filterFields
        ]);
    }

    public function showAction($id, $response = false)
    {
        $repository = $this->getDoctrine()->getRepository(Auto::class);
        $auto = $repository->find($id);

        return $this->render('autos/auto.html.twig', [
            'auto' => $auto,
            'response' => $response
        ]);
    }

    public function new(Request $request)
    {
        $formAuto = $this->createFormAuto();

        $formAuto->handleRequest($request);

        if ($formAuto->isSubmitted() && $formAuto->isValid()) {
		    $obj = $formAuto->getData();

	    	if ($obj instanceof Auto) {
		        $em = $this->getDoctrine()->getManager();
		        $em->persist($obj);
		        $em->flush();
		        return $this->redirectToRoute('auto_new');
		    } else {
		    	throw new Exception("Неизвестный класс создаваемого объекта");
		    }
	    }

        return $this->render('autos/new.html.twig', array(
            'formAuto' => $formAuto->createView(),
        ));
    }

    public function updateAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
	    $auto = $em->getRepository(Auto::class)->find($id);

	    if (!$auto) {
	        throw $this->createNotFoundException(
	            'Не найдено авто номер '.$id
	        );
	    }

        $formAuto = $this->createFormAuto();

        $formAuto->handleRequest($request);

        if ($formAuto->isSubmitted() && $formAuto->isValid()) {
		    $obj = $formAuto->getData();

	    	if ($obj instanceof Auto) {
			    $auto->setManufacturer($obj->getManufacturer());
			    $auto->setReleaseYear($obj->getReleaseYear());
			    $auto->setOriginCountry($obj->getOriginCountry());
		        $em->flush();
		        return $this->redirectToRoute('auto_update', array(
		            'id' => $auto->getId(),
		        ));
		    } else {
		    	throw new Exception("Неизвестный класс редактируемого объекта");
		    }
	    }

        return $this->render('autos/update.html.twig', array(
            'formAuto' => $formAuto->createView(),
        ));
	}

    public function removeAction($id)
    {
        $message = '';
        $em = $this->getDoctrine()->getManager();
        $auto = $em->getRepository(Auto::class)->find($id);
        $ads = $em->getRepository(Ad::class)->findBy(['auto' => $id]);

        if (!$auto) {
            throw $this->createNotFoundException(
                'Не найдено авто номер '.$id
            );
        }

        if (!empty($ads)) {
            $message .= 'Объявления № ';
            foreach ($ads as $ad) {
                $message .= '[' . $ad->getId() . '] ';
                $em->remove($ad);
            }
            $message .= 'и ';
        }

        $message .= 'Автомобиль № [' . $auto->getId() . '] удален[ы]';
        $em->remove($auto);
        $em->flush();

        $autoRepository = $em->getRepository(Auto::class);
        $autos = $autoRepository->findAll();    

        $filterFields = $this->getFilterFields();

        return $this->render('autos/autos.html.twig', [
            'autos' => $autos,
            'response' => $message,
            'filterFields' => $filterFields
        ]);
    }

    private function createFormAuto()
    {
        $auto = new Auto();
        return $this->createFormBuilder($auto)
            ->add('manufacturer', TextType::class)
            ->add('origin_country', TextType::class)
            ->add('release_year', IntegerType::class)
            ->add('save', SubmitType::class, array('label' => 'Добавить в список'))
            ->getForm();
    }

    private function getFilterFields()
    {
        $autoRepository = $this->getDoctrine()->getManager()->getRepository(Auto::class);
        $filterFields = [];
        $filterFields['manufacturer'] = $autoRepository->getFieldValues('a.manufacturer');
        $filterFields['origin_country'] = $autoRepository->getFieldValues('a.origin_country');
        $filterFields['release_year'] = $autoRepository->getFieldValues('a.release_year');

        return $filterFields;
    }
}
