<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fundraiser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $fundraisers = $em->getRepository('AppBundle:Fundraiser')->findAll();

        return $this->render('fundraiser/index.html.twig', array(
            'fundraisers' => $fundraisers,
        ));
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

            //Set the timestamps on fundraiser/author.
            $date = new \DateTime("now");
            $fundraiser->setCreatedDate($date);
            $fundraiser->getAuthor()->setCreatedDate($date);

            $email = $fundraiser->getAuthor()->getEmail();

            $author = $this->getDoctrine()->getRepository('AppBundle:Author')
            ->findOneByEmail("ian0mackenzie@gmail.com");

            //Persist to database.
            $em = $this->getDoctrine()->getManager();

            if($author) {

                $author->setFirstName($fundraiser->getAuthor()->getFirstName());
                $author->setLastName($fundraiser->getAuthor()->getLastName());
                $fundraiser->setAuthor($author);
                $em->persist($fundraiser->getAuthor());
            }

            var_dump($fundraiser->getAuthor()->getFirstName());

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
