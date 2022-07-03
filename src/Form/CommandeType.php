<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('produit', EntityType::class, [
            "class" => Produit::class,
            //"choice_label" => "titre",
            "choice_label" => function($produit){
                return $produit->getTitre() ." - " .$produit->getPrix() ."€";
            },
            "placeholder" => "--choisir--",
            ])
            ->add('quantite')
            ->add('user', EntityType::class, [
                "class" => User::class,
                "choice_label" => "pseudo",
                "placeholder" => "--choisir--",
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'en cours de traitement' => 'en cours de traitement',
                    'envoyé' => 'envoyé',
                    'livré' => 'livré'
                ],
                'placeholder' => '--choisir--'
            ])
            ->add("save", SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
