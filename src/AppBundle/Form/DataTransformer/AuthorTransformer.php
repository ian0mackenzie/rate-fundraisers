<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Author;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class AuthorTransformer implements DataTransformerInterface
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

    public function reverseTransform($email)
    {
        if (!$email)
            return null;

        $author = $this->om->getRepository('AppBundle:Author')->findOneByEmail($email);

        if (!$author) {
            $author = new Author();
            $author->setEmail($email);
        }

        return $author;
    }
}