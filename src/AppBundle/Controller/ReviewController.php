<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Review;
use AppBundle\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/review')]
class ReviewController extends AbstractController
{
    /*NOTE: COMMENTED OUT
     * Lists all review entities.
     */
    #[Route('/', name: 'review_index', methods: ['GET'])]
    public function indexAction(Request $request, EntityManagerInterface $em): Response
    {
        //if we're passing a fundraiser_id it'll be an embed in twig template.
        $fundraiserId = $request->get('fundraiser_id');

        //If we have a fundraiser_id then display reviews belonging to that fundraiser
        if(!empty($fundraiserId)){
            $reviews = $em->getRepository(Review::class)->findBy(['fundraiser' => $fundraiserId]);
        } else {
            $reviews = $em->getRepository(Review::class)->findAll();            
        }

        return $this->render('review/index.html.twig', [
            'reviews' => $reviews
        ]);
    }

    /*NOTE: COMMENTED OUT
     * Creates a new review entity.
     */
    #[Route('/new', name: 'review_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $review = new Review();

        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($review);
            $em->flush();

            return $this->redirectToRoute('review_show', ['id' => $review->getId()]);
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /*NOTE: COMMENTED OUT
     * Finds and displays a review entity.
     */
    #[Route('/{id}', name: 'review_show', methods: ['GET'])]
    public function showAction(Review $review): Response
    {
        $deleteForm = $this->createDeleteForm($review);

        return $this->render('review/show.html.twig', [
            'review' => $review,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /*NOTE: COMMENTED OUT
     * Displays a form to edit an existing review entity.
     */
    #[Route('/{id}/edit', name: 'review_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Review $review, EntityManagerInterface $em): Response
    {
        $deleteForm = $this->createDeleteForm($review);
        $editForm = $this->createForm(ReviewType::class, $review);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            return $this->redirectToRoute('review_edit', ['id' => $review->getId()]);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /*NOTE: COMMENTED OUT
     * Deletes a review entity.
     */
    #[Route('/{id}', name: 'review_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, Review $review, EntityManagerInterface $em): Response
    {
        $form = $this->createDeleteForm($review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($review);
            $em->flush();
        }

        return $this->redirectToRoute('review_index');
    }

    /*NOTE: COMMENTED OUT
     * Creates a form to delete a review entity.
     *
     * @param Review $review The review entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Review $review)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('review_delete', ['id' => $review->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
