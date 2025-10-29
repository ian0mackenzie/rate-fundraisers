<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/author')]
class AuthorController extends AbstractController
{
    #[Route('/', name: 'author_index', methods: ['GET'])]
    public function indexAction(EntityManagerInterface $em): Response
    {
        $authors = $em->getRepository(Author::class)->findAll();

        return $this->render('author/index.html.twig', [
            'authors' => $authors,
        ]);
    }

    /*NOTE: COMMENTED OUT
     * Creates a new author entity.
     */
    #[Route('/new', name: 'author_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // check to see if the email already exists.
            $existingAuthor = $em
                ->getRepository(Author::class)
                ->findOneByEmail($author->getEmail());

            if ($existingAuthor) {
                //if the email does exist, grab the incoming name and update the existing name with it.
                $existingAuthor->setFirstName($author->getFirstName());
                $existingAuthor->setLastName($author->getLastName());
                $author = $existingAuthor;
            } else {
                //Other wise it's a new author. Set the creation timestamp.
                $date = new \DateTime("now");
                $author->setCreatedDate($date);
            }
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('author_show', ['id' => $author->getId()]);
        }

        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /*NOTE: COMMENTED OUT
     * Finds and displays a author entity.
     */
    #[Route('/{id}', name: 'author_show', methods: ['GET'])]
    public function showAction(Author $author): Response
    {
        $deleteForm = $this->createDeleteForm($author);

        return $this->render('author/show.html.twig', [
            'author' => $author,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /*NOTE: COMMENTED OUT
     * Displays a form to edit an existing author entity.
     */
    #[Route('/{id}/edit', name: 'author_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Author $author, EntityManagerInterface $em): Response
    {
        $deleteForm = $this->createDeleteForm($author);
        $editForm = $this->createForm(AuthorType::class, $author);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            return $this->redirectToRoute('author_edit', ['id' => $author->getId()]);
        }

        return $this->render('author/edit.html.twig', [
            'author' => $author,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /*NOTE: COMMENTED OUT
     * Deletes a author entity.
     */
    #[Route('/{id}', name: 'author_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, Author $author, EntityManagerInterface $em): Response
    {
        $form = $this->createDeleteForm($author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($author);
            $em->flush();
        }

        return $this->redirectToRoute('author_index');
    }

    /*NOTE: COMMENTED OUT
     * Creates a form to delete a author entity.
     *
     * @param Author $author The author entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Author $author)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('author_delete', ['id' => $author->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
