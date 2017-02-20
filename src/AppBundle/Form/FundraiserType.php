<?php

namespace AppBundle\Form;

use AppBundle\Form\DataTransformer\FundraiserTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FundraiserType extends AbstractType
{

    private $manager;


    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new FundraiserTransformer($this->manager);
        $builder->add('name')->add('description')->add('thumbnail');

        //We'll handle dates. Don't want users to access that.
        //$builder->add('createdDate');

        $builder->add('author', AuthorType::class, array("label" => FALSE));

        //$builder->get('author')->addModelTransformer($transformer);

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Fundraiser'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_fundraiser';
    }


}
