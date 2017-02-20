<?php
namespace AppBundle\General;
use Doctrine\ORM\EntityManager;

 class HelperClass {

    protected $em;

    public function __construct( $em)
    {
        $this->em = $em;
    }

 	public function checkExistingAuthorOrReturnNew($author){

        // check to see if the email already exists.
        $existingAuthor = $this->em
            ->getRepository('AppBundle:Author')
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
        $author->setFirstName("HIJACKEDYA");
        return $author;

 	}
 }