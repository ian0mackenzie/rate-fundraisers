<?php

namespace AppBundle\Form;

use AppBundle\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\FormEvents;

class ReviewType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('title');
        $builder->add('rating', ChoiceType::class, array(
            'choices' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' =>'5'),
            'expanded' => true,
            
            'multiple' => false,
            'choice_attr' => function($val, $key, $index) {
                return ['class' => 'rating'];
            }
        ));
        $builder->add('review')->add('author')->add('author', AuthorType::class, array("label" => FALSE));

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Review'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_review';
    }


}
