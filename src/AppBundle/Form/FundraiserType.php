<?php

namespace AppBundle\Form;

use AppBundle\Entity\Fundraiser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FundraiserType extends AbstractType
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name')->add('description')->add('thumbnail');

        //We'll handle dates. Don't want users to access that.
        //$builder->add('createdDate');

        //$builder->get('author')->addModelTransformer($transformer);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fundraiser::class
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'appbundle_fundraiser';
    }
}
