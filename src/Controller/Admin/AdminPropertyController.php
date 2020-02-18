<?php


namespace App\Controller\Admin;


use App\Entity\Equipement;
use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPropertyController extends AbstractController
{
    /**
     * @Route("/admin", name="admin.property.index")
     * @param PropertyRepository $repository
     * @return Response
     */
    public function index(PropertyRepository $repository)
    {
        $properties = $repository->findAll();
        return $this->render('admin/property/index.html.twig', compact('properties'));
    }

    /**
     * @Route("/admin/property/create", name="admin.property.new")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {
        $property = new Property();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($property);
            $em->flush();
            $this->addFlash('success', 'Bien creé avec success');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.edit", methods={"GET|POST"})
     * @param Property $property
     * @param Request $request
     * @return Response
     */
    public function edit(Property $property, Request $request)
    {
//        $equipement = new Equipement();
//        $property->addEquipement($equipement);
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'Bien modifié avec success');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.delete", methods={"DELETE"})
     * @param Property $property
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Property $property, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$property->getId(), $request->get('_token'))){
            $em = $this->getDoctrine()->getManager();
            $em->remove($property);
            $em->flush();
            $this->addFlash('success', 'Bien supprimé avec success');
        }
        return $this->redirectToRoute('admin.property.index');

    }
}
