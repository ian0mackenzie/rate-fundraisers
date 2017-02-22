<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fundraiser;
use AppBundle\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\General\HelperClass;

use AppBundle\Form\ReviewType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Fundraiser controller.
 *
 * @Route("fundraiser")
 */
class FundraiserController extends Controller
{
    /**
     * Lists all fundraiser entities.
     *
     * @Route("/", name="fundraiser_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
    	//if we're passing a sort_by it'll be an embed in twig template.
        $sortBy = $request->get('sort_by');

        $em = $this->getDoctrine()->getManager();

        if('average-rating' === $sortBy){
            $fundraisers = $em->getRepository('AppBundle:Fundraiser')->getFundraisersByAverageRating();
        } else {
            $fundraisers = $em->getRepository('AppBundle:Fundraiser')->findAll();
        }

        return $this->render('fundraiser/index.html.twig', array(
            'fundraisers' => $fundraisers,
        ));
    }

   protected function getAuthor(\AppBundle\Entity\Author $author){

        $em = $this->getDoctrine()->getManager();
        $email = $author->getEmail();

        $existingAuthor = $em->getRepository('AppBundle:Author')->findOneByEmail($email);

        if($existingAuthor){
            $existingAuthor->setFirstName($author->getFirstName());
            $existingAuthor->setLastName($author->getLastName());
            $author = $existingAuthor;
        } else {
            $date = new \DateTime("now");
            $author->setCreatedDate($date);
            $em->persist($author);
        }

        $em->flush();

        return $author;
    }

   protected function getReview(\AppBundle\Entity\Author $author, \AppBundle\Entity\Fundraiser $fundraiser, \AppBundle\Entity\Review $review){

        $em = $this->getDoctrine()->getManager();
        $authorId = $author->getId();

        $existingReview = $em->getRepository('AppBundle:Review')->getReviewByUserIdAndFundraiserId($author, $fundraiser);

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
     *
     * @Route("/new", name="fundraiser_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $fundraiser = new Fundraiser();
        $form = $this->createForm('AppBundle\Form\FundraiserType', $fundraiser);



        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            //Update existing author or create new
            $em = $this->getDoctrine()->getManager();
            $author = $this->getAuthor($fundraiser->getAuthor());
            //Set the author
            $fundraiser = $form->getData();
            $fundraiser->setAuthor($author);
            //Set Created Date
            $date = new \DateTime("now");
            $fundraiser->setCreatedDate($date);
            $em->persist($fundraiser);
            $em->flush($fundraiser);




            return $this->redirectToRoute('fundraiser_show', array('id' => $fundraiser->getId()));

        }

        return $this->render('fundraiser/new.html.twig', array(
            'fundraiser' => $fundraiser,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a fundraiser entity.
     *
     * @Route("/{id}", name="fundraiser_show")
     * @Method("GET")
     */
    public function showAction(Fundraiser $fundraiser)
    {

        $deleteForm = $this->createDeleteForm($fundraiser);

        return $this->render('fundraiser/show.html.twig', array(
            'fundraiser' => $fundraiser,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * The review form for fundraisers
     *
     * @Route("/{id}/review", name="fundraiser_review")
     * @Method({"GET", "POST"})
     */
    public function reviewAction(Request $request, Fundraiser $fundraiser)
    {
    	$review = new Review();
    	//Build the review form. 

        $reviewForm = $this->createFormBuilder($review)
            ->add('review', ReviewType::class, array("label" => FALSE))
			->setAction($this->generateUrl('fundraiser_review', array('id' =>$fundraiser->getId())))
            ->getForm();

        $reviewForm->handleRequest($request);

        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {

            //Update existing author or create new
            $em = $this->getDoctrine()->getManager();
            $review = $reviewForm->getData()->getReview();
            $author = $this->getAuthor($review->getAuthor());

            //Update the existing review or get a new one
            $author = $this->getAuthor($review->getAuthor());
            $review = $this->getReview($author, $fundraiser, $review);

            //Set the author and fundraiser for the review.
            $review->setAuthor($author);
            $review->setFundraiser($fundraiser);

            $em->persist($review);
            $em->flush($review);

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('fundraiser_show', array('id' => $fundraiser->getId()));
        }

        return $this->render('fundraiser/review.html.twig', array(
            'form' => $reviewForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing fundraiser entity.
     *
     * @Route("/{id}/edit", name="fundraiser_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Fundraiser $fundraiser)
    {
        $deleteForm = $this->createDeleteForm($fundraiser);
        $editForm = $this->createForm('AppBundle\Form\FundraiserType', $fundraiser);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('fundraiser_edit', array('id' => $fundraiser->getId()));
        }

        return $this->render('fundraiser/edit.html.twig', array(
            'fundraiser' => $fundraiser,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a fundraiser entity.
     *
     * @Route("/{id}", name="fundraiser_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Fundraiser $fundraiser)
    {
        $form = $this->createDeleteForm($fundraiser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($fundraiser);
            $em->flush($fundraiser);
        }

        return $this->redirectToRoute('fundraiser_index');
    }

    /**
     * Creates a form to delete a fundraiser entity.
     *
     * @param Fundraiser $fundraiser The fundraiser entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Fundraiser $fundraiser)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fundraiser_delete', array('id' => $fundraiser->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
