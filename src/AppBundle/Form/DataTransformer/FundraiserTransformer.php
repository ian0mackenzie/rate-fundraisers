<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Fundraiser;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FundraiserTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function transform($author)
    {
        if (null === $author) {
            return '';
        }

        return $author;
    }

    public function reverseTransform($author)
    {
        var_dump("transformer");
        if (!$author)
            return null;

        $existingAuthor = $this->om->getRepository('AppBundle:Author')->findOneByEmail($author->getEmail());


        if (!$existingAuthor) {
            $date = new \DateTime("now");
            $author->setCreatedDate($date);
            var_dump("Doesn't exist");
        } else {
            var_dump("exists");
            var_dump($existingAuthor->getFirstName());
            $existingAuthor->setFirstName($author->getFirstName());
            $existingAuthor->setLastName($author->getLastName());
            $author = $existingAuthor;
        }

        return $author;
    }
}