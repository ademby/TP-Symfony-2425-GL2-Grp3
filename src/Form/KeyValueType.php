<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

class KeyValueType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this);
    }

    public function getParent()
    {
        return TextareaType::class;
    }

    public function transform(mixed $value): mixed
    {
        if (empty($value) || !is_array($value)) {
            return '';
        }

        $lines = [];
        foreach ($value as $key => $val) {
            $lines[] = $key . ': ' . $val;
        }

        return implode("\n", $lines);
    }

    public function reverseTransform(mixed $value): mixed
    {
        $result = [];
        if (empty($value) || !is_string($value)) {
            return $result;
        }

        $lines = explode("\n", $value);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $pos = strpos($line, ':');
            if ($pos === false) {
                continue;
            }

            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));

            if ($key !== '') {
                $result[$key] = $val;
            }
        }

        return $result;
    }
}
