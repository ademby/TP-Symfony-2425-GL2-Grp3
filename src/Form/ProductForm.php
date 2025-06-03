<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Service\CategoryService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType,FileType,SubmitType,TextareaType};
use App\Form\KeyValueType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;


class ProductForm extends AbstractType
{

    private CategoryService $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('price')
            ->add('properties', KeyValueType::class, [
                'required' => false,
                'label' => 'Properties (key: value pairs, one per line)',
                'attr' => ['rows' => 5],
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Please upload a product image']),
                    new File([
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG or GIF)',
                    ]),
                ],
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'None' => null,
                ],
                'choice_loader' => new CallbackChoiceLoader(function () {
                    $categories = $this->categoryService->getCategories();
                    $choices = ['None' => null];
                    foreach ($categories as $category) {
                        $choices[$category->getName()] = $category;
                    }
                    return $choices;
                }),
                'placeholder' => 'Select a category',
                'required' => false,
                'data' => null,
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['submit_label']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'validation_groups' => ['ProductCreation'],
            'submit_label' => 'Save Category',
        ]);
    }
}
