<?php

namespace App\Form;

use App\Entity\Critere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CritereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('sexesRecherches', ChoiceType::class, [
                'choices' => [
                    'Femme' => 'Femme',
                    'Homme' => 'Homme',
                    'Non-Binaire' => 'Non-Binaire'
                ]
            ])
            ->add('departementsRecherches')
            ->add('ageRecherches')
            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Critere::class,
        ]);
    }
}
