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
            'choices' => array('5' => '5', '4' => '4', '3' => '3', '2' => '2', '1' =>'1'),
            'expanded' => true,
            'multiple' => false,
            'choice_attr' => function($val, $key, $index) {
                return ['class' => 'rating'];
            }
        ));
        $builder->add('review');
        //Removed when switching to registration
        //->add('author', AuthorType::class, array("label" => FALSE));

    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'appbundle_review';
    }
}
