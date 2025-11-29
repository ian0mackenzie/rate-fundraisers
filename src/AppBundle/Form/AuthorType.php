<?php

namespace AppBundle\Form;

use AppBundle\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //$builder->add($builder->create('email')->addModelTransformer($transformer));

        $builder->add('firstName')->add('lastName');
        $builder->add('email');

        //We'll handle dates. Don't want users to access that.
        //$builder->add('createdDate');
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'appbundle_author';
    }
}