<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Entity\Fundraiser;
use AppBundle\Entity\Review;
use AppBundle\Form\FundraiserType;
use AppBundle\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fundraiser')]
class FundraiserController extends AbstractController
{
    /**
     * Show all fundraiser entities.
     */
    #[Route('/', name: 'fundraiser_index', methods: ['GET'])]
    public function indexAction(Request $request): Response
    {
        return $this->render('fundraiser/index.html.twig');
    }

    /**
     * Lists all fundraiser entities, optionally sorted.
     */
    #[Route('/list', name: 'fundraiser_list', methods: ['GET'])]
    public function listAction(Request $request, EntityManagerInterface $em): Response
    {
        //if we're passing a sort_by it'll be an embed in twig template.
        $sortBy = $request->get('sort_by');

        if('average-rating' === $sortBy){
            $sortBy = "avg_rating";
            $fundraisers = $em->getRepository(Fundraiser::class)->getFundraisersByAverageRating($sortBy);
        } else {
            $sortBy = "name";
            $fundraisers = $em->getRepository(Fundraiser::class)->getFundraisersByAverageRating($sortBy);
        }

        return $this->render('fundraiser/list.html.twig', [
            'fundraisers' => $fundraisers,
        ]);
    }

   protected function getReview(Author $author, Fundraiser $fundraiser, Review $review, EntityManagerInterface $em): Review
   {
        $existingReview = $em->getRepository(Review::class)->getReviewByUserIdAndFundraiserId($author, $fundraiser);

        if($existingReview) {
            $existingReview->setRating($review->getRating());
            $existingReview->setTitle($review->getTitle());
            $existingReview->setReview($review->getReview());
            $review = $existingReview;
        }

        return $review;
    }

    /**
     * Creates a new fundraiser entity.
     */
    #[Route('/new', name: 'fundraiser_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $fundraiser = new Fundraiser();
        $form = $this->createForm(FundraiserType::class, $fundraiser);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $author = $this->getUser();
            //Set the author
            $fundraiser = $form->getData();
            $fundraiser->setAuthor($author);
            //Set Created Date
            $date = new \DateTime("now");
            $fundraiser->setCreatedDate($date);
            $em->persist($fundraiser);
            $em->flush();

            return $this->redirectToRoute('fundraiser_show', ['id' => $fundraiser->getId()]);
        }

        return $this->render('fundraiser/new.html.twig', [
            'fundraiser' => $fundraiser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a fundraiser entity.
     */
    #[Route('/{id}', name: 'fundraiser_show', methods: ['GET', 'POST'])]
    public function showAction(Request $request, Fundraiser $fundraiser, EntityManagerInterface $em): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Update existing review or create new
            $review = $form->getData();
            $author = $this->getUser();
            $review = $this->getReview($author, $fundraiser, $review, $em);

            //Set the author and fundraiser for the review.
            $review->setAuthor($author);
            $review->setFundraiser($fundraiser);

            $em->persist($review);
            $em->flush();

            return $this->redirectToRoute('fundraiser_show', ['id' => $fundraiser->getId()]);
        }

        return $this->render('fundraiser/show.html.twig', [
            'fundraiser' => $fundraiser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing fundraiser entity.
     */
    #[Route('/{id}/edit', name: 'fundraiser_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Fundraiser $fundraiser, EntityManagerInterface $em): Response
    {
        $deleteForm = $this->createDeleteForm($fundraiser);
        $editForm = $this->createForm(FundraiserType::class, $fundraiser);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            return $this->redirectToRoute('fundraiser_edit', ['id' => $fundraiser->getId()]);
        }

        return $this->render('fundraiser/edit.html.twig', [
            'fundraiser' => $fundraiser,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /* NOTE: COMMENTED OUT
     * Deletes a fundraiser entity.
     */
    #[Route('/{id}', name: 'fundraiser_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, Fundraiser $fundraiser, EntityManagerInterface $em): Response
    {
        $form = $this->createDeleteForm($fundraiser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($fundraiser);
            $em->flush();
        }

        return $this->redirectToRoute('fundraiser_index');
    }

    /*NOTE: COMMENTED OUT
     * Creates a form to delete a fundraiser entity.
     *
     * @param Fundraiser $fundraiser The fundraiser entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Fundraiser $fundraiser)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fundraiser_delete', ['id' => $fundraiser->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
