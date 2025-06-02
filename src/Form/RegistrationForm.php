<?php

namespace App\Form;

use App\Entity\User;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'First name must be at least {{ limit }} characters',
                        'maxMessage' => 'First name cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-\']+$/',
                        'message' => 'First name can only contain letters, spaces, hyphens, and apostrophes',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-\']+$/',
                        'message' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone Number',
                'attr' => [
                    'placeholder' => '+216 xx-xxx-xxx',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your phone number',
                    ]),
                    new Regex([
                        'pattern' => '/^[\+]?[0-9\s\-$$$$]{10,15}$/',
                        'message' => 'Please enter a valid phone number',
                    ]),
                ],
            ])
            ->add('age', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 13,
                        'max' => 120,
                    ]),
                ],
            ])
            ->add('region', ChoiceType::class, [
                'choices' => [
                    'Ariana' => 'ariana',
                    'Béja' => 'beja',
                    'Ben Arous' => 'ben_arous',
                    'Bizerte' => 'bizerte',
                    'Gabès' => 'gabes',
                    'Gafsa' => 'gafsa',
                    'Jendouba' => 'jendouba',
                    'Kairouan' => 'kairouan',
                    'Kasserine' => 'kasserine',
                    'Kébili' => 'kebili',
                    'Kef' => 'kef',
                    'Mahdia' => 'mahdia',
                    'Manouba' => 'manouba',
                    'Médenine' => 'medenine',
                    'Monastir' => 'monastir',
                    'Nabeul' => 'nabeul',
                    'Sfax' => 'sfax',
                    'Sidi Bouzid' => 'sidi_bouzid',
                    'Siliana' => 'siliana',
                    'Sousse' => 'sousse',
                    'Tataouine' => 'tataouine',
                    'Tozeur' => 'tozeur',
                    'Tunis' => 'tunis',
                    'Zaghouan' => 'zaghouan',
                ],
                'placeholder' => 'Select your region',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select your region',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email address',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
