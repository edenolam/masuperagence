<?php

namespace App\Controller\Admin;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/equipement")
 */
class AdminEquipementController extends AbstractController
{
    /**
     * @Route("/", name="equipement.index", methods={"GET"})
     * @param EquipementRepository $equipementRepository
     * @return Response
     */
    public function index(EquipementRepository $equipementRepository): Response
    {
        return $this->render('admin/equipement/index.html.twig', [
            'equipements' => $equipementRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin.equipement.new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($equipement);
            $entityManager->flush();

            return $this->redirectToRoute('equipement.index');
        }

        return $this->render('admin/equipement/new.html.twig', [
            'equipement' => $equipement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin.equipement.edit", methods={"GET","POST"})
     * @param Request $request
     * @param Equipement $equipement
     * @return Response
     */
    public function edit(Request $request, Equipement $equipement): Response
    {
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'equipement modifié avec success');

            return $this->redirectToRoute('equipement.index');
        }

        return $this->render('admin/equipement/edit.html.twig', [
            'equipement' => $equipement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin.equipement.delete", methods={"DELETE"})
     * @param Request $request
     * @param Equipement $equipement
     * @return Response
     */
    public function delete(Request $request, Equipement $equipement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($equipement);
            $entityManager->flush();
            $this->addFlash('success', 'equipement supprimé avec success');
        }

        return $this->redirectToRoute('equipement.index');
    }
}
